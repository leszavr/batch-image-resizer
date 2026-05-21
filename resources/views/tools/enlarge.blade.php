@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.enlarge.settings') }}</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.enlarge.scale') }} <span id="enlarge-scale-value" class="text-gray-300">150%</span></label>
            <input type="range" id="enlarge-scale" min="100" max="400" value="150" class="w-full">
        </div>

        <div class="grid grid-cols-3 gap-2">
            <button type="button" onclick="setScale(125)" class="px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">125%</button>
            <button type="button" onclick="setScale(150)" class="px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">150%</button>
            <button type="button" onclick="setScale(200)" class="px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">200%</button>
        </div>

        <div class="bg-gray-800 rounded-lg p-3 text-sm text-gray-300">
            <div>{{ dbt('tools.enlarge.original') }}: <span id="enlarge-original-size">-</span></div>
            <div>{{ dbt('tools.enlarge.result') }}: <span id="enlarge-result-size">-</span></div>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'enlarge';
$toolName = dbt('tools.enlarge.name');
$toolDescription = dbt('tools.enlarge.description');
@endphp

@push('tool-scripts')
<script>
let originalWidth = 0;
let originalHeight = 0;

const enlargeScale = document.getElementById('enlarge-scale');
const enlargeScaleValue = document.getElementById('enlarge-scale-value');
const originalSizeLabel = document.getElementById('enlarge-original-size');
const resultSizeLabel = document.getElementById('enlarge-result-size');

function onImageLoaded(width, height) {
    originalWidth = width;
    originalHeight = height;
    updateSizeLabels();
}

enlargeScale.addEventListener('input', () => {
    enlargeScaleValue.textContent = `${enlargeScale.value}%`;
    updateSizeLabels();
});

function updateSizeLabels() {
    if (!originalWidth || !originalHeight) {
        originalSizeLabel.textContent = '-';
        resultSizeLabel.textContent = '-';
        return;
    }

    const scale = Number(enlargeScale.value) / 100;
    originalSizeLabel.textContent = `${originalWidth} x ${originalHeight}px`;
    resultSizeLabel.textContent = `${Math.round(originalWidth * scale)} x ${Math.round(originalHeight * scale)}px`;
}

function setScale(value) {
    enlargeScale.value = value;
    enlargeScaleValue.textContent = `${value}%`;
    updateSizeLabels();
    processCurrentFile({ immediate: true });
}

function getToolOptions() {
    return {
        scale: enlargeScale.value
    };
}
</script>
@endpush
