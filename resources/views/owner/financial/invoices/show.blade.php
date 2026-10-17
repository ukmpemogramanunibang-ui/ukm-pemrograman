@extends('layouts.roles.owner')

@section('title', 'Detail Tagihan ' . $invoice->invoice_number . ' - UNICODE')

@section('content')
<div class="p-4 md:p-6">

    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-3" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}" class="hover:text-blue-600">Keuangan</a>
        <i class="fas fa-chevron-right mx-2" aria-hidden="true"></i>
        <a href="{{ route('owner.financial.invoices.index') }}" class="hover:text-blue-600">Tagihan</a>
        <i class="fas fa-chevron-right mx-2" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium font-mono">{{ $invoice->invoice_number }}</span>
    </nav>

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

    @php
        $paid = (float) ($invoice->paid_amount ?? 0);
        $total = (float) ($invoice->total ?? 0);
        $remaining = max(0, $total - $paid);
    @endphp

    {{-- ============== HEADER KARTU ============== --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-sm p-6 mb-6 text-white">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm opacity-90">Tagihan untuk</p>
                <h1 class="text-2xl font-bold">{{ $invoice->user->name ?? '-' }}</h1>
                <p class="text-sm opacity-90 mt-1">
                    <span class="font-mono bg-white/20 px-2 py-0.5 rounded">{{ $invoice->user->nim ?? '-' }}</span>
                    · {{ $invoice->type_label ?? ucfirst($invoice->type) }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs opacity-80">Total Tagihan</p>
                <p class="text-3xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</p>
                @if($paid > 0 && $remaining > 0)
                    <p class="text-xs mt-1 opacity-90">Terbayar Rp {{ number_format($paid, 0, ',', '.') }} · Sisa Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ============== RINGKASAN STATUS ============== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
            <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $invoice->status_label ?? ucfirst($invoice->status) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Periode</p>
            <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $invoice->period ?? '—' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jatuh Tempo</p>
            <p class="text-base font-semibold {{ $invoice->final_due_date && $invoice->final_due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white' }}">
                {{ $invoice->final_due_date?->translatedFormat('d M Y') ?? '—' }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dibuat</p>
            <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $invoice->created_at?->translatedFormat('d M Y H:i') ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ============== KIRI: ALOKASI PEMBAYARAN ============== --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-list-ul text-blue-500 mr-2" aria-hidden="true"></i>
                        Riwayat Alokasi Pembayaran
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Metode</th>
                                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nominal</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ref</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($invoice->allocations as $alloc)
                                <tr>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                        {{ $alloc->payment?->confirmed_at?->translatedFormat('d M Y H:i') ?? $alloc->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $alloc->payment?->method_label ?? '—' }}</td>
                                    <td class="px-4 py-2 text-right font-medium text-green-600 dark:text-green-400">
                                        Rp {{ number_format($alloc->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-xs font-mono text-gray-500 dark:text-gray-400">{{ $alloc->payment?->reference ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada pembayaran yang dialokasikan ke tagihan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($invoice->allocations->isNotEmpty())
                            <tfoot class="bg-gray-50 dark:bg-gray-700/30">
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-right text-xs font-medium text-gray-600 dark:text-gray-300">Total terbayar</td>
                                    <td class="px-4 py-2 text-right font-bold text-green-600 dark:text-green-400">
                                        Rp {{ number_format($paid, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                                @if($remaining > 0)
                                    <tr>
                                        <td colspan="2" class="px-4 py-2 text-right text-xs font-medium text-gray-600 dark:text-gray-300">Sisa tagihan</td>
                                        <td class="px-4 py-2 text-right font-bold text-orange-600 dark:text-orange-400">
                                            Rp {{ number_format($remaining, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Audit trail --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-history text-gray-500 mr-2" aria-hidden="true"></i>
                        Jejak Audit
                    </h3>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($invoice->auditLogs ?? [] as $log)
                        <li class="px-5 py-3 text-sm flex items-start gap-3">
                            <i class="fas fa-circle text-[6px] text-gray-400 mt-1.5" aria-hidden="true"></i>
                            <div class="flex-1">
                                <p class="text-gray-700 dark:text-gray-300">{{ $log->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $log->created_at->translatedFormat('d M Y H:i') }}
                                    · oleh {{ $log->user->name ?? 'Sistem' }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- ============== KANAN: AKSI ============== --}}
        <div class="space-y-6" id="payment">
            {{-- Kartu kredit anggota --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Saldo Kredit Anggota</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($invoice->user->credit_balance ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kredit ini otomatis dipakai saat tagihan baru terbit.</p>
            </div>

            {{-- Aksi pembayaran --}}
            @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-3">
                    <i class="fas fa-money-bill-wave text-green-600 mr-2" aria-hidden="true"></i>
                    Proses Pembayaran
                </h3>

                {{-- Tandai lunas — pembayaran tunai oleh bendahara --}}
                @can('confirm-cash', $invoice)
                <form method="POST" action="{{ route('owner.financial.invoices.confirm-cash', $invoice) }}"
                      onsubmit="return confirm('Konfirmasi penerimaan tunai sebesar Rp {{ number_format($remaining > 0 ? $remaining : $total, 0, ',', '.') }}?')"
                      class="space-y-3">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $remaining > 0 ? $remaining : $total }}">
                    <div>
                        <label for="receipt_number" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">No. Kuitansi (opsional)</label>
                        <input type="text" id="receipt_number" name="receipt_number"
                               placeholder="Otomatis jika kosong"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label for="note" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Catatan bendahara</label>
                        <textarea id="note" name="note" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 transition">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        Tandai Lunas (Tunai)
                    </button>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Tindakan ini tercatat sebagai penerimaan kas dan dijejak di audit log.
                    </p>
                </form>
                @endcan

                {{-- Tombol bayar digital --}}
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
                    @if($invoice->payment_link_url)
                        <a href="{{ $invoice->payment_link_url }}" target="_blank" rel="noopener"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 transition">
                            <i class="fas fa-qrcode" aria-hidden="true"></i> Bayar via QRIS
                        </a>
                    @else
                        <form method="POST" action="{{ route('owner.financial.payments.create-qris', $invoice) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 transition">
                                <i class="fas fa-qrcode" aria-hidden="true"></i> Buat QRIS Dinamis
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @else
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5 text-center">
                    <i class="fas fa-check-circle text-3xl text-green-600 dark:text-green-400 mb-2" aria-hidden="true"></i>
                    <p class="font-semibold text-green-800 dark:text-green-300">Tagihan Lunas</p>
                    <p class="text-xs text-green-700 dark:text-green-400 mt-1">Tidak ada tindakan lebih lanjut.</p>
                </div>
            @endif

            {{-- Pembatalan --}}
            @can('cancel', $invoice)
                @if(!in_array($invoice->status, ['paid', 'cancelled']))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <form method="POST" action="{{ route('owner.financial.invoices.cancel', $invoice) }}"
                          onsubmit="return confirm('Batalkan tagihan ini? Tindakan tidak dapat dibatalkan.')">
                        @csrf
                        <button type="submit"
                                class="w-full text-red-600 hover:text-red-700 dark:text-red-400 text-sm font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-times-circle" aria-hidden="true"></i> Batalkan Tagihan
                        </button>
                    </form>
                </div>
                @endif
            @endcan
        </div>
    </div>
</div>
@endsection