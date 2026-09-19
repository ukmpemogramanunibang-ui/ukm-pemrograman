@extends('layouts.roles.owner')

@section('title', 'Manajemen Tagihan - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tagihan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola semua tagihan anggota</p>
        </div>
        <div class="mt-3 md:mt-0 flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.invoices.generate') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-sync-alt"></i> Generate Tagihan
            </a>
            {{-- TOMBOL CEK VALID TAGIHAN --}}
            <a href="{{ route('owner.financial.invoices.check-tagihan') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-check-circle"></i> Cek Valid Tagihan Akun
            </a>
            {{-- TOMBOL CEK VALID PENGUMUMAN --}}
            <a href="{{ route('owner.financial.invoices.check-pengumuman') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-bullhorn"></i> Cek Valid Pengumuman Akun
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Tagihan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($invoices ?? [] as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-600 dark:text-gray-400">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-white">{{ $invoice->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">
                                {{ str_replace('_', ' ', ucfirst($invoice->type)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800 dark:text-white">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $invoice->final_due_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $invoice->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                   ($invoice->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                   ($invoice->status == 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400')) }}">
                                {{ strtoupper($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('owner.financial.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-file-invoice text-2xl block mb-2 text-gray-300 dark:text-gray-600"></i>
                            Belum ada tagihan. Klik "Generate Tagihan" untuk membuat tagihan otomatis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @isset($invoices)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $invoices->links() }}
        </div>
        @endisset
    </div>
</div>
@endsection