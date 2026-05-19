<?php

namespace App\Services;

use App\Jobs\ProcessImageJob;
use App\Models\ImageJob;
use App\Models\ImageJobFile;
use App\Models\Plan;
use App\Models\User;
use App\Models\UsageLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImageJobService
{
    public function __construct(private readonly PlanCapabilitiesResolver $capabilitiesResolver) {}

    /**
     * Create a new image job, upload files, dispatch processing queue job.
     *
     * @param  array           $options   Validated form/API options
     * @param  UploadedFile[]  $files
     * @param  User|null       $user
     * @param  string|null     $sessionId
     * @return ImageJob
     */
    public function create(
        array  $options,
        array  $files,
        ?User  $user,
        ?string $sessionId = null
    ): ImageJob {
        $hasUnlimitedAccess = $user?->hasUnlimitedAccess() ?? false;

        // Determine effective plan limits
        $plan = $user ? $user->effectivePlan() : $this->getFreePlan();
        $capabilities = $this->capabilitiesResolver->forPlan($plan, $hasUnlimitedAccess);

        $this->assertAllowedOutputFormat($options, $capabilities['output_formats']);

        // Check daily job limit for authenticated users
        if ($user && ! $user->canCreateJob()) {
            throw ValidationException::withMessages([
                'limit' => dbt('jobs.messages.daily_limit_reached', ['limit' => $plan->daily_jobs_limit]),
            ]);
        }

        // Check file count
        $maxFiles = $plan->max_files_per_job;
        if (! $hasUnlimitedAccess && count($files) > $maxFiles) {
            throw ValidationException::withMessages([
                'files' => dbt('jobs.messages.max_files_exceeded', ['max' => $maxFiles]),
            ]);
        }

        // Build pipeline from options
        $pipeline = $this->buildPipeline($options);
        $this->assertAllowedPipeline($pipeline, $capabilities['pipeline_steps'], (bool) $capabilities['watermark']);

        // Create job record
        $job = ImageJob::create([
            'user_id'        => $user?->id,
            'session_id'     => $sessionId,
            'status'         => ImageJob::STATUS_PENDING,
            'name'           => $options['job_name'] ?? null,
            'pipeline'       => $pipeline,
            'output_format'  => $options['output_format'] ?? null,
            'output_quality' => $options['output_quality'] ?? 85,
            'rename_mode'    => $options['rename_mode'] ?? 'original',
            'rename_prefix'  => $options['rename_prefix'] ?? null,
            'rename_suffix'  => $options['rename_suffix'] ?? null,
            'rename_start_number' => (int) ($options['rename_start_number'] ?? 1),
            'total_files'    => count($files),
            'expires_at'     => now()->addHours($plan->storage_ttl_hours ?? (int) config('ipp.storage_ttl_hours', 24)),
        ]);

        // Upload source files
        foreach ($files as $index => $file) {
            $maxSizeMb = $plan->max_file_size_mb;
            if (! $hasUnlimitedAccess && $file->getSize() > $maxSizeMb * 1024 * 1024) {
                // Skip oversized file, record as failed
                ImageJobFile::create([
                    'image_job_id'  => $job->id,
                    'original_name' => $file->getClientOriginalName(),
                    'original_path' => '',
                    'status'        => ImageJobFile::STATUS_FAILED,
                    'error_message' => dbt('jobs.messages.file_too_large', ['limit' => $maxSizeMb]),
                    'sort_order'    => $index,
                ]);
                $job->increment('failed_files');
                continue;
            }

            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

            $storedPath = $file->store("uploads/{$job->uuid}", 'local');

            ImageJobFile::create([
                'image_job_id'   => $job->id,
                'original_name'  => $file->getClientOriginalName(),
                'original_path'  => $storedPath,
                'original_mime'  => $file->getMimeType(),
                'original_size'  => $file->getSize(),
                'original_width' => $width,
                'original_height'=> $height,
                'sort_order'     => $index,
            ]);
        }

        // Refresh total (some may have been marked failed above)
        $pendingCount = $job->files()->where('status', ImageJobFile::STATUS_PENDING)->count();

        if ($pendingCount === 0) {
            $job->update(['status' => ImageJob::STATUS_FAILED, 'completed_at' => now()]);
            return $job;
        }

        // Dispatch queue job
        $priority = ($user && $capabilities['priority_queue'])
            ? 'high'
            : config('ipp.queue', 'image-processing');

        ProcessImageJob::dispatch($job->id)->onQueue($priority);

        // Log usage
        UsageLog::record('job_created', [
            'user_id'      => $user?->id,
            'image_job_id' => $job->id,
            'files_count'  => count($files),
            'ip_address'   => request()->ip(),
        ]);

        return $job->fresh();
    }

    /**
     * Build pipeline steps array from form/API options.
     */
    private function buildPipeline(array $options): array
    {
        $steps = [];

        // If raw pipeline provided (API usage), use it directly
        if (! empty($options['pipeline']) && is_array($options['pipeline'])) {
            return $options['pipeline'];
        }

        // Resize
        if (! empty($options['resize_mode']) && $options['resize_mode'] !== 'none') {
            $steps[] = [
                'step'   => 'resize',
                'params' => [
                    'mode'    => $options['resize_mode'],
                    'width'   => $options['resize_width']  ?? null,
                    'height'  => $options['resize_height'] ?? null,
                    'upscale' => (bool) ($options['resize_upscale'] ?? false),
                ],
            ];
        }

        // Rotate
        if (! empty($options['rotate_direction']) && $options['rotate_direction'] !== 'none') {
            $steps[] = [
                'step'   => 'rotate',
                'params' => ['direction' => $options['rotate_direction']],
            ];
        }

        // Flip
        if (! empty($options['flip_axis']) && $options['flip_axis'] !== 'none') {
            $steps[] = [
                'step'   => 'flip',
                'params' => ['axis' => $options['flip_axis']],
            ];
        }

        // Crop
        if (! empty($options['crop_width']) && ! empty($options['crop_height'])) {
            $steps[] = [
                'step'   => 'crop',
                'params' => [
                    'width'    => $options['crop_width'],
                    'height'   => $options['crop_height'],
                    'position' => $options['crop_position'] ?? 'center',
                ],
            ];
        }

        // Filter
        if (! empty($options['filters_enabled'])) {
            $params = [
                'brightness'  => (int) ($options['filter_brightness'] ?? 0),
                'contrast'    => (int) ($options['filter_contrast'] ?? 0),
                'saturation'  => (int) ($options['filter_saturation'] ?? 100),
                'blur'        => (int) ($options['filter_blur'] ?? 0),
                'sepia'       => (int) ($options['filter_sepia'] ?? 0),
                'grayscale'   => (int) ($options['filter_grayscale'] ?? 0),
                'hue_rotate'  => (int) ($options['filter_hue_rotate'] ?? 0),
            ];

            // Only add filter step if at least one filter is not at default value
            $defaults = ['brightness' => 0, 'contrast' => 0, 'saturation' => 100, 'blur' => 0, 'sepia' => 0, 'grayscale' => 0, 'hue_rotate' => 0];
            $hasActiveFilters = false;
            foreach ($params as $key => $value) {
                if ($value !== $defaults[$key]) {
                    $hasActiveFilters = true;
                    break;
                }
            }

            if ($hasActiveFilters) {
                $steps[] = ['step' => 'filter', 'params' => $params];
            }
        }

        return $steps;
    }

    private function getFreePlan(): Plan
    {
        return Plan::where('slug', 'free')->first() ?? new Plan([
            'max_files_per_job' => (int) config('ipp.max_files_free', 10),
            'max_file_size_mb'  => (int) config('ipp.max_file_size_free_mb', 10),
            'daily_jobs_limit'  => 99,
            'priority_queue'    => false,
            'storage_ttl_hours' => (int) config('ipp.storage_ttl_hours', 24),
        ]);
    }

    private function assertAllowedOutputFormat(array $options, array $allowedFormats): void
    {
        $selected = strtolower((string) ($options['output_format'] ?? 'jpg'));

        if (! in_array($selected, $allowedFormats, true)) {
            throw ValidationException::withMessages([
                'output_format' => dbt('jobs.messages.output_format_unavailable'),
            ]);
        }
    }

    private function assertAllowedPipeline(array $pipeline, array $allowedSteps, bool $watermarkEnabled): void
    {
        foreach ($pipeline as $item) {
            $step = strtolower((string) ($item['step'] ?? ''));

            if ($step === '') {
                continue;
            }

            if ($step === 'watermark' && ! $watermarkEnabled) {
                throw ValidationException::withMessages([
                    'pipeline' => dbt('jobs.messages.watermark_unavailable'),
                ]);
            }

            if (! in_array($step, $allowedSteps, true)) {
                throw ValidationException::withMessages([
                    'pipeline' => dbt('jobs.messages.operation_unavailable', ['operation' => $step]),
                ]);
            }
        }
    }
}