@extends('layouts.roles.ketua')
@section('title', 'Approval Center – UNICODE')
@section('page-title', 'Approval Center')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
            Approval Center
        </p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-check-double text-purple-500 mr-2"></i>Approval Center
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Review & approval semua permintaan yang masuk.
        </p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Menunggu Review',  'value' => 0, 'icon' => 'fa-clock',      'color' => 'amber'],
            ['label' => 'Disetujui',        'value' => 0, 'icon' => 'fa-check',      'color' => 'emerald'],
            ['label' => 'Ditolak',          'value' => 0, 'icon' => 'fa-xmark',      'color' => 'red'],
            ['label' => 'Hari Ini',         'value' => 0, 'icon' => 'fa-calendar-day','color' => 'purple'],
        ] as $s)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">{{ $s['label'] }}</p>
                        <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $s['value'] }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-{{ $s['color'] }}-500/15 text-{{ $s['color'] }}-500
                                flex items-center justify-center">
                        <i class="fas {{ $s['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TAB CATEGORIES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $categories = [
                ['label' => 'Approval Event',      'desc' => 'Persetujuan kegiatan & event',    'icon' => 'fa-calendar-check', 'color' => 'purple', 'route' => 'owner.approvals.index'],
                ['label' => 'Approval Keuangan',   'desc' => 'Persetujuan pengeluaran kas',      'icon' => 'fa-coins',          'color' => 'amber',  'route' => 'owner.financial.invoices.index'],
                ['label' => 'Approval Booking',    'desc' => 'Persetujuan pemesanan ruangan',    'icon' => 'fa-door-open',      'color' => 'cyan',   'route' => 'owner.bookings.index'],
                ['label' => 'Permohonan Izin',     'desc' => 'Persetujuan izin tidak hadir',     'icon' => 'fa-file-signature', 'color' => 'emerald','route' => 'owner.attendance.permissions.index'],
                ['label' => 'Approval Sanksi',     'desc' => 'Persetujuan & review sanksi',      'icon' => 'fa-gavel',          'color' => 'red',    'route' => 'owner.attendance.penalties.index'],
                ['label' => 'Approval Reaktivasi', 'desc' => 'Reaktivasi akun anggota',          'icon' => 'fa-user-check',     'color' => 'indigo', 'route' => 'owner.financial.reactivations.index'],
            ];
        @endphp

        @foreach ($categories as $c)
            <a href="{{ $safeRoute($c['route']) }}"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                      p-5 shadow-sm hover:shadow-md hover:border-purple-400/40 transition group">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-{{ $c['color'] }}-500/15 text-{{ $c['color'] }}-500
                                flex items-center justify-center flex-shrink-0
                                group-hover:scale-110 transition">
                        <i class="fas {{ $c['icon'] }} text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $c['label'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $c['desc'] }}</p>
                    </div>
                    <i class="fas fa-arrow-right text-xs text-gray-400 group-hover:text-purple-500 transition mt-1"></i>
                </div>
            </a>
        @endforeach
    </div>
</div>
</div>
@endsection