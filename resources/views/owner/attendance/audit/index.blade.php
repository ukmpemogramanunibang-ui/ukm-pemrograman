@extends('layouts.roles.owner')

@section('content')
<div class="p-3 md:p-6 w-full max-w-[1700px] mx-auto"
     x-data="auditCenter()"
     x-init="init()">

    {{-- ============ HEADER LUXURY ============ --}}
    <div class="relative overflow-hidden rounded-2xl mb-5 p-5 md:p-7
                bg-gradient-to-br from-slate-900 via-violet-900 to-indigo-900
                shadow-2xl ring-1 ring-white/10 safe-top">
        <div class="absolute inset-0 opacity-40 pointer-events-none"
             style="background:
                radial-gradient(700px 240px at 8% 0%,  rgba(139,92,246,.45), transparent 65%),
                radial-gradient(700px 240px at 92% 100%, rgba(34,211,238,.35), transparent 65%),
                radial-gradient(400px 200px at 50% 50%, rgba(244,114,182,.18), transparent 70%);"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight
                           bg-gradient-to-r from-white via-violet-200 to-cyan-200 bg-clip-text text-transparent">
                    <i class="fas fa-shield-halved text-violet-400 mr-2"></i>
                    Audit, Security &amp; Forensic Center
                </h1>
                <p class="text-xs sm:text-sm text-violet-100/80 mt-1">
                    Audit trail seluruh aktivitas sistem absensi — immutable, hash-chained, forensic-ready.
                </p>

                <div class="mt-3 flex flex-wrap gap-2 text-[10px] sm:text-[11px] text-white/85">
                    {{-- Health --}}
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full ring-1
                                 {{ $health['ok']
                                    ? 'bg-emerald-500/20 ring-emerald-300/40 text-emerald-100'
                                    : 'bg-red-500/25 ring-red-300/50 text-red-100' }}">
                        <i class="fas {{ $health['ok'] ? 'fa-lock' : 'fa-lock-open' }}"></i>
                        Integrity {{ $health['ok'] ? 'OK' : 'BROKEN' }} · {{ number_format($health['score'], 2) }}%
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-white/10 ring-1 ring-white/20">
                        🔗 {{ number_format($stats['total']) }} event tercatat
                    </span>
                    @if($stats['critical_24h'] > 0)
                        <span class="px-2.5 py-1 rounded-full bg-red-500/30 ring-1 ring-red-400/50 text-red-100">
                            🚨 {{ $stats['critical_24h'] }} critical (24 jam)
                        </span>
                    @endif
                    @if($stats['failed_24h'] > 0)
                        <span class="px-2.5 py-1 rounded-full bg-amber-500/30 ring-1 ring-amber-300/50 text-amber-100">
                            ⚠ {{ $stats['failed_24h'] }} gagal/ditolak (24 jam)
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 md:flex md:items-center gap-2 shrink-0">
                <button type="button" @click="verifyIntegrity()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-white text-xs sm:text-sm
                               bg-white/10 backdrop-blur ring-1 ring-white/20 hover:bg-white/20 transition min-h-[44px]">
                    <i class="fas fa-fingerprint"></i>
                    <span x-text="verifying ? 'Verifying…' : 'Verify Integrity'"></span>
                </button>
                <a href="{{ route('owner.attendance.audit.export', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-slate-900 text-xs sm:text-sm
                          bg-gradient-to-r from-cyan-300 via-teal-300 to-emerald-300
                          hover:scale-[1.02] shadow-lg transition min-h-[44px]">
                    <i class="fas fa-file-export"></i> Export Forensic
                </a>
            </div>
        </div>
    </div>

    {{-- ============ ALERT CENTER ============ --}}
    @if($alerts->isNotEmpty())
        <div class="mb-5 rounded-2xl overflow-hidden shadow-lg ring-1 ring-red-300/50
                    bg-gradient-to-r from-red-50 via-rose-50 to-orange-50
                    dark:from-red-950/40 dark:via-rose-950/30 dark:to-orange-950/30">
            <div class="px-4 py-3 flex items-center gap-3 border-b border-red-200/60 dark:border-red-800/40">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 grid place-items-center text-white shadow">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="font-bold text-red-800 dark:text-red-200">Security Alert Center</div>
                <span class="ml-auto text-[11px] px-2 py-0.5 rounded-full bg-red-500/20 text-red-700 dark:text-red-300 font-bold">
                    {{ $alerts->count() }} aktif
                </span>
            </div>
            <div class="divide-y divide-red-100 dark:divide-red-900/30">
                @foreach($alerts as $a)
                    <div class="px-4 py-3 flex items-start gap-3">
                        <span class="shrink-0 w-2 h-2 mt-2 rounded-full bg-red-500 animate-pulse"></span>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-red-900 dark:text-red-100">{{ $a->title }}</div>
                            <div class="text-xs text-red-700 dark:text-red-300 mt-0.5">{{ $a->message }}</div>
                            <div class="text-[10px] text-red-500 mt-1 font-mono">
                                {{ $a->created_at->diffForHumans() }} · {{ $a->type }} · {{ $a->severity }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('owner.attendance.audit.alerts.ack', $a) }}">
                            @csrf
                            <button class="text-[11px] px-2.5 py-1 rounded-lg bg-red-500/10 text-red-700 dark:text-red-300
                                           hover:bg-red-500/20 font-semibold whitespace-nowrap">
                                Tandai
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ KPI ============ --}}
    @php
        $kpis = [
            ['Total',         $stats['total'],         'fa-database',      'from-slate-500 to-slate-700'],
            ['Hari Ini',      $stats['today'],         'fa-calendar-day',  'from-cyan-500 to-blue-600'],
            ['Minggu Ini',    $stats['this_week'],     'fa-calendar-week', 'from-indigo-500 to-violet-600'],
            ['Bulan Ini',     $stats['this_month'],    'fa-calendar',      'from-violet-500 to-fuchsia-600'],
            ['Critical 24h',  $stats['critical_24h'],  'fa-bolt',          'from-red-500 to-rose-700'],
            ['Failed 24h',    $stats['failed_24h'],    'fa-ban',           'from-amber-400 to-orange-600'],
            ['Sensitive 24h', $stats['sensitive_24h'], 'fa-user-shield',   'from-fuchsia-500 to-purple-700'],
            ['Integrity',     $health['score'].'%',    'fa-fingerprint',   'from-emerald-400 to-teal-600'],
        ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3 mb-5">
        @foreach($kpis as [$label, $value, $icon, $grad])
            <div class="group relative overflow-hidden rounded-2xl p-3.5
                        bg-white dark:bg-slate-800/80 shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        hover:-translate-y-0.5 hover:shadow-xl transition min-h-[100px] flex flex-col justify-between">
                <div class="absolute -right-5 -top-5 w-16 h-16 rounded-full opacity-25 blur-2xl
                            bg-gradient-to-br {{ $grad }}"></div>
                <div class="relative flex items-start justify-between gap-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 leading-tight">
                        {{ $label }}
                    </div>
                    <div class="w-7 h-7 shrink-0 rounded-lg grid place-items-center text-white shadow
                                bg-gradient-to-br {{ $grad }}">
                        <i class="fas {{ $icon }} text-[10px]"></i>
                    </div>
                </div>
                <div class="relative mt-2 text-xl font-extrabold tabular-nums text-slate-800 dark:text-white">
                    {{ is_numeric($value) ? number_format($value) : $value }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============ CRITICAL 24H ============ --}}
    @if($critical->isNotEmpty())
        <div class="mb-5 rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70 overflow-hidden">
            <div class="px-4 py-3 flex items-center gap-2 bg-gradient-to-r from-red-100/70 to-transparent dark:from-red-900/20">
                <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                            bg-gradient-to-br from-red-500 to-rose-600">
                    <i class="fas fa-fire text-xs"></i>
                </div>
                <div class="font-bold text-slate-800 dark:text-white text-sm">Aktivitas Kritis 24 Jam</div>
            </div>
            <div class="grid md:grid-cols-2 xl:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 dark:divide-white/5">
                @foreach($critical as $c)
                    <a href="{{ route('owner.attendance.audit.show', $c) }}"
                       class="flex items-start gap-3 p-3.5 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                        <span class="w-1 self-stretch rounded-full bg-gradient-to-b
                                     {{ $c->severity === 'security' ? 'from-fuchsia-500 to-purple-700' : 'from-red-500 to-rose-700' }}"></span>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-slate-800 dark:text-white font-mono truncate">{{ $c->action }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                {{ $c->actor_name }} · {{ $c->created_at->diffForHumans() }}
                            </div>
                            @if($c->description)
                                <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($c->description, 100) }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ FILTER ============ --}}
    <form method="GET"
          class="mb-5 rounded-2xl bg-white dark:bg-slate-800/70 shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5 p-3 sm:p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2">

            {{-- Search --}}
            <div class="relative sm:col-span-2 lg:col-span-3">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       placeholder="Cari actor, action, reason, IP, correlation…"
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl text-sm
                              bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                              text-slate-800 dark:text-white focus:ring-2 focus:ring-violet-400/60 outline-none min-h-[44px]">
            </div>

            {{-- Action --}}
            <select name="action"
                    class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white min-h-[44px]">
                <option value="">Semua Action</option>
                @foreach($meta['actions'] as $a)
                    <option value="{{ $a }}" @selected($filters['action'] === $a)>{{ $a }}</option>
                @endforeach
            </select>

            {{-- Category --}}
            <select name="category"
                    class="lg:col-span-1 px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white min-h-[44px]">
                <option value="">Kategori</option>
                @foreach($meta['categories'] as $k => $v)
                    <option value="{{ $k }}" @selected($filters['category'] === $k)>{{ $v }}</option>
                @endforeach
            </select>

            {{-- Severity --}}
            <select name="severity[]" multiple size="1"
                    class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white min-h-[44px]">
                @foreach($meta['severities'] as $k => $v)
                    <option value="{{ $k }}" @selected(in_array($k, $filters['severity']))>{{ $v['label'] }}</option>
                @endforeach
            </select>

            {{-- Result --}}
            <select name="result[]" multiple size="1"
                    class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white min-h-[44px]">
                @foreach($meta['results'] as $k => $v)
                    <option value="{{ $k }}" @selected(in_array($k, $filters['result']))>{{ $v['label'] }}</option>
                @endforeach
            </select>

            {{-- Source --}}
            <select name="source[]" multiple size="1"
                    class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white min-h-[44px]">
                @foreach($meta['sources'] as $k => $v)
                    <option value="{{ $k }}" @selected(in_array($k, $filters['source']))>{{ $v }}</option>
                @endforeach
            </select>

            {{-- Actor --}}
            <select name="actor_id"
                    class="lg:col-span-3 px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white min-h-[44px]">
                <option value="">Semua Actor</option>
                @foreach($meta['actors'] as $a)
                    <option value="{{ $a->id }}" @selected((string)$filters['actor_id'] === (string)$a->id)>{{ $a->name }}</option>
                @endforeach
            </select>

            {{-- Date range --}}
            <input type="date" name="from" value="{{ $filters['from'] }}"
                   class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                          border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white min-h-[44px]">
            <input type="date" name="to" value="{{ $filters['to'] }}"
                   class="lg:col-span-2 px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                          border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white min-h-[44px]">

            {{-- Quick toggles --}}
            <label class="lg:col-span-2 inline-flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm
                          bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                          text-slate-700 dark:text-slate-200 cursor-pointer min-h-[44px]">
                <input type="checkbox" name="only_critical" value="1" @checked($filters['only_critical'])
                       class="rounded text-red-500">
                <span class="font-semibold">Hanya Critical</span>
            </label>
            <label class="lg:col-span-2 inline-flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm
                          bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                          text-slate-700 dark:text-slate-200 cursor-pointer min-h-[44px]">
                <input type="checkbox" name="only_failed" value="1" @checked($filters['only_failed'])
                       class="rounded text-amber-500">
                <span class="font-semibold">Hanya Gagal</span>
            </label>
            <label class="lg:col-span-2 inline-flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm
                          bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10
                          text-slate-700 dark:text-slate-200 cursor-pointer min-h-[44px]">
                <input type="checkbox" name="only_sensitive" value="1" @checked($filters['only_sensitive'])
                       class="rounded text-fuchsia-500">
                <span class="font-semibold">Sensitive Only</span>
            </label>

            {{-- Buttons --}}
            <button type="submit"
                    class="lg:col-span-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                           text-sm font-semibold text-white shadow-lg
                           bg-gradient-to-r from-violet-500 to-fuchsia-600 hover:scale-[1.02] transition min-h-[44px]">
                <i class="fas fa-filter"></i> Terapkan
            </button>
            <a href="{{ route('owner.attendance.audit.index') }}"
               class="lg:col-span-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-medium
                      border border-slate-300 dark:border-white/10 text-slate-700 dark:text-slate-300
                      hover:bg-slate-100 dark:hover:bg-white/5 min-h-[44px]">
                Reset
            </a>
        </div>
    </form>

    {{-- ============ LEDGER TABLE ============ --}}
    <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                bg-white dark:bg-slate-800/70 overflow-hidden">
        <div class="px-4 py-3 flex items-center gap-3 flex-wrap
                    bg-gradient-to-r from-violet-100/60 via-fuchsia-100/40 to-transparent dark:from-violet-900/20">
            <div class="w-8 h-8 rounded-lg grid place-items-center shadow
                        bg-gradient-to-br from-violet-500 to-fuchsia-600 text-white">
                <i class="fas fa-receipt text-xs"></i>
            </div>
            <div class="font-bold text-slate-800 dark:text-white text-sm">Audit Ledger</div>
            <span class="text-[11px] px-2 py-0.5 rounded-full bg-violet-500/20 text-violet-700 dark:text-violet-300 font-semibold">
                {{ $logs->total() }} event
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-3 py-2.5 text-left w-[130px]">Waktu</th>
                        <th class="px-3 py-2.5 text-left">Actor</th>
                        <th class="px-3 py-2.5 text-left">Action</th>
                        <th class="px-3 py-2.5 text-left">Severity</th>
                        <th class="px-3 py-2.5 text-left">Result</th>
                        <th class="px-3 py-2.5 text-left">Entity</th>
                        <th class="px-3 py-2.5 text-left">Konteks</th>
                        <th class="px-3 py-2.5 text-right">Integritas</th>
                        <th class="px-3 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($logs as $l)
                        @php
                            $sv = $l->severity_meta;
                            $rs = $l->result_meta;
                            $isValid = $l->isIntegrityValid();
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition
                                   {{ $l->is_sensitive ? 'bg-fuchsia-50/40 dark:bg-fuchsia-950/10' : '' }}">
                            <td class="px-3 py-2.5 text-xs text-slate-500 whitespace-nowrap font-mono">
                                {{ $l->created_at?->format('d/m H:i:s') }}
                            </td>
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <div class="font-semibold text-slate-800 dark:text-white text-xs">
                                    {{ $l->actor_name }}
                                </div>
                                @if($l->actor_role)
                                    <div class="text-[10px] text-slate-500">{{ $l->actor_role }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <span class="inline-block text-[10px] font-mono font-bold px-2 py-0.5 rounded-md
                                             bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                             {{ $l->is_sensitive ? 'ring-1 ring-fuchsia-400/60' : '' }}">
                                    {{ $l->action }}
                                </span>
                                @if($l->is_sensitive)
                                    <span class="ml-1 text-[9px] px-1.5 py-0.5 rounded bg-fuchsia-500/20 text-fuchsia-700 dark:text-fuchsia-300 font-bold">
                                        🔒
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full
                                             bg-{{ $sv['color'] }}-100 dark:bg-{{ $sv['color'] }}-900/40
                                             text-{{ $sv['color'] }}-700 dark:text-{{ $sv['color'] }}-300">
                                    {{ $sv['label'] }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full
                                             bg-{{ $rs['color'] }}-100 dark:bg-{{ $rs['color'] }}-900/40
                                             text-{{ $rs['color'] }}-700 dark:text-{{ $rs['color'] }}-300">
                                    <i class="fas {{ $rs['icon'] }}"></i> {{ $rs['label'] }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                @if($l->session_id)
                                    <a href="{{ route('owner.attendance.show', $l->session_id) }}"
                                       class="text-cyan-600 hover:underline">
                                        🔗 Sesi #{{ $l->session_id }}
                                    </a>
                                @elseif($l->auditable_type)
                                    <span class="font-mono text-[10px]">
                                        {{ class_basename($l->auditable_type) }}#{{ $l->auditable_id }}
                                    </span>
                                @else
                                    <span class="opacity-40">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600 dark:text-slate-400 max-w-[280px]">
                                @if($l->description)
                                    <div class="truncate">{{ \Illuminate\Support\Str::limit($l->description, 60) }}</div>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono truncate">
                                    {{ $l->source_label }} ·
                                    {{ $l->ip_address ?? 'local' }}
                                    @if($l->correlation_id) · {{ $l->correlation_id }} @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5 text-right">
                                @if($isValid)
                                    <span title="Hash valid"
                                          class="inline-flex items-center gap-1 text-[10px] font-bold
                                                 text-emerald-600 dark:text-emerald-400">
                                        <i class="fas fa-lock"></i> OK
                                    </span>
                                @else
                                    <span title="Hash mismatch"
                                          class="inline-flex items-center gap-1 text-[10px] font-bold
                                                 text-red-600 dark:text-red-400">
                                        <i class="fas fa-lock-open"></i> BROKEN
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                <a href="{{ route('owner.attendance.audit.show', $l) }}"
                                   class="inline-grid place-items-center w-8 h-8 rounded-lg
                                          text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-900/30"
                                   title="Detail Forensic">
                                    <i class="fas fa-magnifying-glass"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-slate-500 py-16">
                                <i class="fas fa-inbox text-4xl mb-2 opacity-40"></i>
                                <div>Belum ada event audit pada filter ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-white/5">
            @forelse($logs as $l)
                @php
                    $sv = $l->severity_meta;
                    $rs = $l->result_meta;
                @endphp
                <a href="{{ route('owner.attendance.audit.show', $l) }}"
                   class="block p-3.5 hover:bg-slate-50 dark:hover:bg-white/5 transition
                          {{ $l->is_sensitive ? 'bg-fuchsia-50/40 dark:bg-fuchsia-950/10' : '' }}">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-mono font-bold text-slate-500">
                                {{ $l->created_at?->format('d/m/Y H:i:s') }}
                            </div>
                            <div class="text-sm font-semibold text-slate-800 dark:text-white truncate mt-0.5">
                                {{ $l->actor_name }}
                            </div>
                            <div class="text-[10px] font-mono text-slate-500 mt-0.5">{{ $l->action }}</div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                     bg-{{ $sv['color'] }}-100 dark:bg-{{ $sv['color'] }}-900/40
                                     text-{{ $sv['color'] }}-700 dark:text-{{ $sv['color'] }}-300">
                            {{ $sv['label'] }}
                        </span>
                    </div>
                    @if($l->description)
                        <div class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2">{{ $l->description }}</div>
                    @endif
                    <div class="mt-2 flex items-center gap-2 flex-wrap text-[10px]">
                        <span class="px-2 py-0.5 rounded-full bg-{{ $rs['color'] }}-100 dark:bg-{{ $rs['color'] }}-900/40
                                     text-{{ $rs['color'] }}-700 dark:text-{{ $rs['color'] }}-300 font-bold">
                            {{ $rs['label'] }}
                        </span>
                        <span class="text-slate-400 font-mono">{{ $l->source_label }}</span>
                        <span class="text-slate-400 font-mono">{{ $l->ip_address ?? 'local' }}</span>
                    </div>
                </a>
            @empty
                <div class="text-center text-slate-500 py-12">Belum ada event audit.</div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-white/5">{{ $logs->links() }}</div>
        @endif
    </div>

    {{-- ============ VERIFY RESULT MODAL ============ --}}
    <div x-show="verifyOpen" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl grid place-items-center text-white
                            bg-gradient-to-br from-violet-500 to-fuchsia-600 shadow">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-800 dark:text-white">Integrity Verification</div>
                    <div class="text-xs text-slate-500">Hash chain check</div>
                </div>
            </div>
            <template x-if="verifyResult">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span :class="verifyResult.ok ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold'"
                              x-text="verifyResult.ok ? '✓ VALID' : '✗ BROKEN'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Event diperiksa</span>
                        <span class="font-semibold" x-text="verifyResult.checked"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Rusak</span>
                        <span class="font-semibold" x-text="verifyResult.broken?.length ?? 0"></span>
                    </div>
                </div>
            </template>
            <div class="mt-5 flex justify-end">
                <button @click="verifyOpen = false"
                        class="px-4 py-2 rounded-xl text-sm border border-slate-300 dark:border-white/10
                               text-slate-700 dark:text-slate-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function auditCenter() {
    return {
        verifying: false,
        verifyOpen: false,
        verifyResult: null,

        init() {},

        async verifyIntegrity() {
            this.verifying = true;
            try {
                const { data } = await axios.get('{{ route('owner.attendance.audit.verify') }}', {
                    params: { limit: 500 }
                });
                this.verifyResult = data;
                this.verifyOpen = true;
            } catch (e) {
                this.verifyResult = { ok: false, checked: 0, broken: [{ reason: 'Request failed' }] };
                this.verifyOpen = true;
            } finally {
                this.verifying = false;
            }
        },
    };
}
</script>
@endsection