<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Image Processing Platform')</title>
    <meta name="description" content="@yield('description', dbt('meta.description'))">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @php
        $ippI18n = [
            'upload' => [
                'add_file' => dbt('js.upload.add_file'),
                'form_not_found' => dbt('js.upload.form_not_found'),
                'redirect_missing' => dbt('js.upload.redirect_missing'),
                'submit_error' => dbt('js.upload.submit_error'),
            ],
            'job' => [
                'status' => [
                    'pending' => dbt('js.job.status.pending'),
                    'processing' => dbt('js.job.status.processing'),
                    'done' => dbt('js.job.status.done'),
                    'failed' => dbt('js.job.status.failed'),
                    'expired' => dbt('js.job.status.expired'),
                ],
            ],
        ];
    @endphp
    <script>
        window.__IPP_I18N__ = @json($ippI18n);
        // Initialize Feather Icons
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
        // Also replace when content is dynamically added
        const observer = new MutationObserver(() => feather.replace());
        observer.observe(document.body, { childList: true, subtree: true });
    </script>
</head>
<body class="h-full bg-gray-950 text-gray-100 antialiased" x-data>

{{-- Navigation --}}
<nav class="border-b border-gray-800 bg-gray-950/80 backdrop-blur sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-white">
            <svg class="w-8 h-8 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                <path d="M17.5 14v6M14.5 17h6" stroke-linecap="round"/>
            </svg>
            <span>{{ dbt('brand') }}</span>
        </a>

        {{-- Desktop nav --}}
        <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-400">
            <a href="{{ route('tools.index') }}"    class="hover:text-white transition">{{ dbt('nav.tools') }}</a>
            <a href="{{ route('plans.index') }}" class="hover:text-white transition">{{ dbt('nav.plans') }}</a>

            <div class="flex items-center gap-2 text-xs">
                <span class="text-gray-500">{{ dbt('lang.label') }}:</span>
                @php
                    $layoutLocales = class_exists(\App\Services\LocaleService::class)
                        ? app(\App\Services\LocaleService::class)->active()
                        : collect();
                @endphp
                <select class="rounded-md bg-gray-900 border border-gray-800 text-gray-200 px-2 py-1 text-xs" onchange="if(this.value) window.location.href='{{ url('/locale') }}/' + this.value;">
                    @foreach($layoutLocales as $switchLocale)
                        <option value="{{ $switchLocale->code }}" @selected(app()->getLocale() === $switchLocale->code)>{{ strtoupper($switchLocale->code) }}</option>
                    @endforeach
                </select>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-white transition">{{ dbt('nav.dashboard') }}</a>
                @if(auth()->user()->can('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition">{{ dbt('admin.common.title') }}</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="hover:text-white transition">{{ dbt('nav.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}"    class="hover:text-white transition">{{ dbt('nav.login') }}</a>
                <a href="{{ route('register') }}" class="btn-primary text-white px-4 py-2 rounded-lg">{{ dbt('nav.register') }}</a>
            @endauth
        </div>

        {{-- Mobile menu button --}}
        <button class="md:hidden w-11 h-11 flex items-center justify-center text-gray-400 hover:text-white" @click="$dispatch('toggle-menu')" aria-label="{{ dbt('nav.open_menu') }}">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-emerald-900/50 border border-emerald-700 text-emerald-300 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    </div>
@endif
@if(session('error') || $errors->any())
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-900/50 border border-red-700 text-red-300 rounded-lg px-4 py-3 text-sm">
            @if(session('error')){{ session('error') }}@endif
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    </div>
@endif

{{-- Page content --}}
@yield('content')

{{-- Footer --}}
<footer class="border-t border-gray-800 mt-20 py-10 text-center text-sm text-gray-500">
    <div class="max-w-7xl mx-auto px-4">
        <p>© {{ date('Y') }} {{ dbt('brand') }} — {{ dbt('footer.copyright') }}</p>
    </div>
</footer>

@stack('scripts')

</body>
</html>