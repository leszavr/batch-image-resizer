@extends('layouts.app')
@section('title', dbt('nav.dashboard'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-6">{{ dbt('nav.dashboard') }}</h1>

    @if($user->can('admin.dashboard'))
        <div class="card-panel mb-6 border border-violet-500/30 bg-violet-500/5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ dbt('dashboard.admin_access.title') }}</h2>
                    <p class="text-sm text-gray-300 mt-1">
                        {{ dbt('dashboard.admin_access.description') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                        {{ dbt('dashboard.admin_access.action') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="card-panel">
            <p class="text-sm text-gray-400">{{ dbt('dashboard.current_plan') }}</p>
            <p class="text-xl font-bold mt-2">{{ $plan?->localizedName() ?? dbt('plans.price.free') }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-400">{{ dbt('dashboard.jobs_today') }}</p>
            <p class="text-xl font-bold mt-2">{{ $today }} / {{ $plan->daily_jobs_limit ?? '-' }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-400">{{ dbt('dashboard.credits') }}</p>
            <p class="text-xl font-bold mt-2">{{ number_format($user->credits_balance) }}</p>
        </div>
    </div>

    <div class="card-panel">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('dashboard.recent_jobs') }}</h2>
        <div class="space-y-3">
            @forelse($jobs as $job)
                <a class="block border border-gray-800 rounded-xl p-3 hover:border-violet-500 transition" href="{{ route('jobs.show', $job->uuid) }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $job->name ?: dbt('dashboard.job_fallback_name', ['uuid' => $job->uuid]) }}</p>
                            <p class="text-xs text-gray-500">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm text-gray-300">{{ $job->localizedStatus() }}</span>
                    </div>
                </a>
            @empty
                <p class="text-gray-500">{{ dbt('dashboard.no_jobs') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
