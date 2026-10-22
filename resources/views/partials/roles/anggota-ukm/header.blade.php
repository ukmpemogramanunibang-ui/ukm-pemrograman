{{-- resources/views/partials/roles/anggota-ukm/header.blade.php --}}
@php
    $authUser = auth()->user();

    /* ---------- Notifikasi: hitung unread dengan fallback aman ---------- */
    $unreadCount = 0;
    if ($authUser && method_exists($authUser, 'unreadNotifications')) {
        try {
            $unreadCount = (int) $authUser->unreadNotifications()->count();
        } catch (\Throwable $e) {
            $unreadCount = 0;
        }
    }

    /* ---------- Data user ---------- */
    $userName       = $authUser->name ?? 'User';
    $userInitial    = strtoupper(mb_substr($userName, 0, 1));
    $userIdentifier = $authUser->nim ?? $authUser->email ?? '';
    $userRole       = $authUser->roles->pluck('name')->first() ?? 'Anggota UKM';

    /* ---------- Cek route aman ---------- */
    $safeRoute = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name)
        ? route($name, $params) : '#';
@endphp

{{-- Pastikan di app.css ada: [x-cloak]{display:none!important} --}}

<header
    x-data="headerBar()"
    x-init="initHeader()"
    @keydown.escape.window="closeAll()"
    role="banner"
    class="h-16 bg-white/85 dark:bg-gray-900/85 backdrop-blur-md
           border-b border-gray-200 dark:border-gray-800
           flex items-center justify-between gap-3 px-3 md:px-6
           flex-shrink-0 z-20 sticky top-0">

    {{-- ═══════════════════════════════════════════════════════
         LEFT — Toggle sidebar + Breadcrumb
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 md:gap-3 min-w-0">

        {{-- ─── Toggle Sidebar (sinkron dengan Alpine.store('sidebar')) ─── --}}
        <button type="button"
                @click="$store.sidebar.toggle()"
                :title="$store.sidebar.isDesktop
                          ? ($store.sidebar.desktopOpen
                                ? 'Tutup sidebar (Ctrl+B)'
                                : 'Buka sidebar (Ctrl+B)')
                          : 'Buka menu navigasi'"
                :aria-label="$store.sidebar.isDesktop
                                ? 'Toggle sidebar'
                                : 'Buka menu navigasi'"
                :aria-expanded="$store.sidebar.isDesktop
                                    ? $store.sidebar.desktopOpen.toString()
                                    : $store.sidebar.mobileOpen.toString()"
                aria-controls="mainContent"
                class="group w-10 h-10 rounded-xl flex-shrink-0
                       bg-gray-100 dark:bg-gray-800
                       hover:bg-gray-200 dark:hover:bg-gray-700
                       active:scale-90
                       flex items-center justify-center
                       text-gray-600 dark:text-gray-300
                       hover:text-cyan-600 dark:hover:text-cyan-400
                       transition-all duration-200
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">

            {{-- Desktop: chevron berubah arah real-time --}}
            <i class="hidden md:inline-flex items-center justify-center
                      fas text-sm transition-transform duration-300 ease-out"
               :class="$store.sidebar.desktopOpen
                          ? 'fa-chevron-left'
                          : 'fa-chevron-right'"></i>

            {{-- Mobile: hamburger --}}
            <i class="md:hidden fas fa-bars text-sm
                      group-hover:scale-110 transition-transform duration-200"></i>
        </button>

        {{-- ─── Breadcrumb ─── --}}
        <div class="flex flex-col min-w-0 leading-tight">
            <h1 class="text-sm md:text-base font-semibold
                       text-gray-800 dark:text-white truncate">
                @yield('breadcrumb', 'Dashboard')
            </h1>
            @hasSection('breadcrumb-sub')
                <p class="hidden sm:block text-[11px] text-gray-400 dark:text-gray-500 truncate">
                    @yield('breadcrumb-sub')
                </p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         RIGHT — Actions
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-1 md:gap-1.5">

        {{-- ─── Search (desktop only) ─── --}}
        <button type="button"
                @click="openSearch()"
                class="hidden md:flex items-center gap-2 h-10 px-3 rounded-xl
                       bg-gray-100 dark:bg-gray-800
                       hover:bg-gray-200 dark:hover:bg-gray-700
                       active:scale-[0.97]
                       text-gray-500 dark:text-gray-400
                       transition-all duration-200
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
            <i class="fas fa-magnifying-glass text-xs"></i>
            <span class="text-xs font-medium">Cari…</span>
            <kbd class="ml-2 px-1.5 py-0.5 rounded
                        bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-700
                        text-[10px] font-mono text-gray-400">
                ⌘K
            </kbd>
        </button>

        {{-- ─── Dark Mode toggle ─── --}}
        <button type="button"
                @click="toggleTheme()"
                :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
                :aria-label="isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
                class="w-10 h-10 rounded-xl flex-shrink-0
                       bg-gray-100 dark:bg-gray-800
                       hover:bg-gray-200 dark:hover:bg-gray-700
                       active:scale-90
                       flex items-center justify-center
                       text-gray-600 dark:text-gray-300
                       transition-all duration-200
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
            <i class="fas text-sm transition-transform duration-300"
               :class="isDark ? 'fa-sun rotate-0' : 'fa-moon rotate-0'"></i>
        </button>

        {{-- ─── Notifications ─── --}}
        <div class="relative">
            <button type="button"
                    @click="toggleNotif()"
                    :aria-expanded="notifOpen.toString()"
                    aria-haspopup="true"
                    aria-label="Notifikasi"
                    title="Notifikasi"
                    class="relative w-10 h-10 rounded-xl flex-shrink-0
                           bg-gray-100 dark:bg-gray-800
                           hover:bg-gray-200 dark:hover:bg-gray-700
                           active:scale-90
                           flex items-center justify-center
                           text-gray-600 dark:text-gray-300
                           transition-all duration-200
                           focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
                <i class="fas fa-bell text-sm"
                   :class="notifOpen && 'animate-wiggle'"></i>

                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1
                                 rounded-full bg-red-500 text-white text-[10px] font-bold
                                 flex items-center justify-center
                                 ring-2 ring-white dark:ring-gray-900
                                 animate-pulse-slow">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </button>

            {{-- Notif dropdown --}}
            <div x-show="notifOpen"
                 x-cloak
                 @click.outside="notifOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 role="menu"
                 class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)]
                        rounded-2xl shadow-2xl
                        bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800
                        overflow-hidden z-50 origin-top-right">

                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800
                            flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-sm text-gray-800 dark:text-white">
                            Notifikasi
                        </p>
                        @if($unreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                         bg-red-500/10 text-red-500">
                                {{ $unreadCount }} baru
                            </span>
                        @endif
                    </div>
                    <a href="{{ $safeRoute('anggota.pengumuman') }}"
                       class="text-[11px] text-cyan-600 hover:text-cyan-700
                              dark:text-cyan-400 font-medium transition-colors">
                        Lihat semua
                    </a>
                </div>

                <div class="max-h-80 overflow-y-auto p-2">
                    <div class="px-4 py-10 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl
                                    bg-gray-100 dark:bg-gray-800
                                    flex items-center justify-center mb-3">
                            <i class="fas fa-bell-slash text-xl
                                      text-gray-300 dark:text-gray-700"></i>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Belum ada notifikasi baru
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                            Kami akan memberi tahu Anda di sini.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Profile Dropdown ─── --}}
        <div class="relative">
            <button type="button"
                    @click="toggleProfile()"
                    :aria-expanded="profileOpen.toString()"
                    aria-haspopup="true"
                    aria-label="Menu profil"
                    class="flex items-center gap-2 pl-1 pr-1.5 md:pr-3 py-1 rounded-full
                           bg-gray-100 dark:bg-gray-800
                           hover:bg-gray-200 dark:hover:bg-gray-700
                           active:scale-[0.97]
                           transition-all duration-200
                           focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
                <span class="w-8 h-8 rounded-full flex-shrink-0
                             bg-gradient-to-br from-cyan-500 to-indigo-600
                             text-white font-bold flex items-center justify-center text-sm
                             ring-2 ring-white dark:ring-gray-900">
                    {{ $userInitial }}
                </span>
                <span class="hidden md:block text-sm font-medium
                             text-gray-700 dark:text-gray-200
                             max-w-[100px] truncate">
                    {{ $userName }}
                </span>
                <i class="fas fa-chevron-down text-[10px]
                          text-gray-500 dark:text-gray-400
                          transition-transform duration-200"
                   :class="profileOpen && 'rotate-180'"></i>
            </button>

            {{-- Profile dropdown --}}
            <div x-show="profileOpen"
                 x-cloak
                 @click.outside="profileOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 role="menu"
                 class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-2rem)]
                        rounded-2xl shadow-2xl
                        bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800
                        overflow-hidden z-50 origin-top-right">

                {{-- Header profil --}}
                <div class="p-4 bg-gradient-to-br
                            from-cyan-500/5 to-indigo-500/5
                            dark:from-cyan-500/10 dark:to-indigo-500/10
                            border-b border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0
                                    bg-gradient-to-br from-cyan-500 to-indigo-600
                                    text-white font-bold flex items-center justify-center text-lg
                                    shadow-lg shadow-cyan-500/30">
                            {{ $userInitial }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-800 dark:text-white truncate leading-tight">
                                {{ $userName }}
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                {{ $userIdentifier }}
                            </p>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full
                                         bg-emerald-100 dark:bg-emerald-900/30
                                         text-[10px] font-semibold
                                         text-emerald-700 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ $userRole }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Menu --}}
                <div class="p-1.5">
                    @php
                        $profileMenus = [
                            ['route'=>'anggota.profil',    'icon'=>'fa-user',           'label'=>'Profil Saya'],
                            ['route'=>'anggota.poin',      'icon'=>'fa-trophy',         'label'=>'Leaderboard'],
                            ['route'=>'anggota.jadwal',    'icon'=>'fa-calendar-days',  'label'=>'Jadwal Saya'],
                            ['route'=>'anggota.chatting',  'icon'=>'fa-comments',       'label'=>'Chatting'],
                            ['route'=>'anggota.pengaturan','icon'=>'fa-gear',           'label'=>'Pengaturan'],
                        ];
                    @endphp

                    @foreach($profileMenus as $menu)
                        <a href="{{ $safeRoute($menu['route']) }}"
                           role="menuitem"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                                  text-gray-700 dark:text-gray-300
                                  hover:bg-gray-100 dark:hover:bg-gray-800
                                  transition-colors duration-150 group">
                            <i class="fas {{ $menu['icon'] }} w-4 text-gray-400
                                      group-hover:text-cyan-500 transition-colors"></i>
                            {{ $menu['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Logout --}}
                <div class="p-1.5 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ $safeRoute('logout') }}"
                       role="menuitem"
                       onclick="event.preventDefault(); window.showLogoutModal && window.showLogoutModal(event);"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                              text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                              transition-colors duration-150 group">
                        <i class="fas fa-arrow-right-from-bracket w-4
                                  group-hover:translate-x-0.5 transition-transform"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SEARCH PALETTE — overlay full (fungsi ⌘K / Ctrl+K)
    ═══════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="searchOpen"
             x-cloak
             x-transition.opacity.duration.200ms
             class="fixed inset-0 z-[100] flex items-start justify-center
                    pt-[10vh] px-4
                    bg-black/40 backdrop-blur-sm"
             @click.self="searchOpen = false">

            <div x-show="searchOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="w-full max-w-xl rounded-2xl shadow-2xl
                        bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800
                        overflow-hidden">

                <div class="flex items-center gap-3 px-4 py-3
                            border-b border-gray-200 dark:border-gray-800">
                    <i class="fas fa-magnifying-glass text-gray-400"></i>
                    <input type="text"
                           x-ref="searchInput"
                           x-model="searchQuery"
                           @keydown.escape="searchOpen = false"
                           placeholder="Cari menu, halaman, pengumuman…"
                           class="flex-1 bg-transparent outline-none text-sm
                                  text-gray-800 dark:text-white placeholder-gray-400">
                    <kbd class="px-1.5 py-0.5 rounded
                                bg-gray-100 dark:bg-gray-800
                                border border-gray-200 dark:border-gray-700
                                text-[10px] font-mono text-gray-400">
                        ESC
                    </kbd>
                </div>

                <div class="max-h-96 overflow-y-auto p-2">
                    <template x-if="searchQuery.trim().length === 0">
                        <div class="px-4 py-8 text-center">
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Ketik untuk mencari menu, halaman, atau pengumuman.
                            </p>
                        </div>
                    </template>

                    <template x-if="searchQuery.trim().length > 0">
                        <div>
                            <p class="px-3 pt-2 pb-1 text-[10px] font-semibold uppercase
                                      tracking-wider text-gray-400">
                                Hasil Pencarian
                            </p>
                            <a href="{{ $safeRoute('anggota.pengumuman') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                                      text-gray-700 dark:text-gray-300
                                      hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <i class="fas fa-bullhorn w-4 text-cyan-500"></i>
                                <span>
                                    Cari "
                                    <span class="font-semibold" x-text="searchQuery"></span>
                                    " di pengumuman
                                </span>
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</header>

{{-- ═══════════════════════════════════════════════════════
     STYLE: animasi kecil yang ringan
═══════════════════════════════════════════════════════ --}}
@once
@push('styles')
<style>
    @keyframes wiggle {
        0%, 100% { transform: rotate(0deg); }
        20%      { transform: rotate(-12deg); }
        40%      { transform: rotate(12deg); }
        60%      { transform: rotate(-8deg); }
        80%      { transform: rotate(8deg); }
    }
    .animate-wiggle { animation: wiggle 0.6s ease-in-out; }

    @keyframes pulse-slow {
        0%, 100% { transform: scale(1); opacity: 1; }
        50%      { transform: scale(1.08); opacity: 0.85; }
    }
    .animate-pulse-slow { animation: pulse-slow 2.4s ease-in-out infinite; }
</style>
@endpush
@endonce

{{-- ═══════════════════════════════════════════════════════
     SCRIPT: headerBar() — komponen Alpine lokal untuk header
═══════════════════════════════════════════════════════ --}}
@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('headerBar', () => ({
        /* ------- State dropdown ------- */
        notifOpen:   false,
        profileOpen: false,

        /* ------- State search palette ------- */
        searchOpen:  false,
        searchQuery: '',

        /* ------- State dark mode ------- */
        isDark: document.documentElement.classList.contains('dark'),

        /* ==========================================================
           INIT
        ========================================================== */
        initHeader() {
            /* -- Shortcut ⌘K / Ctrl+K → buka search palette -- */
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    this.openSearch();
                }
            });

            /* -- Sinkronisasi dark mode lintas tab & preferensi OS -- */
            window.matchMedia('(prefers-color-scheme: dark)')
                  .addEventListener('change', (e) => {
                      const saved = localStorage.getItem('theme');
                      if (!saved) {
                          document.documentElement.classList.toggle('dark', e.matches);
                          this.isDark = e.matches;
                      }
                  });

            /* -- Sinkron dengan theme.js kalau ada -- */
            window.addEventListener('themeChanged', (e) => {
                this.isDark = !!e.detail?.dark;
            });

            /* -- Tutup semua dropdown saat resize (cegah bug posisi) -- */
            let rt;
            window.addEventListener('resize', () => {
                clearTimeout(rt);
                rt = setTimeout(() => this.closeAll(), 120);
            });

            /* -- Tutup semua dropdown saat scroll halaman -- */
            window.addEventListener('scroll', () => this.closeAll(),
                { passive: true });
        },

        /* ==========================================================
           DROPDOWN HELPERS
        ========================================================== */
        closeAll() {
            this.notifOpen   = false;
            this.profileOpen = false;
        },
        toggleNotif() {
            this.notifOpen = !this.notifOpen;
            this.profileOpen = false;
        },
        toggleProfile() {
            this.profileOpen = !this.profileOpen;
            this.notifOpen   = false;
        },

        /* ==========================================================
           SEARCH PALETTE
        ========================================================== */
        openSearch() {
            this.searchOpen = true;
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        /* ==========================================================
           DARK MODE
        ========================================================== */
        toggleTheme() {
            const html = document.documentElement;
            this.isDark = html.classList.toggle('dark');
            try {
                localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
            } catch (e) { /* localStorage bisa diblokir */ }

            /* Beri tahu theme.js / komponen lain */
            window.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { dark: this.isDark }
            }));
        },
    }));
});
</script>
@endpush
@endonce