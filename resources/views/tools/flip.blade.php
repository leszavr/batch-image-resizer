@extends('tools.layout')

@section('tool-controls')
<div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ dbt('tools.flip.settings') }}</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-400 mb-3">{{ dbt('tools.flip.direction') }}</label>
            
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="flip-direction" value="horizontal" checked class="hidden peer">
                    <div class="border-2 border-gray-700 rounded-lg p-4 text-center hover:border-gray-600 peer-checked:border-violet-500 peer-checked:bg-violet-500/10 transition">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 17H4m0 0l4 4m-4-4l4-4" opacity="0.5"/>
                        </svg>
                        <span class="text-sm text-gray-300 peer-checked:text-white">{{ dbt('tools.flip.horizontal') }}</span>
                    </div>
                </label>
                
                <label class="cursor-pointer">
                    <input type="radio" name="flip-direction" value="vertical" class="hidden peer">
                    <div class="border-2 border-gray-700 rounded-lg p-4 text-center hover:border-gray-600 peer-checked:border-violet-500 peer-checked:bg-violet-500/10 transition">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8v12m0 0l-4-4m4 4l4-4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16V4m0 0l4 4M7 4l-4 4" opacity="0.5"/>
                        </svg>
                        <span class="text-sm text-gray-300 peer-checked:text-white">{{ dbt('tools.flip.vertical') }}</span>
                    </div>
                </label>
            </div>
        </div>
    </div>
</div>
@endsection

@php
$toolId = 'flip';
$toolName = dbt('tools.flip.name');
$toolDescription = dbt('tools.flip.description');
@endphp

@push('tool-scripts')
<script>
function getToolOptions() {
    const direction = document.querySelector('input[name="flip-direction"]:checked').value;
    return {
        direction: direction
    };
}
</script>
@endpush