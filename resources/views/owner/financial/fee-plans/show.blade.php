@extends('layouts.roles.owner')

@section('title', 'Detail Skema Biaya - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.fee-plans.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Skema: {{ $feePlan->name }}</h1>
        <span class="px-3 py-1 text-xs rounded-full {{ $feePlan->status_badge }}">{{ ucfirst($feePlan->status) }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Informasi Utama -->
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Informasi Skema</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kode</p>
                        <p class="font-mono text-sm text-gray-800 dark:text-white">{{ $feePlan->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nama</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $feePlan->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Generasi</p>
                        <p class="text-gray-800 dark:text-white">{{ $feePlan->generation->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Biaya</p>
                        <p class="text-gray-800 dark:text-white">{{ $feePlan->fee_type_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Versi</p>
                        <p class="text-gray-800 dark:text-white">v{{ $feePlan->version }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                        <p class="text-gray-800 dark:text-white">{{ $feePlan->creator->name ?? 'Sistem' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Deskripsi</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $feePlan->description ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Nominal & Target -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Nominal & Target</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nominal</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($feePlan->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $feePlan->nominal_type_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Target</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $feePlan->target_type_label }}</p>
                        @if($feePlan->target_type == 'group' && $feePlan->target_data)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($feePlan->target_data['group_ids'] ?? []) }} kelompok</p>
                        @elseif($feePlan->target_type == 'individual' && $feePlan->target_data)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($feePlan->target_data['user_ids'] ?? []) }} anggota</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Frekuensi</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $feePlan->frequency_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jatuh Tempo</p>
                        <p class="font-medium text-gray-800 dark:text-white">Tanggal {{ $feePlan->due_day }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Toleransi: {{ $feePlan->grace_period }} hari</p>
                    </div>
                </div>
            </div>

            <!-- Masa Berlaku -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Masa Berlaku</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Berlaku Mulai</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $feePlan->effective_from->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Berlaku Sampai</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $feePlan->effective_until ? $feePlan->effective_until->format('d M Y') : 'Tidak terbatas' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                        <span class="px-2 py-1 text-xs rounded-full {{ $feePlan->status_badge }}">{{ ucfirst($feePlan->status) }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Catatan</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $feePlan->notes ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Statistik -->
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Tagihan</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ number_format($totalInvoices) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-green-600 dark:text-green-400">Lunas</span>
                        <span class="font-medium text-green-600 dark:text-green-400">{{ number_format($paidInvoices) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-yellow-600 dark:text-yellow-400">Pending</span>
                        <span class="font-medium text-yellow-600 dark:text-yellow-400">{{ number_format($pendingInvoices) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-red-600 dark:text-red-400">Terlambat</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($overdueInvoices) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Pendapatan</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Aksi Cepat -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Aksi</h3>
                <div class="space-y-2">
                    <a href="{{ route('owner.financial.fee-plans.edit', $feePlan) }}" class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-edit mr-2"></i> Edit Skema
                    </a>
                    <a href="{{ route('owner.financial.fee-plans.duplicate', $feePlan) }}" class="block w-full text-center bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-copy mr-2"></i> Duplikasi
                    </a>
                    <a href="#" class="block w-full text-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-file-invoice mr-2"></i> Lihat Tagihan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection