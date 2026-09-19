{{-- resources/views/partials/role/anggota/header.blade.php --}}
@php
    $agent = new \Jenssegers\Agent\Agent();
    $device = $agent->device();
    $platform = $agent->platform();
@endphp

<header class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-30 transition-all duration-300 shadow-sm">
    <div class="flex items-center justify-between px-4 md:px-8 py-3">

        {{-- Left: Sidebar Toggle + Brand + Breadcrumb --}}
        <div class="flex items-center gap-4">
            {{-- Tombol Toggle Sidebar --}}
            <button id="sidebarToggleBtn"
                    class="relative w-10 h-10 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 text-gray-600 dark:text-gray-300 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 hover:text-cyan dark:hover:text-cyan transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center group shadow-sm hover:shadow-cyan-500/20"
                    @click="window.dispatchEvent(new CustomEvent('toggleSidebar'))"
                    :aria-label="layoutState().isDesktop ? (layoutState().isCollapsed ? 'Buka sidebar' : 'Tutup sidebar') : (layoutState().isMobileOpen ? 'Tutup sidebar' : 'Buka sidebar')"
                    title="Toggle Sidebar (Ctrl+B)">
                <i class="fas fa-bars text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                <span class="absolute inset-0 rounded-xl bg-cyan-400/0 group-hover:bg-cyan-400/10 transition-all duration-300"></span>
                <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-cyan-400 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </button>

            {{-- Brand & Breadcrumb --}}
            <div class="hidden md:block">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Halaman</span>
                    <span class="text-gray-300 dark:text-gray-600">/</span>
                    <span class="text-sm font-bold bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent">
                        @yield('breadcrumb', 'Dashboard')
                    </span>
                </div>
            </div>

            {{-- Shortcut hint (desktop) --}}
            <div class="hidden lg:flex items-center gap-1 text-[10px] text-gray-400 dark:text-gray-600 bg-gray-100/50 dark:bg-gray-800/50 px-2 py-1 rounded-lg border border-gray-200/30 dark:border-gray-700/30">
                <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 rounded text-[9px] font-mono text-gray-500 dark:text-gray-400 shadow-sm">Ctrl</kbd>
                <span class="text-gray-400 dark:text-gray-500">+</span>
                <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 rounded text-[9px] font-mono text-gray-500 dark:text-gray-400 shadow-sm">B</kbd>
            </div>
        </div>

        {{-- Right: Notif, Theme, User --}}
        <div class="flex items-center gap-2 md:gap-3">

            {{-- Search Button (Mobile) --}}
            <button class="md:hidden w-10 h-10 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 hover:text-cyan transition-all duration-300 flex items-center justify-center hover:scale-105 active:scale-95">
                <i class="fas fa-search"></i>
            </button>

            {{-- Notifikasi --}}
            <div class="relative group">
                <button class="relative w-10 h-10 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 hover:text-cyan transition-all duration-300 flex items-center justify-center group-hover:scale-105 active:scale-95">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-gradient-to-br from-red-500 to-red-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-lg shadow-red-500/30 animate-pulse">3</span>
                    <span class="absolute inset-0 rounded-xl bg-red-400/0 group-hover:bg-red-400/10 transition-all duration-300"></span>
                </button>
                <div class="absolute right-0 top-full mt-2 w-72 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0 z-50">
                    <p class="text-sm font-bold text-gray-800 dark:text-white mb-2">Notifikasi</p>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <p class="flex items-center gap-2"><span class="w-2 h-2 bg-cyan-500 rounded-full"></span> Rapat besok jam 09.00</p>
                        <p class="flex items-center gap-2"><span class="w-2 h-2 bg-yellow-500 rounded-full"></span> Pendaftaran anggota baru</p>
                        <p class="flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span> Event webinar berhasil dibuat</p>
                    </div>
                    <a href="#" class="block text-center text-xs text-cyan hover:underline mt-3 pt-2 border-t border-gray-200 dark:border-gray-700">Lihat semua</a>
                </div>
            </div>

            {{-- Toggle Theme --}}
            <button id="themeToggle" class="relative w-10 h-10 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 hover:text-cyan transition-all duration-300 flex items-center justify-center overflow-hidden hover:scale-105 active:scale-95">
                <i class="fas fa-moon dark:hidden text-lg group-hover:rotate-12 transition-transform duration-300"></i>
                <i class="fas fa-sun hidden dark:inline text-lg group-hover:rotate-12 transition-transform duration-300"></i>
                <span class="absolute inset-0 rounded-xl bg-cyan-400/0 hover:bg-cyan-400/10 transition-all duration-300"></span>
            </button>

            {{-- Shortcut hint untuk dropdown (desktop) --}}
            <div class="hidden lg:flex items-center gap-1 text-[10px] text-gray-400 dark:text-gray-600 bg-gray-100/50 dark:bg-gray-800/50 px-2 py-1 rounded-lg border border-gray-200/30 dark:border-gray-700/30">
                <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 rounded text-[9px] font-mono text-gray-500 dark:text-gray-400 shadow-sm">Ctrl</kbd>
                <span class="text-gray-400 dark:text-gray-500">+</span>
                <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 rounded text-[9px] font-mono text-gray-500 dark:text-gray-400 shadow-sm">Space</kbd>
            </div>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }" id="profileDropdownContainer">
                <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-2.5 focus:outline-none group px-2 py-1.5 rounded-xl hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                    <div class="relative">
                        <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=06b6d4&color=fff&size=44' }}"
                             alt="Avatar" class="w-9 h-9 rounded-full border-2 border-cyan-400/50 group-hover:border-cyan-400 transition-all duration-300 shadow-md group-hover:shadow-cyan-500/25">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full animate-pulse"></span>
                    </div>
                    <span class="hidden sm:inline text-sm font-semibold text-gray-700 dark:text-gray-200 group-hover:text-cyan transition-colors duration-300 max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-transition:enter.duration.300ms x-transition:leave.duration.200ms
                     class="absolute right-0 mt-2 w-64 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 py-2 z-50 overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 dark:from-cyan-500/20 dark:to-blue-500/20 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center gap-3">
                            <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=06b6d4&color=fff&size=48' }}"
                                 alt="Avatar" class="w-10 h-10 rounded-full border-2 border-cyan-400">
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[140px]">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('anggota.profil') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:text-cyan transition-all duration-200 group">
                        <span class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 text-cyan flex items-center justify-center group-hover:scale-110 transition-transform duration-200"><i class="fas fa-user w-4"></i></span> Profil
                    </a>
                    <a href="{{ route('anggota.pengaturan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:text-cyan transition-all duration-200 group">
                        <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-200"><i class="fas fa-cog w-4"></i></span> Pengaturan
                    </a>
                    <hr class="my-1 border-gray-200/50 dark:border-gray-700/50">
                    <button onclick="showLogoutModal(event)" class="flex items-center gap-3 w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 group">
                        <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-200"><i class="fas fa-sign-out-alt w-4"></i></span> Keluar
                    </button>
                    <div class="px-4 pt-1 pb-1 border-t border-gray-200/50 dark:border-gray-700/50 mt-1">
                        <button @click="open = false" class="w-full text-center text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all duration-200 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50">
                            <i class="fas fa-times-circle mr-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris bawah: Device & Status --}}
    <div class="px-4 md:px-8 py-1.5 bg-gradient-to-r from-transparent via-gray-50/50 dark:via-gray-900/30 to-transparent border-t border-gray-200/30 dark:border-gray-700/30 text-[11px] text-gray-400 dark:text-gray-500 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5">
                <i class="fas fa-laptop text-cyan-400 text-xs"></i>
                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $device }}</span>
                <span class="text-gray-400 dark:text-gray-600">·</span>
                <span class="text-gray-500 dark:text-gray-400">{{ $platform }}</span>
            </span>
            <span class="hidden sm:inline text-gray-300 dark:text-gray-600">|</span>
            <span class="hidden sm:inline flex items-center gap-1.5">
                <i class="fas fa-clock text-gray-400 text-xs"></i>
                <span id="liveTime" class="text-gray-500 dark:text-gray-400 font-mono text-xs"></span>
            </span>
        </div>
        <span id="connectionStatus" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100/80 dark:bg-gray-800/80 text-[10px] font-medium">
            <span class="w-2 h-2 rounded-full bg-green-500 inline-block transition-all duration-500" id="statusDot"></span>
            <span id="statusText" class="text-gray-600 dark:text-gray-300">Online</span>
        </span>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme toggle
        document.getElementById('themeToggle')?.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Live clock
        function updateClock() {
            const el = document.getElementById('liveTime');
            if (el) {
                const now = new Date();
                el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Connection status
        function updateConnectionStatus() {
            const dot = document.getElementById('statusDot');
            const text = document.getElementById('statusText');
            if (!dot || !text) return;
            if (navigator.onLine) {
                dot.className = 'w-2 h-2 rounded-full bg-green-500 inline-block transition-all duration-500 shadow-sm shadow-green-500/50';
                text.textContent = 'Online';
                text.className = 'text-gray-600 dark:text-gray-300';
            } else {
                dot.className = 'w-2 h-2 rounded-full bg-red-500 inline-block transition-all duration-500 shadow-sm shadow-red-500/50 animate-pulse';
                text.textContent = 'Offline';
                text.className = 'text-red-500 dark:text-red-400';
            }
        }
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);
        updateConnectionStatus();
    });
</script>