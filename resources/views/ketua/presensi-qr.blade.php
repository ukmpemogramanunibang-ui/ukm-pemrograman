@extends('layouts.roles.ketua')
@section('title', 'Presensi QR – UNICODE')
@section('page-title', 'Presensi QR')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
                QR Attendance
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
                <i class="fas fa-qrcode text-purple-500 mr-2"></i>Presensi QR
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Overview sesi presensi & kehadiran anggota.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $total     = $sessions->total() ?? 0;
            $aktif     = collect($sessions->items())->filter(fn ($s) => ($s->status ?? '') === 'active')->count();
            $today     = collect($sessions->items())->filter(fn ($s) => isset($s->created_at) && \Carbon\Carbon::parse($s->created_at)->isToday())->count();
        @endphp
        @foreach ([
            ['label' => 'Total Sesi',    'value' => $total, 'icon' => 'fa-qrcode',       'color' => 'purple'],
            ['label' => 'Sesi Aktif',    'value' => $aktif, 'icon' => 'fa-play-circle',  'color' => 'emerald'],
            ['label' => 'Sesi Hari Ini', 'value' => $today, 'icon' => 'fa-calendar-day', 'color' => 'amber'],
            ['label' => 'Total Hadir',   'value' => 0,      'icon' => 'fa-user-check',   'color' => 'cyan'],
        ] as $s)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">{{ $s['label'] }}</p>
                        <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($s['value']) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-{{ $s['color'] }}-500/15 text-{{ $s['color'] }}-500
                                flex items-center justify-center">
                        <i class="fas {{ $s['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list text-purple-500"></i> Sesi Presensi Terbaru
            </h3>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($sessions as $session)
                @php
                    $status = $session->status ?? 'closed';
                    $badge = match($status) {
                        'active', 'open' => ['emerald', 'Aktif'],
                        'closed'         => ['gray', 'Selesai'],
                        default          => ['purple', ucfirst($status)],
                    };
                @endphp
                <div class="flex items-center gap-4 px-5 py-4 hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition">
                    <div class="w-11 h-11 rounded-xl bg-purple-500/15 text-purple-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $session->title ?? 'Sesi Presensi' }}
                        </p>
                        <p class="text-[10px] text-gray-500 mt-0.5">
                            <i class="far fa-clock mr-1"></i>
                            {{ isset($session->created_at) ? \Carbon\Carbon::parse($session->created_at)->diffForHumans() : '-' }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                 bg-{{ $badge[0] }}-500/15 text-{{ $badge[0] }}-600 dark:text-{{ $badge[0] }}-400">
                        {{ $badge[1] }}
                    </span>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-qrcode text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada sesi presensi</p>
                    <p class="text-xs text-gray-500 mt-1">Sesi presensi akan muncul di sini</p>
                </div>
            @endforelse
        </div>

        @if (method_exists($sessions, 'hasPages') && $sessions->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
</div>
@endsection