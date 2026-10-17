@extends('layouts.roles.anggota-ukm')

@section('title', 'Uangkas Saya – UNICODE')

@section('content')
@php
    $inv = $summary['invoices'] ?? [];
    $pay = $summary['payments'] ?? [];
    $recent = $summary['recent_payments'] ?? collect();
    $overdue = $summary['overdue_invoices'] ?? collect();
    $upcoming = $summary['upcoming_invoices'] ?? collect();

    $fmtRp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

    $statusMap = [
        'pending'   => ['label' => 'Menunggu Bayar', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        'paid'      => ['label' => 'Lunas',          'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'settled'   => ['label' => 'Lunas',          'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'success'   => ['label' => 'Berhasil',       'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'failed'    => ['label' => 'Gagal',          'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'overdue'   => ['label' => 'Terlambat',      'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'partial'   => ['label' => 'Sebagian',       'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'],
        'expired'   => ['label' => 'Kedaluwarsa',    'class' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-400'],
        'cancelled' => ['label' => 'Dibatalkan',     'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500'],
    ];
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto space-y-5">

    {{-- HEADER --}}
    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">Keuangan Saya</p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-coins text-purple-500 mr-2"></i>Uangkas
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Pantau tagihan, pembayaran, dan saldo kredit Anda.
        </p>
    </div>

    {{-- ═══ HERO — SALDO TUNGGAKAN ═══ --}}
    <div class="bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 rounded-2xl p-6 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <p class="text-[11px] uppercase tracking-[0.2em] text-white/70 font-bold">Total Tunggakan Anda</p>
                <p class="text-4xl lg:text-5xl font-extrabold mt-2">{{ $fmtRp($inv['outstanding'] ?? 0) }}</p>
                <p class="text-xs text-white/70 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    @if(($inv['overdue'] ?? 0) > 0)
                        Anda memiliki {{ $inv['overdue'] }} tagihan yang lewat jatuh tempo
                    @elseif(($inv['outstanding'] ?? 0) > 0)
                        Ada tagihan belum lunas, segera selesaikan
                    @else
                        Semua tagihan Anda lunas. Terima kasih! 🎉
                    @endif
                </p>
            </div>
            <div class="flex flex-col justify-center gap-2">
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-bold">Sudah Dibayar</p>
                    <p class="text-xl font-bold mt-0.5">{{ $fmtRp($pay['total_paid'] ?? 0) }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-bold">Bulan Ini</p>
                    <p class="text-xl font-bold mt-0.5">{{ $fmtRp($pay['this_month'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $cards = [
                ['label' => 'Total Tagihan',  'value' => $inv['total'] ?? 0,       'icon' => 'fa-file-invoice', 'color' => 'blue',    'type' => 'int'],
                ['label' => 'Lunas',          'value' => $inv['paid'] ?? 0,        'icon' => 'fa-circle-check', 'color' => 'emerald', 'type' => 'int'],
                ['label' => 'Menunggu',       'value' => $inv['pending'] ?? 0,     'icon' => 'fa-hourglass',    'color' => 'amber',   'type' => 'int'],
                ['label' => 'Terlambat',      'value' => $inv['overdue'] ?? 0,     'icon' => 'fa-triangle-exclamation', 'color' => 'rose', 'type' => 'int'],
            ];
        @endphp
        @foreach($cards as $c)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">{{ $c['label'] }}</p>
                    <div class="w-7 h-7 rounded-lg bg-{{ $c['color'] }}-500/15 text-{{ $c['color'] }}-500 flex items-center justify-center">
                        <i class="fas {{ $c['icon'] }} text-[11px]"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $c['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ═══ TAGIHAN MENUNGGU / TERLAMBAT ═══ --}}
    @if($overdue->isNotEmpty() || $upcoming->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @if($overdue->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-rose-200 dark:border-rose-900/40 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-900/40">
                        <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>Terlambat Bayar
                        </h3>
                    </div>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($overdue as $invItem)
                            @php
                                $st = strtolower($invItem->status ?? 'overdue');
                                $meta = $statusMap[$st] ?? $statusMap['overdue'];
                            @endphp
                            <li class="px-5 py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $invItem->title ?? $invItem->type_label ?? $invItem->type ?? 'Tagihan' }}
                                    </p>
                                    @if(!empty($invItem->due_date))
                                        <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium">
                                            Jatuh tempo {{ \Carbon\Carbon::parse($invItem->due_date)->translatedFormat('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                        {{ $fmtRp($invItem->total ?? 0) }}
                                    </p>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $meta['class'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($upcoming->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-100 dark:border-amber-900/40">
                        <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                            <i class="fas fa-clock"></i>Tagihan Mendatang
                        </h3>
                    </div>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($upcoming as $invItem)
                            @php
                                $st = strtolower($invItem->status ?? 'pending');
                                $meta = $statusMap[$st] ?? $statusMap['pending'];
                            @endphp
                            <li class="px-5 py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $invItem->title ?? $invItem->type_label ?? $invItem->type ?? 'Tagihan' }}
                                    </p>
                                    @if(!empty($invItem->due_date))
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                            Jatuh tempo {{ \Carbon\Carbon::parse($invItem->due_date)->translatedFormat('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                        {{ $fmtRp($invItem->total ?? 0) }}
                                    </p>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $meta['class'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ RIWAYAT PEMBAYARAN SAYA ═══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                <i class="fas fa-receipt text-emerald-500 mr-2"></i>Riwayat Pembayaran Saya
            </h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Total {{ $pay['total'] ?? 0 }} transaksi
            </span>
        </div>

        @if($recent->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500">Anda belum memiliki riwayat pembayaran.</p>
                <p class="text-xs text-gray-400 mt-1">Pembayaran akan muncul di sini setelah dikonfirmasi.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($recent as $payItem)
                    @php
                        $st = strtolower($payItem->status ?? 'pending');
                        $meta = $statusMap[$st] ?? ['label' => ucfirst($st), 'class' => 'bg-slate-100 text-slate-700'];
                        $method = strtoupper($payItem->method ?? 'CASH');
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shrink-0">
                            <i class="fas fa-check-circle text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $method }}
                                @if(!empty($payItem->reference))
                                    <span class="text-xs text-gray-400 font-mono ml-1">· {{ Str::limit($payItem->reference, 18) }}</span>
                                @endif
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($payItem->created_at)->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                {{ $fmtRp($payItem->amount ?? 0) }}
                            </p>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $meta['class'] }}">
                                {{ $meta['label'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- CTA --}}
    @if(($inv['outstanding'] ?? 0) > 0)
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-5 shadow-lg text-white">
            <div class="flex items-start sm:items-center gap-4 flex-col sm:flex-row">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold">Ada tagihan yang perlu dibayar</p>
                    <p class="text-sm text-white/80">
                        Total tunggakan {{ $fmtRp($inv['outstanding'] ?? 0) }} — segera selesaikan.
                    </p>
                </div>
                <a href="{{ route('owner.financial.invoices.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-purple-700 font-bold text-sm hover:bg-gray-100 transition shrink-0">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </a>
            </div>
        </div>
    @endif
</div>
@endsection