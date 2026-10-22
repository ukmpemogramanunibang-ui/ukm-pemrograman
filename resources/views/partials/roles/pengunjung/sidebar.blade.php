@php
    $safeUrl = fn (string $n, $p = [], $f = '#') => \Illuminate\Support\Facades\Route::has($n) ? route($n, $p) : $f;
    $safeIs  = fn (string ...$p) => request()->routeIs(...$p);
    $isLoggedIn = auth()->check();
@endphp

<aside id="sidebar"
       class="w-72 h-screen overflow-y-auto flex-shrink-0 bg-white dark:bg-gray-900
              border-r border-gray-200 dark:border-gray-800 shadow-xl shadow-amber-500/5"
       aria-label="Sidebar Pengunjung">

    <div class="flex items-center gap-3 p-4 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10 bg-white/95 dark:bg-gray-900/95 backdrop-blur">
        <a href="{{ $safeUrl('landing', [], '/') }}" class="flex items-center gap-3 flex-1 min-w-0">
            <div class="relative shrink-0">
                <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo"
                     class="h-10 w-10 rounded-xl ring-2 ring-amber-500/40 object-cover">
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-amber-500 border-2 border-white dark:border-gray-900 rounded-full"></span>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-lg font-extrabold text-transparent bg-clip-text
                             bg-gradient-to-r from-amber-600 to-orange-500 leading-none">UNICODE</span>
                <span class="text-[10px] font-semibold text-gray-400 mt-0.5 tracking-widest">PUBLIK</span>
            </div>
        </a>
    </div>

    <nav class="p-3 space-y-1 pb-6">

        {{-- BERANDA --}}
        <a href="{{ $safeUrl('landing', [], '/') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                  {{ $safeIs('landing') ? 'bg-gradient-to-r from-amber-100 to-orange-100 dark:from-amber-900/40 dark:to-orange-900/30 text-amber-700 dark:text-amber-300 font-semibold shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20' }}">
            <i class="fas fa-home w-5 text-center"></i>
            <span class="text-sm">Beranda</span>
        </a>

        {{-- JELAJAH UNICODE --}}
        <details class="group pt-2" {{ $safeIs('about','explore','dokumentasi','prestasi','portfolio','member') ? 'open' : '' }}>
            <summary class="flex items-center justify-between px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Jelajah UNICODE</span>
                <i class="fas fa-chevron-down text-[9px] text-gray-400 transition group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5 mt-1">
                <a href="{{ $safeUrl('about', [], '/#about') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-info-circle w-5 text-center text-[15px]"></i> Tentang
                </a>
                <a href="{{ $safeUrl('member', [], '/#struktur') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-sitemap w-5 text-center text-[15px]"></i> Struktur
                </a>
                <a href="{{ $safeUrl('member') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-users w-5 text-center text-[15px]"></i> Anggota
                </a>
                <a href="{{ $safeUrl('member', ['tab' => 'divisi']) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-building w-5 text-center text-[15px]"></i> Divisi
                </a>
                <a href="{{ $safeUrl('dokumentasi') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-book w-5 text-center text-[15px]"></i> Dokumentasi
                </a>
                <a href="{{ $safeUrl('prestasi') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-medal w-5 text-center text-[15px]"></i> Prestasi
                </a>
                <a href="{{ $safeUrl('portfolio') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <i class="fas fa-briefcase w-5 text-center text-[15px]"></i> Portfolio
                </a>
            </div>
        </details>

        {{-- EVENT --}}
        <a href="{{ $safeUrl('explore', ['tab' => 'event']) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
            <i class="fas fa-calendar-check w-5 text-center"></i>
            <span>Event</span>
        </a>

        {{-- JADWAL --}}
        <a href="{{ $safeUrl('room.public.index', [], route('landing') . '#jadwal') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
            <i class="fas fa-calendar-days w-5 text-center"></i>
            <span>Jadwal Publik</span>
        </a>

        {{-- RUANGAN --}}
        <a href="{{ $safeUrl('room.public.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                  {{ $safeIs('room.public.*') ? 'bg-amber-100/70 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20' }}">
            <i class="fas fa-door-open w-5 text-center"></i>
            <span>Ruangan Publik</span>
        </a>

        {{-- GAME --}}
        <a href="{{ $safeUrl('explore', ['tab' => 'game']) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
            <i class="fas fa-gamepad w-5 text-center"></i>
            <span>Game</span>
        </a>

        {{-- CTA LOGIN / DAFTAR --}}
        @if(!$isLoggedIn)
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800 space-y-2">
            <a href="{{ $safeUrl('login') }}"
               class="flex items-center justify-center gap-3 w-full px-3 py-2.5 rounded-xl
                      bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold shadow-md shadow-amber-500/20 hover:shadow-lg transition">
                <i class="fas fa-sign-in-alt"></i>
                <span class="text-sm">Login</span>
            </a>
            <a href="{{ $safeUrl('guest.register', [], '/#daftar') }}"
               class="flex items-center justify-center gap-3 w-full px-3 py-2.5 rounded-xl
                      border-2 border-amber-500 text-amber-600 dark:text-amber-400 font-semibold hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                <i class="fas fa-user-plus"></i>
                <span class="text-sm">Daftar</span>
            </a>
        </div>
        @else
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
            <a href="{{ url('/dashboard') }}"
               class="flex items-center justify-center gap-3 w-full px-3 py-2.5 rounded-xl
                      bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold shadow-md shadow-amber-500/20">
                <i class="fas fa-tachometer-alt"></i>
                <span class="text-sm">Dashboard Saya</span>
            </a>
        </div>
        @endif
    </nav>
</aside>
