@php
    $tabs = [
        ['route' => 'owner.settings.general',           'label' => 'Dashboard Builder',  'icon' => 'fa-sliders-h',     'active' => request()->routeIs('owner.settings.general')],
        ['route' => 'owner.settings.dashboard-history', 'label' => 'Riwayat Perubahan',  'icon' => 'fa-history',       'active' => request()->routeIs('owner.settings.dashboard-history')],
        ['route' => 'owner.settings.profile',           'label' => 'Profil Owner',       'icon' => 'fa-user-cog',      'active' => request()->routeIs('owner.settings.profile')],
        ['route' => 'owner.settings.audit',             'label' => 'Audit Sistem',       'icon' => 'fa-clipboard-list','active' => request()->routeIs('owner.settings.audit')],
    ];
@endphp

<nav class="flex flex-wrap items-center gap-1 p-1.5
            bg-gray-100 dark:bg-gray-800/80
            rounded-2xl border border-gray-200 dark:border-gray-700/60">
    @foreach ($tabs as $tab)
        @if (\Illuminate\Support\Facades\Route::has($tab['route']))
            <a href="{{ route($tab['route']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all
                      {{ $tab['active']
                            ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-300 shadow-sm ring-1 ring-cyan-500/20'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/60 dark:hover:bg-gray-700/50' }}">
                <i class="fas {{ $tab['icon'] }} text-xs"></i>
                {{ $tab['label'] }}
            </a>
        @endif
    @endforeach
</nav>