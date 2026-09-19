@extends('layouts.roles.owner')

@section('title', 'Detail Jadwal – ' . $schedule->title)

@push('styles')
<style>
    .detail-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .detail-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    }
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #94a3b8;
        font-weight: 600;
    }
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }
    .dark .info-value {
        color: #f1f5f9;
    }
    .status-badge {
        padding: 4px 14px;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .fade-in {
        animation: fadeInUp 0.4s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .gallery-image {
        transition: all 0.3s;
        cursor: pointer;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid transparent;
    }
    .gallery-image:hover {
        transform: scale(1.03);
        border-color: #3b82f6;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-6xl mx-auto space-y-6">

    {{-- ===== BREADCRUMB ===== --}}
    <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 fade-in">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Dashboard</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <a href="{{ route('owner.schedules.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Jadwal</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[200px]">{{ $schedule->title }}</span>
    </nav>

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 fade-in">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-calendar-alt text-blue-500 text-2xl"></i>
                {{ $schedule->title }}
                <span class="text-sm font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full font-normal">
                    ID: #{{ $schedule->id }}
                </span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Dibuat {{ $schedule->created_at->format('d M Y, H:i') }}
                @if($schedule->updated_at->diffInMinutes($schedule->created_at) > 1)
                    · Diperbarui {{ $schedule->updated_at->diffForHumans() }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('owner.schedules.edit', $schedule) }}"
               class="px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('owner.schedules.index') }}"
               class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- ===== STATUS CARD ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 fade-in">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl
                    @if($schedule->status == 'Aktif') bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                    @elseif($schedule->status == 'Selesai') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                    @else bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 @endif
                ">
                    @if($schedule->status == 'Aktif') <i class="fas fa-play"></i>
                    @elseif($schedule->status == 'Selesai') <i class="fas fa-check"></i>
                    @else <i class="fas fa-times"></i> @endif
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                    <p class="text-xl font-bold">
                        <span class="status-badge
                            @if($schedule->status == 'Aktif') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                            @elseif($schedule->status == 'Selesai') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                            @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 @endif
                        ">
                            {{ $schedule->status }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <span><i class="fas fa-clock mr-1"></i> Mulai: {{ $schedule->start_datetime->format('d M Y, H:i') }}</span>
                <span><i class="fas fa-clock mr-1"></i> Selesai: {{ $schedule->end_datetime->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- ===== GRID UTAMA ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Informasi Lengkap --}}
        <div class="lg:col-span-2 space-y-6 fade-in">

            {{-- Detail Informasi --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden detail-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Informasi Lengkap</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="info-label">Judul Jadwal</p>
                        <p class="info-value">{{ $schedule->title }}</p>
                    </div>
                    <div>
                        <p class="info-label">Ruangan</p>
                        <p class="info-value">{{ $schedule->room->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $schedule->room->room_code ?? '' }}</p>
                    </div>
                    <div>
                        <p class="info-label">Hari</p>
                        <p class="info-value">{{ $schedule->start_datetime->isoFormat('dddd') }}</p>
                    </div>
                    <div>
                        <p class="info-label">Tanggal</p>
                        <p class="info-value">{{ $schedule->start_datetime->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="info-label">Jam Mulai</p>
                        <p class="info-value">{{ $schedule->start_datetime->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="info-label">Jam Selesai</p>
                        <p class="info-value">{{ $schedule->end_datetime->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="info-label">Jenis Aktivitas</p>
                        <p class="info-value">
                            <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
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
                        <p class="info-value">{{ $schedule->semester ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="info-label">Tahun Akademik</p>
                        <p class="info-value">{{ $schedule->academic_year ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="info-label">Kehadiran</p>
                        <p class="info-value">
                            @if($schedule->requires_attendance)
                                <span class="text-green-600"><i class="fas fa-check-circle"></i> Diperlukan</span>
                            @else
                                <span class="text-gray-400">Tidak</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="info-label">Dibuat oleh</p>
                        <p class="info-value">{{ $schedule->creator->name ?? 'Sistem' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="info-label">Deskripsi</p>
                        <p class="info-value text-sm font-normal text-gray-600 dark:text-gray-300">{{ $schedule->description ?: '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- ===== GALERI GAMBAR (DIPERBAIKI) ===== --}}
            @php
                $images = is_array($schedule->images) ? $schedule->images : json_decode($schedule->images, true);
            @endphp
            @if($images && count($images))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden detail-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-images text-pink-500"></i>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Galeri Gambar</h2>
                        <span class="text-xs bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 px-2 py-0.5 rounded-full">{{ count($images) }} file</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Klik untuk perbesar</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($images as $image)
                            <div class="relative group rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition">
                                <img src="{{ asset('storage/' . $image) }}"
                                     alt="Gambar jadwal {{ $loop->iteration }}"
                                     class="w-full h-40 object-cover transition group-hover:scale-105 duration-300">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <a href="{{ asset('storage/' . $image) }}" target="_blank"
                                       class="bg-white/90 hover:bg-white text-gray-800 p-2 rounded-lg shadow-lg transition hover:scale-110"
                                       title="Lihat gambar">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                    <a href="{{ asset('storage/' . $image) }}" download
                                       class="bg-white/90 hover:bg-white text-gray-800 p-2 rounded-lg shadow-lg transition hover:scale-110"
                                       title="Download gambar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                                <div class="absolute bottom-2 left-2 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded-full">
                                    {{ pathinfo($image, PATHINFO_FILENAME) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Info Tambahan --}}
        <div class="space-y-6 fade-in">

            {{-- Jam Operasional Ruangan --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden detail-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center gap-2">
                    <i class="fas fa-clock text-cyan-500"></i>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Jam Operasional</h2>
                </div>
                <div class="p-4">
                    @php
                        $hours = $schedule->room->operational_hours ?? [];
                    @endphp
                    @if(is_array($hours) && count($hours))
                        <div class="space-y-2">
                            @foreach($hours as $day => $range)
                                <div class="flex justify-between items-center p-2 rounded-lg bg-gray-50 dark:bg-gray-700/30">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($day) }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $range }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">24 Jam</p>
                    @endif
                </div>
            </div>

            {{-- Recurrence Days --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden detail-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center gap-2">
                    <i class="fas fa-sync-alt text-purple-500"></i>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Perulangan</h2>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <span class="font-semibold">Tipe:</span> {{ $schedule->recurrence_type ?? 'Sekali' }}
                    </p>
                    @if($schedule->recurrence_days)
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($schedule->recurrence_days as $day)
                                <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
                                    {{ ucfirst($day) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    @if($schedule->end_date)
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-calendar-alt mr-1"></i> Berakhir: {{ $schedule->end_date->format('d M Y') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Booking Terkait --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden detail-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center gap-2">
                    <i class="fas fa-book text-orange-500"></i>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Booking</h2>
                </div>
                <div class="p-4">
                    @php
                        $booking = $schedule->room->bookings()
                            ->where('status', 'Disetujui')
                            ->whereDate('start_datetime', $schedule->start_datetime->toDateString())
                            ->first();
                    @endphp
                    @if($booking)
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <p class="text-sm font-semibold text-green-700 dark:text-green-300">
                                <i class="fas fa-check-circle mr-1"></i> Ada booking
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $booking->title }} · {{ $booking->start_datetime->format('H:i') }} – {{ $booking->end_datetime->format('H:i') }}
                            </p>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Tidak ada booking terkait.</p>
                    @endif
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-col gap-3">
                <a href="{{ route('owner.schedules.edit', $schedule) }}"
                   class="w-full px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i> Edit Jadwal
                </a>
                <form action="{{ route('owner.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i> Hapus Jadwal
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection