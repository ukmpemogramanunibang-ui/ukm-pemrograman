@extends('layouts.roles.owner')

@section('content')
<div class="w-full min-w-0 max-w-[1600px] mx-auto overflow-x-hidden
            p-3 sm:p-4 md:p-6 safe-x safe-bottom"
     x-data="pointsCenter(@js($meta))"
     x-init="init()">

    {{-- ══════════════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl mb-4 md:mb-5 p-4 md:p-7
                bg-gradient-to-br from-slate-900 via-indigo-900 to-fuchsia-900
                shadow-2xl ring-1 ring-white/10">
        <div class="absolute inset-0 opacity-40 pointer-events-none"
             style="background:
                radial-gradient(500px 200px at 8% 0%,  rgba(168,85,247,.45), transparent 65%),
                radial-gradient(500px 200px at 92% 100%, rgba(34,211,238,.35), transparent 65%),
                radial-gradient(300px 160px at 50% 50%, rgba(250,204,21,.15), transparent 70%);"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-2xl md:text-3xl font-extrabold tracking-tight
                           bg-gradient-to-r from-white via-yellow-200 to-fuchsia-200
                           bg-clip-text text-transparent truncate">
                    <i class="fas fa-star text-yellow-400 mr-1.5"></i> Poin Absensi
                </h1>
                <p class="text-[11px] sm:text-sm text-fuchsia-100/80 mt-1">
                    Point Management &amp; Attendance Scoring Center — UNICODE.
                </p>
                <div class="mt-3 flex flex-wrap gap-1.5 sm:gap-2 text-[10px] sm:text-[11px] text-white/80">
                    <span class="px-2 py-1 rounded-full bg-white/10 ring-1 ring-white/20 whitespace-nowrap">
                        📅 {{ \Carbon\Carbon::parse($meta['from'])->translatedFormat('d M Y') }}
                        → {{ \Carbon\Carbon::parse($meta['to'])->translatedFormat('d M Y') }}
                    </span>
                    @if($pendingCount > 0)
                        <span class="px-2 py-1 rounded-full bg-amber-500/30 ring-1 ring-amber-300/50 text-amber-100 whitespace-nowrap">
                            ⏳ {{ $pendingCount }} approval
                        </span>
                    @endif
                    @if($integrity['ok'] ?? true)
                        <span class="px-2 py-1 rounded-full bg-emerald-500/25 ring-1 ring-emerald-300/40 text-emerald-100 whitespace-nowrap">
                            ✓ Ledger OK
                        </span>
                    @else
                        <span class="px-2 py-1 rounded-full bg-red-500/30 ring-1 ring-red-300/50 text-red-100 whitespace-nowrap">
                            ⚠ {{ count($integrity['mismatch'] ?? []) }} masalah
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 md:flex md:items-center md:gap-2 shrink-0">
                <a href="{{ route('owner.attendance.points.export', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-1.5
                          px-3 py-2.5 md:px-3.5 rounded-xl
                          font-semibold text-white text-xs sm:text-sm
                          bg-white/10 backdrop-blur ring-1 ring-white/20
                          hover:bg-white/20 active:scale-95 transition
                          min-h-[44px]">
                    <i class="fas fa-file-export"></i>
                    <span class="hidden sm:inline">Export CSV</span>
                    <span class="sm:hidden">Export</span>
                </a>
                <a href="{{ route('owner.attendance.points.groups', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-1.5
                          px-3 py-2.5 md:px-3.5 rounded-xl
                          font-semibold text-slate-900 text-xs sm:text-sm
                          bg-gradient-to-r from-yellow-300 via-amber-300 to-yellow-400
                          hover:scale-[1.02] active:scale-95 shadow-lg transition
                          min-h-[44px]">
                    <i class="fas fa-layer-group"></i> Rekap
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         KPI TILES — 2 / 3 / 6 kolom, tinggi seragam
    ═══════════════════════════════════════════════════════════════ --}}
    @php
        $kpis = [
            ['label'=>'Saldo Bersih',    'value'=>$statistics['total_balance'], 'sign'=>true,  'icon'=>'fa-wallet',         'g'=>'from-fuchsia-500 to-purple-700'],
            ['label'=>'Total Diperoleh', 'value'=>$statistics['total_earned'],  'sign'=>true,  'icon'=>'fa-arrow-trend-up', 'g'=>'from-emerald-400 to-teal-600'],
            ['label'=>'Total Dikurangi', 'value'=>$statistics['total_penalty'], 'sign'=>true,  'icon'=>'fa-arrow-trend-down','g'=>'from-red-500 to-rose-700'],
            ['label'=>'Total Transaksi', 'value'=>$statistics['total_entries'], 'sign'=>false, 'icon'=>'fa-receipt',        'g'=>'from-cyan-500 to-blue-600'],
            ['label'=>'Anggota Aktif',   'value'=>$statistics['unique_users'],  'sign'=>false, 'icon'=>'fa-users',          'g'=>'from-indigo-500 to-violet-600'],
            ['label'=>'Menunggu Review', 'value'=>$statistics['pending_count'], 'sign'=>false, 'icon'=>'fa-hourglass-half', 'g'=>'from-amber-400 to-orange-600'],
        ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2.5 sm:gap-3 mb-4 md:mb-5">
        @foreach($kpis as $k)
            <div class="group relative overflow-hidden rounded-2xl p-3 sm:p-4 min-w-0
                        bg-white dark:bg-slate-800/80 shadow-lg
                        ring-1 ring-slate-200/70 dark:ring-white/5
                        hover:-translate-y-0.5 hover:shadow-xl transition
                        flex flex-col justify-between
                        min-h-[100px] sm:min-h-[110px]">
                <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full opacity-25 blur-2xl
                            bg-gradient-to-br {{ $k['g'] }}"></div>

                <div class="relative flex items-start justify-between gap-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider leading-tight min-w-0
                                text-slate-500 dark:text-slate-400 truncate">
                        {{ $k['label'] }}
                    </div>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-lg grid place-items-center
                                text-white shadow bg-gradient-to-br {{ $k['g'] }}">
                        <i class="fas {{ $k['icon'] }} text-[10px] sm:text-xs"></i>
                    </div>
                </div>

                <div class="relative mt-2 text-lg sm:text-2xl font-extrabold tabular-nums
                            leading-none text-slate-800 dark:text-white truncate">
                    @if($k['sign'] && $k['value'] > 0)+@endif{{ number_format($k['value']) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         FILTER BAR — grid 12 kolom dengan span proporsional
    ═══════════════════════════════════════════════════════════════ --}}
    <form method="GET"
          class="mb-4 md:mb-5 rounded-2xl bg-white dark:bg-slate-800/70 shadow-lg
                 ring-1 ring-slate-200/70 dark:ring-white/5 p-3 sm:p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2">
            <div class="relative sm:col-span-2 lg:col-span-3 min-w-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Cari user / alasan…"
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl text-sm min-h-[44px]
                              bg-slate-50 dark:bg-slate-900/60
                              border border-slate-200 dark:border-white/10
                              text-slate-800 dark:text-white
                              focus:ring-2 focus:ring-fuchsia-400/60 outline-none">
            </div>

            <select name="period" x-model="filters.period" @change="onPeriodChange"
                    class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm min-h-[44px]
                           bg-slate-50 dark:bg-slate-900/60
                           border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white">
                @foreach($meta['periods'] as $k => $label)
                    <option value="{{ $k }}" @selected($filters['period'] === $k)>{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ $filters['from'] }}"
                   x-show="filters.period === 'custom'"
                   class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm min-h-[44px]
                          bg-slate-50 dark:bg-slate-900/60
                          border border-slate-200 dark:border-white/10
                          text-slate-800 dark:text-white">

            <input type="date" name="to" value="{{ $filters['to'] }}"
                   x-show="filters.period === 'custom'"
                   class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm min-h-[44px]
                          bg-slate-50 dark:bg-slate-900/60
                          border border-slate-200 dark:border-white/10
                          text-slate-800 dark:text-white">

            <select name="entry_type[]" multiple size="1"
                    class="lg:col-span-3 px-3 py-2.5 rounded-xl text-sm min-h-[44px]
                           bg-slate-50 dark:bg-slate-900/60
                           border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white">
                @foreach($meta['type_labels'] as $k => $v)
                    <option value="{{ $k }}" @selected(in_array($k, $filters['entry_type']))>{{ $v['label'] }}</option>
                @endforeach
            </select>

            <select name="status[]" multiple size="1"
                    class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm min-h-[44px]
                           bg-slate-50 dark:bg-slate-900/60
                           border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white">
                @foreach($meta['status_labels'] as $k => $v)
                    <option value="{{ $k }}" @selected(in_array($k, $filters['status']))>{{ $v['label'] }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="lg:col-span-2 inline-flex items-center justify-center gap-1.5
                           px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                           shadow-lg bg-gradient-to-r from-fuchsia-500 to-purple-600
                           hover:scale-[1.02] active:scale-95 transition min-h-[44px]">
                <i class="fas fa-filter"></i> Filter
            </button>

            <a href="{{ route('owner.attendance.points.index') }}"
               class="lg:col-span-1 inline-flex items-center justify-center
                      px-4 py-2.5 rounded-xl text-sm font-medium min-h-[44px]
                      border border-slate-300 dark:border-white/10
                      text-slate-700 dark:text-slate-300
                      hover:bg-slate-100 dark:hover:bg-white/5">
                Reset
            </a>
        </div>
    </form>

    {{-- ══════════════════════════════════════════════════════════════
         TOP USERS + DISTRIBUSI
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5 mb-4 md:mb-5">

        {{-- Top 10 --}}
        <div class="lg:col-span-1 min-w-0 rounded-2xl shadow-lg
                    ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70 overflow-hidden flex flex-col">
            <div class="px-4 py-3 shrink-0 flex items-center gap-2
                        bg-gradient-to-r from-amber-100/70 to-transparent dark:from-amber-900/20">
                <div class="w-8 h-8 rounded-lg grid place-items-center shadow text-white
                            bg-gradient-to-br from-yellow-300 to-amber-500">
                    <i class="fas fa-trophy text-xs"></i>
                </div>
                <div class="font-bold text-slate-800 dark:text-white text-sm">Top 10 Poin</div>
            </div>

            <ol class="divide-y divide-slate-100 dark:divide-white/5
                       max-h-[420px] overflow-y-auto">
                @forelse($topUsers as $i => $u)
                    <li>
                        <a href="{{ route('owner.attendance.points.user', $u['user_id']) }}?period={{ $filters['period'] }}"
                           class="grid grid-cols-[32px_minmax(0,1fr)_auto] items-center gap-2.5
                                  px-3.5 py-2.5 transition
                                  hover:bg-slate-50 dark:hover:bg-white/5">
                            <span class="w-8 h-8 rounded-full grid place-items-center
                                         text-[11px] font-bold shadow shrink-0
                                @if($i===0) bg-gradient-to-br from-yellow-300 to-amber-500 text-white
                                @elseif($i===1) bg-gradient-to-br from-slate-300 to-slate-500 text-white
                                @elseif($i===2) bg-gradient-to-br from-orange-400 to-amber-600 text-white
                                @else bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300
                                @endif">
                                {{ $i + 1 }}
                            </span>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800 dark:text-white truncate">
                                    {{ $u['user']['name'] ?? '-' }}
                                </div>
                                <div class="text-[10px] text-slate-500 truncate">
                                    NIM {{ $u['user']['nim'] ?? '-' }}
                                </div>
                            </div>
                            <span class="font-bold tabular-nums text-sm whitespace-nowrap
                                         {{ $u['total_points'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $u['total_points'] > 0 ? '+' : '' }}{{ $u['total_points'] }}
                            </span>
                        </a>
                    </li>
                @empty
                    <li class="p-6 text-center text-sm text-slate-500">Belum ada data.</li>
                @endforelse
            </ol>
        </div>

        {{-- Distribusi --}}
        <div class="lg:col-span-2 min-w-0 rounded-2xl shadow-lg
                    ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70 overflow-hidden">
            <div class="px-4 py-3 flex items-center gap-2
                        bg-gradient-to-r from-fuchsia-100/60 to-transparent dark:from-fuchsia-900/20">
                <div class="w-8 h-8 rounded-lg grid place-items-center shadow text-white
                            bg-gradient-to-br from-fuchsia-500 to-purple-600">
                    <i class="fas fa-chart-pie text-xs"></i>
                </div>
                <div class="font-bold text-slate-800 dark:text-white text-sm">Distribusi Aktivitas</div>
            </div>

            <div class="p-4 space-y-3">
                @php $maxCount = collect($distribution)->max('count') ?: 1; @endphp
                @forelse($distribution as $d)
                    @php $pct = round($d['count'] / $maxCount * 100, 1); @endphp
                    <div class="min-w-0">
                        <div class="grid grid-cols-[16px_minmax(0,1fr)_auto] items-center gap-2 mb-1.5">
                            <i class="fas {{ $d['icon'] }} text-xs text-{{ $d['color'] }}-500"></i>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $d['label'] }}
                            </span>
                            <span class="text-[11px] text-slate-500 tabular-nums whitespace-nowrap">
                                {{ $d['count'] }}× ·
                                <span class="{{ $d['points'] >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-semibold">
                                    {{ $d['points'] > 0 ? '+' : '' }}{{ $d['points'] }}
                                </span>
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full transition-[width] duration-500
                                        bg-gradient-to-r from-{{ $d['color'] }}-400 to-{{ $d['color'] }}-600"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-sm text-slate-500 py-6">
                        Belum ada data pada periode ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TREND BULANAN
    ═══════════════════════════════════════════════════════════════ --}}
    @if(count($periodTotals))
        <div class="mb-4 md:mb-5 rounded-2xl shadow-lg min-w-0
                    ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70 p-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg grid place-items-center shadow text-white
                            bg-gradient-to-br from-emerald-500 to-teal-600">
                    <i class="fas fa-chart-line text-xs"></i>
                </div>
                <div class="font-bold text-slate-800 dark:text-white text-sm">Tren Poin Bulanan</div>
            </div>

            @php
                $maxAbs = collect($periodTotals)
                    ->flatMap(fn($p) => [abs($p['earned']), abs($p['lost'])])
                    ->max() ?: 1;
            @endphp

            <div class="overflow-x-auto -mx-4 px-4 pb-2">
                <div class="flex items-end gap-3 h-32 min-w-min">
                    @foreach($periodTotals as $p)
                        @php
                            $earnedH = round($p['earned'] / $maxAbs * 100, 1);
                            $lostH   = round(abs($p['lost']) / $maxAbs * 100, 1);
                        @endphp
                        <div class="flex flex-col items-center gap-1 shrink-0 min-w-[64px]">
                            <div class="flex items-end gap-1 h-24">
                                <div class="w-4 rounded-t-md bg-gradient-to-t from-emerald-400 to-emerald-600"
                                     style="height: {{ max(4, $earnedH) }}%"
                                     title="+{{ $p['earned'] }}"></div>
                                <div class="w-4 rounded-t-md bg-gradient-to-t from-red-400 to-red-600"
                                     style="height: {{ max(4, $lostH) }}%"
                                     title="{{ $p['lost'] }}"></div>
                            </div>
                            <div class="text-[10px] font-mono text-slate-500">{{ $p['period'] }}</div>
                            <div class="text-[10px] font-bold tabular-nums
                                        {{ $p['net'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $p['net'] > 0 ? '+' : '' }}{{ $p['net'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         LEDGER — table desktop + card mobile
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                bg-white dark:bg-slate-800/70 overflow-hidden">
        <div class="px-4 py-3 flex items-center gap-3 flex-wrap
                    bg-gradient-to-r from-cyan-100/60 via-blue-100/40 to-transparent
                    dark:from-cyan-900/20">
            <div class="w-8 h-8 rounded-lg grid place-items-center shadow text-white
                        bg-gradient-to-br from-cyan-500 to-blue-600">
                <i class="fas fa-receipt text-xs"></i>
            </div>
            <div class="font-bold text-slate-800 dark:text-white text-sm">Ledger Transaksi</div>
            <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold
                         bg-cyan-500/20 text-cyan-700 dark:text-cyan-300">
                {{ $ledgers->total() }}
            </span>
        </div>

        {{-- Desktop table (≥ md) --}}
        <div class="hidden md:block overflow-x-auto responsive-table">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50
                              text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-3 py-2.5 text-left">Waktu</th>
                        <th class="px-3 py-2.5 text-left">User</th>
                        <th class="px-3 py-2.5 text-left">Aktivitas</th>
                        <th class="px-3 py-2.5 text-left">Status</th>
                        <th class="px-3 py-2.5 text-left">Alasan / Sesi</th>
                        <th class="px-3 py-2.5 text-right">Poin</th>
                        <th class="px-3 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($ledgers as $l)
                        @php
                            $tmeta = $meta['type_labels'][$l->entry_type]
                                ?? ['label'=>ucfirst((string)$l->entry_type),'color'=>'gray','icon'=>'fa-circle'];
                            $smeta = $meta['status_labels'][$l->status ?? 'final']
                                ?? $meta['status_labels']['final'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                            <td data-label="Waktu" class="px-3 py-2.5 text-xs text-slate-500 whitespace-nowrap">
                                {{ $l->created_at?->format('d/m H:i') }}
                            </td>
                            <td data-label="User" class="px-3 py-2.5 whitespace-nowrap">
                                <a href="{{ route('owner.attendance.points.user', $l->user_id) }}"
                                   class="font-medium text-slate-800 dark:text-white hover:text-fuchsia-600">
                                    {{ $l->user->name ?? ('#'.$l->user_id) }}
                                </a>
                            </td>
                            <td data-label="Aktivitas" class="px-3 py-2.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-lg
                                             bg-{{ $tmeta['color'] }}-50 dark:bg-{{ $tmeta['color'] }}-900/30
                                             text-{{ $tmeta['color'] }}-700 dark:text-{{ $tmeta['color'] }}-300 font-semibold">
                                    <i class="fas {{ $tmeta['icon'] }}"></i> {{ $tmeta['label'] }}
                                </span>
                            </td>
                            <td data-label="Status" class="px-3 py-2.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold
                                             px-2 py-0.5 rounded-full
                                             bg-{{ $smeta['color'] }}-100 dark:bg-{{ $smeta['color'] }}-900/40
                                             text-{{ $smeta['color'] }}-700 dark:text-{{ $smeta['color'] }}-300">
                                    <i class="fas {{ $smeta['icon'] }}"></i> {{ $smeta['label'] }}
                                </span>
                            </td>
                            <td data-label="Alasan / Sesi"
                                class="px-3 py-2.5 text-xs text-slate-600 dark:text-slate-400 max-w-[280px]">
                                @if($l->reason) <div class="truncate">📝 {{ $l->reason }}</div> @endif
                                @if(!empty($l->attendance_session_id))
                                    <a href="{{ route('owner.attendance.show', $l->attendance_session_id) }}"
                                       class="text-[11px] text-cyan-600 hover:underline">
                                        🔗 Sesi #{{ $l->attendance_session_id }}
                                    </a>
                                @endif
                            </td>
                            <td data-label="Poin"
                                class="px-3 py-2.5 text-right font-extrabold tabular-nums whitespace-nowrap
                                       {{ $l->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $l->points > 0 ? '+' : '' }}{{ $l->points }}
                            </td>
                            <td data-label="Aksi" class="px-3 py-2.5 text-right whitespace-nowrap">
                                @if(($l->status ?? 'final') === 'pending')
                                    <form method="POST"
                                          action="{{ route('owner.attendance.points.approve', $l->id) }}"
                                          class="inline">
                                        @csrf
                                        <button class="inline-grid place-items-center w-8 h-8 rounded-lg
                                                       text-emerald-600
                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('owner.attendance.points.reject', $l->id) }}"
                                          class="inline ml-0.5">
                                        @csrf
                                        <button class="inline-grid place-items-center w-8 h-8 rounded-lg
                                                       text-red-600
                                                       hover:bg-red-50 dark:hover:bg-red-900/30">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    </form>
                                @elseif(($l->status ?? 'final') === 'final')
                                    <button type="button"
                                            class="inline-grid place-items-center w-8 h-8 rounded-lg
                                                   text-slate-500 hover:text-fuchsia-600
                                                   hover:bg-fuchsia-50 dark:hover:bg-fuchsia-900/20"
                                            onclick="reverseTx({{ $l->id }})">
                                        <i class="fas fa-rotate-left"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500 py-12">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-40"></i>
                                <div>Belum ada transaksi pada periode &amp; filter ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards (< md) --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-white/5">
            @forelse($ledgers as $l)
                @php
                    $tmeta = $meta['type_labels'][$l->entry_type]
                        ?? ['label'=>ucfirst((string)$l->entry_type),'color'=>'gray','icon'=>'fa-circle'];
                    $smeta = $meta['status_labels'][$l->status ?? 'final']
                        ?? $meta['status_labels']['final'];
                @endphp
                <div class="p-3.5 space-y-2 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('owner.attendance.points.user', $l->user_id) }}"
                               class="font-semibold text-slate-800 dark:text-white truncate block">
                                {{ $l->user->name ?? ('#'.$l->user_id) }}
                            </a>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                {{ $l->created_at?->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <span class="text-lg font-extrabold tabular-nums whitespace-nowrap
                                     {{ $l->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $l->points > 0 ? '+' : '' }}{{ $l->points }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 text-[11px] px-2 py-1 rounded-lg
                                     bg-{{ $tmeta['color'] }}-50 dark:bg-{{ $tmeta['color'] }}-900/30
                                     text-{{ $tmeta['color'] }}-700 dark:text-{{ $tmeta['color'] }}-300 font-semibold">
                            <i class="fas {{ $tmeta['icon'] }}"></i> {{ $tmeta['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold
                                     px-2 py-0.5 rounded-full
                                     bg-{{ $smeta['color'] }}-100 dark:bg-{{ $smeta['color'] }}-900/40
                                     text-{{ $smeta['color'] }}-700 dark:text-{{ $smeta['color'] }}-300">
                            <i class="fas {{ $smeta['icon'] }}"></i> {{ $smeta['label'] }}
                        </span>
                    </div>

                    @if($l->reason || $l->attendance_session_id)
                        <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1 min-w-0">
                            @if($l->reason) <div class="break-words">📝 {{ $l->reason }}</div> @endif
                            @if(!empty($l->attendance_session_id))
                                <a href="{{ route('owner.attendance.show', $l->attendance_session_id) }}"
                                   class="inline-block text-cyan-600 hover:underline">
                                    🔗 Sesi #{{ $l->attendance_session_id }}
                                </a>
                            @endif
                        </div>
                    @endif

                    @if(($l->status ?? 'final') === 'pending')
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <form method="POST"
                                  action="{{ route('owner.attendance.points.approve', $l->id) }}">
                                @csrf
                                <button class="w-full py-2 rounded-lg text-xs font-semibold min-h-[44px]
                                               bg-emerald-500/10
                                               text-emerald-700 dark:text-emerald-300
                                               border border-emerald-300/40">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('owner.attendance.points.reject', $l->id) }}">
                                @csrf
                                <button class="w-full py-2 rounded-lg text-xs font-semibold min-h-[44px]
                                               bg-red-500/10
                                               text-red-700 dark:text-red-300
                                               border border-red-300/40">
                                    <i class="fas fa-xmark mr-1"></i> Reject
                                </button>
                            </form>
                        </div>
                    @elseif(($l->status ?? 'final') === 'final')
                        <button type="button"
                                class="w-full py-2 rounded-lg text-xs font-semibold min-h-[44px]
                                       border border-slate-300 dark:border-white/10
                                       text-slate-600 dark:text-slate-300
                                       hover:bg-slate-50 dark:hover:bg-white/5"
                                onclick="reverseTx({{ $l->id }})">
                            <i class="fas fa-rotate-left mr-1"></i> Reversal
                        </button>
                    @endif
                </div>
            @empty
                <div class="text-center text-slate-500 py-12">
                    <i class="fas fa-inbox text-3xl mb-2 opacity-40"></i>
                    <div>Belum ada transaksi.</div>
                </div>
            @endforelse
        </div>

        @if($ledgers->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-white/5 overflow-x-auto">
                <div class="min-w-fit">
                    {{ $ledgers->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         INTEGRITY REPORT
    ═══════════════════════════════════════════════════════════════ --}}
    @if(!($integrity['ok'] ?? true))
        <div class="mt-4 md:mt-5 rounded-2xl overflow-hidden shadow-lg
                    ring-1 ring-red-300/60
                    bg-gradient-to-r from-red-50 to-rose-50
                    dark:from-red-950/40 dark:to-rose-950/30">
            <div class="p-4 flex items-start gap-3">
                <div class="w-10 h-10 shrink-0 rounded-xl grid place-items-center text-white shadow
                            bg-gradient-to-br from-red-500 to-rose-600">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-red-800 dark:text-red-200">
                        Ketidakcocokan Ledger Terdeteksi
                    </div>
                    <div class="text-sm text-red-700 dark:text-red-300 mt-1">
                        {{ count($integrity['mismatch']) }} user memiliki saldo agregat yang berbeda
                        dengan kolom <code>users.points</code>.
                    </div>
                    <ul class="mt-2 text-xs text-red-700 dark:text-red-300 space-y-0.5">
                        @foreach(array_slice($integrity['mismatch'], 0, 5) as $m)
                            <li>• <b>{{ $m->name }}</b>: kolom = {{ $m->points }}, ledger = {{ $m->ledger_total }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════
     MODAL REVERSAL
═══════════════════════════════════════════════════════════════════ --}}
<div id="reverseModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
    <form method="POST" id="reverseForm"
          class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-5 sm:p-6
                 w-full max-w-md max-h-[90vh] overflow-y-auto">
        @csrf
        <div class="font-bold text-slate-800 dark:text-white mb-2">Konfirmasi Reversal</div>
        <p class="text-sm text-slate-500 mb-3">
            Transaksi tidak akan dihapus — sistem akan membuat transaksi
            <b>REVERSAL</b> yang membatalkan nilai.
        </p>
        <textarea name="reason" required minlength="5" rows="3"
                  placeholder="Alasan reversal…"
                  class="w-full p-3 rounded-xl text-sm
                         border border-slate-200 dark:border-white/10
                         bg-slate-50 dark:bg-slate-900/60
                         text-slate-800 dark:text-white
                         focus:ring-2 focus:ring-red-400/60 outline-none"></textarea>
        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-2">
            <button type="button" onclick="closeReverse()"
                    class="px-4 py-2.5 rounded-xl text-sm min-h-[44px]
                           border border-slate-300 dark:border-white/10
                           order-2 sm:order-1">
                Batal
            </button>
            <button class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white min-h-[44px]
                           bg-gradient-to-r from-red-500 to-rose-600 shadow
                           order-1 sm:order-2">
                Reversal Sekarang
            </button>
        </div>
    </form>
</div>

<script>
function pointsCenter(meta) {
    return {
        filters: { period: meta.period },
        init() {},
        onPeriodChange() {},
    };
}

function reverseTx(id) {
    const m = document.getElementById('reverseModal');
    const f = document.getElementById('reverseForm');
    f.action = "{{ url('owner/attendance/points') }}/" + id + "/reverse";
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeReverse() {
    const m = document.getElementById('reverseModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}
</script>
@endsection