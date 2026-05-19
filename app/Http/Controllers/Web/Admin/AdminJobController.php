<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Models\ImageJobFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminJobController extends Controller
{
    public function index(Request $request)
    {
        $staleBefore = now()->subMinutes($this->staleThresholdMinutes());

        $sort = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sort, ['created_at', 'expires_at', 'status'], true)) {
            $sort = 'created_at';
        }

        $jobs = ImageJob::query()
            ->with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->boolean('expired_only'), function ($query) {
                $query->whereNotNull('expires_at')->where('expires_at', '<', now());
            })
            ->when($request->boolean('stale_only'), function ($query) use ($staleBefore) {
                $query->whereIn('status', [ImageJob::STATUS_PENDING, ImageJob::STATUS_PROCESSING])
                    ->where('created_at', '<', $staleBefore);
            })
            ->orderBy($sort, $direction)
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $statuses = [
            ImageJob::STATUS_PENDING,
            ImageJob::STATUS_PROCESSING,
            ImageJob::STATUS_DONE,
            ImageJob::STATUS_FAILED,
            ImageJob::STATUS_EXPIRED,
        ];

        return view('admin.jobs.index', compact('jobs', 'statuses', 'staleBefore', 'sort', 'direction'));
    }

    public function cleanupExpired()
    {
        $jobs = ImageJob::query()
            ->with('files')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->where(function ($query) {
                $query->whereNotNull('result_archive_path')
                    ->orWhereHas('files', fn ($files) => $files->whereNotNull('result_path'));
            })
            ->get();

        $cleanedJobs = 0;

        foreach ($jobs as $job) {
            $paths = array_filter(array_merge(
                [$job->result_archive_path],
                $job->files->pluck('result_path')->filter()->all(),
            ));

            if ($paths !== []) {
                Storage::disk('local')->delete($paths);
            }

            $job->files()->whereNotNull('result_path')->update(['result_path' => null, 'result_size' => 0]);

            $job->update([
                'status' => ImageJob::STATUS_EXPIRED,
                'result_archive_path' => null,
                'result_size_bytes' => 0,
            ]);

            $cleanedJobs++;
        }

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', dbt('admin.jobs.messages.cleaned', ['count' => $cleanedJobs]));
    }

    public function failStale()
    {
        $staleBefore = now()->subMinutes($this->staleThresholdMinutes());

        $jobs = ImageJob::query()
            ->with('files')
            ->whereIn('status', [ImageJob::STATUS_PENDING, ImageJob::STATUS_PROCESSING])
            ->where('created_at', '<', $staleBefore)
            ->get();

        $updatedJobs = 0;

        foreach ($jobs as $job) {
            $job->files()
                ->whereIn('status', [ImageJobFile::STATUS_PENDING, ImageJobFile::STATUS_PROCESSING])
                ->update([
                    'status' => ImageJobFile::STATUS_FAILED,
                    'error_message' => dbt('admin.jobs.messages.marked_stale_file'),
                ]);

            $job->update([
                'status' => ImageJob::STATUS_FAILED,
                'completed_at' => now(),
                'failed_files' => max($job->failed_files, $job->total_files),
            ]);

            $updatedJobs++;
        }

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', dbt('admin.jobs.messages.stopped_stale', ['count' => $updatedJobs]));
    }

    public function destroy(ImageJob $job)
    {
        $this->deleteJobWithFiles($job->load('files'));

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', dbt('admin.jobs.messages.deleted', ['uuid' => $job->uuid]));
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'job_ids' => 'required|array|min:1',
            'job_ids.*' => 'integer|exists:image_jobs,id',
        ]);

        $jobs = ImageJob::query()
            ->with('files')
            ->whereIn('id', $data['job_ids'])
            ->get();

        foreach ($jobs as $job) {
            $this->deleteJobWithFiles($job);
        }

        $count = $jobs->count();

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', dbt('admin.jobs.messages.bulk_deleted', ['count' => $count]));
    }

    private function deleteJobWithFiles(ImageJob $job): void
    {
        $paths = array_filter(array_merge(
            [$job->result_archive_path],
            $job->files->pluck('original_path')->filter()->all(),
            $job->files->pluck('result_path')->filter()->all(),
        ));

        if ($paths !== []) {
            Storage::disk('local')->delete($paths);
        }

        $job->files()->delete();
        $job->delete();
    }

    private function staleThresholdMinutes(): int
    {
        return max(30, (int) ceil(((int) config('ipp.queue_timeout', 300)) / 60) * 6);
    }
}
