@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.clarity.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.clarity.value') }}</label>
            <input type="range" id="clarity-value-display" min="-100" max="100" value="0" class="w-full mb-2" oninput="document.getElementById('clarity-value').value = this.value">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Soft</span>
                <input type="number" id="clarity-value" class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1 text-white text-center focus:border-violet-500 focus:outline-none" value="0">
                <span class="text-xs text-gray-500">Sharp</span>
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button onclick="setClarity(-50)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.clarity.soft') }}</button>
            <button onclick="setClarity(0)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.normal') }}</button>
            <button onclick="setClarity(50)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.clarity.sharp') }}</button>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'clarity';
$toolName = dbt('tools.clarity.name');
$toolDescription = dbt('tools.clarity.description');
@endphp

@push('tool-scripts')
<script>
function getToolOptions() {
    return {
        level: document.getElementById('clarity-value').value
    };
}

function setClarity(value) {
    document.getElementById('clarity-value').value = value;
    document.getElementById('clarity-value-display').value = value;
}
</script>
@endpush