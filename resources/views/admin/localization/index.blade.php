@extends('layouts.app')
@section('title', dbt('admin.localization.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-2">{{ dbt('admin.localization.title') }}</h1>
    <p class="text-sm text-gray-400 mb-6">{{ dbt('admin.localization.subtitle') }}</p>

    @include('admin._nav')

    <div class="card-panel mb-8">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="text-lg font-semibold">{{ dbt('admin.localization.existing_locales') }}</h2>
            <button type="button" onclick="document.getElementById('add-locale-form')?.classList.toggle('hidden')" class="px-3 py-1.5 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                {{ dbt('admin.localization.actions.add_locale') }}
            </button>
        </div>

        <form id="add-locale-form" method="POST" action="{{ route('admin.localization.locales.store') }}" class="hidden grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5 rounded-xl border border-gray-800 p-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.localization.code') }}</label>
                <input name="code" placeholder="de" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.localization.name') }}</label>
                <input name="name" placeholder="German" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.localization.native_name') }}</label>
                <input name="native_name" placeholder="Deutsch" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-300 pt-7">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> {{ dbt('admin.localization.is_active') }}</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_default" value="1"> {{ dbt('admin.localization.is_default') }}</label>
            </div>
            <div class="xl:col-span-4">
                <button class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                    {{ dbt('admin.localization.actions.add_locale') }}
                </button>
            </div>
        </form>

        <div>
            <div class="overflow-x-auto rounded-xl border border-gray-800">
                <table class="w-full text-sm min-w-[760px]">
                    <thead class="bg-gray-900/90">
                        <tr class="text-left text-gray-400 border-b border-gray-800">
                            <th class="py-3 px-4">{{ dbt('admin.localization.code') }}</th>
                            <th class="py-3 px-4">{{ dbt('admin.localization.name') }}</th>
                            <th class="py-3 px-4">{{ dbt('admin.localization.native_name') }}</th>
                            <th class="py-3 px-4">{{ dbt('admin.localization.is_active') }}</th>
                            <th class="py-3 px-4">{{ dbt('admin.localization.is_default') }}</th>
                            <th class="py-3 px-4 text-right">{{ dbt('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locales as $locale)
                            <tr class="border-b border-gray-900 align-top odd:bg-gray-950/40">
                                <td class="py-3 px-4 font-mono text-xs text-gray-300">{{ strtoupper($locale->code) }}</td>
                                <td class="py-3 px-4">
                                    <input type="hidden" form="locale-update-{{ md5($locale->code) }}" name="is_active" value="0">
                                    <input form="locale-update-{{ md5($locale->code) }}" name="name" value="{{ $locale->name }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                                </td>
                                <td class="py-3 px-4">
                                    <input form="locale-update-{{ md5($locale->code) }}" name="native_name" value="{{ $locale->native_name }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                                </td>
                                <td class="py-3 px-4">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-300">
                                        <input form="locale-update-{{ md5($locale->code) }}" type="checkbox" name="is_active" value="1" @checked($locale->is_active)>
                                        <span>{{ $locale->is_active ? dbt('common.yes') : dbt('common.no') }}</span>
                                    </label>
                                </td>
                                <td class="py-3 px-4">
                                    @if($locale->is_default)
                                        <span class="inline-flex items-center rounded-md border border-emerald-500/40 bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-200">
                                            {{ dbt('common.yes') }}
                                        </span>
                                    @else
                                        <button form="locale-update-{{ md5($locale->code) }}" name="is_default" value="1" class="px-2.5 py-1 rounded-lg border border-emerald-500/50 text-emerald-200 hover:bg-emerald-500/10 transition">
                                            {{ dbt('admin.localization.actions.make_default') }}
                                        </button>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <form id="locale-update-{{ md5($locale->code) }}" method="POST" action="{{ route('admin.localization.locales.update', $locale->code) }}" class="hidden">
                                            @csrf
                                            @method('PUT')
                                        </form>

                                        <button form="locale-update-{{ md5($locale->code) }}" class="px-3 py-1.5 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                                            {{ dbt('common.save') }}
                                        </button>

                                        @if(! $locale->is_default)
                                            <form method="POST" action="{{ route('admin.localization.locales.destroy', $locale->code) }}" onsubmit="return confirm('{{ dbt('admin.localization.confirm_delete_locale', ['code' => strtoupper($locale->code)]) }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-1.5 rounded-lg border border-red-500/60 text-red-200 hover:bg-red-500/10 transition">
                                                    {{ dbt('common.delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-panel mb-8">
        <h2 class="text-lg font-semibold mb-4">{{ dbt('admin.localization.ui_translations') }}</h2>
        <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4 mb-5">
            <form method="GET" action="{{ route('admin.localization.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.localization.locale') }}</label>
                    <select name="locale" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white" onchange="this.form.submit()">
                        @foreach($locales as $locale)
                            <option value="{{ $locale->code }}" @selected($selectedLocale === $locale->code)>{{ strtoupper($locale->code) }} — {{ $locale->native_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('common.search') }}</label>
                    <div class="flex gap-3">
                        <input name="q" value="{{ $filter }}" placeholder="{{ dbt('admin.localization.search_placeholder') }}" class="flex-1 rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white">
                        <button class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">{{ dbt('common.search') }}</button>
                        <a href="{{ route('admin.localization.index', ['locale' => $selectedLocale]) }}" class="px-4 py-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white transition">{{ dbt('common.reset') }}</a>
                    </div>
                </div>
            </form>

            <div class="text-sm text-gray-400 xl:text-right">
                <div>{{ dbt('admin.localization.editing_locale') }}: <span class="text-gray-200 font-medium">{{ strtoupper($selectedLocale) }}</span></div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 mb-4">
            <p class="text-sm text-gray-400">{{ dbt('admin.localization.table_hint', ['count' => $translationRows->count()]) }}</p>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-800">
            <table class="w-full text-sm min-w-[980px]">
                <thead class="bg-gray-900/90">
                    <tr class="text-left text-gray-400 border-b border-gray-800 align-bottom">
                        <th class="py-3 px-4 w-[28%]">{{ dbt('admin.localization.key') }}</th>
                        <th class="py-3 px-4 w-[56%]">{{ dbt('admin.localization.translation_text') }} <span class="text-xs text-gray-500">({{ strtoupper($selectedLocale) }})</span></th>
                        <th class="py-3 px-4 w-[16%] text-right">{{ dbt('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($translationRows as $row)
                        <tr class="border-b border-gray-900 align-top odd:bg-gray-950/40">
                            <td class="py-3 px-4">
                                <div class="font-mono text-xs text-violet-300 break-all">{{ $row['key'] }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <form id="translation-row-{{ md5($row['key']) }}" method="POST" action="{{ route('admin.localization.translations.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="locale" value="{{ $selectedLocale }}">
                                    <input type="hidden" name="q" value="{{ $filter }}">
                                    <textarea name="translations[{{ $row['key'] }}]" rows="3" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white">{{ $row['value'] }}</textarea>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button form="translation-row-{{ md5($row['key']) }}" class="px-3 py-1.5 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                                    {{ dbt('common.save') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 px-4 text-gray-500">{{ dbt('admin.localization.empty_translations') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
