@extends('layouts.app')
@section('title', dbt('admin.common.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">{{ dbt('admin.common.title') }}</h1>
            <p class="text-sm text-gray-400 mt-2">{{ dbt('admin.dashboard.subtitle') }}</p>
        </div>
    </div>

    @include('admin._nav')

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
        <div class="card-panel"><p class="text-sm text-gray-400">{{ dbt('admin.dashboard.stats.users') }}</p><p class="text-2xl font-bold mt-2">{{ $stats['users_count'] }}</p></div>
        <div class="card-panel"><p class="text-sm text-gray-400">{{ dbt('admin.dashboard.stats.plans') }}</p><p class="text-2xl font-bold mt-2">{{ $stats['plans_count'] }}</p></div>
        <div class="card-panel"><p class="text-sm text-gray-400">{{ dbt('admin.dashboard.stats.jobs_today') }}</p><p class="text-2xl font-bold mt-2">{{ $stats['jobs_today'] }}</p></div>
        <div class="card-panel"><p class="text-sm text-gray-400">{{ dbt('admin.dashboard.stats.jobs_processing') }}</p><p class="text-2xl font-bold mt-2">{{ $stats['jobs_processing'] }}</p></div>
        <div class="card-panel"><p class="text-sm text-gray-400">{{ dbt('admin.dashboard.stats.expired_archives') }}</p><p class="text-2xl font-bold mt-2">{{ $stats['expired_archives'] }}</p></div>
        <div class="card-panel"><p class="text-sm text-gray-400">{{ dbt('admin.dashboard.stats.stale_jobs') }}</p><p class="text-2xl font-bold mt-2">{{ $stats['stale_jobs'] }}</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card-panel">
            <h2 class="text-lg font-semibold mb-3">{{ dbt('admin.dashboard.quick_actions.title') }}</h2>
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.jobs.cleanup-expired') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-200 hover:bg-amber-500/20 transition">
                        {{ dbt('admin.dashboard.quick_actions.cleanup') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.jobs.fail-stale') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg bg-red-500/10 border border-red-500/30 text-red-200 hover:bg-red-500/20 transition">
                        {{ dbt('admin.dashboard.quick_actions.stop_stale') }}
                    </button>
                </form>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                {{ dbt('admin.dashboard.quick_actions.stale_threshold', ['time' => $staleBefore->diffForHumans()]) }}
            </p>
        </div>

        <div class="card-panel">
            <h2 class="text-lg font-semibold mb-3">{{ dbt('admin.dashboard.available_now.title') }}</h2>
            <ul class="text-sm text-gray-300 space-y-2 list-disc pl-5">
                <li>{{ dbt('admin.dashboard.available_now.cleanup') }}</li>
                <li>{{ dbt('admin.dashboard.available_now.stop_stale') }}</li>
                <li>{{ dbt('admin.dashboard.available_now.plan_editing') }}</li>
            </ul>
        </div>
    </div>

    <div class="card-panel">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.dashboard.recent_jobs.title') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-800">
                        <th class="py-3 pr-4">UUID</th>
                        <th class="py-3 pr-4">{{ dbt('admin.dashboard.recent_jobs.user') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.dashboard.recent_jobs.status') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.dashboard.recent_jobs.files') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.dashboard.recent_jobs.created') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJobs as $job)
                        <tr class="border-b border-gray-900">
                            <td class="py-3 pr-4 font-mono text-xs">{{ $job->uuid }}</td>
                            <td class="py-3 pr-4">{{ $job->user?->email ?? dbt('common.guest') }}</td>
                            <td class="py-3 pr-4">{{ $job->localizedStatus() }}</td>
                            <td class="py-3 pr-4">{{ $job->processed_files }}/{{ $job->total_files }}</td>
                            <td class="py-3 pr-4">{{ $job->created_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-gray-500">{{ dbt('admin.dashboard.recent_jobs.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
