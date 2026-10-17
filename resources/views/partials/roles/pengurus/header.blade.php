{{-- resources/views/partials/roles/pengurus/header.blade.php --}}
@php
    $authUser = auth()->user();
    $unreadCount = 0;
    if ($authUser && method_exists($authUser, 'unreadNotifications')) {
        try { $unreadCount = (int) $authUser->unreadNotifications()->count(); } catch (\Throwable $e) {}
    }
    $userName       = $authUser->name ?? 'Pengurus';
    $userInitial    = strtoupper(mb_substr($userName, 0, 1));
    $userIdentifier = $authUser->email ?? '';
    $safeRoute = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
@endphp

<header x-data="{
        notifOpen: false, profileOpen: false,
        isDark: document.documentElement.classList.contains('dark'),
        closeAll() { this.notifOpen = false; this.profileOpen = false; },
        toggleNotif() { this.notifOpen = !this.notifOpen; this.profileOpen = false; },
        toggleProfile() { this.profileOpen = !this.profileOpen; this.notifOpen = false; },
        toggleTheme() {
            this.isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('theme', this.isDark ? 'dark' : 'light'); } catch(e) {}
        }
    }"
    @keydown.escape.window="closeAll()"
    role="banner"
    class="h-16 bg-white/85 dark:bg-gray-900/85 backdrop-blur-md
           border-b border-gray-200 dark:border-gray-800
           flex items-center justify-between gap-3 px-3 md:px-6
           flex-shrink-0 z-20 sticky top-0">

    <div class="flex items-center gap-2 md:gap-3 min-w-0">
        <button type="button" @click="$store.sidebar.toggle()"
                :title="$store.sidebar.isDesktop ? ($store.sidebar.desktopOpen ? 'Tutup sidebar (Ctrl+B)' : 'Buka sidebar (Ctrl+B)') : 'Buka menu'"
                class="group w-10 h-10 rounded-xl flex-shrink-0
                       bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700
                       active:scale-90 flex items-center justify-center
                       text-gray-600 dark:text-gray-300
                       hover:text-emerald-600 dark:hover:text-emerald-400
                       transition-all duration-200">
            <i class="hidden md:inline-flex fas text-sm transition-transform duration-300 ease-out"
               :class="$store.sidebar.desktopOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
            <i class="md:hidden fas fa-bars text-sm"></i>
        </button>

        <div class="flex flex-col min-w-0 leading-tight">
            <h1 class="text-sm md:text-base font-semibold text-gray-800 dark:text-white truncate">
                @yield('breadcrumb', 'Dashboard Pengurus')
            </h1>
            @hasSection('breadcrumb-sub')
                <p class="hidden sm:block text-[11px] text-gray-400 dark:text-gray-500 truncate">@yield('breadcrumb-sub')</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-1 md:gap-1.5">
        <button type="button" @click="toggleTheme()" :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
                class="w-10 h-10 rounded-xl flex-shrink-0
                       bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700
                       active:scale-90 flex items-center justify-center
                       text-gray-600 dark:text-gray-300 transition-all duration-200">
            <i class="fas text-sm" :class="isDark ? 'fa-sun' : 'fa-moon'"></i>
        </button>

        <div class="relative">
            <button type="button" @click="toggleNotif()" :aria-expanded="notifOpen.toString()"
                    class="relative w-10 h-10 rounded-xl flex-shrink-0
                           bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700
                           active:scale-90 flex items-center justify-center
                           text-gray-600 dark:text-gray-300 transition-all duration-200">
                <i class="fas fa-bell text-sm"></i>
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1
                                 rounded-full bg-red-500 text-white text-[10px] font-bold
                                 flex items-center justify-center ring-2 ring-white dark:ring-gray-900">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </button>
            <div x-show="notifOpen" x-cloak @click.outside="notifOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)]
                        rounded-2xl shadow-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 overflow-hidden z-50 origin-top-right">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <p class="font-semibold text-sm text-gray-800 dark:text-white">Notifikasi</p>
                </div>
                <div class="px-4 py-10 text-center">
                    <i class="fas fa-bell-slash text-2xl text-gray-300 dark:text-gray-700 mb-2"></i>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada notifikasi baru</p>
                </div>
            </div>
        </div>

        <div class="relative">
            <button type="button" @click="toggleProfile()" :aria-expanded="profileOpen.toString()"
                    class="flex items-center gap-2 pl-1 pr-1.5 md:pr-3 py-1 rounded-full
                           bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700
                           active:scale-[0.97] transition-all duration-200">
                <span class="w-8 h-8 rounded-full flex-shrink-0
                             bg-gradient-to-br from-emerald-500 to-teal-600
                             text-white font-bold flex items-center justify-center text-sm
                             ring-2 ring-white dark:ring-gray-900">{{ $userInitial }}</span>
                <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200 max-w-[100px] truncate">
                    {{ $userName }}
                </span>
                <i class="fas fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200"
                   :class="profileOpen && 'rotate-180'"></i>
            </button>
            <div x-show="profileOpen" x-cloak @click.outside="profileOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-2rem)]
                        rounded-2xl shadow-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 overflow-hidden z-50 origin-top-right">
                <div class="p-4 bg-gradient-to-br from-emerald-500/5 to-teal-500/5
                            dark:from-emerald-500/10 dark:to-teal-500/10
                            border-b border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0
                                    bg-gradient-to-br from-emerald-500 to-teal-600
                                    text-white font-bold flex items-center justify-center text-lg">
                            {{ $userInitial }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-800 dark:text-white truncate">{{ $userName }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $userIdentifier }}</p>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full
                                         bg-emerald-100 dark:bg-emerald-900/30
                                         text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Pengurus
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-1.5 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ $safeRoute('logout') }}"
                       onclick="event.preventDefault(); window.showLogoutModal(event);"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                              text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <i class="fas fa-arrow-right-from-bracket w-4"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>