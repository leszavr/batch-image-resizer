@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.blur.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.blur.level') }}</label>
            <input type="range" id="blur-value-display" min="0" max="30" value="0" class="w-full mb-2" oninput="document.getElementById('blur-value').value = this.value">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">None</span>
                <input type="number" id="blur-value" class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1 text-white text-center focus:border-violet-500 focus:outline-none" value="0" min="0" max="30">
                <span class="text-xs text-gray-500">30px</span>
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button onclick="setBlur(5)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.light') }}</button>
            <button onclick="setBlur(15)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.medium') }}</button>
            <button onclick="setBlur(25)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">{{ dbt('tools.filters.heavy') }}</button>
        </div>

        <p class="text-xs text-gray-500 pt-2">{{ dbt('tools.blur.description') }}</p>
    </div>
</div>
@endsection

@php
$toolId = 'blur';
$toolName = dbt('tools.blur.name');
$toolDescription = dbt('tools.blur.description');
@endphp

@push('tool-scripts')
<script>
function getToolOptions() {
    return {
        level: document.getElementById('blur-value').value
    };
}

function setBlur(value) {
    document.getElementById('blur-value').value = value;
    document.getElementById('blur-value-display').value = value;
}
</script>
@endpush
