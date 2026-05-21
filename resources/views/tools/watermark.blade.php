@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.watermark.settings') }}</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.type') }}</label>
            <select id="watermark-type" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
                <option value="text">{{ dbt('tools.watermark.type_text') }}</option>
                <option value="image">{{ dbt('tools.watermark.type_image') }}</option>
            </select>
        </div>

        <div id="watermark-text-controls">
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.text') }}</label>
            <input type="text" id="watermark-text" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none" value="Watermark" maxlength="60">

            <label class="block text-sm text-gray-400 mb-1 mt-3">{{ dbt('tools.watermark.color') }}</label>
            <input type="color" id="watermark-color" class="w-full h-10 bg-gray-800 border border-gray-700 rounded-lg px-1 py-1" value="#ffffff">
        </div>

        <div id="watermark-image-controls" class="hidden">
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.upload_logo') }}</label>
            <input type="file" id="watermark-image" accept="image/*" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-gray-300 focus:border-violet-500 focus:outline-none">
            <p class="text-xs text-gray-500 mt-2">{{ dbt('tools.watermark.upload_logo_hint') }}</p>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.position') }}</label>
            <select id="watermark-position" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
                <option value="top-left">{{ dbt('tools.watermark.top_left') }}</option>
                <option value="top-right">{{ dbt('tools.watermark.top_right') }}</option>
                <option value="bottom-left">{{ dbt('tools.watermark.bottom_left') }}</option>
                <option value="bottom-right" selected>{{ dbt('tools.watermark.bottom_right') }}</option>
                <option value="center">{{ dbt('tools.watermark.center') }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.size') }} <span id="watermark-size-value" class="text-gray-300">28</span></label>
            <input type="range" id="watermark-size" min="10" max="100" value="28" class="w-full">
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.opacity') }} <span id="watermark-opacity-value" class="text-gray-300">60%</span></label>
            <input type="range" id="watermark-opacity" min="10" max="100" value="60" class="w-full">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.offset_x') }}</label>
                <input type="number" id="watermark-offset-x" min="0" max="500" value="20" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.watermark.offset_y') }}</label>
                <input type="number" id="watermark-offset-y" min="0" max="500" value="20" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'watermark';
$toolName = dbt('tools.watermark.name');
$toolDescription = dbt('tools.watermark.description');
@endphp

@push('tool-scripts')
<script>
const watermarkType = document.getElementById('watermark-type');
const textControls = document.getElementById('watermark-text-controls');
const imageControls = document.getElementById('watermark-image-controls');
const sizeInput = document.getElementById('watermark-size');
const opacityInput = document.getElementById('watermark-opacity');
const sizeValue = document.getElementById('watermark-size-value');
const opacityValue = document.getElementById('watermark-opacity-value');
const watermarkImageInput = document.getElementById('watermark-image');

watermarkType.addEventListener('change', () => {
    const imageMode = watermarkType.value === 'image';
    textControls.classList.toggle('hidden', imageMode);
    imageControls.classList.toggle('hidden', !imageMode);
    processCurrentFile({ immediate: true });
});

sizeInput.addEventListener('input', () => {
    sizeValue.textContent = sizeInput.value;
});

opacityInput.addEventListener('input', () => {
    opacityValue.textContent = `${opacityInput.value}%`;
});

watermarkImageInput.addEventListener('change', () => {
    if (watermarkType.value === 'image') {
        processCurrentFile({ immediate: true });
    }
});

function getToolOptions() {
    return {
        type: watermarkType.value,
        text: document.getElementById('watermark-text').value,
        color: document.getElementById('watermark-color').value,
        position: document.getElementById('watermark-position').value,
        size: sizeInput.value,
        opacity: opacityInput.value,
        offset_x: document.getElementById('watermark-offset-x').value,
        offset_y: document.getElementById('watermark-offset-y').value
    };
}

function getToolFiles() {
    if (watermarkType.value !== 'image' || !watermarkImageInput.files.length) {
        return {};
    }

    return {
        watermark_image: watermarkImageInput.files[0]
    };
}
</script>
@endpush
