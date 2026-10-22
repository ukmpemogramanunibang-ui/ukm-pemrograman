@extends('layouts.roles.owner')

@section('title', 'Riwayat Pembayaran - UNICODE')

@push('styles')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-in { animation: fadeSlideUp .28s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay: .04s; }
    .anim-in-2 { animation-delay: .08s; }
    .anim-in-3 { animation-delay: .12s; }

    .row-hoverable { transition: background-color .15s ease; }
    .row-hoverable:hover { background-color: rgba(59, 130, 246, .035); }
    .dark .row-hoverable:hover { background-color: rgba(59, 130, 246, .07); }

    .focus-ring:focus-visible {
        outline: 2px solid rgb(59, 130, 246);
        outline-offset: 2px;
        border-radius: .5rem;
    }

    .status-pill {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .25rem .625rem; border-radius: 9999px;
        font-size: .6875rem; font-weight: 600; letter-spacing: .01em;
        white-space: nowrap;
    }
    .status-pill i { font-size: .625rem; }

    .method-chip {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .25rem .625rem; border-radius: .375rem;
        font-size: .6875rem; font-weight: 500;
    }
</style>
@endpush

@section('content')
@php
    /* ============================================================
     | Peta status — INLINE, tidak bergantung model/konstanta backend.
     | Aman di-render dari konteks apa pun (view biasa, mail, PDF).
     | ============================================================ */
    $statusMap = [
        'pending'     => ['label' => 'Menunggu',      'icon' => 'fa-hourglass-half',    'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        'paid'        => ['label' => 'Berhasil',      'icon' => 'fa-circle-check',      'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'settled'     => ['label' => 'Selesai',       'icon' => 'fa-circle-check',      'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'success'     => ['label' => 'Berhasil',      'icon' => 'fa-circle-check',      'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'failed'      => ['label' => 'Gagal',         'icon' => 'fa-circle-xmark',      'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'expired'     => ['label' => 'Kedaluwarsa',   'icon' => 'fa-clock-rotate-left', 'class' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-400'],
        'refunded'    => ['label' => 'Refund',        'icon' => 'fa-rotate-left',       'class' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300'],
        'reconciling' => ['label' => 'Rekonsiliasi',  'icon' => 'fa-scale-balanced',    'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'cancelled'   => ['label' => 'Dibatalkan',    'icon' => 'fa-ban',               'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500'],
    ];

    $methodMap = [
        'qris'          => ['label' => 'QRIS',           'icon' => 'fa-qrcode'],
        'cash'          => ['label' => 'Tunai',          'icon' => 'fa-money-bill-wave'],
        'bank_transfer' => ['label' => 'Transfer Bank',  'icon' => 'fa-building-columns'],
        'ewallet'       => ['label' => 'Dompet Digital', 'icon' => 'fa-wallet'],
        'gopay'         => ['label' => 'GoPay',          'icon' => 'fa-wallet'],
        'dana'          => ['label' => 'DANA',           'icon' => 'fa-wallet'],
        'ovo'           => ['label' => 'OVO',            'icon' => 'fa-wallet'],
    ];

    $summary      = $summary ?? [];
    $sumReceived  = (float) ($summary['total_received'] ?? 0);
    $sumPending   = (int)   ($summary['pending_count'] ?? 0);
    $sumFailed    = (int)   ($summary['failed_count'] ?? 0);
    $sumThisMonth = (float) ($summary['received_this_month'] ?? 0);
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
        <span class="text-gray-700 dark:text-gray-200 font-medium">Pembayaran</span>
    </nav>

    @include('owner.financial._partials.submenu', ['active' => 'payments'])

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-6 mt-5 gap-4 anim-in">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-receipt text-white text-sm" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Riwayat Pembayaran</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-11.5">
                Semua transaksi pembayaran anggota — digital maupun tunai — dengan status verifikasi dan rekonsiliasi.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.payments.index', ['status' => 'pending']) }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-700 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                <i class="fas fa-hourglass-half text-xs text-amber-500" aria-hidden="true"></i>
                Menunggu
                @if($sumPending > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-[10px] font-bold">{{ $sumPending }}</span>
                @endif
            </a>
            <a href="{{ route('owner.financial.reports.index') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-gray-300 text-gray-700 dark:text-gray-200 text-sm font-medium shadow-sm transition-all">
                <i class="fas fa-chart-line text-xs text-gray-500" aria-hidden="true"></i>
                Laporan
            </a>
        </div>
    </div>

    {{-- ═══════════════ RINGKASAN KARTU ═══════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Diterima</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-sack-dollar text-emerald-600 dark:text-emerald-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight tabular-nums">
                Rp {{ number_format($sumReceived, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">akumulasi terverifikasi</p>
        </div>

        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Bulan Ini</span>
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-teal-600 dark:text-teal-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-teal-600 dark:text-teal-400 tracking-tight tabular-nums">
                Rp {{ number_format($sumThisMonth, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">penerimaan bulan berjalan</p>
        </div>

        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Menunggu Verifikasi</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-amber-600 dark:text-amber-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">{{ number_format($sumPending, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">transaksi perlu tindakan</p>
        </div>

        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Gagal</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                    <i class="fas fa-circle-xmark text-rose-600 dark:text-rose-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($sumFailed, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">perlu ditindaklanjuti</p>
        </div>
    </div>

    {{-- ═══════════════ FILTER BAR ═══════════════ --}}
    <form method="GET" action="{{ route('owner.financial.payments.index') }}" id="filterForm"
          class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-3 mb-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5">
            <div class="lg:col-span-4 relative">
                <label for="q" class="sr-only">Cari pembayaran</label>
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none" aria-hidden="true"></i>
                <input type="text" id="q" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, NIM, atau referensi…"
                       autocomplete="off"
                       class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white placeholder:text-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
            </div>

            <div class="lg:col-span-2">
                <label for="status" class="sr-only">Status</label>
                <select id="status" name="status"
                        class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    <option value="">Semua status</option>
                    @foreach($statusMap as $key => $meta)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="method" class="sr-only">Metode</label>
                <select id="method" name="method"
                        class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    <option value="">Semua metode</option>
                    @foreach($methodMap as $key => $meta)
                        <option value="{{ $key }}" @selected(request('method') === $key)>{{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="from" class="sr-only">Dari tanggal</label>
                <input type="date" id="from" name="from" value="{{ request('from') }}"
                       class="w-full py-2.5 px-3 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
            </div>

            <div class="lg:col-span-2 flex gap-2">
                <button type="submit"
                        class="focus-ring flex-1 inline-flex items-center justify-center gap-2 py-2.5 rounded-lg bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-semibold transition-colors">
                    <i class="fas fa-filter text-xs" aria-hidden="true"></i>
                    Terapkan
                </button>
                @if(request()->hasAny(['q','status','method','from','to']))
                    <a href="{{ route('owner.financial.payments.index') }}"
                       class="focus-ring inline-flex items-center justify-center px-3 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors"
                       title="Reset filter">
                        <i class="fas fa-xmark" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Chip filter aktif --}}
        @if(request()->hasAny(['q','status','method','from','to']))
            <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Filter aktif:</span>
                @if(request('q'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs">
                        <i class="fas fa-search text-[9px]" aria-hidden="true"></i>"{{ request('q') }}"
                    </span>
                @endif
                @if(request('status') && isset($statusMap[request('status')]))
                    <span class="status-pill {{ $statusMap[request('status')]['class'] }}">
                        <i class="fas {{ $statusMap[request('status')]['icon'] }}" aria-hidden="true"></i>
                        {{ $statusMap[request('status')]['label'] }}
                    </span>
                @endif
                @if(request('method') && isset($methodMap[request('method')]))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs">
                        <i class="fas {{ $methodMap[request('method')]['icon'] }} text-[9px]" aria-hidden="true"></i>
                        {{ $methodMap[request('method')]['label'] }}
                    </span>
                @endif
            </div>
        @endif
    </form>

    {{-- ═══════════════ TABEL ═══════════════ --}}
    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">Riwayat transaksi pembayaran anggota</caption>
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Metode</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Referensi</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($payments as $payment)
                        @php
                            $status = $payment->status ?? 'pending';
                            $meta   = $statusMap[$status] ?? $statusMap['pending'];

                            // Method label fallback — gunakan map lokal bila tidak ada accessor
                            $methodKey   = strtolower($payment->method ?? '');
                            $methodMeta  = $methodMap[$methodKey] ?? ['label' => ucfirst($methodKey ?: 'Lainnya'), 'icon' => 'fa-circle-dot'];
                            $methodLabel = $payment->method_label ?? $methodMeta['label'];

                            $createdAt = $payment->created_at ?? null;
                        @endphp
                        <tr class="row-hoverable">
                            {{-- Waktu --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($createdAt)
                                    <div class="text-xs font-medium text-gray-800 dark:text-gray-200 tabular-nums">
                                        {{ $createdAt->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 tabular-nums">
                                        {{ $createdAt->translatedFormat('H:i') }} WIB
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Anggota --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-700 dark:text-slate-200 shrink-0">
                                        {{ strtoupper(substr($payment->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $payment->user->name ?? 'Tanpa nama' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $payment->user->nim ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Metode --}}
                            <td class="px-4 py-3 whitespace-nowrap hidden sm:table-cell">
                                <span class="method-chip bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300">
                                    <i class="fas {{ $methodMeta['icon'] }} text-[10px]" aria-hidden="true"></i>
                                    {{ $methodLabel }}
                                </span>
                            </td>

                            {{-- Nominal --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="font-semibold text-gray-900 dark:text-white tabular-nums">
                                    Rp {{ number_format((float) ($payment->amount ?? 0), 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="status-pill {{ $meta['class'] }}">
                                    <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>
                                    {{ $meta['label'] }}
                                </span>
                            </td>

                            {{-- Referensi --}}
                            <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                                @if(!empty($payment->reference))
                                    <span class="font-mono text-[11px] text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/50 px-2 py-0.5 rounded">
                                        {{ Str::limit($payment->reference, 24, '…') }}
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <a href="{{ route('owner.financial.payments.show', $payment) }}"
                                   class="focus-ring inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                   title="Lihat detail transaksi">
                                    <i class="fas fa-eye text-[10px]" aria-hidden="true"></i>
                                    <span class="hidden sm:inline">Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="inline-flex flex-col items-center max-w-sm">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                        <i class="fas fa-receipt text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">
                                        @if(request()->hasAny(['q','status','method','from','to']))
                                            Tidak ada pembayaran yang cocok
                                        @else
                                            Belum ada pembayaran
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                                        @if(request()->hasAny(['q','status','method','from','to']))
                                            Coba ubah atau reset filter untuk melihat transaksi lainnya.
                                        @else
                                            Transaksi akan muncul di sini setelah anggota melakukan pembayaran.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['q','status','method','from','to']))
                                        <a href="{{ route('owner.financial.payments.index') }}"
                                           class="focus-ring inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition-colors">
                                            <i class="fas fa-rotate-left text-xs" aria-hidden="true"></i>
                                            Reset filter
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
        @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif

        {{-- Footer info --}}
        @if($payments->count() > 0)
            <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/10 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>
                    Menampilkan <strong class="text-gray-700 dark:text-gray-300">{{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }}</strong>
                    dari <strong class="text-gray-700 dark:text-gray-300">{{ number_format($payments->total(), 0, ',', '.') }}</strong> transaksi
                </span>
                <span class="hidden sm:inline">
                    <i class="fas fa-shield-check text-[10px] mr-1" aria-hidden="true"></i>
                    Sumber daya terverifikasi
                </span>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit select filter
    document.querySelectorAll('#filterForm select').forEach(el => {
        el.addEventListener('change', () => el.closest('form').submit());
    });

    // Debounce search input (550ms, minimal 3 karakter)
    (function () {
        const q = document.getElementById('q');
        if (!q) return;
        let t;
        q.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                const v = q.value.trim();
                if (v.length === 0 || v.length >= 3) q.closest('form').submit();
            }, 550);
        });
    })();
</script>
@endpush