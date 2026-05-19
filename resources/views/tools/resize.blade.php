@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.resize.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.resize.width') }}</label>
            <input type="number" id="resize-width" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" placeholder="Width in pixels">
        </div>
        
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.resize.height') }}</label>
            <input type="number" id="resize-height" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" placeholder="Height in pixels (optional)">
        </div>
        
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" id="maintain-aspect" checked class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-violet-500 focus:ring-violet-500">
            <span class="text-sm text-gray-300">{{ dbt('tools.resize.maintain_aspect') }}</span>
        </label>

        <div class="pt-2 border-t border-gray-800">
            <p class="text-xs text-gray-500 mb-2">{{ dbt('tools.resize.presets') }}</p>
            <div class="flex flex-wrap gap-2">
                <button onclick="setPreset(1920)" class="px-3 py-1 bg-gray-800 text-gray-300 rounded text-sm hover:bg-gray-700">1920px</button>
                <button onclick="setPreset(1280)" class="px-3 py-1 bg-gray-800 text-gray-300 rounded text-sm hover:bg-gray-700">1280px</button>
                <button onclick="setPreset(800)" class="px-3 py-1 bg-gray-800 text-gray-300 rounded text-sm hover:bg-gray-700">800px</button>
                <button onclick="setPreset(640)" class="px-3 py-1 bg-gray-800 text-gray-300 rounded text-sm hover:bg-gray-700">640px</button>
                <button onclick="setPreset(320)" class="px-3 py-1 bg-gray-800 text-gray-300 rounded text-sm hover:bg-gray-700">320px</button>
            </div>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'resize';
$toolName = dbt('tools.resize.name');
$toolDescription = dbt('tools.resize.description');
@endphp

@push('tool-scripts')
<script>
let originalWidth = 0;
let originalHeight = 0;

function onImageLoaded(width, height) {
    originalWidth = width;
    originalHeight = height;
    document.getElementById('resize-width').value = width;
    document.getElementById('resize-height').value = height;
}

function getToolOptions() {
    return {
        width: document.getElementById('resize-width').value || originalWidth,
        height: document.getElementById('resize-height').value || null,
        maintain_aspect: document.getElementById('maintain-aspect').checked
    };
}

function setPreset(width) {
    document.getElementById('resize-width').value = width;
    document.getElementById('resize-height').value = '';
    document.getElementById('maintain-aspect').checked = true;
}

// Sync aspect ratio logic
document.getElementById('resize-width').addEventListener('input', function() {
    if (document.getElementById('maintain-aspect').checked && originalWidth > 0) {
        const ratio = this.value / originalWidth;
        document.getElementById('resize-height').value = Math.round(originalHeight * ratio);
    }
});
</script>
@endpush