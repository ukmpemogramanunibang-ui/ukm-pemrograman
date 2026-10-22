@php
    use Illuminate\Support\Facades\Route as RouteFacade;
    use Illuminate\Support\Str;

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
    $currentUserId      = (int) (auth()->id() ?? 0);
    $sessionOwner       = (int) session('owner.explore.owner_id', 0);
    $sessionFlag        = (bool) session('owner.explore.active', false);
    $exploreRole        = (string) session('owner.explore.role_slug', '');
    $exploreToken       = (string) session('owner.explore.token', '');
    $exploreUserId      = (int) session('owner.explore.user_id', 0);
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
        ? (Str::startsWith($userAvatar, ['http://', 'https://'])
            ? $userAvatar
            : asset('storage/' . $userAvatar))
        : null;

    $roleSlug  = $isExploring ? $exploreRole : 'ketua';
    $roleLabel = ucfirst($roleSlug);
    $points    = (int) ($u->total_points ?? 0);

    /* ═══════════════════════════════════════════════════════════
     |  ROUTE RESOLVER — untuk profile dropdown (bukan nav)
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
    $routeParams = $isExploring ? ['roleSlug' => $exploreRole, 'impersonationToken' => $exploreToken] : [];

    /* ═══════════════════════════════════════════════════════════
     |  BREADCRUMB
     ═══════════════════════════════════════════════════════════ */
    $currentRouteName = (string) (request()->route()?->getName() ?? 'ketua.dashboard');
    $cleaned  = preg_replace('/^(ketua|owner\.impersonate|owner)\./', '', $currentRouteName);
    $segments = array_values(array_filter(explode('.', (string) $cleaned)));
    $segments = array_values(array_filter($segments, fn ($s) =>
        ! in_array($s, ['index','show','edit','create','store','update','destroy'], true)
    ));
    $labelFor = fn ($s) => ucwords(str_replace(['-', '_'], ' ', (string) $s));

    /* ═══════════════════════════════════════════════════════════
     |  SEARCH INDEX — single source of truth
     ═══════════════════════════════════════════════════════════ */
    $searchIndex = \App\Support\KetuaMenu::searchable();

    /* ═══════════════════════════════════════════════════════════
     |  ORG INFO
     ═══════════════════════════════════════════════════════════ */
    try {
        $myOrgs    = auth()->user()->activeOrganizations()->get();
        $activeOrg = \App\Services\OrganizationContext::get();
    } catch (\Throwable $e) {
        $myOrgs    = collect();
        $activeOrg = null;
    }
@endphp

{{-- WAJIB: CSS untuk menyembunyikan elemen Alpine sebelum di-init --}}
<style>
    [x-cloak] { display: none !important; }
</style>

<header id="topbar"
        role="banner"
        class="sticky top-0 z-50 h-16 flex items-center justify-between gap-3
               px-4 lg:px-6 bg-white/85 dark:bg-gray-900/85 backdrop-blur-md
               border-b border-gray-200 dark:border-gray-800 shrink-0">

    {{-- ═════════════════════════════════════════════════════════
         KIRI — Toggle + Breadcrumb
         ═════════════════════════════════════════════════════════ --}}
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

        <nav class="hidden sm:flex items-center gap-1.5 text-sm min-w-0" aria-label="Breadcrumb">
            <a href="{{ $safeRoute('ketua.dashboard', [], url('/')) }}"
               class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg
                      text-gray-400 hover:text-purple-600 hover:bg-purple-50
                      dark:hover:bg-purple-900/30 transition">
                <i class="fas fa-home text-xs" aria-hidden="true"></i>
            </a>
            @foreach($segments as $seg)
                <i class="fas fa-chevron-right text-[8px] text-gray-300 dark:text-gray-600 shrink-0" aria-hidden="true"></i>
                <span class="text-gray-600 dark:text-gray-300 font-medium truncate max-w-[160px]">
                    {{ $labelFor($seg) }}
                </span>
            @endforeach
        </nav>
    </div>

    {{-- ═════════════════════════════════════════════════════════
         KANAN — Search, Org, Theme, Notif, Profile
         ═════════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-1.5 shrink-0">

        {{-- ═══ SEARCH / COMMAND PALETTE ═══ --}}
        <div x-data="menuSearch" class="hidden md:block relative">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2
                          text-gray-400 text-xs pointer-events-none"></i>
                <input x-ref="searchInput"
                       type="text"
                       x-model="query"
                       @focus="open = true"
                       @input="open = true; activeIndex = 0"
                       @keydown="onKeydown($event)"
                       placeholder="Cari menu..."
                       autocomplete="off"
                       aria-label="Cari menu navigasi"
                       aria-haspopup="listbox"
                       :aria-expanded="open"
                       class="w-48 lg:w-72 pl-9 pr-16 py-2 text-sm rounded-xl
                              bg-gray-100 dark:bg-gray-800/60
                              border border-transparent
                              focus:border-purple-400 focus:bg-white dark:focus:bg-gray-800
                              text-gray-700 dark:text-gray-200 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition">
                <kbd class="hidden lg:inline-flex absolute right-2 top-1/2 -translate-y-1/2
                            items-center gap-0.5 px-1.5 py-0.5 rounded-md
                            text-[10px] font-semibold text-gray-400 dark:text-gray-500
                            bg-white dark:bg-gray-700/60
                            border border-gray-200 dark:border-gray-600 pointer-events-none">
                    <span x-text="isMac ? '⌘' : 'Ctrl'"></span><span>K</span>
                </kbd>
            </div>

            {{-- Dropdown hasil --}}
            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 @click.outside="close()"
                 class="absolute right-0 mt-2 w-[24rem] max-w-[calc(100vw-1rem)] z-50
                        rounded-2xl bg-white dark:bg-gray-900
                        shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800 overflow-hidden"
                 role="listbox">

                <div class="flex items-center justify-between px-4 py-2.5
                            border-b border-gray-100 dark:border-gray-800">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <span x-show="query.length === 0">Menu Cepat</span>
                        <span x-show="query.length > 0" x-cloak>
                            <span x-text="results.length"></span> hasil
                        </span>
                    </p>
                    <span class="text-[10px] text-gray-400"
                          x-show="query.length > 0" x-cloak>
                        <kbd class="px-1 rounded bg-gray-100 dark:bg-gray-800">↵</kbd> buka
                    </span>
                </div>

                <div class="max-h-80 overflow-y-auto p-1.5">
                    <template x-if="query.length === 0">
                        <template x-for="(item, i) in allItems" :key="'default-' + i">
                            <a :href="item.url"
                               @mouseenter="activeIndex = i"
                               :class="i === activeIndex
                                   ? 'bg-purple-50 dark:bg-purple-900/20 ring-1 ring-purple-200 dark:ring-purple-800'
                                   : 'hover:bg-gray-50 dark:hover:bg-gray-800/60'"
                               class="flex items-center gap-3 px-2.5 py-2 rounded-xl text-left transition">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                             bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    <i class="fas text-xs" :class="item.icon"></i>
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white truncate"
                                          x-text="item.label"></span>
                                    <span class="block text-[10px] text-gray-400" x-text="item.section"></span>
                                </span>
                                <i class="fas fa-arrow-right text-[10px] text-purple-500"
                                   :class="i === activeIndex ? 'opacity-100' : 'opacity-0'"></i>
                            </a>
                        </template>
                    </template>

                    <template x-if="query.length > 0 && results.length === 0">
                        <div class="py-10 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-xl
                                        bg-gray-100 dark:bg-gray-800
                                        flex items-center justify-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Tidak ditemukan
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Coba kata kunci lain
                            </p>
                        </div>
                    </template>

                    <template x-if="query.length > 0 && results.length > 0">
                        <template x-for="(item, i) in results" :key="'r-' + i">
                            <a :href="item.url"
                               @mouseenter="activeIndex = i"
                               :class="i === activeIndex
                                   ? 'bg-purple-50 dark:bg-purple-900/20 ring-1 ring-purple-200 dark:ring-purple-800'
                                   : 'hover:bg-gray-50 dark:hover:bg-gray-800/60'"
                               class="flex items-center gap-3 px-2.5 py-2 rounded-xl text-left transition">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                             bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    <i class="fas text-xs" :class="item.icon"></i>
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white truncate"
                                          x-html="highlight(item.label)"></span>
                                    <span class="block text-[10px] text-gray-400" x-text="item.section"></span>
                                </span>
                                <i class="fas fa-arrow-right text-[10px] text-purple-500"
                                   :class="i === activeIndex ? 'opacity-100' : 'opacity-0'"></i>
                            </a>
                        </template>
                    </template>
                </div>

                <div class="px-3 py-2 border-t border-gray-100 dark:border-gray-800
                            flex items-center justify-between text-[10px] text-gray-400">
                    <span class="flex items-center gap-1"><kbd class="px-1 rounded bg-gray-100 dark:bg-gray-800">↑↓</kbd> navigasi</span>
                    <span class="flex items-center gap-1"><kbd class="px-1 rounded bg-gray-100 dark:bg-gray-800">↵</kbd> buka</span>
                    <span class="flex items-center gap-1"><kbd class="px-1 rounded bg-gray-100 dark:bg-gray-800">Esc</kbd> tutup</span>
                </div>
            </div>
        </div>

        {{-- ═══ ORG SWITCHER ═══ --}}
        @if($myOrgs->count() > 1)
        <div x-data="{ open: false }" @click.outside="open = false" class="relative hidden sm:block">
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-haspopup="true"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-xl
                           bg-purple-50 dark:bg-purple-900/20
                           border border-purple-200 dark:border-purple-800
                           hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                <i class="fas fa-building text-purple-600 dark:text-purple-400 text-xs"></i>
                <span class="text-xs font-bold text-purple-700 dark:text-purple-300 truncate max-w-[110px]">
                    {{ $activeOrg ? $activeOrg->short_name : 'Pilih Org' }}
                </span>
                <i class="fas fa-chevron-down text-[9px] text-purple-500 transition-transform"
                   :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-gray-900
                        shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800 z-50 overflow-hidden">
                <div class="p-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2 py-1 mb-1">
                        Pindah Organisasi
                    </p>
                    @foreach($myOrgs as $org)
                        <form method="POST" action="{{ route('organization.switch', $org) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium
                                           hover:bg-gray-50 dark:hover:bg-gray-800
                                           flex items-center justify-between transition
                                           {{ $activeOrg && $activeOrg->id === $org->id
                                                ? 'text-purple-600 bg-purple-50/60 dark:bg-purple-900/20'
                                                : 'text-gray-700 dark:text-gray-300' }}">
                                <span class="truncate pr-2">{{ $org->name }}</span>
                                @if($activeOrg && $activeOrg->id === $org->id)
                                    <i class="fas fa-check text-[10px] text-purple-500"></i>
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ DARK MODE ═══ --}}
        <button id="dark-toggle" type="button" aria-label="Toggle tema"
                class="w-10 h-10 rounded-xl flex items-center justify-center
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-moon text-sm dark:hidden"></i>
            <i class="fas fa-sun text-sm hidden dark:inline"></i>
        </button>

        {{-- ═══ NOTIFICATION CENTER ═══ --}}
        <div x-data="notifCenter" class="relative">
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-label="Notifikasi"
                    class="relative w-10 h-10 rounded-xl flex items-center justify-center
                           text-gray-500 dark:text-gray-400
                           hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-bell text-sm"></i>
                <span x-show="unread > 0" x-cloak
                      x-text="unread > 99 ? '99+' : unread"
                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full
                             bg-red-500 text-white text-[10px] font-bold leading-none
                             flex items-center justify-center ring-2 ring-white dark:ring-gray-900"></span>
            </button>

            <div x-show="open" x-cloak
                 @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute right-0 mt-2 w-96 max-w-[calc(100vw-1rem)] z-50
                        rounded-2xl bg-white dark:bg-gray-900
                        shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800 overflow-hidden">

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

                <div class="max-h-96 overflow-y-auto">
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

        {{-- ═══ PROFILE ═══ --}}
        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-haspopup="true"
                    class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl
                           hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <div class="w-8 h-8 rounded-full overflow-hidden shrink-0
                            flex items-center justify-center text-white font-bold text-xs ring-2
                            {{ $isExploring
                                ? 'bg-gradient-to-br from-indigo-500 to-purple-500 ring-indigo-300 dark:ring-indigo-700'
                                : 'bg-gradient-to-br from-purple-500 to-pink-500 ring-white dark:ring-gray-900' }}">
                    @if($userAvatarUrl)
                        <img src="{{ $userAvatarUrl }}" alt="" loading="lazy" class="w-full h-full object-cover">
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

                <i class="fas fa-chevron-down text-[9px] text-gray-400 transition-transform"
                   :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak role="menu"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-1rem)] origin-top-right
                        rounded-2xl bg-white dark:bg-gray-900
                        shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800 z-50 overflow-hidden">

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
                                    text-[11px] text-gray-500 dark:text-gray-400
                                    bg-amber-50 dark:bg-amber-900/20 py-1.5 rounded-lg">
                            <i class="fas fa-star text-amber-400"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">
                                {{ number_format($points, 0, ',', '.') }}
                            </span>
                            <span>LeaderPoint</span>
                        </div>
                    @endunless
                </div>

                <div class="p-2" role="group">
                    @php
                        $menuItems = [
                            ['key' => 'pengaturan',    'icon' => 'fa-user',     'color' => 'purple', 'label' => 'Pengaturan & Profil'],
                            ['key' => 'poin-prestasi', 'icon' => 'fa-trophy',   'color' => 'amber',  'label' => 'Leaderboard'],
                            ['key' => 'jadwal',        'icon' => 'fa-calendar', 'color' => 'blue',   'label' => 'Jadwal Saya'],
                            ['key' => 'notifications', 'icon' => 'fa-bell',     'color' => 'pink',   'label' => 'Notifikasi'],
                        ];
                    @endphp
                    @foreach($menuItems as $item)
                        <a href="{{ $safeRoute($routeName($item['key']), $routeParams, '#') }}"
                           role="menuitem"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                                  text-gray-700 dark:text-gray-300
                                  hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                            <i class="fas {{ $item['icon'] }} w-4 text-{{ $item['color'] }}-500"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

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

<meta name="csrf-token" content="{{ csrf_token() }}">

@once
@push('scripts')
<script>
    /* ═══════════════════════════════════════════════════════════════
       GLOBAL — CSRF & MENU INDEX
       ═══════════════════════════════════════════════════════════════ */
    window.__csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    window.KETUA_MENU_INDEX = @json($searchIndex);

    /* ═══════════════════════════════════════════════════════════════
       ALPINE COMPONENTS (Standar Livewire v3)
       Gunakan alpine:init agar aman dari race condition script
       ═══════════════════════════════════════════════════════════════ */
    document.addEventListener('alpine:init', () => {
        
        /* ── MENU SEARCH ── */
        Alpine.data('menuSearch', () => ({
            open: false,
            query: '',
            activeIndex: 0,
            allItems: window.KETUA_MENU_INDEX || [],
            isMac: /Mac|iPhone|iPad/.test(navigator.platform),

            init() {
                window.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        this.open = true;
                        this.$nextTick(() => this.$refs.searchInput?.focus());
                    }
                });
            },

            get results() {
                const q = this.query.trim().toLowerCase();
                if (!q) return [];

                const scored = this.allItems.map(item => {
                    const label = item.label.toLowerCase();
                    const kw    = (item.keywords || '').toLowerCase();
                    let score = 0;

                    if (label === q)                                  score = 100;
                    else if (label.startsWith(q))                     score = 90;
                    else if (label.includes(q))                       score = 80;
                    else if (kw.split(/\s+/).some(k => k === q))      score = 70;
                    else if (kw.includes(q))                          score = 50;

                    return score > 0 ? { ...item, _score: score } : null;
                }).filter(Boolean);

                return scored.sort((a, b) => b._score - a._score).slice(0, 8);
            },

            onKeydown(e) {
                const items = this.query.length === 0 ? this.allItems : this.results;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.activeIndex = Math.min(items.length - 1, this.activeIndex + 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.activeIndex = Math.max(0, this.activeIndex - 1);
                } else if (e.key === 'Enter') {
                    const target = items[this.activeIndex];
                    if (target?.url) {
                        e.preventDefault();
                        window.location.href = target.url;
                    }
                } else if (e.key === 'Escape') {
                    this.close();
                }
            },

            close() {
                this.open = false;
                this.query = '';
                this.activeIndex = 0;
            },

            highlight(text) {
                const esc = String(text).replace(/[&<>"']/g, c => (
                    { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]
                ));
                const q = this.query.trim();
                if (!q) return esc;
                const regex = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig');
                return esc.replace(regex,
                    '<mark class="bg-amber-200/70 dark:bg-amber-500/30 text-inherit rounded px-0.5">$1</mark>');
            },
        }));

        /* ── NOTIFICATION CENTER ── */
        Alpine.data('notifCenter', () => ({
            open: false,
            loading: true,
            unread: 0,
            items: [],
            pollTimer: null,
            isFetching: false,
            latestUrl: '{{ $safeRoute("ketua.notifications.latest", [], "") }}',
            markAllUrl: '{{ $safeRoute("ketua.notifications.mark-all-read", [], "") }}',
            readBase: '{{ url("/ketua/notifications") }}',

            init() {
                this.refresh();
                if (this.latestUrl) {
                    this.pollTimer = setInterval(() => this.refresh(true), 30000);
                }
                window.addEventListener('beforeunload', () => {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                });
            },

            async refresh(silent = false) {
                if (!this.latestUrl || this.isFetching) {
                    this.loading = false;
                    return;
                }
                this.isFetching = true;
                if (!silent) this.loading = this.items.length === 0;

                try {
                    const r = await fetch(this.latestUrl, { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) throw new Error();
                    const data = await r.json();
                    this.items  = data.items || [];
                    this.unread = data.unread || 0;
                    this.syncSidebarBadge();
                } catch (e) {
                    /* silent */
                } finally {
                    this.loading = false;
                    this.isFetching = false;
                }
            },

            async openItem(n) {
                if (n.is_unread) {
                    try {
                        await fetch(this.readBase + '/' + n.id + '/read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.__csrf,
                                'Accept': 'application/json',
                            },
                        });
                        n.is_unread = false;
                        this.unread = Math.max(0, this.unread - 1);
                        this.syncSidebarBadge();
                    } catch (e) {}
                }
                if (n.action_url) window.location.href = n.action_url;
            },

            async markAll() {
                if (this.unread === 0 || !this.markAllUrl) return;
                try {
                    await fetch(this.markAllUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.__csrf,
                            'Accept': 'application/json',
                        },
                    });
                    this.unread = 0;
                    this.items = this.items.map(i => ({ ...i, is_unread: false }));
                    this.syncSidebarBadge();
                } catch (e) {}
            },

            syncSidebarBadge() {
                const badge = document.getElementById('sidebar-notif-badge');
                if (!badge) return;
                if (this.unread > 0) {
                    badge.textContent = this.unread > 99 ? '99+' : this.unread;
                    badge.classList.remove('hidden');
                    badge.classList.add('inline-flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('inline-flex');
                }
            },
        }));
    });
</script>
@endpush
@endonce