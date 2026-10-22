@extends('layouts.roles.anggota-umum')

@section('title', 'Uangkas Saya – UNICODE')

@push('styles')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    @keyframes pulseDot {
        0%   { box-shadow: 0 0 0 0 rgba(239, 68, 68, .6); }
        70%  { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .anim-in { animation: fadeSlideUp .3s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay: .04s; }
    .anim-in-2 { animation-delay: .08s; }
    .anim-in-3 { animation-delay: .12s; }
    .anim-in-4 { animation-delay: .16s; }

    .row-hover { transition: background-color .15s ease; }
    .row-hover:hover { background-color: rgba(59, 130, 246, .04); }
    .dark .row-hover:hover { background-color: rgba(59, 130, 246, .08); }

    .focus-ring:focus-visible {
        outline: 2px solid rgb(59, 130, 246);
        outline-offset: 2px;
        border-radius: .5rem;
    }

    .status-pill {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .25rem .625rem; border-radius: 9999px;
        font-size: .6875rem; font-weight: 600; letter-spacing: .01em;
        white-space: nowrap; line-height: 1;
    }
    .status-pill i { font-size: .625rem; }

    .progress-track {
        position: relative; height: 8px; border-radius: 9999px;
        overflow: hidden; background: rgb(229, 231, 235);
    }
    .dark .progress-track { background: rgb(55, 65, 81); }
    .progress-fill {
        height: 100%; border-radius: 9999px;
        transition: width .6s cubic-bezier(.22,.61,.36,1);
        position: relative; overflow: hidden;
    }
    .progress-fill::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent 40%, rgba(255,255,255,.35) 50%, transparent 60%);
        background-size: 200% 100%;
        animation: shimmer 2.2s ease-in-out infinite;
    }

    .urgent-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: rgb(239, 68, 68);
        animation: pulseDot 1.6s ease-out infinite;
        display: inline-block;
    }

    .tab-btn {
        padding: .5rem 1rem; border-radius: .5rem;
        font-size: .8125rem; font-weight: 500;
        color: rgb(107, 114, 128);
        transition: all .15s ease;
        white-space: nowrap;
    }
    .dark .tab-btn { color: rgb(156, 163, 175); }
    .tab-btn:hover { color: rgb(31, 41, 55); background: rgb(243, 244, 246); }
    .dark .tab-btn:hover { color: rgb(243, 244, 246); background: rgb(55, 65, 81); }
    .tab-btn[aria-selected="true"] {
        background: rgb(37, 99, 235);
        color: white;
        box-shadow: 0 2px 8px -2px rgba(37, 99, 235, .4);
    }
    .tab-btn[aria-selected="true"]:hover { background: rgb(29, 78, 216); }
</style>
@endpush

@section('content')
@php
    /* ─── Ambil data dengan fallback aman ─── */
    $inv      = $summary['invoices']         ?? [];
    $pay      = $summary['payments']         ?? [];
    $recent   = $summary['recent_payments']  ?? collect();
    $overdue  = $summary['overdue_invoices'] ?? collect();
    $upcoming = $summary['upcoming_invoices']?? collect();
    $campaigns = $summary['campaigns']['items'] ?? collect();

    /* ─── Helper rupiah ─── */
    $fmtRp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

    /* ─── Peta status — INLINE, tidak bergantung model ─── */
    $statusMap = [
        'draft'     => ['label' => 'Draft',          'icon' => 'fa-pen',                  'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
        'pending'   => ['label' => 'Menunggu Bayar', 'icon' => 'fa-hourglass-half',       'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        'partial'   => ['label' => 'Bayar Sebagian', 'icon' => 'fa-circle-half-stroke',   'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'],
        'paid'      => ['label' => 'Lunas',          'icon' => 'fa-circle-check',         'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'settled'   => ['label' => 'Lunas',          'icon' => 'fa-circle-check',         'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'success'   => ['label' => 'Berhasil',       'icon' => 'fa-circle-check',         'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'overdue'   => ['label' => 'Terlambat',      'icon' => 'fa-triangle-exclamation', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'failed'    => ['label' => 'Gagal',          'icon' => 'fa-circle-xmark',         'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'expired'   => ['label' => 'Kedaluwarsa',    'icon' => 'fa-clock-rotate-left',    'class' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-400'],
        'refunded'  => ['label' => 'Refund',         'icon' => 'fa-rotate-left',          'class' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300'],
        'cancelled' => ['label' => 'Dibatalkan',     'icon' => 'fa-ban',                  'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500'],
    ];
    $metaOf = fn ($st) => $statusMap[strtolower((string) $st)] ?? $statusMap['pending'];

    /* ─── Hitung persentase pelunasan ─── */
    $totalAmount = (float) ($inv['total_amount'] ?? 0);
    $paidAmount  = (float) ($inv['paid_amount'] ?? 0);
    $pct = $totalAmount > 0 ? min(100, round(($paidAmount / $totalAmount) * 100, 1)) : 0;

    /* ─── Tab aktif ─── */
    $activeTab = request('tab', 'overview');
    $validTabs = ['overview', 'invoices', 'payments', 'campaigns'];
    if (!in_array($activeTab, $validTabs, true)) $activeTab = 'overview';

    $tabLink = function ($t) {
        $q = request()->query();
        $q['tab'] = $t;
        return route('anggota-umum.uangkas', $q);
    };
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-[1400px] mx-auto">

    {{-- ═════════════════════════════════════════════════════════════
     | BREADCRUMB
     ═════════════════════════════════════════════════════════════ --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('anggota-umum.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-home text-[11px]" aria-hidden="true"></i>
            <span>Dashboard</span>
        </a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Uangkas</span>
    </nav>

    {{-- ═════════════════════════════════════════════════════════════
     | HEADER
     ═════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-6 gap-4 anim-in">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
                <i class="fas fa-coins text-white text-base" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">
                    Uangkas Saya
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pantau tagihan, pembayaran, dan kampanye yang berlaku untuk Anda.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if(($inv['outstanding'] ?? 0) > 0)
                <a href="{{ route('anggota-umum.uangkas', ['tab' => 'invoices']) }}"
                   class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                    <i class="fas fa-money-bill-wave text-xs" aria-hidden="true"></i>
                    Bayar Tagihan
                    <span class="ml-1 px-1.5 py-0.5 rounded-md bg-white/25 text-[10px] font-bold">
                        {{ $inv['outstanding'] ? count($upcoming) + count($overdue) : 0 }}
                    </span>
                </a>
            @endif
            <a href="{{ route('profile.edit') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-gray-300 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                <i class="fas fa-user-cog text-xs text-gray-500" aria-hidden="true"></i>
                Profil
            </a>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
     | HERO — TUNGGAKAN / STATUS PELUNASAN
     ═════════════════════════════════════════════════════════════ --}}
    @php
        $heroBg = ($inv['overdue'] ?? 0) > 0
            ? 'from-rose-500 via-rose-600 to-pink-700'
            : (($inv['outstanding'] ?? 0) > 0
                ? 'from-amber-500 via-orange-500 to-rose-500'
                : 'from-emerald-500 via-teal-500 to-emerald-600');
        $heroIcon = ($inv['overdue'] ?? 0) > 0
            ? 'fa-triangle-exclamation'
            : (($inv['outstanding'] ?? 0) > 0 ? 'fa-hourglass-half' : 'fa-circle-check');
        $heroTitle = ($inv['overdue'] ?? 0) > 0
            ? 'Ada Tagihan Terlambat'
            : (($inv['outstanding'] ?? 0) > 0 ? 'Tagihan Belum Lunas' : 'Semua Tagihan Lunas');
        $heroDesc = ($inv['overdue'] ?? 0) > 0
            ? "Anda memiliki {$inv['overdue']} tagihan yang sudah lewat jatuh tempo. Segera selesaikan untuk menghindari sanksi."
            : (($inv['outstanding'] ?? 0) > 0
                ? 'Selesaikan pembayaran Anda sebelum jatuh tempo untuk menghindari denda.'
                : 'Terima kasih! Anda tidak memiliki tunggakan.');
    @endphp

    <div class="anim-in anim-in-1 relative overflow-hidden rounded-2xl shadow-xl bg-gradient-to-br {{ $heroBg }} p-6 md:p-8 mb-5 text-white">
        <div class="absolute -right-20 -top-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-5 items-center">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas {{ $heroIcon }} text-lg" aria-hidden="true"></i>
                    <p class="text-xs uppercase tracking-[0.15em] font-bold text-white/85">{{ $heroTitle }}</p>
                </div>
                <p class="text-4xl lg:text-5xl font-extrabold tabular-nums leading-none mb-3">
                    {{ $fmtRp($inv['outstanding'] ?? 0) }}
                </p>
                <p class="text-sm text-white/85 leading-relaxed">{{ $heroDesc }}</p>

                @if($totalAmount > 0)
                    <div class="mt-5 max-w-md">
                        <div class="flex items-baseline justify-between mb-1.5 text-xs">
                            <span class="opacity-90">Progres Pelunasan</span>
                            <span class="font-bold">{{ $pct }}%</span>
                        </div>
                        <div class="bg-white/25 rounded-full h-2 overflow-hidden">
                            <div class="bg-white rounded-full h-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1.5 text-[11px] opacity-90">
                            <span>Dibayar {{ $fmtRp($paidAmount) }}</span>
                            <span>Total {{ $fmtRp($totalAmount) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-1 gap-2">
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/75 font-bold">Total Tagihan</p>
                    <p class="text-lg font-bold tabular-nums mt-0.5">{{ $inv['total'] ?? 0 }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/75 font-bold">Dibayar Bulan Ini</p>
                    <p class="text-lg font-bold tabular-nums mt-0.5">{{ $fmtRp($pay['this_month'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
     | STAT CARDS
     ═════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @php
            $statCards = [
                [
                    'label' => 'Total Tagihan',
                    'value' => $inv['total'] ?? 0,
                    'sub'   => $fmtRp($inv['total_amount'] ?? 0),
                    'icon'  => 'fa-file-invoice',
                    'color' => 'blue',
                    'type'  => 'int',
                ],
                [
                    'label' => 'Lunas',
                    'value' => $inv['paid'] ?? 0,
                    'sub'   => $fmtRp($inv['paid_amount'] ?? 0),
                    'icon'  => 'fa-circle-check',
                    'color' => 'emerald',
                    'type'  => 'int',
                ],
                [
                    'label' => 'Menunggu',
                    'value' => $inv['pending'] ?? 0,
                    'sub'   => 'perlu dibayar',
                    'icon'  => 'fa-hourglass-half',
                    'color' => 'amber',
                    'type'  => 'int',
                ],
                [
                    'label' => 'Terlambat',
                    'value' => $inv['overdue'] ?? 0,
                    'sub'   => ($inv['overdue'] ?? 0) > 0 ? 'segera selesaikan' : 'semua sesuai jadwal',
                    'icon'  => 'fa-triangle-exclamation',
                    'color' => 'rose',
                    'type'  => 'int',
                ],
            ];
        @endphp

        @foreach($statCards as $i => $c)
            <div class="anim-in anim-in-{{ min($i + 1, 4) }} bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-bold">{{ $c['label'] }}</p>
                    <div class="w-8 h-8 rounded-lg bg-{{ $c['color'] }}-500/15 text-{{ $c['color'] }}-500 flex items-center justify-center">
                        <i class="fas {{ $c['icon'] }} text-[11px]" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums tracking-tight">{{ $c['value'] }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 truncate">{{ $c['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB NAVIGATION
     ═════════════════════════════════════════════════════════════ --}}
    <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm mb-5 overflow-hidden">
        <div class="flex flex-wrap items-center gap-1.5 px-3 pt-3 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div role="tablist" aria-label="Filter data keuangan" class="flex flex-wrap items-center gap-1.5">
                <a role="tab" href="{{ $tabLink('overview') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'overview' ? 'true' : 'false' }}">
                    <i class="fas fa-chart-pie text-[10px] mr-1" aria-hidden="true"></i>Ringkasan
                </a>
                <a role="tab" href="{{ $tabLink('invoices') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'invoices' ? 'true' : 'false' }}">
                    <i class="fas fa-file-invoice text-[10px] mr-1" aria-hidden="true"></i>Tagihan
                    @if(($inv['total'] ?? 0) > 0)
                        <span class="ml-1 opacity-70">{{ $inv['total'] }}</span>
                    @endif
                    @if(($inv['overdue'] ?? 0) > 0)
                        <span class="ml-1 px-1.5 py-0.5 rounded-md bg-rose-500 text-white text-[10px] font-bold">{{ $inv['overdue'] }}</span>
                    @endif
                </a>
                <a role="tab" href="{{ $tabLink('payments') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'payments' ? 'true' : 'false' }}">
                    <i class="fas fa-receipt text-[10px] mr-1" aria-hidden="true"></i>Riwayat Bayar
                    @if(($pay['total'] ?? 0) > 0)
                        <span class="ml-1 opacity-70">{{ $pay['total'] }}</span>
                    @endif
                </a>
                <a role="tab" href="{{ $tabLink('campaigns') }}" class="tab-btn focus-ring" aria-selected="{{ $activeTab === 'campaigns' ? 'true' : 'false' }}">
                    <i class="fas fa-bullhorn text-[10px] mr-1" aria-hidden="true"></i>Kampanye
                    @if($campaigns->count() > 0)
                        <span class="ml-1 opacity-70">{{ $campaigns->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: OVERVIEW
     ═════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'overview')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- KIRI: Tagihan terlambat & mendatang --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Tagihan Terlambat --}}
                @if($overdue->isNotEmpty())
                    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-rose-200 dark:border-rose-900/40 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-900/40 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2">
                                <span class="urgent-dot" aria-hidden="true"></span>
                                Tagihan Terlambat
                            </h3>
                            <span class="text-[11px] font-semibold text-rose-700 dark:text-rose-400">
                                {{ $overdue->count() }} tagihan
                            </span>
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach($overdue as $item)
                                @php
                                    $st = strtolower($item->status ?? 'overdue');
                                    $meta = $metaOf($st);
                                    $due = $item->due_date ?? null;
                                    $daysLate = $due ? now()->diffInDays(\Carbon\Carbon::parse($due)) : null;
                                @endphp
                                <li class="row-hover px-5 py-3.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $item->title ?? $item->type_label ?? ucfirst(str_replace('_', ' ', $item->type ?? 'Tagihan')) }}
                                        </p>
                                        @if($due)
                                            <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium mt-0.5">
                                                <i class="fas fa-clock text-[9px] mr-1" aria-hidden="true"></i>
                                                Jatuh tempo {{ \Carbon\Carbon::parse($due)->translatedFormat('d M Y') }}
                                                @if($daysLate && $daysLate > 0)
                                                    · {{ $daysLate }} hari lewat
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                            {{ $fmtRp($item->total ?? 0) }}
                                        </p>
                                        <span class="status-pill {{ $meta['class'] }} mt-1">
                                            <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                            {{ $meta['label'] }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Tagihan Mendatang --}}
                @if($upcoming->isNotEmpty())
                    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-900/40 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 flex items-center gap-2">
                                <i class="fas fa-calendar-day text-xs" aria-hidden="true"></i>
                                Tagihan Mendatang
                            </h3>
                            <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-400">
                                {{ $upcoming->count() }} tagihan
                            </span>
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach($upcoming as $item)
                                @php
                                    $st = strtolower($item->status ?? 'pending');
                                    $meta = $metaOf($st);
                                    $due = $item->due_date ?? null;
                                    $daysLeft = $due ? now()->diffInDays(\Carbon\Carbon::parse($due), false) : null;
                                    $isUrgent = $daysLeft !== null && $daysLeft <= 3 && $daysLeft >= 0;
                                @endphp
                                <li class="row-hover px-5 py-3.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $item->title ?? $item->type_label ?? ucfirst(str_replace('_', ' ', $item->type ?? 'Tagihan')) }}
                                        </p>
                                        @if($due)
                                            <p class="text-[11px] {{ $isUrgent ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-gray-500 dark:text-gray-400' }} mt-0.5">
                                                <i class="fas fa-clock text-[9px] mr-1" aria-hidden="true"></i>
                                                @if($isUrgent)
                                                    {{ $daysLeft }} hari lagi ·
                                                @endif
                                                Jatuh tempo {{ \Carbon\Carbon::parse($due)->translatedFormat('d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                            {{ $fmtRp($item->total ?? 0) }}
                                        </p>
                                        <span class="status-pill {{ $meta['class'] }} mt-1">
                                            <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                            {{ $meta['label'] }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Empty state --}}
                @if($overdue->isEmpty() && $upcoming->isEmpty())
                    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-12 text-center shadow-sm">
                        <div class="inline-flex flex-col items-center max-w-md">
                            <div class="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center mb-4">
                                <i class="fas fa-circle-check text-3xl text-emerald-500" aria-hidden="true"></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">Semua tagihan lunas</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada tagihan aktif saat ini. Terima kasih telah membayar tepat waktu!
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- KANAN: Sidebar --}}
            <div class="space-y-5">

                {{-- Kartu pembayaran terakhir --}}
                <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                        Pembayaran Terakhir
                    </h3>
                    @if($recent->isNotEmpty())
                        @php
                            $last = $recent->first();
                            $lastSt = strtolower($last->status ?? 'pending');
                            $lastMeta = $metaOf($lastSt);
                        @endphp
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shrink-0">
                                <i class="fas fa-check text-sm" aria-hidden="true"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ strtoupper($last->method ?? 'CASH') }}
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($last->created_at)->translatedFormat('d M Y, H:i') }}
                                </p>
                                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums mt-1">
                                    {{ $fmtRp($last->amount ?? 0) }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                            Belum ada pembayaran.
                        </p>
                    @endif
                </div>

                {{-- Panduan pembayaran --}}
                <div class="anim-in anim-in-4 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/10 p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-500/15 text-blue-500 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-bold text-blue-900 dark:text-blue-200 text-sm mb-1">Cara Membayar</p>
                            <ol class="text-xs text-blue-800/85 dark:text-blue-300/85 space-y-1 list-decimal list-inside">
                                <li>Buka tab <strong>Tagihan</strong></li>
                                <li>Pilih tagihan yang ingin dibayar</li>
                                <li>Pilih metode pembayaran (QRIS / Tunai)</li>
                                <li>Pembayaran tunai dikonfirmasi bendahara</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Kontak bendahara --}}
                <div class="anim-in anim-in-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-headset" aria-hidden="true"></i>
                        Butuh Bantuan?
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-3">
                        Hubungi bendahara UKM untuk pertanyaan seputar tagihan dan pembayaran.
                    </p>
                    <a href="{{ route('kontak') }}"
                       class="focus-ring w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors">
                        <i class="fas fa-comments text-[10px]" aria-hidden="true"></i>
                        Hubungi Bendahara
                    </a>
                </div>
            </div>
        </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: INVOICES
     ═════════════════════════════════════════════════════════════ --}}
    @elseif($activeTab === 'invoices')

        <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-file-invoice text-blue-500" aria-hidden="true"></i>
                    Daftar Tagihan Saya
                </h3>
                <span class="text-[11px] text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/60 px-2 py-0.5 rounded-md">
                    {{ $inv['total'] ?? 0 }} tagihan
                </span>
            </div>

            @if(($inv['total'] ?? 0) === 0)
                <div class="p-12 text-center">
                    <i class="fas fa-file-invoice text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada tagihan untuk Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/80 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Jatuh Tempo</th>
                                <th scope="col" class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @php
                                $allInvoices = $overdue->merge($upcoming);
                            @endphp
                            @forelse($allInvoices as $invItem)
                                @php
                                    $st = strtolower($invItem->status ?? 'pending');
                                    $meta = $metaOf($st);
                                    $due = $invItem->due_date ?? null;
                                    $isPast = $due && \Carbon\Carbon::parse($due)->isPast() && !in_array($st, ['paid','settled','success','cancelled'], true);
                                @endphp
                                <tr class="row-hover">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $invItem->title ?? $invItem->type_label ?? ucfirst(str_replace('_', ' ', $invItem->type ?? 'Tagihan')) }}
                                        </p>
                                        @if(!empty($invItem->period))
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Periode {{ $invItem->period }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap hidden sm:table-cell">
                                        @if($due)
                                            <span class="{{ $isPast ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-gray-600 dark:text-gray-400' }} text-xs">
                                                {{ \Carbon\Carbon::parse($due)->translatedFormat('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right">
                                        <span class="font-bold text-gray-900 dark:text-white tabular-nums">
                                            {{ $fmtRp($invItem->total ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="status-pill {{ $meta['class'] }}">
                                            <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                            {{ $meta['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada tagihan aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: PAYMENTS
     ═════════════════════════════════════════════════════════════ --}}
    @elseif($activeTab === 'payments')

        <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-receipt text-emerald-500" aria-hidden="true"></i>
                    Riwayat Pembayaran
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-md font-semibold">
                        Total: {{ $fmtRp($pay['total_paid'] ?? 0) }}
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/60 px-2 py-0.5 rounded-md">
                        {{ $pay['total'] ?? 0 }} transaksi
                    </span>
                </div>
            </div>

            @if($recent->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat pembayaran.</p>
                    <p class="text-xs text-gray-400 mt-1">Pembayaran akan muncul di sini setelah dikonfirmasi.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($recent as $p)
                        @php
                            $st = strtolower($p->status ?? 'pending');
                            $meta = $metaOf($st);
                            $method = strtoupper($p->method ?? 'CASH');
                            $methodIcon = match(strtolower($p->method ?? '')) {
                                'qris'          => 'fa-qrcode',
                                'cash'          => 'fa-money-bill-wave',
                                'bank_transfer' => 'fa-building-columns',
                                'ewallet'       => 'fa-wallet',
                                default         => 'fa-credit-card',
                            };
                        @endphp
                        <li class="row-hover px-5 py-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fas {{ $methodIcon }} text-sm" aria-hidden="true"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $method }}
                                    @if(!empty($p->reference))
                                        <span class="text-xs text-gray-400 font-mono ml-1">· {{ Str::limit($p->reference, 18) }}</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    <i class="fas fa-calendar text-[9px] mr-1" aria-hidden="true"></i>
                                    {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    {{ $fmtRp($p->amount ?? 0) }}
                                </p>
                                <span class="status-pill {{ $meta['class'] }} mt-1">
                                    <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                    {{ $meta['label'] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: CAMPAIGNS
     ═════════════════════════════════════════════════════════════ --}}
    @elseif($activeTab === 'campaigns')

        @if($campaigns->isEmpty())
            <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-12 text-center shadow-sm">
                <i class="fas fa-bullhorn text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Belum ada kampanye aktif</p>
                <p class="text-xs text-gray-400 mt-1">Kampanye dari bendahara akan muncul di sini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($campaigns as $i => $c)
                    @php
                        $target    = (float) ($c->target_amount    ?? $c->amount ?? 0);
                        $collected = (float) ($c->collected_amount ?? 0);
                        $progress  = $target > 0 ? min(100, round(($collected / $target) * 100, 1)) : 0;
                        $isRequired = (bool) ($c->is_required ?? false);
                        $due = $c->due_date ?? null;
                        $daysLeft = $due ? now()->diffInDays(\Carbon\Carbon::parse($due), false) : null;
                        $isUrgent = $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7;
                    @endphp
                    <div class="anim-in bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                         style="animation-delay: {{ min($i * 0.05, 0.3) }}s">

                        {{-- Header --}}
                        <div class="relative h-20 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 overflow-hidden">
                            <div class="absolute inset-0 opacity-25" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.4) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,.3) 0, transparent 40%);"></div>
                            <div class="absolute inset-0 flex items-center justify-between px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/25 backdrop-blur-sm text-white text-[11px] font-semibold">
                                    <i class="fas fa-bullhorn text-[9px]" aria-hidden="true"></i>
                                    {{ ucfirst($c->category ?? 'kampanye') }}
                                </span>
                                @if($isRequired)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-500 text-white shadow-sm">
                                        WAJIB
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-4">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug mb-1 line-clamp-2">
                                {{ $c->title ?? 'Tanpa judul' }}
                            </h3>

                            @if(!empty($c->description))
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ Str::limit($c->description, 100) }}
                                </p>
                            @endif

                            @if($target > 0)
                                <div class="mb-3">
                                    <div class="flex items-baseline justify-between mb-1.5">
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Terkumpul</span>
                                        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $progress }}%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill bg-gradient-to-r from-emerald-400 to-emerald-500" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="flex items-baseline justify-between mt-1.5 text-[11px]">
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">
                                            {{ $fmtRp($collected) }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400 tabular-nums">
                                            / {{ $fmtRp($target) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700/60">
                                <div class="text-xs">
                                    @if($due)
                                        @if($isUrgent)
                                            <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold">
                                                <i class="fas fa-clock text-[10px]" aria-hidden="true"></i>
                                                {{ $daysLeft }} hari lagi
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 inline-flex items-center gap-1">
                                                <i class="fas fa-calendar text-[10px]" aria-hidden="true"></i>
                                                {{ \Carbon\Carbon::parse($due)->translatedFormat('d M Y') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Tanpa jatuh tempo</span>
                                    @endif
                                </div>
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400 tabular-nums">
                                    {{ $fmtRp($c->amount ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @endif

    {{-- ═════════════════════════════════════════════════════════════
     | FOOTER INFO
     ═════════════════════════════════════════════════════════════ --}}
    <div class="mt-6 text-center text-[11px] text-gray-400 dark:text-gray-500">
        <i class="fas fa-shield-check mr-1" aria-hidden="true"></i>
        Data keuangan Anda tersimpan dengan aman ·
        Terakhir diperbarui {{ now()->translatedFormat('d M Y, H:i') }} WIB
    </div>
</div>
@endsection