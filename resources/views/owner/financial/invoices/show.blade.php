@extends('layouts.roles.owner')

@section('title', 'Detail Tagihan - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.invoices.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Tagihan</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 max-w-3xl">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nomor Tagihan</p>
                <p class="text-xl font-mono font-bold text-gray-800 dark:text-white">{{ $invoice->invoice_number }}</p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full 
                {{ $invoice->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                   ($invoice->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                   ($invoice->status == 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400')) }}">
                {{ strtoupper($invoice->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Anggota</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $invoice->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Generasi</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $invoice->generation->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Jenis</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ str_replace('_', ' ', ucfirst($invoice->type)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Periode</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $invoice->period ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Jatuh Tempo</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $invoice->final_due_date->format('d M Y') }}</p>
                @if($invoice->due_date_reason)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->due_date_reason }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Bayar</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $invoice->paid_at ? $invoice->paid_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Rincian</h3>
            <div class="space-y-2">
                @foreach($invoice->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ $item->description }}</span>
                    <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($invoice->penalty > 0)
                <div class="flex justify-between text-sm text-red-600 dark:text-red-400">
                    <span>Denda</span>
                    <span>+ Rp {{ number_format($invoice->penalty, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($invoice->discount > 0)
                <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-gray-800 dark:text-white">Total</span>
                    <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            @if($invoice->status == 'pending')
            <a href="#" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-check mr-2"></i> Tandai Lunas
            </a>
            @endif
            <a href="{{ route('owner.financial.invoices.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection