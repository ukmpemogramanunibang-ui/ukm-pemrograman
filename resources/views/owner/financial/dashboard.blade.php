@extends('layouts.roles.owner')

@section('title', 'Dashboard Keuangan - UNICODE')

@section('content')
<div class="p-4 md:p-6">

    @php
        // Semua variabel sudah divalidasi controller — tidak ada fallback 0 yang menyamarkan.
        // Bila controller lupa mengirim, akan muncul exception di dev dan log di prod.
    @endphp

    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-3" aria-label="Breadcrumb">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 flex items-center gap-1">
            <i class="fas fa-home" aria-hidden="true"></i> Owner
        </a>
        <i class="fas fa-chevron-right mx-2" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Keuangan</span>
    </nav>

    @include('owner.financial._partials.submenu', ['active' => 'dashboard'])

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 mt-4 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Keuangan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan operasional keuangan UKM Pemrograman UNICODE</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.invoices.generate') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fas fa-sync-alt" aria-hidden="true"></i> Generate Tagihan
            </a>
            <a href="{{ route('owner.financial.reports.index') }}"
               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fas fa-file-alt" aria-hidden="true"></i> Laporan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div role="status" class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div role="alert" class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-exclamation-circle mr-2" aria-hidden="true"></i>{{ session('error') }}
        </div>
    @endif

    {{-- ============== KARTU UTAMA ============== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- 1. Tagihan periode ini --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tagihan Periode Ini</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($invoiceStats['count'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Nilai: Rp {{ number_format($invoiceStats['amount'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        {{-- 2. Belum lunas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Belum Lunas</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                        Rp {{ number_format($invoiceStats['outstanding'] ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ number_format($invoiceStats['unpaid_count'] ?? 0, 0, ',', '.') }} tagihan
                    </p>
                </div>
                <div class="w-11 h-11 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-400">
                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        {{-- 3. Sudah lunas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sudah Lunas</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        Rp {{ number_format($invoiceStats['paid_amount'] ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ number_format($invoiceStats['paid_count'] ?? 0, 0, ',', '.') }} tagihan
                    </p>
                </div>
                <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        {{-- 4. Kredit di muka --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kredit di Muka</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                        Rp {{ number_format($creditStats['total'] ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ number_format($creditStats['members'] ?? 0, 0, ',', '.') }} anggota
                    </p>
                </div>
                <div class="w-11 h-11 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="fas fa-wallet" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ============== ANTREAN & PERHATIAN ============== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Menunggu verifikasi --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                    <i class="fas fa-clock text-yellow-500 mr-1" aria-hidden="true"></i> Menunggu Verifikasi
                </h3>
                <span class="text-xs font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingVerifications->count() ?? 0 }}</span>
            </div>
            @if(($pendingVerifications ?? collect())->isNotEmpty())
                <ul class="space-y-2">
                    @foreach($pendingVerifications->take(4) as $p)
                        <li class="text-sm">
                            <a href="{{ route('owner.financial.payments.show', $p) }}" class="flex justify-between hover:text-blue-600">
                                <span class="text-gray-700 dark:text-gray-300 truncate">{{ $p->user->name ?? '-' }}</span>
                                <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('owner.financial.payments.index', ['status' => 'pending']) }}"
                   class="block text-center text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-3">
                    Lihat semua <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                </a>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada antrean.</p>
            @endif
        </div>

        {{-- Tunai belum dicatat --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">
                <i class="fas fa-hand-holding-usd text-emerald-500 mr-1" aria-hidden="true"></i> Penerimaan Tunai Terbaru
            </h3>
            @if(($recentCashReceipts ?? collect())->isNotEmpty())
                <ul class="space-y-2">
                    @foreach($recentCashReceipts->take(4) as $c)
                        <li class="text-sm flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300 truncate">{{ $c->user->name ?? '-' }}</span>
                            <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($c->amount, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada penerimaan tunai bulan ini.</p>
            @endif
        </div>

        {{-- Jatuh tempo minggu ini --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">
                <i class="fas fa-calendar-day text-red-500 mr-1" aria-hidden="true"></i> Jatuh Tempo Minggu Ini
            </h3>
            @if(($dueThisWeek ?? collect())->isNotEmpty())
                <ul class="space-y-2">
                    @foreach($dueThisWeek->take(4) as $d)
                        <li class="text-sm flex justify-between">
                            <span class="text-gray-700 dark:text-gray-300 truncate">{{ $d->user->name ?? '-' }}</span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                {{ $d->final_due_date?->translatedFormat('d M') ?? '-' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada jatuh tempo.</p>
            @endif
        </div>
    </div>

    {{-- ============== SKEMA BIAYA AKTIF ============== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-fire text-orange-500 mr-2" aria-hidden="true"></i> Skema Biaya Aktif
            </h3>
            <a href="{{ route('owner.financial.fee-plans.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                Kelola <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
            </a>
        </div>
        @if(($activeFeePlans ?? collect())->isNotEmpty())
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($activeFeePlans as $plan)
                    <div class="px-5 py-4 flex flex-col md:flex-row md:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $plan->code }}</span>
                                <h4 class="font-semibold text-gray-800 dark:text-white">{{ $plan->name }}</h4>
                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $plan->fee_type_label }}</span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <span><i class="fas fa-users mr-1" aria-hidden="true"></i>{{ $plan->target_type_label }}</span>
                                <span><i class="fas fa-coins mr-1" aria-hidden="true"></i>Rp {{ number_format($plan->amount, 0, ',', '.') }} / {{ $plan->frequency_label }}</span>
                                <span><i class="fas fa-calendar-day mr-1" aria-hidden="true"></i>Tgl {{ $plan->due_day }}</span>
                            </div>
                        </div>
                        <a href="{{ route('owner.financial.invoices.from-plan.preview', $plan) }}"
                           class="px-3 py-2 text-xs rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium flex items-center gap-1 self-start md:self-auto">
                            <i class="fas fa-bolt" aria-hidden="true"></i> Generate
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-5 py-12 text-center">
                <i class="fas fa-file-invoice-dollar text-4xl text-gray-300 dark:text-gray-600 mb-3" aria-hidden="true"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada skema biaya aktif</p>
                <a href="{{ route('owner.financial.fee-plans.create') }}"
                   class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-plus" aria-hidden="true"></i> Buat Skema Baru
                </a>
            </div>
        @endif
    </div>

    {{-- ============== TREN BULANAN ============== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Pendapatan 6 Bulan Terakhir</h3>
            <a href="{{ route('owner.financial.reports.index') }}" class="text-sm text-blue-600 dark:text-blue-400">Detail <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a>
        </div>
        @if(($monthlyData ?? collect())->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th scope="col" class="text-left py-2 text-gray-600 dark:text-gray-400">Bulan</th>
                            <th scope="col" class="text-right py-2 text-gray-600 dark:text-gray-400">Total Penerimaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyData as $data)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="py-2 text-gray-800 dark:text-white">{{ \Carbon\Carbon::createFromFormat('Y-m', $data->month)->translatedFormat('F Y') }}</td>
                                <td class="py-2 text-right text-green-600 dark:text-green-400 font-medium">Rp {{ number_format($data->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 py-8">Belum ada data pembayaran.</p>
        @endif
    </div>
</div>
@endsection