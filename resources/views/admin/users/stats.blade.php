@extends('layouts.app')
@section('title', dbt('admin.users.stats_title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-3xl font-bold">
            {{ dbt('admin.users.stats_title') }}
            <span class="text-lg text-gray-500">{{ $stats['user']->name }}</span>
        </h1>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white transition">
            {{ dbt('common.back') }}
        </a>
    </div>
    <p class="text-sm text-gray-400 mb-6">
        {{ $stats['user']->email }} · ID: {{ $stats['user']->id }}
    </p>

    @include('admin._nav')

    {{-- Period Selector --}}
    <div class="card-panel mb-6">
        <form method="GET" class="flex gap-3">
            <select name="period" class="rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white" onchange="this.form.submit()">
                <option value="7" {{ $period == '7' ? 'selected' : '' }}>{{ dbt('admin.statistics.period.week') }}</option>
                <option value="30" {{ $period == '30' ? 'selected' : '' }}>{{ dbt('admin.statistics.period.month') }}</option>
                <option value="90" {{ $period == '90' ? 'selected' : '' }}>{{ dbt('admin.statistics.period.quarter') }}</option>
            </select>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.users.stats.total_jobs') }}</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_jobs']) }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.users.stats.jobs_today') }}</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['jobs_today'] }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.users.stats.jobs_this_week') }}</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['jobs_this_week'] }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.users.stats.jobs_this_month') }}</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['jobs_this_month'] }}</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card-panel mb-6">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.users.stats.activity_chart') }}</h2>
        <div class="h-64 relative">
            <canvas id="jobsChart"></canvas>
        </div>
    </div>

    {{-- Recent Jobs --}}
    <div class="card-panel">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.users.stats.recent_jobs') }}</h2>
        @if($stats['recent_jobs']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-gray-500 border-b border-gray-800">
                        <tr>
                            <th class="text-left py-3 px-4">ID</th>
                            <th class="text-left py-3 px-4">{{ dbt('admin.jobs.table.status') }}</th>
                            <th class="text-left py-3 px-4">{{ dbt('admin.jobs.table.files') }}</th>
                            <th class="text-left py-3 px-4">{{ dbt('admin.jobs.table.created') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_jobs'] as $job)
                            <tr class="border-b border-gray-800 hover:bg-gray-900/50">
                                <td class="py-3 px-4 font-mono text-xs">{{ Str::limit($job->uuid, 8) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded text-xs
                                        @match($job->status)
                                            @case('done') bg-green-500/20 text-green-200 @break
                                            @case('failed') bg-red-500/20 text-red-200 @break
                                            @case('processing') bg-yellow-500/20 text-yellow-200 @break
                                            @default bg-gray-500/20 text-gray-200
                                        @endmatch
                                    ">
                                        {{ dbt('status.job.' . $job->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $job->files_count }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $job->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">{{ dbt('admin.users.stats.no_jobs') }}</p>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('jobsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($stats['jobs_chart']['labels']),
            datasets: [{
                label: '{{ dbt('admin.users.stats.jobs_per_day') }}',
                data: @json($stats['jobs_chart']['data']),
                borderColor: 'rgb(167, 139, 250)',
                backgroundColor: 'rgba(167, 139, 250, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: 'rgb(156, 163, 175)' }
                }
            },
            scales: {
                x: {
                    ticks: { color: 'rgb(156, 163, 175)' },
                    grid: { color: 'rgba(75, 85, 99, 0.3)' }
                },
                y: {
                    ticks: { color: 'rgb(156, 163, 175)' },
                    grid: { color: 'rgba(75, 85, 99, 0.3)' },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection