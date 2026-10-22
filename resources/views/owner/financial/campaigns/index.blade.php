@extends('layouts.roles.owner')

@section('title', 'Kampanye Keuangan - UNICODE')

@push('styles')
<style>
    @keyframes fadeSlideUp { from { opacity:0; transform: translateY(6px);} to { opacity:1; transform: translateY(0);} }
    @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
    @keyframes pulseRing { 0%{box-shadow:0 0 0 0 rgba(239,68,68,.5);} 70%{box-shadow:0 0 0 6px rgba(239,68,68,0);} 100%{box-shadow:0 0 0 0 rgba(239,68,68,0);} }

    .anim-in    { animation: fadeSlideUp .3s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1  { animation-delay: .04s; }
    .anim-in-2  { animation-delay: .08s; }
    .anim-in-3  { animation-delay: .12s; }
    .anim-in-4  { animation-delay: .16s; }

    .campaign-card { transition: transform .2s cubic-bezier(.22,.61,.36,1), box-shadow .2s ease, border-color .2s ease; }
    .campaign-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px -12px rgba(0,0,0,.14); border-color: rgba(147,51,234,.35); }
    .dark .campaign-card:hover { box-shadow: 0 16px 32px -12px rgba(0,0,0,.5); border-color: rgba(168,85,247,.4); }

    .focus-ring:focus-visible { outline: 2px solid rgb(147,51,234); outline-offset: 2px; border-radius: .5rem; }

    .progress-track { position: relative; height: 8px; border-radius: 9999px; overflow: hidden; background: rgb(229,231,235); }
    .dark .progress-track { background: rgb(55,65,81); }
    .progress-fill { height: 100%; border-radius: 9999px; transition: width .6s cubic-bezier(.22,.61,.36,1); position: relative; overflow: hidden; }
    .progress-fill::after { content:''; position: absolute; inset:0; background: linear-gradient(90deg, transparent 40%, rgba(255,255,255,.35) 50%, transparent 60%); background-size: 200% 100%; animation: shimmer 2.2s ease-in-out infinite; }

    .status-pill, .category-pill { display: inline-flex; align-items: center; gap: .375rem; padding: .25rem .625rem; border-radius: 9999px; font-size: .6875rem; font-weight: 600; letter-spacing: .01em; white-space: nowrap; line-height: 1; }
    .category-pill { border-radius: .375rem; font-weight: 500; letter-spacing: 0; }
    .category-pill i, .status-pill i { font-size: .625rem; }

    .urgent-dot { width: 8px; height: 8px; border-radius: 50%; background: rgb(239,68,68); animation: pulseRing 1.6s ease-out infinite; display: inline-block; }

    .tab-btn { padding: .5rem 1rem; border-radius: .5rem; font-size: .8125rem; font-weight: 500; color: rgb(107,114,128); transition: all .15s ease; white-space: nowrap; }
    .dark .tab-btn { color: rgb(156,163,175); }
    .tab-btn:hover { color: rgb(31,41,55); background: rgb(243,244,246); }
    .dark .tab-btn:hover { color: rgb(243,244,246); background: rgb(55,65,81); }
    .tab-btn[aria-selected="true"] { background: rgb(147,51,234); color: white; box-shadow: 0 2px 8px -2px rgba(147,51,234,.4); }
    .tab-btn[aria-selected="true"]:hover { background: rgb(126,34,206); }
</style>
@endpush

@section('content')
@php
    /* ─── Peta kategori — INLINE, tidak bergantung model ─── */
    $categoryMap = [
        'id_card'    => ['label' => 'ID Card',    'icon' => 'fa-id-card',            'class' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300'],
        'event'      => ['label' => 'Event',      'icon' => 'fa-calendar-check',     'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'reuni'      => ['label' => 'Reuni',      'icon' => 'fa-users',              'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
        'seragam'    => ['label' => 'Seragam',    'icon' => 'fa-shirt',              'class' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300'],
        'study_tour' => ['label' => 'Study Tour', 'icon' => 'fa-plane',              'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'],
        'donasi'     => ['label' => 'Donasi',     'icon' => 'fa-hand-holding-heart', 'class' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300'],
        'lainnya'    => ['label' => 'Lainnya',    'icon' => 'fa-circle-dot',         'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
    ];

    /* ─── Peta status — HANYA 4 nilai valid dari controller ─── */
    $statusMap = [
        'draft'     => ['label' => 'Draft',      'icon' => 'fa-pen',          'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
        'active'    => ['label' => 'Berjalan',   'icon' => 'fa-circle-play',  'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'closed'    => ['label' => 'Selesai',    'icon' => 'fa-circle-check', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'fa-circle-xmark', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
    ];

    $categoryOf = fn ($key) => $categoryMap[strtolower((string) $key)] ?? $categoryMap['lainnya'];
    $statusOf   = fn ($key) => $statusMap[strtolower((string) $key)]   ?? $statusMap['draft'];

    /* ─── Summary fallback ─── */
    $summary = $summary ?? [];
    $stat = [
        'total'        => (int)   ($summary['total']        ?? ($campaigns->total() ?? 0)),
        'active'       => (int)   ($summary['active']       ?? 0),
        'draft'        => (int)   ($summary['draft']        ?? 0),
        'closed'       => (int)   ($summary['closed']       ?? 0),
        'cancelled'    => (int)   ($summary['cancelled']    ?? 0),
        'collected'    => (float) ($summary['collected']    ?? 0),
        'target'       => (float) ($summary['target']       ?? 0),
        'participants' => (int)   ($summary['participants'] ?? 0),
        'overdue'      => (int)   ($summary['overdue']      ?? 0),
    ];
    $stat['progress_pct'] = $stat['target'] > 0
        ? min(100, round(($stat['collected'] / $stat['target']) * 100, 1))
        : 0;

    $activeTab = request('tab', 'all');
    $viewMode  = request('view') === 'list' ? 'list' : 'grid';

    $tabLink = function ($tab) {
        $q = request()->query();
        $q['tab'] = $tab;
        return route('owner.financial.campaigns.index', $q);
    };
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-[1600px] mx-auto">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}" class="hover:text-purple-600 dark:hover:text-purple-400 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-home text-[11px]" aria-hidden="true"></i><span>Keuangan</span>
        </a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Kampanye</span>
    </nav>

    @include('owner.financial._partials.submenu', ['active' => 'campaigns'])

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-6 mt-5 gap-4 anim-in">
        <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                <i class="fas fa-bullhorn text-white text-base" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">Kampanye Keuangan</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Penggalangan dana internal UKM — ID Card, Event, Reuni, dan lainnya.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.campaigns.create') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white text-sm font-semibold shadow-sm hover:shadow-md shadow-purple-500/20 transition-all">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i> Buat Kampanye
            </a>
            @if(($stat['total'] ?? 0) > 0)
                <a href="{{ route('owner.financial.reports.index') }}"
                   class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-gray-300 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                    <i class="fas fa-chart-line text-xs text-gray-500" aria-hidden="true"></i> Laporan
                </a>
            @endif
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div role="status" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 mb-4">
            <i class="fas fa-circle-check mt-0.5 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-sm">{{ session('success') }}</p>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 transition-colors">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div role="alert" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 mb-4">
            <i class="fas fa-circle-exclamation mt-0.5 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-sm">{{ session('error') }}</p>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 transition-colors">&times;</button>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Kampanye</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-bullhorn text-purple-600 dark:text-purple-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">{{ number_format($stat['total'], 0, ',', '.') }}</p>
            <div class="flex items-center gap-2 mt-1.5 text-[11px]">
                <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $stat['active'] }} aktif</span>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $stat['draft'] }} draft</span>
            </div>
        </div>

        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dana Terkumpul</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-sack-dollar text-emerald-600 dark:text-emerald-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight tabular-nums">
                Rp {{ number_format($stat['collected'], 0, ',', '.') }}
            </p>
            @if($stat['target'] > 0)
                <div class="mt-2 progress-track">
                    <div class="progress-fill bg-gradient-to-r from-emerald-400 to-emerald-500" style="width: {{ $stat['progress_pct'] }}%"></div>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5">
                    <strong class="text-emerald-600 dark:text-emerald-400">{{ $stat['progress_pct'] }}%</strong>
                    dari target Rp {{ number_format($stat['target'], 0, ',', '.') }}
                </p>
            @else
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5">belum ada target ditetapkan</p>
            @endif
        </div>

        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Partisipasi</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center">
                    <i class="fas fa-users text-sky-600 dark:text-sky-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600 dark:text-sky-400 tracking-tight tabular-nums">{{ number_format($stat['participants'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">anggota berpartisipasi</p>
        </div>

        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            @if($stat['overdue'] > 0)
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/20 rounded-full -mr-12 -mt-12"></div>
            @endif
            <div class="flex items-start justify-between mb-3 relative">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Perlu Perhatian</span>
                <div class="w-8 h-8 rounded-lg {{ $stat['overdue'] > 0 ? 'bg-rose-50 dark:bg-rose-900/30' : 'bg-slate-50 dark:bg-slate-700/50' }} flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation {{ $stat['overdue'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }} text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $stat['overdue'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }} tracking-tight tabular-nums relative">{{ $stat['overdue'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 relative">
                {{ $stat['overdue'] > 0 ? 'kampanye lewat jatuh tempo' : 'semua sesuai jadwal' }}
            </p>
        </div>
    </div>

    {{-- TAB + FILTER --}}
    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm mb-5 overflow-hidden">
        <div class="flex flex-wrap items-center gap-1.5 px-3 pt-3 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div role="tablist" aria-label="Filter status kampanye" class="flex flex-wrap items-center gap-1.5">
                <a role="tab" href="{{ $tabLink('all') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'all' ? 'true' : 'false' }}">
                    <i class="fas fa-layer-group text-[10px] mr-1" aria-hidden="true"></i>Semua
                    <span class="ml-1 opacity-70">{{ $stat['total'] }}</span>
                </a>
                <a role="tab" href="{{ $tabLink('active') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'active' ? 'true' : 'false' }}">
                    <i class="fas fa-circle-play text-[10px] mr-1" aria-hidden="true"></i>Berjalan
                    @if($stat['active'] > 0)<span class="ml-1 opacity-70">{{ $stat['active'] }}</span>@endif
                </a>
                <a role="tab" href="{{ $tabLink('draft') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'draft' ? 'true' : 'false' }}">
                    <i class="fas fa-pen text-[10px] mr-1" aria-hidden="true"></i>Draft
                    @if($stat['draft'] > 0)<span class="ml-1 opacity-70">{{ $stat['draft'] }}</span>@endif
                </a>
                <a role="tab" href="{{ $tabLink('closed') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'closed' ? 'true' : 'false' }}">
                    <i class="fas fa-circle-check text-[10px] mr-1" aria-hidden="true"></i>Selesai
                    @if($stat['closed'] > 0)<span class="ml-1 opacity-70">{{ $stat['closed'] }}</span>@endif
                </a>
                <a role="tab" href="{{ $tabLink('overdue') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'overdue' ? 'true' : 'false' }}">
                    <i class="fas fa-triangle-exclamation text-[10px] mr-1" aria-hidden="true"></i>Terlambat
                    @if($stat['overdue'] > 0)
                        <span class="ml-1 px-1.5 py-0.5 rounded-md bg-rose-500 text-white text-[10px] font-bold">{{ $stat['overdue'] }}</span>
                    @endif
                </a>
            </div>

            <div class="ml-auto flex items-center gap-1 p-0.5 rounded-lg bg-gray-100 dark:bg-gray-700/50">
                @php
                    $gridQuery = array_merge(request()->query(), ['view' => 'grid']);
                    $listQuery = array_merge(request()->query(), ['view' => 'list']);
                @endphp
                <a href="{{ route('owner.financial.campaigns.index', $gridQuery) }}"
                   class="focus-ring inline-flex items-center justify-center w-8 h-8 rounded-md transition-colors {{ $viewMode === 'grid' ? 'bg-white dark:bg-gray-800 shadow-sm text-purple-600 dark:text-purple-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                   title="Tampilan kartu" aria-label="Tampilan kartu">
                    <i class="fas fa-grip text-xs" aria-hidden="true"></i>
                </a>
                <a href="{{ route('owner.financial.campaigns.index', $listQuery) }}"
                   class="focus-ring inline-flex items-center justify-center w-8 h-8 rounded-md transition-colors {{ $viewMode === 'list' ? 'bg-white dark:bg-gray-800 shadow-sm text-purple-600 dark:text-purple-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                   title="Tampilan tabel" aria-label="Tampilan tabel">
                    <i class="fas fa-list text-xs" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('owner.financial.campaigns.index') }}" id="filterForm" class="p-3">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5">
                <div class="lg:col-span-4 relative">
                    <label for="q" class="sr-only">Cari kampanye</label>
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none" aria-hidden="true"></i>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Cari judul atau deskripsi…" autocomplete="off"
                           class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white placeholder:text-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 transition-all">
                </div>
                <div class="lg:col-span-3">
                    <label for="category" class="sr-only">Kategori</label>
                    <select id="category" name="category"
                            class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 transition-all">
                        <option value="">Semua kategori</option>
                        @foreach($categoryMap as $key => $meta)
                            <option value="{{ $key }}" @selected(request('category') === $key)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label for="sort" class="sr-only">Urutkan</label>
                    <select id="sort" name="sort"
                            class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 transition-all">
                        <option value="latest"   @selected(request('sort') === 'latest')>Terbaru</option>
                        <option value="deadline" @selected(request('sort') === 'deadline')>Jatuh tempo terdekat</option>
                        <option value="progress" @selected(request('sort') === 'progress')>Progres tertinggi</option>
                        <option value="amount"   @selected(request('sort') === 'amount')>Nominal terbesar</option>
                        <option value="title"    @selected(request('sort') === 'title')>Judul A-Z</option>
                    </select>
                </div>
                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit" class="focus-ring flex-1 inline-flex items-center justify-center gap-2 py-2.5 rounded-lg bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-semibold transition-colors">
                        <i class="fas fa-filter text-xs" aria-hidden="true"></i> Filter
                    </button>
                    @if(request()->hasAny(['q','category','sort']))
                        <a href="{{ route('owner.financial.campaigns.index', ['tab' => $activeTab, 'view' => $viewMode]) }}"
                           class="focus-ring inline-flex items-center justify-center px-3 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors"
                           title="Reset filter">
                            <i class="fas fa-xmark" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </div>

            @if(request()->hasAny(['q','category']))
                <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60">
                    <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Filter aktif:</span>
                    @if(request('q'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs">
                            <i class="fas fa-search text-[9px]" aria-hidden="true"></i>"{{ request('q') }}"
                        </span>
                    @endif
                    @if(request('category') && isset($categoryMap[request('category')]))
                        <span class="category-pill {{ $categoryMap[request('category')]['class'] }}">
                            <i class="fas {{ $categoryMap[request('category')]['icon'] }}" aria-hidden="true"></i>
                            {{ $categoryMap[request('category')]['label'] }}
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- CONTENT --}}
    @if($campaigns->isEmpty())
        <div class="anim-in anim-in-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-16 text-center shadow-sm">
            <div class="inline-flex flex-col items-center max-w-md">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-100 to-fuchsia-100 dark:from-purple-900/30 dark:to-fuchsia-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-bullhorn text-3xl text-purple-500 dark:text-purple-400" aria-hidden="true"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">
                    @if(request()->hasAny(['q','category']) || $activeTab !== 'all')
                        Tidak ada kampanye yang cocok
                    @else
                        Belum ada kampanye
                    @endif
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    @if(request()->hasAny(['q','category']) || $activeTab !== 'all')
                        Coba ubah filter atau lihat semua kampanye.
                    @else
                        Mulai dengan membuat kampanye pertama untuk menggalang dana internal UKM.
                    @endif
                </p>
                <div class="flex flex-wrap gap-2 justify-center">
                    @if(request()->hasAny(['q','category']) || $activeTab !== 'all')
                        <a href="{{ route('owner.financial.campaigns.index') }}"
                           class="focus-ring inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition-colors">
                            <i class="fas fa-rotate-left text-xs" aria-hidden="true"></i> Reset filter
                        </a>
                    @endif
                    <a href="{{ route('owner.financial.campaigns.create') }}"
                       class="focus-ring inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white text-sm font-semibold shadow-sm transition-all">
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i> Buat Kampanye
                    </a>
                </div>
            </div>
        </div>
    @elseif($viewMode === 'grid')
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($campaigns as $i => $campaign)
                @php
                    $catMeta   = $categoryOf($campaign->category ?? null);
                    $statMeta  = $statusOf($campaign->status ?? null);

                    $amount    = (float) ($campaign->amount ?? 0);
                    $target    = (float) ($campaign->target_amount    ?? $amount);
                    $collected = (float) ($campaign->collected_amount ?? 0);
                    $progress  = $target > 0 ? min(100, round(($collected / $target) * 100, 1)) : 0;

                    $participants = (int) ($campaign->participants_count ?? 0);
                    $expected     = (int) ($campaign->expected_participants ?? 0);
                    $partPct      = $expected > 0 ? min(100, round(($participants / $expected) * 100)) : 0;

                    $dueDate  = $campaign->due_date ? \Carbon\Carbon::parse($campaign->due_date) : null;
                    $isPast   = $dueDate && $dueDate->isPast();
                    $daysLeft = $dueDate ? now()->diffInDays($dueDate, false) : null;
                    $isUrgent = $dueDate && !$isPast && $daysLeft !== null && $daysLeft <= 7 && $campaign->status === 'active';

                    $isRequired = (bool) ($campaign->is_required ?? false);
                    $isArchived = in_array($campaign->status ?? '', ['closed', 'cancelled'], true);
                @endphp

                <article class="campaign-card anim-in bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col {{ $isArchived ? 'opacity-90' : '' }}"
                         style="animation-delay: {{ min($i * 0.03, 0.3) }}s">

                    <div class="relative h-20 bg-gradient-to-br {{ $campaign->status === 'active' ? 'from-purple-500 via-fuchsia-500 to-purple-600' : ($campaign->status === 'closed' ? 'from-slate-500 via-slate-600 to-slate-700' : ($campaign->status === 'cancelled' ? 'from-rose-500 via-rose-600 to-rose-700' : 'from-purple-400 to-purple-500')) }} overflow-hidden">
                        <div class="absolute inset-0 opacity-25" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.4) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,.3) 0, transparent 40%);"></div>
                        <div class="absolute inset-0 flex items-center justify-between px-4">
                            <span class="category-pill bg-white/25 backdrop-blur-sm text-white">
                                <i class="fas {{ $catMeta['icon'] }}" aria-hidden="true"></i>
                                {{ $catMeta['label'] }}
                            </span>
                            <span class="status-pill bg-white/25 backdrop-blur-sm text-white">
                                <i class="fas {{ $statMeta['icon'] }}" aria-hidden="true"></i>
                                {{ $statMeta['label'] }}
                            </span>
                        </div>
                        @if($isRequired)
                            <div class="absolute top-0 right-0 p-2 pointer-events-none">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-500 text-white shadow-sm">WAJIB</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug mb-1 line-clamp-2">
                            {{ $campaign->title ?? 'Tanpa judul' }}
                        </h3>

                        @if(!empty($campaign->description))
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                {{ Str::limit($campaign->description, 120) }}
                            </p>
                        @endif

                        @if($target > 0)
                            <div class="mb-4">
                                <div class="flex items-baseline justify-between mb-1.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Terkumpul</span>
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $progress }}%</span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill bg-gradient-to-r from-emerald-400 to-emerald-500" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="flex items-baseline justify-between mt-1.5 text-xs">
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">
                                        Rp {{ number_format($collected, 0, ',', '.') }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400 tabular-nums">
                                        / Rp {{ number_format($target, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 p-2.5">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-0.5">Nominal</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    Rp {{ number_format($amount, 0, ',', '.') }}
                                </p>
                                @if(!$isRequired)
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">opsional</p>
                                @endif
                            </div>

                            <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 p-2.5">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-0.5">Peserta</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    {{ number_format($participants, 0, ',', '.') }}
                                    @if($expected > 0)<span class="text-xs font-medium text-gray-500 dark:text-gray-400">/ {{ number_format($expected, 0, ',', '.') }}</span>@endif
                                </p>
                                @if($expected > 0)
                                    <div class="mt-1 h-1 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full rounded-full bg-sky-500" style="width: {{ $partPct }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs mb-4 mt-auto">
                            @if($dueDate)
                                @if($isPast && $campaign->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-semibold">
                                        <span class="urgent-dot"></span>
                                        Lewat {{ $dueDate->translatedFormat('d M Y') }}
                                    </span>
                                @elseif($isUrgent)
                                    <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-semibold">
                                        <i class="fas fa-clock text-[10px]" aria-hidden="true"></i>
                                        {{ $daysLeft }} hari lagi · {{ $dueDate->translatedFormat('d M Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-calendar text-[10px]" aria-hidden="true"></i>
                                        {{ $dueDate->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 dark:text-gray-500 inline-flex items-center gap-1.5">
                                    <i class="fas fa-calendar text-[10px]" aria-hidden="true"></i> Tanpa jatuh tempo
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5 pt-3 border-t border-gray-100 dark:border-gray-700/60">
                            <a href="{{ route('owner.financial.campaigns.show', $campaign) }}"
                               class="focus-ring flex-1 inline-flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-eye text-[10px]" aria-hidden="true"></i> Detail
                            </a>

                            @if($campaign->status === 'draft')
                                <form action="{{ route('owner.financial.campaigns.activate', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="focus-ring inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                            title="Aktifkan" aria-label="Aktifkan kampanye">
                                        <i class="fas fa-play text-[10px]" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('owner.financial.campaigns.edit', $campaign) }}"
                               class="focus-ring inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                               title="Edit" aria-label="Edit kampanye">
                                <i class="fas fa-pen text-[10px]" aria-hidden="true"></i>
                            </a>

                            <form action="{{ route('owner.financial.campaigns.destroy', $campaign) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Hapus kampanye ini? Tindakan tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="focus-ring inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors"
                                        title="Hapus" aria-label="Hapus kampanye">
                                    <i class="fas fa-trash text-[10px]" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="anim-in anim-in-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <caption class="sr-only">Daftar kampanye keuangan</caption>
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kampanye</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Kategori</th>
                            <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell w-48">Progres</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Jatuh Tempo</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($campaigns as $campaign)
                            @php
                                $catMeta   = $categoryOf($campaign->category ?? null);
                                $statMeta  = $statusOf($campaign->status ?? null);
                                $amount    = (float) ($campaign->amount ?? 0);
                                $target    = (float) ($campaign->target_amount    ?? $amount);
                                $collected = (float) ($campaign->collected_amount ?? 0);
                                $progress  = $target > 0 ? min(100, round(($collected / $target) * 100, 1)) : 0;
                                $dueDate   = $campaign->due_date ? \Carbon\Carbon::parse($campaign->due_date) : null;
                                $isPast    = $dueDate && $dueDate->isPast();
                                $isRequired = (bool) ($campaign->is_required ?? false);
                            @endphp
                            <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-fuchsia-600 flex items-center justify-center text-white shrink-0 shadow-sm">
                                            <i class="fas {{ $catMeta['icon'] }} text-[10px]" aria-hidden="true"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $campaign->title ?? 'Tanpa judul' }}
                                                @if($isRequired)
                                                    <span class="ml-1 text-[9px] font-bold px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 align-middle">WAJIB</span>
                                                @endif
                                            </p>
                                            @if(!empty($campaign->description))
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate max-w-md">
                                                    {{ Str::limit($campaign->description, 70) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                                    <span class="category-pill {{ $catMeta['class'] }}">
                                        <i class="fas {{ $catMeta['icon'] }}" aria-hidden="true"></i>
                                        {{ $catMeta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <span class="font-semibold text-gray-900 dark:text-white tabular-nums">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    @if($target > 0)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 progress-track">
                                                <div class="progress-fill bg-gradient-to-r from-emerald-400 to-emerald-500" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums w-10 text-right">{{ $progress }}%</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 tabular-nums">
                                            Rp {{ number_format($collected, 0, ',', '.') }} / Rp {{ number_format($target, 0, ',', '.') }}
                                        </p>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap hidden sm:table-cell">
                                    @if($dueDate)
                                        <div class="flex items-center gap-1.5 {{ $isPast && $campaign->status === 'active' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-600 dark:text-gray-400' }}">
                                            <i class="fas fa-calendar text-[10px]" aria-hidden="true"></i>
                                            <span class="text-xs font-medium tabular-nums">{{ $dueDate->translatedFormat('d M Y') }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="status-pill {{ $statMeta['class'] }}">
                                        <i class="fas {{ $statMeta['icon'] }}" aria-hidden="true"></i>
                                        {{ $statMeta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('owner.financial.campaigns.show', $campaign) }}"
                                           class="focus-ring inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                           title="Lihat detail">
                                            <i class="fas fa-eye text-[10px]" aria-hidden="true"></i>
                                            <span class="hidden sm:inline">Detail</span>
                                        </a>
                                        <a href="{{ route('owner.financial.campaigns.edit', $campaign) }}"
                                           class="focus-ring inline-flex items-center justify-center w-7 h-7 rounded-md text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                           title="Edit" aria-label="Edit">
                                            <i class="fas fa-pen text-[10px]" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('owner.financial.campaigns.destroy', $campaign) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus kampanye ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="focus-ring inline-flex items-center justify-center w-7 h-7 rounded-md text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors"
                                                    title="Hapus" aria-label="Hapus">
                                                <i class="fas fa-trash text-[10px]" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- PAGINATION --}}
    @if($campaigns->hasPages() || $campaigns->count() > 0)
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan <strong class="text-gray-700 dark:text-gray-300">{{ $campaigns->firstItem() ?? 0 }}–{{ $campaigns->lastItem() ?? 0 }}</strong>
                dari <strong class="text-gray-700 dark:text-gray-300">{{ number_format($campaigns->total(), 0, ',', '.') }}</strong> kampanye
            </p>
            @if($campaigns->hasPages())
                <div class="self-start sm:self-auto">{{ $campaigns->withQueryString()->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('filterForm');
        if (!form) return;

        form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => form.submit()));

        const q = document.getElementById('q');
        if (q) {
            let t;
            q.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(() => {
                    const v = q.value.trim();
                    if (v.length === 0 || v.length >= 3) form.submit();
                }, 550);
            });
        }
    })();

    document.addEventListener('keydown', e => {
        if (e.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) {
            e.preventDefault();
            document.getElementById('q')?.focus();
        }
    });
</script>
@endpush