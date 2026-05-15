@extends('layouts.app')
@section('title', dbt('admin.plans.title'))

@section('content')
@php
    $renderOptionGroup = function (string $name, array $options, array $selected = [], string $mode = 'text') {
        $selected = array_values(array_filter($selected));

        $labelFor = static function (string $option, string $mode): string {
            return match ($mode) {
                'format' => strtoupper($option),
                'feature' => $option,
                default => $option,
            };
        };

        return view('admin.plans.partials.option-group', [
            'name' => $name,
            'options' => $options,
            'selected' => $selected,
            'mode' => $mode,
            'labelFor' => $labelFor,
        ])->render();
    };
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-2">{{ dbt('admin.plans.title') }}</h1>
    <p class="text-sm text-gray-400 mb-6">{{ dbt('admin.plans.subtitle') }}</p>

    @include('admin._nav')

    <div class="card-panel mb-8" x-data="{ createLocale: @js($defaultLocale) }">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.plans.create_title') }}</h2>
        <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.name') }}</label>
                    <input name="name" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Slug</label>
                    <input name="slug" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.price_month') }}</label>
                    <input type="number" name="price_month" value="0" min="0" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.price_year') }}</label>
                    <input type="number" name="price_year" value="0" min="0" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.currency') }}</label>
                    <input name="currency" value="RUB" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 uppercase" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.max_files') }}</label>
                    <input type="number" name="max_files_per_job" value="10" min="1" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.max_file_size') }}</label>
                    <input type="number" name="max_file_size_mb" value="10" min="1" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.daily_limit') }}</label>
                    <input type="number" name="daily_jobs_limit" value="5" min="1" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.monthly_credits') }}</label>
                    <input type="number" name="monthly_credits" value="0" min="0" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.storage_ttl_hours') }}</label>
                    <input type="number" name="storage_ttl_hours" value="24" min="1" max="8760" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.sort_order') }}</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.description') }}</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2"></textarea>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.allowed_formats') }}</label>
                        {!! $renderOptionGroup('allowed_formats', $formatOptions, [], 'format') !!}
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.allowed_operations') }}</label>
                        {!! $renderOptionGroup('allowed_operations', $operationOptions) !!}
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-800 p-4 space-y-4">
                <div class="flex flex-col md:flex-row md:items-end gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.translation_locale') }}</label>
                        <select x-model="createLocale" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white min-w-[220px]">
                            @foreach($locales as $locale)
                                <option value="{{ $locale->code }}">{{ strtoupper($locale->code) }} — {{ $locale->native_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-sm text-gray-500">{{ dbt('admin.plans.translation_hint') }}</p>
                </div>

                @foreach($locales as $locale)
                    <div x-show="createLocale === '{{ $locale->code }}'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.name') }} <span class="text-gray-500">({{ strtoupper($locale->code) }})</span></label>
                            <input name="name_translations[{{ $locale->code }}]" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.description') }} <span class="text-gray-500">({{ strtoupper($locale->code) }})</span></label>
                            <textarea name="description_translations[{{ $locale->code }}]" rows="3" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2"></textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.feature_flags') }}</label>
                {!! $renderOptionGroup('feature_flags', $featureOptions, ['is_active'], 'feature') !!}
            </div>

            <button class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                {{ dbt('admin.plans.actions.create') }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        @foreach($plans as $plan)
            <div class="card-panel">
                <div class="text-xs text-gray-500">{{ $plan->name }}</div>
                <div class="text-2xl font-bold mt-1">{{ $analytics[$plan->id]['users_count'] ?? 0 }}</div>
                <div class="text-sm text-gray-400">{{ dbt('admin.plans.analytics.users') }}</div>
                <div class="mt-3 text-xs text-gray-500">
                    {{ dbt('admin.plans.analytics.subscriptions') }}: {{ $analytics[$plan->id]['subscriptions_count'] ?? 0 }}<br>
                    {{ dbt('admin.plans.analytics.jobs') }}: {{ $analytics[$plan->id]['jobs_count'] ?? 0 }}<br>
                    MRR: {{ number_format(($analytics[$plan->id]['monthly_revenue'] ?? 0) / 100, 0, '.', ' ') }} {{ $plan->currency }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="space-y-6">
        @foreach($plans as $plan)
            @php
                $selectedFeatures = collect($plan->feature_flags ?? [])->filter()->values()->all();
                if ($plan->watermark && ! in_array('watermark', $selectedFeatures, true)) {
                    $selectedFeatures[] = 'watermark';
                }
                if ($plan->api_access && ! in_array('api_access', $selectedFeatures, true)) {
                    $selectedFeatures[] = 'api_access';
                }
                if ($plan->priority_queue && ! in_array('priority_queue', $selectedFeatures, true)) {
                    $selectedFeatures[] = 'priority_queue';
                }
                if ($plan->is_active && ! in_array('is_active', $selectedFeatures, true)) {
                    $selectedFeatures[] = 'is_active';
                }
                if ($plan->is_popular && ! in_array('is_popular', $selectedFeatures, true)) {
                    $selectedFeatures[] = 'is_popular';
                }
            @endphp
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="card-panel space-y-4" x-data="{ locale: @js($defaultLocale) }">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-semibold">
                        {{ $plan->name }} <span class="text-sm text-gray-500">({{ $plan->slug }})</span>
                        @if($plan->is_popular)
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-violet-500/20 text-violet-200 border border-violet-500/30">{{ dbt('plans.popular') }}</span>
                        @endif
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-200 border border-blue-500/30">
                            {{ $plan->storage_ttl_formatted ?? '24h' }}
                        </span>
                    </h2>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                            {{ dbt('common.save') }}
                        </button>
                        <button form="delete-plan-{{ $plan->id }}" type="submit" class="px-4 py-2 rounded-lg border border-red-500/60 text-red-200 hover:bg-red-500/10 transition">
                            {{ dbt('common.delete') }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.name') }}</label>
                        <input name="name" value="{{ old('name.' . $plan->id, $plan->name) ?: $plan->name }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Slug</label>
                        <input name="slug" value="{{ $plan->slug }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.price_month') }}</label>
                        <input type="number" name="price_month" value="{{ $plan->price_month }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.price_year') }}</label>
                        <input type="number" name="price_year" value="{{ $plan->price_year }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.currency') }}</label>
                        <input name="currency" value="{{ $plan->currency }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 uppercase">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.max_files') }}</label>
                        <input type="number" name="max_files_per_job" value="{{ $plan->max_files_per_job }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.max_file_size') }}</label>
                        <input type="number" name="max_file_size_mb" value="{{ $plan->max_file_size_mb }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.daily_limit') }}</label>
                        <input type="number" name="daily_jobs_limit" value="{{ $plan->daily_jobs_limit }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.monthly_credits') }}</label>
                        <input type="number" name="monthly_credits" value="{{ $plan->monthly_credits }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.storage_ttl_hours') }}</label>
                        <input type="number" name="storage_ttl_hours" value="{{ $plan->storage_ttl_hours ?? 24 }}" min="1" max="8760" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.sort_order') }}</label>
                        <input type="number" name="sort_order" value="{{ $plan->sort_order }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.description') }}</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">{{ $plan->description }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.allowed_formats') }}</label>
                            {!! $renderOptionGroup('allowed_formats', $formatOptions, $plan->allowed_formats ?? [], 'format') !!}
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.allowed_operations') }}</label>
                            {!! $renderOptionGroup('allowed_operations', $operationOptions, $plan->allowed_operations ?? []) !!}
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 p-4 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-end gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.translation_locale') }}</label>
                            <select x-model="locale" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white min-w-[220px]">
                                @foreach($locales as $locale)
                                    <option value="{{ $locale->code }}">{{ strtoupper($locale->code) }} — {{ $locale->native_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-sm text-gray-500">{{ dbt('admin.plans.translation_hint') }}</p>
                    </div>

                    @foreach($locales as $locale)
                        <div x-show="locale === '{{ $locale->code }}'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.name') }} <span class="text-gray-500">({{ strtoupper($locale->code) }})</span></label>
                                <input name="name_translations[{{ $locale->code }}]" value="{{ $plan->name_translations[$locale->code] ?? '' }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.description') }} <span class="text-gray-500">({{ strtoupper($locale->code) }})</span></label>
                                <textarea name="description_translations[{{ $locale->code }}]" rows="3" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">{{ $plan->description_translations[$locale->code] ?? '' }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.plans.fields.feature_flags') }}</label>
                    {!! $renderOptionGroup('feature_flags', $featureOptions, $selectedFeatures, 'feature') !!}
                </div>
            </form>

            <form id="delete-plan-{{ $plan->id }}" method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="hidden" onsubmit="return confirm('{{ dbt('admin.plans.confirm_delete', ['name' => $plan->localizedName()]) }}')">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</div>
@endsection