@extends('layouts.owner')

@section('content')
@php
    $durationSeconds = max(1, (int)($settings['duration'] ?? 60));
    $customText = trim((string)($settings['custom_text'] ?? ''));
    $user = auth()->user();

    // Nama pengguna
    $playerName = $user->name
        ?? $user->nama
        ?? $user->username
        ?? 'Pemain';

    // Role pengguna — tangani jika berupa objek relasi
    if (is_object($user->role ?? null)) {
        // Ambil properti yang umum: display_name, name, atau title
        $playerRole = $user->role->display_name
            ?? $user->role->name
            ?? $user->role->title
            ?? 'Pemain';
    } elseif (is_string($user->role ?? null)) {
        $playerRole = $user->role;
    } elseif (isset($user->jabatan)) {
        $playerRole = $user->jabatan;
    } elseif (isset($user->level)) {
        $playerRole = 'Level ' . $user->level;
    } else {
        $playerRole = 'Pemain';
    }

    // Avatar: cek beberapa field umum, fallback ke inisial
    $avatarUrl = $user->avatar
        ?? $user->profile_photo_url
        ?? $user->photo
        ?? null;
    $avatarInitial = strtoupper(substr($playerName, 0, 1));
@endphp

<div id="typing-page"
     class="typing-page min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(99,102,241,.16),_transparent_32%),linear-gradient(135deg,#f8fafc,#eef2ff)] dark:bg-[radial-gradient(circle_at_top_right,_rgba(79,70,229,.18),_transparent_30%),linear-gradient(135deg,#020617,#0f172a)] p-3 sm:p-5 lg:p-7">

    <div class="mx-auto max-w-[1500px]">

        {{-- ======================== TOP BAR ======================== --}}
        <div class="mb-5 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-indigo-200/70 bg-white/80 px-3 py-1 text-xs font-semibold text-indigo-700 shadow-sm backdrop-blur dark:border-indigo-500/20 dark:bg-slate-900/70 dark:text-indigo-300">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                    LIVE TYPING ARENA
                </div>
                <h1 class="flex flex-wrap items-center gap-3 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                    <i class="fas fa-keyboard text-indigo-500"></i>
                    {{ $game->name }}
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kompetisi typing presisi tinggi · durasi <span id="topbar-duration">{{ $durationSeconds }}</span> detik
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Player Card --}}
                <div class="player-identity-card rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/80 flex items-center gap-3">
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="Avatar" class="h-10 w-10 rounded-full object-cover ring-2 ring-indigo-500/30">
    @else
        <div class="h-10 w-10 rounded-full bg-indigo-500/10 flex items-center justify-center text-sm font-black text-indigo-600 dark:text-indigo-400">
            {{ $avatarInitial }}
        </div>
    @endif
    <div class="min-w-0 flex-1">
        <div class="text-[11px] font-bold uppercase tracking-[.16em] text-slate-400">Pemain</div>
        <div id="player-name-top" class="max-w-[220px] truncate text-sm font-extrabold text-slate-800 dark:text-white">
            {{ $playerName }}
        </div>
        <div id="player-role-top" class="text-[11px] font-semibold text-indigo-500 dark:text-indigo-400">
            {{ $playerRole }}
        </div>
    </div>
</div>

                <a href="{{ route('owner.game.games.show', $game) }}"
                   class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <i class="fas fa-arrow-left mr-2"></i> Keluar
                </a>
            </div>
        </div>

        {{-- ======================== ARENA LAYOUT ======================== --}}
        <div id="arena-layout" class="arena-layout grid gap-5">

            {{-- ======================== MAIN ARENA ======================== --}}
            <section id="typing-app"
                     class="overflow-hidden rounded-[28px] border border-white/80 bg-white/85 shadow-[0_20px_70px_rgba(15,23,42,.10)] backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/85">

                {{-- LIVE METRICS GRID --}}
                <div class="metrics-grid grid gap-2 border-b border-slate-200/80 p-3 dark:border-slate-800">

                    {{-- 1. Waktu --}}
                    <div class="metric-card" data-metric="timer">
                        <div class="metric-label">Waktu</div>
                        <div id="typing-timer" class="metric-value text-indigo-600 dark:text-indigo-400">00:00.000</div>
                        <div class="metric-sub">elapsed / tersisa</div>
                    </div>

                    {{-- 2. WPM --}}
                    <div class="metric-card" data-metric="wpm">
                        <div class="metric-label">WPM</div>
                        <div id="typing-wpm" class="metric-value text-emerald-600 dark:text-emerald-400">0.0</div>
                        <div class="metric-sub">word / minute</div>
                    </div>

                    {{-- 3. CPM --}}
                    <div class="metric-card" data-metric="cpm">
                        <div class="metric-label">CPM</div>
                        <div id="typing-cpm" class="metric-value text-cyan-600 dark:text-cyan-400">0.0</div>
                        <div class="metric-sub">char / minute</div>
                    </div>

                    {{-- 4. Akurasi --}}
                    <div class="metric-card" data-metric="accuracy">
                        <div class="metric-label">Akurasi</div>
                        <div id="typing-accuracy" class="metric-value text-amber-600 dark:text-amber-400">100.0%</div>
                        <div class="metric-sub">karakter benar</div>
                    </div>

                    {{-- 5. Benar (hidden on mobile/tablet) --}}
                    <div class="metric-card" data-metric="correct" data-desktop-only>
                        <div class="metric-label">Benar</div>
                        <div id="typing-correct" class="metric-value text-emerald-600 dark:text-emerald-400">0</div>
                        <div class="metric-sub">kata selesai</div>
                    </div>

                    {{-- 6. Salah (hidden on mobile/tablet) --}}
                    <div class="metric-card" data-metric="wrong" data-desktop-only>
                        <div class="metric-label">Salah</div>
                        <div id="typing-wrong" class="metric-value text-rose-600 dark:text-rose-400">0</div>
                        <div class="metric-sub">kata selesai</div>
                    </div>

                    {{-- 7. Respons Key --}}
                    <div class="metric-card" data-metric="keyresponse">
                        <div class="metric-label">Respons Key</div>
                        <div id="typing-key-response" class="metric-value text-violet-600 dark:text-violet-400">0ms</div>
                        <div class="metric-sub">jeda tombol terakhir</div>
                    </div>

                    {{-- 8. Aktivitas (hidden on mobile/tablet) --}}
                    <div class="metric-card" data-metric="activity" data-desktop-only>
                        <div class="metric-label">Aktivitas</div>
                        <div id="typing-activity" class="metric-value text-sky-600 dark:text-sky-400 text-base sm:text-lg">SIAP</div>
                        <div id="typing-activity-sub" class="metric-sub">menunggu permainan</div>
                    </div>
                </div>

                {{-- PROGRESS BAR --}}
                <div class="px-5 pt-5 sm:px-7">
                    <div class="mb-2 flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                        <span>PROGRESS</span>
                        <span><span id="typing-word-count">0</span>/<span id="total-words">{{ 0 }}</span> kata</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                        <div id="progress-bar" class="h-full w-0 rounded-full bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-400 transition-[width] duration-200 ease-out"></div>
                    </div>
                </div>

                {{-- TEXT DISPLAY --}}
                <div class="p-5 sm:p-7">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="text-xs font-bold uppercase tracking-[.18em] text-slate-400">Text challenge</div>
                        <div id="typing-status" class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-300">
                            <span id="status-dot" class="h-2 w-2 rounded-full bg-slate-400"></span>
                            <span id="status-text">Klik Mulai</span>
                        </div>
                    </div>

                    <div id="text-display" class="typing-text-panel">
                        {{-- character engine renders here --}}
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400">
                        <span><i class="fas fa-space-bar mr-1"></i> Tekan <b>Space</b> untuk commit kata.</span>
                        <span id="typing-speed">0.0 char/dtk</span>
                    </div>
                </div>

                {{-- INPUT --}}
                <div class="px-5 pb-5 sm:px-7">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <i class="fas fa-terminal"></i>
                        </div>
                        <input
                            type="text"
                            id="typing-input"
                            inputmode="text"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false"
                            class="typing-input"
                            placeholder="Klik Mulai lalu ketik di sini..."
                            disabled
                        >
                        <div id="input-state-pill" class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-bold text-slate-400">
                            READY
                        </div>
                    </div>
                </div>

                {{-- CONTROLS --}}
                <div class="flex flex-col gap-3 border-t border-slate-200/80 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7 dark:border-slate-800 dark:bg-slate-950/30">
                    <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="chip"><i class="fas fa-bolt mr-1 text-amber-500"></i> realtime</span>
                        <span class="chip"><i class="fas fa-microchip mr-1 text-indigo-500"></i> high precision</span>
                        <span class="chip"><i class="fas fa-wifi mr-1 text-emerald-500"></i> competition ready</span>
                    </div>

                    <div class="control-btns flex gap-2">
                        <button id="typing-reset-btn" type="button" class="control-btn control-btn-secondary">
                            <i class="fas fa-rotate-left"></i> Reset
                        </button>
                        <button id="typing-start-btn" type="button" class="control-btn control-btn-primary">
                            <i class="fas fa-play"></i> Mulai
                        </button>
                    </div>
                </div>
            </section>

            {{-- ======================== COMPETITION PREVIEW ======================== --}}
            <aside id="competition-panel"
                   class="rounded-[28px] border border-white/80 bg-white/85 p-4 shadow-[0_20px_70px_rgba(15,23,42,.10)] backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/85">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-[.18em] text-indigo-500">Competition preview</div>
                        <h2 class="mt-1 text-lg font-black text-slate-900 dark:text-white">Live ranking</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Siapa paling cepat dan paling dekat finish.</p>
                    </div>
                    <div class="rounded-xl bg-emerald-500/10 px-2.5 py-1.5 text-[10px] font-black text-emerald-600 dark:text-emerald-400">
                        LIVE
                    </div>
                </div>

                <div id="competition-sync-status" class="mb-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-400">
                    Menunggu peserta lain...
                </div>

                <div id="competition-list" class="space-y-2"></div>

                <div class="mt-4 rounded-2xl border border-indigo-200 bg-indigo-50/80 p-3 dark:border-indigo-500/20 dark:bg-indigo-500/10">
                    <div class="flex items-center gap-2 text-xs font-extrabold text-indigo-700 dark:text-indigo-300">
                        <i class="fas fa-shield-halved"></i>
                        Prinsip ranking
                    </div>
                    <p class="mt-1 text-[11px] leading-5 text-indigo-700/80 dark:text-indigo-300/80">
                        Peringkat memprioritaskan progress, lalu waktu finish, lalu WPM, kemudian akurasi.
                    </p>
                </div>
            </aside>
        </div>

        {{-- ======================== RESULT SECTION (ENHANCED) ======================== --}}
        <section id="result-section"
                 class="mt-5 hidden rounded-[28px] border border-white/80 bg-white/90 p-5 shadow-[0_20px_70px_rgba(15,23,42,.10)] backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90 sm:p-8">

            {{-- Result Header --}}
            <div class="text-center">
                <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-orange-500/20">
                    <i class="fas fa-trophy text-2xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white">Hasil Typing</h2>
                <p id="result-summary" class="mt-1 text-sm text-slate-500 dark:text-slate-400"></p>
                <div id="result-grade" class="mt-3 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-lg font-black text-white">
                    A+
                </div>
            </div>

            {{-- Hero Result: Circular Accuracy Ring + Speed Chart --}}
            <div class="mt-7 grid gap-5 lg:grid-cols-[280px_1fr]">

                {{-- Circular Accuracy Ring --}}
                <div class="flex flex-col items-center justify-center rounded-3xl border border-slate-200 bg-slate-50/80 p-6 dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="relative flex items-center justify-center" style="width:180px;height:180px;">
                        <svg width="180" height="180" viewBox="0 0 180 180" class="-rotate-90">
                            <circle cx="90" cy="90" r="78" fill="none" stroke="currentColor" stroke-width="10" class="text-slate-200 dark:text-slate-700"/>
                            <circle id="result-accuracy-ring"
                                    cx="90" cy="90" r="78" fill="none"
                                    stroke="url(#accuracyGradient)" stroke-width="10"
                                    stroke-linecap="round"
                                    stroke-dasharray="490.09"
                                    stroke-dashoffset="0"
                                    style="transition: stroke-dashoffset 1s ease-out;"/>
                            <defs>
                                <linearGradient id="accuracyGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#10b981"/>
                                    <stop offset="50%" stop-color="#f59e0b"/>
                                    <stop offset="100%" stop-color="#ef4444"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute text-center">
                            <div id="result-accuracy-ring-value" class="text-4xl font-black text-slate-900 dark:text-white">100%</div>
                            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Akurasi</div>
                        </div>
                    </div>
                    <div class="mt-4 grid w-full grid-cols-2 gap-2 text-center text-xs">
                        <div class="rounded-xl bg-white p-2 dark:bg-slate-900/60">
                            <div class="font-bold text-slate-400">Karakter Benar</div>
                            <div id="result-correct-chars" class="text-lg font-black text-emerald-500">0</div>
                        </div>
                        <div class="rounded-xl bg-white p-2 dark:bg-slate-900/60">
                            <div class="font-bold text-slate-400">Total Karakter</div>
                            <div id="result-total-chars" class="text-lg font-black text-indigo-500">0</div>
                        </div>
                    </div>
                </div>

                {{-- Speed Bar Chart --}}
                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-6 dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                            <i class="fas fa-chart-bar mr-2 text-indigo-500"></i> Grafik Kecepatan (WPM)
                        </h3>
                        <span id="result-chart-legend" class="text-[10px] font-semibold text-slate-400">sampel per 500ms</span>
                    </div>
                    <div id="result-speed-chart" class="speed-chart relative h-36 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900/60">
                        <div class="absolute inset-0 flex items-end gap-[2px] p-2" id="speed-bars-container">
                            {{-- bars rendered here --}}
                        </div>
                        <div class="absolute inset-x-0 top-0 h-px bg-slate-200 dark:bg-slate-700"></div>
                        <div class="absolute inset-x-0 top-1/2 h-px border-t border-dashed border-slate-200 dark:border-slate-700"></div>
                        <div class="absolute inset-x-0 bottom-0 h-px bg-slate-200 dark:bg-slate-700"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-[10px] font-semibold text-slate-400">
                        <span>Awal</span>
                        <span>Waktu berjalan</span>
                        <span>Akhir</span>
                    </div>

                    {{-- Additional stats below chart --}}
                    <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <div class="rounded-xl bg-white p-2 text-center dark:bg-slate-900/60">
                            <div class="text-[9px] font-bold uppercase text-slate-400">WPM Tertinggi</div>
                            <div id="result-best-wpm" class="text-lg font-black text-emerald-500">0.0</div>
                        </div>
                        <div class="rounded-xl bg-white p-2 text-center dark:bg-slate-900/60">
                            <div class="text-[9px] font-bold uppercase text-slate-400">WPM Terendah</div>
                            <div id="result-lowest-wpm" class="text-lg font-black text-rose-500">0.0</div>
                        </div>
                        <div class="rounded-xl bg-white p-2 text-center dark:bg-slate-900/60">
                            <div class="text-[9px] font-bold uppercase text-slate-400">Konsistensi</div>
                            <div id="result-consistency" class="text-lg font-black text-violet-500">0.0%</div>
                        </div>
                        <div class="rounded-xl bg-white p-2 text-center dark:bg-slate-900/60">
                            <div class="text-[9px] font-bold uppercase text-slate-400">Rata² Respons</div>
                            <div id="result-avg-response" class="text-lg font-black text-cyan-500">0ms</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Full Detailed Metrics Grid --}}
            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-8">
                <div class="result-card"><span>WPM</span><strong id="result-wpm">0.0</strong></div>
                <div class="result-card"><span>CPM</span><strong id="result-cpm">0.0</strong></div>
                <div class="result-card"><span>Akurasi Karakter</span><strong id="result-accuracy">0.0%</strong></div>
                <div class="result-card"><span>Benar</span><strong id="result-correct">0</strong></div>
                <div class="result-card"><span>Salah</span><strong id="result-wrong">0</strong></div>
                <div class="result-card"><span>Finish</span><strong id="result-finish-time">0.000s</strong></div>
                <div class="result-card"><span>Total Event Key</span><strong id="result-key-events">0</strong></div>
                <div class="result-card"><span>Blokir Paste</span><strong id="result-blocked-paste">0</strong></div>
            </div>

            {{-- Error Pattern Analysis --}}
            <div class="mt-6 grid gap-5 lg:grid-cols-2">
                {{-- Key Response Distribution --}}
                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <i class="fas fa-bolt mr-2 text-amber-500"></i> Distribusi Respons Key
                    </h4>
                    <div class="mt-3 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tercepat</span>
                            <strong id="result-fastest-key" class="text-emerald-500">0ms</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Terlambat</span>
                            <strong id="result-slowest-key" class="text-rose-500">0ms</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Rata-rata</span>
                            <strong id="result-avg-key" class="text-cyan-500">0ms</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Standar Deviasi</span>
                            <strong id="result-std-dev" class="text-violet-500">0ms</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Sampel</span>
                            <strong id="result-key-samples" class="text-slate-600 dark:text-slate-300">0</strong>
                        </div>
                    </div>
                </div>

                {{-- Tab Switch & Anti-Cheat --}}
                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <i class="fas fa-shield mr-2 text-indigo-500"></i> Integritas Sesi
                    </h4>
                    <div class="mt-3 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tab Beralih</span>
                            <strong id="result-tab-switches" class="text-amber-500">0</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Blokir Clipboard</span>
                            <strong id="result-clipboard-blocks" class="text-violet-500">0</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Blokir Drop</span>
                            <strong id="result-drop-blocks" class="text-cyan-500">0</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Total Karakter Benar</span>
                            <strong id="result-total-correct-chars" class="text-emerald-500">0</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Total Karakter Diketik</span>
                            <strong id="result-total-typed-chars" class="text-indigo-500">0</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-7 flex flex-wrap justify-center gap-2">
                <button id="result-play-again" type="button" class="control-btn control-btn-primary">
                    <i class="fas fa-redo"></i> Main Lagi
                </button>
                <a href="{{ route('owner.game.games.show', $game) }}" class="control-btn control-btn-secondary">
                    <i class="fas fa-house"></i> Kembali
                </a>
            </div>
        </section>
    </div>
</div>

{{-- ======================== JAVASCRIPT ======================== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const DURATION_MS = {{ $durationSeconds * 1000 }};
    const GAME_ID = {{ (int)$game->id }};
    const TOKEN = @json($token);
    const RESULT_URL = @json(route('owner.game.games.submit-result'));
    const COMPETITION_ENDPOINT = @json($competitionEndpoint ?? null);

    const page = document.getElementById('typing-page');
    const arenaLayout = document.getElementById('arena-layout');
    const root = document.documentElement;

    // ======================== ADAPTIVE DEVICE DETECTION ========================
    const adaptive = {
        mediaMobile: window.matchMedia('(max-width: 639px)'),
        mediaSmallTablet: window.matchMedia('(min-width: 640px) and (max-width: 767px)'),
        mediaLargeTablet: window.matchMedia('(min-width: 768px) and (max-width: 1100px)'),
        mediaDesktop: window.matchMedia('(min-width: 1101px)'),
        mediaTouch: window.matchMedia('(pointer: coarse)'),
        mediaLandscape: window.matchMedia('(orientation: landscape)'),
        mediaReducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)'),
        mediaFoldable: window.matchMedia('(device-posture: folded)'),
        mediaNarrow: window.matchMedia('(max-width: 767px)'),

        profile() {
            const width = window.innerWidth;
            const height = window.innerHeight;
            const touch = this.mediaTouch.matches ||
                ('ontouchstart' in window) ||
                (navigator.maxTouchPoints || 0) > 0;

            let device = 'desktop';
            let layoutMode = 'full';

            if (width <= 639) {
                device = 'mobile';
                layoutMode = 'minimal';
            } else if (width <= 767) {
                device = 'small-tablet';
                layoutMode = 'minimal';
            } else if (width <= 1100) {
                device = 'tablet';
                layoutMode = (width <= 900 && touch) ? 'minimal' : 'full';
            } else {
                device = 'desktop';
                layoutMode = 'full';
            }

            return {
                device,
                layoutMode,
                touch,
                width,
                height,
                landscape: width > height,
                compactHeight: height <= 760,
                reducedMotion: this.mediaReducedMotion.matches,
                isFoldable: this.mediaFoldable.matches,
                isNarrow: width <= 767,
                isUltraNarrow: width <= 374,
                isLargePhone: width >= 375 && width <= 639,
                isTablet: width >= 640 && width <= 1100,
                isDesktop: width > 1100,
                dpr: window.devicePixelRatio || 1,
                orientation: width > height ? 'landscape' : 'portrait'
            };
        },

        apply() {
            const p = this.profile();
            document.body.dataset.typingDevice = p.device;
            document.body.dataset.typingLayout = p.layoutMode;
            document.body.dataset.typingTouch = p.touch ? 'true' : 'false';
            document.body.dataset.typingLandscape = p.landscape ? 'true' : 'false';
            document.body.dataset.typingCompact = p.compactHeight ? 'true' : 'false';
            document.body.dataset.typingNarrow = p.isNarrow ? 'true' : 'false';

            if (page) {
                page.dataset.device = p.device;
                page.dataset.layout = p.layoutMode;
                page.dataset.touch = p.touch ? 'true' : 'false';
                page.dataset.landscape = p.landscape ? 'true' : 'false';
                page.dataset.narrow = p.isNarrow ? 'true' : 'false';
            }

            if (arenaLayout) {
                arenaLayout.dataset.device = p.device;
                arenaLayout.dataset.layout = p.layoutMode;
                arenaLayout.dataset.touch = p.touch ? 'true' : 'false';
            }

            root.style.setProperty('--typing-gutter', `${Math.max(8, Math.min(28, p.width * 0.018))}px`);
            root.style.setProperty('--typing-font-scale', `${Math.max(0.88, Math.min(1.15, p.width / 1200))}`);
            root.style.setProperty('--typing-panel-height', `${Math.max(150, Math.min(430, p.height * 0.52))}px`);
            root.style.setProperty('--typing-metric-cols', p.layoutMode === 'minimal' ? '2' : '4');

            // Toggle desktop-only metric cards
            document.querySelectorAll('[data-desktop-only]').forEach(card => {
                if (p.layoutMode === 'minimal') {
                    card.style.display = 'none';
                } else {
                    card.style.display = '';
                }
            });

            // Adjust metrics grid class
            const metricsGrid = document.querySelector('.metrics-grid');
            if (metricsGrid) {
                if (p.layoutMode === 'minimal' && p.isUltraNarrow) {
                    metricsGrid.style.gridTemplateColumns = 'repeat(2, minmax(0, 1fr))';
                } else if (p.layoutMode === 'minimal') {
                    metricsGrid.style.gridTemplateColumns = 'repeat(2, minmax(0, 1fr))';
                } else {
                    metricsGrid.style.gridTemplateColumns = '';
                }
            }

            return p;
        }
    };

    adaptive.apply();
    window.addEventListener('resize', () => adaptive.apply(), { passive: true });
    window.addEventListener('orientationchange', () => window.setTimeout(() => adaptive.apply(), 120), { passive: true });

    [
        adaptive.mediaMobile,
        adaptive.mediaSmallTablet,
        adaptive.mediaLargeTablet,
        adaptive.mediaDesktop,
        adaptive.mediaTouch,
        adaptive.mediaLandscape,
        adaptive.mediaReducedMotion,
        adaptive.mediaFoldable,
        adaptive.mediaNarrow
    ].forEach(media => {
        if (typeof media.addEventListener === 'function') {
            media.addEventListener('change', () => adaptive.apply());
        } else if (typeof media.addListener === 'function') {
            media.addListener(() => adaptive.apply());
        }
    });

    // Device Posture API (for fold devices)
    if (navigator.devicePosture) {
        navigator.devicePosture.addEventListener('change', () => adaptive.apply());
    }

    // ======================== TEXT DATA ========================
    const rawText = @json($customText);
    const fallbackText = 'Ketik teks ini untuk berlatih mengetik dengan presisi tinggi.';
    const sourceText = String(rawText || fallbackText).trim();

    const words = sourceText
        .split(/\s+/u)
        .map(word => word.trim())
        .filter(Boolean);

    // ======================== DOM REFERENCES ========================
    const el = {
        app: document.getElementById('typing-app'),
        result: document.getElementById('result-section'),
        text: document.getElementById('text-display'),
        input: document.getElementById('typing-input'),
        timer: document.getElementById('typing-timer'),
        wpm: document.getElementById('typing-wpm'),
        cpm: document.getElementById('typing-cpm'),
        accuracy: document.getElementById('typing-accuracy'),
        wordCount: document.getElementById('typing-word-count'),
        totalWords: document.getElementById('total-words'),
        correct: document.getElementById('typing-correct'),
        wrong: document.getElementById('typing-wrong'),
        keyResponse: document.getElementById('typing-key-response'),
        activity: document.getElementById('typing-activity'),
        activitySub: document.getElementById('typing-activity-sub'),
        speed: document.getElementById('typing-speed'),
        progress: document.getElementById('progress-bar'),
        statusDot: document.getElementById('status-dot'),
        statusText: document.getElementById('status-text'),
        inputState: document.getElementById('input-state-pill'),
        start: document.getElementById('typing-start-btn'),
        reset: document.getElementById('typing-reset-btn'),
        resultPlayAgain: document.getElementById('result-play-again'),

        // Result fields
        resultSummary: document.getElementById('result-summary'),
        resultGrade: document.getElementById('result-grade'),
        resultWpm: document.getElementById('result-wpm'),
        resultCpm: document.getElementById('result-cpm'),
        resultAccuracy: document.getElementById('result-accuracy'),
        resultCorrect: document.getElementById('result-correct'),
        resultWrong: document.getElementById('result-wrong'),
        resultFinishTime: document.getElementById('result-finish-time'),
        resultKeyEvents: document.getElementById('result-key-events'),
        resultBlockedPaste: document.getElementById('result-blocked-paste'),
        resultCorrectChars: document.getElementById('result-correct-chars'),
        resultTotalChars: document.getElementById('result-total-chars'),
        resultAccuracyRing: document.getElementById('result-accuracy-ring'),
        resultAccuracyRingValue: document.getElementById('result-accuracy-ring-value'),
        resultBestWpm: document.getElementById('result-best-wpm'),
        resultLowestWpm: document.getElementById('result-lowest-wpm'),
        resultConsistency: document.getElementById('result-consistency'),
        resultAvgResponse: document.getElementById('result-avg-response'),
        resultFastestKey: document.getElementById('result-fastest-key'),
        resultSlowestKey: document.getElementById('result-slowest-key'),
        resultAvgKey: document.getElementById('result-avg-key'),
        resultStdDev: document.getElementById('result-std-dev'),
        resultKeySamples: document.getElementById('result-key-samples'),
        resultTabSwitches: document.getElementById('result-tab-switches'),
        resultClipboardBlocks: document.getElementById('result-clipboard-blocks'),
        resultDropBlocks: document.getElementById('result-drop-blocks'),
        resultTotalCorrectChars: document.getElementById('result-total-correct-chars'),
        resultTotalTypedChars: document.getElementById('result-total-typed-chars'),
        speedBarsContainer: document.getElementById('speed-bars-container'),
        resultChartLegend: document.getElementById('result-chart-legend'),

        competitionList: document.getElementById('competition-list'),
        competitionSync: document.getElementById('competition-sync-status'),
        topbarDuration: document.getElementById('topbar-duration')
    };

    el.totalWords.textContent = words.length;
    el.topbarDuration.textContent = DURATION_MS / 1000;

    const player = {
        id: {{ (int)(auth()->id() ?? 0) }},
        name: @json($playerName),
        role: @json($playerRole)
    };

    // ======================== STATE MANAGEMENT ========================
    const state = {
        started: false,
        finished: false,
        currentWord: 0,
        correctWords: 0,
        wrongWords: 0,
        correctChars: 0,
        typedChars: 0,
        liveCorrectChars: 0,
        liveTypedChars: 0,
        committedChars: 0,
        startPerf: 0,
        lastPerf: 0,
        lastKeyPerf: 0,
        lastInputPerf: 0,
        lastKeyResponseMs: 0,
        keyResponseSamples: [],
        allKeyResponseTimes: [],
        totalKeyEvents: 0,
        rejectedPasteAttempts: 0,
        elapsedMs: 0,
        remainingMs: DURATION_MS,
        raf: 0,
        submitDone: false,
        syncInFlight: false,
        keySequence: 0,
        speedHistory: [],
        lastSpeedSamplePerf: 0,
        wordTimings: [],
        currentWordStartPerf: 0,
        maxWpm: 0,
        minWpm: Infinity,
        antiCheat: {
            blockedPaste: 0,
            blockedDrop: 0,
            blockedClipboard: 0,
            tabSwitches: 0
        }
    };

    // ======================== UTILITY FUNCTIONS ========================
    function formatMs(ms) {
        const safe = Math.max(0, ms);
        const min = Math.floor(safe / 60000);
        const sec = Math.floor((safe % 60000) / 1000);
        const milli = Math.floor(safe % 1000);
        return `${String(min).padStart(2,'0')}:${String(sec).padStart(2,'0')}.${String(milli).padStart(3,'0')}`;
    }

    function formatSeconds(ms) {
        return `${(Math.max(0, ms) / 1000).toFixed(3)}s`;
    }

    function currentElapsed() {
        if (!state.started) return state.elapsedMs;
        return Math.min(DURATION_MS, performance.now() - state.startPerf);
    }

    function splitChars(value) {
        return Array.from(String(value ?? ''));
    }

    function getCurrentExpected() {
        return words[state.currentWord] || '';
    }

    function getLiveCharacterCounts() {
        const typed = splitChars(el.input?.value || '');
        const expected = splitChars(getCurrentExpected());
        let correct = 0;
        for (let i = 0; i < Math.min(expected.length, typed.length); i++) {
            if (expected[i] === typed[i]) correct++;
        }
        return { typed: typed.length, correct };
    }

    function getTypingActivity(now = performance.now()) {
        if (!state.started || state.finished) {
            return { key: 'READY', label: 'SIAP', sub: 'menunggu permainan' };
        }
        const last = state.lastKeyPerf || state.startPerf;
        const idleMs = Math.max(0, now - last);
        if (idleMs <= 350) {
            return { key: 'TYPING', label: 'MENGETIK', sub: `key aktif · ${Math.round(idleMs)}ms` };
        }
        if (idleMs <= 1100) {
            return { key: 'PAUSE', label: 'PAUSE', sub: `jeda · ${Math.round(idleMs)}ms` };
        }
        return { key: 'IDLE', label: 'BERHENTI', sub: `tidak mengetik · ${(idleMs / 1000).toFixed(1)}s` };
    }

    function updateTypingActivity(now = performance.now()) {
        const activity = getTypingActivity(now);
        if (el.activity) {
            el.activity.textContent = activity.label;
            el.activity.dataset.state = activity.key;
        }
        if (el.activitySub) el.activitySub.textContent = activity.sub;
        if (el.keyResponse) {
            el.keyResponse.textContent = state.lastKeyResponseMs > 0 ? `${state.lastKeyResponseMs.toFixed(0)}ms` : '0ms';
        }
    }

    function getMetrics() {
        const elapsedMs = Math.max(1, currentElapsed());
        const elapsedMin = elapsedMs / 60000;
        const live = getLiveCharacterCounts();
        state.liveCorrectChars = live.correct;
        state.liveTypedChars = live.typed;

        const grossTypedChars = state.typedChars + state.liveTypedChars;
        const correctChars = state.correctChars + state.liveCorrectChars;
        const accuracy = grossTypedChars > 0
            ? Math.max(0, Math.min(100, (correctChars / grossTypedChars) * 100))
            : 100;

        const grossWpm = grossTypedChars / 5 / elapsedMin;
        const netWpm = correctChars / 5 / elapsedMin;
        const grossCpm = grossTypedChars / elapsedMin;
        const cpm = correctChars / elapsedMin;
        const speed = correctChars / (elapsedMs / 1000);
        const progress = words.length ? (state.currentWord / words.length) * 100 : 0;

        return {
            elapsedMs,
            cpm,
            grossCpm,
            grossWpm,
            wpm: netWpm,
            accuracy,
            speed,
            progress
        };
    }

    function setStatus(text, type = 'idle') {
        const meta = {
            idle: { dot: 'bg-slate-400', input: 'READY' },
            running: { dot: 'bg-emerald-500', input: 'LIVE' },
            error: { dot: 'bg-rose-500', input: 'CHECK' },
            finished: { dot: 'bg-indigo-500', input: 'DONE' }
        }[type] || { dot: 'bg-slate-400', input: 'READY' };

        el.statusDot.className = `h-2 w-2 rounded-full ${meta.dot}`;
        el.statusText.textContent = text;
        el.inputState.textContent = meta.input;
    }

    // ======================== TEXT RENDERING ========================
    function createWordNode(word, index) {
        const wrap = document.createElement('span');
        wrap.className = 'typing-word';
        wrap.dataset.wordIndex = String(index);
        splitChars(word).forEach((character, i) => {
            const ch = document.createElement('span');
            ch.className = 'typing-char';
            ch.textContent = character;
            ch.dataset.charIndex = String(i);
            wrap.appendChild(ch);
        });
        if (index === 0) wrap.classList.add('is-current');
        return wrap;
    }

    function renderText() {
        const frag = document.createDocumentFragment();
        words.forEach((word, index) => {
            frag.appendChild(createWordNode(word, index));
            if (index < words.length - 1) {
                const space = document.createElement('span');
                space.className = 'typing-space';
                space.textContent = ' ';
                frag.appendChild(space);
            }
        });
        el.text.replaceChildren(frag);
    }

    function getWordNode(index) {
        return el.text.querySelector(`.typing-word[data-word-index="${index}"]`);
    }

    function setCurrentWordVisual(index) {
        el.text.querySelectorAll('.typing-word').forEach(node => node.classList.remove('is-current'));
        const node = getWordNode(index);
        if (node) {
            node.classList.add('is-current');
            const behavior = adaptive.mediaReducedMotion.matches || adaptive.mediaTouch.matches ? 'auto' : 'smooth';
            node.scrollIntoView({
                behavior,
                block: adaptive.profile().device === 'mobile' ? 'center' : 'nearest',
                inline: 'nearest'
            });
        }
    }

    function compareWord(expected, typed) {
        const expectedChars = splitChars(expected);
        const typedChars = splitChars(typed);
        let correct = 0;
        for (let i = 0; i < Math.max(expectedChars.length, typedChars.length); i++) {
            if (expectedChars[i] !== undefined && typedChars[i] !== undefined && expectedChars[i] === typedChars[i]) {
                correct++;
            }
        }
        return { exact: expected === typed, correctChars: correct };
    }

    function paintCurrentWord(typed) {
        const node = getWordNode(state.currentWord);
        if (!node) return;
        const expectedChars = splitChars(getCurrentExpected());
        const typedChars = splitChars(typed);
        const chars = node.querySelectorAll('.typing-char');

        chars.forEach((charNode, index) => {
            charNode.classList.remove('is-correct', 'is-wrong', 'is-missing');
            if (typedChars[index] === undefined) {
                charNode.classList.add('is-missing');
            } else if (typedChars[index] === expectedChars[index]) {
                charNode.classList.add('is-correct');
            } else {
                charNode.classList.add('is-wrong');
            }
        });

        let prefixValid = true;
        for (let i = 0; i < typedChars.length; i++) {
            if (typedChars[i] !== expectedChars[i]) { prefixValid = false; break; }
        }
        node.classList.toggle('has-live-error', typedChars.length > 0 && !prefixValid);
    }

    function commitWord() {
        const typed = el.input.value;
        const expected = words[state.currentWord] || '';
        if (!typed) return;

        const result = compareWord(expected, typed);
        const typedCharCount = splitChars(typed).length;
        state.correctChars += result.correctChars;
        state.typedChars += typedCharCount;
        state.liveCorrectChars = 0;
        state.liveTypedChars = 0;
        state.committedChars += expected.length;

        // Word timing
        if (state.currentWordStartPerf > 0) {
            const wordTimeMs = performance.now() - state.currentWordStartPerf;
            state.wordTimings.push({ wordIndex: state.currentWord, timeMs: wordTimeMs, correct: result.exact });
        }

        const node = getWordNode(state.currentWord);
        if (result.exact) {
            state.correctWords++;
            if (node) { node.classList.remove('is-current', 'has-live-error'); node.classList.add('is-complete'); }
        } else {
            state.wrongWords++;
            if (node) { node.classList.remove('is-current'); node.classList.add('is-wrong-word'); }
        }

        state.currentWord++;
        el.input.value = '';

        if (state.currentWord >= words.length) {
            finishGame('🏁 Semua kata selesai.');
            return;
        }

        state.currentWordStartPerf = performance.now();
        setCurrentWordVisual(state.currentWord);
        setStatus(result.exact ? '✅ Kata benar — lanjut.' : '❌ Kata salah — lanjut.', result.exact ? 'running' : 'error');
    }

    // ======================== SPEED HISTORY TRACKING ========================
    function sampleSpeedHistory(now = performance.now()) {
        if (!state.started || state.finished) return;
        if (state.lastSpeedSamplePerf === 0) state.lastSpeedSamplePerf = state.startPerf;

        const elapsedMs = Math.min(DURATION_MS, now - state.startPerf);
        const elapsedMin = elapsedMs / 60000;
        const live = getLiveCharacterCounts();
        const grossTypedChars = state.typedChars + live.typed;
        const correctChars = state.correctChars + live.correct;
        const wpm = correctChars / 5 / Math.max(0.001, elapsedMin);
        const cpm = correctChars / Math.max(0.001, elapsedMin);
        const accuracy = grossTypedChars > 0 ? (correctChars / grossTypedChars) * 100 : 100;

        state.speedHistory.push({
            elapsedMs: Math.round(elapsedMs),
            wpm: Math.round(wpm * 10) / 10,
            cpm: Math.round(cpm * 10) / 10,
            accuracy: Math.round(accuracy * 10) / 10,
            keyResponseAvg: state.keyResponseSamples.length
                ? state.keyResponseSamples.reduce((a, b) => a + b, 0) / state.keyResponseSamples.length
                : 0
        });

        // Track min/max WPM
        if (wpm > state.maxWpm) state.maxWpm = wpm;
        if (wpm < state.minWpm && wpm > 0) state.minWpm = wpm;
    }

    function updateStats() {
        const metrics = getMetrics();
        el.timer.textContent = formatMs(Math.max(0, DURATION_MS - metrics.elapsedMs));
        el.wpm.textContent = Number.isFinite(metrics.wpm) ? metrics.wpm.toFixed(1) : '0.0';
        el.cpm.textContent = Number.isFinite(metrics.cpm) ? metrics.cpm.toFixed(1) : '0.0';
        el.accuracy.textContent = `${metrics.accuracy.toFixed(1)}%`;
        el.wordCount.textContent = state.currentWord;
        el.correct.textContent = state.correctWords;
        el.wrong.textContent = state.wrongWords;
        el.speed.textContent = `${metrics.speed.toFixed(1)} char/dtk`;
        el.progress.style.width = `${Math.min(100, metrics.progress)}%`;
        updateTypingActivity();

        if (metrics.accuracy < 80) {
            el.accuracy.classList.add('text-rose-600', 'dark:text-rose-400');
            el.accuracy.classList.remove('text-amber-600', 'dark:text-amber-400');
        } else {
            el.accuracy.classList.remove('text-rose-600', 'dark:text-rose-400');
            el.accuracy.classList.add('text-amber-600', 'dark:text-amber-400');
        }
    }

    function tick(now) {
        if (!state.started || state.finished) return;
        state.lastPerf = now;
        state.elapsedMs = Math.min(DURATION_MS, now - state.startPerf);
        state.remainingMs = Math.max(0, DURATION_MS - state.elapsedMs);

        updateStats();
        updateCompetitionLocal();

        // Sample speed history every ~500ms
        if (now - state.lastSpeedSamplePerf >= 500) {
            sampleSpeedHistory(now);
            state.lastSpeedSamplePerf = now;
        }

        if (state.remainingMs <= 0) {
            finishGame('⏰ Waktu habis.');
            return;
        }
        state.raf = requestAnimationFrame(tick);
    }

    // ======================== RESULT RENDERING ========================
    function calculateGrade(wpm, accuracy) {
        if (wpm >= 80 && accuracy >= 95) return { grade: 'A+', color: 'bg-emerald-500', label: 'Luar Biasa' };
        if (wpm >= 60 && accuracy >= 90) return { grade: 'A', color: 'bg-emerald-500', label: 'Sangat Baik' };
        if (wpm >= 40 && accuracy >= 85) return { grade: 'B+', color: 'bg-cyan-500', label: 'Baik' };
        if (wpm >= 30 && accuracy >= 80) return { grade: 'B', color: 'bg-cyan-500', label: 'Cukup Baik' };
        if (wpm >= 20 && accuracy >= 70) return { grade: 'C', color: 'bg-amber-500', label: 'Perlu Latihan' };
        return { grade: 'D', color: 'bg-rose-500', label: 'Terus Berlatih' };
    }

    function calculateStdDev(values) {
        if (values.length < 2) return 0;
        const mean = values.reduce((a, b) => a + b, 0) / values.length;
        const sqDiffs = values.map(v => Math.pow(v - mean, 2));
        return Math.sqrt(sqDiffs.reduce((a, b) => a + b, 0) / (values.length - 1));
    }

    function renderSpeedChart() {
        const container = el.speedBarsContainer;
        container.replaceChildren();

        if (!state.speedHistory.length) {
            container.innerHTML = '<div class="flex h-full w-full items-center justify-center text-xs text-slate-400">Tidak ada data kecepatan</div>';
            return;
        }

        const maxWpm = Math.max(...state.speedHistory.map(s => s.wpm), 1);
        const barCount = Math.min(state.speedHistory.length, 60);
        const samples = state.speedHistory.slice(-barCount);

        samples.forEach(sample => {
            const bar = document.createElement('div');
            const heightPct = Math.max(8, Math.min(100, (sample.wpm / maxWpm) * 100));
            bar.style.height = `${heightPct}%`;
            bar.style.flex = '1';
            bar.style.minWidth = '2px';
            bar.style.maxWidth = '8px';
            bar.style.borderRadius = '2px 2px 0 0';
            bar.style.background = sample.wpm >= 60
                ? 'linear-gradient(180deg, #10b981, #059669)'
                : sample.wpm >= 30
                    ? 'linear-gradient(180deg, #f59e0b, #d97706)'
                    : 'linear-gradient(180deg, #ef4444, #dc2626)';
            bar.title = `WPM: ${sample.wpm.toFixed(1)} · ${(sample.elapsedMs / 1000).toFixed(1)}s`;
            container.appendChild(bar);
        });

        el.resultChartLegend.textContent = `${samples.length} sampel · maks ${maxWpm.toFixed(1)} WPM`;
    }

    function renderResult(metrics) {
        const gradeInfo = calculateGrade(metrics.wpm, metrics.accuracy);
        const allKeyTimes = state.allKeyResponseTimes.length ? state.allKeyResponseTimes : state.keyResponseSamples;
        const avgKey = allKeyTimes.length ? allKeyTimes.reduce((a, b) => a + b, 0) / allKeyTimes.length : 0;
        const fastestKey = allKeyTimes.length ? Math.min(...allKeyTimes) : 0;
        const slowestKey = allKeyTimes.length ? Math.max(...allKeyTimes) : 0;
        const stdDev = calculateStdDev(allKeyTimes);
        const consistency = state.maxWpm > 0 ? ((state.minWpm > 0 ? state.minWpm : 0) / state.maxWpm) * 100 : 100;

        // Basic fields
        el.resultWpm.textContent = metrics.wpm.toFixed(1);
        el.resultCpm.textContent = metrics.cpm.toFixed(1);
        el.resultAccuracy.textContent = `${metrics.accuracy.toFixed(1)}%`;
        el.resultCorrect.textContent = state.correctWords;
        el.resultWrong.textContent = state.wrongWords;
        el.resultFinishTime.textContent = formatSeconds(state.elapsedMs);
        el.resultKeyEvents.textContent = state.totalKeyEvents;
        el.resultBlockedPaste.textContent = state.rejectedPasteAttempts;
        el.resultSummary.textContent = `${state.currentWord}/${words.length} kata selesai · ${formatSeconds(state.elapsedMs)} · Grade ${gradeInfo.grade}`;

        // Grade
        el.resultGrade.textContent = `${gradeInfo.grade} · ${gradeInfo.label}`;
        el.resultGrade.className = `mt-3 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-lg font-black text-white ${gradeInfo.color}`;

        // Accuracy ring
        const circumference = 2 * Math.PI * 78;
        const ringOffset = circumference * (1 - metrics.accuracy / 100);
        el.resultAccuracyRing.style.strokeDasharray = String(circumference);
        el.resultAccuracyRing.style.strokeDashoffset = String(ringOffset);
        el.resultAccuracyRingValue.textContent = `${metrics.accuracy.toFixed(1)}%`;

        // Character stats
        el.resultCorrectChars.textContent = state.correctChars;
        el.resultTotalChars.textContent = state.typedChars;
        el.resultTotalCorrectChars.textContent = state.correctChars;
        el.resultTotalTypedChars.textContent = state.typedChars;

        // Speed stats
        el.resultBestWpm.textContent = state.maxWpm.toFixed(1);
        el.resultLowestWpm.textContent = state.minWpm === Infinity ? '0.0' : state.minWpm.toFixed(1);
        el.resultConsistency.textContent = `${consistency.toFixed(1)}%`;
        el.resultAvgResponse.textContent = `${avgKey.toFixed(0)}ms`;

        // Key response stats
        el.resultFastestKey.textContent = `${fastestKey.toFixed(0)}ms`;
        el.resultSlowestKey.textContent = `${slowestKey.toFixed(0)}ms`;
        el.resultAvgKey.textContent = `${avgKey.toFixed(0)}ms`;
        el.resultStdDev.textContent = `${stdDev.toFixed(0)}ms`;
        el.resultKeySamples.textContent = allKeyTimes.length;

        // Anti-cheat
        el.resultTabSwitches.textContent = state.antiCheat.tabSwitches;
        el.resultClipboardBlocks.textContent = state.antiCheat.blockedClipboard + state.antiCheat.blockedPaste;
        el.resultDropBlocks.textContent = state.antiCheat.blockedDrop;

        // Speed chart
        renderSpeedChart();

        // Show result section
        el.app.classList.add('hidden');
        el.result.classList.remove('hidden');
    }

    // ======================== GAME CONTROL ========================
    function resetState() {
        cancelAnimationFrame(state.raf);
        state.started = false;
        state.finished = false;
        state.currentWord = 0;
        state.correctWords = 0;
        state.wrongWords = 0;
        state.correctChars = 0;
        state.typedChars = 0;
        state.liveCorrectChars = 0;
        state.liveTypedChars = 0;
        state.committedChars = 0;
        state.startPerf = 0;
        state.lastPerf = 0;
        state.lastKeyPerf = 0;
        state.lastInputPerf = 0;
        state.lastKeyResponseMs = 0;
        state.keyResponseSamples = [];
        state.allKeyResponseTimes = [];
        state.totalKeyEvents = 0;
        state.rejectedPasteAttempts = 0;
        state.elapsedMs = 0;
        state.remainingMs = DURATION_MS;
        state.submitDone = false;
        state.syncInFlight = false;
        state.keySequence = 0;
        state.speedHistory = [];
        state.lastSpeedSamplePerf = 0;
        state.wordTimings = [];
        state.currentWordStartPerf = 0;
        state.maxWpm = 0;
        state.minWpm = Infinity;
        state.antiCheat = { blockedPaste: 0, blockedDrop: 0, blockedClipboard: 0, tabSwitches: 0 };

        el.input.value = '';
        el.input.disabled = true;
        el.start.disabled = false;
        el.start.innerHTML = '<i class="fas fa-play"></i> Mulai';
        el.start.classList.remove('control-btn-danger');
        el.start.classList.add('control-btn-primary');

        renderText();
        updateStats();
        setStatus('Klik Mulai untuk memulai.', 'idle');
        el.result.classList.add('hidden');
        el.app.classList.remove('hidden');

        renderCompetition([{
            rank: 1, id: player.id, name: player.name, role: player.role,
            progress: 0, wpm: 0, accuracy: 100, status: 'READY', me: true
        }]);
    }

    function startGame() {
        if (state.started) return;
        state.started = true;
        state.finished = false;
        state.startPerf = performance.now();
        state.lastPerf = state.startPerf;
        state.lastKeyPerf = 0;
        state.lastInputPerf = 0;
        state.lastKeyResponseMs = 0;
        state.keyResponseSamples = [];
        state.allKeyResponseTimes = [];
        state.elapsedMs = 0;
        state.remainingMs = DURATION_MS;
        state.speedHistory = [];
        state.lastSpeedSamplePerf = state.startPerf;
        state.currentWordStartPerf = performance.now();
        state.maxWpm = 0;
        state.minWpm = Infinity;

        el.input.disabled = false;
        el.input.focus({ preventScroll: true });
        el.start.innerHTML = '<i class="fas fa-stop"></i> Selesai';
        el.start.classList.remove('control-btn-primary');
        el.start.classList.add('control-btn-danger');
        setStatus('🟢 Sedang mengetik...', 'running');

        cancelAnimationFrame(state.raf);
        state.raf = requestAnimationFrame(tick);
        updateStats();
        syncCompetition();
    }

    function finishGame(reason = 'Game selesai.') {
        if (state.finished) return;
        state.finished = true;
        state.started = false;
        cancelAnimationFrame(state.raf);
        state.elapsedMs = Math.min(DURATION_MS, state.elapsedMs || currentElapsed());
        state.remainingMs = Math.max(0, DURATION_MS - state.elapsedMs);

        // Final speed sample
        sampleSpeedHistory(performance.now());

        el.input.disabled = true;
        el.start.disabled = false;
        el.start.innerHTML = '<i class="fas fa-redo"></i> Main Lagi';
        el.start.classList.remove('control-btn-danger');
        el.start.classList.add('control-btn-primary');

        const metrics = getMetrics();
        setStatus(reason, 'finished');
        renderResult(metrics);
        syncCompetition(true);
        submitResult(metrics);
    }

    // ======================== COMPETITION SYNC ========================
    function updateCompetitionLocal() {
        const metrics = getMetrics();
        renderCompetition([{
            rank: 1,
            id: player.id,
            name: player.name,
            role: player.role,
            progress: metrics.progress,
            wpm: metrics.wpm,
            accuracy: metrics.accuracy,
            gross_wpm: metrics.grossWpm,
            avg_key_response_ms: state.keyResponseSamples.length
                ? state.keyResponseSamples.reduce((a, b) => a + b, 0) / state.keyResponseSamples.length
                : 0,
            typing_activity: getTypingActivity().label,
            status: state.finished ? 'FINISHED' : (state.started ? 'LIVE' : 'READY'),
            me: true
        }]);
    }

    function renderCompetition(rows) {
        const sorted = [...rows].sort((a, b) => {
            const pa = Number(a.progress || 0);
            const pb = Number(b.progress || 0);
            if (pb !== pa) return pb - pa;
            const ta = String(a.status || '').toUpperCase() === 'FINISHED' ? 0 : 1;
            const tb = String(b.status || '').toUpperCase() === 'FINISHED' ? 0 : 1;
            if (ta !== tb) return ta - tb;
            const aw = Number(a.wpm || 0);
            const bw = Number(b.wpm || 0);
            if (bw !== aw) return bw - aw;
            return Number(b.accuracy || 0) - Number(a.accuracy || 0);
        }).slice(0, 10);

        el.competitionList.replaceChildren();

        sorted.forEach((row, index) => {
            const pct = Math.max(0, Math.min(100, Number(row.progress || 0)));
            const card = document.createElement('div');
            card.className = `competition-row ${row.me ? 'is-me' : ''}`;

            const rank = document.createElement('div');
            rank.className = 'competition-rank';
            rank.textContent = String(index + 1);

            const body = document.createElement('div');
            body.className = 'min-w-0 flex-1';

            const top = document.createElement('div');
            top.className = 'flex items-center justify-between gap-2';

            const name = document.createElement('div');
            name.className = 'truncate text-sm font-extrabold text-slate-800 dark:text-white';
            name.textContent = row.me ? `${row.name} (Anda)` : (row.name || 'Pemain');

            const status = document.createElement('span');
            status.className = 'text-[10px] font-black text-slate-400';
            status.textContent = row.status || 'LIVE';

            top.append(name, status);

            const role = document.createElement('div');
            role.className = 'truncate text-[10px] font-semibold text-indigo-500 dark:text-indigo-400';
            role.textContent = row.role || 'Pemain';

            const barWrap = document.createElement('div');
            barWrap.className = 'mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700';

            const bar = document.createElement('div');
            bar.className = 'h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400 transition-[width] duration-300';
            bar.style.width = `${pct}%`;
            barWrap.appendChild(bar);

            const metricsLine = document.createElement('div');
            metricsLine.className = 'mt-1 flex items-center justify-between text-[10px] font-bold text-slate-400';
            const left = document.createElement('span');
            left.textContent = `${pct.toFixed(0)}%`;
            const right = document.createElement('span');
            right.textContent = `${Number(row.wpm || 0).toFixed(1)} WPM · ${Number(row.accuracy || 100).toFixed(1)}% · ${row.typing_activity || row.status || 'LIVE'}`;
            metricsLine.append(left, right);

            body.append(top, role, barWrap, metricsLine);
            card.append(rank, body);
            el.competitionList.appendChild(card);
        });

        if (!sorted.length) {
            el.competitionList.innerHTML = '<div class="rounded-2xl border border-dashed border-slate-300 p-4 text-center text-xs text-slate-400 dark:border-slate-700">Belum ada data peserta.</div>';
        }
    }

    async function syncCompetition(force = false) {
        if (!COMPETITION_ENDPOINT) {
            el.competitionSync.textContent = 'Mode lokal aktif · endpoint realtime belum dikonfigurasi.';
            return;
        }
        if (state.syncInFlight && !force) return;
        state.syncInFlight = true;

        try {
            const metrics = getMetrics();
            const qs = new URLSearchParams({
                game_id: String(GAME_ID),
                token: String(TOKEN),
                player_id: String(player.id),
                name: String(player.name),
                role: String(player.role),
                progress: String(metrics.progress),
                wpm: String(metrics.wpm),
                accuracy: String(metrics.accuracy),
                gross_wpm: String(metrics.grossWpm),
                avg_key_response_ms: String(
                    state.keyResponseSamples.length
                        ? state.keyResponseSamples.reduce((a, b) => a + b, 0) / state.keyResponseSamples.length
                        : 0
                ),
                typing_activity: getTypingActivity().label,
                status: state.finished ? 'FINISHED' : (state.started ? 'LIVE' : 'READY')
            });

            const url = `${COMPETITION_ENDPOINT}${COMPETITION_ENDPOINT.includes('?') ? '&' : '?'}${qs.toString()}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
                credentials: 'same-origin',
                keepalive: true
            });

            if (!response.ok) throw new Error('competition sync failed');
            const data = await response.json();

            if (Array.isArray(data.players)) renderCompetition(data.players);
            el.competitionSync.textContent = `${Array.isArray(data.players) ? data.players.length : 1} peserta · sinkron ${new Date().toLocaleTimeString()}`;
        } catch (error) {
            el.competitionSync.textContent = 'Live ranking sementara tidak tersambung — game tetap berjalan lokal.';
        } finally {
            state.syncInFlight = false;
        }

        if (!force && state.started) {
            window.setTimeout(() => syncCompetition(false), 1200);
        }
    }

    async function submitResult(metrics) {
        if (state.submitDone) return;
        state.submitDone = true;

        const allKeyTimes = state.allKeyResponseTimes.length ? state.allKeyResponseTimes : state.keyResponseSamples;
        const avgKey = allKeyTimes.length ? allKeyTimes.reduce((a, b) => a + b, 0) / allKeyTimes.length : 0;
        const consistency = state.maxWpm > 0 ? ((state.minWpm > 0 ? state.minWpm : 0) / state.maxWpm) * 100 : 100;

        const payload = {
            game_id: GAME_ID,
            token: TOKEN,
            result: {
                wpm: Number(metrics.wpm.toFixed(2)),
                cpm: Number(metrics.cpm.toFixed(2)),
                accuracy: Number(metrics.accuracy.toFixed(2)),
                correct_words: state.correctWords,
                wrong_words: state.wrongWords,
                total_words: words.length,
                completed_words: state.currentWord,
                progress: Number(metrics.progress.toFixed(2)),
                correct_chars: state.correctChars,
                typed_chars: state.typedChars,
                duration_used_ms: Math.round(state.elapsedMs),
                duration_used: Number((state.elapsedMs / 1000).toFixed(3)),
                finish_timestamp_ms: Number(state.elapsedMs.toFixed(3)),
                gross_wpm: Number(metrics.grossWpm.toFixed(2)),
                gross_cpm: Number(metrics.grossCpm.toFixed(2)),
                avg_key_response_ms: Number(avgKey.toFixed(2)),
                last_key_response_ms: Number(state.lastKeyResponseMs.toFixed(2)),
                key_events: state.totalKeyEvents,
                blocked_paste_attempts: state.rejectedPasteAttempts,
                tab_switches: state.antiCheat.tabSwitches,
                anti_cheat: {
                    clipboard_blocks: state.antiCheat.blockedClipboard,
                    paste_blocks: state.antiCheat.blockedPaste,
                    drop_blocks: state.antiCheat.blockedDrop
                },
                speed_history: state.speedHistory,
                best_wpm: Number(state.maxWpm.toFixed(2)),
                lowest_wpm: state.minWpm === Infinity ? 0 : Number(state.minWpm.toFixed(2)),
                consistency: Number(consistency.toFixed(2)),
                word_timings: state.wordTimings,
                grade: calculateGrade(metrics.wpm, metrics.accuracy).grade,
                mode: 'typing_competition_v3'
            }
        };

        try {
            const response = await fetch(RESULT_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
                keepalive: true
            });
            const data = await response.json().catch(() => ({}));
            if (data.redirect) window.location.href = data.redirect;
        } catch (error) {
            console.warn('Hasil belum tersimpan ke server.', error);
        }
    }

    // ======================== KEYBOARD EVENT HANDLING ========================
    function registerKeyActivity() {
        const now = performance.now();
        if (state.lastKeyPerf > 0) {
            const responseMs = Math.max(0, now - state.lastKeyPerf);
            if (responseMs <= 2000) {
                state.keyResponseSamples.push(responseMs);
                state.allKeyResponseTimes.push(responseMs);
                if (state.keyResponseSamples.length > 40) state.keyResponseSamples.shift();
                if (state.allKeyResponseTimes.length > 200) state.allKeyResponseTimes.shift();
                state.lastKeyResponseMs = responseMs;
            }
        }
        state.lastKeyPerf = now;
        state.lastInputPerf = now;
        state.totalKeyEvents++;
        state.keySequence++;
        updateTypingActivity(now);
    }

    function isBlockedClipboardShortcut(event) {
        const modifier = event.ctrlKey || event.metaKey;
        return modifier && ['c', 'x', 'v'].includes(String(event.key).toLowerCase());
    }

    // ======================== INPUT EVENT LISTENERS ========================
    el.input.addEventListener('beforeinput', (event) => {
        if (!state.started || state.finished) return;
        const blocked = ['insertFromPaste', 'insertFromDrop', 'insertFromYank', 'insertFromPasteAsQuotation'];
        if (blocked.includes(event.inputType)) {
            event.preventDefault();
            state.rejectedPasteAttempts++;
            if (event.inputType === 'insertFromDrop') state.antiCheat.blockedDrop++;
            else state.antiCheat.blockedPaste++;
            setStatus('🛡️ Paste/drop diblokir — ketik manual.', 'error');
            updateStats();
        }
    });

    el.input.addEventListener('paste', (event) => {
        event.preventDefault();
        state.antiCheat.blockedPaste++;
        state.rejectedPasteAttempts++;
        setStatus('🛡️ Paste dinonaktifkan. Silakan mengetik manual.', 'error');
        updateStats();
    });

    el.input.addEventListener('drop', (event) => {
        event.preventDefault();
        state.antiCheat.blockedDrop++;
        state.rejectedPasteAttempts++;
        setStatus('🛡️ Drag & drop diblokir.', 'error');
        updateStats();
    });

    el.input.addEventListener('copy', (event) => { event.preventDefault(); state.antiCheat.blockedClipboard++; });
    el.input.addEventListener('cut', (event) => { event.preventDefault(); state.antiCheat.blockedClipboard++; });
    el.input.addEventListener('contextmenu', (event) => { event.preventDefault(); state.antiCheat.blockedClipboard++; });
    el.text.addEventListener('copy', (event) => event.preventDefault());
    el.text.addEventListener('contextmenu', (event) => event.preventDefault());

    el.input.addEventListener('input', () => {
        if (!state.started || state.finished) return;
        const expectedChars = splitChars(getCurrentExpected());
        const typedChars = splitChars(el.input.value);
        let validPrefix = true;
        for (let i = 0; i < typedChars.length; i++) {
            if (typedChars[i] !== expectedChars[i]) { validPrefix = false; break; }
        }
        paintCurrentWord(el.input.value);
        const live = getLiveCharacterCounts();
        state.liveCorrectChars = live.correct;
        state.liveTypedChars = live.typed;
        state.lastInputPerf = performance.now();

        if (!validPrefix || typedChars.length > expectedChars.length) {
            setStatus('⚠️ Karakter tidak cocok — periksa kembali.', 'error');
        } else {
            const activity = getTypingActivity();
            setStatus(activity.key === 'IDLE' ? '🟡 Jeda mengetik...' : '🟢 Sedang mengetik...', 'running');
        }
        updateStats();
    });

    el.input.addEventListener('keydown', (event) => {
        if (!state.started || state.finished) return;
        if (isBlockedClipboardShortcut(event)) {
            event.preventDefault();
            state.antiCheat.blockedClipboard++;
            setStatus('🛡️ Clipboard dinonaktifkan.', 'error');
            return;
        }
        if (event.key === ' ' || event.code === 'Space') {
            event.preventDefault();
            registerKeyActivity();
            commitWord();
            return;
        }
        if (event.key === 'Backspace') { registerKeyActivity(); return; }
        if (event.key === 'Escape') return;

        const isPrintable = event.key.length === 1 && !event.altKey && !event.ctrlKey && !event.metaKey && !event.isComposing;
        if (isPrintable) {
            registerKeyActivity();
            window.requestAnimationFrame(updateStats);
        }
    });

    el.input.addEventListener('blur', () => {
        if (!state.started || state.finished) return;
        if (!adaptive.mediaTouch.matches) {
            window.setTimeout(() => {
                if (state.started && !state.finished) el.input.focus({ preventScroll: true });
            }, 0);
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (!state.started || state.finished) return;
        if (document.hidden) {
            state.antiCheat.tabSwitches++;
            setStatus('⚠️ Tab berpindah — kembali ke arena.', 'error');
            return;
        }
        el.input.focus({ preventScroll: true });
        setStatus('🟢 Arena aktif kembali.', 'running');
    });

    // ======================== CONTROL BUTTONS ========================
    el.start.addEventListener('click', () => {
        if (!state.started && !state.finished) { startGame(); return; }
        if (state.started) { finishGame('⏹ Dihentikan oleh pengguna.'); return; }
        if (state.finished) { resetState(); startGame(); }
    });

    el.reset.addEventListener('click', resetState);
    el.resultPlayAgain.addEventListener('click', () => { resetState(); startGame(); });

    el.text.addEventListener('click', () => {
        if (state.started && !state.finished) el.input.focus({ preventScroll: true });
    });

    el.input.addEventListener('touchstart', () => {
        if (state.started && !state.finished) el.input.focus({ preventScroll: true });
    }, { passive: true });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && state.started && !state.finished) finishGame('⏹ Dihentikan dengan Escape.');
        if (state.started && !state.finished &&
            document.activeElement !== el.input &&
            !['INPUT', 'TEXTAREA', 'BUTTON', 'A', 'SELECT'].includes(document.activeElement?.tagName)) {
            el.input.focus({ preventScroll: true });
        }
    });

    // ======================== INITIALIZATION ========================
    renderText();
    resetState();
});
</script>

{{-- ======================== STYLES ======================== --}}
<style>
    :root {
        --typing-gutter: 24px;
        --typing-font-scale: 1;
        --typing-panel-height: 320px;
        --typing-safe-bottom: env(safe-area-inset-bottom, 0px);
        --typing-metric-cols: 4;
    }

    /* Adaptive Shell */
    .typing-page {
        padding-left: var(--typing-gutter) !important;
        padding-right: var(--typing-gutter) !important;
        overscroll-behavior-x: none;
    }

    .arena-layout {
        grid-template-columns: minmax(0, 1fr) 360px;
        align-items: start;
    }

    .metrics-grid {
        grid-template-columns: repeat(var(--typing-metric-cols), minmax(0, 1fr));
    }

    .typing-text-panel {
        min-height: clamp(180px, 28vh, var(--typing-panel-height));
        max-height: min(52vh, 430px);
        contain: content;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }

    /* Metric Cards */
    .metric-card {
        border-radius: 18px;
        padding: 11px 10px;
        background: rgba(248,250,252,.9);
        border: 1px solid rgba(226,232,240,.9);
        min-width: 0;
        transition: opacity .2s ease, transform .2s ease;
    }

    .dark .metric-card {
        background: rgba(15,23,42,.72);
        border-color: rgba(51,65,85,.85);
    }

    .metric-label {
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgb(148 163 184);
    }

    .metric-value {
        margin-top: 3px;
        font-size: clamp(1.05rem, calc(3vw * var(--typing-font-scale)), 1.75rem);
        line-height: 1.05;
        font-weight: 950;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .metric-sub {
        margin-top: 4px;
        font-size: 9px;
        color: rgb(148 163 184);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Text Panel */
    .typing-text-panel {
        overflow: auto;
        border-radius: 24px;
        padding: clamp(16px, 2.5vw, 24px);
        background: linear-gradient(180deg, rgba(248,250,252,.98), rgba(241,245,249,.92));
        border: 1px solid rgb(226 232 240);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: clamp(1rem, 2vw, 1.22rem);
        line-height: 1.75;
        letter-spacing: 0;
        scroll-behavior: smooth;
        user-select: none;
    }

    .dark .typing-text-panel {
        background: linear-gradient(180deg, rgba(15,23,42,.96), rgba(15,23,42,.82));
        border-color: rgb(51 65 85);
    }

    .typing-word {
        display: inline-flex;
        position: relative;
        padding: 1px 2px;
        margin: 0;
        border-radius: 7px;
        transition: background-color .12s ease, box-shadow .12s ease, transform .12s ease;
    }

    .typing-word.is-current {
        background: rgba(99,102,241,.12);
        box-shadow: inset 0 -2px 0 rgba(99,102,241,.75);
    }

    .typing-word.has-live-error { background: rgba(244,63,94,.09); }
    .typing-word.is-complete { background: rgba(16,185,129,.09); }
    .typing-word.is-wrong-word { background: rgba(244,63,94,.10); }

    .typing-char {
        position: relative;
        display: inline-block;
        min-width: 0;
        margin: 0;
        padding: 0;
        color: rgb(71 85 105);
        transition: color .08s ease, background-color .08s ease, transform .08s ease;
        border-radius: 4px;
    }

    .dark .typing-char { color: rgb(203 213 225); }
    .typing-char.is-correct { color: rgb(5 150 105); }
    .dark .typing-char.is-correct { color: rgb(52 211 153); }
    .typing-char.is-wrong { color: rgb(225 29 72); background: rgba(244,63,94,.14); }
    .dark .typing-char.is-wrong { color: rgb(251 113 133); }
    .typing-char.is-missing { opacity: .85; }

    #typing-activity { font-variant-numeric: tabular-nums; letter-spacing: .02em; }
    #typing-activity[data-state="TYPING"] { animation: typingPulse 1s ease-in-out infinite; }
    #typing-activity[data-state="IDLE"] { opacity: .72; }

    @keyframes typingPulse {
        0%, 100% { transform: translateZ(0); opacity: 1; }
        50% { transform: translateZ(0) scale(1.02); opacity: .78; }
    }

    .typing-space {
        display: inline-block;
        width: .38em;
        min-width: .38em;
        white-space: normal;
    }

    /* Input */
    .typing-input {
        width: 100%;
        min-height: 58px;
        border-radius: 20px;
        border: 2px solid rgb(203 213 225);
        background: white;
        padding: 17px 88px 17px 46px;
        color: rgb(15 23 42);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: clamp(1rem, 2vw, 1.15rem);
        font-weight: 700;
        outline: none;
        box-shadow: 0 8px 24px rgba(15,23,42,.05);
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        -webkit-appearance: none;
        appearance: none;
    }

    .typing-input:focus {
        border-color: rgb(99 102 241);
        box-shadow: 0 0 0 5px rgba(99,102,241,.12), 0 10px 30px rgba(15,23,42,.08);
        transform: translateY(-1px);
    }

    .typing-input:disabled { cursor: not-allowed; opacity: .62; }
    .dark .typing-input { background: rgb(15 23 42); border-color: rgb(51 65 85); color: white; }

    /* Chips & Buttons */
    .chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: white;
        border: 1px solid rgb(226 232 240);
        padding: 7px 10px;
    }

    .dark .chip { background: rgba(15,23,42,.8); border-color: rgb(51 65 85); }

    .control-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        border-radius: 15px;
        padding: 11px 16px;
        font-size: 13px;
        font-weight: 900;
        transition: transform .12s ease, box-shadow .12s ease, background-color .12s ease;
        touch-action: manipulation;
    }

    .control-btn:hover { transform: translateY(-1px); }
    .control-btn:active { transform: translateY(1px) scale(.99); }
    .control-btn-primary {
        color: white;
        background: linear-gradient(135deg, rgb(79 70 229), rgb(124 58 237));
        box-shadow: 0 10px 22px rgba(99,102,241,.24);
    }
    .control-btn-danger {
        color: white;
        background: linear-gradient(135deg, rgb(225 29 72), rgb(244 63 94));
        box-shadow: 0 10px 22px rgba(244,63,94,.20);
    }
    .control-btn-secondary {
        color: rgb(71 85 105);
        background: white;
        border: 1px solid rgb(226 232 240);
    }
    .dark .control-btn-secondary {
        color: rgb(226 232 240);
        background: rgb(15 23 42);
        border-color: rgb(51 65 85);
    }

    /* Result Cards */
    .result-card {
        border-radius: 20px;
        padding: 16px;
        text-align: center;
        background: rgb(248 250 252);
        border: 1px solid rgb(226 232 240);
    }
    .dark .result-card {
        background: rgba(15,23,42,.72);
        border-color: rgb(51 65 85);
    }
    .result-card span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: rgb(148 163 184);
    }
    .result-card strong {
        display: block;
        margin-top: 5px;
        font-size: clamp(1.2rem, 2.5vw, 1.7rem);
        font-weight: 950;
        font-variant-numeric: tabular-nums;
        color: rgb(15 23 42);
    }
    .dark .result-card strong { color: white; }

    /* Competition Rows */
    .competition-row {
        display: flex;
        gap: 10px;
        padding: 11px;
        border-radius: 18px;
        background: rgba(248,250,252,.92);
        border: 1px solid rgb(226 232 240);
        transition: transform .12s ease, border-color .12s ease, background-color .12s ease;
    }
    .competition-row:hover { transform: translateY(-1px); border-color: rgb(165 180 252); }
    .competition-row.is-me { border-color: rgba(99,102,241,.45); background: rgba(238,242,255,.85); }
    .dark .competition-row { background: rgba(15,23,42,.72); border-color: rgb(51 65 85); }
    .dark .competition-row.is-me { background: rgba(49,46,129,.18); border-color: rgba(129,140,248,.35); }

    .competition-rank {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        flex: 0 0 auto;
        border-radius: 10px;
        background: rgb(226 232 240);
        color: rgb(71 85 105);
        font-size: 11px;
        font-weight: 950;
    }
    .dark .competition-rank { background: rgb(30 41 59); color: rgb(203 213 225); }

    /* Speed Chart */
    .speed-chart {
        background-image: linear-gradient(to top, rgba(99,102,241,.03) 1px, transparent 1px);
        background-size: 100% 25%;
    }

    /* ======================== RESPONSIVE BREAKPOINTS ======================== */

    /* Tablet: one column, competition below arena */
    @media (max-width: 1100px) {
        .arena-layout { grid-template-columns: minmax(0, 1fr); }
        #competition-panel { order: 2; }
        .typing-text-panel { max-height: 46vh; }
    }

    /* Mobile / Narrow layouts: minimal metric cards */
    @media (max-width: 767px) {
        .typing-page {
            padding-top: max(8px, env(safe-area-inset-top, 0px)) !important;
            padding-bottom: calc(10px + var(--typing-safe-bottom)) !important;
        }
        .typing-page > div { max-width: 100% !important; }
        .metrics-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; }

        /* Hide desktop-only metrics on mobile/tablet */
        [data-desktop-only] { display: none !important; }

        .metric-card { padding: 8px 7px; border-radius: 14px; }
        .metric-label { font-size: 8px; letter-spacing: .08em; }
        .metric-value { font-size: clamp(0.95rem, 3.5vw, 1.25rem); }
        .metric-sub { font-size: 7px; margin-top: 2px; }

        .typing-text-panel {
            min-height: clamp(150px, 34vh, 280px);
            max-height: 40vh;
            border-radius: 18px;
            line-height: 1.65;
            padding: 12px;
            font-size: clamp(.92rem, 3.8vw, 1.05rem);
        }

        .typing-word { padding: 1px 2px; margin: 0; }

        .typing-input {
            min-height: 56px;
            border-radius: 16px;
            padding: 14px 68px 14px 40px;
            font-size: 16px;
        }

        .control-btn { min-height: 46px; flex: 1 1 0; padding-inline: 10px; font-size: 12px; }
        .control-btns { width: 100%; }
        #competition-panel { border-radius: 20px !important; }
        .chip { padding: 5px 7px; font-size: 10px; }
        .player-identity-card { padding: 8px 10px; }
        .player-identity-card .text-sm { font-size: 12px; }
        .player-identity-card .text-[11px] { font-size: 9px; }

        /* Result section mobile */
        #result-section { padding: 14px; border-radius: 20px; }
        #result-section .grid.lg\\:grid-cols-\\[280px_1fr\\] { grid-template-columns: 1fr; }
        #result-section .grid.md\\:grid-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        #result-section .grid.xl\\:grid-cols-8 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    /* Ultra narrow phones */
    @media (max-width: 374px) {
        .metrics-grid { gap: 4px; }
        .metric-card { padding: 6px 5px; border-radius: 10px; }
        .metric-value { font-size: 0.85rem; }
        .metric-label { font-size: 7px; }
        .metric-sub { font-size: 6px; }
        .typing-text-panel { padding: 8px; font-size: 0.88rem; line-height: 1.6; }
        .typing-input { min-height: 50px; font-size: 15px; padding: 12px 58px 12px 36px; }
        .control-btn { min-height: 42px; font-size: 11px; padding-inline: 8px; }
    }

    /* Large phones (375px - 639px) */
    @media (min-width: 375px) and (max-width: 639px) {
        .metrics-grid { gap: 6px; }
        .typing-text-panel { padding: 14px; font-size: 1rem; }
    }

    /* Small tablets (640px - 767px) - still minimal but wider */
    @media (min-width: 640px) and (max-width: 767px) {
        .metrics-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .typing-text-panel { padding: 16px; font-size: 1.05rem; }
        .typing-input { padding: 15px 76px 15px 44px; }
    }

    /* Tablet (768px - 1100px) */
    @media (min-width: 768px) and (max-width: 1100px) {
        .metrics-grid { gap: 8px; }
        [data-desktop-only] { display: block !important; }
        .typing-text-panel { max-height: 42vh; }
    }

    /* Desktop: all cards visible */
    @media (min-width: 1101px) {
        [data-desktop-only] { display: block !important; }
        .metrics-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    /* Landscape phone */
    @media (max-width: 900px) and (orientation: landscape) {
        .typing-text-panel { min-height: 170px; max-height: 48vh; }
        .typing-page { padding-top: 6px !important; }
        .metrics-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        [data-desktop-only] { display: block !important; }
    }

    /* Coarse pointer */
    @media (pointer: coarse) {
        .control-btn:hover, .competition-row:hover { transform: none; }
        .typing-input:focus { transform: none; }
    }

    /* Compact height */
    @media (max-height: 760px) {
        .typing-text-panel { min-height: 150px; max-height: 36vh; }
        #typing-app > div:first-child { padding-top: 6px; padding-bottom: 6px; }
    }

    /* Fold devices */
    @media (device-posture: folded) {
        .arena-layout { grid-template-columns: minmax(0, 1fr); }
        #competition-panel { order: 2; }
        .typing-text-panel { max-height: 38vh; }
    }

    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            scroll-behavior: auto !important;
            animation-duration: .001ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .001ms !important;
        }
        .typing-text-panel { scroll-behavior: auto; }
    }

    /* Prevent overflow */
    #typing-app, #competition-panel, #result-section {
        min-width: 0;
        max-width: 100%;
    }
    #typing-app { overflow-wrap: anywhere; }

    /* Player identity card hover */
    .player-identity-card {
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .player-identity-card:hover {
        box-shadow: 0 8px 20px rgba(99,102,241,.15);
        transform: translateY(-1px);
    }
</style>
@endsection