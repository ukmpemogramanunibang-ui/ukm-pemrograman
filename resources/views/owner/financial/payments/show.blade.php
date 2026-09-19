@extends('layouts.roles.owner')

@section('title', 'Detail Pembayaran - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.payments.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Pembayaran</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 max-w-3xl">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ID Transaksi</p>
                <p class="text-xl font-mono font-bold text-gray-800 dark:text-white">{{ $payment->transaction_id ?? $payment->id }}</p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full 
                {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                   ($payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                   ($payment->status == 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400')) }}">
                {{ strtoupper($payment->status ?? '') }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Anggota</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $payment->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah</p>
                <p class="font-medium text-green-600 dark:text-green-400">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Metode Pembayaran</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ strtoupper($payment->payment_method ?? '-') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gateway</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $payment->gateway ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Bayar</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : ($payment->created_at ? $payment->created_at->format('d M Y H:i') : '-') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">External ID</p>
                <p class="font-medium text-gray-800 dark:text-white font-mono text-sm">{{ $payment->external_id ?? '-' }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Alokasi Pembayaran</h3>
            @if($payment->allocations && $payment->allocations->count() > 0)
                <div class="space-y-2">
                    @foreach($payment->allocations as $allocation)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tagihan {{ $allocation->invoice->invoice_number ?? '-' }}</span>
                        <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($allocation->amount, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada alokasi pembayaran.</p>
            @endif
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('owner.financial.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection