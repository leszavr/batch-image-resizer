@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.rotate.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-1">{{ dbt('tools.rotate.angle') }}</label>
            <input type="range" id="rotate-angle-display" min="-180" max="180" value="90" class="w-full mb-2" oninput="document.getElementById('rotate-angle').value = this.value">
            <input type="number" id="rotate-angle" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-center focus:border-violet-500 focus:outline-none" value="90">
        </div>

        <div class="flex gap-2 pt-2">
            <button onclick="setAngle(-90)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">-90°</button>
            <button onclick="setAngle(90)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">90°</button>
        </div>
        <div class="flex gap-2">
            <button onclick="setAngle(-180)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">-180°</button>
            <button onclick="setAngle(180)" class="flex-1 px-3 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition text-sm">180°</button>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'rotate';
$toolName = dbt('tools.rotate.name');
$toolDescription = dbt('tools.rotate.description');
@endphp

@push('tool-scripts')
<script>
function getToolOptions() {
    return {
        angle: document.getElementById('rotate-angle').value
    };
}

function setAngle(angle) {
    document.getElementById('rotate-angle').value = angle;
    document.getElementById('rotate-angle-display').value = angle;
}
</script>
@endpush