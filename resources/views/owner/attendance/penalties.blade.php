@extends('layouts.roles.owner')

@section('content')
<div class="p-3 md:p-6 w-full max-w-[1800px] mx-auto
            bg-gradient-to-b from-slate-50 via-white to-slate-50
            dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 min-h-screen"
     x-data="penaltyCenter(@js($meta))"
     x-init="init()"
     @keydown.window.prevent.ctrl.k="openCommand()"
     @keydown.window.prevent.meta.k="openCommand()"
     @keydown.window.escape="closeAll()">

    {{-- ============================================================
         TOAST NOTIFICATIONS
    ============================================================= --}}
    <div class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto min-w-[280px] max-w-md rounded-2xl p-4
                        shadow-2xl ring-1 ring-white/20 backdrop-blur-xl
                        animate__animated animate__fadeInRight"
                 :class="{
                    'bg-emerald-500/95 text-white': t.type === 'success',
                    'bg-rose-500/95 text-white': t.type === 'error',
                    'bg-amber-500/95 text-white': t.type === 'warning',
                    'bg-slate-800/95 text-white': t.type === 'info'
                 }">
                <div class="flex items-start gap-3">
                    <i class="fas mt-0.5"
                       :class="{
                          'fa-check-circle': t.type === 'success',
                          'fa-times-circle': t.type === 'error',
                          'fa-exclamation-triangle': t.type === 'warning',
                          'fa-info-circle': t.type === 'info'
                       }"></i>
                    <div class="flex-1 text-sm font-medium" x-text="t.msg"></div>
                </div>
            </div>
        </template>
    </div>

    {{-- ============================================================
         HEADER FUTURISTIC
    ============================================================= --}}
    <div class="relative overflow-hidden rounded-3xl mb-6 p-6 md:p-8
                bg-gradient-to-br from-slate-950 via-rose-950/90 to-slate-950
                shadow-2xl ring-1 ring-rose-500/20">

        {{-- Animated aurora background --}}
        <div class="absolute inset-0 opacity-50 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full blur-3xl
                        bg-rose-500/30 animate-pulse-slow"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full blur-3xl
                        bg-fuchsia-500/20 animate-pulse-slow"
                 style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full blur-3xl
                        bg-amber-500/15 animate-pulse-slow"
                 style="animation-delay: 2s;"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image:
                linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px);
                background-size: 40px 40px;"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-2xl grid place-items-center
                                bg-gradient-to-br from-rose-500 via-red-600 to-fuchsia-700
                                shadow-lg shadow-rose-500/50 ring-1 ring-white/20">
                        <i class="fas fa-gavel text-white text-lg"></i>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-[0.2em] text-rose-300/80 font-bold">
                            Attendance Discipline Center
                        </div>
                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight
                                   bg-gradient-to-r from-white via-rose-100 to-amber-100
                                   bg-clip-text text-transparent">
                            Pusat Denda Absensi
                        </h1>
                    </div>
                </div>

                <p class="text-sm text-rose-100/70 max-w-2xl leading-relaxed">
                    Sistem pakar pengelolaan pelanggaran, poin penalti, dan denda otomatis.
                    Rule engine terintegrasi dengan attendance records, approval workflow,
                    dispute management, dan audit trail lengkap.
                </p>

                <div class="mt-4 flex flex-wrap gap-2 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 bg-white/5 backdrop-blur ring-1 ring-white/15 text-white/90">
                        <i class="fas fa-calendar-day text-rose-300"></i>
                        {{ \Carbon\Carbon::parse($meta['from'])->translatedFormat('d M Y') }}
                        <span class="text-white/40">→</span>
                        {{ \Carbon\Carbon::parse($meta['to'])->translatedFormat('d M Y') }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 bg-rose-500/20 backdrop-blur ring-1 ring-rose-400/40 text-rose-100">
                        <i class="fas fa-scale-balanced"></i>
                        {{ number_format($stats['total_cases']) }} kasus
                    </span>

                    @if(($alerts['overdue'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                     bg-red-500/25 backdrop-blur ring-1 ring-red-400/50 text-red-100
                                     animate-pulse">
                            <i class="fas fa-clock"></i>
                            {{ $alerts['overdue'] }} overdue
                        </span>
                    @endif

                    @if(($alerts['disputed'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                     bg-fuchsia-500/25 backdrop-blur ring-1 ring-fuchsia-400/50 text-fuchsia-100">
                            <i class="fas fa-gavel"></i>
                            {{ $alerts['disputed'] }} keberatan
                        </span>
                    @endif

                    @if(($alerts['critical'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                     bg-orange-500/25 backdrop-blur ring-1 ring-orange-400/50 text-orange-100
                                     animate-pulse">
                            <i class="fas fa-fire"></i>
                            {{ $alerts['critical'] }} kritis
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 bg-white/5 backdrop-blur ring-1 ring-white/15 text-white/60 cursor-pointer
                                 hover:bg-white/10 transition"
                          @click="openCommand()">
                        <i class="fas fa-terminal"></i>
                        <span class="font-mono">Ctrl+K</span> Command
                    </span>
                </div>
            </div>

            {{-- Action Buttons — FIXED: no duplication --}}
            <div class="grid grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-2 shrink-0">
                <a href="{{ route('owner.attendance.penalties', array_merge(request()->query(), ['period' => 'this_month'])) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                          font-semibold text-rose-950 text-xs sm:text-sm
                          bg-gradient-to-r from-rose-200 via-rose-300 to-amber-300
                          hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-rose-500/30 transition
                          min-h-[44px] whitespace-nowrap">
                    <i class="fas fa-calendar-day"></i>
                    <span>Bulan Ini</span>
                </a>

                <a href="{{ route('owner.attendance.penalties', array_merge(request()->query(), ['period' => 'this_year'])) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                          font-semibold text-white text-xs sm:text-sm
                          bg-white/5 backdrop-blur ring-1 ring-white/20 hover:bg-white/15 transition
                          min-h-[44px] whitespace-nowrap">
                    <i class="fas fa-calendar"></i>
                    <span>Tahun Ini</span>
                </a>

                <a href="{{ route('owner.attendance.penalties.export', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                          font-semibold text-white text-xs sm:text-sm
                          bg-white/5 backdrop-blur ring-1 ring-white/20 hover:bg-white/15 transition
                          min-h-[44px] whitespace-nowrap">
                    <i class="fas fa-file-export"></i>
                    <span>Export CSV</span>
                </a>

                <form action="{{ route('owner.attendance.penalties.reconcile') }}" method="POST" class="contents">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                                   font-semibold text-white text-xs sm:text-sm
                                   bg-gradient-to-r from-cyan-500/20 to-blue-500/20 backdrop-blur
                                   ring-1 ring-cyan-400/30 hover:ring-cyan-400/60 transition
                                   min-h-[44px] whitespace-nowrap w-full">
                        <i class="fas fa-rotate"></i>
                        <span>Reconcile</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================
         INSIGHT PANEL — anomaly & pattern detection
    ============================================================= --}}
    @php
        $insights = [];
        if (($alerts['repeat'] ?? 0) > 0) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-user-clock',
                'title' => 'Pelanggar Berulang Terdeteksi',
                'text'  => "{$alerts['repeat']} user memiliki ≥3 denda tahun ini. Perlu pembinaan khusus.",
            ];
        }
        if (($stats['total_overdue'] ?? 0) > 0 && ($stats['total_amount'] ?? 0) > 0) {
            $pct = round(($stats['total_overdue'] / max($stats['total_amount'], 1)) * 100);
            $insights[] = [
                'type' => 'danger',
                'icon' => 'fa-triangle-exclamation',
                'title' => "Tingkat Overdue: {$pct}%",
                'text'  => 'Rp ' . number_format($stats['total_overdue'], 0, ',', '.') .
                           ' dari total Rp ' . number_format($stats['total_amount'], 0, ',', '.') . ' belum dibayar.',
            ];
        }
        if (($stats['prev_month'] ?? 0) > 0 && ($stats['this_month'] ?? 0) > 0) {
            $delta = $stats['this_month'] - $stats['prev_month'];
            $pct   = round(abs($delta) / max($stats['prev_month'], 1) * 100);
            $insights[] = [
                'type' => $delta > 0 ? 'warning' : 'success',
                'icon' => $delta > 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down',
                'title' => "Tren Bulanan: " . ($delta > 0 ? '+' : '') . "{$pct}%",
                'text'  => "Bulan ini {$stats['this_month']} kasus vs bulan lalu {$stats['prev_month']} kasus.",
            ];
        }
    @endphp

    @if(count($insights) > 0)
        <div class="mb-5 rounded-2xl overflow-hidden
                    bg-gradient-to-r from-slate-900 via-slate-900/95 to-slate-900
                    ring-1 ring-white/10 shadow-xl">
            <div class="px-4 py-2.5 flex items-center gap-2
                        bg-gradient-to-r from-violet-500/20 via-fuchsia-500/15 to-transparent
                        border-b border-white/10">
                <i class="fas fa-brain text-violet-400"></i>
                <div class="text-xs font-bold text-white uppercase tracking-wider">
                    AI Insight Panel
                </div>
                <span class="ml-auto text-[10px] text-white/50">
                    <i class="fas fa-sparkles mr-1"></i> Auto-generated analysis
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($insights), 3) }} divide-y md:divide-y-0 md:divide-x divide-white/5">
                @foreach($insights as $ins)
                    @php
                        $color = match($ins['type']) {
                            'danger'  => 'text-red-400 bg-red-500/10',
                            'warning' => 'text-amber-400 bg-amber-500/10',
                            'success' => 'text-emerald-400 bg-emerald-500/10',
                            default   => 'text-blue-400 bg-blue-500/10',
                        };
                    @endphp
                    <div class="p-4 flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl grid place-items-center shrink-0 {{ $color }}">
                            <i class="fas {{ $ins['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white">{{ $ins['title'] }}</div>
                            <div class="text-[11px] text-white/60 mt-0.5 leading-snug">{{ $ins['text'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============================================================
         ALERT CENTER
    ============================================================= --}}
    @php
        $alertsList = collect([
            ['k'=>'overdue',  'label'=>'Terlambat Bayar', 'count'=>($alerts['overdue']  ?? 0),
             'color'=>'red',    'icon'=>'fa-clock',           'msg'=>'denda melewati jatuh tempo'],
            ['k'=>'disputed', 'label'=>'Keberatan',       'count'=>($alerts['disputed'] ?? 0),
             'color'=>'fuchsia','icon'=>'fa-gavel',           'msg'=>'keberatan menunggu review'],
            ['k'=>'pending',  'label'=>'Menunggu Review', 'count'=>($alerts['pending']  ?? 0),
             'color'=>'amber',  'icon'=>'fa-hourglass-half',  'msg'=>'kasus butuh approval'],
            ['k'=>'critical', 'label'=>'Kritis',          'count'=>($alerts['critical'] ?? 0),
             'color'=>'orange', 'icon'=>'fa-fire',            'msg'=>'kasus severity kritis'],
            ['k'=>'repeat',   'label'=>'Pelanggar Ulang', 'count'=>($alerts['repeat']   ?? 0),
             'color'=>'violet', 'icon'=>'fa-repeat',          'msg'=>'user dengan ≥3 denda tahun ini'],
        ])->filter(fn($a) => $a['count'] > 0)->values();
    @endphp

    @if($alertsList->count())
        <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @foreach($alertsList as $a)
                <div class="group relative overflow-hidden rounded-2xl p-4
                            bg-white dark:bg-slate-900/70 shadow-lg
                            ring-1 ring-{{ $a['color'] }}-300/50 dark:ring-{{ $a['color'] }}-700/40
                            hover:scale-[1.02] transition cursor-pointer">
                    <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full opacity-10 blur-2xl
                                bg-gradient-to-br from-{{ $a['color'] }}-500 to-{{ $a['color'] }}-700"></div>
                    <div class="relative flex items-start gap-3">
                        <div class="w-10 h-10 shrink-0 rounded-xl grid place-items-center text-white shadow-lg
                                    bg-gradient-to-br from-{{ $a['color'] }}-500 to-{{ $a['color'] }}-700
                                    group-hover:scale-110 transition">
                            <i class="fas {{ $a['icon'] }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-bold uppercase tracking-wider
                                        text-{{ $a['color'] }}-600 dark:text-{{ $a['color'] }}-400">
                                {{ $a['label'] }}
                            </div>
                            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none mt-1">
                                {{ $a['count'] }}
                            </div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 leading-tight">
                                {{ $a['msg'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============================================================
         KPI SECTION
    ============================================================= --}}
    @php
        $caseKpis = [
            ['label'=>'Total Kasus',    'value'=>$stats['total_cases'],    'icon'=>'fa-folder-open',
             'g'=>'from-rose-500 to-red-700',    'accent'=>'rose'],
            ['label'=>'Aktif',          'value'=>$stats['active_cases'],   'icon'=>'fa-fire',
             'g'=>'from-orange-500 to-red-600',  'accent'=>'orange'],
            ['label'=>'Menunggu',       'value'=>$stats['pending_cases'],  'icon'=>'fa-hourglass-half',
             'g'=>'from-amber-400 to-orange-600','accent'=>'amber'],
            ['label'=>'Terselesaikan',  'value'=>$stats['resolved'],       'icon'=>'fa-circle-check',
             'g'=>'from-emerald-500 to-teal-600','accent'=>'emerald'],
        ];
        $finKpis = [
            ['label'=>'Total Nominal',  'value'=>$stats['total_amount'],   'icon'=>'fa-money-bill-wave',
             'g'=>'from-rose-500 to-fuchsia-700'],
            ['label'=>'Sudah Dibayar',  'value'=>$stats['total_paid'],     'icon'=>'fa-check-double',
             'g'=>'from-emerald-500 to-teal-600'],
            ['label'=>'Belum Dibayar',  'value'=>$stats['total_unpaid'],   'icon'=>'fa-clock',
             'g'=>'from-amber-500 to-orange-600'],
            ['label'=>'Overdue',        'value'=>$stats['total_overdue'],  'icon'=>'fa-triangle-exclamation',
             'g'=>'from-red-500 to-rose-700'],
        ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
        @foreach($caseKpis as $k)
            <div class="group relative overflow-hidden rounded-2xl p-4
                        bg-white dark:bg-slate-900/70 shadow-lg
                        ring-1 ring-slate-200/70 dark:ring-white/5
                        hover:-translate-y-0.5 hover:shadow-xl hover:ring-{{ $k['accent'] }}-400/30 transition
                        flex flex-col justify-between min-h-[120px]">
                <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full opacity-20 blur-2xl
                            bg-gradient-to-br {{ $k['g'] }}"></div>
                <div class="relative flex items-start justify-between gap-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider
                                text-slate-500 dark:text-slate-400 leading-tight">
                        {{ $k['label'] }}
                    </div>
                    <div class="w-9 h-9 shrink-0 rounded-xl grid place-items-center text-white shadow-lg
                                bg-gradient-to-br {{ $k['g'] }}
                                group-hover:scale-110 transition">
                        <i class="fas {{ $k['icon'] }} text-xs"></i>
                    </div>
                </div>
                <div class="relative mt-3 flex items-end justify-between gap-2">
                    <div class="text-3xl font-black tabular-nums text-slate-800 dark:text-white leading-none">
                        {{ number_format($k['value']) }}
                    </div>
                    {{-- Sparkline placeholder --}}
                    <svg viewBox="0 0 60 20" class="w-16 h-5 opacity-40">
                        <polyline points="0,15 10,12 20,14 30,8 40,10 50,5 60,7"
                                  fill="none" stroke="currentColor" stroke-width="1.5"
                                  class="text-{{ $k['accent'] }}-500"/>
                    </svg>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        @foreach($finKpis as $k)
            <div class="group relative overflow-hidden rounded-2xl p-4
                        bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950
                        shadow-xl ring-1 ring-white/10
                        hover:-translate-y-0.5 hover:ring-white/20 transition
                        flex flex-col justify-between min-h-[120px]">
                <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full opacity-30 blur-2xl
                            bg-gradient-to-br {{ $k['g'] }}"></div>
                <div class="relative flex items-start justify-between gap-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-tight">
                        {{ $k['label'] }}
                    </div>
                    <div class="w-9 h-9 shrink-0 rounded-xl grid place-items-center text-white shadow-lg
                                bg-gradient-to-br {{ $k['g'] }}
                                group-hover:scale-110 transition">
                        <i class="fas {{ $k['icon'] }} text-xs"></i>
                    </div>
                </div>
                <div class="relative mt-3 text-lg sm:text-xl font-black tabular-nums leading-none
                            bg-gradient-to-r from-white via-rose-200 to-amber-200
                            bg-clip-text text-transparent">
                    Rp {{ number_format($k['value'], 0, ',', '.') }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============================================================
         FILTER BAR — Advanced
    ============================================================= --}}
    <div x-data="{ advanced: false }"
         class="mb-5 rounded-2xl bg-white dark:bg-slate-900/70 shadow-lg
                ring-1 ring-slate-200/70 dark:ring-white/5 overflow-hidden">
        <form method="GET">
            <div class="p-3 sm:p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2">
                    <div class="relative sm:col-span-2 lg:col-span-4">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="q" value="{{ $filters['q'] }}"
                               placeholder="Cari case number, nama, NIM…"
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl text-sm
                                      bg-slate-50 dark:bg-slate-900/60
                                      border border-slate-200 dark:border-white/10
                                      text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-rose-400/60 focus:border-rose-400 outline-none
                                      min-h-[44px]">
                    </div>

                    <select name="period" x-model="filters.period" @change="onPeriodChange"
                            class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm
                                   bg-slate-50 dark:bg-slate-900/60
                                   border border-slate-200 dark:border-white/10
                                   text-slate-800 dark:text-white min-h-[44px]">
                        @foreach($meta['periods'] as $k => $v)
                            <option value="{{ $k }}" @selected($filters['period'] === $k)>{{ $v }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="lg:col-span-2 inline-flex items-center justify-center gap-2
                                   px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                                   bg-gradient-to-r from-rose-500 to-red-700 shadow-lg
                                   hover:scale-[1.02] active:scale-95 transition min-h-[44px]">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>

                    <a href="{{ route('owner.attendance.penalties') }}"
                       class="lg:col-span-2 inline-flex items-center justify-center
                              px-4 py-2.5 rounded-xl text-sm font-medium
                              border border-slate-300 dark:border-white/10
                              text-slate-700 dark:text-slate-300
                              hover:bg-slate-100 dark:hover:bg-white/5 min-h-[44px]">
                        <i class="fas fa-rotate-left mr-2"></i> Reset
                    </a>

                    <button type="button" @click="advanced = !advanced"
                            class="lg:col-span-2 inline-flex items-center justify-center gap-2
                                   px-4 py-2.5 rounded-xl text-sm font-medium
                                   border border-slate-300 dark:border-white/10
                                   text-slate-700 dark:text-slate-300
                                   hover:bg-slate-100 dark:hover:bg-white/5 min-h-[44px]">
                        <i class="fas" :class="advanced ? 'fa-chevron-up' : 'fa-sliders'"></i>
                        <span x-text="advanced ? 'Tutup' : 'Filter Lanjutan'"></span>
                    </button>
                </div>

                {{-- Advanced filters --}}
                <div x-show="advanced" x-collapse.duration.300ms x-cloak
                     class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                        <input type="date" name="from"
                               value="{{ $filters['period']==='custom' ? $meta['from'] : '' }}"
                               placeholder="Dari tanggal"
                               class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                      border border-slate-200 dark:border-white/10
                                      text-slate-800 dark:text-white min-h-[44px]">
                        <input type="date" name="to"
                               value="{{ $filters['period']==='custom' ? $meta['to'] : '' }}"
                               placeholder="Sampai tanggal"
                               class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                      border border-slate-200 dark:border-white/10
                                      text-slate-800 dark:text-white min-h-[44px]">
                        <input type="number" name="min_amount" value="{{ $filters['min_amount'] }}"
                               placeholder="Nominal min (Rp)"
                               class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                      border border-slate-200 dark:border-white/10
                                      text-slate-800 dark:text-white min-h-[44px]">
                        <input type="number" name="max_amount" value="{{ $filters['max_amount'] }}"
                               placeholder="Nominal max (Rp)"
                               class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                      border border-slate-200 dark:border-white/10
                                      text-slate-800 dark:text-white min-h-[44px]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 mt-2">
                        <select name="status[]" multiple size="1"
                                class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                       border border-slate-200 dark:border-white/10
                                       text-slate-800 dark:text-white min-h-[44px]">
                            <option value="">Status…</option>
                            @foreach($reference['status_labels'] as $k => $v)
                                <option value="{{ $k }}" @selected(in_array($k, $filters['status']))>{{ $v['label'] }}</option>
                            @endforeach
                        </select>
                        <select name="type[]" multiple size="1"
                                class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                       border border-slate-200 dark:border-white/10
                                       text-slate-800 dark:text-white min-h-[44px]">
                            <option value="">Jenis denda…</option>
                            @foreach($reference['type_labels'] as $k => $v)
                                <option value="{{ $k }}" @selected(in_array($k, $filters['type']))>{{ $v['label'] }}</option>
                            @endforeach
                        </select>
                        <select name="severity[]" multiple size="1"
                                class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                       border border-slate-200 dark:border-white/10
                                       text-slate-800 dark:text-white min-h-[44px]">
                            <option value="">Severity…</option>
                            @foreach($reference['severity_labels'] as $k => $v)
                                <option value="{{ $k }}" @selected(in_array($k, $filters['severity']))>{{ $v['label'] }}</option>
                            @endforeach
                        </select>
                        <select name="payment[]" multiple size="1"
                                class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                       border border-slate-200 dark:border-white/10
                                       text-slate-800 dark:text-white min-h-[44px]">
                            <option value="">Pembayaran…</option>
                            @foreach($reference['payment_labels'] as $k => $v)
                                <option value="{{ $k }}" @selected(in_array($k, $filters['payment']))>{{ $v['label'] }}</option>
                            @endforeach
                        </select>
                        <select name="group_id[]" multiple size="1"
                                class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                                       border border-slate-200 dark:border-white/10
                                       text-slate-800 dark:text-white min-h-[44px]">
                            <option value="">Kelompok…</option>
                            @foreach($reference['groups'] as $g)
                                <option value="{{ $g->id }}" @selected(in_array($g->id, $filters['group_id']))>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TOP OFFENDERS + DISTRIBUSI
    ============================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5 mb-5">

        {{-- TOP PELANGGAR --}}
        <div class="lg:col-span-1 rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-900/70 overflow-hidden flex flex-col">
            <div class="px-4 py-3 bg-gradient-to-r from-rose-100/70 to-transparent dark:from-rose-900/20
                        border-b border-rose-100 dark:border-rose-900/30
                        flex items-center gap-2 shrink-0">
                <div class="w-9 h-9 rounded-xl grid place-items-center shadow
                            bg-gradient-to-br from-rose-500 to-red-700 text-white">
                    <i class="fas fa-user-shield text-xs"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">Top Pelanggar</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Periode terpilih</div>
                </div>
                <span class="ml-auto text-[10px] px-2 py-1 rounded-full
                             bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold">
                    {{ count($topPenalties) }}
                </span>
            </div>

            <ol class="divide-y divide-slate-100 dark:divide-white/5 max-h-[560px] overflow-y-auto
                       scrollbar-thin">
                @forelse($topPenalties as $i => $u)
                    <li class="p-3.5 space-y-2.5 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                        <div class="grid grid-cols-[36px_minmax(0,1fr)_auto] items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl grid place-items-center text-xs font-black shadow-lg
                                @if($i===0) bg-gradient-to-br from-yellow-300 to-amber-500 text-slate-900 ring-2 ring-amber-300
                                @elseif($i===1) bg-gradient-to-br from-slate-300 to-slate-500 text-white
                                @elseif($i===2) bg-gradient-to-br from-orange-400 to-amber-600 text-white
                                @else bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300
                                @endif">
                                @if($i < 3)
                                    <i class="fas fa-crown text-[10px]"></i>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                    {{ $u->user->name ?? 'Unknown' }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono">
                                    {{ $u->user->nim ?? '-' }}
                                </div>
                            </div>
                            @if(($u->max_warning ?? 0) > 0)
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full
                                             bg-amber-100 dark:bg-amber-900/30
                                             text-amber-700 dark:text-amber-300 font-bold whitespace-nowrap">
                                    ⚠ W{{ $u->max_warning }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-1.5 text-[10px]">
                            <div class="rounded-lg p-2 bg-slate-50 dark:bg-slate-800/60 text-center">
                                <div class="text-slate-500 text-[9px] uppercase tracking-wider">Kasus</div>
                                <div class="font-black text-slate-800 dark:text-white tabular-nums text-sm">
                                    {{ $u->total_cases }}
                                </div>
                            </div>
                            <div class="rounded-lg p-2 bg-red-50 dark:bg-red-900/20 text-center">
                                <div class="text-red-600 dark:text-red-400 text-[9px] uppercase tracking-wider">Poin</div>
                                <div class="font-black text-red-700 dark:text-red-300 tabular-nums text-sm">
                                    {{ $u->total_points }}
                                </div>
                            </div>
                            <div class="rounded-lg p-2 bg-rose-50 dark:bg-rose-900/20 text-center">
                                <div class="text-rose-600 dark:text-rose-400 text-[9px] uppercase tracking-wider">Denda</div>
                                <div class="font-black text-rose-700 dark:text-rose-300 tabular-nums text-xs truncate">
                                    {{ ($u->total_amount ?? 0) > 0 ? 'Rp'.number_format($u->total_amount/1000, 0).'k' : '-' }}
                                </div>
                            </div>
                        </div>

                        @php $paidPct = ($u->total_amount ?? 0) > 0 ? round(($u->total_paid / $u->total_amount) * 100) : 100; @endphp
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 transition-all duration-500"
                                     style="width: {{ $paidPct }}%"></div>
                            </div>
                            <div class="text-[9px] font-bold text-slate-500 tabular-nums whitespace-nowrap">
                                {{ $paidPct }}%
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-8 text-center">
                        <i class="fas fa-check-circle text-3xl text-emerald-500 mb-2"></i>
                        <div class="text-sm text-slate-500 font-medium">Tidak ada pelanggaran</div>
                        <div class="text-xs text-slate-400 mt-0.5">pada periode ini</div>
                    </li>
                @endforelse
            </ol>
        </div>

        {{-- DISTRIBUSI --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Severity Heatmap --}}
            <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        bg-white dark:bg-slate-900/70 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-red-100/60 to-transparent dark:from-red-900/20
                            border-b border-red-100 dark:border-red-900/30
                            flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl grid place-items-center shadow
                                bg-gradient-to-br from-red-500 to-rose-700 text-white">
                        <i class="fas fa-signal text-xs"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Distribusi Severity</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400">Tingkat keparahan pelanggaran</div>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @php $maxSev = collect($distribution['by_severity'])->max('count') ?: 1; @endphp
                    @forelse($distribution['by_severity'] as $d)
                        @php $pct = round($d['count'] / $maxSev * 100, 1); @endphp
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="w-3 h-3 rounded-full bg-{{ $d['color'] }}-500
                                             ring-2 ring-{{ $d['color'] }}-300/50 dark:ring-{{ $d['color'] }}-700/40"></span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                                    {{ $d['label'] }}
                                </span>
                                <span class="ml-auto text-[11px] text-slate-500 tabular-nums font-semibold">
                                    {{ $d['count'] }} kasus
                                </span>
                            </div>
                            <div class="h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden
                                        ring-1 ring-slate-200/50 dark:ring-white/5">
                                <div class="h-full rounded-full bg-gradient-to-r
                                            from-{{ $d['color'] }}-400 to-{{ $d['color'] }}-600
                                            transition-all duration-700 ease-out"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-slate-500 py-6">
                            <i class="fas fa-inbox text-2xl mb-2 opacity-40"></i>
                            <div>Belum ada data.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- By Type --}}
            <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        bg-white dark:bg-slate-900/70 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-fuchsia-100/60 to-transparent dark:from-fuchsia-900/20
                            border-b border-fuchsia-100 dark:border-fuchsia-900/30
                            flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl grid place-items-center shadow
                                bg-gradient-to-br from-fuchsia-500 to-rose-700 text-white">
                        <i class="fas fa-shapes text-xs"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Distribusi Jenis Denda</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400">Kategori konsekuensi</div>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @php $maxTy = collect($distribution['by_type'])->max('count') ?: 1; @endphp
                    @forelse($distribution['by_type'] as $d)
                        @php $pct = round($d['count'] / $maxTy * 100, 1); @endphp
                        <div>
                            <div class="grid grid-cols-[20px_minmax(0,1fr)_auto] items-center gap-2 mb-1.5">
                                <i class="fas {{ $d['icon'] }} text-sm text-{{ $d['color'] }}-500"></i>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider truncate">
                                    {{ $d['label'] }}
                                </span>
                                <span class="text-[11px] text-slate-500 tabular-nums whitespace-nowrap font-semibold">
                                    {{ $d['count'] }}×
                                    @if(($d['amount'] ?? 0) > 0)
                                        <span class="text-rose-500">· Rp{{ number_format($d['amount'], 0, ',', '.') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden
                                        ring-1 ring-slate-200/50 dark:ring-white/5">
                                <div class="h-full rounded-full bg-gradient-to-r
                                            from-{{ $d['color'] }}-400 to-{{ $d['color'] }}-600
                                            transition-all duration-700 ease-out"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-slate-500 py-6">
                            <i class="fas fa-inbox text-2xl mb-2 opacity-40"></i>
                            <div>Belum ada data.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         TREND 12 BULAN
    ============================================================= --}}
    <div class="mb-5 rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                bg-white dark:bg-slate-900/70 p-4">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-9 h-9 rounded-xl grid place-items-center shadow
                        bg-gradient-to-br from-fuchsia-500 to-rose-700 text-white">
                <i class="fas fa-chart-line text-xs"></i>
            </div>
            <div>
                <div class="font-bold text-slate-800 dark:text-white text-sm">Tren Denda 12 Bulan</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400">Total nominal vs pembayaran</div>
            </div>
        </div>
        @php $maxTr = collect($trend)->max('amount') ?: 1; @endphp
        <div class="-mx-4 px-4 overflow-x-auto pb-2 scrollbar-thin">
            <div class="flex items-end gap-3 h-40 min-w-min">
                @foreach($trend as $t)
                    @php
                        $h = round(($t['amount'] ?? 0) / $maxTr * 100, 1);
                        $paidPct = ($t['amount'] ?? 0) > 0 ? (($t['paid'] / $t['amount']) * 100) : 0;
                    @endphp
                    <div class="flex flex-col items-center gap-1 shrink-0 min-w-[68px] group">
                        <div class="flex items-end gap-0.5 h-32">
                            <div class="w-8 rounded-t-lg bg-gradient-to-t from-rose-500 to-red-700 relative
                                        shadow-lg group-hover:scale-105 transition-transform cursor-pointer"
                                 style="height: {{ max(6, $h) }}%"
                                 title="Total: Rp{{ number_format($t['amount'] ?? 0, 0, ',', '.') }}">
                                <div class="absolute bottom-0 left-0 right-0
                                            bg-gradient-to-t from-emerald-500 to-emerald-400
                                            rounded-t-lg"
                                     style="height: {{ $paidPct }}%"></div>
                            </div>
                        </div>
                        <div class="text-[10px] font-mono font-bold text-slate-500">{{ $t['period'] }}</div>
                        <div class="text-[10px] font-bold tabular-nums text-rose-600 dark:text-rose-400">
                            {{ $t['count'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex items-center justify-center gap-5 mt-3 text-[10px] text-slate-500">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-gradient-to-br from-rose-500 to-red-700"></span>
                Total denda
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-gradient-to-br from-emerald-500 to-emerald-400"></span>
                Sudah dibayar
            </span>
        </div>
    </div>

    {{-- ============================================================
         RULE ENGINE VIEWER
    ============================================================= --}}
    @if(isset($rules) && $rules->count())
        <div class="mb-5 rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-900/70 overflow-hidden"
             x-data="{ openRules: false }">
            <button type="button" @click="openRules = !openRules"
                    class="w-full px-4 py-3 flex items-center gap-3 text-left
                           bg-gradient-to-r from-indigo-100/60 via-violet-100/40 to-transparent
                           dark:from-indigo-900/20
                           hover:from-indigo-100 dark:hover:from-indigo-900/30 transition">
                <div class="w-9 h-9 rounded-xl grid place-items-center shadow
                            bg-gradient-to-br from-indigo-500 to-violet-700 text-white">
                    <i class="fas fa-scroll text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 dark:text-white text-sm">
                        Rule Engine Aktif
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                        {{ $rules->count() }} aturan penalty terkonfigurasi
                    </div>
                </div>
                <i class="fas text-slate-400 transition-transform duration-300"
                   :class="openRules ? 'fa-chevron-up rotate-0' : 'fa-chevron-down'"></i>
            </button>

            <div x-show="openRules" x-collapse.duration.300ms x-cloak
                 class="border-t border-slate-100 dark:border-white/5">
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/50
                                      text-[10px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-3 py-2.5 text-left">Code</th>
                                <th class="px-3 py-2.5 text-left">Nama</th>
                                <th class="px-3 py-2.5 text-left">Pelanggaran</th>
                                <th class="px-3 py-2.5 text-center">Jenis</th>
                                <th class="px-3 py-2.5 text-center">Severity</th>
                                <th class="px-3 py-2.5 text-center">Occurrence</th>
                                <th class="px-3 py-2.5 text-right">Poin</th>
                                <th class="px-3 py-2.5 text-right">Denda</th>
                                <th class="px-3 py-2.5 text-right">Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @foreach($rules as $r)
                                @php
                                    $sev = \App\Http\Controllers\Owner\AttendancePenaltyController::SEVERITY_LABELS[$r->severity] ?? ['label'=>$r->severity,'color'=>'slate'];
                                    $vt  = \App\Http\Controllers\Owner\AttendancePenaltyController::VIOLATION_LABELS[$r->violation_type] ?? ['label'=>$r->violation_type];
                                    $tt  = \App\Http\Controllers\Owner\AttendancePenaltyController::TYPE_LABELS[$r->penalty_type] ?? ['label'=>$r->penalty_type,'color'=>'slate'];
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                    <td class="px-3 py-2.5 font-mono text-slate-600 dark:text-slate-300 font-semibold">
                                        {{ $r->code }}
                                    </td>
                                    <td class="px-3 py-2.5 font-medium text-slate-800 dark:text-white">
                                        {{ $r->name }}
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">
                                        {{ $vt['label'] }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold
                                                     bg-{{ $tt['color'] ?? 'slate' }}-100 dark:bg-{{ $tt['color'] ?? 'slate' }}-900/40
                                                     text-{{ $tt['color'] ?? 'slate' }}-700 dark:text-{{ $tt['color'] ?? 'slate' }}-300">
                                            {{ $tt['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold
                                                     bg-{{ $sev['color'] }}-100 dark:bg-{{ $sev['color'] }}-900/40
                                                     text-{{ $sev['color'] }}-700 dark:text-{{ $sev['color'] }}-300">
                                            {{ $sev['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-slate-600 dark:text-slate-300">
                                        <span class="font-bold">{{ $r->min_occurrences }}</span>
                                        <span class="text-slate-400">–</span>
                                        <span class="font-bold">{{ $r->max_occurrences ?? '∞' }}</span>
                                        <div class="text-[9px] text-slate-400 uppercase tracking-wider">
                                            {{ $r->period_scope }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-bold tabular-nums
                                               {{ $r->point_penalty < 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-500' }}">
                                        {{ $r->point_penalty > 0 ? '+' : '' }}{{ $r->point_penalty }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-bold tabular-nums
                                               {{ $r->financial_amount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                        {{ $r->financial_amount > 0 ? 'Rp '.number_format($r->financial_amount, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-slate-500 text-[11px]">
                                        {{ $r->due_days > 0 ? $r->due_days.' hari' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         LEDGER / KASUS LIST
    ============================================================= --}}
    <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                bg-white dark:bg-slate-900/70 overflow-hidden">
        <div class="px-4 py-3 flex items-center gap-3 flex-wrap
                    bg-gradient-to-r from-rose-100/60 via-red-100/40 to-transparent
                    dark:from-rose-900/20 border-b border-rose-100 dark:border-rose-900/30">
            <div class="w-9 h-9 rounded-xl grid place-items-center shadow
                        bg-gradient-to-br from-rose-500 to-red-700 text-white">
                <i class="fas fa-list-ul text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-800 dark:text-white text-sm">Daftar Kasus Denda</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                    <span x-text="selected.length"></span> terpilih dari {{ $penalties->total() }} kasus
                </div>
            </div>

            {{-- Bulk action bar --}}
            <div x-show="selected.length > 0" x-transition x-cloak
                 class="flex items-center gap-2">
                <button type="button" @click="bulkAction('approve')"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold text-white
                               bg-gradient-to-r from-emerald-500 to-teal-600 shadow
                               hover:scale-105 transition">
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
                <button type="button" @click="bulkAction('waive')"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold
                               text-cyan-700 dark:text-cyan-300
                               bg-cyan-100 dark:bg-cyan-900/40
                               hover:scale-105 transition">
                    <i class="fas fa-shield mr-1"></i> Waive
                </button>
                <button type="button" @click="bulkAction('cancel')"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold text-white
                               bg-gradient-to-r from-rose-500 to-red-700 shadow
                               hover:scale-105 transition">
                    <i class="fas fa-ban mr-1"></i> Cancel
                </button>
                <button type="button" @click="selected = []"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-500
                               hover:bg-slate-100 dark:hover:bg-white/5 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- DESKTOP TABLE --}}
        <div class="hidden md:block overflow-x-auto scrollbar-thin">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50
                              text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-3 py-3 w-8">
                            <input type="checkbox" class="rounded accent-rose-500"
                                   @change="toggleAll($event)">
                        </th>
                        <th class="px-3 py-3 text-left">No. Kasus</th>
                        <th class="px-3 py-3 text-left">User</th>
                        <th class="px-3 py-3 text-left">Pelanggaran</th>
                        <th class="px-3 py-3 text-left">Sesi</th>
                        <th class="px-3 py-3 text-center">Severity</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-3 py-3 text-right">Poin</th>
                        <th class="px-3 py-3 text-right">Denda</th>
                        <th class="px-3 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($penalties as $p)
                        @php
                            $smeta = \App\Http\Controllers\Owner\AttendancePenaltyController::STATUS_LABELS[$p->status] ?? ['label'=>$p->status,'color'=>'slate','icon'=>'fa-circle'];
                            $vmeta = \App\Http\Controllers\Owner\AttendancePenaltyController::VIOLATION_LABELS[$p->violation_type] ?? ['label'=>$p->violation_type,'color'=>'slate'];
                            $sev   = \App\Http\Controllers\Owner\AttendancePenaltyController::SEVERITY_LABELS[$p->severity] ?? ['label'=>$p->severity,'color'=>'slate'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition
                                   {{ $p->is_overdue ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                            <td class="px-3 py-3">
                                <input type="checkbox" class="rounded accent-rose-500"
                                       value="{{ $p->id }}" x-model="selected">
                            </td>
                            <td class="px-3 py-3 font-mono text-xs text-slate-500 whitespace-nowrap">
                                <span class="font-bold text-slate-700 dark:text-slate-300">
                                    {{ $p->case_number }}
                                </span>
                                @if($p->is_overdue)
                                    <div class="text-[9px] text-red-500 font-bold uppercase tracking-wider mt-0.5">
                                        <i class="fas fa-clock"></i> Overdue {{ $p->days_overdue }}d
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full grid place-items-center shrink-0
                                                bg-gradient-to-br from-slate-500 to-slate-700 text-white text-[10px] font-bold">
                                        {{ strtoupper(substr($p->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-800 dark:text-white truncate max-w-[150px]">
                                            {{ $p->user->name ?? '-' }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            {{ $p->user->nim ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-lg
                                             bg-{{ $vmeta['color'] }}-50 dark:bg-{{ $vmeta['color'] }}-900/30
                                             text-{{ $vmeta['color'] }}-700 dark:text-{{ $vmeta['color'] }}-300 font-semibold whitespace-nowrap">
                                    {{ $vmeta['label'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-400 truncate max-w-[180px]">
                                {{ $p->session->title ?? '—' }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full
                                             bg-{{ $sev['color'] }}-100 dark:bg-{{ $sev['color'] }}-900/40
                                             text-{{ $sev['color'] }}-700 dark:text-{{ $sev['color'] }}-300 uppercase tracking-wider">
                                    {{ $sev['label'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-full
                                             bg-{{ $smeta['color'] }}-100 dark:bg-{{ $smeta['color'] }}-900/40
                                             text-{{ $smeta['color'] }}-700 dark:text-{{ $smeta['color'] }}-300">
                                    <i class="fas {{ $smeta['icon'] }}"></i> {{ $smeta['label'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-right font-extrabold tabular-nums
                                       {{ $p->points < 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-500' }}">
                                {{ $p->points > 0 ? '+' : '' }}{{ $p->points }}
                            </td>
                            <td class="px-3 py-3 text-right font-extrabold tabular-nums whitespace-nowrap
                                       {{ $p->amount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                {{ $p->amount > 0 ? 'Rp '.number_format($p->amount, 0, ',', '.') : '—' }}
                                @if($p->amount > 0 && $p->outstanding > 0)
                                    <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold">
                                        Sisa: Rp {{ number_format($p->outstanding, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                <button type="button"
                                        class="inline-grid place-items-center w-8 h-8 rounded-lg
                                               text-slate-500 hover:text-rose-600
                                               hover:bg-rose-50 dark:hover:bg-rose-900/20 transition"
                                        @click="openDetail({{ $p->id }})"
                                        title="Lihat detail">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-slate-500 py-16">
                                <i class="fas fa-inbox text-4xl mb-3 opacity-40"></i>
                                <div class="font-medium">Belum ada kasus denda</div>
                                <div class="text-xs mt-1">pada periode &amp; filter ini</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARD --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-white/5">
            @forelse($penalties as $p)
                @php
                    $smeta = \App\Http\Controllers\Owner\AttendancePenaltyController::STATUS_LABELS[$p->status] ?? ['label'=>$p->status,'color'=>'slate','icon'=>'fa-circle'];
                    $vmeta = \App\Http\Controllers\Owner\AttendancePenaltyController::VIOLATION_LABELS[$p->violation_type] ?? ['label'=>$p->violation_type,'color'=>'slate'];
                @endphp
                <div class="p-3.5 space-y-2.5
                            {{ $p->is_overdue ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" class="rounded accent-rose-500 mt-1"
                               value="{{ $p->id }}" x-model="selected">
                        <button type="button" @click="openDetail({{ $p->id }})"
                                class="flex-1 min-w-0 text-left active:scale-[0.98] transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="text-[10px] font-mono text-slate-500 font-bold">
                                        {{ $p->case_number }}
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-white truncate mt-0.5">
                                        {{ $p->user->name ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono">
                                        NIM {{ $p->user->nim ?? '-' }}
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-full shrink-0
                                             bg-{{ $smeta['color'] }}-100 dark:bg-{{ $smeta['color'] }}-900/40
                                             text-{{ $smeta['color'] }}-700 dark:text-{{ $smeta['color'] }}-300">
                                    <i class="fas {{ $smeta['icon'] }}"></i> {{ $smeta['label'] }}
                                </span>
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="inline-flex items-center text-[11px] px-2 py-1 rounded-lg
                                     bg-{{ $vmeta['color'] }}-50 dark:bg-{{ $vmeta['color'] }}-900/30
                                     text-{{ $vmeta['color'] }}-700 dark:text-{{ $vmeta['color'] }}-300 font-semibold">
                            {{ $vmeta['label'] }}
                        </span>
                        @if($p->session)
                            <span class="text-[10px] text-slate-500 truncate">
                                <i class="fas fa-calendar-check mr-1"></i>{{ $p->session->title }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-lg p-2 bg-red-50 dark:bg-red-900/20 text-center">
                            <div class="text-[10px] text-red-600 dark:text-red-400 uppercase tracking-wider font-bold">
                                Poin
                            </div>
                            <div class="text-base font-black text-red-700 dark:text-red-300 tabular-nums">
                                {{ $p->points }}
                            </div>
                        </div>
                        <div class="rounded-lg p-2 bg-rose-50 dark:bg-rose-900/20 text-center">
                            <div class="text-[10px] text-rose-600 dark:text-rose-400 uppercase tracking-wider font-bold">
                                Denda
                            </div>
                            <div class="text-sm font-black text-rose-700 dark:text-rose-300 truncate tabular-nums">
                                {{ $p->amount > 0 ? 'Rp'.number_format($p->amount, 0, ',', '.') : '—' }}
                            </div>
                        </div>
                    </div>

                    @if($p->is_overdue)
                        <div class="flex items-center gap-1.5 text-[10px] text-red-600 dark:text-red-400
                                    font-bold uppercase tracking-wider">
                            <i class="fas fa-clock animate-pulse"></i>
                            Overdue {{ $p->days_overdue }} hari
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-slate-500 py-16">
                    <i class="fas fa-inbox text-4xl mb-3 opacity-40"></i>
                    <div class="font-medium">Belum ada kasus denda</div>
                </div>
            @endforelse
        </div>

        @if($penalties->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-white/5
                        bg-slate-50/50 dark:bg-slate-900/30">
                {{ $penalties->links() }}
            </div>
        @endif
    </div>

    {{-- ============================================================
         FLOATING ACTION BUTTON (mobile)
    ============================================================= --}}
    <button type="button" @click="openCommand()"
            class="lg:hidden fixed bottom-5 right-5 z-40
                   w-14 h-14 rounded-2xl
                   bg-gradient-to-br from-rose-500 to-red-700
                   shadow-2xl shadow-rose-500/50 ring-1 ring-white/20
                   grid place-items-center text-white
                   hover:scale-110 active:scale-95 transition">
        <i class="fas fa-terminal text-lg"></i>
    </button>

    {{-- ============================================================
         COMMAND PALETTE
    ============================================================= --}}
    <div x-show="command.open" x-cloak
         class="fixed inset-0 z-[90] flex items-start justify-center pt-[10vh] p-4
                bg-black/70 backdrop-blur-sm"
         @click="command.open = false">
        <div @click.stop
             class="w-full max-w-2xl bg-slate-900 rounded-2xl shadow-2xl
                    ring-1 ring-white/10 overflow-hidden
                    animate__animated animate__fadeInDown">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-white/10">
                <i class="fas fa-terminal text-rose-400"></i>
                <input type="text" x-model="command.query" @keydown.escape="command.open=false"
                       placeholder="Ketik perintah… (contoh: approve, overdue, export)"
                       class="flex-1 bg-transparent text-white placeholder-white/40 outline-none text-sm">
                <kbd class="text-[10px] text-white/40 px-1.5 py-0.5 rounded bg-white/10">ESC</kbd>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-2">
                <template x-for="cmd in filteredCommands" :key="cmd.action">
                    <button type="button" @click="runCommand(cmd.action)"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left
                                   hover:bg-white/5 transition">
                        <div class="w-8 h-8 rounded-lg grid place-items-center
                                    bg-gradient-to-br from-rose-500/30 to-fuchsia-500/30
                                    ring-1 ring-rose-400/30 text-rose-300">
                            <i class="fas text-xs" :class="cmd.icon"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-white" x-text="cmd.label"></div>
                            <div class="text-[10px] text-white/50" x-text="cmd.desc"></div>
                        </div>
                        <i class="fas fa-arrow-right text-white/30 text-xs"></i>
                    </button>
                </template>
                <div x-show="filteredCommands.length === 0"
                     class="text-center py-8 text-white/40 text-sm">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <div>Tidak ada perintah yang cocok</div>
                </div>
            </div>
            <div class="px-4 py-2 border-t border-white/10 text-[10px] text-white/40
                        flex items-center gap-4">
                <span><kbd class="px-1 rounded bg-white/10">↑↓</kbd> navigasi</span>
                <span><kbd class="px-1 rounded bg-white/10">↵</kbd> jalankan</span>
                <span class="ml-auto">Command Palette v1.0</span>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL DETAIL
    ============================================================= --}}
    <div x-show="modal.open" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4
                bg-black/70 backdrop-blur-sm"
         @keydown.escape.window="modal.open=false">
        <div @click.outside="modal.open=false"
             class="w-full sm:max-w-4xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto
                    bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl
                    shadow-2xl ring-1 ring-white/10 scrollbar-thin">

            {{-- Modal Header --}}
            <div class="sticky top-0 z-10 bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900
                        text-white p-4 sm:p-5 flex items-center gap-3
                        rounded-t-3xl border-b border-white/10">
                <div class="w-11 h-11 rounded-2xl grid place-items-center shadow-lg
                            bg-gradient-to-br from-rose-500 to-red-700 ring-1 ring-white/20">
                    <i class="fas fa-gavel text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[10px] uppercase tracking-widest text-rose-200/80 font-bold">
                        Detail Kasus
                    </div>
                    <div class="font-bold truncate text-lg"
                         x-text="modal.data?.case_number || '—'"></div>
                </div>
                <button type="button" @click="modal.open=false"
                        class="w-9 h-9 rounded-lg grid place-items-center
                               hover:bg-white/10 transition text-white/70 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Loading --}}
            <div x-show="modal.loading" class="p-16 text-center text-slate-500">
                <i class="fas fa-spinner fa-spin text-3xl text-rose-500"></i>
                <div class="mt-3 text-sm font-medium">Memuat detail kasus…</div>
            </div>

            {{-- Content --}}
            <div x-show="!modal.loading && modal.data" x-cloak>

                {{-- Tabs --}}
                <div class="border-b border-slate-200 dark:border-white/10 px-4 sm:px-6 pt-4">
                    <div class="flex gap-1 overflow-x-auto scrollbar-thin">
                        @foreach(['overview' => 'Ringkasan', 'timeline' => 'Timeline',
                                  'disputes' => 'Keberatan', 'audit' => 'Audit Log'] as $tab => $label)
                            <button type="button" @click="modal.tab = '{{ $tab }}'"
                                    class="px-4 py-2.5 text-xs font-bold rounded-t-lg whitespace-nowrap transition
                                           border-b-2"
                                    :class="modal.tab === '{{ $tab }}'
                                        ? 'text-rose-600 dark:text-rose-400 border-rose-500 bg-rose-50/50 dark:bg-rose-900/10'
                                        : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-white/5'">
                                {{ $label }}
                                @if($tab === 'disputes')
                                    <span x-show="modal.disputes && modal.disputes.length"
                                          class="ml-1 px-1.5 py-0.5 rounded-full text-[9px]
                                                 bg-fuchsia-500 text-white"
                                          x-text="modal.disputes?.length || 0"></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">

                    {{-- TAB: OVERVIEW --}}
                    <div x-show="modal.tab === 'overview'" x-transition>
                        {{-- Summary grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="rounded-xl p-3 bg-slate-50 dark:bg-slate-800/70 text-center">
                                <div class="text-[10px] text-slate-500 uppercase tracking-wider">Status</div>
                                <div class="font-bold text-sm mt-1" x-text="modal.data?.status_label"></div>
                            </div>
                            <div class="rounded-xl p-3 bg-red-50 dark:bg-red-900/20 text-center">
                                <div class="text-[10px] text-red-600 uppercase tracking-wider">Poin</div>
                                <div class="font-black text-lg mt-1 text-red-700 dark:text-red-300 tabular-nums"
                                     x-text="modal.data?.points"></div>
                            </div>
                            <div class="rounded-xl p-3 bg-rose-50 dark:bg-rose-900/20 text-center">
                                <div class="text-[10px] text-rose-600 uppercase tracking-wider">Denda</div>
                                <div class="font-black text-sm mt-1 text-rose-700 dark:text-rose-300 tabular-nums"
                                     x-text="modal.data ? 'Rp ' + Number(modal.data.amount).toLocaleString('id-ID') : '—'"></div>
                            </div>
                            <div class="rounded-xl p-3 bg-emerald-50 dark:bg-emerald-900/20 text-center">
                                <div class="text-[10px] text-emerald-600 uppercase tracking-wider">Sisa</div>
                                <div class="font-black text-sm mt-1 text-emerald-700 dark:text-emerald-300 tabular-nums"
                                     x-text="modal.data ? 'Rp ' + Number(modal.data.outstanding).toLocaleString('id-ID') : '—'"></div>
                            </div>
                        </div>

                        {{-- Info grid --}}
                        <div class="mt-5 rounded-2xl bg-slate-50 dark:bg-slate-800/70 p-4 space-y-2.5 text-sm">
                            <div class="grid grid-cols-3 gap-3">
                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">User</div>
                                <div class="col-span-2 font-medium" x-text="modal.data?.user?.name || '—'"></div>

                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">NIM</div>
                                <div class="col-span-2 font-medium font-mono" x-text="modal.data?.user?.nim || '—'"></div>

                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Kelompok</div>
                                <div class="col-span-2 font-medium" x-text="modal.data?.group || '—'"></div>

                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Sesi</div>
                                <div class="col-span-2 font-medium" x-text="modal.data?.session || '—'"></div>

                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Pelanggaran</div>
                                <div class="col-span-2 font-medium" x-text="modal.data?.violation_label || '—'"></div>

                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Jatuh Tempo</div>
                                <div class="col-span-2 font-medium"
                                     x-text="modal.data?.due_at ? new Date(modal.data.due_at).toLocaleDateString('id-ID', {dateStyle:'full'}) : '—'"></div>

                                <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Alasan</div>
                                <div class="col-span-2 text-slate-700 dark:text-slate-300 leading-relaxed"
                                     x-text="modal.data?.reason || '—'"></div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-5 border-t border-slate-200 dark:border-white/10 pt-4
                                    flex flex-wrap gap-2">
                            <template x-if="modal.data?.status === 'pending'">
                                <form :action="`{{ url('owner/attendance/penalties') }}/${modal.data.id}/approve`"
                                      method="POST" class="inline">
                                    @csrf
                                    <button class="px-4 py-2.5 rounded-xl text-sm font-bold text-white
                                                   bg-gradient-to-r from-emerald-500 to-teal-600 shadow-lg
                                                   hover:scale-[1.02] active:scale-95 transition min-h-[44px]">
                                        <i class="fas fa-check mr-1.5"></i> Approve
                                    </button>
                                </form>
                            </template>

                            <template x-if="['pending','review'].includes(modal.data?.status)">
                                <button type="button" @click="showAction('reject')"
                                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-white
                                               bg-gradient-to-r from-rose-500 to-red-700 shadow-lg
                                               hover:scale-[1.02] active:scale-95 transition min-h-[44px]">
                                    <i class="fas fa-xmark mr-1.5"></i> Tolak
                                </button>
                            </template>

                            <template x-if="modal.data && Number(modal.data.outstanding) > 0">
                                <button type="button" @click="showAction('pay')"
                                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-white
                                               bg-gradient-to-r from-cyan-500 to-blue-600 shadow-lg
                                               hover:scale-[1.02] active:scale-95 transition min-h-[44px]">
                                    <i class="fas fa-money-bill-wave mr-1.5"></i> Bayar
                                </button>
                            </template>

                            <template x-if="['active','partial','overdue','disputed'].includes(modal.data?.status)">
                                <button type="button" @click="showAction('cancel')"
                                        class="px-4 py-2.5 rounded-xl text-sm font-bold
                                               border border-rose-300 dark:border-rose-700/50
                                               text-rose-700 dark:text-rose-300
                                               hover:bg-rose-50 dark:hover:bg-rose-900/20
                                               transition min-h-[44px]">
                                    <i class="fas fa-ban mr-1.5"></i> Batalkan
                                </button>
                            </template>

                            <button type="button" @click="showAction('waive')"
                                    class="px-4 py-2.5 rounded-xl text-sm font-bold
                                           border border-slate-300 dark:border-white/10
                                           text-slate-700 dark:text-slate-300
                                           hover:bg-slate-100 dark:hover:bg-white/5
                                           transition min-h-[44px]">
                                <i class="fas fa-shield mr-1.5"></i> Bebaskan
                            </button>

                            <button type="button" @click="showAction('reverse')"
                                    class="px-4 py-2.5 rounded-xl text-sm font-bold
                                           border border-slate-300 dark:border-white/10
                                           text-slate-700 dark:text-slate-300
                                           hover:bg-slate-100 dark:hover:bg-white/5
                                           transition min-h-[44px]">
                                <i class="fas fa-rotate-left mr-1.5"></i> Reversal
                            </button>
                        </div>

                        <div class="mt-3">
                            <button type="button" @click="showAction('dispute')"
                                    class="w-full px-4 py-2.5 rounded-xl text-sm font-bold
                                           bg-fuchsia-500/10 text-fuchsia-700 dark:text-fuchsia-300
                                           border border-fuchsia-300/50
                                           hover:bg-fuchsia-500/20 transition min-h-[44px]">
                                <i class="fas fa-gavel mr-1.5"></i> Ajukan Keberatan (Dispute)
                            </button>
                        </div>
                    </div>

                    {{-- TAB: TIMELINE --}}
                    <div x-show="modal.tab === 'timeline'" x-transition>
                        <ol class="relative border-l-2 border-slate-200 dark:border-white/10 ml-3 space-y-5">
                            <template x-for="(t, idx) in modal.timeline" :key="idx">
                                <li class="pl-6 relative">
                                    <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full
                                                 bg-gradient-to-br from-rose-500 to-red-700
                                                 ring-4 ring-white dark:ring-slate-900
                                                 shadow-lg"></span>
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                                         x-text="t.description"></div>
                                    <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-2">
                                        <i class="fas fa-user-circle"></i>
                                        <span x-text="t.actor"></span>
                                        <span class="text-slate-400">·</span>
                                        <i class="fas fa-clock"></i>
                                        <span x-text="t.at_human"></span>
                                    </div>
                                    <template x-if="t.before && t.after && Object.keys(t.before).length">
                                        <div class="mt-2 text-[10px] rounded-lg
                                                    bg-slate-50 dark:bg-slate-900/60
                                                    border border-slate-200 dark:border-white/5
                                                    p-2.5 space-y-1">
                                            <template x-for="(v, k) in t.after" :key="k">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-mono text-slate-500 font-bold" x-text="k"></span>
                                                    <span class="line-through text-slate-400"
                                                          x-text="t.before[k] ?? '—'"></span>
                                                    <i class="fas fa-arrow-right text-rose-400 text-[9px]"></i>
                                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400"
                                                          x-text="v"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </li>
                            </template>
                            <template x-if="!modal.timeline || modal.timeline.length === 0">
                                <li class="text-center text-slate-500 py-8">
                                    <i class="fas fa-stream text-3xl mb-2 opacity-40"></i>
                                    <div class="text-sm">Belum ada aktivitas tercatat</div>
                                </li>
                            </template>
                        </ol>
                    </div>

                    {{-- TAB: DISPUTES --}}
                    <div x-show="modal.tab === 'disputes'" x-transition>
                        <template x-if="modal.disputes && modal.disputes.length">
                            <div class="space-y-3">
                                <template x-for="d in modal.disputes" :key="d.id">
                                    <div class="rounded-xl p-4
                                                bg-fuchsia-50/60 dark:bg-fuchsia-900/10
                                                border border-fuchsia-200/50 dark:border-fuchsia-800/40">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full grid place-items-center
                                                            bg-gradient-to-br from-fuchsia-500 to-purple-700
                                                            text-white text-[10px] font-bold">
                                                    <span x-text="(d.user || 'U').charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div class="text-xs font-bold text-fuchsia-800 dark:text-fuchsia-200"
                                                     x-text="d.user || 'User'"></div>
                                            </div>
                                            <span class="text-[10px] px-2 py-1 rounded-full font-bold uppercase
                                                         bg-fuchsia-200 dark:bg-fuchsia-900/40
                                                         text-fuchsia-800 dark:text-fuchsia-200"
                                                  x-text="d.status"></span>
                                        </div>
                                        <div class="text-sm text-fuchsia-900/80 dark:text-fuchsia-100/80
                                                    leading-relaxed"
                                             x-text="d.reason"></div>
                                        <template x-if="d.review_notes">
                                            <div class="mt-2 pt-2 border-t border-fuchsia-200/50
                                                        dark:border-fuchsia-800/40
                                                        text-[11px] italic
                                                        text-fuchsia-700 dark:text-fuchsia-300">
                                                <i class="fas fa-comment-dots mr-1"></i>
                                                Review: <span x-text="d.review_notes"></span>
                                            </div>
                                        </template>
                                        <template x-if="d.reviewed_at">
                                            <div class="text-[10px] text-fuchsia-600 dark:text-fuchsia-400 mt-1">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Reviewed:
                                                <span x-text="new Date(d.reviewed_at).toLocaleString('id-ID')"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!modal.disputes || modal.disputes.length === 0">
                            <div class="text-center py-12 text-slate-500">
                                <i class="fas fa-gavel text-4xl mb-3 opacity-40"></i>
                                <div class="font-medium">Belum ada keberatan</div>
                                <div class="text-xs mt-1">Tidak ada dispute untuk kasus ini</div>
                            </div>
                        </template>
                    </div>

                    {{-- TAB: AUDIT LOG --}}
                    <div x-show="modal.tab === 'audit'" x-transition>
                        <div class="space-y-2">
                            <template x-for="(t, idx) in modal.timeline" :key="idx">
                                <div class="rounded-lg p-3 bg-slate-50 dark:bg-slate-800/70
                                            border border-slate-200 dark:border-white/5">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200"
                                              x-text="t.action"></span>
                                        <span class="text-[10px] text-slate-500" x-text="t.at_human"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-400"
                                         x-text="t.description"></div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        <i class="fas fa-user mr-1"></i>
                                        <span x-text="t.actor"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL ACTION
    ============================================================= --}}
    <div x-show="action.open" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4
                bg-black/70 backdrop-blur-sm"
         @keydown.escape="action.open = false">
        <form :action="action.url" method="POST"
              @click.outside="action.open = false"
              class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl
                     ring-1 ring-white/10 p-6
                     animate__animated animate__zoomIn animate__faster">
            @csrf

            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl grid place-items-center
                            bg-gradient-to-br from-rose-500 to-red-700 text-white shadow-lg">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="font-bold text-lg text-slate-800 dark:text-white"
                     x-text="action.title"></div>
            </div>

            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4"
               x-text="action.desc"></p>

            <template x-if="action.type === 'pay'">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">
                            Jumlah Bayar (Rp)
                        </label>
                        <input type="number" name="amount" min="1" required
                               class="w-full p-3 rounded-xl text-sm font-mono
                                      bg-slate-50 dark:bg-slate-900/60
                                      border border-slate-200 dark:border-white/10
                                      text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-rose-400/60 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">
                            Metode Pembayaran
                        </label>
                        <select name="method"
                                class="w-full p-3 rounded-xl text-sm
                                       bg-slate-50 dark:bg-slate-900/60
                                       border border-slate-200 dark:border-white/10
                                       text-slate-800 dark:text-white">
                            <option value="cash">💵 Cash</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                            <option value="qris">📱 QRIS</option>
                            <option value="ewallet">💳 E-Wallet</option>
                            <option value="other">📌 Lainnya</option>
                        </select>
                    </div>
                </div>
            </template>

            <template x-if="action.type !== 'pay'">
                <textarea name="reason" required minlength="5" rows="4"
                          placeholder="Alasan / keterangan (min 5 karakter)…"
                          class="w-full p-3 rounded-xl text-sm
                                 bg-slate-50 dark:bg-slate-900/60
                                 border border-slate-200 dark:border-white/10
                                 text-slate-800 dark:text-white
                                 focus:ring-2 focus:ring-rose-400/60 outline-none
                                 resize-none"></textarea>
            </template>

            <div class="mt-5 flex flex-col sm:flex-row justify-end gap-2">
                <button type="button" @click="action.open=false"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold
                               border border-slate-300 dark:border-white/10
                               text-slate-700 dark:text-slate-300
                               hover:bg-slate-100 dark:hover:bg-white/5 transition
                               order-2 sm:order-1 min-h-[44px]">
                    Batal
                </button>
                <button class="px-4 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg
                               bg-gradient-to-r from-rose-500 to-red-700
                               hover:scale-[1.02] active:scale-95 transition
                               order-1 sm:order-2 min-h-[44px]">
                    <i class="fas fa-check mr-1.5"></i> Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     ALPINE COMPONENT
============================================================= --}}
<script>
function penaltyCenter(meta) {
    return {
        filters: { period: meta?.period || 'this_month' },
        modal:   { open: false, loading: false, data: null, timeline: [], disputes: [], tab: 'overview' },
        action:  { open: false, url: '', title: '', desc: '', type: '' },
        command: { open: false, query: '' },
        selected: [],
        toasts: [],

        commands: [
            { action: 'export',     icon: 'fa-file-export',   label: 'Export CSV',        desc: 'Unduh semua kasus sebagai CSV' },
            { action: 'reconcile',  icon: 'fa-rotate',        label: 'Reconcile Overdue', desc: 'Tandai denda yang lewat jatuh tempo' },
            { action: 'bulk_waive', icon: 'fa-shield',        label: 'Bulk Waive',        desc: 'Bebaskan banyak kasus sekaligus' },
            { action: 'focus_search', icon: 'fa-search',      label: 'Focus Search',      desc: 'Fokus ke kolom pencarian' },
            { action: 'go_top',     icon: 'fa-arrow-up',      label: 'Kembali ke Atas',   desc: 'Scroll ke atas halaman' },
        ],

        get filteredCommands() {
            if (!this.command.query) return this.commands;
            const q = this.command.query.toLowerCase();
            return this.commands.filter(c =>
                c.label.toLowerCase().includes(q) || c.desc.toLowerCase().includes(q)
            );
        },

        init() {
            const flash = @json(session('success') ?? null);
            if (flash) this.toast(flash, 'success');
            const err = @json(session('error') ?? null);
            if (err) this.toast(err, 'error');
        },

        toast(msg, type = 'info', duration = 4000) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, msg, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, duration);
        },

        closeAll() {
            this.modal.open = false;
            this.action.open = false;
            this.command.open = false;
        },

        onPeriodChange() {
            this.$el.closest('form')?.submit();
        },

        openCommand() {
            this.command.open = true;
            this.command.query = '';
            this.$nextTick(() => {
                document.querySelector('input[x-model="command.query"]')?.focus();
            });
        },

        runCommand(action) {
            this.command.open = false;
            switch (action) {
                case 'export':
                    window.location.href = "{{ route('owner.attendance.penalties.export', request()->query()) }}";
                    break;
                case 'reconcile':
                    document.querySelector('form[action*="reconcile"]')?.submit();
                    break;
                case 'focus_search':
                    document.querySelector('input[name="q"]')?.focus();
                    break;
                case 'go_top':
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    break;
                case 'bulk_waive':
                    if (this.selected.length === 0) {
                        this.toast('Pilih minimal satu kasus terlebih dahulu', 'warning');
                        return;
                    }
                    this.bulkAction('waive');
                    break;
            }
        },

        toggleAll(event) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][x-model="selected"]');
            if (event.target.checked) {
                this.selected = Array.from(checkboxes).map(cb => cb.value);
            } else {
                this.selected = [];
            }
        },

        bulkAction(action) {
            if (!this.selected.length) return;
            const reason = prompt('Alasan bulk action ini?');
            if (!reason || reason.length < 5) {
                this.toast('Alasan minimal 5 karakter', 'warning');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('owner.attendance.penalties.bulk') }}";

            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden'; actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden'; reasonInput.name = 'reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);

            this.selected.forEach(id => {
                const i = document.createElement('input');
                i.type = 'hidden'; i.name = 'ids[]'; i.value = id;
                form.appendChild(i);
            });

            document.body.appendChild(form);
            form.submit();
        },

        async openDetail(id) {
            this.modal.open    = true;
            this.modal.loading = true;
            this.modal.data    = null;
            this.modal.timeline = [];
            this.modal.disputes = [];
            this.modal.tab     = 'overview';

            try {
                const url = `{{ url('owner/attendance/penalties') }}/${id}`;
                const { data } = await axios.get(url);
                this.modal.data     = data.penalty;
                this.modal.timeline = data.timeline || [];
                this.modal.disputes = data.disputes || [];
            } catch (e) {
                console.error(e);
                this.toast('Gagal memuat detail kasus', 'error');
                this.modal.open = false;
            } finally {
                this.modal.loading = false;
            }
        },

        showAction(type) {
            if (!this.modal.data) return;
            const id = this.modal.data.id;
            const base = `{{ url('owner/attendance/penalties') }}/${id}`;

            const configs = {
                reject:  { url: `${base}/reject`,  title: 'Tolak Denda',
                           desc: 'Denda akan ditolak dan tercatat di audit trail.', type: 'reject' },
                cancel:  { url: `${base}/cancel`,  title: 'Batalkan Denda',
                           desc: 'Denda dibatalkan, riwayat tetap tersimpan.', type: 'cancel' },
                waive:   { url: `${base}/waive`,   title: 'Bebaskan Denda',
                           desc: 'Denda dibebaskan tanpa pembayaran.', type: 'waive' },
                reverse: { url: `${base}/reverse`, title: 'Reversal Denda',
                           desc: 'Buat entri reversal (audit-safe).', type: 'reverse' },
                pay:     { url: `${base}/pay`,     title: 'Catat Pembayaran',
                           desc: 'Masukkan jumlah pembayaran yang diterima.', type: 'pay' },
                dispute: { url: `${base}/dispute`, title: 'Ajukan Keberatan',
                           desc: 'Alasan keberatan minimal 10 karakter.', type: 'dispute' },
            };

            const c = configs[type];
            if (!c) return;
            this.action = { open: true, ...c };
        },
    };
}
</script>

<style>
[x-cloak] { display: none !important; }

.scrollbar-thin::-webkit-scrollbar { height: 6px; width: 6px; }
.scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(100,116,139,.4);
    border-radius: 6px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(100,116,139,.6);
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50%      { opacity: 0.5; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

/* Smooth checkbox styling */
input[type="checkbox"] {
    transition: transform 0.15s;
}
input[type="checkbox"]:hover {
    transform: scale(1.15);
}
input[type="checkbox"]:checked {
    transform: scale(1.05);
}
</style>
@endsection