@extends('layouts.roles.owner')

@section('title', 'Detail Sesi – ' . $session->title)
@section('page-title', 'Detail Sesi Absensi')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .stat-card { transition: transform .2s ease, box-shadow .2s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
    .badge { display: inline-flex; align-items: center; gap: 4px; }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
</style>
@endpush

@section('content')
@php
    // ─── Safe helpers ───────────────────────────────
    $stats       = $session->stats ?? [];
    $hadir       = $stats['hadir']       ?? 0;
    $tepat       = $stats['tepat']       ?? ($stats['tepat_waktu'] ?? 0);
    $terlambat   = $stats['terlambat']   ?? 0;
    $izin        = $stats['izin']        ?? 0;
    $bolos       = $stats['bolos']       ?? 0;
    $belum       = $stats['belum']       ?? 0;
    $targetCount = (int) ($session->target_count ?? 0);

    // ─── Target data (JSON) ─────────────────────────
    $targetData = $session->target_data ?? [];
    $tRoles     = $targetData['roles']       ?? [];
    $tGroups    = $targetData['groups']      ?? [];
    $tDivisions = $targetData['divisions']   ?? [];
    $tGens      = $targetData['generations'] ?? [];

    // ─── Time status ────────────────────────────────
    $timeStatus      = $session->time_status ?? 'unscheduled';
    $timeStatusLabel = $session->time_status_label ?? '-';
@endphp

<div class="p-3 md:p-6 space-y-6 pb-24 lg:pb-6">

    {{-- ═══════════════════════════════════════════ --}}
    {{-- HEADER                                       --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div class="flex-1 min-w-0">
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-1 overflow-x-auto">
                <a href="{{ route('owner.attendance.index') }}" class="hover:text-cyan-600 whitespace-nowrap">
                    <i class="fas fa-arrow-left mr-1"></i> Kehadiran
                </a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap">Detail</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 truncate">
                <i class="fas fa-fingerprint text-cyan-500"></i>
                <span class="truncate">{{ $session->title }}</span>
            </h1>
            @if($session->description)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $session->description }}</p>
            @endif
            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                <span class="badge px-2.5 py-1 rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 font-semibold uppercase">
                    <i class="fas fa-circle text-[6px]"></i>
                    {{ $session->session_status }}
                </span>
                <span class="badge px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-tag"></i>{{ ucfirst($session->attendance_type ?? '-') }}
                </span>
                <span class="badge px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-wifi"></i>{{ ucfirst($session->attendance_mode ?? '-') }}
                </span>
                @if($session->shift_name)
                    <span class="badge px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                        <i class="fas fa-clock"></i>{{ $session->shift_name }}
                    </span>
                @endif
                <span class="badge px-2.5 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                    <i class="fas fa-hourglass-half"></i>{{ $timeStatusLabel }}
                </span>
            </div>
        </div>

        {{-- Action buttons (desktop) --}}
        <div class="hidden md:flex flex-wrap gap-2">
            <a href="{{ route('owner.attendance.monitor', $session) }}"
               class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition shadow-md shadow-purple-500/20">
                <i class="fas fa-broadcast-tower"></i> Live Monitor
            </a>
            @if($session->session_status === 'scheduled')
                <form action="{{ route('owner.attendance.open-now', $session) }}" method="POST" class="inline">
                    @csrf
                    <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition shadow-md shadow-green-500/20">
                        <i class="fas fa-play"></i> Buka Sekarang
                    </button>
                </form>
            @endif
            @if($session->isOpen())
                <form id="closeForm" action="{{ route('owner.attendance.close', $session) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Tutup sesi ini sekarang?')"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition shadow-md shadow-red-500/20">
                        <i class="fas fa-stop"></i> Tutup Sesi
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- FLASH MESSAGES                               --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded-lg flex items-start gap-3">
            <i class="fas fa-check-circle mt-0.5"></i>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded-lg flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- WARNING: TARGET BELUM DI-RESOLVE             --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($targetCount === 0 && $targets->total() === 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 text-amber-800 dark:text-amber-300 p-4 rounded-lg flex items-start gap-3">
            <i class="fas fa-exclamation-triangle mt-0.5"></i>
            <div class="flex-1 text-sm">
                <p class="font-semibold mb-1">Target peserta belum di-resolve</p>
                <p class="text-xs">Sesi ini punya <code class="px-1 bg-amber-100 dark:bg-amber-900/40 rounded">target_data</code> tapi belum ada peserta di <code class="px-1 bg-amber-100 dark:bg-amber-900/40 rounded">attendance_targets</code>. Target akan otomatis di-resolve saat sesi <b>dibuka</b>.</p>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TITIK WAKTU SESI                             --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <i class="fas fa-clock text-cyan-500"></i> Titik Waktu Sesi
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                <p class="text-gray-500 dark:text-gray-400">Jadwal Acara</p>
                <p class="font-bold text-gray-800 dark:text-white mt-1">
                    {{ $session->scheduled_start_at?->format('H:i') ?? '-' }}
                    –
                    {{ $session->scheduled_end_at?->format('H:i') ?? '-' }}
                </p>
                <p class="text-[10px] text-gray-400 mt-0.5">
                    {{ $session->scheduled_start_at?->format('d M Y') ?? '-' }}
                </p>
            </div>
            <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
                <p class="text-blue-600 dark:text-blue-300">Absensi Dibuka</p>
                <p class="font-bold text-gray-800 dark:text-white mt-1">{{ $session->attendance_open_at?->format('H:i:s') ?? '-' }}</p>
            </div>
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-3">
                <p class="text-red-600 dark:text-red-300">Absensi Ditutup</p>
                <p class="font-bold text-gray-800 dark:text-white mt-1">{{ $session->attendance_close_at?->format('H:i:s') ?? '-' }}</p>
            </div>
            <div class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-3">
                <p class="text-purple-600 dark:text-purple-300">Sesi Berakhir</p>
                <p class="font-bold text-gray-800 dark:text-white mt-1">{{ $session->session_end_at?->format('H:i:s') ?? '—' }}</p>
            </div>
            <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 p-3">
                <p class="text-yellow-600 dark:text-yellow-300">Grace Period</p>
                <p class="font-bold text-gray-800 dark:text-white mt-1">{{ $session->grace_period_minutes ?? 0 }} menit</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- INFO TARGET & METODE                         --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Target Roles --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">
                <i class="fas fa-user-tag text-purple-500 mr-1"></i> Target Role
            </p>
            <div class="flex flex-wrap gap-1">
                @forelse($tRoles as $r)
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-medium capitalize">
                        {{ str_replace('_', ' ', $r) }}
                    </span>
                @empty
                    <span class="text-xs text-gray-400 italic">Semua role</span>
                @endforelse
            </div>
        </div>

        {{-- Metode --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">
                <i class="fas fa-fingerprint text-cyan-500 mr-1"></i> Metode Diizinkan
            </p>
            <div class="flex flex-wrap gap-1">
                @forelse($session->allowed_methods ?? [] as $m)
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 font-bold uppercase">
                        {{ $m }}
                    </span>
                @empty
                    <span class="text-xs text-gray-400 italic">Belum ditentukan</span>
                @endforelse
            </div>
        </div>

        {{-- Jadwal & Ruangan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">
                <i class="fas fa-map-marker-alt text-emerald-500 mr-1"></i> Jadwal & Ruangan
            </p>
            <div class="space-y-1 text-xs">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-gray-400 w-3"></i>
                    <span class="text-gray-600 dark:text-gray-300 truncate">{{ $session->schedule?->title ?? 'Tanpa jadwal' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-door-open text-gray-400 w-3"></i>
                    <span class="text-gray-600 dark:text-gray-300 truncate">{{ $session->room?->name ?? 'Tanpa ruangan' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TARGET SUMMARY (Kelompok/Divisi/Generasi)    --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if(count($tGroups) || count($tDivisions) || count($tGens))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fas fa-bullseye text-rose-500"></i> Target Berdasarkan Kelompok / Divisi / Generasi
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                @if(count($tGroups))
                    <div class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-3">
                        <p class="text-purple-600 dark:text-purple-300 font-semibold mb-1">
                            <i class="fas fa-layer-group mr-1"></i> Kelompok ({{ count($tGroups) }})
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 font-mono text-[10px] break-all">
                            {{ implode(', ', $tGroups) }}
                        </p>
                    </div>
                @endif
                @if(count($tDivisions))
                    <div class="rounded-lg bg-pink-50 dark:bg-pink-900/20 p-3">
                        <p class="text-pink-600 dark:text-pink-300 font-semibold mb-1">
                            <i class="fas fa-sitemap mr-1"></i> Divisi ({{ count($tDivisions) }})
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 font-mono text-[10px] break-all">
                            {{ implode(', ', $tDivisions) }}
                        </p>
                    </div>
                @endif
                @if(count($tGens))
                    <div class="rounded-lg bg-indigo-50 dark:bg-indigo-900/20 p-3">
                        <p class="text-indigo-600 dark:text-indigo-300 font-semibold mb-1">
                            <i class="fas fa-stream mr-1"></i> Generasi ({{ count($tGens) }})
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 font-mono text-[10px] break-all">
                            {{ implode(', ', $tGens) }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- STATS CARDS                                  --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="stats-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $statCards = [
                ['label' => 'Hadir',       'value' => $hadir,       'color' => 'green',   'icon' => 'fa-user-check'],
                ['label' => 'Tepat Waktu', 'value' => $tepat,       'color' => 'emerald', 'icon' => 'fa-check'],
                ['label' => 'Terlambat',   'value' => $terlambat,   'color' => 'yellow',  'icon' => 'fa-clock'],
                ['label' => 'Izin',        'value' => $izin,        'color' => 'cyan',    'icon' => 'fa-file-signature'],
                ['label' => 'Bolos',       'value' => $bolos,       'color' => 'red',     'icon' => 'fa-user-slash'],
                ['label' => 'Belum',       'value' => $belum,       'color' => 'gray',    'icon' => 'fa-hourglass-half'],
            ];
        @endphp
        @foreach($statCards as $sc)
            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $sc['label'] }}</p>
                        <p class="text-2xl font-bold text-{{ $sc['color'] }}-600 dark:text-{{ $sc['color'] }}-400 mt-1">{{ $sc['value'] }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-{{ $sc['color'] }}-100 dark:bg-{{ $sc['color'] }}-900/30 flex items-center justify-center text-{{ $sc['color'] }}-600 dark:text-{{ $sc['color'] }}-400 flex-shrink-0">
                        <i class="fas {{ $sc['icon'] }} text-xs"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Progress bar --}}
    @php $pct = $targetCount > 0 ? min(100, round(($hadir / $targetCount) * 100)) : 0; @endphp
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Progres Kehadiran</p>
            <p class="text-sm font-bold text-cyan-600 dark:text-cyan-400">{{ $hadir }} / {{ $targetCount }} ({{ $pct }}%)</p>
        </div>
        <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-cyan-500 to-teal-500 transition-all duration-500"
                 style="width: {{ $pct }}%"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TABEL PESERTA                                --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-users text-cyan-500"></i>
                Daftar Peserta
                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $targets->total() }})</span>
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('owner.attendance.manual.form', $session) }}"
                   class="px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-xs font-medium transition flex items-center gap-1">
                    <i class="fas fa-user-edit"></i> Absensi Manual
                </a>
                <a href="{{ route('owner.attendance.monitor', $session) }}"
                   class="px-3 py-1.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-xs font-medium transition flex items-center gap-1">
                    <i class="fas fa-broadcast-tower"></i> Monitor
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">NIM</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Kelompok</th>
                        <th class="px-4 py-3 text-left">Mode</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                        <th class="px-4 py-3 text-right">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($targets as $target)
                        @php
                            $record = $target->record ?? null;
                            $points = $record->points ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                                {{ $target->snapshot_name ?? $target->user?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                {{ $target->snapshot_nim ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">
                                {{ $target->snapshot_role ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">
                                {{ $target->snapshot_group_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php $mode = $target->target_mode ?? 'undetermined'; @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                    {{ $mode === 'online'  ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $mode === 'offline' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                    {{ $mode === 'undetermined' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                ">
                                    {{ strtoupper($mode) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($record)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                        bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        {{ $record->status_label ?? strtoupper($record->status ?? '-') }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        BELUM ABSEN
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                {{ $record?->recorded_at?->format('H:i:s') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($record)
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase">
                                        {{ $record->method ?? '—' }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold
                                {{ $points > 0 ? 'text-green-600' : '' }}
                                {{ $points < 0 ? 'text-red-600' : '' }}
                                {{ $points === 0 ? 'text-gray-400' : '' }}
                            ">
                                {{ $record ? (($points > 0 ? '+' : '') . $points) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <i class="fas fa-users-slash text-3xl mb-2 block opacity-40"></i>
                                <p class="text-sm">Belum ada peserta target.</p>
                                <p class="text-xs mt-1">Target akan di-resolve otomatis saat sesi dibuka.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($targets->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $targets->links() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- ACTION BAR MOBILE (STICKY)                   --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-t border-gray-200 dark:border-gray-700 shadow-2xl px-3 py-2 flex items-center gap-2">
        <a href="{{ route('owner.attendance.monitor', $session) }}"
           class="flex-1 px-3 py-2.5 rounded-lg bg-purple-500 hover:bg-purple-600 text-white text-xs font-semibold flex items-center justify-center gap-1">
            <i class="fas fa-broadcast-tower"></i> Monitor
        </a>
        @if($session->session_status === 'scheduled')
            <form action="{{ route('owner.attendance.open-now', $session) }}" method="POST" class="flex-1">
                @csrf
                <button class="w-full px-3 py-2.5 rounded-lg bg-green-500 hover:bg-green-600 text-white text-xs font-semibold flex items-center justify-center gap-1">
                    <i class="fas fa-play"></i> Buka
                </button>
            </form>
        @endif
        @if($session->isOpen())
            <form action="{{ route('owner.attendance.close', $session) }}" method="POST" class="flex-1"
                  onsubmit="return confirm('Tutup sesi ini?')">
                @csrf
                <button class="w-full px-3 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-semibold flex items-center justify-center gap-1">
                    <i class="fas fa-stop"></i> Tutup
                </button>
            </form>
        @endif
    </div>
</div>
@endsection