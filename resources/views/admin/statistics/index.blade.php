@extends('layouts.app')
@section('title', dbt('admin.statistics.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-2">{{ dbt('admin.statistics.title') }}</h1>
    <p class="text-sm text-gray-400 mb-6">{{ dbt('admin.statistics.subtitle') }}</p>

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

    {{-- Main Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.statistics.total_users') }}</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs text-gray-500 mt-1">+{{ $stats['users_today'] }} {{ dbt('admin.statistics.today') }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.statistics.total_jobs') }}</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_jobs']) }}</p>
            <p class="text-xs text-gray-500 mt-1">+{{ $stats['jobs_today'] }} {{ dbt('admin.statistics.today') }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.statistics.active_subscriptions') }}</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($stats['active_subscriptions']) }}</p>
        </div>
        <div class="card-panel">
            <p class="text-sm text-gray-500">{{ dbt('admin.statistics.total_revenue') }}</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    {{-- Job Status Distribution --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card-panel border-l-4 border-l-gray-500">
            <p class="text-sm text-gray-500">{{ dbt('status.job.pending') }}</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($stats['jobs_by_status']['pending']) }}</p>
        </div>
        <div class="card-panel border-l-4 border-l-yellow-500">
            <p class="text-sm text-gray-500">{{ dbt('status.job.processing') }}</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($stats['jobs_by_status']['processing']) }}</p>
        </div>
        <div class="card-panel border-l-4 border-l-green-500">
            <p class="text-sm text-gray-500">{{ dbt('status.job.done') }}</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($stats['jobs_by_status']['done']) }}</p>
        </div>
        <div class="card-panel border-l-4 border-l-red-500">
            <p class="text-sm text-gray-500">{{ dbt('status.job.failed') }}</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($stats['jobs_by_status']['failed']) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="card-panel">
            <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.statistics.users_growth') }}</h2>
            <div class="h-64 relative">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
        <div class="card-panel">
            <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.statistics.jobs_activity') }}</h2>
            <div class="h-64 relative">
                <canvas id="jobsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card-panel mb-6">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.statistics.revenue') }}</h2>
        <div class="h-64 relative">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Top Users --}}
    <div class="card-panel">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.statistics.top_users') }}</h2>
        @if($stats['top_users']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-gray-500 border-b border-gray-800">
                        <tr>
                            <th class="text-left py-3 px-4">#</th>
                            <th class="text-left py-3 px-4">{{ dbt('auth.name') }}</th>
                            <th class="text-left py-3 px-4">Email</th>
                            <th class="text-left py-3 px-4">{{ dbt('admin.statistics.jobs_count') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['top_users'] as $index => $user)
                            <tr class="border-b border-gray-800 hover:bg-gray-900/50">
                                <td class="py-3 px-4">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">{{ $user->name }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $user->email }}</td>
                                <td class="py-3 px-4 font-bold">{{ number_format($user->image_jobs_count) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">{{ dbt('admin.statistics.no_data') }}</p>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Common chart options
    const commonOptions = {
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
    };

    // Users Chart
    new Chart(document.getElementById('usersChart'), {
        type: 'bar',
        data: {
            labels: @json($stats['users_chart']['labels']),
            datasets: [{
                label: '{{ dbt('admin.statistics.new_users') }}',
                data: @json($stats['users_chart']['data']),
                backgroundColor: 'rgba(167, 139, 250, 0.5)',
                borderColor: '#a78bfa',
                borderWidth: 1,
            }]
        },
        options: commonOptions
    });

    // Jobs Chart
    new Chart(document.getElementById('jobsChart'), {
        type: 'line',
        data: {
            labels: @json($stats['jobs_chart']['labels']),
            datasets: [{
                label: '{{ dbt('admin.statistics.jobs_per_day') }}',
                data: @json($stats['jobs_chart']['data']),
                borderColor: 'rgb(52, 211, 153)',
                backgroundColor: 'rgba(52, 211, 153, 0.1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: commonOptions
    });

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: @json($stats['revenue_chart']['labels']),
            datasets: [{
                label: '{{ dbt('admin.statistics.daily_revenue') }}',
                data: @json($stats['revenue_chart']['data']),
                borderColor: 'rgb(251, 191, 36)',
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: commonOptions
    });
});
</script>
@endsection