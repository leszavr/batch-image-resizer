@extends('layouts.app')
@section('title', dbt('admin.jobs.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-2">{{ dbt('admin.jobs.title') }}</h1>
    <p class="text-sm text-gray-400 mb-6">{{ dbt('admin.jobs.subtitle') }}</p>

    @include('admin._nav')

    <div class="card-panel mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.jobs.filters.status') }}</label>
                <select name="status" class="w-full rounded-lg bg-gray-900 border border-gray-800 text-white px-3 py-2">
                    <option value="">{{ dbt('common.all') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ \App\Models\ImageJob::localizedStatusFor($status) }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-300 min-h-[42px]">
                <input type="checkbox" name="expired_only" value="1" @checked(request()->boolean('expired_only'))>
                {{ dbt('admin.jobs.filters.expired_only') }}
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-300 min-h-[42px]">
                <input type="checkbox" name="stale_only" value="1" @checked(request()->boolean('stale_only'))>
                {{ dbt('admin.jobs.filters.stale_only') }}
            </label>
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.jobs.filters.sort') }}</label>
                <select name="sort" class="w-full rounded-lg bg-gray-900 border border-gray-800 text-white px-3 py-2">
                    <option value="created_at" @selected($sort === 'created_at')>{{ dbt('admin.jobs.filters.sort_created') }}</option>
                    <option value="expires_at" @selected($sort === 'expires_at')>{{ dbt('admin.jobs.filters.sort_expires') }}</option>
                    <option value="status" @selected($sort === 'status')>{{ dbt('admin.jobs.filters.sort_status') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.jobs.filters.direction') }}</label>
                <select name="direction" class="w-full rounded-lg bg-gray-900 border border-gray-800 text-white px-3 py-2">
                    <option value="desc" @selected($direction === 'desc')>{{ dbt('admin.jobs.filters.direction_desc') }}</option>
                    <option value="asc" @selected($direction === 'asc')>{{ dbt('admin.jobs.filters.direction_asc') }}</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 xl:justify-end min-w-0">
                <button class="w-full sm:w-auto px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition whitespace-nowrap">{{ dbt('common.filter') }}</button>
                <a href="{{ route('admin.jobs.index') }}" class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white transition whitespace-nowrap">{{ dbt('common.reset') }}</a>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
        <form method="POST" action="{{ route('admin.jobs.cleanup-expired') }}">
            @csrf
            <button class="px-4 py-2 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-200 hover:bg-amber-500/20 transition">
                {{ dbt('admin.jobs.actions.cleanup') }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.jobs.fail-stale') }}">
            @csrf
            <button class="px-4 py-2 rounded-lg bg-red-500/10 border border-red-500/30 text-red-200 hover:bg-red-500/20 transition">
                {{ dbt('admin.jobs.actions.stop_stale') }}
            </button>
        </form>
        <p class="text-xs text-gray-500 self-center">{{ dbt('admin.jobs.stale_hint', ['time' => $staleBefore->diffForHumans()]) }}</p>
    </div>

    <div class="card-panel overflow-x-auto" x-data="adminJobsTable()">
        <form id="bulk-delete-form" method="POST" action="{{ route('admin.jobs.bulk-destroy') }}" @submit="if (selected.length === 0) { $event.preventDefault(); return; } if (!confirm('{{ dbt('admin.jobs.confirm.bulk_delete') }}')) { $event.preventDefault(); }">
            @csrf
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div class="flex items-center gap-3 text-sm">
                    <label class="flex items-center gap-2 text-gray-300">
                        <input type="checkbox" @change="toggleAll($event)" :checked="allChecked">
                        {{ dbt('admin.jobs.bulk.select_all') }}
                    </label>
                    <span class="text-gray-500">{{ dbt('admin.jobs.bulk.selected') }}: <span x-text="selected.length"></span></span>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500/10 border border-red-500/30 text-red-200 hover:bg-red-500/20 transition disabled:opacity-50" :disabled="selected.length === 0">
                    {{ dbt('admin.jobs.bulk.delete') }}
                </button>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-800">
                        <th class="py-3 pr-4 w-10"></th>
                        <th class="py-3 pr-4">UUID</th>
                        <th class="py-3 pr-4">{{ dbt('admin.jobs.table.user') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.jobs.table.status') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.jobs.table.archive') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.jobs.table.expires') }}</th>
                        <th class="py-3 pr-4">{{ dbt('admin.jobs.table.created') }}</th>
                        <th class="py-3 pr-4 text-right">{{ dbt('admin.jobs.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr class="border-b border-gray-900 align-top">
                            <td class="py-3 pr-4">
                                <input type="checkbox" name="job_ids[]" value="{{ $job->id }}" x-model="selected">
                            </td>
                            <td class="py-3 pr-4 font-mono text-xs">{{ $job->uuid }}</td>
                            <td class="py-3 pr-4">{{ $job->user?->email ?? dbt('common.guest') }}</td>
                            <td class="py-3 pr-4">{{ $job->localizedStatus() }}</td>
                            <td class="py-3 pr-4">{{ $job->result_archive_path ?: '—' }}</td>
                            <td class="py-3 pr-4">{{ $job->expires_at?->diffForHumans() ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $job->created_at?->diffForHumans() }}</td>
                            <td class="py-3 pr-4 text-right">
                                <button type="submit"
                                        form="delete-job-{{ $job->id }}"
                                        class="px-3 py-1.5 rounded-lg border border-red-500/30 text-red-200 hover:bg-red-500/10 transition"
                                        onclick="return confirm('{{ dbt('admin.jobs.confirm.delete_single') }}');">
                                    {{ dbt('common.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-gray-500">{{ dbt('admin.jobs.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>

        @foreach($jobs as $job)
            <form id="delete-job-{{ $job->id }}" method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
