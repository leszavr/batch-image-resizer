@extends('layouts.app')
@section('title', dbt('nav.plans'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-8">{{ dbt('nav.plans') }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        @foreach($plans as $plan)
            <div class="card-panel relative {{ $plan->is_popular ? 'ring-1 ring-violet-500 shadow-lg shadow-violet-500/10' : '' }}">
                @if($plan->is_popular)
                    <span class="absolute -top-3 right-4 px-3 py-1 rounded-full text-xs font-semibold bg-violet-500 text-white">{{ dbt('plans.popular') }}</span>
                @endif

                <h2 class="text-xl font-bold">{{ $plan->localizedName() }}</h2>
                <p class="text-sm text-gray-400 mt-1">{{ $plan->localizedDescription() }}</p>
                <p class="text-2xl font-black mt-4">{{ $plan->priceMonthFormatted() }}</p>
                <ul class="mt-4 text-sm text-gray-300 space-y-1">
                    <li>{{ dbt('plans.max_files') }}: {{ $plan->max_files_per_job }}</li>
                    <li>{{ dbt('plans.max_file_size') }}: {{ $plan->max_file_size_mb }}MB</li>
                    <li>{{ dbt('plans.daily_limit') }}: {{ $plan->daily_jobs_limit }}</li>
                    <li>{{ dbt('plans.ai_credits') }}: {{ $plan->monthly_credits }}</li>
                </ul>
            </div>
        @endforeach
    </div>
</div>
@endsection
