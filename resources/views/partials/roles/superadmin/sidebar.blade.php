{{-- resources/views/partials/roles/superadmin/sidebar.blade.php --}}
@php
    $safeRoute = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name)
        ? route($name, $params) : '#';

    $isGroupActive = function (array $routes) {
        foreach ($routes as $r) {
            if (\Illuminate\Support\Facades\Route::has($r)
                && (request()->routeIs($r) || request()->routeIs($r . '.*'))) {
                return true;
            }
        }
        return false;
    };

    /**
     * Menu Superadmin — berdasarkan routes yang tersedia:
     *   superadmin.dashboard, superadmin.ringkasan,
     *   superadmin.announcements.*, superadmin.reports.*,
     *   superadmin.contacts.*, superadmin.notifications
     */
    $menuItems = [
        ['type' => 'link', 'route' => 'superadmin.dashboard', 'icon' => 'fa-gauge-high', 'label' => 'Dashboard'],
        ['type' => 'link', 'route' => 'superadmin.ringkasan', 'icon' => 'fa-chart-pie',  'label' => 'Ringkasan'],

        ['type' => 'group', 'label' => 'Pengumuman', 'icon' => 'fa-bullhorn',
         'route' => 'superadmin.announcements.all',
         'children' => [
             ['route' => 'superadmin.announcements.all',       'label' => 'Semua Pengumuman'],
             ['route' => 'superadmin.announcements.create',    'label' => 'Buat Pengumuman'],
             ['route' => 'superadmin.announcements.officials', 'label' => 'Untuk Pengurus'],
             ['route' => 'superadmin.announcements.members',   'label' => 'Untuk Anggota'],
             ['route' => 'superadmin.announcements.history',   'label' => 'Riwayat'],
         ]],

        ['type' => 'group', 'label' => 'Laporan', 'icon' => 'fa-chart-line',
         'route' => 'superadmin.reports.analysis',
         'children' => [
             ['route' => 'superadmin.reports.analysis',    'label' => 'Analisis Umum'],
             ['route' => 'superadmin.reports.rooms',       'label' => 'Laporan Ruangan'],
             ['route' => 'superadmin.reports.bookings',    'label' => 'Laporan Booking'],
             ['route' => 'superadmin.reports.schedules',   'label' => 'Laporan Jadwal'],
             ['route' => 'superadmin.reports.permissions', 'label' => 'Laporan Izin'],
             ['route' => 'superadmin.reports.export',      'label' => 'Export Data'],
         ]],

        ['type' => 'group', 'label' => 'Kontak', 'icon' => 'fa-address-book',
         'route' => 'superadmin.contacts.all',
         'children' => [
             ['route' => 'superadmin.contacts.all',       'label' => 'Semua Kontak'],
             ['route' => 'superadmin.contacts.officials', 'label' => 'Pengurus'],
             ['route' => 'superadmin.contacts.members',   'label' => 'Anggota'],
         ]],

        ['type' => 'link', 'route' => 'superadmin.notifications', 'icon' => 'fa-bell', 'label' => 'Notifikasi'],
    ];
@endphp

<aside
    x-data
    role="navigation"
    aria-label="Sidebar navigasi superadmin"
    class="fixed inset-y-0 left-0 z-40 flex flex-col
           w-80 max-w-[85vw]
           bg-white dark:bg-gray-900
           border-r border-gray-200 dark:border-gray-800
           shadow-xl shadow-black/5
           transition-transform duration-300 ease-in-out
           will-change-transform"
    :class="{
        'translate-x-0':          $store.sidebar.mobileOpen,
        '-translate-x-full':     !$store.sidebar.mobileOpen,
        'md:translate-x-0':       $store.sidebar.desktopOpen,
        'md:-translate-x-full':  !$store.sidebar.desktopOpen
    }">

    {{-- Header: Logo + X --}}
    <div class="h-16 flex items-center justify-between px-4
                border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('superadmin.dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600
                         text-white font-bold flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-user-shield text-sm"></i>
            </span>
            <span class="flex flex-col leading-tight">
                <span class="font-bold text-sm text-gray-800 dark:text-white whitespace-nowrap">
                    UNICODE
                </span>
                <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold uppercase tracking-wider">
                    Superadmin
                </span>
            </span>
        </a>

        <button type="button"
                @click="$store.sidebar.close()"
                title="Tutup sidebar (Ctrl+B)"
                aria-label="Tutup sidebar"
                class="w-9 h-9 rounded-xl flex items-center justify-center
                       text-gray-500 dark:text-gray-400
                       hover:bg-red-50 dark:hover:bg-red-900/20
                       hover:text-red-500 dark:hover:text-red-400
                       active:scale-90 transition-all duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto p-3 space-y-1
                scrollbar-thin">

        @foreach($menuItems as $item)

            {{-- LINK --}}
            @if($item['type'] === 'link')
                @php
                    $isActive = \Illuminate\Support\Facades\Route::has($item['route'])
                        && (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'));
                @endphp
                <a href="{{ $safeRoute($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          transition-all duration-200
                          {{ $isActive
                                ? 'bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-lg shadow-purple-500/20'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-purple-600 dark:hover:text-purple-400' }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                </a>

            {{-- GROUP --}}
            @elseif($item['type'] === 'group')
                @php
                    $childRoutes = collect($item['children'])->pluck('route')->all();
                    $groupActive = $isGroupActive($childRoutes);
                @endphp
                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                    <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                                   transition-all duration-200 cursor-pointer
                                   {{ $groupActive
                                        ? 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-purple-600 dark:hover:text-purple-400' }}">
                        <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                        <span class="whitespace-nowrap flex-1 text-left">{{ $item['label'] }}</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-transition.duration.200ms
                         class="mt-1 ml-4 pl-3 border-l border-gray-200 dark:border-gray-700 space-y-0.5">
                        @foreach($item['children'] as $child)
                            @php
                                $childActive = \Illuminate\Support\Facades\Route::has($child['route'])
                                    && (request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*'));
                            @endphp
                            <a href="{{ $safeRoute($child['route']) }}"
                               class="block px-3 py-2 rounded-lg text-[13px] transition
                                      {{ $childActive
                                            ? 'text-purple-600 dark:text-purple-400 font-semibold bg-purple-50 dark:bg-purple-900/20'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="p-3 border-t border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('logout') }}"
           onclick="event.preventDefault(); window.showLogoutModal(event);"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                  text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
            <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap">Logout</span>
        </a>
    </div>
</aside>

{{-- Overlay mobile --}}
<div x-show="$store.sidebar.mobileOpen"
     x-transition.opacity.duration.250ms
     @click="$store.sidebar.close()"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 md:hidden"
     style="display: none;"></div>