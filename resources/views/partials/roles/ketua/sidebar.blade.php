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

    {{-- ═══════════════ LOGO & ORG BADGE ═══════════════ --}}
    <div class="flex flex-col border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10 bg-white/95 dark:bg-gray-900/95 backdrop-blur p-4">
        <a href="{{ \App\Support\KetuaMenu::url('ketua.dashboard') }}"
           class="flex items-center gap-3 min-w-0">
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
                <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500
                             mt-0.5 tracking-widest">
                    KETUA PANEL
                </span>
            </div>
        </a>

        {{-- ═══ BADGE ORGANISASI AKTIF ═══ --}}
        @php
            $sidebarActiveOrg = \App\Services\OrganizationContext::get();
        @endphp
        @if($sidebarActiveOrg)
            <div class="mt-3 px-3 py-2 rounded-xl
                        bg-gradient-to-r from-indigo-50 to-purple-50
                        dark:from-indigo-900/20 dark:to-purple-900/20
                        border border-indigo-100 dark:border-indigo-800
                        flex items-center justify-between">
                <div class="flex items-center gap-2 truncate">
                    <div class="w-5 h-5 rounded-md bg-indigo-500 text-white
                                flex items-center justify-center shrink-0">
                        <i class="fas fa-building text-[8px]"></i>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-700 dark:text-indigo-300
                                 uppercase tracking-widest truncate">
                        {{ $sidebarActiveOrg->short_name ?? $sidebarActiveOrg->name }}
                    </span>
                </div>
                @if(auth()->user()->activeOrganizations()->count() > 1)
                    <a href="{{ route('organization.selector') }}"
                       class="text-[9px] font-bold text-indigo-500 hover:text-indigo-700 underline shrink-0">
                        Ubah
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- ═══════════════ NAV — loop dari KetuaMenu ═══════════════ --}}
    <nav class="p-3 space-y-1 pb-6" id="ketua-sidebar-nav">
        @php
            $menuItems = \App\Support\KetuaMenu::items();
        @endphp

        @foreach($menuItems as $index => $item)
            @php
                $prevItem = $index > 0 ? $menuItems[$index - 1] : null;
                $addGap   = $prevItem && $prevItem['type'] === 'group' && $item['type'] === 'link';
            @endphp

            @if($item['type'] === 'link')
                @php
                    $url      = \App\Support\KetuaMenu::url($item['route']);
                    $isActive = \App\Support\KetuaMenu::isActive($item['active'] ?? []);
                    $emph     = ! empty($item['emphasis']);

                    $activeClass = $isActive
                        ? ($emph
                            ? 'bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/40 dark:to-pink-900/30 text-purple-700 dark:text-purple-300 font-semibold shadow-sm'
                            : 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium')
                        : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20';
                @endphp
                <a href="{{ $url }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                          {{ $addGap ? 'mt-2' : '' }} {{ $activeClass }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center"></i>
                    <span class="flex-1 text-sm">{{ $item['label'] }}</span>
                    @if(! empty($item['badge']))
                        <span id="{{ $item['badge'] }}"
                              class="hidden min-w-[20px] h-5 px-1.5 rounded-full
                                     bg-red-500 text-white text-[10px] font-bold
                                     items-center justify-center">0</span>
                    @endif
                </a>
            @else
                @php
                    $isOpen = \App\Support\KetuaMenu::isActive($item['active'] ?? []);
                @endphp
                <div class="sidebar-group pt-2" data-group="{{ $item['key'] }}">
                    <button type="button"
                            data-group-toggle="{{ $item['key'] }}"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                            class="sidebar-group-toggle flex items-center justify-between w-full
                                   px-3 py-2 rounded-lg cursor-pointer
                                   hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                            {{ $item['label'] }}
                        </span>
                        <i class="fas fa-chevron-down text-[9px] text-gray-400 transition-transform duration-200 group-chevron"
                           style="transform: {{ $isOpen ? 'rotate(180deg)' : 'rotate(0)' }};"></i>
                    </button>
                    <div class="sidebar-group-content space-y-0.5 mt-1 {{ $isOpen ? '' : 'hidden' }}"
                         data-group-content="{{ $item['key'] }}">
                        @foreach($item['children'] as $child)
                            @php
                                $childUrl    = \App\Support\KetuaMenu::url($child['route']);
                                $childActive = \App\Support\KetuaMenu::isActive($child['active'] ?? []);
                            @endphp
                            <a href="{{ $childUrl }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                                      {{ $childActive
                                        ? 'bg-purple-100/70 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20' }}">
                                <i class="fas {{ $child['icon'] }} w-5 text-center text-[15px]"></i>
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

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