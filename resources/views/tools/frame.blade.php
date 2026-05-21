@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.frame.settings') }}</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.frame.style') }}</label>
            <select id="frame-style" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
                <option value="solid">{{ dbt('tools.frame.solid') }}</option>
                <option value="double">{{ dbt('tools.frame.double') }}</option>
                <option value="dashed">{{ dbt('tools.frame.dashed') }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.frame.color') }}</label>
            <input type="color" id="frame-color" class="w-full h-10 bg-gray-800 border border-gray-700 rounded-lg" value="#ffffff">
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.frame.thickness') }} <span id="frame-thickness-value" class="text-gray-300">20</span>px</label>
            <input type="range" id="frame-thickness" min="1" max="120" value="20" class="w-full">
        </div>

        <div class="flex gap-2 pt-2">
            <button type="button" onclick="setFrameThickness(10)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.light') }}</button>
            <button type="button" onclick="setFrameThickness(20)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.medium') }}</button>
            <button type="button" onclick="setFrameThickness(40)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.heavy') }}</button>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'frame';
$toolName = dbt('tools.frame.name');
$toolDescription = dbt('tools.frame.description');
@endphp

@push('tool-scripts')
<script>
const frameThickness = document.getElementById('frame-thickness');
const frameThicknessValue = document.getElementById('frame-thickness-value');

frameThickness.addEventListener('input', () => {
    frameThicknessValue.textContent = frameThickness.value;
});

function setFrameThickness(value) {
    frameThickness.value = value;
    frameThicknessValue.textContent = value;
    processCurrentFile({ immediate: true });
}

function getToolOptions() {
    return {
        style: document.getElementById('frame-style').value,
        color: document.getElementById('frame-color').value,
        thickness: frameThickness.value
    };
}
</script>
@endpush
