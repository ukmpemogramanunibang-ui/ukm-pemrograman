@extends('layouts.roles.owner')

@section('title', 'Laporan Keuangan - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Laporan Keuangan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan laporan keuangan UKM</p>
        </div>
        <a href="{{ route('owner.financial.reports.export') }}" class="mt-3 md:mt-0 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
            <i class="fas fa-download"></i> Export Laporan
        </a>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($totalInvoices, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Denda</p>
            <p class="text-xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($totalPenalty, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Tunggakan</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($totalPending + $totalOverdue, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Grafik Bulanan -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pendapatan Bulanan</h3>
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
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data.</p>
        @endif
    </div>

    <!-- Kategori -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pendapatan per Kategori</h3>
        @if($categoryData->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 text-gray-600 dark:text-gray-400">Kategori</th>
                            <th class="text-right py-2 text-gray-600 dark:text-gray-400">Total</th>
                            <th class="text-right py-2 text-gray-600 dark:text-gray-400">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = $categoryData->sum('total'); @endphp
                        @foreach($categoryData as $cat)
                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                            <td class="py-2 text-gray-800 dark:text-white">{{ str_replace('_', ' ', ucfirst($cat->type)) }}</td>
                            <td class="py-2 text-right text-green-600 dark:text-green-400 font-medium">Rp {{ number_format($cat->total, 0, ',', '.') }}</td>
                            <td class="py-2 text-right text-gray-600 dark:text-gray-400">{{ $grandTotal > 0 ? round(($cat->total / $grandTotal) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data.</p>
        @endif
    </div>
</div>
@endsection