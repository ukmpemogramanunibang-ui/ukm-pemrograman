@extends('layouts.roles.owner')

@section('title', 'Detail Booking – ' . $booking->title)

@push('styles')
<style>
    /* ===== RESET & BASE ===== */
    .detail-page { font-family: 'Inter', system-ui, sans-serif; }

    /* ===== GLASSMORPHISM ===== */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 20px 60px -12px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.22,1,0.36,1);
    }
    .glass-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 40px 80px -16px rgba(0,0,0,0.12);
    }
    .dark .glass-card {
        background: rgba(30,41,59,0.7);
        border-color: rgba(255,255,255,0.06);
        box-shadow: 0 20px 60px -12px rgba(0,0,0,0.3);
    }

    /* ===== HEADER GRADIENT ===== */
    .header-gradient {
        background: linear-gradient(135deg, rgba(6,182,212,0.08) 0%, rgba(59,130,246,0.05) 50%, rgba(139,92,246,0.08) 100%);
        border-bottom: 1px solid rgba(6,182,212,0.08);
    }
    .dark .header-gradient {
        background: linear-gradient(135deg, rgba(6,182,212,0.06) 0%, rgba(59,130,246,0.04) 50%, rgba(139,92,246,0.06) 100%);
    }

    /* ===== STAT BADGE ===== */
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .dark .stat-badge {
        background: rgba(30,41,59,0.6);
        border-color: rgba(255,255,255,0.06);
    }

    /* ===== INFO ITEM ===== */
    .info-item {
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .info-item:hover {
        background: rgba(255,255,255,0.4);
        border-color: rgba(6,182,212,0.12);
    }
    .dark .info-item:hover {
        background: rgba(255,255,255,0.04);
        border-color: rgba(6,182,212,0.08);
    }
    .info-item .label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .dark .info-item .label { color: #64748b; }
    .info-item .value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #0f172a;
    }
    .dark .info-item .value { color: #f1f5f9; }

    /* ===== CAROUSEL ===== */
    .carousel-container {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 200px;
        overflow: hidden;
        background: #f3f4f6;
        border-radius: 16px;
    }
    .carousel-slides {
        display: flex;
        transition: transform 0.4s ease-in-out;
        height: 100%;
    }
    .carousel-slide {
        min-width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
    }
    .carousel-slide img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        cursor: pointer;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .carousel-btn:hover { background: rgba(0,0,0,0.7); }
    .carousel-btn.prev { left: 8px; }
    .carousel-btn.next { right: 8px; }
    .carousel-dots {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 10;
    }
    .carousel-dots span {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background 0.2s;
    }
    .carousel-dots span.active { background: white; }

    /* ===== ANIMATIONS ===== */
    .fade-in-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s cubic-bezier(0.22,1,0.36,1) forwards;
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="detail-page p-4 md:p-8 lg:p-10 max-w-full mx-auto space-y-8">

    @php
        $user = Auth::user();
        $canApprove = in_array($user->level_id, [1, 2, 3]);
        $images = isset($booking->room->images) && is_array($booking->room->images)
            ? $booking->room->images
            : [];
        $totalImages = count($images);
    @endphp

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 fade-in-up">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-500 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <a href="{{ route('owner.bookings.index') }}" class="hover:text-cyan-500 transition">Booking</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[180px]">{{ $booking->title }}</span>
            </nav>
            <div class="flex items-center gap-4 flex-wrap">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $booking->title }}
                </h1>
                <span class="stat-badge
                    @if($booking->status == 'Menunggu Persetujuan') bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-700/30
                    @elseif($booking->status == 'Disetujui') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700/30
                    @elseif($booking->status == 'Ditolak') bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700/30
                    @elseif($booking->status == 'Selesai') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-700/30
                    @elseif($booking->status == 'Konflik') bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:border-orange-700/30
                    @else bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-700/30 dark:text-gray-300 dark:border-gray-600 @endif
                ">
                    <span class="w-2 h-2 rounded-full inline-block
                        @if($booking->status == 'Menunggu Persetujuan') bg-yellow-500
                        @elseif($booking->status == 'Disetujui') bg-emerald-500
                        @elseif($booking->status == 'Ditolak') bg-red-500
                        @elseif($booking->status == 'Selesai') bg-blue-500
                        @elseif($booking->status == 'Konflik') bg-orange-500
                        @else bg-gray-400 @endif
                    "></span>
                    {{ $booking->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-4 flex-wrap">
                <span><i class="far fa-calendar-alt mr-1.5 text-cyan-400"></i> Dibuat {{ $booking->created_at->format('d M Y, H:i') }}</span>
                <span><i class="far fa-user mr-1.5 text-cyan-400"></i> Pemesan: {{ $booking->requester_name }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
            <a href="{{ route('owner.bookings.index') }}"
               class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- ===== STATS ROW ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 fade-in-up">
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Durasi</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $booking->start_datetime->diffInHours($booking->end_datetime) }} jam
                </p>
            </div>
        </div>
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Peserta</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $booking->participant_count ?? 0 }}</p>
            </div>
        </div>
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="fas fa-calendar-check text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Mulai</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $booking->start_datetime->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Status</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $booking->status }}</p>
            </div>
        </div>
    </div>

    {{-- ===== MAIN GRID ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: 2/3 --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- INFORMASI BOOKING --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Booking</h2>
                    <span class="ml-auto text-xs text-gray-400 font-medium bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">Detail</span>
                </div>
                <div class="p-7 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                    <div class="info-item">
                        <div class="label">Judul</div>
                        <div class="value">{{ $booking->title }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Ruangan</div>
                        <div class="value">{{ $booking->room->name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Pemesan</div>
                        <div class="value">{{ $booking->requester_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Telepon</div>
                        <div class="value">{{ $booking->requester_phone ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Mulai</div>
                        <div class="value">{{ $booking->start_datetime->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Selesai</div>
                        <div class="value">{{ $booking->end_datetime->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Peserta</div>
                        <div class="value">{{ $booking->participant_count ?? 0 }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Prioritas</div>
                        <div class="value">{{ $booking->priority ?? 'Biasa' }}</div>
                    </div>
                    <div class="info-item sm:col-span-2">
                        <div class="label">Tujuan / Deskripsi</div>
                        <div class="value">{{ $booking->purpose ?? $booking->description ?? '-' }}</div>
                    </div>

                    {{-- APPROVER & WAKTU --}}
                    @if($booking->approved_by)
                    <div class="info-item sm:col-span-2">
                        <div class="label">Disetujui Oleh</div>
                        <div class="value">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                {{ $booking->approver ? $booking->approver->name : 'User tidak ditemukan' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item sm:col-span-2">
                        <div class="label">Waktu Disetujui</div>
                        <div class="value">{{ $booking->approved_at ? $booking->approved_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    @endif

                    @if($booking->checkin_at)
                    <div class="info-item">
                        <div class="label">Check-in</div>
                        <div class="value">{{ $booking->checkin_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                    @if($booking->checkout_at)
                    <div class="info-item">
                        <div class="label">Check-out</div>
                        <div class="value">{{ $booking->checkout_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- KONFLIK --}}
            @if($booking->conflicts->isNotEmpty())
            <div class="glass-card rounded-3xl overflow-hidden border-l-4 border-red-500">
                <div class="header-gradient px-7 py-5 flex items-center gap-3 bg-red-50 dark:bg-red-900/10">
                    <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h2 class="text-lg font-bold text-red-700 dark:text-red-300">⚠️ Konflik Ditemukan</h2>
                </div>
                <div class="p-7">
                    <ul class="list-disc pl-5 space-y-1 text-sm text-red-700 dark:text-red-300">
                        @foreach($booking->conflicts as $conflict)
                            @php
                                $details = is_array($conflict->details) ? $conflict->details : json_decode($conflict->details, true);
                            @endphp
                            @if(is_array($details))
                                @foreach($details as $msg)
                                    <li>{{ $msg }}</li>
                                @endforeach
                            @else
                                <li>{{ $conflict->details ?? 'Konflik tidak diketahui' }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- TOMBOL AKSI --}}
            <div class="flex flex-wrap gap-3">
                @if(in_array($booking->status, ['Menunggu Persetujuan', 'Konflik']))
                    @if($canApprove)
                        <form action="{{ route('owner.bookings.approve', $booking) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-green-500/20 hover:shadow-green-500/30 flex items-center gap-2 text-sm">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                        </form>
                        <form action="{{ route('owner.bookings.reject', $booking) }}" method="POST" class="inline">
                            @csrf
                            <div class="flex items-center gap-2">
                                <input type="text" name="reason" placeholder="Alasan ditolak" class="border rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 dark:border-gray-600">
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-red-500/20 flex items-center gap-2 text-sm">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>
                        </form>
                        @if($booking->status == 'Konflik')
                            <form action="{{ route('owner.bookings.override', $booking) }}" method="POST" class="inline">
                                @csrf
                                <div class="flex items-center gap-2">
                                    <input type="text" name="override_reason" placeholder="Alasan override" class="border rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 dark:border-gray-600" required>
                                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-orange-500/20 flex items-center gap-2 text-sm">
                                        <i class="fas fa-arrow-right"></i> Override
                                    </button>
                                </div>
                            </form>
                        @endif
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-lock mr-1"></i> Hanya Owner, Superadmin, Ketua
                        </span>
                    @endif
                @endif

                @if($booking->status == 'Disetujui' && !$booking->checkin_at)
                    <form action="{{ route('owner.bookings.checkin', $booking) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-blue-500/20 flex items-center gap-2 text-sm">
                            <i class="fas fa-sign-in-alt"></i> Check-in
                        </button>
                    </form>
                @endif

                @if($booking->status == 'Disetujui' && $booking->checkin_at && !$booking->checkout_at)
                    <form action="{{ route('owner.bookings.checkout', $booking) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-purple-500/20 flex items-center gap-2 text-sm">
                            <i class="fas fa-sign-out-alt"></i> Check-out
                        </button>
                    </form>
                @endif

                @if($canApprove)
                    <button onclick="document.getElementById('editTimeForm').classList.toggle('hidden')" class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-white font-semibold rounded-2xl transition shadow-lg shadow-yellow-500/20 flex items-center gap-2 text-sm">
                        <i class="fas fa-clock"></i> Ubah Waktu
                    </button>
                    <button onclick="document.getElementById('moveRoomForm').classList.toggle('hidden')" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-2xl transition shadow-lg shadow-indigo-500/20 flex items-center gap-2 text-sm">
                        <i class="fas fa-exchange-alt"></i> Pindah Ruangan
                    </button>
                @endif
            </div>

            {{-- Form Edit Waktu --}}
            <div id="editTimeForm" class="hidden glass-card p-6 rounded-3xl">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">⏰ Ubah Waktu Booking</h3>
                <form action="{{ route('owner.bookings.update-time', $booking) }}" method="POST" class="flex flex-wrap gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mulai</label>
                        <input type="datetime-local" name="start_datetime" value="{{ $booking->start_datetime->format('Y-m-d\TH:i') }}" required class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selesai</label>
                        <input type="datetime-local" name="end_datetime" value="{{ $booking->end_datetime->format('Y-m-d\TH:i') }}" required class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl transition shadow-md shadow-blue-500/20 flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>

            {{-- Form Pindah Ruangan --}}
            <div id="moveRoomForm" class="hidden glass-card p-6 rounded-3xl">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">📦 Pindah ke Ruangan Lain</h3>
                <form action="{{ route('owner.bookings.move-room', $booking) }}" method="POST" class="flex flex-wrap gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ruangan Baru</label>
                        <select name="room_id" required class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 min-w-[200px]">
                            <option value="">Pilih Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl transition shadow-md shadow-indigo-500/20 flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i> Pindahkan
                    </button>
                </form>
            </div>

        </div>

        {{-- RIGHT: 1/3 --}}
        <div class="space-y-6">

            {{-- GALERI FOTO RUANGAN --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 text-white flex items-center justify-center shadow-md shadow-pink-500/20">
                            <i class="fas fa-images"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Foto Ruangan</h2>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                        {{ $totalImages }} foto
                    </span>
                </div>
                <div class="p-5">
                    @if($totalImages > 0)
                        <div class="carousel-container" id="roomCarousel">
                            <div class="carousel-slides" id="carouselSlides">
                                @foreach($images as $img)
                                    <div class="carousel-slide">
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $booking->room->name }}" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                            @if($totalImages > 1)
                                <button class="carousel-btn prev" id="carouselPrev"><i class="fas fa-chevron-left"></i></button>
                                <button class="carousel-btn next" id="carouselNext"><i class="fas fa-chevron-right"></i></button>
                                <div class="carousel-dots" id="carouselDots">
                                    @for($i = 0; $i < $totalImages; $i++)
                                        <span class="{{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></span>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                            <i class="fas fa-image text-5xl opacity-30 mb-3"></i>
                            <span class="text-sm">Tidak ada foto ruangan</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FASILITAS RUANGAN --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                            <i class="fas fa-couch"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Fasilitas Ruangan</h2>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                        {{ isset($booking->room->facilities) ? $booking->room->facilities->count() : 0 }}
                    </span>
                </div>
                <div class="p-5">
                    @if(isset($booking->room->facilities) && $booking->room->facilities->count())
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking->room->facilities as $facility)
                                <span class="px-3 py-1.5 bg-gray-100/80 dark:bg-gray-700/50 rounded-xl text-xs font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    {{ $facility->name }} @if($facility->quantity > 1) ×{{ $facility->quantity }} @endif
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Tidak ada fasilitas tercatat.</p>
                    @endif
                </div>
            </div>

            {{-- LINK DETAIL RUANGAN --}}
            <div class="glass-card rounded-3xl overflow-hidden p-5 text-center">
                <a href="{{ route('owner.rooms.show', $booking->room_id) }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium flex items-center justify-center gap-2">
                    <i class="fas fa-door-open"></i> Lihat Detail Ruangan
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

        </div>
    </div>

</div>

{{-- ===== CAROUSEL JAVASCRIPT ===== --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slidesContainer = document.getElementById('carouselSlides');
        const prevBtn = document.getElementById('carouselPrev');
        const nextBtn = document.getElementById('carouselNext');
        const dots = document.querySelectorAll('#carouselDots span');
        let currentIndex = 0;
        const totalSlides = {{ $totalImages ?? 0 }};

        if (totalSlides <= 1) return;

        function updateCarousel(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;
            const offset = -currentIndex * 100;
            slidesContainer.style.transform = 'translateX(' + offset + '%)';
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIndex);
            });
        }

        prevBtn.addEventListener('click', (e) => { e.stopPropagation(); updateCarousel(currentIndex - 1); });
        nextBtn.addEventListener('click', (e) => { e.stopPropagation(); updateCarousel(currentIndex + 1); });
        dots.forEach((dot, i) => {
            dot.addEventListener('click', function() {
                updateCarousel(parseInt(this.dataset.index));
            });
        });

        let interval = setInterval(() => { updateCarousel(currentIndex + 1); }, 5000);
        const container = document.getElementById('roomCarousel');
        container.addEventListener('mouseenter', () => clearInterval(interval));
        container.addEventListener('mouseleave', () => {
            interval = setInterval(() => { updateCarousel(currentIndex + 1); }, 5000);
        });
    });
</script>
@endpush
@endsection