@extends('layouts.app')
@section('title', dbt('presets.title'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-6">{{ dbt('presets.title') }}</h1>

    <div class="card-panel mb-6">
        <form method="POST" action="{{ route('presets.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="label-field">{{ dbt('presets.name') }}</label>
                <input class="input-field w-full" name="name" required>
            </div>
            <div>
                <label class="label-field">Pipeline (JSON)</label>
                <textarea class="input-field w-full min-h-32" name="pipeline" placeholder='[{"step":"resize","params":{"mode":"width","width":1200}}]'></textarea>
            </div>
            <button class="btn-primary px-4 py-2 rounded-lg">{{ dbt('presets.save') }}</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($presets as $preset)
            <div class="card-panel flex items-start justify-between gap-4">
                <div>
                    <p class="font-semibold">{{ $preset->name }}</p>
                    <pre class="text-xs text-gray-400 mt-2 whitespace-pre-wrap">{{ json_encode($preset->pipeline, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                </div>
                @if($preset->user_id === auth()->id())
                    <form method="POST" action="{{ route('presets.destroy', $preset->id) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300">{{ dbt('presets.delete') }}</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-gray-500">{{ dbt('presets.empty') }}</p>
        @endforelse
    </div>
</div>
@endsection
