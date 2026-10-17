{{-- resources/views/owner/impersonate/profile.blade.php --}}
@extends('layouts.impersonate')

@section('title', 'Profil — ' . ($user->name ?? 'User'))
@section('page_title', 'Profil Saya')
@section('page_description', 'Detail akun yang sedang Anda jelajahi')

@section('content')

    {{-- ═══════════════════════════════════════════════════════════
         HEADER PROFIL — avatar, nama, role
    ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl
                border border-slate-200 dark:border-slate-800
                overflow-hidden">

        {{-- Cover / banner tipis --}}
        <div class="h-24 sm:h-28 bg-gradient-to-r
                    from-amber-400 via-orange-500 to-amber-500
                    dark:from-amber-500 dark:via-orange-600 dark:to-amber-500"></div>

        <div class="px-5 sm:px-7 pb-6">
            {{-- Avatar + identitas --}}
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12 sm:-mt-14">

                {{-- Avatar --}}
                <div class="shrink-0">
                    @if($user->avatar_url ?? null)
                        <img src="{{ asset('storage/' . $user->avatar_url) }}"
                             alt="{{ $user->name }}"
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover
                                    ring-4 ring-white dark:ring-slate-900
                                    shadow-xl">
                    @else
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl
                                    bg-gradient-to-br from-amber-500 to-orange-500
                                    text-white flex items-center justify-center
                                    text-3xl sm:text-4xl font-black
                                    ring-4 ring-white dark:ring-slate-900
                                    shadow-xl">
                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 2)) }}
                        </div>
                    @endif
                </div>

                {{-- Info singkat --}}
                <div class="flex-1 min-w-0 pb-1">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white
                               leading-tight truncate">
                        {{ $user->name ?? 'User' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400
                              mt-0.5 flex items-center gap-2 flex-wrap">
                        <span>
                            <i class="fas fa-id-card text-[10px] mr-1"></i>
                            {{ $user->nim ?? '—' }}
                        </span>
                        <span class="opacity-50">•</span>
                        <span class="inline-flex items-center gap-1">
                            <i class="fas fa-user-shield text-[10px]"></i>
                            {{ ucfirst($roleSlug ?? 'user') }}
                        </span>
                    </p>
                </div>

                {{-- Badge status --}}
                <div class="shrink-0 pb-1">
                    @if($user->is_active ?? false)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                     bg-emerald-50 dark:bg-emerald-900/25
                                     text-emerald-700 dark:text-emerald-300
                                     border border-emerald-200 dark:border-emerald-700/40
                                     text-[11px] font-black">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                     bg-rose-50 dark:bg-rose-900/25
                                     text-rose-700 dark:text-rose-300
                                     border border-rose-200 dark:border-rose-700/40
                                     text-[11px] font-black">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         GRID DETAIL — 2 kolom
    ═══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 mt-5">

        {{-- KOLOM KIRI: Data Diri --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl
                    border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800
                        flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl
                             bg-gradient-to-br from-sky-500 to-blue-600
                             text-white flex items-center justify-center">
                    <i class="fas fa-circle-user text-sm"></i>
                </span>
                <h2 class="font-black text-sm text-slate-900 dark:text-white">
                    Data Diri
                </h2>
            </div>

            <dl class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                @foreach([
                    ['Nama Lengkap', $user->name         ?? '—',       'fa-user'],
                    ['NIM / Username', $user->nim        ?? ($user->username ?? '—'), 'fa-id-badge'],
                    ['Email',        $user->email        ?? '—',       'fa-envelope'],
                    ['Nomor HP',     $user->phone        ?? '—',       'fa-phone'],
                    ['Universitas',  $user->university?->name ?? '—',  'fa-graduation-cap'],
                    ['Program Studi', $user->major?->name ?? '—',      'fa-book'],
                    ['Status Akun',  ($user->is_active ?? false) ? 'Aktif' : 'Nonaktif', 'fa-toggle-on'],
                    ['Role',         ucfirst($roleSlug ?? 'user'),    'fa-shield-halved'],
                ] as [$label, $value, $icon])
                    <div>
                        <dt class="text-[10px] font-black uppercase tracking-wider
                                   text-slate-400 dark:text-slate-500
                                   flex items-center gap-1.5 mb-1">
                            <i class="fas {{ $icon }} text-[9px]"></i>
                            {{ $label }}
                        </dt>
                        <dd class="text-sm font-semibold text-slate-800 dark:text-slate-100
                                   break-words">
                            {{ $value }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- KOLOM KANAN: Info Sesi Jelajah --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl
                    border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800
                        flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl
                             bg-gradient-to-br from-amber-500 to-orange-500
                             text-white flex items-center justify-center">
                    <i class="fas fa-user-secret text-sm"></i>
                </span>
                <h2 class="font-black text-sm text-slate-900 dark:text-white">
                    Info Sesi
                </h2>
            </div>

            @php
                $__imp = session('impersonation', []);
                $__started = (int) ($__imp['started_at'] ?? time());
            @endphp

            <div class="p-5 sm:p-6 space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fas fa-crown text-amber-500 mt-0.5"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Owner Asli
                        </p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                            {{ auth()->user()->name ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <i class="fas fa-hourglass-half text-sky-500 mt-0.5"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Sesi Dimulai
                        </p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ \Carbon\Carbon::createFromTimestamp($__started)->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <i class="fas fa-fingerprint text-emerald-500 mt-0.5"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Fingerprint
                        </p>
                        <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400
                                   break-all">
                            {{ substr($__imp['fingerprint'] ?? '—', 0, 24) }}…
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-5 sm:px-6 pb-5 sm:pb-6">
                <form method="POST"
                      action="{{ route('owner.settings.explore.exit') }}"
                      onsubmit="return confirm('Keluar dari mode jelajah?')">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   bg-rose-50 dark:bg-rose-900/20
                                   hover:bg-rose-100 dark:hover:bg-rose-900/40
                                   text-rose-700 dark:text-rose-300
                                   text-xs font-black transition">
                        <i class="fas fa-right-from-bracket"></i>
                        Keluar dari Jelajah
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection