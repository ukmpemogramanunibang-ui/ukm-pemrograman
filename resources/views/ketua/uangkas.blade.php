@extends('layouts.roles.ketua')

@section('title', 'Uangkas – UNICODE')
@section('page-title', 'Uangkas')

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
    .row-hover:hover { background-color: rgba(147, 51, 234, .04); }
    .dark .row-hover:hover { background-color: rgba(147, 51, 234, .08); }

    .focus-ring:focus-visible {
        outline: 2px solid rgb(147, 51, 234);
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

    .urgent-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: rgb(239, 68, 68);
        animation: pulseDot 1.6s ease-out infinite;
        display: inline-block;
    }

    .progress-track {
        position: relative; height: 8px; border-radius: 9999px;
        overflow: hidden; background: rgba(255,255,255,.2);
    }
    .progress-fill {
        height: 100%; border-radius: 9999px;
        transition: width .6s cubic-bezier(.22,.61,.36,1);
        position: relative; overflow: hidden;
    }
    .progress-fill::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent 40%, rgba(255,255,255,.4) 50%, transparent 60%);
        background-size: 200% 100%;
        animation: shimmer 2.2s ease-in-out infinite;
    }

    .tab-btn {
        padding: .5rem 1rem; border-radius: .5rem;
        font-size: .8125rem; font-weight: 500;
        color: rgb(107, 114, 128);
        transition: all .15s ease;
        white-space: nowrap;
        text-decoration: none;
    }
    .dark .tab-btn { color: rgb(156, 163, 175); }
    .tab-btn:hover { color: rgb(31, 41, 55); background: rgb(243, 244, 246); }
    .dark .tab-btn:hover { color: rgb(243, 244, 246); background: rgb(55, 65, 81); }
    .tab-btn[aria-selected="true"] {
        background: rgb(147, 51, 234);
        color: white;
        box-shadow: 0 2px 8px -2px rgba(147, 51, 234, .4);
    }
    .tab-btn[aria-selected="true"]:hover { background: rgb(126, 34, 206); }
</style>
@endpush

@section('content')
@php
    /* ─── Ambil dari service (fallback aman) ─── */
    $overview        = $global['overview']          ?? [];
    $recentPayments  = $global['recent_payments']   ?? collect();
    $topDebtors      = $global['top_debtors']       ?? collect();
    $activeCampaigns = $global['active_campaigns']  ?? collect();

    $myInv = $summary['invoices']         ?? [];
    $myPay = $summary['payments']         ?? [];
    $myRecent = $summary['recent_payments'] ?? collect();
    $myOverdue = $summary['overdue_invoices'] ?? collect();
    $myUpcoming = $summary['upcoming_invoices'] ?? collect();

    /* ─── Helper rupiah ─── */
    $fmtRp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

    /* ─── Peta status — INLINE ─── */
    $statusMap = [
        'draft'     => ['label' => 'Draft',          'icon' => 'fa-pen',                  'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
        'pending'   => ['label' => 'Menunggu',       'icon' => 'fa-hourglass-half',       'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        'partial'   => ['label' => 'Sebagian',       'icon' => 'fa-circle-half-stroke',   'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'],
        'paid'      => ['label' => 'Berhasil',       'icon' => 'fa-circle-check',         'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'settled'   => ['label' => 'Selesai',        'icon' => 'fa-circle-check',         'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'success'   => ['label' => 'Berhasil',       'icon' => 'fa-circle-check',         'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'overdue'   => ['label' => 'Terlambat',      'icon' => 'fa-triangle-exclamation', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'failed'    => ['label' => 'Gagal',          'icon' => 'fa-circle-xmark',         'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'expired'   => ['label' => 'Kedaluwarsa',    'icon' => 'fa-clock-rotate-left',    'class' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-400'],
        'refunded'  => ['label' => 'Refund',         'icon' => 'fa-rotate-left',          'class' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300'],
        'cancelled' => ['label' => 'Dibatalkan',     'icon' => 'fa-ban',                  'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500'],
    ];
    $metaOf = fn ($st) => $statusMap[strtolower((string) $st)] ?? $statusMap['pending'];

    /* ─── Tab aktif ─── */
    $activeTab = $tab ?? request('tab', 'overview');
    $validTabs = ['overview', 'payments', 'debtors', 'campaigns', 'my'];
    if (!in_array($activeTab, $validTabs, true)) $activeTab = 'overview';

    $tabLink = function ($t) {
        $q = request()->query();
        $q['tab'] = $t;
        return route('ketua.keuangan', $q);
    };

    /* ─── Hitung persentase pelunasan global ─── */
    $gTotalPaid = (float) ($overview['total_paid'] ?? 0);
    $gTotalOutstanding = (float) ($overview['total_outstanding'] ?? 0);
    $gTotalAll = $gTotalPaid + $gTotalOutstanding;
    $gPct = $gTotalAll > 0 ? min(100, round(($gTotalPaid / $gTotalAll) * 100, 1)) : 0;
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-[1400px] mx-auto space-y-5">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 anim-in">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg shadow-purple-500/25">
                <i class="fas fa-coins text-white text-base" aria-hidden="true"></i>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold">Financial Overview</p>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">
                    Uangkas Ketua
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan keuangan kas UKM UNICODE
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.dashboard') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                <i class="fas fa-chart-pie text-xs" aria-hidden="true"></i>
                Dashboard Keuangan
            </a>
            <a href="{{ route('owner.financial.campaigns.index') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-purple-300 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                <i class="fas fa-bullhorn text-xs text-purple-500" aria-hidden="true"></i>
                Kampanye
            </a>
        </div>
    </div>

    {{-- ═══════════════ HERO — TOTAL KAS GLOBAL ═══════════════ --}}
    <div class="anim-in anim-in-1 relative overflow-hidden rounded-2xl shadow-xl bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 p-6 md:p-8 text-white">
        <div class="absolute -right-20 -top-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-5 items-center">
            <div class="lg:col-span-2">
                <p class="text-[11px] uppercase tracking-[0.2em] text-white/75 font-bold">Total Saldo Kas UKM</p>
                <p class="text-4xl lg:text-5xl font-extrabold mt-2 tabular-nums leading-none">
                    {{ $fmtRp($gTotalPaid) }}
                </p>
                <p class="text-xs text-white/75 mt-3">
                    <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                    Akumulasi seluruh pembayaran terverifikasi ·
                    Diperbarui {{ now()->translatedFormat('d M Y, H:i') }} WIB
                </p>

                @if($gTotalAll > 0)
                    <div class="mt-5 max-w-md">
                        <div class="flex items-baseline justify-between mb-1.5 text-xs">
                            <span class="text-white/85">Tingkat Pelunasan Global</span>
                            <span class="font-bold">{{ $gPct }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill bg-white" style="width: {{ $gPct }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-1 gap-2">
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/75 font-bold">Bulan Ini</p>
                    <p class="text-lg font-bold tabular-nums mt-0.5">{{ $fmtRp($overview['paid_this_month'] ?? 0) }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/75 font-bold">Tunggakan</p>
                    <p class="text-lg font-bold tabular-nums mt-0.5">{{ $fmtRp($gTotalOutstanding) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ STAT CARDS ═══════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $stats = [
                ['label' => 'Total Tagihan',   'value' => $overview['total_invoices'] ?? 0,  'sub' => 'invoice terdaftar',    'icon' => 'fa-file-invoice',          'color' => 'blue'],
                ['label' => 'Terlambat',       'value' => $overview['overdue_count'] ?? 0,   'sub' => 'perlu tindak lanjut',  'icon' => 'fa-triangle-exclamation',  'color' => 'rose'],
                ['label' => 'Kampanye Aktif',  'value' => $overview['active_campaigns'] ?? 0,'sub' => 'sedang berjalan',      'icon' => 'fa-bullhorn',              'color' => 'purple'],
                ['label' => 'Pembayaran Bulan Ini','value' => $fmtRp($overview['paid_this_month'] ?? 0),'sub' => 'terverifikasi',   'icon' => 'fa-coins',                 'color' => 'emerald'],
            ];
        @endphp
        @foreach($stats as $i => $c)
            <div class="anim-in anim-in-{{ min($i + 1, 4) }} bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-bold">{{ $c['label'] }}</p>
                    <div class="w-8 h-8 rounded-lg bg-{{ $c['color'] }}-500/15 text-{{ $c['color'] }}-500 flex items-center justify-center">
                        <i class="fas {{ $c['icon'] }} text-[11px]" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-{{ is_string($c['value']) ? 'base' : '2xl' }} font-bold text-gray-900 dark:text-white tabular-nums tracking-tight leading-tight">
                    {{ $c['value'] }}
                </p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">{{ $c['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ═══════════════ TABS ═══════════════ --}}
    <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center gap-1.5 px-3 pt-3 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div role="tablist" aria-label="Tab keuangan" class="flex flex-wrap items-center gap-1.5">
                <a role="tab" href="{{ $tabLink('overview') }}" class="tab-btn" aria-selected="{{ $activeTab === 'overview' ? 'true' : 'false' }}">
                    <i class="fas fa-chart-pie text-[10px] mr-1" aria-hidden="true"></i>Ringkasan
                </a>
                <a role="tab" href="{{ $tabLink('payments') }}" class="tab-btn" aria-selected="{{ $activeTab === 'payments' ? 'true' : 'false' }}">
                    <i class="fas fa-receipt text-[10px] mr-1" aria-hidden="true"></i>Pembayaran
                    @if($recentPayments->count() > 0)<span class="ml-1 opacity-70">{{ $recentPayments->count() }}</span>@endif
                </a>
                <a role="tab" href="{{ $tabLink('debtors') }}" class="tab-btn" aria-selected="{{ $activeTab === 'debtors' ? 'true' : 'false' }}">
                    <i class="fas fa-user-clock text-[10px] mr-1" aria-hidden="true"></i>Penunggak
                    @if($topDebtors->count() > 0)<span class="ml-1 opacity-70">{{ $topDebtors->count() }}</span>@endif
                </a>
                <a role="tab" href="{{ $tabLink('campaigns') }}" class="tab-btn" aria-selected="{{ $activeTab === 'campaigns' ? 'true' : 'false' }}">
                    <i class="fas fa-bullhorn text-[10px] mr-1" aria-hidden="true"></i>Kampanye
                    @if($activeCampaigns->count() > 0)<span class="ml-1 opacity-70">{{ $activeCampaigns->count() }}</span>@endif
                </a>
                <a role="tab" href="{{ $tabLink('my') }}" class="tab-btn" aria-selected="{{ $activeTab === 'my' ? 'true' : 'false' }}">
                    <i class="fas fa-user-circle text-[10px] mr-1" aria-hidden="true"></i>Tagihan Saya
                    @if(($myInv['overdue'] ?? 0) > 0)
                        <span class="ml-1 px-1.5 py-0.5 rounded-md bg-rose-500 text-white text-[10px] font-bold">{{ $myInv['overdue'] }}</span>
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

            {{-- KIRI: Riwayat pembayaran terbaru --}}
            <div class="lg:col-span-2 anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-receipt text-emerald-500" aria-hidden="true"></i>
                        Pembayaran Terbaru Anggota
                    </h3>
                    <a href="{{ $tabLink('payments') }}" class="text-xs font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                        Lihat semua →
                    </a>
                </div>

                @if($recentPayments->isEmpty())
                    <div class="p-12 text-center">
                        <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran tercatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($recentPayments as $pay)
                            @php
                                $st = strtolower($pay->status ?? 'pending');
                                $meta = $metaOf($st);
                                $method = strtoupper($pay->method ?? 'CASH');
                            @endphp
                            <li class="row-hover px-5 py-3.5 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($pay->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $pay->user->name ?? 'Anggota' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $method }}
                                        · {{ \Carbon\Carbon::parse($pay->created_at)->translatedFormat('d M Y, H:i') }}
                                        @if(!empty($pay->reference))
                                            · <span class="font-mono">{{ Str::limit($pay->reference, 12) }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                        {{ $fmtRp($pay->amount ?? 0) }}
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

            {{-- KANAN: Ringkasan tagihan saya --}}
            <div class="space-y-5">
                <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-user-circle text-purple-500" aria-hidden="true"></i>
                        Tagihan Saya
                    </h3>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Total</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white">{{ $myInv['total'] ?? 0 }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Belum Lunas</dt>
                            <dd class="font-semibold text-orange-600 dark:text-orange-400 tabular-nums">
                                {{ $fmtRp($myInv['outstanding'] ?? 0) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Sudah Dibayar</dt>
                            <dd class="font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">
                                {{ $fmtRp($myPay['total_paid'] ?? 0) }}
                            </dd>
                        </div>
                    </dl>
                    <a href="{{ $tabLink('my') }}"
                       class="focus-ring mt-4 w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold transition">
                        <i class="fas fa-eye text-[10px]" aria-hidden="true"></i>
                        Lihat Detail
                    </a>
                </div>

                {{-- Info modul lengkap --}}
                <div class="anim-in anim-in-4 rounded-xl border border-purple-200 dark:border-purple-900 bg-purple-50 dark:bg-purple-900/10 p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-500/15 text-purple-500 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-check text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-bold text-purple-900 dark:text-purple-200 text-sm mb-1">Modul Lengkap</p>
                            <p class="text-xs text-purple-800/85 dark:text-purple-300/85 mb-3">
                                Kelola tagihan, pembayaran, dan skema biaya lengkap.
                            </p>
                            <a href="{{ route('owner.financial.invoices.index') }}"
                               class="focus-ring inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold transition">
                                <i class="fas fa-file-invoice text-[10px]" aria-hidden="true"></i>
                                Buka Tagihan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: PAYMENTS (riwayat pembayaran semua anggota)
     ═════════════════════════════════════════════════════════════ --}}
    @elseif($activeTab === 'payments')
        <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-receipt text-emerald-500" aria-hidden="true"></i>
                    Riwayat Pembayaran Anggota
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-md font-semibold">
                        Total: {{ $fmtRp($overview['total_paid'] ?? 0) }}
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/60 px-2 py-0.5 rounded-md">
                        {{ $recentPayments->count() }} terbaru
                    </span>
                </div>
            </div>

            @if($recentPayments->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/80 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Metode</th>
                                <th scope="col" class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach($recentPayments as $pay)
                                @php
                                    $st = strtolower($pay->status ?? 'pending');
                                    $meta = $metaOf($st);
                                @endphp
                                <tr class="row-hover">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center text-[11px] font-bold shrink-0">
                                                {{ strtoupper(substr($pay->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $pay->user->name ?? 'Anggota' }}
                                                </p>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">
                                                    {{ $pay->user->nim ?? '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap hidden sm:table-cell">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-[11px] font-medium">
                                            {{ strtoupper($pay->method ?? 'CASH') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right">
                                        <span class="font-bold text-gray-900 dark:text-white tabular-nums">
                                            {{ $fmtRp($pay->amount ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="status-pill {{ $meta['class'] }}">
                                            <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                            {{ $meta['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400 text-xs hidden md:table-cell">
                                        {{ \Carbon\Carbon::parse($pay->created_at)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: DEBTORS
     ═════════════════════════════════════════════════════════════ --}}
    @elseif($activeTab === 'debtors')
        <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-clock text-rose-500" aria-hidden="true"></i>
                    Top Penunggak
                </h3>
                <span class="text-[11px] text-gray-500 dark:text-gray-400">
                    {{ $topDebtors->count() }} anggota
                </span>
            </div>

            @if($topDebtors->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center mb-3">
                            <i class="fas fa-circle-check text-3xl text-emerald-500" aria-hidden="true"></i>
                        </div>
                        <p class="text-base font-semibold text-gray-800 dark:text-white">Semua anggota lunas!</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tidak ada tunggakan saat ini.</p>
                    </div>
                </div>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($topDebtors as $i => $d)
                        <li class="row-hover px-5 py-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold
                                {{ $i === 0 ? 'bg-gradient-to-br from-rose-500 to-red-600 text-white' :
                                   ($i === 1 ? 'bg-gradient-to-br from-orange-400 to-amber-500 text-white' :
                                   ($i === 2 ? 'bg-gradient-to-br from-amber-400 to-yellow-500 text-white' :
                                   'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300')) }}">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $d->user->name ?? 'Anggota #' . $d->user_id }}
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $d->user->nim ?? '—' }} · {{ $d->count ?? 0 }} tagihan
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-rose-600 dark:text-rose-400 tabular-nums">
                                    {{ $fmtRp($d->total_debt ?? 0) }}
                                </p>
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
        @if($activeCampaigns->isEmpty())
            <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-12 text-center shadow-sm">
                <i class="fas fa-bullhorn text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Belum ada kampanye aktif</p>
                <a href="{{ route('owner.financial.campaigns.create') }}"
                   class="focus-ring inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white text-sm font-semibold shadow-sm transition">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                    Buat Kampanye
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeCampaigns as $i => $c)
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

                        <div class="relative h-20 bg-gradient-to-br from-purple-500 via-fuchsia-500 to-pink-600 overflow-hidden">
                            <div class="absolute inset-0 opacity-25" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.4) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,.3) 0, transparent 40%);"></div>
                            <div class="absolute inset-0 flex items-center justify-between px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/25 backdrop-blur-sm text-white text-[11px] font-semibold">
                                    <i class="fas fa-bullhorn text-[9px]" aria-hidden="true"></i>
                                    {{ ucfirst($c->category ?? 'kampanye') }}
                                </span>
                                @if($isRequired)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-500 text-white shadow-sm">WAJIB</span>
                                @endif
                            </div>
                        </div>

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
                                    <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 transition-all duration-700" style="width: {{ $progress }}%"></div>
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
                                <span class="text-sm font-bold text-purple-600 dark:text-purple-400 tabular-nums">
                                    {{ $fmtRp($c->amount ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    {{-- ═════════════════════════════════════════════════════════════
     | TAB: MY (tagihan pribadi ketua)
     ═════════════════════════════════════════════════════════════ --}}
    @elseif($activeTab === 'my')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Tagihan belum lunas --}}
            <div class="lg:col-span-2 space-y-5">
                @if($myOverdue->isNotEmpty())
                    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-rose-200 dark:border-rose-900/40 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-900/40">
                            <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2">
                                <span class="urgent-dot" aria-hidden="true"></span>
                                Tagihan Saya yang Terlambat
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach($myOverdue as $item)
                                @php
                                    $st = strtolower($item->status ?? 'overdue');
                                    $meta = $metaOf($st);
                                @endphp
                                <li class="px-5 py-3.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $item->title ?? $item->type_label ?? ucfirst(str_replace('_', ' ', $item->type ?? 'Tagihan')) }}
                                        </p>
                                        @if(!empty($item->due_date))
                                            <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium mt-0.5">
                                                Jatuh tempo {{ \Carbon\Carbon::parse($item->due_date)->translatedFormat('d M Y') }}
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

                @if($myUpcoming->isNotEmpty())
                    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-900/40">
                            <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 flex items-center gap-2">
                                <i class="fas fa-calendar-day text-xs" aria-hidden="true"></i>
                                Tagihan Mendatang
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach($myUpcoming as $item)
                                @php
                                    $st = strtolower($item->status ?? 'pending');
                                    $meta = $metaOf($st);
                                @endphp
                                <li class="px-5 py-3.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $item->title ?? $item->type_label ?? ucfirst(str_replace('_', ' ', $item->type ?? 'Tagihan')) }}
                                        </p>
                                        @if(!empty($item->due_date))
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                Jatuh tempo {{ \Carbon\Carbon::parse($item->due_date)->translatedFormat('d M Y') }}
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

                @if($myOverdue->isEmpty() && $myUpcoming->isEmpty())
                    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-12 text-center shadow-sm">
                        <div class="inline-flex flex-col items-center max-w-md">
                            <div class="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center mb-4">
                                <i class="fas fa-circle-check text-3xl text-emerald-500" aria-hidden="true"></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">Semua tagihan Anda lunas</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada tagihan aktif saat ini.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kanan: riwayat saya --}}
            <div class="anim-in anim-in-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-receipt text-emerald-500" aria-hidden="true"></i>
                        Riwayat Saya
                    </h3>
                </div>

                @if($myRecent->isEmpty())
                    <div class="p-8 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($myRecent as $p)
                            @php
                                $st = strtolower($p->status ?? 'pending');
                                $meta = $metaOf($st);
                            @endphp
                            <li class="px-5 py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">
                                        {{ strtoupper($p->method ?? 'CASH') }}
                                    </p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">
                                        {{ $fmtRp($p->amount ?? 0) }}
                                    </p>
                                    <span class="status-pill {{ $meta['class'] }} mt-0.5">
                                        <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                        {{ $meta['label'] }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    {{-- FOOTER --}}
    <div class="text-center text-[11px] text-gray-400 dark:text-gray-500 pt-3">
        <i class="fas fa-shield-check mr-1" aria-hidden="true"></i>
        Data keuangan terverifikasi · Terakhir diperbarui {{ now()->translatedFormat('d M Y, H:i') }} WIB
    </div>
</div>
@endsection