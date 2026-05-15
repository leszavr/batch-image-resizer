@extends('layouts.app')
@section('title', dbt('admin.users.create_title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-bold mb-2">{{ dbt('admin.users.create_title') }}</h1>
    <p class="text-sm text-gray-400 mb-6">{{ dbt('admin.users.create_subtitle') }}</p>

    @include('admin._nav')

    <div class="card-panel">
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/30 text-red-200">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('auth.name') }} *</label>
                    <input name="name" value="{{ old('name') }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('auth.password') }} *</label>
                    <input type="password" name="password" minlength="8" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2" required>
                    <p class="text-xs text-gray-500 mt-1">{{ dbt('admin.users.password_min') }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('admin.users.plan') }}</label>
                    <select name="plan_id" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2 text-white">
                        <option value="">{{ dbt('admin.users.no_plan') }}</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ $plan->localizedName() }} ({{ $plan->slug }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">{{ dbt('dashboard.credits') }}</label>
                    <input type="number" min="0" name="credits_balance" value="{{ old('credits_balance', 0) }}" class="w-full rounded-lg bg-gray-900 border border-gray-800 px-3 py-2">
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-400 mb-3">{{ dbt('admin.users.roles') }}</p>
                <div class="flex flex-wrap gap-5 text-sm text-gray-300">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(old('roles') && in_array($role->name, old('roles')))>
                            {{ \App\Models\User::localizedRoleName($role->name) }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 text-sm text-gray-300">
                    <input type="checkbox" name="unlimited_access" value="1" @checked(old('unlimited_access'))>
                    {{ dbt('admin.users.fields.unlimited_access') }}
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 rounded-lg border border-violet-500 text-white bg-violet-500/10 hover:bg-violet-500/20 transition">
                    {{ dbt('admin.users.actions.create') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white transition">
                    {{ dbt('common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection