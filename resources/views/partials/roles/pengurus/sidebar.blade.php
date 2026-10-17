{{-- resources/views/partials/roles/pengurus/sidebar.blade.php --}}
@php
    $safeRoute = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $isGroupActive = function (array $routes) {
        foreach ($routes as $r) {
            if (\Illuminate\Support\Facades\Route::has($r) && (request()->routeIs($r) || request()->routeIs($r.'.*'))) return true;
        }
        return false;
    };
    $menuItems = [
        ['type'=>'link','route'=>'pengurus.dashboard','icon'=>'fa-gauge-high','label'=>'Dashboard'],
        ['type'=>'group','label'=>'Misi','icon'=>'fa-flag','route'=>'pengurus.misi','children'=>[
            ['route'=>'pengurus.misi','label'=>'Misi Saya'],
            ['route'=>'pengurus.misi.divisi','label'=>'Misi Divisi'],
            ['route'=>'pengurus.misi.selesai','label'=>'Misi Selesai'],
        ]],
        ['type'=>'group','label'=>'Tugas','icon'=>'fa-list-check','route'=>'pengurus.tugas','children'=>[
            ['route'=>'pengurus.tugas','label'=>'Tugas Saya'],
            ['route'=>'pengurus.tugas.divisi','label'=>'Tugas Divisi'],
        ]],
        ['type'=>'group','label'=>'Event','icon'=>'fa-calendar-check','route'=>'pengurus.event','children'=>[
            ['route'=>'pengurus.event','label'=>'Semua Event'],
            ['route'=>'pengurus.event.saya','label'=>'Event Saya'],
            ['route'=>'pengurus.event.kehadiran','label'=>'Kehadiran'],
        ]],
        ['type'=>'link','route'=>'pengurus.pengumuman','icon'=>'fa-bullhorn','label'=>'Pengumuman'],
        ['type'=>'link','route'=>'pengurus.kontak','icon'=>'fa-address-book','label'=>'Kontak'],
        ['type'=>'link','route'=>'pengurus.anggota','icon'=>'fa-users','label'=>'Anggota'],
        ['type'=>'link','route'=>'pengurus.kelompok','icon'=>'fa-layer-group','label'=>'Kelompok'],
        ['type'=>'link','route'=>'pengurus.presensi','icon'=>'fa-clipboard-check','label'=>'Presensi'],
        ['type'=>'link','route'=>'pengurus.data-diri','icon'=>'fa-id-card','label'=>'Data Diri'],
        ['type'=>'link','route'=>'pengurus.peringatan','icon'=>'fa-triangle-exclamation','label'=>'Peringatan'],
        ['type'=>'link','route'=>'pengurus.pengaturan','icon'=>'fa-cog','label'=>'Pengaturan'],
    ];
@endphp

<aside x-data role="navigation" aria-label="Sidebar pengurus"
    class="fixed inset-y-0 left-0 z-40 flex flex-col w-80 max-w-[85vw]
           bg-white dark:bg-gray-900
           border-r border-gray-200 dark:border-gray-800 shadow-xl shadow-black/5
           transition-transform duration-300 ease-in-out will-change-transform"
    :class="{
        'translate-x-0':        $store.sidebar.mobileOpen,
        '-translate-x-full':   !$store.sidebar.mobileOpen,
        'md:translate-x-0':     $store.sidebar.desktopOpen,
        'md:-translate-x-full':!$store.sidebar.desktopOpen
    }">

    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('pengurus.dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600
                         text-white font-bold flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-user-tie text-sm"></i>
            </span>
            <span class="flex flex-col leading-tight">
                <span class="font-bold text-sm text-gray-800 dark:text-white whitespace-nowrap">UNICODE</span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wider">Pengurus</span>
            </span>
        </a>
        <button type="button" @click="$store.sidebar.close()" title="Tutup sidebar (Ctrl+B)"
                class="w-9 h-9 rounded-xl flex items-center justify-center
                       text-gray-500 dark:text-gray-400
                       hover:bg-red-50 dark:hover:bg-red-900/20
                       hover:text-red-500 dark:hover:text-red-400
                       active:scale-90 transition-all duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-3 space-y-1 scrollbar-thin">
        @foreach($menuItems as $item)
            @if($item['type'] === 'link')
                @php $isActive = \Illuminate\Support\Facades\Route::has($item['route']) && (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')); @endphp
                <a href="{{ $safeRoute($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ $isActive
                                ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/20'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                </a>
            @elseif($item['type'] === 'group')
                @php $groupActive = $isGroupActive(collect($item['children'])->pluck('route')->all()); @endphp
                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                   {{ $groupActive
                                        ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                        <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                        <span class="whitespace-nowrap flex-1 text-left">{{ $item['label'] }}</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-transition.duration.200ms class="mt-1 ml-4 pl-3 border-l border-gray-200 dark:border-gray-700 space-y-0.5">
                        @foreach($item['children'] as $child)
                            @php $childActive = \Illuminate\Support\Facades\Route::has($child['route']) && (request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*')); @endphp
                            <a href="{{ $safeRoute($child['route']) }}"
                               class="block px-3 py-2 rounded-lg text-[13px] transition
                                      {{ $childActive
                                            ? 'text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-900/20'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    <div class="p-3 border-t border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('logout') }}" onclick="event.preventDefault(); window.showLogoutModal(event);"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                  text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
            <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap">Logout</span>
        </a>
    </div>
</aside>

<div x-show="$store.sidebar.mobileOpen" x-transition.opacity.duration.250ms
     @click="$store.sidebar.close()"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 md:hidden" style="display: none;"></div>