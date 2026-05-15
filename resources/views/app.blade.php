@extends('layouts.app')

@section('title', dbt('app.title'))

@section('content')
@php
    $allowedFormats = $capabilities['output_formats'] ?? config('bir.output_formats');
    $allowedSteps = $capabilities['pipeline_steps'] ?? config('bir.pipeline_steps');
    $rotateOptions = [
        'none' => dbt('app.rotate.none'),
        'left' => dbt('app.rotate.left'),
        '180' => dbt('app.rotate.flip'),
        'right' => dbt('app.rotate.right'),
    ];
    $flipOptions = [
        'none' => dbt('app.flip.none'),
        'horizontal' => dbt('app.flip.horizontal'),
        'vertical' => dbt('app.flip.vertical'),
    ];
@endphp
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10" x-data="imageProcessor()">
    {{-- Landing --}}
    <section class="mb-10 lg:mb-12">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight leading-tight mb-4">
                {{ dbt('app.hero.title') }}
            </h1>
            <p class="text-gray-400 text-base sm:text-lg leading-relaxed">
                {{ dbt('app.hero.description') }}
            </p>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-violet-400 text-xl font-bold">1</div>
                <p class="text-sm text-gray-300 mt-1">{{ dbt('app.steps.upload') }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-violet-400 text-xl font-bold">2</div>
                <p class="text-sm text-gray-300 mt-1">{{ dbt('app.steps.configure') }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-violet-400 text-xl font-bold">3</div>
                <p class="text-sm text-gray-300 mt-1">{{ dbt('app.steps.download') }}</p>
            </div>
        </div>
    </section>

    <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data" id="job-form" novalidate @submit.prevent="submitForm" class="pb-24 lg:pb-0">
        @csrf

        <div x-show="errors.length > 0" x-cloak class="mb-6 bg-red-900/50 border border-red-700 text-red-200 rounded-2xl px-4 py-3">
            <div class="font-semibold mb-2">{{ dbt('app.submit_failed') }}</div>
            <ul class="space-y-1 text-sm">
                <template x-for="(error, idx) in errors" :key="idx">
                    <li x-text="error"></li>
                </template>
            </ul>
        </div>

        {{-- Upload is always visible --}}
        <div class="mb-4">
            <div
                class="relative border-2 border-dashed rounded-2xl transition-colors cursor-pointer border-gray-700 hover:border-violet-500 bg-gray-900"
                :class="{
                    'border-violet-500 bg-violet-950/20': dragging,
                    'border-red-500 bg-red-950/20': fieldErrors.files,
                }"
                role="button"
                aria-label="{{ dbt('app.upload_zone_aria') }}"
                tabindex="0"
                @dragover.prevent.stop="dragging = true"
                @dragleave.prevent.stop="dragging = false"
                @drop.prevent.stop="onDrop($event)"
                @click="openPickerFromZone($event)"
                @keydown.enter.prevent="openPicker()"
                @keydown.space.prevent="openPicker()"
                :style="files.length === 0 ? 'min-height:420px' : 'min-height:180px'"
            >
                <input id="upload-files" type="file" name="files[]" multiple accept="image/*"
                       x-ref="fileInput"
                       class="sr-only"
                       @change="onFileChange($event)">

                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none p-6 text-center" x-show="files.length === 0">
                    <svg class="w-16 h-16 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <p class="text-gray-200 text-lg font-semibold mb-2">{{ dbt('app.upload_zone_title') }}</p>
                    <p class="text-gray-400 font-medium">{!! dbt('app.upload_zone_hint') !!}</p>
                    <p class="text-gray-600 text-sm mt-2">{{ dbt('app.upload_zone_formats', ['size' => config('bir.max_file_size_mb')]) }}</p>
                </div>

                <div class="absolute inset-0 flex items-center justify-center pointer-events-none" x-show="files.length > 0">
                    <div class="text-center px-6">
                        <p class="text-sm text-gray-300">{{ dbt('app.more_files.title') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ dbt('app.more_files.description') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content after files are selected --}}
        <div x-show="files.length > 0" x-cloak class="space-y-4">
            <div class="card-panel p-0 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-300">
                        {{ dbt('app.uploaded_files') }}: <span class="text-violet-400" x-text="files.length"></span>
                    </h2>
                    <button type="button" class="text-xs text-gray-500 hover:text-red-400 transition" @click.stop="clearFiles()">
                        {{ dbt('common.clear_all') }}
                    </button>
                </div>

                <div class="max-h-[280px] overflow-y-auto">
                    <template x-for="(file, idx) in files" :key="idx">
                        <div class="px-4 py-3 border-b border-gray-800 last:border-b-0 flex items-center justify-between gap-3 min-h-14">
                            <div class="min-w-0">
                                <p class="text-sm text-gray-200 truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-500" x-text="formatFileSize(file.raw.size)"></p>
                            </div>
                            <button type="button"
                                    class="w-11 h-11 rounded-full bg-red-900/50 text-red-300 hover:bg-red-900/70 transition shrink-0"
                                    @click.stop="removeFile(idx)">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="space-y-3 lg:space-y-4">
                <div class="card-panel p-0 overflow-hidden">
                    <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 text-left"
                            @click="toggleMobilePanel('format')"
                            :aria-expanded="panelVisible('format')">
                        <h3 class="text-sm font-semibold text-gray-300">{{ dbt('app.sections.format_quality') }}</h3>
                        <svg class="w-4 h-4 text-gray-500 transition-transform lg:hidden" :class="panelVisible('format') ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="px-4 pb-4 border-t border-gray-800" x-show="panelVisible('format')" x-transition.duration.300ms>
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            @foreach($allowedFormats as $fmt)
                            <label class="format-btn">
                                <input type="radio" name="output_format" value="{{ $fmt }}" class="sr-only peer" @if($fmt === 'jpg') checked @endif>
                                <span class="block text-center py-2 rounded-lg border border-gray-700 cursor-pointer text-sm font-medium peer-checked:bg-violet-600 peer-checked:border-violet-600 peer-checked:text-white hover:border-gray-500 transition">
                                    {{ strtoupper($fmt) }}
                                </span>
                            </label>
                            @endforeach
                        </div>

                        <div class="mt-3" x-data="{ quality: 85 }">
                            <label class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>{{ dbt('app.quality') }}</span>
                                <span x-text="quality + '%'" class="text-violet-400 font-medium"></span>
                            </label>
                            <input type="range" name="output_quality" min="1" max="100" x-model="quality" class="range-slider w-full">
                        </div>
                    </div>
                </div>

                @if(in_array('resize', $allowedSteps, true))
                <div class="card-panel p-0 overflow-hidden" x-data="{ mode: 'none' }">
                    <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 text-left"
                            @click="toggleMobilePanel('resize')"
                            :aria-expanded="panelVisible('resize')">
                        <h3 class="text-sm font-semibold text-gray-300">{{ dbt('app.sections.resize') }}</h3>
                        <svg class="w-4 h-4 text-gray-500 transition-transform lg:hidden" :class="panelVisible('resize') ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="px-4 pb-4 border-t border-gray-800" x-show="panelVisible('resize')" x-transition.duration.300ms>
                        <select name="resize_mode" x-model="mode" class="input-field w-full mt-3">
                            <option value="none">{{ dbt('app.resize.none') }}</option>
                            <option value="width">{{ dbt('app.resize.width') }}</option>
                            <option value="height">{{ dbt('app.resize.height') }}</option>
                            <option value="fixed">{{ dbt('app.resize.fixed') }}</option>
                            <option value="fit">{{ dbt('app.resize.fit') }}</option>
                            <option value="cover">{{ dbt('app.resize.cover') }}</option>
                        </select>
                        <div class="mt-2 grid grid-cols-2 gap-2" x-show="mode !== 'none'">
                            <div x-show="mode !== 'height'">
                                <label class="label-field">{{ dbt('app.width_px') }}</label>
                                <input type="number" name="resize_width" min="1" max="10000" placeholder="1920" class="input-field w-full">
                            </div>
                            <div x-show="mode !== 'width'">
                                <label class="label-field">{{ dbt('app.height_px') }}</label>
                                <input type="number" name="resize_height" min="1" max="10000" placeholder="1080" class="input-field w-full">
                            </div>
                        </div>
                        <label class="mt-2 flex items-center gap-2 text-sm text-gray-400 cursor-pointer" x-show="mode !== 'none'">
                            <input type="checkbox" name="resize_upscale" value="1" class="rounded border-gray-600">
                            {{ dbt('app.allow_upscale') }}
                        </label>
                    </div>
                </div>
                @endif

                @if(in_array('rotate', $allowedSteps, true))
                <div class="card-panel p-0 overflow-hidden">
                    <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 text-left"
                            @click="toggleMobilePanel('rotate')"
                            :aria-expanded="panelVisible('rotate')">
                        <h3 class="text-sm font-semibold text-gray-300">{{ dbt('app.sections.rotate') }}</h3>
                        <svg class="w-4 h-4 text-gray-500 transition-transform lg:hidden" :class="panelVisible('rotate') ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="px-4 pb-4 border-t border-gray-800" x-show="panelVisible('rotate')" x-transition.duration.300ms>
                        <div class="grid grid-cols-4 gap-2 mt-3">
                            @foreach($rotateOptions as $val => $label)
                            <label class="format-btn">
                                <input type="radio" name="rotate_direction" value="{{ $val }}" class="sr-only peer" @if($val === 'none') checked @endif>
                                <span class="block text-center py-2 rounded-lg border border-gray-700 cursor-pointer text-xs font-medium peer-checked:bg-violet-600 peer-checked:border-violet-600 peer-checked:text-white hover:border-gray-500 transition">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('flip', $allowedSteps, true))
                <div class="card-panel p-0 overflow-hidden">
                    <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 text-left"
                            @click="toggleMobilePanel('flip')"
                            :aria-expanded="panelVisible('flip')">
                        <h3 class="text-sm font-semibold text-gray-300">{{ dbt('app.sections.flip') }}</h3>
                        <svg class="w-4 h-4 text-gray-500 transition-transform lg:hidden" :class="panelVisible('flip') ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="px-4 pb-4 border-t border-gray-800" x-show="panelVisible('flip')" x-transition.duration.300ms>
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            @foreach($flipOptions as $val => $label)
                            <label class="format-btn">
                                <input type="radio" name="flip_axis" value="{{ $val }}" class="sr-only peer" @if($val === 'none') checked @endif>
                                <span class="block text-center py-2 rounded-lg border border-gray-700 cursor-pointer text-xs font-medium peer-checked:bg-violet-600 peer-checked:border-violet-600 peer-checked:text-white hover:border-gray-500 transition">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="card-panel p-0 overflow-hidden">
                    <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 text-left"
                            @click="toggleMobilePanel('rename')"
                            :aria-expanded="panelVisible('rename')">
                        <h3 class="text-sm font-semibold text-gray-300">{{ dbt('app.sections.rename') }}</h3>
                        <svg class="w-4 h-4 text-gray-500 transition-transform lg:hidden" :class="panelVisible('rename') ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="px-4 pb-4 border-t border-gray-800" x-show="panelVisible('rename')" x-transition.duration.300ms>
                        <div class="mb-3 mt-3">
                            <label class="label-field">{{ dbt('app.rename.mode') }}</label>
                            <select name="rename_mode" class="input-field w-full">
                                <option value="original">{{ dbt('app.rename.original') }}</option>
                                <option value="sequence">{{ dbt('app.rename.sequence') }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="label-field">{{ dbt('app.rename.prefix') }}</label>
                                <input type="text" name="rename_prefix" maxlength="50" placeholder="new_" class="input-field w-full">
                            </div>
                            <div>
                                <label class="label-field">{{ dbt('app.rename.suffix') }}</label>
                                <input type="text" name="rename_suffix" maxlength="50" placeholder="_resized" class="input-field w-full">
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="label-field">{{ dbt('app.rename.start_number') }}</label>
                            <input type="number" name="rename_start_number" min="1" max="999999" value="1" placeholder="001" class="input-field w-full">
                            <p class="text-xs text-gray-500 mt-1">{{ dbt('app.rename.sequence_hint') }}</p>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submit-btn"
                        class="hidden lg:flex w-full btn-primary py-4 rounded-2xl text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed transition items-center justify-center"
                        :disabled="files.length === 0">
                    <span x-show="!submitting">
                        {{ dbt('app.process') }}
                        <span x-show="files.length > 0" x-text="' (' + files.length + ')'" ></span>
                    </span>
                    <span x-show="submitting" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ dbt('app.uploading') }}
                    </span>
                </button>
            </div>
        </div>

        <div x-show="files.length > 0" x-cloak class="lg:hidden fixed inset-x-0 bottom-0 z-40 border-t border-gray-800 bg-gray-950/95 backdrop-blur px-4 py-3">
            <button type="submit"
                    class="w-full h-14 btn-primary rounded-xl text-base font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition"
                    :disabled="files.length === 0">
                <span x-show="!submitting">
                    {{ dbt('app.process') }}
                    <span x-show="files.length > 0" x-text="' (' + files.length + ')' "></span>
                </span>
                <span x-show="submitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    {{ dbt('app.uploading') }}
                </span>
            </button>
        </div>
    </form>
</div>
@endsection
