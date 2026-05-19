@extends('layouts.app')

@section('title', dbt('tools.meta.index_title'))

@section('content')
<div class="min-h-screen bg-gray-950 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">{{ dbt('tools.index.title') }}</h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">{{ dbt('tools.index.subtitle') }}</p>
        </div>

        {{-- Basic Tools Section --}}
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-6">{{ dbt('tools.category.basic') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($tools['basic'] as $tool)
                <a href="{{ route($tool['route']) }}" class="group bg-gray-900 rounded-xl border border-gray-800 p-6 hover:border-violet-500 hover:bg-gray-800/50 transition">
                    <div class="w-14 h-14 bg-violet-500/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-violet-500/30 transition">
                        @include('tools.icons.' . $tool['icon'])
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-violet-400 transition">{{ dbt($tool['name']) }}</h3>
                    <p class="text-gray-400 text-sm">{{ dbt($tool['description']) }}</p>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-6">{{ dbt('tools.category.filters') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($tools['filters'] as $tool)
                <a href="{{ route($tool['route']) }}" class="group bg-gray-900 rounded-xl border border-gray-800 p-6 hover:border-amber-500 hover:bg-gray-800/50 transition">
                    <div class="w-14 h-14 bg-amber-500/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-amber-500/30 transition">
                        @include('tools.icons.' . $tool['icon'])
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-amber-400 transition">{{ dbt($tool['name']) }}</h3>
                    <p class="text-gray-400 text-sm">{{ dbt($tool['description']) }}</p>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Features --}}
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 border border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 bg-violet-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ dbt('tools.features.dragdrop.title') }}</h3>
                    <p class="text-gray-400 text-sm">{{ dbt('tools.features.dragdrop.desc') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-violet-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ dbt('tools.features.preview.title') }}</h3>
                    <p class="text-gray-400 text-sm">{{ dbt('tools.features.preview.desc') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-violet-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ dbt('tools.features.download.title') }}</h3>
                    <p class="text-gray-400 text-sm">{{ dbt('tools.features.download.desc') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection