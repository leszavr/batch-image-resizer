<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Services\ImageJobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageJobApiController extends Controller
{
    public function __construct(private readonly ImageJobService $service) {}

    /**
     * POST /api/jobs
     * Create and queue a new image job.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files'          => 'required|array|min:1',
            'files.*'        => 'required|file|mimes:jpg,jpeg,png,gif,bmp,tiff,tif,webp|max:51200',
            'output_format'  => 'nullable|in:jpg,png,webp,avif,gif,tiff',
            'output_quality' => 'nullable|integer|min:1|max:100',
            'pipeline'       => 'nullable|array',
            'rename_mode'    => 'nullable|in:original,sequence',
            'rename_prefix'  => 'nullable|string|max:50',
            'rename_suffix'  => 'nullable|string|max:50',
            'rename_start_number' => 'nullable|integer|min:1|max:999999',
        ]);

        $job = $this->service->create(
            options:   $request->except('files'),
            files:     $request->file('files'),
            user:      $request->user(),
            sessionId: null,
        );

        return response()->json([
            'uuid'   => $job->uuid,
            'status' => $job->status,
        ], 201);
    }

    /**
     * GET /api/jobs/{uuid}
     * Get job status and progress.
     */
    public function show(ImageJob $imageJob)
    {
        $this->authorizeApiAccess($imageJob);

        return response()->json([
            'uuid'            => $imageJob->uuid,
            'status'          => $imageJob->status,
            'progress'        => $imageJob->progressPercent(),
            'total_files'     => $imageJob->total_files,
            'processed_files' => $imageJob->processed_files,
            'failed_files'    => $imageJob->failed_files,
            'is_finished'     => $imageJob->isFinished(),
            'download_url'    => $imageJob->status === ImageJob::STATUS_DONE
                ? route('api.jobs.download', $imageJob->uuid) : null,
            'created_at'      => $imageJob->created_at,
            'completed_at'    => $imageJob->completed_at,
        ]);
    }

    /**
     * GET /api/jobs/{uuid}/download
     * Download result ZIP.
     */
    public function download(ImageJob $imageJob)
    {
        $this->authorizeApiAccess($imageJob);

        if ($imageJob->status !== ImageJob::STATUS_DONE || ! $imageJob->result_archive_path) {
            return response()->json(['error' => 'Result not ready.'], 404);
        }

        $absPath = Storage::disk('local')->path($imageJob->result_archive_path);

        if (! file_exists($absPath)) {
            return response()->json(['error' => 'Archive expired or not found.'], 404);
        }

        return response()->download($absPath, "bir_{$imageJob->uuid}.zip");
    }

    private function authorizeApiAccess(ImageJob $job): void
    {
        $user = request()->user();
        if ($user && $job->user_id === $user->id) return;
        abort(403, 'Access denied.');
    }
}
