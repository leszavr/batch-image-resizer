@extends('layouts.app')
@section('title', dbt('history.title'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-6">{{ dbt('history.title') }}</h1>

    <div class="card-panel overflow-hidden p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/60 text-gray-300">
                <tr>
                    <th class="text-left p-3">ID</th>
                    <th class="text-left p-3">{{ dbt('history.status') }}</th>
                    <th class="text-left p-3">{{ dbt('history.files') }}</th>
                    <th class="text-left p-3">{{ dbt('history.date') }}</th>
                    <th class="text-right p-3">{{ dbt('history.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($jobs as $job)
                    <tr>
                        <td class="p-3">{{ $job->uuid }}</td>
                        <td class="p-3">{{ $job->localizedStatus() }}</td>
                        <td class="p-3">{{ $job->total_files }}</td>
                        <td class="p-3">{{ $job->created_at->format('d.m.Y H:i') }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('jobs.show', $job->uuid) }}" class="text-violet-400 hover:underline">{{ dbt('history.open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">{{ dbt('history.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $jobs->links() }}</div>
</div>
@endsection
