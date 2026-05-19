@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.filters.settings') }}</h3>
    
    <div class="space-y-5">
        {{-- Brightness --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="text-sm text-gray-400">{{ dbt('tools.filters.brightness') }}</label>
                <span id="filter-brightness-val" class="text-xs text-gray-500">0</span>
            </div>
            <input type="range" id="filter-brightness" min="-100" max="100" value="0" class="w-full" oninput="updateValue('filter-brightness', 'filter-brightness-val')">
        </div>

        {{-- Contrast --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="text-sm text-gray-400">{{ dbt('tools.filters.contrast') }}</label>
                <span id="filter-contrast-val" class="text-xs text-gray-500">0</span>
            </div>
            <input type="range" id="filter-contrast" min="-100" max="100" value="0" class="w-full" oninput="updateValue('filter-contrast', 'filter-contrast-val')">
        </div>

        {{-- Blur --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="text-sm text-gray-400">{{ dbt('tools.filters.blur') }}</label>
                <span id="filter-blur-val" class="text-xs text-gray-500">0</span>
            </div>
            <input type="range" id="filter-blur" min="0" max="20" value="0" class="w-full" oninput="updateValue('filter-blur', 'filter-blur-val')">
        </div>

        {{-- Grayscale --}}
        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="filter-grayscale" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-violet-500 focus:ring-violet-500">
                <span class="text-sm text-gray-300">{{ dbt('tools.filters.grayscale') }}</span>
            </label>
        </div>

        {{-- Reset --}}
        <button onclick="resetFilters()" class="w-full px-4 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ __('common.reset') }}</button>
    </div>
</div>
@endsection

@php
$toolId = 'filters';
$toolName = dbt('tools.filters.name');
$toolDescription = dbt('tools.filters.description');
@endphp

@push('tool-scripts')
<script>
function getToolOptions() {
    return {
        brightness: document.getElementById('filter-brightness').value,
        contrast: document.getElementById('filter-contrast').value,
        blur: document.getElementById('filter-blur').value,
        grayscale: document.getElementById('filter-grayscale').checked ? 1 : 0
    };
}

function updateValue(inputId, displayId) {
    document.getElementById(displayId).textContent = document.getElementById(inputId).value;
}

function resetFilters() {
    document.getElementById('filter-brightness').value = 0;
    document.getElementById('filter-brightness-val').textContent = '0';
    document.getElementById('filter-contrast').value = 0;
    document.getElementById('filter-contrast-val').textContent = '0';
    document.getElementById('filter-blur').value = 0;
    document.getElementById('filter-blur-val').textContent = '0';
    document.getElementById('filter-grayscale').checked = false;
}
</script>
@endpush