@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.annotate.settings') }}</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.annotate.type') }}</label>
            <select id="annotate-type" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
                <option value="line">{{ dbt('tools.annotate.line') }}</option>
                <option value="rectangle">{{ dbt('tools.annotate.rectangle') }}</option>
                <option value="arrow">{{ dbt('tools.annotate.arrow') }}</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.annotate.color') }}</label>
                <input type="color" id="annotate-color" class="w-full h-10 bg-gray-800 border border-gray-700 rounded-lg" value="#ff3b30">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.annotate.thickness') }}</label>
                <input type="number" id="annotate-thickness" min="1" max="30" value="4" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.annotate.opacity') }} <span id="annotate-opacity-value" class="text-gray-300">100%</span></label>
            <input type="range" id="annotate-opacity" min="10" max="100" value="100" class="w-full">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-400 mb-1">X1 (%)</label>
                <input type="number" id="annotate-x1" min="0" max="100" value="15" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Y1 (%)</label>
                <input type="number" id="annotate-y1" min="0" max="100" value="15" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">X2 (%)</label>
                <input type="number" id="annotate-x2" min="0" max="100" value="85" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Y2 (%)</label>
                <input type="number" id="annotate-y2" min="0" max="100" value="85" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-violet-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2">
            <button type="button" onclick="setAnnotatePreset('diag1')" class="px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.annotate.diagonal_1') }}</button>
            <button type="button" onclick="setAnnotatePreset('diag2')" class="px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.annotate.diagonal_2') }}</button>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'annotate';
$toolName = dbt('tools.annotate.name');
$toolDescription = dbt('tools.annotate.description');
@endphp

@push('tool-scripts')
<script>
const annotateOpacity = document.getElementById('annotate-opacity');
const annotateOpacityValue = document.getElementById('annotate-opacity-value');

annotateOpacity.addEventListener('input', () => {
    annotateOpacityValue.textContent = `${annotateOpacity.value}%`;
});

function setAnnotatePreset(type) {
    if (type === 'diag1') {
        document.getElementById('annotate-x1').value = 10;
        document.getElementById('annotate-y1').value = 10;
        document.getElementById('annotate-x2').value = 90;
        document.getElementById('annotate-y2').value = 90;
    }

    if (type === 'diag2') {
        document.getElementById('annotate-x1').value = 90;
        document.getElementById('annotate-y1').value = 10;
        document.getElementById('annotate-x2').value = 10;
        document.getElementById('annotate-y2').value = 90;
    }

    processCurrentFile({ immediate: true });
}

function getToolOptions() {
    return {
        type: document.getElementById('annotate-type').value,
        color: document.getElementById('annotate-color').value,
        thickness: document.getElementById('annotate-thickness').value,
        opacity: annotateOpacity.value,
        x1: document.getElementById('annotate-x1').value,
        y1: document.getElementById('annotate-y1').value,
        x2: document.getElementById('annotate-x2').value,
        y2: document.getElementById('annotate-y2').value
    };
}
</script>
@endpush
