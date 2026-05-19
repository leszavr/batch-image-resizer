@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.crop.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.crop.width') }}</label>
            <input type="number" id="crop-width" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" placeholder="Auto">
        </div>
        
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.crop.height') }}</label>
            <input type="number" id="crop-height" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" placeholder="Auto">
        </div>
        
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.crop.position_x') }}</label>
            <input type="number" id="crop-x" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" value="0">
        </div>
        
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.crop.position_y') }}</label>
            <input type="number" id="crop-y" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" value="0">
        </div>

        <div class="flex gap-2 pt-2">
            <button onclick="setPreset('1:1')" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">1:1</button>
            <button onclick="setPreset('16:9')" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">16:9</button>
            <button onclick="setPreset('4:3')" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">4:3</button>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'crop';
$toolName = dbt('tools.crop.name');
$toolDescription = dbt('tools.crop.description');
@endphp

@push('tool-scripts')
<script>
let originalWidth = 0;
let originalHeight = 0;

function onImageLoaded(width, height) {
    originalWidth = width;
    originalHeight = height;
    
    // Set default crop to center
    const cropW = Math.min(width, height);
    const cropH = cropW;
    document.getElementById('crop-width').value = cropW;
    document.getElementById('crop-height').value = cropH;
    document.getElementById('crop-x').value = Math.floor((width - cropW) / 2);
    document.getElementById('crop-y').value = Math.floor((height - cropH) / 2);
}

function getToolOptions() {
    return {
        width: document.getElementById('crop-width').value || originalWidth,
        height: document.getElementById('crop-height').value || originalHeight,
        x: document.getElementById('crop-x').value || 0,
        y: document.getElementById('crop-y').value || 0
    };
}

function setPreset(ratio) {
    if (originalWidth === 0) return;
    
    const [w, h] = ratio.split(':').map(Number);
    const maxSize = Math.min(originalWidth, originalHeight);
    
    let newW, newH;
    if (originalWidth / originalHeight > w / h) {
        newH = maxSize;
        newW = Math.round(newH * w / h);
    } else {
        newW = maxSize;
        newH = Math.round(newW * h / w);
    }
    
    document.getElementById('crop-width').value = newW;
    document.getElementById('crop-height').value = newH;
    document.getElementById('crop-x').value = Math.floor((originalWidth - newW) / 2);
    document.getElementById('crop-y').value = Math.floor((originalHeight - newH) / 2);
}
</script>
@endpush