@extends('layouts.app')
@section('title', dbt('auth.login_title'))
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <h1 class="text-3xl font-bold text-center mb-8">{{ dbt('auth.login_title') }}</h1>
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="label-field">{{ dbt('auth.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input-field w-full" placeholder="you@example.com" autocomplete="email">
                </div>
                <div>
                    <label class="label-field">{{ dbt('auth.password') }}</label>
                    <input type="password" name="password" required
                           class="input-field w-full" autocomplete="current-password">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-600">
                    {{ dbt('auth.remember') }}
                </label>
                <button type="submit" class="btn-primary w-full py-3 rounded-xl font-bold">{{ dbt('auth.login_btn') }}</button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-500">
                {{ dbt('nav.register') }}? <a href="{{ route('register') }}" class="text-violet-400 hover:underline">{{ dbt('auth.register_btn') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
