{{-- Header Owner - Premium dengan Dark Mode, Search, Notifikasi Realtime --}}
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
                <div id="search-results" class="hidden absolute left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-[60vh] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700"></div>
            </div>

            {{-- ===== DARK MODE TOGGLE ===== --}}
            <button id="theme-toggle" class="relative w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 focus:outline-none group">
                <span class="absolute inset-0 rounded-xl bg-gradient-to-br from-amber-400 to-yellow-500 dark:from-indigo-500 dark:to-purple-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></span>
                <i id="theme-icon" class="fas fa-moon text-lg md:text-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-12"></i>
            </button>

{{-- ===== NOTIFIKASI REALTIME — Mobile Optimized ===== --}}
@php
    $initialUnreadCount = 0;
    try {
        $initialUnreadCount = auth()->user()->unreadNotifications()->count();
    } catch (\Throwable $e) {
        $initialUnreadCount = 0;
    }
@endphp
<div class="relative" id="notification-wrapper" data-initial-count="{{ $initialUnreadCount }}">

    {{-- Bell Button --}}
    <button id="notification-toggle"
            type="button"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 relative focus:outline-none p-1.5 md:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            aria-label="Notifikasi">
        <i class="fas fa-bell text-base md:text-xl" id="notification-bell-icon"></i>
        <span id="notification-badge"
              class="absolute -top-0.5 -right-0.5 md:-top-1 md:-right-1 min-w-[16px] h-4 md:min-w-[20px] md:h-5 px-1 items-center justify-center bg-red-500 text-white text-[8px] md:text-[10px] rounded-full font-bold badge-pulse {{ $initialUnreadCount > 0 ? 'flex' : 'hidden' }}">
            {{ $initialUnreadCount > 9 ? '9+' : $initialUnreadCount }}
        </span>
    </button>

    {{-- Dropdown — Mobile: fixed full width | Desktop: absolute right-0 --}}
    <div id="notification-dropdown"
         class="hidden
                fixed md:absolute
                left-2 right-2 top-16
                md:left-auto md:right-0 md:top-auto md:mt-3
                md:w-96
                bg-white dark:bg-gray-800
                rounded-2xl md:rounded-xl
                shadow-2xl
                border border-gray-200 dark:border-gray-700
                z-[60]
                overflow-hidden
                max-h-[calc(100vh-5.5rem)] md:max-h-[80vh]
                flex flex-col">

        {{-- Header --}}
        <div class="flex-shrink-0 p-3.5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
            <div class="flex items-center gap-2 min-w-0">
                <h3 class="font-bold text-base text-gray-800 dark:text-white">Notifikasi</h3>
                <span id="notification-pill"
                      class="text-[10px] px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 rounded-full font-bold {{ $initialUnreadCount > 0 ? '' : 'hidden' }}">
                    {{ $initialUnreadCount }} baru
                </span>
            </div>
            <button id="notification-markall"
                    type="button"
                    class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-semibold whitespace-nowrap ml-2 {{ $initialUnreadCount > 0 ? '' : 'hidden' }}">
                Tandai dibaca
            </button>
        </div>

        {{-- Mini Tabs --}}
        <div class="flex-shrink-0 flex border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
            <button type="button" data-notif-tab="all"
                    class="notif-tab flex-1 py-2.5 text-xs font-semibold border-b-2 text-cyan-600 border-cyan-500 transition">
                Semua
            </button>
            <button type="button" data-notif-tab="unread"
                    class="notif-tab flex-1 py-2.5 text-xs font-semibold border-b-2 text-gray-500 border-transparent transition">
                Belum
            </button>
            <button type="button" data-notif-tab="starred"
                    class="notif-tab flex-1 py-2.5 text-xs font-semibold border-b-2 text-gray-500 border-transparent transition">
                ★ Favorit
            </button>
        </div>

        {{-- List — scrollable --}}
        <div class="overflow-y-auto overscroll-contain flex-1 divide-y divide-gray-100 dark:divide-gray-700"
             id="notification-list">
            <div class="py-10 text-center text-gray-400 dark:text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                <p class="text-sm">Memuat notifikasi...</p>
            </div>
        </div>

        {{-- Footer --}}
        <a href="{{ route('owner.notifications.index') }}"
           class="flex-shrink-0 block text-center py-3.5 text-sm font-semibold text-cyan-600 dark:text-cyan-400 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition border-t border-gray-200 dark:border-gray-700">
            Lihat semua notifikasi →
        </a>
    </div>

    {{-- Toast Container --}}
    <div id="notification-toasts"
         class="fixed bottom-4 right-4 left-4 sm:left-auto sm:bottom-6 sm:right-6 z-[9999] flex flex-col gap-2 pointer-events-none sm:w-80"></div>
</div>
            {{-- ===== PROFIL LUXURY ===== --}}
            <div class="relative" id="profile-wrapper">
                <button id="profile-toggle" class="flex items-center gap-1 md:gap-2 focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
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

                <div id="profile-dropdown" class="hidden absolute right-0 mt-1 md:mt-3 w-[calc(100vw-2rem)] max-w-[340px] md:w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden dropdown-enter">
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
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ============================================================
     | 1. SEARCH
     * ============================================================ */
    const searchInput   = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    let searchTimeout   = null;

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
        const results = menuItems.filter(item =>
            terms.every(term => item.text.toLowerCase().includes(term))
        );

        if (results.length === 0) {
            searchResults.innerHTML = `
                <div class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                    <i class="fas fa-search text-2xl mb-2 block opacity-30"></i>
                    Tidak ditemukan menu untuk "<span class="font-semibold">${query}</span>"
                </div>`;
            searchResults.classList.remove('hidden');
            return;
        }

        let html = '';
        results.forEach(item => {
            const highlighted = item.text.replace(
                new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi'),
                match => `<span class="bg-yellow-200 dark:bg-yellow-800/50 px-0.5 rounded">${match}</span>`
            );
            html += `
                <a href="${item.href}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm text-gray-700 dark:text-gray-300">
                    <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                    <span>${highlighted}</span>
                    <span class="ml-auto text-[10px] text-gray-400 dark:text-gray-500">↗</span>
                </a>`;
        });

        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');
    }

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            searchTimeout = setTimeout(() => performSearch(q), 150);
        });

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('search-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
            if (e.key === 'Escape') {
                searchInput.blur();
                searchResults.classList.add('hidden');
            }
        });
    }

    /* ============================================================
     | 2. DARK MODE TOGGLE  ← INI YANG HILANG DI FILE ANDA
     * ============================================================ */
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon   = document.getElementById('theme-icon');
    const root        = document.documentElement;

    function applyTheme(theme) {
        if (theme === 'dark') {
            root.classList.add('dark');
            root.style.colorScheme = 'dark';
            if (themeIcon) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        } else {
            root.classList.remove('dark');
            root.style.colorScheme = 'light';
            if (themeIcon) {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    }

    // Initial theme
    const savedTheme = localStorage.getItem('theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(savedTheme || (systemDark ? 'dark' : 'light'));

    // Click handler
    if (themeToggle) {
        themeToggle.addEventListener('click', function (e) {
            e.preventDefault();
            const isDark = root.classList.contains('dark');
            const next   = isDark ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('theme', next);
            window.dispatchEvent(new Event('resize'));
        });
    }

    // Sinkron kalau OS user ganti
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });

    /* ============================================================
     | 3. PROFILE DROPDOWN
     * ============================================================ */
    const profileToggle   = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('profile-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
            }
        });
    }

    /* ============================================================
     | 4. LOGOUT MODAL
     * ============================================================ */
    window.showLogoutModal = function (event) {
        if (event && typeof event.preventDefault === 'function') event.preventDefault();
        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            const inner = modal.querySelector('.transform');
            if (inner) {
                inner.classList.remove('scale-95');
                inner.classList.add('scale-100');
            }
        }, 10);
    };

    window.closeLogoutModal = function () {
        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;
        modal.classList.add('opacity-0');
        const inner = modal.querySelector('.transform');
        if (inner) {
            inner.classList.remove('scale-100');
            inner.classList.add('scale-95');
        }
        setTimeout(() => modal.classList.add('hidden'), 300);
    };

    const logoutModal = document.getElementById('logout-confirm-modal');
    if (logoutModal) {
        logoutModal.addEventListener('click', function (e) {
            if (e.target === this) window.closeLogoutModal();
        });
    }

    /* ============================================================
     | 5. NOTIFICATION MODULE
     * ============================================================ */
    initNotificationModule();
});


/* ============================================================
 | NOTIFICATION MODULE — versi bersih, tanpa duplikasi
 * ============================================================ */
function initNotificationModule() {
    'use strict';

    const wrapper  = document.getElementById('notification-wrapper');
    const toggle   = document.getElementById('notification-toggle');
    const drop     = document.getElementById('notification-dropdown');
    const badge    = document.getElementById('notification-badge');
    const pill     = document.getElementById('notification-pill');
    const markAll  = document.getElementById('notification-markall');
    const list     = document.getElementById('notification-list');
    const bellIcon = document.getElementById('notification-bell-icon');
    const toastBox = document.getElementById('notification-toasts');

    if (!wrapper || !toggle || !drop) return;

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const BASE = '{{ url("owner/notifications") }}';
    const URLS = {
        poll:    BASE + '/poll',
        readAll: BASE + '/read-all',
        index:   BASE,
        show:    BASE + '/',
    };

    let count     = parseInt(wrapper.dataset.initialCount || '0', 10);
    let items     = [];
    let activeTab = 'all';
    let isOpen    = false;
    let lastId    = '';
    let firstLoad = true;
    let pollTimer = null;

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[c]));
    }

    function setCount(n) {
        count = Math.max(0, n);
        wrapper.dataset.initialCount = count;

        if (count > 0) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            badge.textContent = count > 9 ? '9+' : count;
            pill.classList.remove('hidden');
            pill.textContent = count + ' baru';
            markAll.classList.remove('hidden');
            document.title = document.title.replace(/^\(\d+\)\s*/, '');
            document.title = `(${count}) ${document.title}`;
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
            pill.classList.add('hidden');
            markAll.classList.add('hidden');
            document.title = document.title.replace(/^\(\d+\)\s*/, '');
        }
    }

    function renderList() {
        const filtered = items.filter(it => {
            if (activeTab === 'unread')  return !it.is_read && !it.source_gone;
            if (activeTab === 'starred') return it.is_starred && !it.source_gone;
            return !it.source_gone;
        });

        if (!filtered.length) {
            const emoji = activeTab === 'starred' ? '⭐' : '🎉';
            const text  = activeTab === 'starred'
                ? 'Belum ada notifikasi favorit'
                : (activeTab === 'unread' ? 'Semua notifikasi sudah dibaca' : 'Belum ada notifikasi');
            list.innerHTML = `
                <div class="py-10 text-center text-gray-400 dark:text-gray-500">
                    <div class="text-4xl mb-3">${emoji}</div>
                    <p class="text-sm">${text}</p>
                </div>`;
            return;
        }

        list.innerHTML = filtered.map(it => {
            const priority = it.priority || 'normal';
            const bgClass =
                priority === 'urgent' ? 'bg-rose-100 dark:bg-rose-900/30'
              : priority === 'high'   ? 'bg-amber-100 dark:bg-amber-900/30'
              :                         'bg-cyan-100 dark:bg-cyan-900/30';

            const starClass = it.is_starred ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600';

            let statusBadge = '';
            if (it.event_type === 'updated' || it.is_edited) {
                statusBadge = `<span class="text-[9px] px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold">✏️ Diperbarui</span>`;
            } else if (it.event_type === 'restored') {
                statusBadge = `<span class="text-[9px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold">♻️ Dipulihkan</span>`;
            } else if (it.event_type === 'deleted') {
                statusBadge = `<span class="text-[9px] px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 font-bold">🗑️ Dihapus</span>`;
            } else if (!it.is_read) {
                statusBadge = `<span class="text-[9px] px-1.5 py-0.5 rounded-full bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300 font-bold">🆕 Baru</span>`;
            }

            return `
            <div class="relative px-3.5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition ${!it.is_read ? 'bg-cyan-50/40 dark:bg-cyan-900/10' : ''}">
                <a href="${it.action_url || URLS.index}"
                   data-notif-open
                   data-id="${it.id}"
                   data-read="${it.is_read ? 1 : 0}"
                   class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-lg ${bgClass}">
                        ${it.source_meta?.emoji || '🔔'}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-1.5 flex-wrap">
                            <p class="text-sm ${!it.is_read ? 'font-semibold' : 'font-medium'} text-gray-800 dark:text-white leading-snug"
                               style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                ${esc(it.title)}
                            </p>
                            ${statusBadge}
                            ${!it.is_read ? '<span class="w-2 h-2 bg-cyan-500 rounded-full flex-shrink-0 mt-1.5"></span>' : ''}
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                           style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            ${esc(it.message || '—')}
                        </p>
                    </div>
                </a>
                <div class="flex items-center justify-between gap-2 mt-2.5 pl-[52px]">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-[10px] text-gray-400 whitespace-nowrap">${esc(it.time_ago || '')}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 truncate max-w-[100px]">
                            ${esc(it.source_meta?.label || '')}
                        </span>
                        ${it.is_edited ? `<span class="text-[9px] text-gray-400">v${it.version}</span>` : ''}
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" data-notif-star data-id="${it.id}" class="p-2 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition active:scale-95">
                            <i class="fas fa-star text-sm ${starClass}"></i>
                        </button>
                        ${it.is_read ? `
                            <button type="button" data-notif-del data-id="${it.id}" class="p-2 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition active:scale-95">
                                <i class="fas fa-trash text-sm text-gray-400 hover:text-rose-500"></i>
                            </button>` : `
                            <button type="button" onclick="event.preventDefault();event.stopPropagation();" class="p-2 rounded-lg opacity-30 cursor-not-allowed">
                                <i class="fas fa-trash text-sm text-gray-400"></i>
                            </button>`}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    async function api(url, method = 'GET') {
        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                },
                credentials: 'same-origin',
            });
            let data = null;
            try { data = await res.json(); } catch (e) {}
            return { ok: res.ok, status: res.status, data };
        } catch (e) {
            return { ok: false, status: 0, data: null };
        }
    }

    async function poll() {
        const url = URLS.poll + (lastId ? `?since_id=${encodeURIComponent(lastId)}` : '');
        const { ok, data } = await api(url);
        if (!ok || !data) return;

        const prevMap = new Map(items.map(i => [i.id, i.version || 1]));
        const updatedItems = (data.items || []).filter(it => {
            const prevVer = prevMap.get(it.id) || 0;
            return (it.version || 1) > prevVer;
        });

        const prev = count;
        setCount(data.unread_count ?? 0);
        items = data.items || [];
        if (data.latest_id) lastId = data.latest_id;

        if (!firstLoad) {
            updatedItems.forEach(it => {
                if ((it.version || 1) > (prevMap.get(it.id) || 0)) {
                    onNewNotification(it);
                }
            });
        }

        if (isOpen) renderList();
        firstLoad = false;
    }

    function onNewNotification(item) {
        if (!item || !bellIcon) return;
        bellIcon.style.animation = 'none';
        void bellIcon.offsetWidth;
        bellIcon.style.animation = 'bell-shake 0.8s ease-in-out';
        showToast(item);
    }

    function showToast(item) {
        if (!toastBox) return;
        const t = document.createElement('div');
        t.className = 'pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 w-80 p-3 flex gap-3 cursor-pointer transition-all duration-300 opacity-0 translate-x-4';
        t.innerHTML = `
            <div class="shrink-0 w-9 h-9 rounded-full bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center text-base">
                ${item.source_meta?.emoji || '🔔'}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs md:text-sm font-semibold text-gray-800 dark:text-white truncate">${esc(item.title)}</p>
                <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-0.5">${esc(item.message || '')}</p>
                <p class="text-[9px] md:text-[10px] text-gray-400 mt-1">${esc(item.time_ago || '')}</p>
            </div>`;
        t.addEventListener('click', () => {
            window.location.href = item.action_url || URLS.index;
        });
        toastBox.appendChild(t);

        requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-4'));
        setTimeout(() => {
            t.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => t.remove(), 300);
        }, 6000);
    }

    function openDropdown() {
        isOpen = true;
        drop.classList.remove('hidden');
        poll();
    }

    function closeDropdown() {
        isOpen = false;
        drop.classList.add('hidden');
    }

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen ? closeDropdown() : openDropdown();
    });

    document.addEventListener('click', (e) => {
        if (isOpen && wrapper && !wrapper.contains(e.target)) closeDropdown();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) closeDropdown();
    });

    document.querySelectorAll('.notif-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            activeTab = tab.dataset.notifTab;
            document.querySelectorAll('.notif-tab').forEach(t => {
                const on = t.dataset.notifTab === activeTab;
                t.classList.toggle('text-cyan-600', on);
                t.classList.toggle('border-cyan-500', on);
                t.classList.toggle('text-gray-500', !on);
                t.classList.toggle('border-transparent', !on);
            });
            renderList();
        });
    });

    list.addEventListener('click', async (e) => {
        const openA = e.target.closest('[data-notif-open]');
        if (openA) {
            if (openA.dataset.read === '0') {
                openA.dataset.read = '1';
                setCount(count - 1);
                api(URLS.show + openA.dataset.id + '/read', 'POST').catch(() => {});
                const it = items.find(x => x.id === openA.dataset.id);
                if (it) it.is_read = true;
            }
            return;
        }

        const starBtn = e.target.closest('[data-notif-star]');
        if (starBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = starBtn.dataset.id;
            const it = items.find(x => x.id === id);
            if (!it) return;
            const newVal = !it.is_starred;
            it.is_starred = newVal;
            renderList();
            const { ok, data } = await api(URLS.show + id + '/toggle-star', 'POST');
            if (!ok) {
                it.is_starred = !newVal;
                renderList();
            } else if (data && typeof data.is_starred === 'boolean') {
                it.is_starred = data.is_starred;
                renderList();
            }
            return;
        }

        const delBtn = e.target.closest('[data-notif-del]');
        if (delBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = delBtn.dataset.id;
            if (!confirm('Hapus notifikasi ini?')) return;
            const { ok, data } = await api(URLS.show + id, 'DELETE');
            if (ok && data?.success) {
                items = items.filter(x => x.id !== id);
                renderList();
                setCount(data.unread_count ?? count);
            } else {
                alert(data?.message || 'Gagal menghapus.');
            }
            return;
        }
    });

    if (markAll) {
        markAll.addEventListener('click', async (e) => {
            e.stopPropagation();
            const { ok } = await api(URLS.readAll, 'POST');
            if (ok) {
                setCount(0);
                items = items.map(i => ({ ...i, is_read: true }));
                renderList();
            }
        });
    }

    setCount(count);
    poll();
    pollTimer = setInterval(poll, 10000);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(pollTimer);
            pollTimer = null;
        } else if (!pollTimer) {
            poll();
            pollTimer = setInterval(poll, 10000);
        }
    });
}
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

    /* Bell shake animation */
    @keyframes bell-shake {
        0%, 100% { transform: rotate(0); }
        15% { transform: rotate(15deg); }
        30% { transform: rotate(-15deg); }
        45% { transform: rotate(10deg); }
        60% { transform: rotate(-10deg); }
        75% { transform: rotate(5deg); }
    }

    /* Line clamp 2 baris */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
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

    /* Scrollbar dropdown notifikasi */
    #notification-list::-webkit-scrollbar {
        width: 4px;
    }
    #notification-list::-webkit-scrollbar-track {
        background: transparent;
    }
    #notification-list::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .dark #notification-list::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
        /* ============================================================
       NOTIFICATION — Mobile Responsive
       ============================================================ */

    /* Mobile: dropdown full width */
    @media (max-width: 767px) {
        #notification-dropdown {
            /* Posisi fixed dari header */
            position: fixed;
            top: 56px;              /* tinggi header mobile */
            left: 8px;
            right: 8px;
            width: auto;
            max-height: calc(100vh - 64px);
            border-radius: 16px;
        }
    }

    /* Item actions selalu terlihat di mobile */
    @media (hover: none) and (pointer: coarse) {
        #notification-list [data-notif-star],
        #notification-list [data-notif-del] {
            opacity: 1 !important;
        }
    }

    /* Desktop: hover-only reveal */
    @media (hover: hover) and (pointer: fine) {
        #notification-list [data-notif-star],
        #notification-list [data-notif-del] {
            opacity: 0.6;
            transition: opacity 0.2s ease;
        }
        #notification-list > div:hover [data-notif-star],
        #notification-list > div:hover [data-notif-del] {
            opacity: 1;
        }
    }

    /* Active press feedback */
    #notification-list button:active {
        transform: scale(0.92);
    }

    /* Truncate panjang source label */
    #notification-list .source-label {
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>