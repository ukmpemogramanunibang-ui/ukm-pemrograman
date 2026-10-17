@extends('layouts.roles.owner')

@section('title', 'Detail Jadwal – ' . $schedule->title)

@php
    // ═══════════════════════════════════════════════════════════
    // PREPARE DATA — null-safe di satu tempat
    // ═══════════════════════════════════════════════════════════
    $room         = $schedule->room;
    $creator      = $schedule->creator;
    $startDT      = $schedule->start_datetime;
    $endDT        = $schedule->end_datetime;
    $endDate      = $schedule->end_date;
    $images       = is_array($schedule->images) ? $schedule->images : [];
    $recurDays    = is_array($schedule->recurrence_days) ? $schedule->recurrence_days : [];

    // Status meta
    $statusMeta = match (true) {
        $schedule->status === 'Aktif'      => ['icon' => 'fa-play', 'tone' => 'emerald', 'label' => 'Aktif'],
        $schedule->status === 'Selesai'    => ['icon' => 'fa-check', 'tone' => 'blue',    'label' => 'Selesai'],
        $schedule->status === 'Dibatalkan' => ['icon' => 'fa-ban',  'tone' => 'rose',    'label' => 'Dibatalkan'],
        default                            => ['icon' => 'fa-circle-info', 'tone' => 'slate', 'label' => $schedule->status ?? '—'],
    };

    // Audience label
    $audienceLabel = method_exists($schedule, 'getAudienceLabelAttribute')
        ? $schedule->audience_label
        : ucfirst(str_replace('_', ' ', $schedule->audience_type ?? 'all'));

    // Attendance label
    $attendanceLabel = method_exists($schedule, 'getAttendanceModeLabelAttribute')
        ? $schedule->attendance_mode_label
        : ucfirst($schedule->attendance_mode ?? 'none');

    // Attendance icon
    $attendanceIcon = match ($schedule->attendance_mode ?? 'none') {
        'digital' => 'fa-qrcode',
        'upload'  => 'fa-file-upload',
        default   => 'fa-minus-circle',
    };

    // Booking terkait — null safe
    $booking = null;
    if ($room && $startDT) {
        try {
            $booking = $room->bookings()
                ->where('status', 'Disetujui')
                ->whereDate('start_datetime', $startDT->toDateString())
                ->first();
        } catch (\Throwable $e) {
            $booking = null;
        }
    }

    // Operational hours
    $operationalHours = $room->operational_hours ?? [];
    if (is_string($operationalHours)) {
        $operationalHours = json_decode($operationalHours, true) ?: [];
    }

    // Duration
    $durationMinutes = ($startDT && $endDT) ? $startDT->diffInMinutes($endDT) : 0;
    $durationLabel   = $durationMinutes > 0
        ? ($durationMinutes >= 60
            ? floor($durationMinutes / 60) . ' jam ' . ($durationMinutes % 60 > 0 ? ($durationMinutes % 60) . ' mnt' : '')
            : $durationMinutes . ' menit')
        : '—';
@endphp

@push('styles')
<style>
    .detail-card {
        transition: transform .3s cubic-bezier(.4,0,.2,1),
                    box-shadow .3s cubic-bezier(.4,0,.2,1);
    }
    .detail-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 60px rgba(0,0,0,.08);
    }
    .info-label {
        font-size: .65rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: .25rem;
    }
    .info-value {
        font-size: .9rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.4;
    }
    .dark .info-value { color: #f1f5f9; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        padding: 4px 14px;
        border-radius: 9999px;
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .fade-in {
        animation: fadeInUp .4s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .gallery-img {
        transition: transform .3s ease;
    }
    .gallery-img:hover { transform: scale(1.05); }

    /* Safe area for mobile */
    .safe-bottom { padding-bottom: max(env(safe-area-inset-bottom), 0); }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-6xl mx-auto space-y-6">

    {{-- ═══════════════════════════════════════════════════════════
         BREADCRUMB
    ═══════════════════════════════════════════════════════════ --}}
    <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 flex-wrap fade-in">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
        <a href="{{ route('owner.schedules.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
            Jadwal
        </a>
        <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
        <span class="text-gray-700 dark:text-gray-300 font-semibold truncate max-w-[200px]">
            {{ $schedule->title }}
        </span>
    </nav>

    {{-- ═══════════════════════════════════════════════════════════
         HERO — Gradient header
    ═══════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-600
                text-white shadow-xl fade-in">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1
                                     rounded-full bg-white/20 backdrop-blur-sm
                                     border border-white/30 text-[10px] font-black tracking-wider">
                            <i class="fas fa-calendar-alt"></i>
                            JADWAL #{{ $schedule->id }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1
                                     rounded-full bg-white/20 backdrop-blur-sm
                                     border border-white/30 text-[10px] font-black tracking-wider">
                            <i class="fas {{ $statusMeta['icon'] }}"></i>
                            {{ strtoupper($statusMeta['label']) }}
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black leading-tight break-words">
                        {{ $schedule->title }}
                    </h1>

                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2
                                text-xs md:text-sm text-white/85">
                        @if($room)
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-door-open"></i>
                                {{ $room->name }}
                            </span>
                        @endif
                        @if($startDT)
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-clock"></i>
                                {{ $startDT->isoFormat('dddd, D MMM Y · HH:mm') }} WIB
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-hourglass-half"></i>
                            {{ $durationLabel }}
                        </span>
                    </div>

                    <p class="mt-3 text-[11px] text-white/70">
                        Dibuat {{ optional($schedule->created_at)->format('d M Y, H:i') ?? '—' }}
                        @if($schedule->updated_at && $schedule->created_at
                            && $schedule->updated_at->diffInMinutes($schedule->created_at) > 1)
                            · Diperbarui {{ $schedule->updated_at->diffForHumans() }}
                        @endif
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    <a href="{{ route('owner.schedules.edit', $schedule) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              bg-white/20 hover:bg-white/30 backdrop-blur-sm
                              border border-white/30
                              text-white text-xs font-black transition">
                        <i class="fas fa-edit"></i>
                        <span class="hidden sm:inline">Edit</span>
                    </a>
                    <a href="{{ route('owner.schedules.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              bg-white text-blue-700 hover:bg-blue-50
                              text-xs font-black transition shadow-sm">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         FLASH MESSAGES
    ═══════════════════════════════════════════════════════════ --}}
    @if(session('success'))
        <div role="alert"
             class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20
                    border border-emerald-200 dark:border-emerald-700/40
                    text-emerald-700 dark:text-emerald-300 text-sm
                    flex items-start gap-2.5 fade-in">
            <i class="fas fa-circle-check mt-0.5 shrink-0"></i>
            <div class="min-w-0">
                <p class="font-black">Berhasil</p>
                <p class="mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div role="alert"
             class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20
                    border border-rose-200 dark:border-rose-700/40
                    text-rose-700 dark:text-rose-300 text-sm
                    flex items-start gap-2.5 fade-in">
            <i class="fas fa-circle-exclamation mt-0.5 shrink-0"></i>
            <div class="min-w-0">
                <p class="font-black">Gagal</p>
                <p class="mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         INTEGRATION CARDS — Target & Absensi
    ═══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 fade-in">

        {{-- Target Penerima --}}
        <div class="p-5 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20
                    border border-indigo-200 dark:border-indigo-700
                    detail-card">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-11 h-11 rounded-xl bg-indigo-500 text-white
                            flex items-center justify-center shrink-0
                            shadow-md shadow-indigo-500/20">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-wider
                              text-indigo-600 dark:text-indigo-400">
                        Target Penerima
                    </p>
                    <p class="font-black text-gray-900 dark:text-white mt-0.5">
                        {{ $audienceLabel }}
                    </p>
                </div>
            </div>

            <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400">
                {{ number_format($schedule->audience_count ?? 0) }}
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">penerima</span>
            </p>

            @if($schedule->attendance_session_id)
                <a href="{{ route('owner.attendance.show', $schedule->attendance_session_id) }}"
                   class="mt-4 inline-flex items-center gap-2 px-3 py-2 rounded-lg
                          bg-indigo-500 hover:bg-indigo-600 text-white
                          text-[11px] font-black transition">
                    <i class="fas fa-arrow-right"></i>
                    Buka Sesi Absensi
                </a>
            @endif
        </div>

        {{-- Mode Absensi --}}
        <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20
                    border border-emerald-200 dark:border-emerald-700
                    detail-card">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white
                            flex items-center justify-center shrink-0
                            shadow-md shadow-emerald-500/20">
                    <i class="fas {{ $attendanceIcon }}"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-wider
                              text-emerald-600 dark:text-emerald-400">
                        Mode Absensi
                    </p>
                    <p class="font-black text-gray-900 dark:text-white mt-0.5">
                        {{ $attendanceLabel }}
                    </p>
                </div>
            </div>

            @if(($schedule->attendance_mode ?? 'none') !== 'none')
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                 text-[10px] font-black
                                 bg-emerald-100 dark:bg-emerald-900/40
                                 text-emerald-700 dark:text-emerald-300">
                        <i class="fas fa-check-circle"></i>
                        {{ $schedule->auto_create_attendance ? 'AUTO-CREATE' : 'MANUAL' }}
                    </span>
                    @if($schedule->requires_review)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                     text-[10px] font-black
                                     bg-amber-100 dark:bg-amber-900/40
                                     text-amber-700 dark:text-amber-300">
                            <i class="fas fa-shield-halved"></i>
                            PERLU REVIEW
                        </span>
                    @endif
                </div>
            @else
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Jadwal ini tidak memerlukan absensi.
                </p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MAIN GRID — 2 kolom
    ═══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ═════════════════════════════════════════════════
             KIRI — Informasi utama (2 kolom)
        ═════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- INFO LENGKAP --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        overflow-hidden detail-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700
                            bg-gradient-to-r from-blue-50 to-indigo-50
                            dark:from-blue-900/10 dark:to-indigo-900/10
                            flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <h2 class="font-black text-gray-800 dark:text-white">Informasi Lengkap</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="info-label">Judul Jadwal</p>
                        <p class="info-value">{{ $schedule->title }}</p>
                    </div>

                    <div>
                        <p class="info-label">Ruangan</p>
                        <p class="info-value">{{ $room->name ?? '—' }}</p>
                        @if($room?->room_code)
                            <p class="text-[11px] text-gray-400 font-mono mt-0.5">
                                {{ $room->room_code }}
                            </p>
                        @endif
                    </div>

                    <div>
                        <p class="info-label">Hari</p>
                        <p class="info-value">
                            {{ optional($startDT)->isoFormat('dddd') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="info-label">Tanggal</p>
                        <p class="info-value">
                            {{ optional($startDT)->format('d M Y') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="info-label">Jam Mulai</p>
                        <p class="info-value">
                            <i class="fas fa-clock text-blue-500 mr-1"></i>
                            {{ optional($startDT)->format('H:i') ?? '—' }} WIB
                        </p>
                    </div>

                    <div>
                        <p class="info-label">Jam Selesai</p>
                        <p class="info-value">
                            <i class="fas fa-clock text-rose-500 mr-1"></i>
                            {{ optional($endDT)->format('H:i') ?? '—' }} WIB
                        </p>
                    </div>

                    <div>
                        <p class="info-label">Durasi</p>
                        <p class="info-value">
                            <i class="fas fa-hourglass-half text-amber-500 mr-1"></i>
                            {{ $durationLabel }}
                        </p>
                    </div>

                    <div>
                        <p class="info-label">Jenis Aktivitas</p>
                        <p class="info-value">
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20
                                         text-blue-700 dark:text-blue-300
                                         rounded-full text-[11px] font-bold">
                                {{ $schedule->activity_type ?: 'Umum' }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="info-label">Tipe Jadwal</p>
                        <p class="info-value">{{ $schedule->recurrence_type ?? 'Sekali' }}</p>
                    </div>

                    <div>
                        <p class="info-label">Semester</p>
                        <p class="info-value">{{ $schedule->semester ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="info-label">Tahun Akademik</p>
                        <p class="info-value">{{ $schedule->academic_year ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="info-label">Dibuat oleh</p>
                        <p class="info-value">{{ $creator->name ?? 'Sistem' }}</p>
                    </div>

                    @if($schedule->description)
                        <div class="md:col-span-2">
                            <p class="info-label">Deskripsi</p>
                            <p class="info-value text-sm font-normal text-gray-600 dark:text-gray-300
                                      whitespace-pre-line leading-relaxed">
                                {{ $schedule->description }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- GALERI GAMBAR --}}
            @if(count($images) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700
                            overflow-hidden detail-card">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700
                                bg-gradient-to-r from-pink-50 to-fuchsia-50
                                dark:from-pink-900/10 dark:to-fuchsia-900/10
                                flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-images text-pink-500"></i>
                            <h2 class="font-black text-gray-800 dark:text-white">Galeri Gambar</h2>
                            <span class="text-[10px] bg-pink-100 dark:bg-pink-900/30
                                         text-pink-700 dark:text-pink-300
                                         px-2 py-0.5 rounded-full font-black">
                                {{ count($images) }} FILE
                            </span>
                        </div>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                            <i class="fas fa-hand-pointer"></i> Klik untuk perbesar
                        </span>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($images as $image)
                                @php $imageUrl = asset('storage/' . ltrim($image, '/')); @endphp
                                <div class="relative group rounded-xl overflow-hidden
                                            border border-gray-200 dark:border-gray-700
                                            shadow-sm hover:shadow-md transition-all">
                                    <img src="{{ $imageUrl }}"
                                         alt="Gambar {{ $loop->iteration }}"
                                         loading="lazy"
                                         class="w-full h-40 object-cover gallery-img">

                                    <div class="absolute inset-0 bg-black/50 opacity-0
                                                group-hover:opacity-100 transition-opacity
                                                flex items-center justify-center gap-2">
                                        <a href="{{ $imageUrl }}" target="_blank" rel="noopener"
                                           class="w-9 h-9 rounded-lg bg-white/90 hover:bg-white
                                                  text-gray-800 flex items-center justify-center
                                                  shadow-lg transition hover:scale-110"
                                           title="Lihat gambar">
                                            <i class="fas fa-expand"></i>
                                        </a>
                                        <a href="{{ $imageUrl }}" download
                                           class="w-9 h-9 rounded-lg bg-white/90 hover:bg-white
                                                  text-gray-800 flex items-center justify-center
                                                  shadow-lg transition hover:scale-110"
                                           title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>

                                    <div class="absolute bottom-2 left-2 px-2 py-0.5
                                                bg-black/70 backdrop-blur-sm text-white
                                                text-[9px] font-bold rounded-full
                                                max-w-[calc(100%-1rem)] truncate">
                                        #{{ $loop->iteration }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
        {{-- ═════════════════════════════════════════════════
             AKHIR KOLOM KIRI
        ═════════════════════════════════════════════════ --}}

        {{-- ═════════════════════════════════════════════════
             KANAN — Info tambahan (1 kolom)
        ═════════════════════════════════════════════════ --}}
        <div class="space-y-6">

            {{-- JAM OPERASIONAL --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        overflow-hidden detail-card">
                <div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                            bg-gradient-to-r from-cyan-50 to-blue-50
                            dark:from-cyan-900/10 dark:to-blue-900/10
                            flex items-center gap-2">
                    <i class="fas fa-clock text-cyan-500"></i>
                    <h2 class="font-black text-sm text-gray-800 dark:text-white">Jam Operasional</h2>
                </div>

                <div class="p-4">
                    @if(is_array($operationalHours) && count($operationalHours) > 0)
                        @php
                            $dayNames = [
                                'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
                                'thursday' => 'Kamis', 'friday' => 'Jumat',
                                'saturday' => 'Sabtu', 'sunday' => 'Minggu',
                            ];
                        @endphp
                        <div class="space-y-2">
                            @foreach($dayNames as $key => $label)
                                @php
                                    $val = $operationalHours[$key] ?? null;
                                    $range = null;
                                    if (is_array($val)) {
                                        $range = ($val['start'] ?? $val['open'] ?? null)
                                            ? ($val['start'] ?? $val['open']) . ' – ' . ($val['end'] ?? $val['close'] ?? '')
                                            : null;
                                    } elseif (is_string($val)) {
                                        $range = $val;
                                    }
                                @endphp
                                <div class="flex justify-between items-center p-2 rounded-lg
                                            bg-gray-50 dark:bg-gray-700/30">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $label }}
                                    </span>
                                    @if($range)
                                        <span class="text-xs font-mono text-gray-600 dark:text-gray-400">
                                            {{ $range }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-black
                                                     text-rose-500 dark:text-rose-400">
                                            TUTUP
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 text-center py-4">
                            <i class="fas fa-infinity"></i> Operasional 24 Jam
                        </p>
                    @endif
                </div>
            </div>

            {{-- PERULANGAN --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        overflow-hidden detail-card">
                <div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                            bg-gradient-to-r from-purple-50 to-violet-50
                            dark:from-purple-900/10 dark:to-violet-900/10
                            flex items-center gap-2">
                    <i class="fas fa-sync-alt text-purple-500"></i>
                    <h2 class="font-black text-sm text-gray-800 dark:text-white">Perulangan</h2>
                </div>

                <div class="p-4 space-y-3">
                    <div>
                        <p class="info-label">Tipe</p>
                        <p class="info-value">{{ $schedule->recurrence_type ?? 'Sekali' }}</p>
                    </div>

                    @if(count($recurDays) > 0)
                        <div>
                            <p class="info-label">Hari</p>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                @foreach($recurDays as $day)
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20
                                                 text-blue-700 dark:text-blue-300
                                                 rounded-full text-[10px] font-black">
                                        {{ ucfirst($day) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($endDate)
                        <div>
                            <p class="info-label">Berakhir</p>
                            <p class="info-value">
                                <i class="fas fa-calendar-alt text-amber-500 mr-1"></i>
                                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- BOOKING TERKAIT --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        overflow-hidden detail-card">
                <div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                            bg-gradient-to-r from-orange-50 to-amber-50
                            dark:from-orange-900/10 dark:to-amber-900/10
                            flex items-center gap-2">
                    <i class="fas fa-book text-orange-500"></i>
                    <h2 class="font-black text-sm text-gray-800 dark:text-white">Booking Terkait</h2>
                </div>

                <div class="p-4">
                    @if($booking)
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20
                                    rounded-xl border border-emerald-200 dark:border-emerald-800">
                            <p class="text-xs font-black text-emerald-700 dark:text-emerald-300">
                                <i class="fas fa-check-circle mr-1"></i> Ada booking
                            </p>
                            <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">
                                {{ $booking->title }} ·
                                {{ optional($booking->start_datetime)->format('H:i') }} –
                                {{ optional($booking->end_datetime)->format('H:i') }}
                            </p>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 text-center py-4">
                            <i class="fas fa-inbox mr-1"></i> Tidak ada booking terkait.
                        </p>
                    @endif
                </div>
            </div>

            {{-- AKSI CEPAT --}}
            <div class="space-y-2">
                <a href="{{ route('owner.schedules.edit', $schedule) }}"
                   class="w-full inline-flex items-center justify-center gap-2
                          px-5 py-3 rounded-xl
                          bg-amber-500 hover:bg-amber-600 text-white
                          text-sm font-black transition shadow-sm">
                    <i class="fas fa-edit"></i> Edit Jadwal
                </a>

                <form action="{{ route('owner.schedules.destroy', $schedule) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus jadwal \'{{ addslashes($schedule->title) }}\'?\nTindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                                   px-5 py-3 rounded-xl
                                   bg-rose-500 hover:bg-rose-600 text-white
                                   text-sm font-black transition shadow-sm">
                        <i class="fas fa-trash"></i> Hapus Jadwal
                    </button>
                </form>
            </div>

        </div>
        {{-- ═════════════════════════════════════════════════
             AKHIR KOLOM KANAN
        ═════════════════════════════════════════════════ --}}

    </div>
    {{-- ═══════════════════════════════════════════════════════════
         AKHIR MAIN GRID
    ═══════════════════════════════════════════════════════════ --}}

    {{-- Footer info --}}
    <div class="text-center pt-2 safe-bottom">
        <p class="text-[10px] text-gray-400 dark:text-gray-500
                  flex items-center justify-center gap-1.5">
            <i class="fas fa-info-circle"></i>
            Semua waktu ditampilkan dalam zona WIB (Asia/Jakarta)
        </p>
    </div>
</div>
@endsection