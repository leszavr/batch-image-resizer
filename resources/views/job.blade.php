@extends('layouts.app')
@section('title', dbt('job.title', ['uuid' => $imageJob->uuid]))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10"
     x-data="jobPoller('{{ $imageJob->uuid }}', '{{ $imageJob->status }}')"
     x-init="init()"
     @keydown.window="onKeyDown($event)">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('home') }}" class="text-gray-500 hover:text-white transition text-sm">← {{ dbt('job.new_job') }}</a>
        <span class="text-gray-700">|</span>
        <h1 class="text-xl font-bold">{{ dbt('job.processing_title') }}</h1>
    </div>

    {{-- Status card --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                {{-- Status badge --}}
                <span x-text="statusLabel" :class="statusClass"
                      class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"></span>
                <span class="text-gray-400 text-sm">
                    {{ dbt('job.files_label') }}: <span x-text="total" class="text-white font-medium"></span>
                </span>
            </div>
            <span class="text-gray-500 text-sm">{{ $imageJob->created_at->diffForHumans() }}</span>
        </div>

        {{-- Progress bar --}}
        <div class="w-full bg-gray-800 rounded-full h-3 mb-3" x-show="!isFinished || progress < 100">
            <div class="h-3 rounded-full transition-all duration-500"
                 :class="statusBarClass"
                 :style="'width:' + progress + '%'"></div>
        </div>

        <div class="flex justify-between text-sm text-gray-400">
            <span>{{ dbt('job.processed') }}: <span x-text="processed" class="text-emerald-400 font-medium"></span></span>
            <span>{{ dbt('job.errors') }}: <span x-text="failed" :class="failed > 0 ? 'text-red-400' : 'text-gray-500'" class="font-medium"></span></span>
            <span x-text="progress + '%'" class="text-gray-300 font-medium"></span>
        </div>

        {{-- Processing spinner --}}
        <div x-show="status === 'pending' || status === 'processing'" class="mt-4 flex items-center gap-2 text-sm text-gray-400">
            <svg class="animate-spin w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ dbt('job.pending_message') }}
        </div>

        {{-- Download button --}}
        <div x-show="status === 'done'" class="mt-4">
            <a :href="downloadUrl" class="btn-primary inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ dbt('job.download_zip') }}
            </a>
            <p class="text-xs text-gray-500 mt-2">
                {{ dbt('job.archive_ttl', ['hours' => config('ipp.storage_ttl_hours')]) }}
            </p>

            <div class="mt-5 bg-gray-950 border border-gray-800 rounded-xl p-4" x-show="resultFiles.length > 0" x-cloak>
                <h3 class="text-sm font-semibold text-gray-200 mb-3">{{ dbt('job.processed_files') }}</h3>
                <div class="max-h-[420px] overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <template x-for="file in resultFiles" :key="file.id">
                            <button type="button"
                                    class="group block text-left"
                                    @click="openPreview(file)">
                                <div class="aspect-square rounded-lg overflow-hidden border border-gray-800 bg-gray-900">
                                    <img :src="file.preview_url" :alt="file.name" class="w-full h-full object-cover transition group-hover:scale-[1.02]">
                                </div>
                                <p class="text-xs text-gray-400 mt-1 truncate" x-text="file.name"></p>
                                <p class="text-[11px] text-gray-500 truncate" x-text="fileMeta(file)"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Failed --}}
        <div x-show="status === 'failed'" class="mt-4 text-sm text-red-400">
            {{ dbt('job.failed_message') }}
        </div>
    </div>

    <div x-show="previewOpen" x-cloak class="fixed inset-0 z-50 bg-black/80 p-4 sm:p-8 flex items-center justify-center" @click.self="closePreview()">
        <button type="button"
                x-show="resultFiles.length > 1"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-gray-900/90 text-gray-200"
                @click="prevPreview()">
            ←
        </button>
        <button type="button" class="absolute top-4 right-4 w-11 h-11 rounded-full bg-gray-900/90 text-gray-200" @click="closePreview()">✕</button>
        <button type="button"
                x-show="resultFiles.length > 1"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-gray-900/90 text-gray-200"
                @click="nextPreview()">
            →
        </button>
        <img :src="previewFile?.preview_url" :alt="previewFile?.name || 'Preview'" class="max-w-full max-h-full object-contain rounded-lg border border-gray-700">
    </div>

    {{-- File list --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-800">
            <h2 class="font-semibold">{{ dbt('job.files_section') }}</h2>
        </div>
        <div class="divide-y divide-gray-800">
            @foreach($imageJob->files as $file)
            <div class="px-6 py-3 flex items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $file->original_name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $file->originalSizeFormatted() }}
                        @if($file->original_width)
                            · {{ $file->original_width }}×{{ $file->original_height }}px
                        @endif
                    </p>
                </div>
                @if($file->result_width)
                <div class="text-right text-xs text-gray-500">
                    {{ $file->resultSizeFormatted() }}
                    · {{ $file->result_width }}×{{ $file->result_height }}px
                </div>
                @endif
                <div>
                    @if($file->status === 'done')
                        <span class="badge-success">✓</span>
                    @elseif($file->status === 'failed')
                        <span class="badge-error" title="{{ $file->error_message }}">✗</span>
                    @elseif($file->status === 'processing')
                        <span class="badge-info">...</span>
                    @else
                        <span class="badge-neutral">—</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
