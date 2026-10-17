@php
    $safeUrl = fn (string $name, $params = [], string $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
    $safeIs  = fn (string ...$p) => request()->routeIs(...$p);

    $isExploring = (bool) session('owner.explore.active', false)
        && (int) auth()->id() === (int) session('owner.explore.owner_id', 0);

    $user = auth()->user();
    if ($isExploring) {
        $user = \App\Models\User::find(session('owner.explore.user_id')) ?? $user;
    }

    /* ═══════════════════════════════════════════════════════════
       STATE MENU — HANYA route ketua.*
       ═══════════════════════════════════════════════════════════ */
    $isManajemen = $safeIs(
        'ketua.manajemen-anggota.*',
        'ketua.manajemen-pengurus.*',
        'ketua.jabatan.*',
        'ketua.divisi.*',
        'ketua.kelompok.*'
    );

    $isKegiatan   = $safeIs('ketua.kegiatan');
    $isPresensi   = $safeIs('ketua.presensi');
    $isKeuangan   = $safeIs('ketua.keuangan');
    $isLeaderPoin = $safeIs('ketua.poin-prestasi');
    $isKomun      = $safeIs('ketua.pengumuman.*');
    $isApproval   = $safeIs('ketua.approval');
    $isLaporan    = $safeIs('ketua.laporan');
    $isNotif      = $safeIs('ketua.notifications.*');
    $isPengaturan = $safeIs('ketua.pengaturan');
@endphp

<aside id="sidebar"
       class="bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800
              h-screen overflow-y-auto flex-shrink-0 w-72
              shadow-xl shadow-gray-200/40 dark:shadow-gray-900/40"
       aria-label="Sidebar navigasi Ketua">

    {{-- ═══════════════ LOGO ═══════════════ --}}
    <div class="flex items-center gap-3 p-4 border-b border-gray-200 dark:border-gray-800
                sticky top-0 z-10 bg-white/95 dark:bg-gray-900/95 backdrop-blur">
        <a href="{{ $safeUrl('ketua.dashboard') }}"
           class="flex items-center gap-3 flex-1 min-w-0">
            <div class="relative shrink-0">
                <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo"
                     class="h-10 w-10 rounded-xl ring-2 ring-purple-500/40 object-cover">
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500
                             border-2 border-white dark:border-gray-900 rounded-full animate-pulse"></span>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-lg font-extrabold text-transparent bg-clip-text
                             bg-gradient-to-r from-purple-600 to-pink-500 leading-none">
                    UNICODE
                </span>
                <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 mt-0.5 tracking-widest">
                    KETUA PANEL
                </span>
            </div>
        </a>
    </div>

    {{-- ═══════════════ NAV ═══════════════ --}}
    <nav class="p-3 space-y-1 pb-6" id="ketua-sidebar-nav">

        {{-- ═══ DASHBOARD ═══ --}}
        <a href="{{ $safeUrl('ketua.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                  {{ $safeIs('ketua.dashboard')
                    ? 'bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/40 dark:to-pink-900/30
                       text-purple-700 dark:text-purple-300 font-semibold shadow-sm'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-home w-5 text-center"></i>
            <span class="text-sm">Dashboard</span>
        </a>

        {{-- ═══ MANAJEMEN (persist) ═══ --}}
        <div class="sidebar-group pt-2" data-group="manajemen">
            <button type="button"
                    data-group-toggle="manajemen"
                    aria-expanded="{{ $isManajemen ? 'true' : 'false' }}"
                    class="sidebar-group-toggle flex items-center justify-between w-full
                           px-3 py-2 rounded-lg cursor-pointer
                           hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                    Manajemen
                </span>
                <i class="fas fa-chevron-down text-[9px] text-gray-400 transition-transform duration-200 group-chevron"
                   style="transform: {{ $isManajemen ? 'rotate(180deg)' : 'rotate(0)' }};"></i>
            </button>
            <div class="sidebar-group-content space-y-0.5 mt-1 {{ $isManajemen ? '' : 'hidden' }}"
                 data-group-content="manajemen">

                <a href="{{ $safeUrl('ketua.manajemen-anggota.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ $safeIs('ketua.manajemen-anggota.*')
                            ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                    <i class="fas fa-users w-5 text-center text-[15px]"></i> Anggota
                </a>

                <a href="{{ $safeUrl('ketua.manajemen-pengurus.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ $safeIs('ketua.manajemen-pengurus.*')
                            ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                    <i class="fas fa-user-tie w-5 text-center text-[15px]"></i> Pengurus
                </a>

                <a href="{{ $safeUrl('ketua.jabatan.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ $safeIs('ketua.jabatan.*')
                            ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                    <i class="fas fa-briefcase w-5 text-center text-[15px]"></i> Jabatan
                </a>

                <a href="{{ $safeUrl('ketua.divisi.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ $safeIs('ketua.divisi.*')
                            ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                    <i class="fas fa-sitemap w-5 text-center text-[15px]"></i> Divisi
                </a>

                <a href="{{ $safeUrl('ketua.kelompok.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ $safeIs('ketua.kelompok.*')
                            ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                    <i class="fas fa-layer-group w-5 text-center text-[15px]"></i> Kelompok
                </a>
            </div>
        </div>

        {{-- ═══ KEGIATAN ═══ --}}
        <a href="{{ $safeUrl('ketua.kegiatan') }}"
           class="flex items-center gap-3 px-3 py-2.5 mt-2 rounded-xl text-sm transition
                  {{ $isKegiatan
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-calendar-check w-5 text-center"></i>
            <span>Kegiatan & Event</span>
        </a>

        {{-- ═══ PRESENSI ═══ --}}
        <a href="{{ $safeUrl('ketua.presensi') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isPresensi
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-qrcode w-5 text-center"></i>
            <span>Presensi QR</span>
        </a>

        {{-- ═══ UANGKAS ═══ --}}
        <a href="{{ $safeUrl('ketua.keuangan') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isKeuangan
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-coins w-5 text-center"></i>
            <span>Uangkas</span>
        </a>

        {{-- ═══ LEADERPOINT ═══ --}}
        <a href="{{ $safeUrl('ketua.poin-prestasi') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isLeaderPoin
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-medal w-5 text-center"></i>
            <span>LeaderPoint</span>
        </a>

        {{-- ═══ KOMUNIKASI (persist) ═══ --}}
        <div class="sidebar-group pt-2" data-group="komunikasi">
            <button type="button"
                    data-group-toggle="komunikasi"
                    aria-expanded="{{ $isKomun ? 'true' : 'false' }}"
                    class="sidebar-group-toggle flex items-center justify-between w-full
                           px-3 py-2 rounded-lg cursor-pointer
                           hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                    Komunikasi
                </span>
                <i class="fas fa-chevron-down text-[9px] text-gray-400 transition-transform duration-200 group-chevron"
                   style="transform: {{ $isKomun ? 'rotate(180deg)' : 'rotate(0)' }};"></i>
            </button>
            <div class="sidebar-group-content space-y-0.5 mt-1 {{ $isKomun ? '' : 'hidden' }}"
                 data-group-content="komunikasi">
                <a href="{{ $safeUrl('ketua.pengumuman.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ $safeIs('ketua.pengumuman.*')
                            ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                    <i class="fas fa-bullhorn w-5 text-center text-[15px]"></i> Pengumuman
                </a>
            </div>
        </div>

        {{-- ═══ APPROVAL ═══ --}}
        <a href="{{ $safeUrl('ketua.approval') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isApproval
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-check-double w-5 text-center"></i>
            <span>Approval Center</span>
        </a>

        {{-- ═══ LAPORAN ═══ --}}
        <a href="{{ $safeUrl('ketua.laporan') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isLaporan
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>Laporan & Analitik</span>
        </a>

        {{-- ═══ NOTIFICATION ═══ --}}
        <a href="{{ $safeUrl('ketua.notifications.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isNotif
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-bell w-5 text-center"></i>
            <span class="flex-1">Notification Center</span>
            <span id="sidebar-notif-badge"
                  class="hidden min-w-[20px] h-5 px-1.5 rounded-full
                         bg-red-500 text-white text-[10px] font-bold
                         items-center justify-center">0</span>
        </a>

        {{-- ═══ PENGATURAN ═══ --}}
        <a href="{{ $safeUrl('ketua.pengaturan') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $isPengaturan
                    ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
            <i class="fas fa-cog w-5 text-center"></i>
            <span>Pengaturan</span>
        </a>

        {{-- ═══ LOGOUT ═══ --}}
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
            <button type="button" onclick="showLogoutModal(event)"
                    class="flex items-center gap-3 w-full text-left px-3 py-2.5 rounded-xl
                           text-red-600 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="text-sm font-medium">Keluar Akun</span>
            </button>
        </div>
    </nav>
</aside>

@once
@push('scripts')
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       SIDEBAR GROUP PERSISTENCE
       Simpan state accordion ke localStorage — tetap terbuka
       meskipun halaman di-refresh / pindah
       ═══════════════════════════════════════════════════════════ */
    const STORAGE_KEY = 'ketua_sidebar_groups_v1';

    function readState() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch (e) {
            return {};
        }
    }

    function writeState(state) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (e) {}
    }

    function applyGroupState(content, chevron, isOpen, toggle) {
        if (isOpen) {
            content.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        } else {
            content.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        }
    }

    function initSidebarGroups() {
        const toggles = document.querySelectorAll('[data-group-toggle]');
        if (!toggles.length) return;

        const state = readState();

        toggles.forEach(function (toggle) {
            const key = toggle.dataset.groupToggle;
            const content = document.querySelector('[data-group-content="' + key + '"]');
            const chevron = toggle.querySelector('.group-chevron');

            if (!content) return;

            /* ── Restore state — prioritas: localStorage > server-side default ── */
            let isOpen;
            if (Object.prototype.hasOwnProperty.call(state, key)) {
                isOpen = !!state[key];
            } else {
                /* Fallback ke server-side (berdasarkan route aktif) */
                isOpen = !content.classList.contains('hidden');
            }
            applyGroupState(content, chevron, isOpen, toggle);

            /* ── Click handler ── */
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const currentlyOpen = !content.classList.contains('hidden');
                const newState = !currentlyOpen;

                applyGroupState(content, chevron, newState, toggle);

                /* Simpan state */
                state[key] = newState;
                writeState(state);
            });
        });
    }

    /* ── Init ── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarGroups);
    } else {
        initSidebarGroups();
    }

    /* ── Sidebar notif badge polling ── */
    document.addEventListener('DOMContentLoaded', function () {
        const badge = document.getElementById('sidebar-notif-badge');
        if (!badge) return;

        const url = '{{ \Illuminate\Support\Facades\Route::has("ketua.notifications.unread-count") ? route("ketua.notifications.unread-count") : "" }}';
        if (!url) return;

        async function fetchUnread() {
            try {
                const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const { count } = await r.json();
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                    badge.classList.add('inline-flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('inline-flex');
                }
            } catch (e) {}
        }

        fetchUnread();
        setInterval(fetchUnread, 30000);
    });
})();
</script>
@endpush
@endonce