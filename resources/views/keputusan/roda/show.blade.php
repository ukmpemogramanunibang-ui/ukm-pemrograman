@extends('layouts.app')

@section('title', $wheel->title)

@php
    /* ============================================================
       PREPARE DATA DI PHP — hindari koma di dalam @json()
    ============================================================ */
    $entriesJson = $wheel->entries->map(function ($e) {
        return [
            'id'     => $e->id,
            'name'   => $e->name,
            'weight' => (int) $e->weight,
        ];
    })->values()->all();

    $resultTemplate = $wheel->result_template ?? 'Sukses kepada {name}!';
    $soundPath = $wheel->sound_path
        ? asset('storage/' . $wheel->sound_path)
        : null;

    $colors = [
        '#00d9ff', '#8b5cf6', '#ec4899', '#f59e0b',
        '#10b981', '#3b82f6', '#ef4444', '#06b6d4',
    ];
@endphp

@section('content')
<section class="relative min-h-screen overflow-hidden bg-[#050B18] py-6 text-white">

    {{-- BACKGROUND DECORATION --}}
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-40 -left-40 h-[36rem] w-[36rem] rounded-full bg-cyan-500/10 blur-[140px]"></div>
        <div class="absolute -bottom-40 -right-40 h-[36rem] w-[36rem] rounded-full bg-violet-500/10 blur-[140px]"></div>
        <div class="absolute top-1/2 left-1/2 h-[30rem] w-[30rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-500/5 blur-[120px]"></div>
    </div>

    {{-- SUCCESS BANNER --}}
    @if(session('success'))
        <div id="success-banner"
             class="fixed left-1/2 top-4 z-[60] -translate-x-1/2 transform rounded-2xl border border-emerald-400/40 bg-emerald-500/20 px-5 py-3 text-sm font-bold text-emerald-100 shadow-2xl backdrop-blur-xl transition-all duration-500">
            <div class="flex items-center gap-3">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('success-banner').remove()"
                        class="ml-2 text-emerald-200/60 hover:text-white">✕</button>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const b = document.getElementById('success-banner');
                if (b) {
                    b.style.opacity = '0';
                    b.style.transform = 'translate(-50%, -20px)';
                    setTimeout(() => b.remove(), 500);
                }
            }, 4000);
        </script>
    @endif

    <div class="relative mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('keputusan.index') }}"
                   class="group flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-white/5 transition hover:border-cyan-400/40 hover:bg-cyan-400/10">
                    <svg class="h-5 w-5 text-slate-300 transition group-hover:text-cyan-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-cyan-300">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-cyan-400"></span>
                        Roda Keberuntungan
                    </div>
                    <h1 class="mt-1 truncate text-xl font-black tracking-tight sm:text-2xl">
                        {{ $wheel->title }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="hidden items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs sm:flex">
                    <span class="text-slate-500">Kode:</span>
                    <span class="font-mono font-black tracking-wider text-cyan-300">{{ $wheel->public_token }}</span>
                </div>

                <button onclick="copyShareLink(this)"
                        class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs font-bold text-slate-200 transition hover:border-cyan-400/40 hover:bg-cyan-400/10 hover:text-cyan-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span>Bagikan</span>
                </button>
            </div>
        </header>

        {{-- MAIN GRID --}}
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] xl:grid-cols-[minmax(0,1fr)_380px]">

            {{-- LEFT: WHEEL --}}
            <div class="flex flex-col gap-5">

                {{-- STATUS BAR --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div id="status-dot" class="h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-400"></div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-500">Status</div>
                                <div id="status-label" class="mt-0.5 text-sm font-black text-white">SIAP DIPUTAR</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <div class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-500">Timer</div>
                                <div id="timer-display" class="mt-0.5 font-mono text-sm font-black text-white">00:00</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-500">Speed</div>
                                <div id="speed-display" class="mt-0.5 font-mono text-sm font-black text-cyan-300">0.0x</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/5">
                        <div id="progress-bar" class="h-full w-0 rounded-full bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-500 transition-[width] duration-100 ease-linear"></div>
                    </div>
                </div>

                {{-- WHEEL CANVAS --}}
                <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-white/[0.04] to-white/[0.01] p-4 backdrop-blur-xl sm:p-8">
                    <div class="pointer-events-none absolute inset-0 opacity-50">
                        <div class="absolute left-1/2 top-1/2 h-[80%] w-[80%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan-500/10 blur-3xl"></div>
                    </div>

                    <div class="relative mx-auto aspect-square w-full max-w-[480px] sm:max-w-[560px] lg:max-w-[640px] xl:max-w-[720px]">

                        {{-- POINTER --}}
                        <div class="absolute left-1/2 top-0 z-20 -translate-x-1/2 -translate-y-3">
                            <div class="relative">
                                <div class="absolute left-1/2 top-1/2 h-8 w-8 -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan-400/30 blur-xl"></div>
                                <svg class="relative h-10 w-10 drop-shadow-[0_4px_12px_rgba(0,217,255,0.6)]" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2L4 20L12 17L20 20L12 2Z"
                                          fill="url(#pointerGrad)"
                                          stroke="white"
                                          stroke-width="0.5"
                                          stroke-linejoin="round"/>
                                    <defs>
                                        <linearGradient id="pointerGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0" stop-color="#00d9ff"/>
                                            <stop offset="1" stop-color="#8b5cf6"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                        </div>

                        {{-- CANVAS --}}
                        <canvas id="wheel-canvas" width="900" height="900"
                                class="h-full w-full rounded-full shadow-[0_0_120px_rgba(0,217,255,0.15)]"
                                style="transform-origin:center; will-change:transform; backface-visibility:hidden;"></canvas>

                        {{-- HUB --}}
                        <div class="pointer-events-none absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-cyan-400/60 bg-[#050B18] shadow-[0_0_40px_rgba(0,217,255,0.5)]">
                                <div class="text-xl">🎯</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CONTROLS --}}
                <div class="flex flex-col items-center gap-4">
                    @if($isOperator)
                        <div class="flex w-full max-w-md flex-col items-center gap-3">
                            <button id="spin-btn"
                                    class="group relative w-full overflow-hidden rounded-3xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-500 p-[2px] shadow-[0_10px_60px_-10px_rgba(0,217,255,0.6)] transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_15px_80px_-10px_rgba(0,217,255,0.8)] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100">
                                <div class="relative flex items-center justify-center gap-3 rounded-3xl bg-[#050B18] px-8 py-5">
                                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-cyan-400/10 via-blue-500/10 to-violet-500/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                    <span id="spin-icon" class="relative text-2xl">🎯</span>
                                    <span id="spin-label" class="relative text-lg font-black tracking-wide text-white">
                                        PUTAR RODA
                                    </span>
                                </div>
                            </button>

                            <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Anda operator roda ini • Tekan <kbd class="rounded bg-white/5 px-1.5 py-0.5 text-[10px] font-mono">Space</kbd> untuk memutar</span>
                            </div>
                        </div>
                    @else
                        <div class="flex w-full max-w-md items-center gap-4 rounded-3xl border border-fuchsia-400/20 bg-fuchsia-500/[0.06] p-5 backdrop-blur-xl">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-fuchsia-500/20 text-2xl">
                                👁
                            </div>
                            <div>
                                <div class="text-sm font-black text-fuchsia-200">Mode Penonton</div>
                                <p class="mt-1 text-xs leading-5 text-slate-400">
                                    Roda dikendalikan oleh operator lain. Anda akan melihat putaran secara sinkron.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: SIDEBAR --}}
            <aside class="space-y-5 lg:sticky lg:top-6 lg:self-start">

                {{-- INFO --}}
                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur-xl">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-white/5 bg-black/20 p-3">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">Peserta</div>
                            <div class="mt-1 text-2xl font-black text-white">{{ $wheel->entries->count() }}</div>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-black/20 p-3">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">Durasi</div>
                            <div class="mt-1 text-2xl font-black text-white">{{ $wheel->duration }}<span class="text-sm text-slate-500">s</span></div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between rounded-xl border border-white/5 bg-black/20 px-3 py-2.5">
                            <span class="text-slate-500">Sistem</span>
                            <span class="font-bold text-cyan-300">
                                {{ $wheel->selection_mode === 'weighted' ? '⚖️ Bobot Peluang' : '🎲 Acak Rahasia' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-white/5 bg-black/20 px-3 py-2.5">
                            <span class="text-slate-500">Mode Hasil</span>
                            <span class="font-bold text-white">
                                @switch($wheel->result_mode)
                                    @case('single') 1 Terpilih @break
                                    @case('multi') {{ $wheel->winner_count }} Terpilih @break
                                    @case('ranking') Urutan Lengkap @break
                                @endswitch
                            </span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-white/5 bg-black/20 px-3 py-2.5">
                            <span class="text-slate-500">Audio</span>
                            <span class="font-bold text-white">
                                @switch($wheel->sound_mode)
                                    @case('system') 🔊 Sistem @break
                                    @case('upload') 🎵 Kustom @break
                                    @case('none') 🔇 Tanpa Suara @break
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ENTRIES --}}
                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-300">Peserta</div>
                            <h3 class="mt-1 text-sm font-black">Daftar Nama</h3>
                        </div>
                        <div class="rounded-full bg-cyan-400/10 px-2.5 py-1 text-[10px] font-black text-cyan-300">
                            {{ $wheel->entries->count() }} NAMA
                        </div>
                    </div>

                    <ul class="max-h-[420px] space-y-1.5 overflow-y-auto pr-1">
                        @foreach($wheel->entries as $entry)
                            <li class="group flex items-center justify-between gap-2 rounded-xl border border-white/5 bg-black/20 px-3 py-2.5 text-xs transition hover:border-cyan-400/20 hover:bg-cyan-400/[0.03]">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full"
                                          style="background: {{ $colors[$loop->index % count($colors)] }}"></span>
                                    <span class="truncate font-semibold text-white">{{ $entry->name }}</span>
                                </div>
                                @if($wheel->selection_mode === 'weighted')
                                    <span class="shrink-0 font-mono text-[11px] font-black text-cyan-300">
                                        {{ $entry->weight }}%
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- HISTORY --}}
                @if($wheel->spins->count() > 0)
                    <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur-xl">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-300">Riwayat</div>
                                <h3 class="mt-1 text-sm font-black">Putaran Terakhir</h3>
                            </div>
                            <div class="text-2xl">📜</div>
                        </div>

                        <ul class="space-y-1.5">
                            @foreach($wheel->spins->take(5) as $spin)
                                <li class="flex items-center justify-between rounded-xl border border-white/5 bg-black/20 px-3 py-2 text-xs">
                                    <span class="text-slate-500">#{{ $spin->spin_number }}</span>
                                    <span class="truncate font-semibold text-white">
                                        {{ $spin->winner?->name ?? '—' }}
                                    </span>
                                    <span class="text-[10px] text-slate-500">{{ $spin->created_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </div>

    {{-- CONFETTI CANVAS --}}
    <canvas id="confetti-canvas"
            class="pointer-events-none fixed inset-0 z-[55] hidden"
            width="1920" height="1080"></canvas>

    {{-- RESULT OVERLAY --}}
    <div id="result-overlay" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md" onclick="closeResult()"></div>

        <div class="relative w-full max-w-lg scale-90 transform opacity-0 transition-all duration-500" id="result-card">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-cyan-400/40 via-blue-500/40 to-violet-500/40 p-[2px] shadow-[0_25px_100px_-20px_rgba(0,217,255,0.7)]">
                <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#0a1424] to-[#050B18] p-8 text-center sm:p-10">
                    <div class="pointer-events-none absolute -top-20 left-1/2 h-40 w-40 -translate-x-1/2 rounded-full bg-cyan-400/20 blur-3xl"></div>

                    <div class="relative">
                        <div class="mb-2 text-5xl animate-bounce">🎉</div>
                        <div class="text-[10px] font-black uppercase tracking-[0.3em] text-cyan-300">
                            Hasil Putaran
                        </div>

                        <div id="winner-display" class="mt-6 space-y-3"></div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                            @if($isOperator)
                                <button onclick="closeResult(); document.getElementById('spin-btn')?.click();"
                                        class="rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-6 py-3.5 text-sm font-black text-slate-950 shadow-lg shadow-cyan-500/30 transition hover:-translate-y-0.5">
                                    🎯 PUTAR LAGI
                                </button>
                            @endif
                            <button onclick="closeResult()"
                                    class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-white/10">
                                TUTUP
                            </button>
                        </div>

                        <div class="mt-6 text-[11px] text-slate-500">
                            Putaran <span id="result-spin-num" class="font-mono font-black text-cyan-300">#1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes floatIn {
        from { opacity: 0; transform: translateY(12px) scale(.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes medalFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-6px); }
    }
    .winner-item { animation: floatIn .5s cubic-bezier(.2,.8,.2,1) backwards; }
    .medal-bounce { animation: medalFloat 2s ease-in-out infinite; }
    #wheel-canvas {
        image-rendering: -webkit-optimize-contrast;
        transform: translateZ(0);
    }
</style>

<script>
/* ============================================================
   DATA DARI SERVER
   ============================================================ */
const wheelData = {
    entries: @json($entriesJson),
    mode: "{{ $wheel->selection_mode }}",
    resultMode: "{{ $wheel->result_mode }}",
    resultTemplate: @json($resultTemplate),
    durationSec: {{ (int) $wheel->duration }},
    soundMode: "{{ $wheel->sound_mode }}",
    soundPath: @json($soundPath),
    isOperator: {{ $isOperator ? 'true' : 'false' }},
    token: "{{ $wheel->public_token }}",
    controllerToken: @json($controllerToken ?? null),
    csrf: "{{ csrf_token() }}",
    spinUrl: "{{ route('keputusan.roda.spin', $wheel->public_token) }}",
    stateUrl: "{{ route('keputusan.roda.state', $wheel->public_token) }}",
    completeUrl: "{{ url('/keputusan/roda/' . $wheel->public_token . '/complete') }}",
};

/* ============================================================
   CANVAS
   ============================================================ */
const canvas = document.getElementById('wheel-canvas');
const ctx    = canvas.getContext('2d');

const COLORS = [
    '#00d9ff','#8b5cf6','#ec4899','#f59e0b','#10b981',
    '#3b82f6','#ef4444','#06b6d4','#a855f7','#14b8a6',
    '#f97316','#84cc16','#e11d48','#6366f1','#0891b2',
];

let currentAngle = 0;
let animating    = false;
let activeSpinId = null;   // spin yang sedang tampil
let spinRAF      = null;   // handle requestAnimationFrame
let recoveryTimer = null;
let lastVisibility = 'visible';
let hiddenAt = null;

/* ============================================================
   DRAW WHEEL
   ============================================================ */
function drawWheel() {
    const size = canvas.width;
    const cx = size / 2, cy = size / 2;
    const r = size / 2 - 20;

    ctx.clearRect(0, 0, size, size);

    const N = wheelData.entries.length;
    if (N === 0) return;

    const totalWeight = wheelData.mode === 'weighted'
        ? (wheelData.entries.reduce((s, e) => s + (e.weight || 0), 0) || N)
        : N;

    let acc = 0;

    wheelData.entries.forEach((entry, i) => {
        const segSize = wheelData.mode === 'weighted'
            ? ((entry.weight || 0) / totalWeight) * Math.PI * 2
            : (Math.PI * 2) / N;

        const startAngle = acc;
        const endAngle   = acc + segSize;
        const midAngle   = startAngle + segSize / 2;
        const color      = COLORS[i % COLORS.length];

        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, r, startAngle, endAngle);
        ctx.closePath();

        const grad = ctx.createRadialGradient(cx, cy, r * 0.15, cx, cy, r);
        grad.addColorStop(0, color);
        grad.addColorStop(0.7, shade(color, -0.1));
        grad.addColorStop(1, shade(color, -0.35));
        ctx.fillStyle = grad;
        ctx.fill();

        ctx.strokeStyle = 'rgba(255,255,255,0.2)';
        ctx.lineWidth = 3;
        ctx.stroke();

        if (segSize > 0.08) {
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(midAngle);
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(0,0,0,0.7)';
            ctx.shadowBlur = 8;

            const fontSize = Math.min(38, Math.max(16, segSize * 200));
            ctx.font = `900 ${fontSize}px system-ui, -apple-system, sans-serif`;
            ctx.fillStyle = '#ffffff';

            const text = entry.name.length > 20
                ? entry.name.slice(0, 18) + '…'
                : entry.name;

            ctx.fillText(text, r - 40, 0);
            ctx.restore();
        }

        acc = endAngle;
    });

    ctx.beginPath();
    ctx.arc(cx, cy, r + 6, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(0,217,255,0.5)';
    ctx.lineWidth = 8;
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(cx, cy, r, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(255,255,255,0.15)';
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(cx, cy, 60, 0, Math.PI * 2);
    const hubGrad = ctx.createRadialGradient(cx, cy, 0, cx, cy, 60);
    hubGrad.addColorStop(0, '#0a1929');
    hubGrad.addColorStop(1, '#050B18');
    ctx.fillStyle = hubGrad;
    ctx.fill();
    ctx.strokeStyle = '#00d9ff';
    ctx.lineWidth = 4;
    ctx.stroke();

    for (let i = 0; i < 8; i++) {
        const a = (i / 8) * Math.PI * 2;
        const x = cx + Math.cos(a) * 45;
        const y = cy + Math.sin(a) * 45;
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(0,217,255,0.6)';
        ctx.fill();
    }
}

function shade(hex, percent) {
    const num = parseInt(hex.replace('#', ''), 16);
    let r = (num >> 16) + Math.round(255 * percent);
    let g = ((num >> 8) & 0x00FF) + Math.round(255 * percent);
    let b = (num & 0x0000FF) + Math.round(255 * percent);
    r = Math.max(0, Math.min(255, r));
    g = Math.max(0, Math.min(255, g));
    b = Math.max(0, Math.min(255, b));
    return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
}

/* ============================================================
   EASING
   ============================================================ */
function spinEasing(t) {
    if (t <= 0) return 0;
    if (t >= 1) return 1;

    if (t < 0.08) {
        const x = t / 0.08;
        return 0.20 * (x * x);
    }
    if (t < 0.50) {
        const x = (t - 0.08) / 0.42;
        return 0.20 + 0.55 * x;
    }
    if (t < 0.85) {
        const x = (t - 0.50) / 0.35;
        return 0.75 + 0.20 * (1 - Math.pow(1 - x, 2));
    }
    const x = (t - 0.85) / 0.15;
    return 0.95 + 0.05 * (1 - Math.pow(1 - x, 3));
}

/* ============================================================
   AUDIO
   ============================================================ */
let audioCtx = null;

function getAudioContext() {
    if (!audioCtx) {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return null;
        audioCtx = new Ctx();
    }
    return audioCtx;
}

async function unlockAudio() {
    const c = getAudioContext();
    if (!c) return null;
    if (c.state === 'suspended') await c.resume();
    return c;
}

function beep(ctx, freq, start, dur, vol, type) {
    type = type || 'sine';
    const osc  = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = type;
    osc.frequency.setValueAtTime(freq, start);
    gain.gain.setValueAtTime(0.0001, start);
    gain.gain.exponentialRampToValueAtTime(vol, start + 0.01);
    gain.gain.exponentialRampToValueAtTime(0.0001, start + dur);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start(start);
    osc.stop(start + dur + 0.03);
}

function playTick() {
    if (wheelData.soundMode === 'none') return;
    const ctx = getAudioContext();
    if (!ctx || ctx.state !== 'running') return;
    beep(ctx, 1200, ctx.currentTime, 0.04, 0.02, 'square');
}

async function playFinish() {
    if (wheelData.soundMode === 'none') return;

    if (wheelData.soundMode === 'upload' && wheelData.soundPath) {
        try {
            const audio = new Audio(wheelData.soundPath);
            audio.volume = 0.7;
            await audio.play();
        } catch (e) {}
        return;
    }

    const ctx = await unlockAudio();
    if (!ctx) return;
    const t = ctx.currentTime;
    beep(ctx, 523.25, t,        0.15, 0.06, 'sine');
    beep(ctx, 659.25, t + 0.14, 0.15, 0.06, 'sine');
    beep(ctx, 783.99, t + 0.28, 0.22, 0.07, 'sine');
    beep(ctx, 1046.5, t + 0.52, 0.45, 0.06, 'triangle');
}

/* ============================================================
   TICKER
   ============================================================ */
let tickTimer = null;

function startTicker() {
    stopTicker();
    if (wheelData.soundMode !== 'system') return;

    let interval = 45;
    const tick = () => {
        if (!animating) { stopTicker(); return; }
        playTick();
        interval = Math.min(400, interval * 1.06);
        tickTimer = setTimeout(tick, interval);
    };
    tickTimer = setTimeout(tick, interval);
}

function stopTicker() {
    if (tickTimer) { clearTimeout(tickTimer); tickTimer = null; }
}

/* ============================================================
   ANIMASI SPIN — support resume
   ------------------------------------------------------------
   @param startAngle       Sudut awal
   @param targetAngle      Sudut akhir
   @param durationMs       Total durasi
   @param startOffsetMs    Kalau resume, offset waktu yang sudah berlalu (0 = mulai dari awal)
   @param onDone           Callback saat selesai
   ============================================================ */
function animateSpin(startAngle, targetAngle, durationMs, onDone, startOffsetMs = 0) {
    animating = true;
    stopTicker();
    startTicker();

    const adjustedDuration = Math.max(1, durationMs - startOffsetMs);
    const startTime = performance.now() - startOffsetMs;
    const delta = targetAngle - startAngle;

    const timerEl    = document.getElementById('timer-display');
    const progressEl = document.getElementById('progress-bar');
    const speedEl    = document.getElementById('speed-display');
    const statusLbl  = document.getElementById('status-label');
    const statusDot  = document.getElementById('status-dot');

    if (statusLbl) statusLbl.textContent = 'BERPUTAR...';
    if (statusDot) statusDot.className = 'h-2.5 w-2.5 animate-pulse rounded-full bg-cyan-400';

    let lastFrame = startTime;
    let lastAngle = currentAngle;

    function frame(now) {
        const elapsed = now - startTime;
        const t = Math.min(1, elapsed / durationMs);
        const eased = spinEasing(t);
        currentAngle = startAngle + delta * eased;

        canvas.style.transform = `rotate(${currentAngle}deg)`;

        const remaining = Math.max(0, durationMs - elapsed);
        const sec = Math.ceil(remaining / 1000);
        if (timerEl) {
            timerEl.textContent = String(Math.floor(sec / 60)).padStart(2, '0') + ':' +
                                  String(sec % 60).padStart(2, '0');
        }
        if (progressEl) progressEl.style.width = (t * 100) + '%';

        const dt = now - lastFrame;
        if (dt > 0 && speedEl) {
            const dAngle = Math.abs(currentAngle - lastAngle);
            const speed = (dAngle / 360) / (dt / 1000);
            speedEl.textContent = speed.toFixed(1) + 'x';
            lastAngle = currentAngle;
        }
        lastFrame = now;

        if (t < 1) {
            spinRAF = requestAnimationFrame(frame);
        } else {
            animating = false;
            spinRAF = null;
            stopTicker();
            currentAngle = targetAngle;
            canvas.style.transform = `rotate(${currentAngle}deg)`;

            if (timerEl) timerEl.textContent = '00:00';
            if (progressEl) progressEl.style.width = '100%';
            if (speedEl) speedEl.textContent = '0.0x';
            if (statusLbl) statusLbl.textContent = 'SELESAI';
            if (statusDot) statusDot.className = 'h-2.5 w-2.5 rounded-full bg-emerald-400';

            if (typeof onDone === 'function') onDone();
        }
    }

    spinRAF = requestAnimationFrame(frame);
}

function stopAnimation() {
    if (spinRAF) {
        cancelAnimationFrame(spinRAF);
        spinRAF = null;
    }
    animating = false;
    stopTicker();
}

/* ============================================================
   WINNERS
   ============================================================ */
function escapeHtml(str) {
    return String(str || '').replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

function renderWinnerMarkup(winners) {
    const template = wheelData.resultTemplate || 'Sukses kepada {name}!';

    if (!winners || winners.length === 0) {
        return '<div class="text-2xl font-black text-slate-500">—</div>';
    }

    if (winners.length === 1) {
        const text = template.replaceAll('{name}', winners[0].name);
        return '<div class="winner-item">' +
            '<div class="text-3xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 sm:text-5xl">' +
            escapeHtml(text) +
            '</div></div>';
    }

    const medals = ['🥇', '🥈', '🥉'];
    return winners.map(function (w, i) {
        const text = template.replaceAll('{name}', w.name);
        return '<div class="winner-item flex items-center justify-center gap-3 rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-3" style="animation-delay:' + (i * 0.15) + 's">' +
            '<span class="text-2xl ' + (i < 3 ? 'medal-bounce' : '') + '">' + (medals[i] || (i + 1) + '.') + '</span>' +
            '<span class="text-lg font-black tracking-tight text-white sm:text-xl">' + escapeHtml(text) + '</span>' +
            '</div>';
    }).join('');
}

function showWinners(winners, spinNumber) {
    const display = document.getElementById('winner-display');
    if (display) display.innerHTML = renderWinnerMarkup(winners);

    if (spinNumber) {
        const el = document.getElementById('result-spin-num');
        if (el) el.textContent = '#' + spinNumber;
    }

    const overlay = document.getElementById('result-overlay');
    const card = document.getElementById('result-card');
    if (!overlay || !card) return;

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');

    requestAnimationFrame(function () {
        card.classList.remove('scale-90', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    });

    playFinish();
    launchConfetti();
}

function closeResult() {
    const overlay = document.getElementById('result-overlay');
    const card = document.getElementById('result-card');
    if (!overlay || !card) return;

    card.classList.add('scale-90', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(function () {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }, 250);
}

/* ============================================================
   CONFETTI
   ============================================================ */
function launchConfetti() {
    const cvs = document.getElementById('confetti-canvas');
    if (!cvs) return;
    const cctx = cvs.getContext('2d');
    cvs.width = window.innerWidth;
    cvs.height = window.innerHeight;
    cvs.classList.remove('hidden');

    const particles = [];
    const colors = ['#00d9ff','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ffffff'];

    for (let i = 0; i < 180; i++) {
        particles.push({
            x: window.innerWidth / 2 + (Math.random() - 0.5) * 200,
            y: window.innerHeight / 2 - 100,
            vx: (Math.random() - 0.5) * 24,
            vy: Math.random() * -22 - 5,
            size: Math.random() * 8 + 4,
            color: colors[Math.floor(Math.random() * colors.length)],
            rotation: Math.random() * Math.PI * 2,
            rotSpeed: (Math.random() - 0.5) * 0.4,
            life: 1,
        });
    }

    const start = performance.now();

    function tick(now) {
        const t = (now - start) / 1000;
        cctx.clearRect(0, 0, cvs.width, cvs.height);

        let alive = 0;
        particles.forEach(function (p) {
            p.vy += 0.5;
            p.vx *= 0.99;
            p.x += p.vx;
            p.y += p.vy;
            p.rotation += p.rotSpeed;
            p.life -= 0.008;

            if (p.life > 0 && p.y < cvs.height + 50) {
                alive++;
                cctx.save();
                cctx.translate(p.x, p.y);
                cctx.rotate(p.rotation);
                cctx.globalAlpha = Math.max(0, p.life);
                cctx.fillStyle = p.color;
                cctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                cctx.restore();
            }
        });

        if (alive > 0 && t < 5) {
            requestAnimationFrame(tick);
        } else {
            cctx.clearRect(0, 0, cvs.width, cvs.height);
            cvs.classList.add('hidden');
        }
    }
    requestAnimationFrame(tick);
}

/* ============================================================
   SHARE
   ============================================================ */
function copyShareLink(btn) {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function () {
        const original = btn.innerHTML;
        btn.innerHTML = '<span>Disalin!</span>';
        btn.classList.add('text-emerald-300', 'border-emerald-400/40');
        setTimeout(function () {
            btn.innerHTML = original;
            btn.classList.remove('text-emerald-300', 'border-emerald-400/40');
        }, 2000);
    });
}

/* ============================================================
   ADJUST TARGET ANGLE
   ============================================================ */
function adjustTargetAngle(start, target, durationSec) {
    const desiredRotations = 8 + durationSec * 0.6;
    const serverDelta = Math.abs(target - start);
    const currentRotations = serverDelta / 360;

    if (currentRotations < desiredRotations) {
        const extra = Math.ceil(desiredRotations - currentRotations);
        return target + extra * 360;
    }
    return target;
}

/* ============================================================
   🛡️ STATE RECOVERY
   ------------------------------------------------------------
   Dipanggil saat:
   - Page load pertama kali
   - Echo reconnect
   - window online
   - visibility change (tab kembali aktif)
   - Setelah fetch spin gagal
   ============================================================ */
async function recoverState() {
    try {
        const res = await fetch(wheelData.stateUrl, {
            headers: { 'Accept': 'application/json' },
        });

        if (!res.ok) return null;

        const state = await res.json();

        // 1. ADA SPIN AKTIF
        if (state.active_spin) {
            const spin = state.active_spin;

            // Skip kalau ini spin yang sedang kita tampilkan
            if (activeSpinId === spin.spin_id && animating) return state;

            // Kalau sudah lewat, skip (server harusnya sudah complete)
            if (spin.remaining_ms <= 0) {
                showWinners(spin.winners, spin.spin_number);
                return state;
            }

            // Resume dari posisi saat ini
            activeSpinId = spin.spin_id;

            const target = adjustTargetAngle(
                spin.start_angle,
                spin.target_angle,
                spin.duration
            );

            // Hitung sudut saat ini berdasarkan elapsed
            const progress = spin.elapsed_ms / (spin.duration * 1000);
            const easedNow = spinEasing(progress);
            currentAngle = spin.start_angle + (target - spin.start_angle) * easedNow;

            // Set sudut langsung tanpa animasi
            canvas.style.transform = `rotate(${currentAngle}deg)`;

            // Lanjutkan animasi
            animateSpin(
                spin.start_angle,
                target,
                spin.duration * 1000,
                function () {
                    showWinners(spin.winners, spin.spin_number);
                    activeSpinId = null;
                    if (wheelData.isOperator) resetSpinButton();
                },
                spin.elapsed_ms   // ⬅️ offset supaya sinkron
            );

            return state;
        }

        // 2. TIDAK ADA SPIN AKTIF, cek last_completed
        if (state.last_completed && state.last_completed.status === 'completed') {
            // Optional: tampilkan hasil terakhir kalau baru selesai < 5 detik yang lalu
            const last = state.last_completed;
            if (last.started_at) {
                const elapsed = (Date.now() - new Date(last.started_at).getTime()) / 1000;
                if (elapsed < last.duration + 5 && elapsed > last.duration) {
                    showWinners(last.winners, last.spin_number);
                }
            }
        }

        return state;
    } catch (err) {
        console.warn('[Wheel] recoverState gagal:', err);
        return null;
    }
}

/* ============================================================
   SPIN BUTTON STATE
   ============================================================ */
function resetSpinButton() {
    const spinBtn = document.getElementById('spin-btn');
    const spinLabel = document.getElementById('spin-label');
    const spinIcon = document.getElementById('spin-icon');

    if (spinBtn) spinBtn.disabled = false;
    if (spinLabel) spinLabel.textContent = 'PUTAR RODA';
    if (spinIcon) spinIcon.textContent = '🎯';
}

function setSpinButtonLoading() {
    const spinBtn = document.getElementById('spin-btn');
    const spinLabel = document.getElementById('spin-label');
    const spinIcon = document.getElementById('spin-icon');

    if (spinBtn) spinBtn.disabled = true;
    if (spinLabel) spinLabel.textContent = 'MEMUTAR...';
    if (spinIcon) spinIcon.textContent = '⏳';
}

/* ============================================================
   🚀 INIT
   ============================================================ */
document.addEventListener('DOMContentLoaded', async function () {
    canvas.style.transform = 'rotate(' + currentAngle + 'deg)';
    drawWheel();

    // 1. RECOVERY PERTAMA — sebelum apa-apa, sync dengan server
    await recoverState();

    // 2. ECHO REALTIME
    if (typeof Echo !== 'undefined') {
        try {
            const channel = Echo.channel('wheel.' + wheelData.token);

            channel.listen('.wheel.spun', function (data) {
                // Operator sudah handle sendiri, tapi kalau dia reload, harus ikut
                if (activeSpinId === data.spin_id && animating) return;

                activeSpinId = data.spin_id;

                const target = adjustTargetAngle(
                    parseFloat(data.start_angle),
                    parseFloat(data.target_angle),
                    parseInt(data.duration)
                );

                animateSpin(
                    parseFloat(data.start_angle),
                    target,
                    parseInt(data.duration) * 1000,
                    function () {
                        setTimeout(function () {
                            showWinners(data.winners, data.spin_number);
                            activeSpinId = null;
                            if (wheelData.isOperator) resetSpinButton();
                        }, 300);
                    }
                );
            });

            channel.listen('.wheel.completed', function (data) {
                // Optional: bisa dipakai untuk refresh history
                console.log('[Wheel] Spin #' + data.spin_number + ' selesai');
            });

            // 3. RECOVERY SAAT ECHO RECONNECT
            Echo.connector?.pusher?.connection?.bind('connected', function () {
                console.log('[Wheel] Echo reconnect — syncing state');
                recoverState();
            });
        } catch (err) {
            console.warn('[Wheel] Echo setup gagal:', err);
        }
    }

    // 4. SPIN BUTTON (operator)
    const spinBtn = document.getElementById('spin-btn');
    if (spinBtn) {
        spinBtn.addEventListener('click', async function () {
            if (animating) return;
            setSpinButtonLoading();

            await unlockAudio();

            try {
                const res = await fetch(wheelData.spinUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': wheelData.csrf,
                        'X-Controller-Token': wheelData.controllerToken || '',
                    },
                    body: JSON.stringify({
                        controller_token: wheelData.controllerToken || '',
                    }),
                });

                const ct = res.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    const txt = await res.text();
                    throw new Error('Server error (' + res.status + '): ' + txt.slice(0, 100));
                }

                const data = await res.json();

                // 409 CONFLICT — ada spin aktif, recovery saja
                if (res.status === 409) {
                    console.log('[Wheel] Spin conflict — recovering');
                    if (data.recover) {
                        // Gunakan data recover langsung
                        activeSpinId = data.recover.spin_id;
                        const target = adjustTargetAngle(
                            data.recover.start_angle,
                            data.recover.target_angle,
                            data.recover.duration
                        );
                        animateSpin(
                            data.recover.start_angle,
                            target,
                            data.recover.duration * 1000,
                            function () {
                                showWinners(data.recover.winners, data.recover.spin_number);
                                activeSpinId = null;
                                resetSpinButton();
                            },
                            data.recover.elapsed_ms || 0
                        );
                    } else {
                        await recoverState();
                        resetSpinButton();
                    }
                    return;
                }

                if (!res.ok) throw new Error(data.message || 'HTTP ' + res.status);

                activeSpinId = data.spin_id;

                const target = adjustTargetAngle(
                    parseFloat(data.start_angle),
                    parseFloat(data.target_angle),
                    parseInt(data.duration)
                );

                animateSpin(
                    parseFloat(data.start_angle),
                    target,
                    parseInt(data.duration) * 1000,
                    async function () {
                        try {
                            await fetch(wheelData.completeUrl + '/' + data.spin_id, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': wheelData.csrf,
                                    'Accept': 'application/json',
                                },
                            });
                        } catch (err) {}

                        setTimeout(function () {
                            showWinners(data.winners, data.spin_number);
                            activeSpinId = null;
                            resetSpinButton();
                        }, 300);
                    }
                );

            } catch (e) {
                console.error('[Wheel]', e);
                alert('❌ ' + e.message);
                resetSpinButton();
                // Coba recovery setelah error
                setTimeout(recoverState, 1500);
            }
        });
    }

    // 5. KEYBOARD SHORTCUT
    document.addEventListener('keydown', function (e) {
        if (e.code === 'Space' && wheelData.isOperator && !animating) {
            const spin = document.getElementById('spin-btn');
            if (spin && !spin.disabled) {
                e.preventDefault();
                spin.click();
            }
        }
        if (e.code === 'Escape') closeResult();
    });

    // 6. RECOVERY SAAT KEMBALI ONLINE
    window.addEventListener('online', function () {
        console.log('[Wheel] Online — syncing');
        recoverState();
    });

    // 7. RECOVERY SAAT TAB KEMBALI AKTIF
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            hiddenAt = Date.now();
            lastVisibility = 'hidden';
        } else {
            const hiddenDuration = hiddenAt ? Date.now() - hiddenAt : 0;
            hiddenAt = null;
            lastVisibility = 'visible';

            // Kalau tab hidden > 3 detik, sync ulang
            if (hiddenDuration > 3000) {
                console.log('[Wheel] Tab aktif kembali — syncing (' + Math.round(hiddenDuration / 1000) + 's)');
                recoverState();
            }
        }
    });

    // 8. AUTO SCROLL
    if (document.referrer && document.referrer.indexOf('/roda/buat') !== -1) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

/* ============================================================
   CLEANUP
   ============================================================ */
window.addEventListener('beforeunload', function () {
    stopAnimation();
    if (recoveryTimer) clearTimeout(recoveryTimer);
});
</script>
@endsection