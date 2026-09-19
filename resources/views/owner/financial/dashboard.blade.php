@extends('layouts.roles.owner')

@section('title', 'Dashboard Keuangan - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Keuangan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan keuangan UKM Pemrograman UNICODE</p>
        </div>
        <div class="mt-3 md:mt-0 flex gap-2">
            <a href="{{ route('owner.financial.invoices.generate') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-sync-alt"></i> Generate Tagihan
            </a>
            <a href="{{ route('owner.financial.reports.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-file-alt"></i> Laporan
            </a>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalInvoices, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
            </div>
            <div class="mt-2 flex gap-2 text-xs">
                <span class="text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i> {{ $paidInvoices }} Lunas</span>
                <span class="text-yellow-600 dark:text-yellow-400"><i class="fas fa-clock"></i> {{ $pendingInvoices }} Pending</span>
                <span class="text-red-600 dark:text-red-400"><i class="fas fa-exclamation-circle"></i> {{ $overdueInvoices }} Telat</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalPayments, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Denda</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($totalPenalty, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-400">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Anggota Aktif</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalMembers + $totalOfficials, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Anggota: {{ $totalMembers }} | Pengurus: {{ $totalOfficials }}
            </div>
        </div>
    </div>

    <!-- Grafik Bulanan (sementara berupa tabel) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pendapatan Bulanan (6 Bulan Terakhir)</h3>
        @if($monthlyData->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 text-gray-600 dark:text-gray-400">Bulan</th>
                            <th class="text-right py-2 text-gray-600 dark:text-gray-400">Total</th>
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
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data pembayaran.</p>
        @endif
    </div>
</div>
@endsection