@php
    $safeRoute = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $menuItems = [
        ['route'=>'anggota.dashboard',  'icon'=>'fa-home',            'label'=>'Dashboard'],
        ['route'=>'anggota.event',      'icon'=>'fa-calendar-check',  'label'=>'Event'],
        ['route'=>'anggota.pengumuman', 'icon'=>'fa-bullhorn',        'label'=>'Pengumuman'],
        ['route'=>'anggota.jadwal',     'icon'=>'fa-calendar-alt',    'label'=>'Jadwal'],
        ['route'=>'anggota.chatting',   'icon'=>'fa-comments',        'label'=>'Chatting'],
        ['route'=>'anggota.pengaturan', 'icon'=>'fa-cog',             'label'=>'Pengaturan'],
    ];
@endphp
<a href="{{ route('anggota-umum.uangkas') }}"
   class="... {{ request()->routeIs('anggota-umum.uangkas') ? 'active' : '' }}">
    <i class="fas fa-coins"></i>
    <span>Uangkas</span>
</a>
<aside x-data role="navigation" aria-label="Sidebar anggota umum"
    class="fixed inset-y-0 left-0 z-40 flex flex-col w-80 max-w-[85vw]
           bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800
           shadow-xl shadow-black/5 transition-transform duration-300 ease-in-out will-change-transform"
    :class="{
        'translate-x-0':        $store.sidebar.mobileOpen,
        '-translate-x-full':   !$store.sidebar.mobileOpen,
        'md:translate-x-0':     $store.sidebar.desktopOpen,
        'md:-translate-x-full':!$store.sidebar.desktopOpen
    }">

    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('anggota.dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700
                         text-white font-bold flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-user text-sm"></i>
            </span>
            <span class="flex flex-col leading-tight">
                <span class="font-bold text-sm text-gray-800 dark:text-white whitespace-nowrap">UNICODE</span>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Anggota Umum</span>
            </span>
        </a>
        <button type="button" @click="$store.sidebar.close()"
                title="Tutup sidebar (Ctrl+B)"
                class="w-9 h-9 rounded-xl flex items-center justify-center
                       text-gray-500 dark:text-gray-400
                       hover:bg-red-50 dark:hover:bg-red-900/20
                       hover:text-red-500 active:scale-90 transition-all">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-3 space-y-1 scrollbar-thin">
        @foreach($menuItems as $item)
            @php $isActive = \Illuminate\Support\Facades\Route::has($item['route']) && (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')); @endphp
            <a href="{{ $safeRoute($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      {{ $isActive
                            ? 'bg-gradient-to-r from-slate-500 to-slate-700 text-white shadow-lg shadow-slate-500/20'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-slate-600 dark:hover:text-slate-400' }}">
                <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                <span class="whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-3 border-t border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('logout') }}" onclick="event.preventDefault(); window.showLogoutModal(event);"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                  text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
            <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap">Logout</span>
        </a>
    </div>
</aside>

<div x-show="$store.sidebar.mobileOpen" x-transition.opacity.duration.250ms
     @click="$store.sidebar.close()"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 md:hidden" style="display: none;"></div>