@php
    $safeUrl = fn (string $n, $p = [], $f = '#') => \Illuminate\Support\Facades\Route::has($n) ? route($n, $p) : $f;
    $safeIs  = fn (string ...$p) => request()->routeIs(...$p);
    $user = auth()->user();

    $hasKelompok = false;
    $hasDivisi   = false;
    try { $hasKelompok = $user?->kelompok()->exists() ?? false; } catch (\Throwable $e) {}
    try { $hasDivisi   = $user?->divisi()->exists() ?? false; } catch (\Throwable $e) {}
@endphp

<aside id="sidebar"
       class="w-72 h-screen overflow-y-auto flex-shrink-0 bg-white dark:bg-gray-900
              border-r border-gray-200 dark:border-gray-800 shadow-xl shadow-blue-500/5"
       aria-label="Sidebar Anggota">

    <div class="flex items-center gap-3 p-4 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10 bg-white/95 dark:bg-gray-900/95 backdrop-blur">
        <a href="{{ $safeUrl('anggota.dashboard', [], '/') }}" class="flex items-center gap-3 flex-1 min-w-0">
            <div class="relative shrink-0">
                <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo"
                     class="h-10 w-10 rounded-xl ring-2 ring-blue-500/40 object-cover">
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full animate-pulse"></span>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-lg font-extrabold text-transparent bg-clip-text
                             bg-gradient-to-r from-blue-600 to-indigo-500 leading-none">UNICODE</span>
                <span class="text-[10px] font-semibold text-gray-400 mt-0.5 tracking-widest">ANGGOTA</span>
            </div>
        </a>
    </div>

    <nav class="p-3 space-y-1 pb-6">

        {{-- DASHBOARD --}}
        <a href="{{ $safeUrl('anggota.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                  {{ $safeIs('anggota.dashboard')
                    ? 'bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/30 text-blue-700 dark:text-blue-300 font-semibold shadow-sm'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-home w-5 text-center"></i>
            <span class="text-sm">Dashboard</span>
        </a>

        {{-- CHATTING --}}
        <a href="{{ $safeUrl('anggota.chatting') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.chatting') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-comments w-5 text-center"></i>
            <span>Chatting</span>
        </a>

        {{-- PRESENSI --}}
        <a href="{{ $safeUrl('anggota.presensi') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.presensi') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-fingerprint w-5 text-center"></i>
            <span>Presensi</span>
        </a>

        {{-- PENGUMUMAN & NOTIFIKASI --}}
        <a href="{{ $safeUrl('anggota.pengumuman') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.pengumuman','owner.notifications.*') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-bullhorn w-5 text-center"></i>
            <span>Pengumuman & Notifikasi</span>
        </a>

        {{-- KELOMPOK (kondisional) --}}
        @if($hasKelompok)
        <a href="{{ $safeUrl('anggota.kelompok') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.kelompok') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-layer-group w-5 text-center"></i>
            <span>Kelompok Saya</span>
        </a>
        @endif

        {{-- DIVISI (kondisional) --}}
        @if($hasDivisi)
        <a href="{{ $safeUrl('anggota.divisi') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.divisi') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-sitemap w-5 text-center"></i>
            <span>Divisi Saya</span>
        </a>
        @endif

        {{-- MISI --}}
        <a href="{{ $safeUrl('anggota.misi') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.misi') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-crosshairs w-5 text-center"></i>
            <span>Misi</span>
        </a>

        {{-- LEADERPOINT --}}
        <a href="{{ $safeUrl('anggota.leaderpoint') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.leaderpoint','anggota.poin') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-star w-5 text-center"></i>
            <span>LeaderPoint</span>
        </a>

        {{-- GAME --}}
        <a href="{{ $safeUrl('anggota.game') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.game') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-gamepad w-5 text-center"></i>
            <span>Game</span>
        </a>

        {{-- RUANGAN --}}
        <a href="{{ $safeUrl('anggota.ruangan') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.ruangan') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-door-open w-5 text-center"></i>
            <span>Ruangan</span>
        </a>

        {{-- JADWAL --}}
        <a href="{{ $safeUrl('anggota.jadwal') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.jadwal') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-calendar-days w-5 text-center"></i>
            <span>Jadwal</span>
        </a>

        {{-- EVENT --}}
        <a href="{{ $safeUrl('anggota.event') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.event') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-calendar-check w-5 text-center"></i>
            <span>Event</span>
        </a>

        {{-- UANGKAS --}}
        <a href="{{ $safeUrl('anggota.uangkas') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('anggota.uangkas') ? 'bg-blue-100/70 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
            <i class="fas fa-coins w-5 text-center"></i>
            <span>Uangkas</span>
        </a>

        {{-- PENGATURAN --}}
        <details class="group pt-2" {{ $safeIs('anggota.pengaturan*','anggota.profil','anggota.sertifikat','anggota.portofolio','anggota.sanksi') ? 'open' : '' }}>
            <summary class="flex items-center justify-between px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pengaturan</span>
                <i class="fas fa-chevron-down text-[9px] text-gray-400 transition group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5 mt-1">
                <a href="{{ $safeUrl('anggota.profil') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <i class="fas fa-user w-5 text-center text-[15px]"></i> Profil
                </a>
                <a href="{{ $safeUrl('anggota.portofolio') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <i class="fas fa-briefcase w-5 text-center text-[15px]"></i> Portofolio
                </a>
                <a href="{{ $safeUrl('anggota.sertifikat') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <i class="fas fa-certificate w-5 text-center text-[15px]"></i> Sertifikat
                </a>
                <a href="{{ $safeUrl('anggota.sanksi') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <i class="fas fa-triangle-exclamation w-5 text-center text-[15px]"></i> Sanksi
                </a>
                <a href="{{ $safeUrl('anggota.pengaturan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <i class="fas fa-shield-halved w-5 text-center text-[15px]"></i> Keamanan
                </a>
            </div>
        </details>

        {{-- LOGOUT --}}
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
            <button type="button" onclick="showLogoutModal(event)"
                    class="flex items-center gap-3 w-full text-left px-3 py-2.5 rounded-xl
                           text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="text-sm font-medium">Keluar Akun</span>
            </button>
        </div>
    </nav>
</aside>
