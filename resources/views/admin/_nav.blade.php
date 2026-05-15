<div class="mb-8 flex flex-wrap gap-3 text-sm">
    <a href="{{ route('admin.dashboard') }}"
       class="px-4 py-2 rounded-lg border {{ request()->routeIs('admin.dashboard') ? 'border-violet-500 text-white bg-violet-500/10' : 'border-gray-800 text-gray-300 hover:border-violet-500 hover:text-white' }} transition">
        {{ dbt('admin.nav.overview') }}
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="px-4 py-2 rounded-lg border {{ request()->routeIs('admin.users.*') ? 'border-violet-500 text-white bg-violet-500/10' : 'border-gray-800 text-gray-300 hover:border-violet-500 hover:text-white' }} transition">
        {{ dbt('admin.nav.users') }}
    </a>
    <a href="{{ route('admin.jobs.index') }}"
       class="px-4 py-2 rounded-lg border {{ request()->routeIs('admin.jobs.*') ? 'border-violet-500 text-white bg-violet-500/10' : 'border-gray-800 text-gray-300 hover:border-violet-500 hover:text-white' }} transition">
        {{ dbt('admin.nav.jobs') }}
    </a>
    <a href="{{ route('admin.plans.index') }}"
       class="px-4 py-2 rounded-lg border {{ request()->routeIs('admin.plans.*') ? 'border-violet-500 text-white bg-violet-500/10' : 'border-gray-800 text-gray-300 hover:border-violet-500 hover:text-white' }} transition">
        {{ dbt('admin.nav.plans') }}
    </a>
    <a href="{{ route('admin.statistics.index') }}"
       class="px-4 py-2 rounded-lg border {{ request()->routeIs('admin.statistics.*') ? 'border-violet-500 text-white bg-violet-500/10' : 'border-gray-800 text-gray-300 hover:border-violet-500 hover:text-white' }} transition">
        {{ dbt('admin.nav.statistics') }}
    </a>
    <a href="{{ route('admin.localization.index') }}"
       class="px-4 py-2 rounded-lg border {{ request()->routeIs('admin.localization.*') ? 'border-violet-500 text-white bg-violet-500/10' : 'border-gray-800 text-gray-300 hover:border-violet-500 hover:text-white' }} transition">
        {{ dbt('admin.nav.localization') }}
    </a>
</div>
