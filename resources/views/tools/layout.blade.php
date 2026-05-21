@extends('layouts.app')

@section('title', $toolName . ' — ' . dbt('tools.meta.title'))

@section('content')
<div class="min-h-screen bg-gray-950 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('tools.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ dbt('tools.back_to_tools') }}
            </a>
            <h1 class="text-3xl font-bold text-white">{{ $toolName }}</h1>
            <p class="text-gray-400 mt-2">{{ $toolDescription ?? '' }}</p>
        </div>

        {{-- Tool Container --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Upload & Controls --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Upload Zone --}}
                <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.upload.title') }}</h3>
                    
                    <div 
                        id="drop-zone"
                        class="border-2 border-dashed border-gray-700 rounded-lg p-8 text-center cursor-pointer hover:border-violet-500 hover:bg-gray-800/50 transition"
                        data-tool="{{ $toolId }}"
                    >
                        <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-gray-300 mb-2">{{ dbt('tools.upload.drag_drop') }}</p>
                        <p class="text-gray-500 text-sm">{{ dbt('tools.upload.or_click') }}</p>
                        <input type="file" id="file-input" class="hidden" accept="image/*">
                    </div>

                    <div id="upload-progress" class="hidden mt-4">
                        <div class="w-full bg-gray-800 rounded-full h-2">
                            <div class="bg-violet-500 h-2 rounded-full transition-all" style="width: 0%" id="progress-bar"></div>
                        </div>
                        <p class="text-sm text-gray-400 mt-2" id="upload-status">{{ dbt('tools.upload.processing') }}</p>
                    </div>
                </div>

                {{-- Tool Controls --}}
                <div id="tool-controls">
                    @yield('tool-controls')
                </div>
            </div>

            {{-- Right: Preview Area --}}
            <div class="lg:col-span-2">
                <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 h-full min-h-[500px]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">{{ dbt('tools.preview.title') }}</h3>
                        <div class="flex items-center gap-4">
                            <button 
                                id="compare-btn"
                                class="hidden px-4 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm"
                                onmousedown="showOriginal()"
                                onmouseup="showResult()"
                                onmouseleave="showResult()"
                                ontouchstart="showOriginal()"
                                ontouchend="showResult()"
                            >
                                {{ dbt('tools.preview.hold_to_compare') }}
                            </button>
                            <a 
                                id="download-btn"
                                href="#"
                                class="hidden inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-500 transition text-sm"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ dbt('tools.preview.download') }}
                            </a>
                        </div>
                    </div>

                    {{-- Preview Container --}}
                    <div id="preview-container" class="relative bg-gray-950 rounded-lg overflow-hidden flex items-center justify-center min-h-[400px]">
                        <div id="empty-state" class="text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p>{{ dbt('tools.preview.empty') }}</p>
                        </div>
                        
                        <img id="original-preview" class="hidden max-w-full max-h-[500px] object-contain" alt="Original">
                        <img id="result-preview" class="hidden max-w-full max-h-[500px] object-contain" alt="Result">
                    </div>

                    {{-- Image Info --}}
                    <div id="image-info" class="hidden mt-4 flex items-center justify-between text-sm text-gray-400">
                        <span id="dimensions"></span>
                        <span id="file-size"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const TOOL_ID = '{{ $toolId }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const PROCESS_URL = '{{ route('tools.process') }}';
    
    let currentSessionId = null;
    let originalImageData = null;
    let currentFile = null;
    let activeRequest = null;
    let activeRequestId = 0;
    let processDebounceTimer = null;

    // Drop zone handling
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const uploadStatus = document.getElementById('upload-status');
    const toolControls = document.getElementById('tool-controls');

    dropZone.addEventListener('click', () => fileInput.click());
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-violet-500', 'bg-gray-800/50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-violet-500', 'bg-gray-800/50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-violet-500', 'bg-gray-800/50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            alert('{{ dbt('tools.errors.not_image') }}');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert('{{ dbt('tools.errors.too_large') }}');
            return;
        }

        currentFile = file;
        currentSessionId = null;

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const originalPreview = document.getElementById('original-preview');
            const resultPreview = document.getElementById('result-preview');
            const emptyState = document.getElementById('empty-state');
            const imageInfo = document.getElementById('image-info');
            const dimensions = document.getElementById('dimensions');
            const fileSize = document.getElementById('file-size');
            const compareBtn = document.getElementById('compare-btn');
            const downloadBtn = document.getElementById('download-btn');

            originalImageData = e.target.result;
            originalPreview.src = e.target.result;
            originalPreview.classList.remove('hidden');
            resultPreview.classList.add('hidden');
            resultPreview.removeAttribute('src');
            emptyState.classList.add('hidden');
            imageInfo.classList.remove('hidden');
            compareBtn.classList.add('hidden');
            downloadBtn.classList.add('hidden');
            downloadBtn.href = '#';
            
            // Get image dimensions
            const img = new Image();
            img.onload = () => {
                dimensions.textContent = `${img.width} × ${img.height}px`;
                onImageLoaded(img.width, img.height);
                processCurrentFile({ immediate: true });
            };
            img.src = e.target.result;
            
            fileSize.textContent = formatFileSize(file.size);
        };
        reader.readAsDataURL(file);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function processCurrentFile({ immediate = false } = {}) {
        if (!currentFile) {
            return;
        }

        window.clearTimeout(processDebounceTimer);

        const run = () => uploadAndProcess(currentFile);

        if (immediate) {
            run();
            return;
        }

        processDebounceTimer = window.setTimeout(run, 250);
    }

    function uploadAndProcess(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('tool', TOOL_ID);
        formData.append('_token', CSRF_TOKEN);

        // Add tool-specific options
        const options = getToolOptions();
        if (options) {
            Object.keys(options).forEach(key => {
                formData.append(`options[${key}]`, options[key]);
            });
        }

        const toolFiles = getToolFiles();
        if (toolFiles) {
            Object.keys(toolFiles).forEach(key => {
                if (toolFiles[key] instanceof File) {
                    formData.append(key, toolFiles[key]);
                }
            });
        }

        uploadProgress.classList.remove('hidden');
        progressBar.style.width = '0%';
        uploadStatus.textContent = '{{ dbt('tools.upload.processing') }}';
        progressBar.classList.remove('bg-red-500');

        if (activeRequest) {
            activeRequest.abort();
        }

        const xhr = new XMLHttpRequest();
        const requestId = ++activeRequestId;
        activeRequest = xhr;
        
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const progress = (e.loaded / e.total) * 50;
                progressBar.style.width = progress + '%';
            }
        });

        xhr.addEventListener('load', () => {
            if (requestId !== activeRequestId) {
                return;
            }

            activeRequest = null;

            let response = null;

            try {
                response = xhr.responseText ? JSON.parse(xhr.responseText) : null;
            } catch (e) {
                console.error('JSON parse error:', e);
            }

            if (xhr.status === 200 && response?.success) {
                currentSessionId = response.session_id;
                progressBar.style.width = '100%';
                uploadStatus.textContent = '{{ dbt('tools.upload.complete') }}';
                
                // Show result
                const resultPreview = document.getElementById('result-preview');
                const compareBtn = document.getElementById('compare-btn');
                const downloadBtn = document.getElementById('download-btn');
                
                resultPreview.src = response.result_url;
                resultPreview.classList.remove('hidden');
                resultPreview.onload = () => {
                    document.getElementById('original-preview').classList.add('hidden');
                };
                
                compareBtn.classList.remove('hidden');
                downloadBtn.href = response.download_url;
                downloadBtn.classList.remove('hidden');
                
                window.setTimeout(() => {
                    if (!activeRequest) {
                        uploadProgress.classList.add('hidden');
                    }
                }, 400);

                return;
            }

            console.error('HTTP Error:', xhr.status, xhr.statusText, response);
            uploadStatus.textContent = response?.error || '{{ dbt('tools.errors.processing_failed') }}';
            progressBar.classList.add('bg-red-500');
        });

        xhr.addEventListener('error', () => {
            if (requestId !== activeRequestId) {
                return;
            }

            activeRequest = null;
            console.error('XHR Network Error');
            uploadStatus.textContent = '{{ dbt('tools.errors.upload_failed') }}';
            progressBar.classList.add('bg-red-500');
        });

        xhr.addEventListener('abort', () => {
            if (requestId !== activeRequestId) {
                return;
            }

            activeRequest = null;
        });

        xhr.open('POST', PROCESS_URL);
        xhr.send(formData);
    }

    function setupLivePreviewListeners() {
        if (!toolControls) {
            return;
        }

        toolControls.addEventListener('input', (event) => {
            if (!currentFile || !(event.target instanceof HTMLInputElement || event.target instanceof HTMLSelectElement || event.target instanceof HTMLTextAreaElement)) {
                return;
            }

            processCurrentFile();
        });

        toolControls.addEventListener('change', (event) => {
            if (!currentFile || !(event.target instanceof HTMLInputElement || event.target instanceof HTMLSelectElement || event.target instanceof HTMLTextAreaElement)) {
                return;
            }

            processCurrentFile();
        });

        toolControls.addEventListener('click', (event) => {
            if (!currentFile) {
                return;
            }

            const button = event.target.closest('button');
            if (!button) {
                return;
            }

            processCurrentFile();
        });
    }

    function showOriginal() {
        const original = document.getElementById('original-preview');
        const result = document.getElementById('result-preview');
        if (currentSessionId) {
            original.classList.remove('hidden');
            result.classList.add('hidden');
        }
    }

    function showResult() {
        const original = document.getElementById('original-preview');
        const result = document.getElementById('result-preview');
        if (currentSessionId) {
            original.classList.add('hidden');
            result.classList.remove('hidden');
        }
    }

    // Override in specific tool views
    function getToolOptions() {
        return {};
    }

    function getToolFiles() {
        return {};
    }

    function onImageLoaded(width, height) {
        // Override in specific tool views
    }

    setupLivePreviewListeners();
</script>

@stack('tool-scripts')
@endpush
@endsection