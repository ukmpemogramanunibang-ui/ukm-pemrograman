@extends('layouts.roles.pengurus')

@section('title', 'Keuangan Saya – UNICODE')

@section('content')
@php
    $inv = $summary['invoices'] ?? [];
    $pay = $summary['payments'] ?? [];
    $recent = $summary['recent_payments'] ?? collect();
    $overdue = $summary['overdue_invoices'] ?? collect();
    $fmtRp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

    $statusMap = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        'paid'    => ['label' => 'Lunas',    'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'settled' => ['label' => 'Lunas',    'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'success' => ['label' => 'Berhasil', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'failed'  => ['label' => 'Gagal',    'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
        'overdue' => ['label' => 'Terlambat','class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
    ];

    $isBendahara = auth()->user()?->jabatans()
        ?->whereIn('name', ['Bendahara', 'Asisten Bendahara'])->exists() ?? false;
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-blue-500 font-bold mb-1">Keuangan</p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-wallet text-blue-500 mr-2"></i>Uangkas Saya
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ringkasan tagihan dan pembayaran Anda.</p>
    </div>

    {{-- HERO --}}
    <div class="bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 rounded-2xl p-6 shadow-xl text-white">
        <p class="text-[11px] uppercase tracking-wider text-white/70 font-bold">Total Tunggakan</p>
        <p class="text-4xl font-extrabold mt-2">{{ $fmtRp($inv['outstanding'] ?? 0) }}</p>
        <p class="text-xs text-white/70 mt-2">
            Sudah dibayar {{ $fmtRp($pay['total_paid'] ?? 0) }} dari total {{ $fmtRp($inv['total_amount'] ?? 0) }}
        </p>
    </div>

    {{-- STAT --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Total Tagihan', 'value' => $inv['total'] ?? 0,   'icon' => 'fa-file-invoice', 'color' => 'blue'],
            ['label' => 'Lunas',         'value' => $inv['paid'] ?? 0,    'icon' => 'fa-circle-check', 'color' => 'emerald'],
            ['label' => 'Menunggu',      'value' => $inv['pending'] ?? 0, 'icon' => 'fa-hourglass',    'color' => 'amber'],
            ['label' => 'Terlambat',     'value' => $inv['overdue'] ?? 0, 'icon' => 'fa-triangle-exclamation', 'color' => 'rose'],
        ] as $c)
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

    {{-- Akses modul (bendahara) --}}
    @if($isBendahara)
        <div class="rounded-2xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/10 p-5">
            <div class="flex items-start gap-3">
                <i class="fas fa-shield-check text-blue-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-bold text-blue-900 dark:text-blue-200">Akses Bendahara</p>
                    <p class="text-sm text-blue-800/80 dark:text-blue-300/80 mb-3">
                        Anda memiliki akses untuk mengelola keuangan UKM.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('pengurus.bendahara.iuran') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                            <i class="fas fa-coins"></i> Skema Biaya
                        </a>
                        <a href="{{ route('pengurus.bendahara.tagihan') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900 text-xs font-semibold transition">
                            <i class="fas fa-file-invoice"></i> Tagihan
                        </a>
                        <a href="{{ route('pengurus.bendahara.verifikasi') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900 text-xs font-semibold transition">
                            <i class="fas fa-check-double"></i> Verifikasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Terlambat --}}
    @if($overdue->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-rose-200 dark:border-rose-900/40 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-900/40">
                <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>Tagihan Terlambat
                </h3>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                @foreach($overdue as $item)
                    <li class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $item->title ?? $item->type_label ?? 'Tagihan' }}
                            </p>
                            @if(!empty($item->due_date))
                                <p class="text-[11px] text-rose-600 dark:text-rose-400">
                                    Jatuh tempo {{ \Carbon\Carbon::parse($item->due_date)->translatedFormat('d M Y') }}
                                </p>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums shrink-0">
                            {{ $fmtRp($item->total ?? 0) }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Riwayat Pembayaran --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                <i class="fas fa-receipt text-emerald-500 mr-2"></i>Riwayat Pembayaran
            </h3>
        </div>

        @if($recent->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500">Belum ada pembayaran.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($recent as $p)
                    @php
                        $st = strtolower($p->status ?? 'pending');
                        $meta = $statusMap[$st] ?? ['label' => ucfirst($st), 'class' => 'bg-slate-100 text-slate-700'];
                    @endphp
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ strtoupper($p->method ?? 'CASH') }}
                                @if(!empty($p->reference))
                                    <span class="text-xs text-gray-400 font-mono ml-1">· {{ Str::limit($p->reference, 18) }}</span>
                                @endif
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                {{ $fmtRp($p->amount ?? 0) }}
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
</div>
@endsection