<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Models\Plan;
use App\Models\User;
use App\Services\SchedulerService;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly SchedulerService $schedulerService) {}

    public function index()
    {
        $staleBefore = now()->subMinutes($this->staleThresholdMinutes());

        $stats = [
            'users_count' => User::query()->count(),
            'plans_count' => Plan::query()->count(),
            'jobs_today' => ImageJob::query()->whereDate('created_at', today())->count(),
            'jobs_processing' => ImageJob::query()
                ->whereIn('status', [ImageJob::STATUS_PENDING, ImageJob::STATUS_PROCESSING])
                ->count(),
            'expired_archives' => ImageJob::query()
                ->whereNotNull('result_archive_path')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->count(),
            'stale_jobs' => ImageJob::query()
                ->whereIn('status', [ImageJob::STATUS_PENDING, ImageJob::STATUS_PROCESSING])
                ->where('created_at', '<', $staleBefore)
                ->count(),
        ];

        $recentJobs = ImageJob::query()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        $schedulerStatus = $this->schedulerService->getSchedulerStatus();

        return view('admin.dashboard', compact('stats', 'recentJobs', 'staleBefore', 'schedulerStatus'));
    }

    private function staleThresholdMinutes(): int
    {
        return max(30, (int) ceil(((int) config('ipp.queue_timeout', 300)) / 60) * 6);
    }
}