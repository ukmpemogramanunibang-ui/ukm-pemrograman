@php
    use Illuminate\Support\Facades\Route as RouteFacade;
    use Illuminate\Support\Facades\Cache;

    $safeRoute = function (string $name, array $params = [], string $fallback = '#') {
        try {
            return RouteFacade::has($name) ? route($name, $params) : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    };

    /* ═══════════════════════════════════════════════════════════
     |  SILENT EXPLORE PROTECTION
     ═══════════════════════════════════════════════════════════ */
    $currentUserId = (int) (auth()->id() ?? 0);
    $sessionOwner  = (int) session('owner.explore.owner_id', 0);
    $sessionFlag   = (bool) session('owner.explore.active', false);
    $exploreRole   = (string) session('owner.explore.role_slug', '');
    $exploreToken  = (string) session('owner.explore.token', '');
    $exploreUserId = (int) session('owner.explore.user_id', 0);

    $sessionFingerprint = (string) session('owner.explore.fingerprint', '');
    $currentFingerprint = hash('sha256', session()->getId() . '|' . $currentUserId);

    $isExploring = $sessionFlag
        && $currentUserId > 0
        && $sessionOwner === $currentUserId
        && $exploreRole !== ''
        && $exploreToken !== ''
        && $exploreUserId > 0
        && ($sessionFingerprint === '' || $sessionFingerprint === $currentFingerprint);

    /* ═══════════════════════════════════════════════════════════
     |  RESOLVE USER
     ═══════════════════════════════════════════════════════════ */
    $u = null;
    try {
        if ($isExploring) {
            $u = \App\Models\User::with('level:id,name')
                ->select('id', 'name', 'nim', 'avatar_url', 'level_id', 'total_points')
                ->find($exploreUserId);
        }
        $u ??= auth()->user();
    } catch (\Throwable $e) {
        $u = auth()->user();
    }

    if (! $u) return;

    $userName      = $u->name ?: 'User';
    $userInitial   = strtoupper(mb_substr($userName, 0, 1));
    $userAvatar    = (string) ($u->avatar_url ?? '');
    $userAvatarUrl = $userAvatar !== ''
        ? (\Illuminate\Support\Str::startsWith($userAvatar, ['http://', 'https://'])
            ? $userAvatar
            : asset('storage/' . $userAvatar))
        : null;

    $roleSlug  = $isExploring ? $exploreRole : 'ketua';
    $roleLabel = ucfirst($roleSlug);
    $points    = (int) ($u->total_points ?? 0);

    /* ═══════════════════════════════════════════════════════════
     |  ROUTE RESOLVER
     ═══════════════════════════════════════════════════════════ */
    $routeName = function (string $key) use ($isExploring): string {
        if (! $isExploring) return 'ketua.' . $key;

        static $map = [
            'dashboard'     => 'owner.impersonate.dashboard',
            'profil'        => 'owner.impersonate.profile',
            'profile'       => 'owner.impersonate.profile',
            'pengaturan'    => 'owner.impersonate.settings',
            'settings'      => 'owner.impersonate.settings',
            'notifications' => 'owner.impersonate.notifications',
            'jadwal'        => 'owner.impersonate.jadwal',
            'chatting'      => 'owner.impersonate.chatting',
            'poin-prestasi' => 'owner.impersonate.leaderpoint',
            'leaderpoint'   => 'owner.impersonate.leaderpoint',
            'leaderboard'   => 'owner.impersonate.leaderpoint',
        ];

        return $map[$key] ?? 'owner.impersonate.dashboard';
    };

    $routeParams = $isExploring
        ? ['roleSlug' => $exploreRole, 'impersonationToken' => $exploreToken]
        : [];

    /* ═══════════════════════════════════════════════════════════
     |  BREADCRUMB
     ═══════════════════════════════════════════════════════════ */
    $currentRouteName = (string) (request()->route()?->getName() ?? 'ketua.dashboard');
    $cleaned  = preg_replace('/^(ketua|owner\.impersonate|owner)\./', '', $currentRouteName);
    $segments = array_values(array_filter(explode('.', (string) $cleaned)));
    $labelFor = fn ($s) => ucwords(str_replace(['-', '_'], ' ', (string) $s));
@endphp

<header id="topbar"
        role="banner"
        class="sticky top-0 z-30 h-16 flex items-center justify-between gap-3
               px-4 lg:px-6 bg-white/85 dark:bg-gray-900/85 backdrop-blur-md
               border-b border-gray-200 dark:border-gray-800 shrink-0">

    {{-- ═══ KIRI: toggle + breadcrumb ═══ --}}
    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
        <button id="sidebar-toggle" type="button"
                aria-label="Toggle sidebar"
                aria-controls="sidebar"
                aria-expanded="true"
                class="relative w-10 h-10 shrink-0 rounded-xl
                       text-gray-600 dark:text-gray-300
                       hover:bg-purple-50 dark:hover:bg-purple-900/30
                       hover:text-purple-600 dark:hover:text-purple-400
                       active:scale-95 transition">
            <i class="fas fa-bars text-base" aria-hidden="true"></i>
        </button>

        <nav class="hidden sm:flex items-center gap-2 text-sm min-w-0" aria-label="Breadcrumb">
            <a href="{{ $safeRoute('ketua.dashboard', [], url('/')) }}"
               class="shrink-0 text-gray-400 hover:text-purple-600 transition">
                <i class="fas fa-home text-xs" aria-hidden="true"></i>
            </a>
            @foreach($segments as $seg)
                <i class="fas fa-chevron-right text-[8px] text-gray-300 dark:text-gray-600 shrink-0"></i>
                <span class="text-gray-600 dark:text-gray-300 font-medium truncate">
                    {{ $labelFor($seg) }}
                </span>
            @endforeach
        </nav>
    </div>

    {{-- ═══ KANAN: actions + profile ═══ --}}
    <div class="flex items-center gap-1.5 shrink-0"
         x-data="notifCenter()"
         x-init="init()">

        {{-- Search --}}
        <div class="hidden md:flex relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="search" placeholder="Cari..." aria-label="Cari"
                   class="w-44 lg:w-64 pl-9 pr-3 py-2 text-sm rounded-lg
                          bg-gray-100 dark:bg-gray-800 border border-transparent
                          focus:border-purple-400 focus:bg-white dark:focus:bg-gray-800
                          text-gray-700 dark:text-gray-200 placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition">
        </div>

        {{-- Dark mode toggle --}}
        <button id="dark-toggle" type="button" aria-label="Toggle tema"
                class="w-9 h-9 rounded-lg flex items-center justify-center
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-moon text-sm dark:hidden"></i>
            <i class="fas fa-sun text-sm hidden dark:inline"></i>
        </button>

        {{-- ═══ NOTIFICATION CENTER ═══ --}}
        <div class="relative">
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-label="Notifikasi"
                    class="relative w-9 h-9 rounded-lg flex items-center justify-center
                           text-gray-500 dark:text-gray-400
                           hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-bell text-sm"></i>

                {{-- Badge unread --}}
                <span x-show="unread > 0"
                      x-transition:enter="transition duration-200"
                      x-transition:enter-start="scale-0"
                      x-transition:enter-end="scale-100"
                      x-text="unread > 99 ? '99+' : unread"
                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full
                             bg-red-500 text-white text-[10px] font-bold leading-none
                             flex items-center justify-center ring-2 ring-white dark:ring-gray-900"
                      style="display: none;"></span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-96 max-w-[calc(100vw-1rem)] z-50
                        rounded-2xl bg-white dark:bg-gray-900
                        shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800 overflow-hidden"
                 style="display: none;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3
                            border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Notifikasi</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            <span x-text="unread"></span> belum dibaca
                        </p>
                    </div>
                    <button type="button"
                            @click="markAll()"
                            :disabled="unread === 0"
                            :class="unread === 0 ? 'opacity-40 cursor-not-allowed' : ''"
                            class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                        <i class="fas fa-check-double"></i> Tandai semua
                    </button>
                </div>

                {{-- List --}}
                <div class="max-h-96 overflow-y-auto">
                    {{-- Loading skeleton --}}
                    <template x-if="loading">
                        <div class="p-3 space-y-2">
                            <template x-for="i in 3" :key="i">
                                <div class="flex items-start gap-3 p-2 animate-pulse">
                                    <div class="w-9 h-9 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Empty --}}
                    <template x-if="!loading && items.length === 0">
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl
                                        bg-gradient-to-br from-purple-100 to-pink-100
                                        dark:from-purple-900/30 dark:to-pink-900/30
                                        flex items-center justify-center">
                                <i class="fas fa-inbox text-2xl text-purple-500"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Semua sudah beres!
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Belum ada notifikasi baru
                            </p>
                        </div>
                    </template>

                    {{-- Items --}}
                    <template x-for="n in items" :key="n.id">
                        <a :href="n.action_url || '#'"
                           @click.prevent="openItem(n)"
                           class="flex items-start gap-3 px-4 py-3
                                  border-b border-gray-100 dark:border-gray-800 last:border-0
                                  transition cursor-pointer"
                           :class="n.is_unread
                               ? 'bg-purple-50/50 dark:bg-purple-900/10 hover:bg-purple-50'
                               : 'hover:bg-gray-50 dark:hover:bg-gray-800'">

                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                 :class="{
                                     'bg-blue-500/15 text-blue-500':       n.type === 'info',
                                     'bg-emerald-500/15 text-emerald-500': n.type === 'success',
                                     'bg-amber-500/15 text-amber-500':     n.type === 'warning',
                                     'bg-red-500/15 text-red-500':         n.type === 'danger',
                                 }">
                                <i class="fas text-sm" :class="n.icon"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate"
                                   x-text="n.title"></p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2"
                                   x-text="n.message"></p>
                                <p class="text-[10px] text-gray-400 mt-1" x-text="n.time"></p>
                            </div>

                            <span x-show="n.is_unread"
                                  class="w-2 h-2 rounded-full bg-purple-500 mt-2 flex-shrink-0"></span>
                        </a>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ $safeRoute('ketua.notifications.index', [], '#') }}"
                       class="block text-center py-2 rounded-lg text-xs font-bold
                              text-purple-600 dark:text-purple-400
                              hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                        Lihat Semua Notifikasi →
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══ PROFILE DROPDOWN ═══ --}}
        <div class="relative group">
            <button type="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                    class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg
                           hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <div class="w-8 h-8 rounded-full overflow-hidden shrink-0
                            flex items-center justify-center
                            text-white font-bold text-xs ring-2
                            {{ $isExploring
                                ? 'bg-gradient-to-br from-indigo-500 to-purple-500 ring-indigo-300 dark:ring-indigo-700'
                                : 'bg-gradient-to-br from-purple-500 to-pink-500 ring-white dark:ring-gray-900' }}">
                    @if($userAvatarUrl)
                        <img src="{{ $userAvatarUrl }}" alt="" loading="lazy"
                             class="w-full h-full object-cover">
                    @else
                        {{ $userInitial }}
                    @endif
                </div>

                <div class="hidden sm:flex flex-col items-start leading-tight min-w-0 max-w-[140px]">
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate w-full">
                        {{ $userName }}
                    </span>
                    <span class="text-[10px] font-medium truncate w-full
                                 {{ $isExploring
                                    ? 'text-indigo-600 dark:text-indigo-400'
                                    : 'text-purple-600 dark:text-purple-400' }}">
                        {{ $isExploring ? 'Jelajah · ' . $roleLabel : 'Ketua' }}
                    </span>
                </div>

                <i class="fas fa-chevron-down text-[9px] text-gray-400
                          transition-transform group-hover:rotate-180"></i>
            </button>

            {{-- Dropdown --}}
            <div role="menu"
                 class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-1rem)] origin-top-right
                        rounded-2xl bg-white dark:bg-gray-900
                        shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800
                        opacity-0 invisible pointer-events-none
                        group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto
                        group-focus-within:opacity-100 group-focus-within:visible group-focus-within:pointer-events-auto
                        transition-all duration-200 ease-out
                        translate-y-1 group-hover:translate-y-0
                        z-50">

                {{-- Header --}}
                <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full overflow-hidden shrink-0
                                    flex items-center justify-center text-white font-bold
                                    {{ $isExploring
                                        ? 'bg-gradient-to-br from-indigo-500 to-purple-500'
                                        : 'bg-gradient-to-br from-purple-500 to-pink-500' }}">
                            @if($userAvatarUrl)
                                <img src="{{ $userAvatarUrl }}" alt="" class="w-full h-full object-cover">
                            @else
                                {{ $userInitial }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">
                                {{ $userName }}
                            </p>
                            <p class="text-[11px] font-medium truncate
                                      {{ $isExploring
                                        ? 'text-indigo-600 dark:text-indigo-400'
                                        : 'text-purple-600 dark:text-purple-400' }}">
                                {{ $isExploring ? 'Menjelajah · ' . $roleLabel : 'Ketua • Aktif' }}
                            </p>
                        </div>
                    </div>

                    @unless($isExploring)
                        <div class="mt-3 flex items-center justify-center gap-1
                                    text-[11px] text-gray-500 dark:text-gray-400">
                            <i class="fas fa-star text-amber-400"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">
                                {{ number_format($points, 0, ',', '.') }}
                            </span>
                            <span>LeaderPoint</span>
                        </div>
                    @endunless
                </div>

                {{-- Menu --}}
                <div class="p-2" role="group">
                    @php
                        $menuItems = [
                            ['key' => 'pengaturan',    'icon' => 'fa-user',       'color' => 'purple', 'label' => 'Pengaturan & Profil'],
                            ['key' => 'poin-prestasi', 'icon' => 'fa-trophy',     'color' => 'amber',  'label' => 'Leaderboard'],
                            ['key' => 'jadwal',        'icon' => 'fa-calendar',   'color' => 'blue',   'label' => 'Jadwal Saya'],
                            ['key' => 'notifications', 'icon' => 'fa-bell',       'color' => 'pink',   'label' => 'Notifikasi'],
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        <a href="{{ $safeRoute($routeName($item['key']), $routeParams, '#') }}"
                           role="menuitem"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                                  text-gray-700 dark:text-gray-300
                                  hover:bg-purple-50 dark:hover:bg-purple-900/20
                                  transition-colors">
                            <i class="fas {{ $item['icon'] }} w-4 text-{{ $item['color'] }}-500"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                    <button type="button"
                            onclick="showLogoutModal(event)"
                            class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm
                                   text-red-600 dark:text-red-400
                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Keluar Akun</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

@once
@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════════
   NOTIF CENTER — Global (dipanggil oleh x-data="notifCenter()")
   ═══════════════════════════════════════════════════════════════ */
function notifCenter() {
    return {
        open: false,
        loading: true,
        unread: 0,
        items: [],
        pollTimer: null,
        isFetching: false,

        async init() {
            await this.refresh();
            // Polling tiap 30 detik (hanya fetch, tidak buka dropdown)
            this.pollTimer = setInterval(() => this.refresh(true), 30000);

            // Cleanup saat halaman di-unload
            window.addEventListener('beforeunload', () => {
                if (this.pollTimer) clearInterval(this.pollTimer);
            });
        },

        async refresh(silent = false) {
            if (this.isFetching) return;
            this.isFetching = true;
            if (!silent) this.loading = this.items.length === 0;

            try {
                const r = await fetch('{{ route("ketua.notifications.latest") }}', {
                    headers: { 'Accept': 'application/json' },
                });
                if (!r.ok) throw new Error();
                const data = await r.json();
                this.items  = data.items || [];
                this.unread = data.unread || 0;

                // Update sidebar badge juga
                const sidebarBadge = document.getElementById('sidebar-notif-badge');
                if (sidebarBadge) {
                    if (this.unread > 0) {
                        sidebarBadge.textContent = this.unread > 99 ? '99+' : this.unread;
                        sidebarBadge.classList.remove('hidden');
                        sidebarBadge.classList.add('inline-flex');
                    } else {
                        sidebarBadge.classList.add('hidden');
                        sidebarBadge.classList.remove('inline-flex');
                    }
                }
            } catch (e) {
                // silent fail
            } finally {
                this.loading = false;
                this.isFetching = false;
            }
        },

        async openItem(n) {
            if (n.is_unread) {
                try {
                    await fetch('/ketua/notifications/' + n.id + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    n.is_unread = false;
                    this.unread = Math.max(0, this.unread - 1);

                    // Update sidebar badge
                    const sidebarBadge = document.getElementById('sidebar-notif-badge');
                    if (sidebarBadge) {
                        if (this.unread > 0) {
                            sidebarBadge.textContent = this.unread;
                            sidebarBadge.classList.remove('hidden');
                            sidebarBadge.classList.add('inline-flex');
                        } else {
                            sidebarBadge.classList.add('hidden');
                            sidebarBadge.classList.remove('inline-flex');
                        }
                    }
                } catch (e) {}
            }
            if (n.action_url) window.location.href = n.action_url;
        },

        async markAll() {
            if (this.unread === 0) return;
            try {
                await fetch('{{ route("ketua.notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                this.unread = 0;
                this.items = this.items.map(i => ({ ...i, is_unread: false }));

                const sidebarBadge = document.getElementById('sidebar-notif-badge');
                if (sidebarBadge) {
                    sidebarBadge.classList.add('hidden');
                    sidebarBadge.classList.remove('inline-flex');
                }
            } catch (e) {}
        },
    };
}
</script>
@endpush
@endonce