{{-- ═══════════════════════════════════════════════════════════════
     IMPERSONATION LAYOUT — Production Grade
     ═══════════════════════════════════════════════════════════════
     Layout untuk halaman yang HANYA ADA dalam mode jelajah akun:
       /owner/{roleSlug}/dashboard
       /owner/{roleSlug}/profile
       ... (perluas sesuai kebutuhan)

     Data shared dari middleware RequireImpersonation:
       - $impersonationActive  (bool)
       - $impersonationOwner   (App\Models\User) — Owner asli
       - $impersonatedUser     (App\Models\User) — user target

     Fallback: session('impersonation') — struktur flat.
--}}
@php
    /* ─────────────────────────────────────────────────────────────
       PREPARE DATA (defensive — jangan crash kalau middleware
       tidak jalan atau session hilang)
    ───────────────────────────────────────────────────────────── */
    $imp = session('impersonation', []);

    $impActive   = (bool) ($impersonationActive ?? ($imp['active'] ?? false));
    $impUser     = $impersonatedUser   ?? null;
    $impOwner    = $impersonationOwner ?? null;

    // Fallback ke model kalau share tidak tersedia
    if (! $impUser && ! empty($imp['target_user_id'])) {
        try { $impUser = \App\Models\User::with('level:id,name')->find($imp['target_user_id']); }
        catch (\Throwable) { $impUser = null; }
    }
    if (! $impOwner && ! empty($imp['owner_id'])) {
        try { $impOwner = \App\Models\User::find($imp['owner_id']); }
        catch (\Throwable) { $impOwner = null; }
    }

    $impRoleSlug = (string) ($imp['role_slug'] ?? 'anggota');
    $impStarted  = (int)    ($imp['started_at'] ?? time());
    $impElapsed  = max(0, time() - $impStarted);
    $impSessionSeconds = 3600;                              // 60 menit total
    $impRemaining      = max(0, $impSessionSeconds - $impElapsed);
    $impSessionPct     = $impSessionSeconds > 0
        ? min(100, (int) round(($impElapsed / $impSessionSeconds) * 100))
        : 0;

    $impUserName  = $impUser?->name  ?? ($imp['target_name']  ?? '—');
    $impUserLevel = $impUser?->level?->name ?? match ((int) ($impUser?->level_id ?? 0)) {
        3 => 'Ketua', 4 => 'Pengurus', 5 => 'Anggota', 6 => 'Pengunjung',
        default => 'User',
    };
    $impUserAvatar = $impUser?->avatar_url
        ? asset('storage/' . $impUser->avatar_url)
        : null;

    /* ─────────────────────────────────────────────────────────────
       MENU — hanya route yang BENAR-BENAR ada di gateway impersonate
       yang di-enable. Route lain ditampilkan disabled + tooltip.
    ───────────────────────────────────────────────────────────── */
    $menuAvailable = [
        [
            'route' => 'owner.impersonate.dashboard',
            'icon'  => 'fa-gauge-high',
            'label' => 'Dashboard',
        ],
        [
            'route' => 'owner.impersonate.profile',
            'icon'  => 'fa-user',
            'label' => 'Profil Saya',
        ],
    ];

    $menuLockedByRole = [
        'anggota' => [
            ['icon' => 'fa-star',           'label' => 'Poin Saya'],
            ['icon' => 'fa-calendar-check', 'label' => 'Presensi'],
            ['icon' => 'fa-trophy',         'label' => 'Event'],
            ['icon' => 'fa-certificate',    'label' => 'Sertifikat'],
            ['icon' => 'fa-briefcase',      'label' => 'Portofolio'],
            ['icon' => 'fa-cog',            'label' => 'Pengaturan'],
        ],
        'pengurus' => [
            ['icon' => 'fa-tasks',          'label' => 'Tugas'],
            ['icon' => 'fa-layer-group',    'label' => 'Kelompok'],
            ['icon' => 'fa-bullhorn',       'label' => 'Pengumuman'],
            ['icon' => 'fa-address-book',   'label' => 'Kontak'],
            ['icon' => 'fa-users',          'label' => 'Anggota'],
            ['icon' => 'fa-id-card',        'label' => 'Data Diri'],
            ['icon' => 'fa-cog',            'label' => 'Pengaturan'],
        ],
        'ketua' => [
            ['icon' => 'fa-chart-line',     'label' => 'Ringkasan'],
            ['icon' => 'fa-users-cog',      'label' => 'Struktur'],
            ['icon' => 'fa-bullhorn',       'label' => 'Pengumuman'],
        ],
        'superadmin' => [
            ['icon' => 'fa-bullhorn',       'label' => 'Pengumuman'],
            ['icon' => 'fa-users',          'label' => 'Manajemen'],
            ['icon' => 'fa-chart-bar',      'label' => 'Laporan'],
            ['icon' => 'fa-address-book',   'label' => 'Kontak'],
        ],
        'pengunjung' => [
            ['icon' => 'fa-compass',        'label' => 'Explore'],
        ],
    ];
    $menuLocked = $menuLockedByRole[$impRoleSlug] ?? [];
@endphp

<!DOCTYPE html>
<html lang="id"
      x-data="{ dark: false }"
      :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">

    <title>@yield('title', 'Jelajah Akun') — {{ config('app.name', 'UNICODE') }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        /* Pulsing dot pada banner */
        @keyframes rcPulse {
            0%,100% { box-shadow: 0 0 0 0 currentColor; }
            50%     { box-shadow: 0 0 0 8px transparent; }
        }
        .rc-pulse { animation: rcPulse 2s infinite; }

        /* Progress bar untuk timer */
        .rc-progress {
            background: linear-gradient(90deg, #10b981 0%, #f59e0b 60%, #ef4444 100%);
        }

        /* Scrollbar */
        .rc-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .rc-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .dark .rc-scroll::-webkit-scrollbar-thumb { background: #475569; }
        .rc-scroll { scrollbar-width: thin; }

        /* Sidebar slide */
        .rc-sidebar-backdrop { backdrop-filter: blur(4px); }

        /* Focus visible */
        :focus-visible { outline: 2px solid #f59e0b; outline-offset: 2px; border-radius: 4px; }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen antialiased">

{{-- ═════════════════════════════════════════════════════════════════
     BANNER JELAJAH — selalu terlihat di paling atas
═════════════════════════════════════════════════════════════════ --}}
@if($impActive)
<div id="rcBanner"
     class="sticky top-0 z-40
            bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500
            dark:from-amber-600 dark:via-orange-600 dark:to-amber-600
            text-white shadow-lg shadow-amber-500/20">
    <div class="max-w-[1600px] mx-auto px-3 sm:px-5 py-2.5 flex items-center gap-3">

        {{-- Indikator --}}
        <span class="hidden sm:inline-flex items-center gap-2 shrink-0">
            <span class="w-2.5 h-2.5 rounded-full bg-white rc-pulse"></span>
            <span class="text-[10px] font-black uppercase tracking-widest">Mode Jelajah</span>
        </span>

        {{-- Avatar + Nama target --}}
        <div class="flex items-center gap-2 min-w-0 flex-1 sm:flex-none">
            @if($impUserAvatar)
                <img src="{{ $impUserAvatar }}" alt=""
                     class="w-8 h-8 rounded-full object-cover ring-2 ring-white/40 shrink-0">
            @else
                <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-black shrink-0">
                    {{ strtoupper(substr($impUserName, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-black truncate leading-tight">{{ $impUserName }}</p>
                <p class="text-[10px] opacity-90 truncate leading-tight">{{ $impUserLevel }}</p>
            </div>
        </div>

        {{-- Timer --}}
        <div class="hidden md:flex items-center gap-2 shrink-0 px-3 py-1 rounded-lg bg-white/15 border border-white/20">
            <i class="fas fa-hourglass-half text-xs"></i>
            <div class="leading-tight">
                <p class="text-[9px] uppercase tracking-wider opacity-80">Durasi</p>
                <p class="text-xs font-black" id="rcElapsed">00:00</p>
            </div>
            <div class="w-px h-6 bg-white/25"></div>
            <div class="leading-tight">
                <p class="text-[9px] uppercase tracking-wider opacity-80">Sisa</p>
                <p class="text-xs font-black" id="rcRemaining">60:00</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('owner.impersonate.profile', ['roleSlug' => $impRoleSlug]) }}"
               class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                      bg-white/15 hover:bg-white/25 border border-white/20
                      text-[11px] font-bold transition"
               title="Lihat profil yang sedang dijelajahi">
                <i class="fas fa-id-badge"></i> Profil
            </a>

            <button type="button" id="rcExitBtn"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg
                           bg-white text-amber-600 hover:bg-amber-50
                           text-[11px] font-black shadow-sm transition">
                <i class="fas fa-right-from-bracket"></i>
                <span class="hidden sm:inline">Keluar Jelajah</span>
                <span class="sm:hidden">Keluar</span>
            </button>
        </div>
    </div>

    {{-- Progress bar tipis di bawah banner --}}
    <div class="h-0.5 w-full bg-white/20">
        <div id="rcProgress" class="h-full rc-progress transition-all duration-1000"
             style="width: {{ $impSessionPct }}%"></div>
    </div>
</div>
@endif

{{-- ═════════════════════════════════════════════════════════════════
     LAYOUT: SIDEBAR + CONTENT
═════════════════════════════════════════════════════════════════ --}}
<div class="flex min-h-[calc(100vh-{{ $impActive ? '58px' : '0px' }})]">

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside id="rcSidebar"
           class="fixed lg:static inset-y-0 left-0 z-30
                  w-72 lg:w-72 xl:w-80 shrink-0
                  bg-white dark:bg-slate-900
                  border-r border-slate-200 dark:border-slate-800
                  flex flex-col
                  -translate-x-full lg:translate-x-0
                  transition-transform duration-300 ease-out
                  {{ $impActive ? 'top-[58px] lg:top-0' : 'top-0' }}">
        {{-- Padding atas saat banner aktif --}}
        @if($impActive)
            <div class="hidden lg:block h-3"></div>
        @endif

        {{-- Header sidebar: profil target --}}
        <div class="p-5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                @if($impUserAvatar)
                    <img src="{{ $impUserAvatar }}" alt=""
                         class="w-12 h-12 rounded-2xl object-cover ring-2 ring-amber-400/60">
                @else
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500
                                 flex items-center justify-center text-white text-lg font-black">
                        {{ strtoupper(substr($impUserName, 0, 1)) }}
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-black text-slate-900 dark:text-white truncate">{{ $impUserName }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        {{ $impUserLevel }}
                        @if($impUser?->nim) · {{ $impUser->nim }} @endif
                    </p>
                </div>
            </div>

            @if($impActive)
                <div class="mt-3 flex items-center gap-2 text-[10px] font-bold">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full
                                 bg-amber-50 dark:bg-amber-900/30
                                 text-amber-700 dark:text-amber-300
                                 border border-amber-200 dark:border-amber-700/40">
                        <i class="fas fa-user-secret"></i> Impersonasi
                    </span>
                    <span class="text-slate-400">
                        Owner: {{ $impOwner?->name ?? '—' }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto rc-scroll px-3 py-4 space-y-1">

            <p class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest
                      text-slate-400 dark:text-slate-500">
                Menu Tersedia
            </p>

            @foreach($menuAvailable as $item)
                @php
                    $routeName = $item['route'];
                    $routeExists = \Illuminate\Support\Facades\Route::has($routeName);
                    $isActive = $routeExists && request()->routeIs($routeName);
                    $href = $routeExists
                        ? route($routeName, ['roleSlug' => $impRoleSlug])
                        : '#';
                @endphp
                <a href="{{ $href }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
                          text-sm font-bold transition
                          {{ $isActive
                                ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md shadow-amber-500/25'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="fas {{ $item['icon'] }} w-4 text-center
                              {{ $isActive ? '' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500' }}"></i>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if($isActive)
                        <i class="fas fa-chevron-right text-[10px] opacity-80"></i>
                    @endif
                </a>
            @endforeach

            @if(count($menuLocked) > 0)
                <div class="pt-4">
                    <p class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest
                              text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                        <i class="fas fa-lock text-[9px]"></i> Perlu Keluar Jelajah
                    </p>
                    <div class="space-y-0.5">
                        @foreach($menuLocked as $item)
                            <div class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl
                                        text-sm font-medium
                                        text-slate-400 dark:text-slate-500
                                        cursor-not-allowed select-none"
                                 title="Keluar dari mode jelajah untuk mengakses menu ini">
                                <i class="fas {{ $item['icon'] }} w-4 text-center opacity-60"></i>
                                <span class="flex-1 opacity-80">{{ $item['label'] }}</span>
                                <i class="fas fa-lock text-[9px] opacity-50"></i>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 px-3 text-[10px] text-slate-400 dark:text-slate-500 italic leading-relaxed">
                        <i class="fas fa-circle-info mr-1"></i>
                        Menu ini belum tersedia di gateway jelajah.
                        Tambahkan route <code class="font-mono">owner.impersonate.*</code> untuk mengaktifkan.
                    </p>
                </div>
            @endif
        </nav>

        {{-- Footer sidebar: info sesi --}}
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider
                        text-slate-400 dark:text-slate-500">
                <span><i class="fas fa-shield-halved mr-1"></i> Sesi</span>
                <span id="rcSidebarRemaining">60:00</span>
            </div>
            <div class="h-1 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div id="rcSidebarProgress" class="h-full rc-progress transition-all duration-1000"
                     style="width: {{ $impSessionPct }}%"></div>
            </div>
            <button type="button" id="rcExitBtnSide"
                    class="w-full mt-2 inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           bg-rose-50 dark:bg-rose-900/20
                           hover:bg-rose-100 dark:hover:bg-rose-900/40
                           text-rose-700 dark:text-rose-300
                           text-xs font-black transition">
                <i class="fas fa-right-from-bracket"></i> Keluar dari Jelajah
            </button>
        </div>
    </aside>

    {{-- Backdrop untuk mobile --}}
    <div id="rcSidebarBackdrop"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden hidden"></div>

    {{-- ══════════ MAIN CONTENT ══════════ --}}
    <main class="flex-1 min-w-0 flex flex-col">

        {{-- Top nav --}}
        <header class="sticky top-0 z-10
                       bg-white/85 dark:bg-slate-900/85 backdrop-blur-md
                       border-b border-slate-200 dark:border-slate-800
                       {{ $impActive ? 'lg:top-0' : '' }}">

            <div class="px-4 sm:px-6 py-3 flex items-center gap-3">
                {{-- Mobile menu toggle --}}
                <button type="button" id="rcSidebarToggle"
                        class="lg:hidden w-10 h-10 rounded-xl
                               bg-slate-100 dark:bg-slate-800
                               hover:bg-slate-200 dark:hover:bg-slate-700
                               flex items-center justify-center transition"
                        aria-label="Buka menu">
                    <i class="fas fa-bars text-slate-700 dark:text-slate-200"></i>
                </button>

                {{-- Breadcrumb + title --}}
                <div class="flex-1 min-w-0">
                    <nav class="hidden sm:flex items-center gap-1.5 text-[11px]
                                text-slate-500 dark:text-slate-400">
                        <span class="font-bold text-amber-600 dark:text-amber-400">
                            <i class="fas fa-user-secret mr-1"></i>Jelajah Akun
                        </span>
                        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
                        <span class="truncate">{{ $impUserLevel }}</span>
                        @hasSection('breadcrumb')
                            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
                            <span class="font-semibold text-slate-700 dark:text-slate-200 truncate">
                                @yield('breadcrumb')
                            </span>
                        @endif
                    </nav>
                    <h1 class="text-sm font-black text-slate-800 dark:text-white truncate
                               @hasSection('breadcrumb') hidden sm:block @endif">
                        @yield('page_title', 'Jelajah Akun')
                    </h1>
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    {{-- Theme toggle --}}
                    <button type="button" id="rcThemeToggle"
                            class="w-9 h-9 rounded-xl
                                   bg-slate-100 dark:bg-slate-800
                                   hover:bg-slate-200 dark:hover:bg-slate-700
                                   flex items-center justify-center transition"
                            title="Ganti tema">
                        <i class="fas fa-moon text-slate-600 dark:hidden"></i>
                        <i class="fas fa-sun text-amber-400 hidden dark:inline"></i>
                    </button>

                    {{-- Session chip --}}
                    @if($impActive)
                        <span class="hidden sm:inline-flex items-center gap-1.5
                                     px-2.5 py-1.5 rounded-xl
                                     bg-amber-50 dark:bg-amber-900/20
                                     border border-amber-200 dark:border-amber-700/40
                                     text-[10px] font-black text-amber-700 dark:text-amber-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 rc-pulse"></span>
                            <span id="rcTopbarRemaining">60:00</span>
                        </span>
                    @endif
                </div>
            </div>
        </header>

        {{-- Page header --}}
        @hasSection('page_actions')
            <div class="px-4 sm:px-6 pt-5 pb-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                        @yield('page_heading', trim($__env->yieldContent('page_title', 'Jelajah Akun')))
                    </h2>
                    @hasSection('page_description')
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            @yield('page_description')
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @yield('page_actions')
                </div>
            </div>
        @endif

        {{-- Main slot --}}
        <div class="flex-1 p-4 sm:p-6">
            @yield('content')
        </div>

        {{-- Footer --}}
        <footer class="px-4 sm:px-6 py-4 border-t border-slate-200 dark:border-slate-800
                       bg-white/60 dark:bg-slate-900/60 backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-[11px]
                        text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-3 flex-wrap">
                    @if($impActive)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 rc-pulse"></span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">Sesi Aktif</span>
                        </span>
                        <span class="opacity-60">•</span>
                    @endif
                    <span>
                        <i class="far fa-clock mr-1"></i>
                        Dimulai {{ \Carbon\Carbon::createFromTimestamp($impStarted)->diffForHumans() }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-mono text-[10px] opacity-60 hidden sm:inline">
                        {{ substr($imp['fingerprint'] ?? '', 0, 12) }}…
                    </span>
                    <span>
                        <i class="fas fa-shield-halved mr-1"></i>
                        {{ config('app.name') }}
                    </span>
                </div>
            </div>
        </footer>
    </main>
</div>

{{-- ═════════════════════════════════════════════════════════════════
     MODAL KONFIRMASI KELUAR
═════════════════════════════════════════════════════════════════ --}}
<div id="rcExitModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4
            bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl
                border border-slate-200 dark:border-slate-800 overflow-hidden
                transform transition-all scale-95 opacity-0"
         id="rcExitModalCard">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl shrink-0
                            bg-amber-100 dark:bg-amber-900/30
                            flex items-center justify-center
                            text-amber-600 dark:text-amber-400">
                    <i class="fas fa-right-from-bracket text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-black text-base text-slate-900 dark:text-white">
                        Keluar dari Mode Jelajah?
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Anda akan keluar dari akun
                        <b class="text-slate-700 dark:text-slate-200">{{ $impUserName }}</b>
                        dan kembali ke dashboard Owner.
                    </p>
                    <ul class="mt-3 space-y-1 text-[11px] text-slate-500 dark:text-slate-400">
                        <li class="flex items-center gap-1.5">
                            <i class="fas fa-circle-check text-emerald-500 text-[10px]"></i>
                            Sesi jelajah akan diakhiri
                        </li>
                        <li class="flex items-center gap-1.5">
                            <i class="fas fa-circle-check text-emerald-500 text-[10px]"></i>
                            Aktivitas tercatat di audit log
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="px-6 pb-6 pt-2 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
            <button type="button" id="rcExitCancel"
                    class="inline-flex items-center justify-center gap-2
                           px-5 py-2.5 rounded-xl
                           bg-slate-100 dark:bg-slate-800
                           hover:bg-slate-200 dark:hover:bg-slate-700
                           text-slate-700 dark:text-slate-200
                           text-xs font-black transition">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" id="rcExitConfirm"
                    class="inline-flex items-center justify-center gap-2
                           px-5 py-2.5 rounded-xl
                           bg-gradient-to-r from-rose-500 to-red-600
                           hover:from-rose-600 hover:to-red-700
                           text-white text-xs font-black
                           shadow-md shadow-rose-500/25 transition">
                <i class="fas fa-right-from-bracket"></i>
                Ya, Keluar Sekarang
            </button>
        </div>
    </div>
</div>

{{-- Form tersembunyi untuk POST exit (CSRF safe) --}}
@if(\Illuminate\Support\Facades\Route::has('owner.settings.explore.exit'))
    <form id="rcExitForm"
          action="{{ route('owner.settings.explore.exit') }}"
          method="POST"
          class="hidden">
        @csrf
    </form>
@endif

{{-- ═════════════════════════════════════════════════════════════════
     SCRIPTS
═════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    /* ─────────────────────────────────────────────────────────────
       CONFIG dari server
    ───────────────────────────────────────────────────────────── */
    const IMP_ACTIVE      = @json($impActive);
    const IMP_ELAPSED     = {{ $impElapsed }};         // detik
    const IMP_TOTAL       = {{ $impSessionSeconds }};  // detik total
    const IMP_WARN_AT     = 55 * 60;                   // warning di 55 menit

    /* ─────────────────────────────────────────────────────────────
       THEME TOGGLE
    ───────────────────────────────────────────────────────────── */
    (function initTheme() {
        const stored = localStorage.getItem('rc-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = stored ? stored === 'dark' : prefersDark;
        if (dark) document.documentElement.classList.add('dark');
    })();

    document.getElementById('rcThemeToggle')?.addEventListener('click', () => {
        const html = document.documentElement;
        html.classList.toggle('dark');
        localStorage.setItem('rc-theme', html.classList.contains('dark') ? 'dark' : 'light');
    });

    /* ─────────────────────────────────────────────────────────────
       SIDEBAR MOBILE
    ───────────────────────────────────────────────────────────── */
    const sidebar  = document.getElementById('rcSidebar');
    const backdrop = document.getElementById('rcSidebarBackdrop');
    const toggle   = document.getElementById('rcSidebarToggle');

    function openSidebar() {
        sidebar?.classList.remove('-translate-x-full');
        backdrop?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar?.classList.add('-translate-x-full');
        backdrop?.classList.add('hidden');
        document.body.style.overflow = '';
    }
    toggle?.addEventListener('click', openSidebar);
    backdrop?.addEventListener('click', closeSidebar);

    /* ─────────────────────────────────────────────────────────────
       MODAL EXIT
    ───────────────────────────────────────────────────────────── */
    const modal     = document.getElementById('rcExitModal');
    const modalCard = document.getElementById('rcExitModalCard');
    const form      = document.getElementById('rcExitForm');

    function openExitModal() {
        if (!modal || !modalCard) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // trigger transisi
        requestAnimationFrame(() => {
            modalCard.classList.remove('scale-95', 'opacity-0');
            modalCard.classList.add('scale-100', 'opacity-100');
        });
        document.body.style.overflow = 'hidden';
    }
    function closeExitModal() {
        if (!modal || !modalCard) return;
        modalCard.classList.add('scale-95', 'opacity-0');
        modalCard.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 180);
    }

    document.getElementById('rcExitBtn')?.addEventListener('click', openExitModal);
    document.getElementById('rcExitBtnSide')?.addEventListener('click', openExitModal);
    document.getElementById('rcExitCancel')?.addEventListener('click', closeExitModal);
    document.getElementById('rcExitConfirm')?.addEventListener('click', () => {
        if (form) form.submit();
    });
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) closeExitModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeExitModal();
        }
    });

    /* ─────────────────────────────────────────────────────────────
       TIMER — update setiap 1 detik
    ───────────────────────────────────────────────────────────── */
    if (IMP_ACTIVE) {
        let elapsed = IMP_ELAPSED;

        const fmt = (sec) => {
            sec = Math.max(0, Math.floor(sec));
            const h = Math.floor(sec / 3600);
            const m = Math.floor((sec % 3600) / 60);
            const s = sec % 60;
            const pad = (n) => String(n).padStart(2, '0');
            return h > 0 ? `${pad(h)}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
        };

        const els = {
            elapsed:         document.getElementById('rcElapsed'),
            remaining:       document.getElementById('rcRemaining'),
            topbarRemaining: document.getElementById('rcTopbarRemaining'),
            sideRemaining:   document.getElementById('rcSidebarRemaining'),
            progress:        document.getElementById('rcProgress'),
            sideProgress:    document.getElementById('rcSidebarProgress'),
        };

        function tick() {
            const remaining = Math.max(0, IMP_TOTAL - elapsed);

            if (els.elapsed)         els.elapsed.textContent         = fmt(elapsed);
            if (els.remaining)       els.remaining.textContent       = fmt(remaining);
            if (els.topbarRemaining) els.topbarRemaining.textContent = fmt(remaining);
            if (els.sideRemaining)   els.sideRemaining.textContent   = fmt(remaining);

            const pct = Math.min(100, (elapsed / IMP_TOTAL) * 100);
            if (els.progress)     els.progress.style.width     = pct + '%';
            if (els.sideProgress) els.sideProgress.style.width = pct + '%';

            // Warning ketika mendekati timeout
            if (remaining <= (IMP_TOTAL - IMP_WARN_AT) && remaining > 0) {
                const banner = document.getElementById('rcBanner');
                if (banner && !banner.dataset.warned) {
                    banner.dataset.warned = '1';
                    banner.classList.remove('from-amber-500', 'via-orange-500', 'to-amber-500');
                    banner.classList.add('from-rose-500', 'via-red-500', 'to-rose-500');
                }
            }

            // Auto keluar jika habis
            if (remaining <= 0 && form) {
                form.submit();
                return;
            }

            elapsed++;
        }

        tick();
        setInterval(tick, 1000);
    }

    /* ─────────────────────────────────────────────────────────────
       TOAST (dipakai opsional oleh view yang extend)
    ───────────────────────────────────────────────────────────── */
    window.rcToast = function (message, tone = 'info', duration = 3000) {
        const tones = {
            success: 'bg-emerald-500',
            error:   'bg-rose-500',
            warning: 'bg-amber-500',
            info:    'bg-sky-500',
        };
        const el = document.createElement('div');
        el.className = `fixed bottom-6 right-6 z-[100] max-w-sm
                        px-4 py-3 rounded-xl text-white text-sm font-bold shadow-2xl
                        transition-all duration-200
                        ${tones[tone] || tones.info}`;
        el.style.opacity = '0';
        el.style.transform = 'translateY(12px)';
        el.textContent = message;
        document.body.appendChild(el);
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(12px)';
            setTimeout(() => el.remove(), 220);
        }, duration);
    };
})();
</script>

@stack('scripts')
</body>
</html>