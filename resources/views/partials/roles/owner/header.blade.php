{{-- Header Owner - Premium dengan Dark Mode, Search, Notifikasi Real --}}
<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40 safe-top transition-colors duration-300">
    <div class="flex items-center justify-between px-3 md:px-6 py-2 md:py-3">
        {{-- ===== KIRI: Toggle Sidebar + Judul ===== --}}
        <div class="flex items-center gap-2 md:gap-4 min-w-0">
            <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none p-1.5 md:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-bars text-lg md:text-xl"></i>
            </button>
            <h1 class="text-sm md:text-xl font-semibold text-gray-800 dark:text-white truncate">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>

        {{-- ===== KANAN: Search | Dark Mode | Notifikasi | Profil ===== --}}
        <div class="flex items-center gap-1.5 md:gap-4 flex-shrink-0">

            {{-- ===== SEARCH ===== --}}
            <div class="relative hidden md:block" id="search-wrapper">
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1.5 focus-within:ring-2 focus-within:ring-cyan-500 dark:focus-within:ring-cyan-400 transition-all duration-300">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500 text-sm mr-2"></i>
                    <input type="text" id="search-input" placeholder="Cari menu..." class="bg-transparent border-none outline-none text-sm text-gray-700 dark:text-gray-200 w-32 md:w-48 lg:w-64 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300 focus:w-48 lg:focus:w-72">
                    <kbd class="hidden md:inline-block text-[10px] font-mono text-gray-400 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded px-1.5 py-0.5 ml-2">Ctrl+K</kbd>
                </div>
                {{-- Dropdown hasil search --}}
                <div id="search-results" class="hidden absolute left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-[60vh] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700"></div>
            </div>

            {{-- ===== DARK MODE TOGGLE ===== --}}
            <button id="theme-toggle" class="relative w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 focus:outline-none group">
                <span class="absolute inset-0 rounded-xl bg-gradient-to-br from-amber-400 to-yellow-500 dark:from-indigo-500 dark:to-purple-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></span>
                <i id="theme-icon" class="fas fa-moon text-lg md:text-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-12"></i>
            </button>

          {{-- ===== NOTIFIKASI REAL ===== --}}
<div class="relative" id="notification-wrapper">
    <button id="notification-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 relative focus:outline-none p-1.5 md:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <i class="fas fa-bell text-base md:text-xl"></i>
        @php
            $unreadCount = 0;
            try {
                $unreadCount = auth()->user()->unreadNotifications()->count();
            } catch (\Exception $e) {
                $unreadCount = 0;
            }
        @endphp
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 md:-top-1 md:-right-1 w-4 h-4 md:w-5 md:h-5 bg-red-500 text-white text-[8px] md:text-[10px] rounded-full flex items-center justify-center font-bold badge-pulse">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Notifikasi --}}
    <div id="notification-dropdown" class="hidden absolute right-0 mt-1 md:mt-3 w-[calc(100vw-2rem)] max-w-[380px] md:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-[80vh] overflow-hidden">
        <div class="p-3 md:p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800 z-10">
            <h3 class="font-bold text-sm md:text-base text-gray-800 dark:text-white">Notifikasi</h3>
            <a href="{{ route('owner.notifications.index') }}" class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-y-auto max-h-[60vh] divide-y divide-gray-100 dark:divide-gray-700" id="notification-list">
            @php
                try {
                    $notifications = auth()->user()->notifications()->latest()->take(10)->get();
                } catch (\Exception $e) {
                    $notifications = collect();
                }
            @endphp
            @forelse($notifications as $notif)
                <a href="{{ $notif->link ?? '#' }}" class="block px-3 md:px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $notif->read_at ? 'opacity-70' : '' }}" data-id="{{ $notif->id }}">
                    <div class="flex items-start gap-2 md:gap-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center flex-shrink-0 text-sm md:text-base"
                             style="background: rgba({{ $notif->type === 'success' ? '34,197,94' : ($notif->type === 'warning' ? '234,179,8' : ($notif->type === 'danger' ? '239,68,68' : '59,130,246')) }}, 0.12);">
                            <i class="fas {{ $notif->icon ?? 'fa-bell' }} text-{{ $notif->type === 'success' ? 'green' : ($notif->type === 'warning' ? 'yellow' : ($notif->type === 'danger' ? 'red' : 'blue')) }}-500 dark:text-{{ $notif->type === 'success' ? 'green' : ($notif->type === 'warning' ? 'yellow' : ($notif->type === 'danger' ? 'red' : 'blue')) }}-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $notif->title }}</p>
                            <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 truncate">{{ $notif->message }}</p>
                            <p class="text-[8px] md:text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notif->read_at)
                            <span class="w-1.5 h-1.5 md:w-2 md:h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="py-6 md:py-8 text-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-inbox text-2xl md:text-3xl mb-2 block"></i>
                    <p class="text-xs md:text-sm">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

            {{-- ===== PROFIL LUXURY ===== --}}
            <div class="relative" id="profile-wrapper">
                <button id="profile-toggle" class="flex items-center gap-1 md:gap-2 focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                    {{-- Avatar dengan ring gradient --}}
                    <div class="relative flex-shrink-0">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 dark:from-cyan-500 dark:to-blue-600 animate-pulse-slow opacity-80"></div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff&size=40"
                             alt="Avatar" class="h-8 w-8 md:h-10 md:w-10 rounded-full border-2 border-white dark:border-gray-700 shadow-md relative z-10 transition-transform duration-300 group-hover:scale-105">
                        <span class="absolute -bottom-0.5 -right-0.5 z-20 w-2.5 h-2.5 md:w-3 md:h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                    </div>
                    <div class="hidden sm:block text-left min-w-0">
                        <p class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-200 truncate max-w-[80px] md:max-w-[120px]">{{ Auth::user()->name }}</p>
                        <p class="text-[8px] md:text-[10px] text-gray-400 dark:text-gray-500 truncate max-w-[80px] md:max-w-[120px]">
                            {{ Auth::user()->roles->pluck('name')->first() ?? 'Owner' }}
                        </p>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] md:text-xs text-gray-400 dark:text-gray-500 hidden sm:inline transition-transform duration-300 group-hover:rotate-180"></i>
                </button>

                {{-- Dropdown Profil Luxury --}}
                <div id="profile-dropdown" class="hidden absolute right-0 mt-1 md:mt-3 w-[calc(100vw-2rem)] max-w-[340px] md:w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden dropdown-enter">
                    {{-- Header Profil --}}
                    <div class="px-5 py-4 bg-gradient-to-r from-cyan-50 via-blue-50 to-indigo-50 dark:from-cyan-900/20 dark:via-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 dark:from-cyan-500 dark:to-blue-600 opacity-60"></div>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff&size=80"
                                     alt="Avatar" class="h-16 w-16 md:h-20 md:w-20 rounded-full border-2 border-white dark:border-gray-700 shadow-xl relative z-10">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-base md:text-lg text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">
                                        <i class="fas fa-shield-alt text-[8px]"></i>
                                        {{ Auth::user()->roles->pluck('name')->implode(', ') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                        <i class="fas fa-circle text-[6px] text-emerald-500"></i>
                                        {{ Auth::user()->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        <i class="fas fa-layer-group text-[8px]"></i>
                                        Level {{ Auth::user()->level_id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Menu --}}
                    <div class="py-1 divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="py-1">
                            <a href="{{ Auth::user()->getDashboardRoute() }}" class="flex items-center gap-3 px-5 py-2.5 text-xs md:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-tachometer-alt w-5 text-cyan-500 dark:text-cyan-400"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('owner.settings.profile') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs md:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-y border-gray-100 dark:border-gray-700">
                                <i class="fas fa-user-cog w-5 text-blue-500 dark:text-blue-400"></i>
                                <span>Data Diri</span>
                            </a>
                            <a href="{{ route('owner.settings.general') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs md:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-cog w-5 text-purple-500 dark:text-purple-400"></i>
                                <span>Pengaturan Umum</span>
                            </a>
                        </div>
                        <div class="py-1">
                            <button onclick="showLogoutModal(event)" class="flex items-center gap-3 w-full text-left px-5 py-2.5 text-xs md:text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Keluar Akun</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ============================================ --}}
{{-- SEARCH JAVASCRIPT (Live Search Sidebar Menu) --}}
{{-- ============================================ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---- SEARCH FUNCTION ----
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        let searchTimeout = null;

        // Kumpulkan semua menu dari sidebar
        const menuItems = [];
        document.querySelectorAll('#sidebar-menu a, #sidebar-menu .menu-item:not(button)').forEach(el => {
            const href = el.getAttribute('href');
            const text = el.textContent.trim();
            if (href && text && !text.includes('Home') && !text.includes('Logout')) {
                menuItems.push({ text, href, el });
            }
        });

        function performSearch(query) {
            if (!query || query.length < 1) {
                searchResults.classList.add('hidden');
                return;
            }

            const terms = query.toLowerCase().trim().split(/\s+/);
            const results = menuItems.filter(item => {
                const matchAll = terms.every(term => item.text.toLowerCase().includes(term));
                return matchAll;
            });

            if (results.length === 0) {
                searchResults.innerHTML = `
                    <div class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                        <i class="fas fa-search text-2xl mb-2 block opacity-30"></i>
                        Tidak ditemukan menu untuk "<span class="font-semibold">${query}</span>"
                    </div>
                `;
                searchResults.classList.remove('hidden');
                return;
            }

            let html = '';
            results.forEach(item => {
                const highlighted = item.text.replace(new RegExp(query, 'gi'), match => `<span class="bg-yellow-200 dark:bg-yellow-800/50 px-0.5 rounded">${match}</span>`);
                html += `
                    <a href="${item.href}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        <span>${highlighted}</span>
                        <span class="ml-auto text-[10px] text-gray-400 dark:text-gray-500">↗</span>
                    </a>
                `;
            });

            searchResults.innerHTML = html;
            searchResults.classList.remove('hidden');
        }

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            searchTimeout = setTimeout(() => performSearch(query), 150);
        });

        // Tutup search saat klik di luar
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('search-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Keyboard shortcut Ctrl+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
            if (e.key === 'Escape') {
                searchInput.blur();
                searchResults.classList.add('hidden');
            }
        });

        // ---- DARK MODE TOGGLE (Full Theme) ----
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        // Cek preferensi dari localStorage
        const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (currentTheme === 'dark') {
            html.classList.add('dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            html.classList.remove('dark');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }

        themeToggle.addEventListener('click', function() {
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
            // Trigger resize agar layout menyesuaikan
            window.dispatchEvent(new Event('resize'));
        });

        // ---- NOTIFICATION DROPDOWN ----
        const notifToggle = document.getElementById('notification-toggle');
        const notifDropdown = document.getElementById('notification-dropdown');
        if (notifToggle && notifDropdown) {
            notifToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                const wrapper = document.getElementById('notification-wrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !notifDropdown.classList.contains('hidden')) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }

        // ---- PROFILE DROPDOWN ----
        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                const wrapper = document.getElementById('profile-wrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !profileDropdown.classList.contains('hidden')) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        // ---- LOGOUT MODAL ----
        window.showLogoutModal = function(event) {
            event.preventDefault();
            const modal = document.getElementById('logout-confirm-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        };

        window.closeLogoutModal = function() {
            const modal = document.getElementById('logout-confirm-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        document.getElementById('logout-confirm-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLogoutModal();
        });
    });
</script>

<style>
    /* Animasi pulse untuk indikator online */
    @keyframes pulse-slow {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    .animate-pulse-slow {
        animation: pulse-slow 2s ease-in-out infinite;
    }

    /* Dropdown enter animation */
    .dropdown-enter {
        animation: dropdownSlide 0.2s ease-out forwards;
    }
    @keyframes dropdownSlide {
        from { opacity: 0; transform: translateY(-8px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Badge pulse */
    .badge-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }

    /* Search input focus */
    #search-input:focus {
        width: 100%;
    }
    @media (min-width: 768px) {
        #search-input:focus {
            width: 300px;
        }
    }
    @media (min-width: 1024px) {
        #search-input:focus {
            width: 360px;
        }
    }

    /* Scrollbar di search results */
    #search-results::-webkit-scrollbar {
        width: 4px;
    }
    #search-results::-webkit-scrollbar-track {
        background: transparent;
    }
    #search-results::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .dark #search-results::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
</style>