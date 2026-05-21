@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.vibrance.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.vibrance.value') }}</label>
            <input type="range" id="vibrance-value-display" min="-100" max="100" value="0" class="w-full mb-2" oninput="document.getElementById('vibrance-value').value = this.value">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">-100</span>
                <input type="number" id="vibrance-value" class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1 text-white text-center focus:border-violet-500 focus:outline-none" value="0">
                <span class="text-xs text-gray-500">+100</span>
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button onclick="setVibrance(-50)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.desaturate') }}</button>
            <button onclick="setVibrance(0)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.normal') }}</button>
            <button onclick="setVibrance(50)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.vivid') }}</button>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'vibrance';
$toolName = dbt('tools.vibrance.name');
$toolDescription = dbt('tools.vibrance.description');
@endphp

@push('tool-scripts')
<script>
function getToolOptions() {
    return {
        level: document.getElementById('vibrance-value').value
    };
}

function setVibrance(value) {
    document.getElementById('vibrance-value').value = value;
    document.getElementById('vibrance-value-display').value = value;
}
</script>
@endpush
