@extends('layouts.roles.owner')

@section('title', 'Manajemen Tagihan - UNICODE')

@push('styles')
<style>
    /* Smooth entry animation — lebih halus, tidak berlebihan */
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-in { animation: fadeSlideUp .28s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay: .04s; }
    .anim-in-2 { animation-delay: .08s; }
    .anim-in-3 { animation-delay: .12s; }

    /* Row hover — aksen garis kiri */
    .row-hoverable { transition: background-color .15s ease, box-shadow .15s ease; }
    .row-hoverable:hover { background-color: rgba(59, 130, 246, .035); }
    .dark .row-hoverable:hover { background-color: rgba(59, 130, 246, .07); }

    /* Focus ring konsisten untuk seluruh aksi */
    .focus-ring:focus-visible {
        outline: 2px solid rgb(59, 130, 246);
        outline-offset: 2px;
        border-radius: .5rem;
    }

    /* Status pill */
    .status-pill {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .25rem .625rem; border-radius: 9999px;
        font-size: .6875rem; font-weight: 600; letter-spacing: .01em;
        white-space: nowrap;
    }
    .status-pill i { font-size: .625rem; }

    /* Skeleton loading */
    .skeleton {
        background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
        background-size: 200% 100%;
        animation: shimmer 1.4s ease-in-out infinite;
        border-radius: .375rem;
    }
    .dark .skeleton {
        background: linear-gradient(90deg, #374151 0%, #4b5563 50%, #374151 100%);
        background-size: 200% 100%;
    }
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('content')
@php
    /* ============================================================
     | Peta status — DIDEfinisikan inline agar view tidak bergantung
     | pada model/konstanta backend. Aman di semua konteks render.
     | ============================================================ */
    $statusMap = [
        'draft'     => ['label' => 'Draft',          'icon' => 'fa-pen',              'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
        'pending'   => ['label' => 'Menunggu Bayar', 'icon' => 'fa-hourglass-half',   'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        'partial'   => ['label' => 'Bayar Sebagian', 'icon' => 'fa-circle-half-stroke','class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'],
        'paid'      => ['label' => 'Lunas',          'icon' => 'fa-circle-check',     'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'overdue'   => ['label' => 'Terlambat',      'icon' => 'fa-triangle-exclamation','class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'expired'   => ['label' => 'Kedaluwarsa',    'icon' => 'fa-clock-rotate-left','class' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-400'],
        'cancelled' => ['label' => 'Dibatalkan',     'icon' => 'fa-ban',              'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500'],
    ];

    $filterOptions = [
        'draft', 'pending', 'partial', 'paid', 'overdue', 'expired', 'cancelled',
    ];
@endphp

<div class="p-4 md:p-6 max-w-[1600px] mx-auto">

    {{-- ═══════════════ BREADCRUMB ═══════════════ --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}"
           class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-home text-[11px]" aria-hidden="true"></i>
            <span>Keuangan</span>
        </a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Tagihan</span>
    </nav>

    @include('owner.financial._partials.submenu', ['active' => 'invoices'])

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-6 mt-5 gap-4 anim-in">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-file-invoice-dollar text-white text-sm" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Manajemen Tagihan</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-11.5">
                Kelola seluruh tagihan anggota, pantau status pembayaran, dan proses konfirmasi.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.invoices.generate') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold shadow-sm hover:shadow transition-all">
                <i class="fas fa-bolt text-xs" aria-hidden="true"></i>
                Generate Tagihan
            </a>
            <a href="{{ route('owner.financial.invoices.check-tagihan') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                <i class="fas fa-shield-check text-xs text-indigo-500" aria-hidden="true"></i>
                Cek Valid
            </a>
            <a href="{{ route('owner.financial.reports.index') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                <i class="fas fa-chart-line text-xs text-gray-500" aria-hidden="true"></i>
                Laporan
            </a>
        </div>
    </div>

    {{-- ═══════════════ ALERTS ═══════════════ --}}
    @if(session('success'))
        <div role="status" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 mb-4">
            <i class="fas fa-circle-check mt-0.5 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-sm">{{ session('success') }}</p>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup"
                    class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 transition-colors">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div role="alert" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 mb-4">
            <i class="fas fa-circle-exclamation mt-0.5 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-sm">{{ session('error') }}</p>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup"
                    class="text-rose-600 dark:text-rose-400 hover:text-rose-800 transition-colors">&times;</button>
        </div>
    @endif

    {{-- ═══════════════ RINGKASAN KARTU ═══════════════ --}}
    @php
        $summary = $summary ?? [];
        $sumTotal    = (float) ($summary['total_amount'] ?? 0);
        $sumPaid     = (float) ($summary['paid_amount'] ?? 0);
        $sumOut      = (float) ($summary['outstanding_amount'] ?? 0);
        $sumCount    = (int)   ($summary['total_count'] ?? 0);
        $sumPaidCnt  = (int)   ($summary['paid_count'] ?? 0);
        $sumOverdue  = (int)   ($summary['overdue_count'] ?? 0);
        $sumPending  = (int)   ($summary['pending_count'] ?? 0);
        $pctPaid     = $sumTotal > 0 ? round(($sumPaid / $sumTotal) * 100, 1) : 0;
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        {{-- Total Tagihan --}}
        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Tagihan</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($sumCount, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Rp {{ number_format($sumTotal, 0, ',', '.') }}
            </p>
        </div>

        {{-- Terbayar --}}
        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Terbayar</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-circle-check text-emerald-600 dark:text-emerald-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight">
                {{ $pctPaid }}<span class="text-base">%</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Rp {{ number_format($sumPaid, 0, ',', '.') }} · {{ number_format($sumPaidCnt, 0, ',', '.') }} tagihan
            </p>
        </div>

        {{-- Belum Lunas --}}
        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Belum Lunas</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-amber-600 dark:text-amber-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">
                Rp {{ number_format($sumOut, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ number_format($sumPending, 0, ',', '.') }} menunggu konfirmasi
            </p>
        </div>

        {{-- Terlambat --}}
        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Terlambat</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($sumOverdue, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">perlu tindak lanjut</p>
        </div>
    </div>

    {{-- ═══════════════ FILTER BAR ═══════════════ --}}
    <form method="GET" action="{{ route('owner.financial.invoices.index') }}" id="filterForm"
          class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-3 mb-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5">
            {{-- Search --}}
            <div class="lg:col-span-4 relative">
                <label for="q" class="sr-only">Cari tagihan</label>
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none" aria-hidden="true"></i>
                <input type="text" id="q" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, NIM, atau no. tagihan…"
                       autocomplete="off"
                       class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white placeholder:text-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
            </div>

            {{-- Status --}}
            <div class="lg:col-span-2">
                <label for="status" class="sr-only">Status</label>
                <select id="status" name="status"
                        class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    <option value="">Semua status</option>
                    @foreach($filterOptions as $key)
                        <option value="{{ $key }}" @selected(request('status') === $key)>
                            {{ $statusMap[$key]['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Periode --}}
            <div class="lg:col-span-2">
                <label for="period" class="sr-only">Periode</label>
                <input type="month" id="period" name="period" value="{{ request('period') }}"
                       class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
            </div>

            {{-- Generasi --}}
            <div class="lg:col-span-2">
                <label for="generation_id" class="sr-only">Generasi</label>
                <select id="generation_id" name="generation_id"
                        class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    <option value="">Semua generasi</option>
                    @foreach(($generations ?? []) as $gen)
                        <option value="{{ $gen->id }}" @selected((string) request('generation_id') === (string) $gen->id)>{{ $gen->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="lg:col-span-2 flex gap-2">
                <button type="submit"
                        class="focus-ring flex-1 inline-flex items-center justify-center gap-2 py-2.5 rounded-lg bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-semibold transition-colors">
                    <i class="fas fa-filter text-xs" aria-hidden="true"></i>
                    Terapkan
                </button>
                @if(request()->hasAny(['q','status','period','generation_id']))
                    <a href="{{ route('owner.financial.invoices.index') }}"
                       class="focus-ring inline-flex items-center justify-center px-3 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors"
                       title="Reset filter">
                        <i class="fas fa-xmark" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Chip filter aktif --}}
        @if(request()->hasAny(['q','status','period','generation_id']))
            <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Filter aktif:</span>
                @if(request('q'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs">
                        <i class="fas fa-search text-[9px]" aria-hidden="true"></i>
                        "{{ request('q') }}"
                    </span>
                @endif
                @if(request('status') && isset($statusMap[request('status')]))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $statusMap[request('status')]['class'] }} text-xs">
                        <i class="fas {{ $statusMap[request('status')]['icon'] }} text-[9px]" aria-hidden="true"></i>
                        {{ $statusMap[request('status')]['label'] }}
                    </span>
                @endif
                @if(request('period'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-xs">
                        <i class="fas fa-calendar text-[9px]" aria-hidden="true"></i>
                        {{ request('period') }}
                    </span>
                @endif
            </div>
        @endif
    </form>

    {{-- ═══════════════ TABEL ═══════════════ --}}
    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">Daftar tagihan anggota beserta status pembayarannya</caption>
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Tagihan</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Jenis</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Terbayar</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Jatuh Tempo</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($invoices as $invoice)
                        @php
                            $status = $invoice->status ?? 'draft';
                            $meta   = $statusMap[$status] ?? $statusMap['draft'];
                            $paid   = (float) ($invoice->paid_amount ?? 0);
                            $total  = (float) ($invoice->total ?? 0);
                            $remain = max(0, $total - $paid);
                            $isOverdue = $status === 'overdue'
                                || ($invoice->final_due_date
                                    && $invoice->final_due_date->isPast()
                                    && !in_array($status, ['paid','cancelled','expired'], true));
                        @endphp
                        <tr class="row-hoverable">
                            {{-- No. Tagihan --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs font-medium text-gray-700 dark:text-gray-300 tracking-tight">
                                    {{ $invoice->invoice_number ?? '—' }}
                                </span>
                            </td>

                            {{-- Anggota --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-700 dark:text-slate-200 shrink-0">
                                        {{ strtoupper(substr($invoice->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $invoice->user->name ?? 'Tanpa nama' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $invoice->user->nim ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Jenis --}}
                            <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-[11px] font-medium">
                                    {{ $invoice->type_label ?? ucfirst(str_replace('_', ' ', $invoice->type ?? 'lainnya')) }}
                                </span>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="font-semibold text-gray-900 dark:text-white tabular-nums">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Terbayar --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right hidden lg:table-cell">
                                @if($paid > 0)
                                    <div class="flex flex-col items-end gap-0.5">
                                        <span class="font-medium text-emerald-600 dark:text-emerald-400 tabular-nums">
                                            Rp {{ number_format($paid, 0, ',', '.') }}
                                        </span>
                                        @if($remain > 0)
                                            <span class="text-[11px] text-orange-600 dark:text-orange-400 tabular-nums">
                                                sisa Rp {{ number_format($remain, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- Jatuh tempo --}}
                            <td class="px-4 py-3 whitespace-nowrap hidden sm:table-cell">
                                @if($invoice->final_due_date)
                                    <div class="flex items-center gap-1.5 {{ $isOverdue ? 'text-rose-600 dark:text-rose-400' : 'text-gray-600 dark:text-gray-400' }}">
                                        @if($isOverdue)
                                            <i class="fas fa-circle-exclamation text-[10px]" aria-hidden="true"></i>
                                        @endif
                                        <span class="text-xs font-medium tabular-nums">
                                            {{ $invoice->final_due_date->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="status-pill {{ $meta['class'] }}">
                                    <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                    {{ $meta['label'] }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('owner.financial.invoices.show', $invoice) }}"
                                       class="focus-ring inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                       title="Lihat detail">
                                        <i class="fas fa-eye text-[10px]" aria-hidden="true"></i>
                                        <span class="hidden sm:inline">Detail</span>
                                    </a>
                                    @if(!in_array($status, ['paid','cancelled','expired'], true))
                                        <a href="{{ route('owner.financial.invoices.show', $invoice) }}#payment"
                                           class="focus-ring inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors shadow-sm"
                                           title="Proses pembayaran">
                                            <i class="fas fa-money-bill-wave text-[10px]" aria-hidden="true"></i>
                                            <span class="hidden sm:inline">Bayar</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="inline-flex flex-col items-center max-w-sm">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                        <i class="fas fa-file-invoice text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">
                                        @if(request()->hasAny(['q','status','period','generation_id']))
                                            Tidak ada tagihan yang cocok
                                        @else
                                            Belum ada tagihan
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                                        @if(request()->hasAny(['q','status','period','generation_id']))
                                            Coba ubah atau reset filter untuk melihat tagihan lainnya.
                                        @else
                                            Buat tagihan otomatis dari skema biaya aktif untuk memulai.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['q','status','period','generation_id']))
                                        <a href="{{ route('owner.financial.invoices.index') }}"
                                           class="focus-ring inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition-colors">
                                            <i class="fas fa-rotate-left text-xs" aria-hidden="true"></i>
                                            Reset filter
                                        </a>
                                    @else
                                        <a href="{{ route('owner.financial.invoices.generate') }}"
                                           class="focus-ring inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shadow-sm">
                                            <i class="fas fa-bolt text-xs" aria-hidden="true"></i>
                                            Generate Tagihan
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @endif

        {{-- Footer info --}}
        @if($invoices->count() > 0)
            <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/10 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>
                    Menampilkan <strong class="text-gray-700 dark:text-gray-300">{{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }}</strong>
                    dari <strong class="text-gray-700 dark:text-gray-300">{{ number_format($invoices->total(), 0, ',', '.') }}</strong> tagihan
                </span>
                <span class="hidden sm:inline">
                    <i class="fas fa-shield-check text-[10px] mr-1" aria-hidden="true"></i>
                    Data dari server · aman
                </span>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Auto-submit saat select berubah (UX lebih halus)
    document.querySelectorAll('#filterForm select').forEach(el => {
        el.addEventListener('change', () => {
            el.closest('form').submit();
        });
    });

    // Debounce untuk search input (500ms)
    (function () {
        const q = document.getElementById('q');
        if (!q) return;
        let t;
        q.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                if (q.value.trim().length === 0 || q.value.trim().length >= 3) {
                    q.closest('form').submit();
                }
            }, 550);
        });
    })();
</script>
@endpush