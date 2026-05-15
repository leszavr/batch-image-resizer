@extends('layouts.app')
@section('title', dbt('admin.users.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-3xl font-bold">{{ dbt('admin.users.title') }}</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
            {{ dbt('admin.users.actions.create') }}
        </a>
    </div>
    <p class="text-sm text-gray-400 mb-6">{{ dbt('admin.users.subtitle') }}</p>

    @include('admin._nav')

    @if(session('success'))
        <div class="card-panel mb-6 border-l-4 border-l-green-500">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="card-panel mb-6 border-l-4 border-l-red-500">
            {{ session('error') }}
        </div>
    @endif

    <div class="card-panel mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="{{ dbt('admin.users.search_placeholder') }}"
                class="flex-1 rounded-lg bg-gray-900 border border-gray-800 text-white px-3 py-2"
            >
            <div class="flex gap-3">
                <button class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">{{ dbt('common.search') }}</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white transition">{{ dbt('common.reset') }}</a>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        @forelse($users as $managedUser)
            @php
                $blockStatus = $managedUser->blockStatus();
            @endphp
            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="card-panel space-y-4 {{ $blockStatus['blocked'] ? 'border-l-4 border-l-red-500' : '' }}">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-semibold {{ $blockStatus['blocked'] ? 'text-red-400' : 'text-white' }}">
                            {{ $managedUser->name }}
                            @if($blockStatus['blocked'])
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-200 border border-red-500/30">
                                    {{ $blockStatus['type'] === 'permanent' ? dbt('admin.users.status.blocked_permanent') : dbt('admin.users.status.blocked_until', ['until' => $blockStatus['until']->format('d.m.Y H:i')]) }}
                                </span>
                            @endif
                            @if($managedUser->unlimited_access)
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-200 border border-green-500/30">
                                    {{ dbt('admin.users.unlimited_access') }}
                                </span>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500">ID: {{ $managedUser->id }} · {{ dbt('admin.users.registered') }} {{ $managedUser->created_at?->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.stats', $managedUser) }}" class="px-3 py-2 rounded-lg border border-blue-500/60 text-blue-200 hover:bg-blue-500/10 transition text-sm">
                            {{ dbt('admin.users.actions.stats') }}
                        </a>
                        <button form="delete-user-{{ $managedUser->id }}" type="submit" class="px-3 py-2 rounded-lg border border-red-500/60 text-red-200 hover:bg-red-500/10 transition text-sm"
                            {{ $managedUser->id === auth()->id() ? 'disabled' : '' }}>
                            {{ dbt('common.delete') }}
                        </button>
                        <button type="button" onclick="document.getElementById('reset-password-{{ $managedUser->id }}').classList.toggle('hidden')" class="px-3 py-2 rounded-lg border border-yellow-500/60 text-yellow-200 hover:bg-yellow-500/10 transition text-sm">
                            {{ dbt('admin.users.actions.reset_password') }}
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                            {{ dbt('common.save') }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('auth.name') }}</label>
                        <input name="name" value="{{ $managedUser->name }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" value="{{ $managedUser->email }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.users.plan') }}</label>
                        <select name="plan_id" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white">
                            <option value="">{{ dbt('admin.users.no_plan') }}</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" @selected((int) $managedUser->plan_id === (int) $plan->id)>{{ $plan->localizedName() }} ({{ $plan->slug }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">{{ dbt('dashboard.credits') }}</label>
                        <input type="number" min="0" name="credits_balance" value="{{ $managedUser->credits_balance }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-400 mb-3">{{ dbt('admin.users.roles') }}</p>
                        <div class="flex flex-wrap gap-5 text-sm text-gray-300">
                            @php($userRoles = $managedUser->getRoleNames()->all())
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, $userRoles, true))>
                                    {{ \App\Models\User::localizedRoleName($role->name) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 mb-3">{{ dbt('admin.users.blocking.title') }}</p>
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-sm text-gray-300">
                                <input type="checkbox" name="is_blocked" value="1" @checked($managedUser->is_blocked)>
                                {{ dbt('admin.users.blocking.permanent') }}
                            </label>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ dbt('admin.users.blocking.until') }}</label>
                                <input type="datetime-local" name="blocked_until" value="{{ $managedUser->blocked_until?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ dbt('admin.users.blocking.reason') }}</label>
                                <input type="text" name="block_reason" value="{{ $managedUser->block_reason }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-sm" placeholder="{{ dbt('admin.users.blocking.reason_placeholder') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-5">
                    <label class="flex items-center gap-2 text-sm text-gray-300">
                        <input type="checkbox" name="unlimited_access" value="1" @checked($managedUser->unlimited_access)>
                        {{ dbt('admin.users.fields.unlimited_access') }}
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="rounded-lg border border-gray-800 p-3">
                        <p class="text-gray-500">{{ dbt('admin.users.effective_plan') }}</p>
                        <p class="mt-2 text-white font-medium">{{ $managedUser->effectivePlan()->localizedName() ?? dbt('plans.price.free') }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-800 p-3">
                        <p class="text-gray-500">{{ dbt('dashboard.jobs_today') }}</p>
                        <p class="mt-2 text-white font-medium">{{ $managedUser->todayJobsCount() }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-800 p-3">
                        <p class="text-gray-500">{{ dbt('admin.users.total_jobs') }}</p>
                        <p class="mt-2 text-white font-medium">{{ $managedUser->imageJobs()->count() }}</p>
                    </div>
                </div>

                {{-- Reset Password Form --}}
                <div id="reset-password-{{ $managedUser->id }}" class="hidden border-t border-gray-800 pt-4 mt-4">
                    <form method="POST" action="{{ route('admin.users.reset-password', $managedUser) }}" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.users.new_password') }}</label>
                            <input type="password" name="password" minlength="8" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-lg border border-yellow-500 text-white bg-yellow-500/10 hover:bg-yellow-500/20 transition">
                            {{ dbt('admin.users.actions.reset_password_confirm') }}
                        </button>
                    </form>
                </div>
            </form>

            <form id="delete-user-{{ $managedUser->id }}" method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" class="hidden" onsubmit="return confirm('{{ dbt('admin.users.confirm_delete', ['email' => $managedUser->email]) }}')">
                @csrf
                @method('DELETE')
            </form>
        @empty
            <div class="card-panel text-gray-500">{{ dbt('admin.users.empty') }}</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection