{{-- resources/views/partials/header.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;

    $current     = Route::currentRouteName();
    $isLoginPage = $current === 'login';

    /* =========================================================
     | Helper — Route aman (fallback jika route belum terdaftar)
     * ========================================================= */
    $safeRoute = function (string $name, array $params = [], string $fallback = '#') {
        if (! Route::has($name)) return $fallback;
        try { return route($name, $params); }
        catch (\Throwable $e) { return $fallback; }
    };

    /* =========================================================
     | Helper — Cek pola route aktif (mendukung wildcard ".*")
     * ========================================================= */
    $isActive = function ($patterns) use ($current) {
        foreach ((array) $patterns as $p) {
            if ($current === $p) return true;
            if (str_ends_with($p, '.*') && str_starts_with((string) $current, substr($p, 0, -1))) return true;
        }
        return false;
    };

    /* =========================================================
     | Direktori — Organisasi (7 unit) & UKM (9 unit)
     * ========================================================= */
    $organisasiChildren = [
        ['slug' => 'bem',              'label' => 'BEM',              'desc' => 'Badan Eksekutif Mahasiswa'],
        ['slug' => 'hima-informatika', 'label' => 'HIMA Informatika', 'desc' => 'Himpunan Mahasiswa Informatika'],
        ['slug' => 'hima-kebidanan',   'label' => 'HIMA Kebidanan',   'desc' => 'Himpunan Mahasiswa Kebidanan'],
        ['slug' => 'hima-keperawatan', 'label' => 'HIMA Keperawatan', 'desc' => 'Himpunan Mahasiswa Keperawatan'],
        ['slug' => 'hima-manajemen',   'label' => 'HIMA Manajemen',   'desc' => 'Himpunan Mahasiswa Manajemen'],
        ['slug' => 'hima-hukum',       'label' => 'HIMA Hukum',       'desc' => 'Himpunan Mahasiswa Hukum'],
        ['slug' => 'hima-ars',         'label' => 'HIMA ARS',         'desc' => 'Himpunan Mahasiswa ARS'],
    ];

    $ukmChildren = [
        ['slug' => 'pemrograman',   'label' => 'UKM Pemrograman',   'desc' => 'Programming & Developer Club'],
        ['slug' => 'multimedia',    'label' => 'UKM Multimedia',    'desc' => 'Desain & Multimedia Kreatif'],
        ['slug' => 'nova',          'label' => 'UKM Nova',          'desc' => 'Kewirausahaan & Bisnis'],
        ['slug' => 'badminton',     'label' => 'UKM Badminton',     'desc' => 'Olahraga Bulu Tangkis'],
        ['slug' => 'basket',        'label' => 'UKM Basket',        'desc' => 'Olahraga Bola Basket'],
        ['slug' => 'teater-teras',  'label' => 'UKM Teater Teras',  'desc' => 'Seni Pertunjukan & Teater'],
        ['slug' => 'aksara-tarana', 'label' => 'UKM Aksara Tarana', 'desc' => 'Sastra & Jurnalistik'],
        ['slug' => 'savora-padus',  'label' => 'UM Savora Padus',   'desc' => 'Paduan Suara Mahasiswa'],
        ['slug' => 'futsal',        'label' => 'UKM Futsal',        'desc' => 'Olahraga Futsal'],
    ];

    $resolveChildren = function (array $items, string $prefix, string $indexRoute) use ($safeRoute) {
        $indexUrl = $safeRoute($indexRoute);
        return array_map(function ($it) use ($prefix, $indexUrl, $safeRoute) {
            $url = $safeRoute(
                "{$prefix}.{$it['slug']}",
                [],
                $indexUrl === '#' ? '#' : $indexUrl . '#' . $it['slug']
            );
            return [
                'label'  => $it['label'],
                'desc'   => $it['desc'] ?? null,
                'url'    => $url,
                'route'  => "{$prefix}.{$it['slug']}",
                'active' => ["{$prefix}.{$it['slug']}", "{$prefix}.{$it['slug']}.*"],
            ];
        }, $items);
    };

    $navItems = [
        ['type' => 'link', 'label' => 'Beranda', 'icon' => 'fas fa-home',
         'url' => $safeRoute('landing', [], '/'), 'active' => ['landing']],

        ['type' => 'link', 'label' => 'Tentang', 'icon' => 'fas fa-circle-info',
         'url' => $safeRoute('about'), 'active' => ['about', 'about.*']],

        ['type' => 'dropdown',
         'label' => 'Organisasi', 'icon' => 'fas fa-sitemap',
         'url'   => $safeRoute('organisasi.index'),
         'active'=> ['organisasi', 'organisasi.*'],
         'badge' => count($organisasiChildren) . ' unit',
         'children' => $resolveChildren($organisasiChildren, 'organisasi', 'organisasi.index')],

        ['type' => 'dropdown',
         'label' => 'UKM', 'icon' => 'fas fa-users-rectangle',
         'url'   => $safeRoute('ukm.index'),
         'active'=> ['ukm', 'ukm.*'],
         'badge' => count($ukmChildren) . ' unit',
         'children' => $resolveChildren($ukmChildren, 'ukm', 'ukm.index')],

        ['type' => 'link', 'label' => 'Dokumentasi', 'icon' => 'fas fa-book',
         'url' => $safeRoute('dokumentasi'), 'active' => ['dokumentasi', 'dokumentasi.*']],

        ['type' => 'link', 'label' => 'Member', 'icon' => 'fas fa-users',
         'url' => $safeRoute('member'), 'active' => ['member', 'member.*']],

        ['type' => 'link', 'label' => 'Prestasi', 'icon' => 'fas fa-trophy',
         'url' => $safeRoute('prestasi'), 'active' => ['prestasi', 'prestasi.*']],

        ['type' => 'link', 'label' => 'Portofolio', 'icon' => 'fas fa-briefcase',
         'url' => $safeRoute('portfolio'), 'active' => ['portfolio', 'portfolio.*']],

        ['type' => 'link', 'label' => 'Kontak', 'icon' => 'fas fa-envelope',
         'url' => $safeRoute('kontak'), 'active' => ['kontak', 'kontak.*']],

        ['type' => 'link', 'label' => 'Ruang', 'icon' => 'fas fa-door-open',
         'url' => $safeRoute('room.public.index'), 'active' => ['room.*'], 'accent' => true],

        ['type' => 'link', 'label' => 'Cek Akun', 'icon' => 'fas fa-user-check',
         'url' => $safeRoute('account.check.index'), 'active' => ['account.*'], 'accent' => true],

        ['type' => 'link', 'label' => 'Eksplor', 'icon' => 'fas fa-compass',
         'url' => $safeRoute('explore'), 'active' => ['explore', 'explore.*'], 'accent' => true],
    ];

    $keputusanActive = str_starts_with((string) $current, 'keputusan');
    $keputusanUrl    = $safeRoute('keputusan.index');
@endphp

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- TOP BAR                                                  --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="relative z-50 border-b border-white/10 bg-midnight/90 py-2 backdrop-blur-md">
    <div class="mx-auto flex w-full max-w-[1800px] flex-wrap items-center justify-between gap-3 px-4 text-xs text-white sm:px-6 md:text-sm lg:px-10">

        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ $safeRoute('landing', [], '/') }}" class="group flex items-center gap-1.5 truncate">
                <i class="fas fa-graduation-cap text-cyan transition-transform duration-500 group-hover:rotate-12"></i>
                <span class="truncate bg-gradient-to-r from-cyan via-electric to-violet bg-clip-text font-bold text-transparent">
                    UKM Pemrograman UNICODE
                </span>
            </a>
            <span aria-hidden="true" class="hidden text-white/20 md:inline">|</span>
            <span class="hidden truncate text-gray-300/80 md:inline">Universitas Bhakti Asih — Tangerang</span>
        </div>

        <div class="flex items-center gap-3 md:gap-4">
            <button id="theme-toggle" type="button"
                    aria-label="Ubah tema gelap atau terang" aria-pressed="false"
                    class="group relative grid h-8 w-8 place-items-center rounded-full text-lg text-gray-300 transition-all duration-300 hover:bg-white/5 hover:text-yellow-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan">
                <i id="theme-icon" class="fas fa-moon transition-transform duration-500 group-hover:rotate-12"></i>
            </button>

            <a href="https://instagram.com/ukm_pemrograman" target="_blank" rel="noopener noreferrer"
               aria-label="Instagram UKM Pemrograman"
               class="text-base text-gray-300 transition-all duration-300 hover:-translate-y-0.5 hover:text-pink-400">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://github.com/ukm-pemrograman" target="_blank" rel="noopener noreferrer"
               aria-label="GitHub UKM Pemrograman"
               class="text-base text-gray-300 transition-all duration-300 hover:-translate-y-0.5 hover:text-white">
                <i class="fab fa-github"></i>
            </a>

            <span class="hidden max-w-xs truncate text-[10px] uppercase tracking-wider text-gray-400/70 xl:inline">
                Website Resmi — Universitas Bhakti Asih Tangerang
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MAIN HEADER                                              --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<header id="site-header"
        class="sticky top-0 z-40 border-b border-white/10 bg-gradient-to-r from-midnight via-deepblue to-electric shadow-2xl shadow-black/30">
    <div class="mx-auto flex w-full max-w-[1800px] items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-10">

        {{-- LOGO --}}
        <a href="{{ $safeRoute('landing', [], '/') }}" class="group flex shrink-0 items-center gap-3" aria-label="Beranda UNICODE">
            <span class="relative">
                <img src="{{ asset('images/logo_ukm3.jpeg') }}"
                     alt="Logo UKM Pemrograman UNICODE"
                     loading="eager" decoding="async"
                     class="h-11 w-11 rounded-full border-2 border-white/30 shadow-lg transition-all duration-500 group-hover:rotate-6 group-hover:scale-110 md:h-12 md:w-12">
                <span aria-hidden="true"
                      class="pointer-events-none absolute inset-0 rounded-full ring-2 ring-cyan/0 transition-all duration-500 group-hover:ring-cyan/40"></span>
            </span>
            <span class="flex flex-col leading-none">
                <span class="bg-gradient-to-r from-cyan via-electric to-violet bg-clip-text text-lg font-extrabold text-transparent drop-shadow-lg md:text-xl">
                    UNICODE
                </span>
                <span class="mt-0.5 text-[9px] tracking-wide text-white/60 md:text-[10px]">UKM Pemrograman</span>
            </span>
        </a>

        {{-- ═══════════════ NAV DESKTOP ═══════════════ --}}
        @if(!$isLoginPage)
            <nav aria-label="Navigasi utama"
                 class="hidden min-w-0 flex-1 items-center justify-center gap-0.5 text-[13px] font-medium text-white xl:flex xl:gap-1 xl:text-sm">

                @foreach($navItems as $item)
                    @if(($item['type'] ?? 'link') === 'link')
                        @php $active = $isActive($item['active']); @endphp
                        <a href="{{ $item['url'] }}"
                           @if($active) aria-current="page" @endif
                           class="group relative whitespace-nowrap rounded-lg px-2 py-2 outline-none transition-all duration-200 hover:bg-white/5 focus-visible:ring-2 focus-visible:ring-cyan xl:px-2.5">
                            <span class="flex items-center gap-1.5 transition-colors {{ $active ? 'font-semibold text-cyan-200' : (!empty($item['accent']) ? 'font-bold text-cyan-300 hover:text-cyan-200' : 'hover:text-cyan-200') }}">
                                <i class="{{ $item['icon'] }} text-[10px] transition-transform duration-300 group-hover:scale-110 xl:text-[11px]"></i>
                                {{ $item['label'] }}
                            </span>
                            <span aria-hidden="true"
                                  class="pointer-events-none absolute -bottom-0.5 left-1/2 h-0.5 -translate-x-1/2 rounded-full bg-gradient-to-r from-cyan to-electric transition-all duration-300
                                         {{ $active ? 'w-3/4 opacity-100' : 'w-0 opacity-0 group-hover:w-3/4 group-hover:opacity-100' }}"></span>
                        </a>
                    @else
                        @php
                            $parentActive = $isActive($item['active']);
                            $childActive  = false;
                            foreach ($item['children'] as $c) {
                                if ($isActive($c['active'])) { $childActive = true; break; }
                            }
                            $isOpen = $parentActive || $childActive;
                        @endphp

                        {{-- ═════════ DROPDOWN DESKTOP (FIXED) ═════════ --}}
                        <div class="group/dd relative">
                            <a href="{{ $item['url'] }}"
                               @if($isOpen) aria-current="page" @endif
                               aria-haspopup="true" aria-expanded="false"
                               class="relative flex items-center gap-1 whitespace-nowrap rounded-lg px-2 py-2 outline-none transition-all duration-200 hover:bg-white/5 focus-visible:ring-2 focus-visible:ring-cyan xl:px-2.5">
                                <span class="flex items-center gap-1.5 transition-colors {{ $isOpen ? 'font-semibold text-cyan-200' : 'hover:text-cyan-200' }}">
                                    <i class="{{ $item['icon'] }} text-[10px] xl:text-[11px]"></i>
                                    {{ $item['label'] }}
                                    <i class="fas fa-chevron-down text-[8px] opacity-70 transition-transform duration-300 group-hover/dd:rotate-180"></i>
                                </span>
                                <span aria-hidden="true"
                                      class="pointer-events-none absolute -bottom-0.5 left-1/2 h-0.5 -translate-x-1/2 rounded-full bg-gradient-to-r from-cyan to-electric transition-all duration-300
                                             {{ $isOpen ? 'w-3/4 opacity-100' : 'w-0 opacity-0 group-hover/dd:w-3/4 group-hover/dd:opacity-100' }}"></span>
                            </a>

                            {{-- ⚡ PANEL DROPDOWN — FIX UTAMA DI SINI ⚡ --}}
                            {{-- Perubahan:
                                 1. transition-all → transition-opacity (visibility berubah instan)
                                 2. + pointer-events-none / group-hover:pointer-events-auto
                                 3. duration 200 → 150ms
                            --}}
                            <div class="invisible pointer-events-none absolute left-1/2 top-full z-50 -translate-x-1/2 pt-3 opacity-0
                                        transition-opacity duration-150 ease-out
                                        group-hover/dd:visible group-hover/dd:pointer-events-auto group-hover/dd:opacity-100
                                        group-focus-within/dd:visible group-focus-within/dd:pointer-events-auto group-focus-within/dd:opacity-100">
                                <div class="w-80 origin-top overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-b from-midnight via-deepblue/95 to-midnight shadow-2xl shadow-black/50 ring-1 ring-white/5 backdrop-blur-xl">
                                    {{-- Header panel --}}
                                    <div class="flex items-center justify-between border-b border-white/10 bg-white/5 px-4 py-3">
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-cyan-300">{{ $item['label'] }}</p>
                                            <p class="mt-0.5 text-[10px] text-white/50">{{ $item['badge'] }} aktif</p>
                                        </div>
                                        <i class="{{ $item['icon'] }} text-lg text-white/20"></i>
                                    </div>

                                    {{-- Daftar anak --}}
                                    <ul class="max-h-[65vh] overflow-y-auto py-1.5">
                                        @foreach($item['children'] as $child)
                                            @php $cActive = $isActive($child['active']); @endphp
                                            <li>
                                                <a href="{{ $child['url'] }}"
                                                   @if($cActive) aria-current="page" @endif
                                                   class="group/it flex items-start gap-3 px-4 py-2.5 text-sm transition-colors
                                                          hover:bg-white/10 focus:bg-white/10 focus:outline-none
                                                          {{ $cActive ? 'bg-cyan/10 text-cyan-100' : 'text-white/85 hover:text-white' }}">
                                                    <span class="mt-1.5 grid h-1.5 w-1.5 shrink-0 place-items-center rounded-full transition-all
                                                                 {{ $cActive ? 'bg-cyan shadow-[0_0_8px_rgba(0,217,255,0.8)]' : 'bg-white/20 group-hover/it:bg-cyan/70' }}"></span>
                                                    <span class="min-w-0 flex-1">
                                                        <span class="block truncate font-medium leading-tight">{{ $child['label'] }}</span>
                                                        @if($child['desc'])
                                                            <span class="mt-0.5 block truncate text-[10px] leading-tight text-white/40">{{ $child['desc'] }}</span>
                                                        @endif
                                                    </span>
                                                    <i class="fas fa-arrow-right mt-1 text-[9px] text-cyan-300 opacity-0 transition-all duration-200 group-hover/it:translate-x-0.5 group-hover/it:opacity-100"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    {{-- Footer panel --}}
                                    <div class="border-t border-white/10 bg-black/30 px-4 py-2.5">
                                        <a href="{{ $item['url'] }}"
                                           class="group/all flex items-center justify-between text-[11px] font-semibold text-cyan-300 transition-colors hover:text-cyan-200">
                                            Lihat semua {{ $item['label'] }}
                                            <i class="fas fa-arrow-right text-[9px] transition-transform duration-300 group-hover/all:translate-x-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- MENU KEPUTUSAN --}}
                <a href="{{ $keputusanUrl }}"
                   @if($keputusanActive) aria-current="page" @endif
                   class="group relative ml-1 flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg border px-2.5 py-2 font-bold outline-none transition-all duration-300 focus-visible:ring-2 focus-visible:ring-amber-300 xl:px-3
                          {{ $keputusanActive
                              ? 'border-amber-300/60 bg-amber-400/25 text-amber-100 shadow-[0_0_20px_rgba(252,211,77,0.35)]'
                              : 'border-transparent bg-amber-400/10 text-amber-300 hover:border-amber-300/40 hover:bg-amber-400/20 hover:text-amber-200' }}">
                    <i class="fas fa-dice text-[11px] transition-transform duration-500 group-hover:rotate-180"></i>
                    Keputusan
                    <span class="rounded bg-amber-400/30 px-1.5 py-0.5 text-[9px] font-black text-amber-100">NEW</span>
                </a>
            </nav>
        @else
            <div class="hidden flex-1 xl:block">
                <a href="{{ url()->previous() ?: $safeRoute('landing', [], '/') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-white/80 transition-all hover:-translate-x-0.5 hover:text-white">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
            </div>
        @endif

        {{-- ═══════════════ KANAN: Login / Profil ═══════════════ --}}
        <div class="flex shrink-0 items-center gap-3">
            @auth
                <div class="relative hidden md:block" id="profile-wrapper">
                    <div class="flex items-center gap-1">
                        <a href="{{ Auth::user()->getDashboardRoute() }}"
                           class="flex items-center gap-2 rounded-full bg-white/10 px-2 py-1 ring-2 ring-transparent transition-all duration-300 hover:bg-white/20 hover:ring-cyan-400/50 focus:outline-none focus-visible:ring-cyan-400/70">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=32"
                                 alt="Avatar {{ Auth::user()->name }}"
                                 class="h-8 w-8 shrink-0 rounded-full border border-white/50">
                            <span class="hidden max-w-[100px] truncate text-sm font-medium text-white lg:block">
                                {{ Auth::user()->name }}
                            </span>
                        </a>
                        <button id="profile-dropdown-btn" type="button"
                                aria-haspopup="true" aria-expanded="false" aria-controls="profile-dropdown-menu"
                                aria-label="Buka menu akun"
                                class="rounded-md p-1 text-white/60 outline-none transition hover:text-white focus-visible:ring-2 focus-visible:ring-cyan">
                            <i id="profile-dropdown-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                        </button>
                    </div>

                    <div id="profile-dropdown-menu" role="menu" aria-labelledby="profile-dropdown-btn"
                         class="absolute right-0 mt-3 hidden w-60 origin-top-right scale-95 overflow-hidden rounded-xl border border-gray-200 bg-white opacity-0 shadow-2xl transition-all duration-200 ease-out dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-gray-200 bg-gray-50/80 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="mt-0.5 truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            <span class="mt-2 inline-block rounded-full bg-cyan-100 px-2.5 py-1 text-[10px] font-bold text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">
                                {{ Auth::user()->roles->pluck('name')->implode(', ') ?: 'Member' }}
                            </span>
                        </div>
                        <div class="py-1">
                            <a href="{{ Auth::user()->getDashboardRoute() }}" role="menuitem"
                               class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:bg-gray-700">
                                <i class="fas fa-tachometer-alt w-4 text-gray-400"></i> Dashboard
                            </a>
                        </div>
                        <div class="border-t border-gray-200 py-1 dark:border-gray-700">
                            <button type="button" onclick="showLogoutModal(event)" role="menuitem"
                                    class="flex w-full items-center gap-3 px-5 py-2.5 text-left text-sm text-red-600 transition-colors hover:bg-red-50 focus:bg-red-50 focus:outline-none dark:text-red-400 dark:hover:bg-red-900/20 dark:focus:bg-red-900/20">
                                <i class="fas fa-sign-out-alt w-4"></i> Keluar Akun
                            </button>
                        </div>
                    </div>
                </div>
            @else
                @if(!$isLoginPage)
                    <a href="{{ $safeRoute('login', [], '/login') }}"
                       class="group relative inline-flex items-center overflow-hidden rounded-full bg-white px-5 py-2.5 font-bold text-midnight shadow-[0_0_15px_rgba(0,217,255,0.3)] outline-none transition-all duration-300 hover:shadow-[0_0_30px_rgba(0,217,255,0.6)] focus-visible:ring-2 focus-visible:ring-cyan md:px-6">
                        <span aria-hidden="true"
                              class="absolute inset-0 -translate-x-full transform bg-gradient-to-r from-cyan to-electric transition-transform duration-500 ease-in-out group-hover:translate-x-0"></span>
                        <span class="relative z-10 flex items-center text-midnight transition-colors group-hover:text-white">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </span>
                    </a>
                @else
                    <span class="hidden text-sm font-medium text-white/70 md:inline">
                        <i class="fas fa-lock mr-1"></i> Halaman Login
                    </span>
                @endif
            @endauth

            <button id="mobile-menu-toggle" type="button"
                    aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobile-menu"
                    class="rounded-md p-1 text-2xl text-white outline-none transition-all duration-300 hover:text-cyan focus-visible:ring-2 focus-visible:ring-cyan xl:hidden">
                <i id="mobile-menu-icon" class="fas fa-bars transition-transform duration-300"></i>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MOBILE MENU                                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="mobile-menu"
         class="hidden overflow-hidden bg-gradient-to-b from-midnight to-deepblue px-4 pb-4 text-white shadow-xl xl:hidden">
        @if(!$isLoginPage)
            <nav aria-label="Navigasi mobile" class="pt-1">
                @foreach($navItems as $item)
                    @if(($item['type'] ?? 'link') === 'link')
                        @php $active = $isActive($item['active']); @endphp
                        <a href="{{ $item['url'] }}"
                           @if($active) aria-current="page" @endif
                           class="group flex items-center justify-between border-b border-white/5 px-3 py-3 transition-all duration-200 hover:bg-white/10
                                  {{ !empty($item['accent']) ? 'font-bold text-cyan-300' : ($active ? 'font-semibold text-cyan-200' : '') }}">
                            <span class="flex items-center">
                                <i class="{{ $item['icon'] }} mr-2 w-4 text-center text-xs opacity-80"></i>
                                {{ $item['label'] }}
                            </span>
                            @if($active)
                                <span class="rounded-full bg-cyan/20 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-cyan-200">aktif</span>
                            @endif
                        </a>
                    @else
                        @php
                            $parentActive = $isActive($item['active']);
                            $childActive  = false;
                            foreach ($item['children'] as $c) {
                                if ($isActive($c['active'])) { $childActive = true; break; }
                            }
                            $isOpen = $parentActive || $childActive;
                        @endphp
                        <div data-mobile-accordion class="border-b border-white/5">
                            <div class="flex items-stretch">
                                <a href="{{ $item['url'] }}"
                                   @if($isOpen) aria-current="page" @endif
                                   class="flex flex-1 items-center gap-2 px-3 py-3 transition hover:bg-white/10 {{ $isOpen ? 'font-semibold text-cyan-200' : '' }}">
                                    <i class="{{ $item['icon'] }} w-4 text-center text-xs opacity-80"></i>
                                    {{ $item['label'] }}
                                    <span class="ml-1 rounded bg-white/10 px-1.5 py-0.5 text-[9px] font-bold text-cyan-300">{{ $item['badge'] }}</span>
                                </a>
                                <button type="button" data-accordion-toggle
                                        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                                        aria-label="Buka submenu {{ $item['label'] }}"
                                        class="grid w-12 place-items-center text-white/60 outline-none transition hover:text-cyan focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-cyan">
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"
                                       style="{{ $isOpen ? 'transform: rotate(180deg);' : '' }}"></i>
                                </button>
                            </div>
                            <div data-accordion-panel class="{{ $isOpen ? '' : 'hidden' }} bg-black/25 py-1">
                                @foreach($item['children'] as $child)
                                    @php $cActive = $isActive($child['active']); @endphp
                                    <a href="{{ $child['url'] }}"
                                       @if($cActive) aria-current="page" @endif
                                       class="group flex items-start gap-3 px-8 py-2 text-sm transition-colors hover:bg-white/5
                                              {{ $cActive ? 'text-cyan-200' : 'text-white/80 hover:text-white' }}">
                                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full {{ $cActive ? 'bg-cyan shadow-[0_0_6px_rgba(0,217,255,0.8)]' : 'bg-white/30' }}"></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate">{{ $child['label'] }}</span>
                                            @if($child['desc'])
                                                <span class="block truncate text-[10px] text-white/40">{{ $child['desc'] }}</span>
                                            @endif
                                        </span>
                                    </a>
                                @endforeach
                                <a href="{{ $item['url'] }}"
                                   class="block border-t border-white/5 px-8 py-2 text-[11px] font-semibold text-cyan-300 transition hover:text-cyan-200">
                                    Lihat semua {{ $item['label'] }} →
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach

                <a href="{{ $keputusanUrl }}"
                   @if($keputusanActive) aria-current="page" @endif
                   class="mt-1 flex items-center justify-between border-b border-white/5 px-3 py-3 font-bold transition-all duration-200 hover:bg-white/10
                          {{ $keputusanActive ? 'bg-amber-400/15 text-amber-100' : 'text-amber-300' }}">
                    <span><i class="fas fa-dice mr-2"></i> Keputusan</span>
                    <span class="rounded bg-amber-400/20 px-2 py-0.5 text-[10px] font-bold text-amber-300">NEW</span>
                </a>
            </nav>
        @else
            <div class="border-b border-white/5 py-3 text-sm text-white/70">
                <a href="{{ url()->previous() ?: $safeRoute('landing', [], '/') }}"
                   class="inline-flex items-center transition hover:text-white">
                    <i class="fas fa-arrow-left mr-1.5"></i> Kembali
                </a>
            </div>
        @endif

        @auth
            <div class="mt-4 border-t border-white/20 pt-4">
                <a href="{{ Auth::user()->getDashboardRoute() }}"
                   class="block rounded-lg bg-white/10 py-2.5 text-center text-sm font-medium transition hover:bg-white/20">
                    <i class="fas fa-tachometer-alt mr-1.5 text-xs"></i> Dashboard Saya
                </a>
                <button type="button" onclick="showLogoutModal(event)"
                        class="mt-2 block w-full rounded-lg bg-red-500/20 py-2.5 text-center text-sm text-red-200 transition hover:bg-red-500/30">
                    <i class="fas fa-sign-out-alt mr-1.5 text-xs"></i> Keluar
                </button>
            </div>
        @else
            @if(!$isLoginPage)
                <div class="mt-4 border-t border-white/20 pt-4">
                    <a href="{{ $safeRoute('login', [], '/login') }}"
                       class="block rounded-lg bg-white/10 py-2.5 text-center text-sm font-medium transition hover:bg-white/20">
                        <i class="fas fa-sign-in-alt mr-1.5 text-xs"></i> Login
                    </a>
                </div>
            @endif
        @endauth
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MARQUEE                                                  --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(!$isLoginPage)
        <div class="overflow-hidden border-t border-white/10 bg-black/40 py-2 backdrop-blur-sm">
            <div class="animate-marquee whitespace-nowrap text-xs font-semibold tracking-wider text-white md:text-sm">
                🚀 Selamat datang di UKM Pemrograman UNICODE – Tempat berkembangnya inovator digital masa depan!
                &nbsp;|&nbsp; Workshop Laravel • Hackathon • Bootcamp • Seminar AI • Kolaborasi Proyek
                &nbsp;|&nbsp; Pendaftaran anggota baru dibuka hingga 30 April 2026!
            </div>
        </div>
    @endif
</header>

<style>
    @keyframes marquee {
        0%   { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee { animation: marquee 30s linear infinite; }

    @media (prefers-reduced-motion: reduce) {
        .animate-marquee { animation: none; }
    }

    :target { scroll-margin-top: 6rem; }
    #profile-dropdown-menu { scrollbar-width: thin; }

    nav [class*="max-h-"]::-webkit-scrollbar { width: 6px; }
    nav [class*="max-h-"]::-webkit-scrollbar-thumb {
        background: rgba(0, 217, 255, 0.3);
        border-radius: 3px;
    }
    nav [class*="max-h-"]::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 217, 255, 0.6);
    }
</style>

<script>
    (function () {
        'use strict';

        const storage = {
            get(k) { try { return localStorage.getItem(k); } catch { return null; } },
            set(k, v) { try { localStorage.setItem(k, v); } catch {} }
        };

        document.addEventListener('DOMContentLoaded', function () {

            /* 1. MOBILE MENU */
            const toggle     = document.getElementById('mobile-menu-toggle');
            const menu       = document.getElementById('mobile-menu');
            const toggleIcon = document.getElementById('mobile-menu-icon');

            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    const open = menu.classList.toggle('hidden') === false;
                    toggle.setAttribute('aria-expanded', String(open));
                    if (toggleIcon) {
                        toggleIcon.classList.toggle('fa-bars', !open);
                        toggleIcon.classList.toggle('fa-times', open);
                        toggleIcon.style.transform = open ? 'rotate(90deg)' : 'rotate(0deg)';
                    }
                });
            }

            /* 2. MOBILE ACCORDION */
            document.querySelectorAll('[data-accordion-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const parent = btn.closest('[data-mobile-accordion]');
                    const panel  = parent && parent.querySelector('[data-accordion-panel]');
                    const icon   = btn.querySelector('i');
                    if (!panel) return;

                    const isOpen = !panel.classList.contains('hidden');
                    panel.classList.toggle('hidden', isOpen);
                    btn.setAttribute('aria-expanded', String(!isOpen));
                    if (icon) icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            });

            /* 3. DARK MODE */
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon   = document.getElementById('theme-icon');
            const html        = document.documentElement;

            const applyTheme = (isDark) => {
                html.classList.toggle('dark', isDark);
                if (themeIcon) {
                    themeIcon.classList.toggle('fa-moon', !isDark);
                    themeIcon.classList.toggle('fa-sun', isDark);
                }
                if (themeToggle) themeToggle.setAttribute('aria-pressed', String(isDark));
            };

            const storedTheme = storage.get('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(storedTheme ? storedTheme === 'dark' : prefersDark);

            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    const isDark = !html.classList.contains('dark');
                    applyTheme(isDark);
                    storage.set('theme', isDark ? 'dark' : 'light');
                });
            }

            /* 4. PROFILE DROPDOWN */
            const dropdownBtn  = document.getElementById('profile-dropdown-btn');
            const dropdownMenu = document.getElementById('profile-dropdown-menu');
            const dropdownIcon = document.getElementById('profile-dropdown-icon');
            const wrapper      = document.getElementById('profile-wrapper');

            if (dropdownBtn && dropdownMenu) {
                const openDropdown = () => {
                    dropdownMenu.classList.remove('hidden');
                    void dropdownMenu.offsetWidth;
                    dropdownMenu.classList.remove('scale-95', 'opacity-0');
                    dropdownMenu.classList.add('scale-100', 'opacity-100');
                    dropdownBtn.setAttribute('aria-expanded', 'true');
                    if (dropdownIcon) dropdownIcon.style.transform = 'rotate(180deg)';
                };

                const closeDropdown = () => {
                    dropdownMenu.classList.add('scale-95', 'opacity-0');
                    dropdownMenu.classList.remove('scale-100', 'opacity-100');
                    dropdownBtn.setAttribute('aria-expanded', 'false');
                    if (dropdownIcon) dropdownIcon.style.transform = 'rotate(0deg)';
                    setTimeout(() => {
                        if (dropdownMenu.classList.contains('opacity-0')) {
                            dropdownMenu.classList.add('hidden');
                        }
                    }, 200);
                };

                dropdownBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = dropdownMenu.classList.contains('hidden');
                    isHidden ? openDropdown() : closeDropdown();
                });

                document.addEventListener('click', function (e) {
                    if (wrapper && !wrapper.contains(e.target)) closeDropdown();
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && !dropdownMenu.classList.contains('hidden')) {
                        closeDropdown();
                        dropdownBtn.focus();
                    }
                });
            }

            /* 5. EFEK SCROLL HEADER */
            const header = document.getElementById('site-header');
            if (header) {
                const onScroll = () => {
                    header.classList.toggle('shadow-2xl', window.scrollY > 8);
                    header.classList.toggle('backdrop-blur-md', window.scrollY > 8);
                };
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();
            }

            /* 6. BLUR FOCUS SAAT HALAMAN DIMUAT
                  Mencegah panel Organisasi/UKM terbuka otomatis
                  karena browser me-restore focus ke link sebelumnya. */
            if (document.activeElement && document.activeElement !== document.body) {
                try { document.activeElement.blur(); } catch (e) {}
            }
        });
    })();
</script>