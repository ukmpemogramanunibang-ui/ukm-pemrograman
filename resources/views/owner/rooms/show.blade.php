@extends('layouts.roles.owner')

@section('title', 'Detail Ruangan – ' . $room->name)

@push('styles')
<style>
    /* ===== RESET & BASE ===== */
    .detail-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* ===== GLASSMORPHISM ===== */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.08), 0 4px 20px -4px rgba(0, 0, 0, 0.02);
        transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .glass-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 40px 80px -16px rgba(0, 0, 0, 0.12), 0 8px 32px -8px rgba(0, 0, 0, 0.04);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .dark .glass-card {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.06);
        box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.3);
    }
    .dark .glass-card:hover {
        border-color: rgba(255, 255, 255, 0.12);
        box-shadow: 0 40px 80px -16px rgba(0, 0, 0, 0.4);
    }

    /* ===== HEADER GRADIENT ===== */
    .header-gradient {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.08) 0%, rgba(59, 130, 246, 0.05) 50%, rgba(139, 92, 246, 0.08) 100%);
        border-bottom: 1px solid rgba(6, 182, 212, 0.08);
    }
    .dark .header-gradient {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.06) 0%, rgba(59, 130, 246, 0.04) 50%, rgba(139, 92, 246, 0.06) 100%);
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
        letter-spacing: 0.02em;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .dark .stat-badge {
        background: rgba(30, 41, 59, 0.6);
        border-color: rgba(255, 255, 255, 0.06);
    }

    /* ===== INFO ITEM ===== */
    .info-item {
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .info-item:hover {
        background: rgba(255, 255, 255, 0.4);
        border-color: rgba(6, 182, 212, 0.12);
    }
    .dark .info-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(6, 182, 212, 0.08);
    }
    .info-item .label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .dark .info-item .label {
        color: #64748b;
    }
    .info-item .value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #0f172a;
    }
    .dark .info-item .value {
        color: #f1f5f9;
    }

    /* ===== QR CODE ===== */
    .qr-container {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 48px -12px rgba(0, 0, 0, 0.08);
        transition: all 0.4s;
        position: relative;
        overflow: hidden;
    }
    .qr-container::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 30%, rgba(6, 182, 212, 0.04), transparent 70%);
        pointer-events: none;
    }
    .qr-container:hover {
        box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.12);
        transform: scale(1.01);
    }
    .dark .qr-container {
        background: #1e293b;
        box-shadow: 0 12px 48px -12px rgba(0, 0, 0, 0.3);
    }
    .qr-container img {
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    /* ===== DOWNLOAD BTN ===== */
    .download-btn {
        padding: 8px 18px;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        border: 1px solid transparent;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 4px 16px -4px rgba(59, 130, 246, 0.2);
    }
    .download-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 32px -6px rgba(59, 130, 246, 0.3);
    }
    .download-btn-outline {
        background: transparent;
        border-color: rgba(59, 130, 246, 0.3);
        color: #3b82f6;
        box-shadow: none;
    }
    .dark .download-btn-outline {
        border-color: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }
    .download-btn-outline:hover {
        background: rgba(59, 130, 246, 0.06);
        border-color: #3b82f6;
    }

    /* ===== GALLERY ===== */
    .gallery-item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        border: 2px solid transparent;
    }
    .gallery-item:hover {
        transform: scale(1.02);
        border-color: #3b82f6;
        box-shadow: 0 12px 40px -8px rgba(59, 130, 246, 0.15);
    }
    .gallery-item img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.6s;
    }
    .gallery-item:hover img {
        transform: scale(1.05);
    }
    .gallery-item .overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(0deg, rgba(0,0,0,0.15) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        align-items: flex-end;
        padding: 16px;
    }
    .gallery-item:hover .overlay {
        opacity: 1;
    }
    .gallery-item .overlay span {
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(4px);
        padding: 4px 12px;
        border-radius: 9999px;
    }

    /* ===== SCHEDULE CARD ===== */
    .schedule-item {
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        background: rgba(255,255,255,0.4);
        backdrop-filter: blur(4px);
    }
    .dark .schedule-item {
        background: rgba(30, 41, 59, 0.3);
        border-color: rgba(255,255,255,0.04);
    }
    .schedule-item:hover {
        border-color: rgba(6, 182, 212, 0.15);
        box-shadow: 0 8px 24px -8px rgba(0, 0, 0, 0.04);
    }

    /* ===== ANIMATIONS ===== */
    .fade-in-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up:nth-child(1) { animation-delay: 0.04s; }
    .fade-in-up:nth-child(2) { animation-delay: 0.08s; }
    .fade-in-up:nth-child(3) { animation-delay: 0.12s; }
    .fade-in-up:nth-child(4) { animation-delay: 0.16s; }
    .fade-in-up:nth-child(5) { animation-delay: 0.20s; }
    .fade-in-up:nth-child(6) { animation-delay: 0.24s; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 640px) {
        .gallery-item img {
            height: 130px;
        }
        .qr-container {
            padding: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="detail-page p-4 md:p-8 lg:p-10 max-w-full px-4 md:px-10 xl:px-16 mx-auto space-y-8">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 fade-in-up">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-500 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <a href="{{ route('owner.rooms.index') }}" class="hover:text-cyan-500 transition">Ruangan</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[180px]">{{ $room->name }}</span>
            </nav>
            <div class="flex items-center gap-4 flex-wrap">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $room->name }}
                </h1>
                <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 px-4 py-1.5 rounded-full border border-gray-200 dark:border-gray-600">
                    {{ $room->room_code }}
                </span>
                <span class="stat-badge
                    @switch($room->status)
                        @case('Tersedia') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700/30 @break
                        @case('Sedang Digunakan') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-700/30 @break
                        @case('Dipesan') bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-700/30 @break
                        @case('Menunggu Approval') bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/20 dark:text-purple-300 dark:border-purple-700/30 @break
                        @case('Maintenance') bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:border-orange-700/30 @break
                        @default bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-700/30 dark:text-gray-300 dark:border-gray-600
                    @endswitch
                ">
                    <span class="w-2 h-2 rounded-full inline-block
                        @switch($room->status)
                            @case('Tersedia') bg-emerald-500 @break
                            @case('Sedang Digunakan') bg-blue-500 @break
                            @case('Dipesan') bg-yellow-500 @break
                            @case('Menunggu Approval') bg-purple-500 @break
                            @case('Maintenance') bg-orange-500 @break
                            @default bg-gray-400 @break
                        @endswitch
                    "></span>
                    {{ $room->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-4 flex-wrap">
                <span><i class="far fa-calendar-alt mr-1.5 text-cyan-400"></i> Dibuat {{ $room->created_at->format('d M Y, H:i') }}</span>
                @if($room->updated_at->diffInMinutes($room->created_at) > 1)
                    <span><i class="far fa-clock mr-1.5 text-cyan-400"></i> Diperbarui {{ $room->updated_at->diffForHumans() }}</span>
                @endif
                <span><i class="far fa-user mr-1.5 text-cyan-400"></i> Penanggung Jawab: {{ $room->responsible_person ?? 'Belum ditentukan' }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
            <a href="{{ route('owner.rooms.edit', $room) }}"
               class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-white font-semibold rounded-2xl transition shadow-lg shadow-yellow-500/20 hover:shadow-yellow-500/30 flex items-center gap-2 text-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
            <a href="{{ route('owner.rooms.index') }}"
               class="px-6 py-3 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-2xl transition border border-gray-200 dark:border-gray-600 shadow-sm flex items-center gap-2 text-sm backdrop-blur-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div class="glass-card p-5 border-l-4 border-green-500 text-green-700 dark:text-green-300 flex items-center gap-4 fade-in-up">
            <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="flex-1">{!! session('success') !!}</div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="glass-card p-5 border-l-4 border-red-500 text-red-700 dark:text-red-300 flex items-center gap-4 fade-in-up">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 flex-shrink-0">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div class="flex-1">{{ session('error') }}</div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- STATS ROW --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 fade-in-up">
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Kapasitas</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $room->capacity_normal }} <span class="text-sm font-normal text-gray-500">orang</span></p>
                @if($room->capacity_max && $room->capacity_max > $room->capacity_normal)
                    <p class="text-[10px] text-gray-400">max {{ $room->capacity_max }}</p>
                @endif
            </div>
        </div>
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Kondisi</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full
                            {{ $room->condition == 'Baik' ? 'bg-emerald-500' : '' }}
                            {{ $room->condition == 'Perlu Perbaikan' ? 'bg-yellow-500' : '' }}
                            {{ $room->condition == 'Rusak' ? 'bg-red-500' : '' }}
                        "></span>
                        {{ $room->condition }}
                    </span>
                </p>
            </div>
        </div>
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Jam Operasional</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    @if($room->operational_hours && is_array($room->operational_hours))
                        {{ count($room->operational_hours) }} hari
                    @else
                        24 Jam
                    @endif
                </p>
            </div>
        </div>
        <div class="glass-card p-5 flex items-center gap-4 rounded-2xl">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">
                <i class="fas fa-qrcode text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">QR Code</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    @if($room->has_qr && $room->qr_code)
                        <span class="text-emerald-500">Aktif</span>
                    @else
                        <span class="text-gray-400">Belum</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MAIN GRID --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: 2/3 --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- INFORMASI LENGKAP --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Lengkap</h2>
                    <span class="ml-auto text-xs text-gray-400 font-medium bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">Detail</span>
                </div>
                <div class="p-7 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                    <div class="info-item">
                        <div class="label">Nama Ruangan</div>
                        <div class="value">{{ $room->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Kategori</div>
                        <div class="value">{{ $room->category ?? '-' }}</div>
                    </div>
                    <div class="info-item sm:col-span-2">
                        <div class="label">Deskripsi</div>
                        <div class="value">{{ $room->description ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Gedung</div>
                        <div class="value">{{ $room->building ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Lantai</div>
                        <div class="value">{{ $room->floor ?? '-' }}</div>
                    </div>
                    <div class="info-item sm:col-span-2">
                        <div class="label">Alamat</div>
                        <div class="value">{{ $room->address ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Penanggung Jawab</div>
                        <div class="value">{{ $room->responsible_person ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Telepon</div>
                        <div class="value">{{ $room->responsible_phone ?? '-' }}</div>
                    </div>
                    <div class="info-item sm:col-span-2">
                        <div class="label">Jam Operasional</div>
                        @if($room->operational_hours && is_array($room->operational_hours))
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                @foreach($room->operational_hours as $day => $range)
                                    <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700/50 rounded-xl text-xs font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        {{ ucfirst($day) }}: {{ $range }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">24 Jam</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FASILITAS --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                            <i class="fas fa-couch"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Fasilitas</h2>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">{{ $room->facilities->count() }} item</span>
                </div>
                <div class="p-7">
                    @if($room->facilities->count())
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($room->facilities as $facility)
                                <span class="px-5 py-2.5 bg-gray-100/80 dark:bg-gray-700/50 rounded-2xl text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2.5 border border-gray-200 dark:border-gray-600 shadow-sm">
                                    <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                                    {{ $facility->name }}
                                    @if($facility->quantity > 1)
                                        <span class="text-xs text-gray-400 dark:text-gray-500 bg-white/50 dark:bg-gray-800/30 px-2 py-0.5 rounded-full">×{{ $facility->quantity }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-6">Belum ada fasilitas tercatat.</p>
                    @endif
                </div>
            </div>

            {{-- GALERI --}}
            @if($room->images && count($room->images))
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 text-white flex items-center justify-center shadow-md shadow-pink-500/20">
                            <i class="fas fa-images"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Galeri</h2>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">{{ count($room->images) }} foto</span>
                </div>
                <div class="p-7">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($room->images as $image)
                            <div class="gallery-item" onclick="window.open('{{ asset('storage/' . $image) }}', '_blank')">
                                <img src="{{ asset('storage/' . $image) }}" alt="Gambar ruangan" loading="lazy">
                                <div class="overlay">
                                    <span><i class="fas fa-expand mr-1.5"></i> Lihat</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- DOKUMEN --}}
            @if($room->documents && count($room->documents))
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-red-500/20">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Dokumen</h2>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">{{ count($room->documents) }} file</span>
                </div>
                <div class="p-7">
                    <ul class="space-y-3">
                        @foreach($room->documents as $doc)
                            <li class="flex items-center justify-between p-4 bg-gray-50/80 dark:bg-gray-700/30 rounded-2xl border border-gray-200 dark:border-gray-600 transition hover:shadow-md">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ basename($doc) }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $doc) }}" target="_blank"
                                   class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-semibold rounded-xl transition shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 flex items-center gap-2">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: 1/3 --}}
        <div class="space-y-6">

            {{-- QR CODE --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-500 text-white flex items-center justify-center shadow-md shadow-teal-500/20">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">QR Code</h2>
                    </div>
                    @if($qrValid ?? false)
                        <span class="text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-700/30 font-medium">Aktif</span>
                    @endif
                </div>
                <div class="p-7 flex flex-col items-center">
                    @php
    $qrValid = false;
    try {
        $qrService = app(\App\Services\QrCodeService::class);
        if (method_exists($qrService, 'isQrValid')) {
            $qrValid = $qrService->isQrValid($room);
        } else {
            // Fallback: cek langsung apakah file QR ada
            $qrValid = $room->has_qr && $room->qr_code && Storage::disk('public')->exists($room->qr_code);
        }
    } catch (\Exception $e) {
        $qrValid = $room->has_qr && $room->qr_code && Storage::disk('public')->exists($room->qr_code);
    }
@endphp

                    @if($qrValid)
                        <div class="qr-container w-full max-w-[200px] mx-auto">
                            <img src="{{ asset('storage/' . $room->qr_code) }}"
                                 alt="QR Code"
                                 class="w-full aspect-square object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <p class="text-center text-xs text-red-500 hidden">Gagal memuat QR</p>
                        </div>
                        <div class="mt-6 flex flex-wrap gap-2 justify-center">
                            @php $formats = ['png', 'jpg', 'webp', 'pdf']; @endphp
                            @foreach($formats as $fmt)
                                <a href="{{ route('owner.rooms.download-qr', ['room' => $room->id, 'format' => $fmt]) }}"
                                   class="download-btn {{ $loop->index > 0 ? 'download-btn-outline' : '' }} text-sm">
                                    <i class="fas fa-download text-xs mr-1"></i> {{ strtoupper($fmt) }}
                                </a>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-400 mt-4">Klik format untuk download</p>
                    @else
                        <div class="w-44 h-44 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <i class="fas fa-qrcode text-8xl opacity-30"></i>
                        </div>
                        <form action="{{ route('owner.rooms.generate-qr', $room) }}" method="POST" class="mt-6 w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white font-semibold rounded-2xl transition shadow-lg shadow-teal-500/20 hover:shadow-teal-500/30 flex items-center justify-center gap-2 text-sm">
                                <i class="fas fa-qrcode"></i> Generate QR Code
                            </button>
                        </form>
                        <p class="text-[10px] text-gray-400 mt-3">Klik tombol untuk membuat QR</p>
                    @endif
                </div>
            </div>

            {{-- JADWAL --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Jadwal</h2>
                    </div>
                    <a href="{{ route('owner.rooms.schedules', $room) }}" class="text-xs font-medium text-cyan-500 hover:text-cyan-600 dark:hover:text-cyan-400 hover:underline flex items-center gap-1">
                        Lihat Semua <i class="fas fa-arrow-right ml-0.5"></i>
                    </a>
                </div>
                <div class="p-5 max-h-[440px] overflow-y-auto space-y-4">
                    @if($room->schedules->count())
                        @foreach($room->schedules->take(6) as $schedule)
                            @php
                                $person = '';
                                $semester = '';
                                $academicYear = '';
                                $notes = '';
                                $activityType = $schedule->activity_type ?? 'Umum';
                                $hari = \Carbon\Carbon::parse($schedule->start_datetime)->isoFormat('dddd');
                                $tanggal = \Carbon\Carbon::parse($schedule->start_datetime)->format('d M Y');
                                $waktuMulai = \Carbon\Carbon::parse($schedule->start_datetime)->format('H:i');
                                $waktuSelesai = \Carbon\Carbon::parse($schedule->end_datetime)->format('H:i');
                                $desc = $schedule->description ?? '';
                                if (preg_match('/Pengguna:\s*([^\n]+)/', $desc, $match)) { $person = trim($match[1]); }
                                if (preg_match('/Semester:\s*([^\n]+)/', $desc, $match)) { $semester = trim($match[1]); }
                                if (preg_match('/Tahun Akademik:\s*([^\n]+)/', $desc, $match)) { $academicYear = trim($match[1]); }
                                if (preg_match('/Catatan:\s*([^\n]+)/', $desc, $match)) { $notes = trim($match[1]); }
                            @endphp
                            <div class="schedule-item p-4 rounded-2xl">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center flex-shrink-0 text-lg font-bold shadow-md shadow-blue-500/20">
                                        {{ \Carbon\Carbon::parse($schedule->start_datetime)->format('d') }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $schedule->title }}</p>
                                        <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-calendar-day text-blue-400 text-[10px]"></i> {{ $hari }}, {{ $tanggal }}</span>
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-clock text-cyan-400 text-[10px]"></i> {{ $waktuMulai }} – {{ $waktuSelesai }}</span>
                                            @if($activityType && $activityType != 'Umum')
                                                <span class="inline-flex items-center gap-1"><i class="fas fa-tag text-purple-400 text-[10px]"></i> {{ $activityType }}</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            @if($person)
                                                <span class="inline-flex items-center gap-1"><i class="fas fa-user text-green-500 text-[10px]"></i> {{ $person }}</span>
                                            @endif
                                            @if($academicYear)
                                                <span class="inline-flex items-center gap-1"><i class="fas fa-graduation-cap text-indigo-500 text-[10px]"></i> {{ $academicYear }}</span>
                                            @endif
                                            @if($semester)
                                                <span class="inline-flex items-center gap-1"><i class="fas fa-layer-group text-amber-500 text-[10px]"></i> {{ $semester }}</span>
                                            @endif
                                        </div>
                                        @if($notes)
                                            <p class="text-xs text-gray-400 mt-1 italic">{{ $notes }}</p>
                                        @endif
                                        @if($schedule->status)
                                            <span class="mt-2 inline-block px-3 py-1 rounded-full text-[10px] font-medium
                                                {{ $schedule->status == 'Aktif' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                                {{ $schedule->status == 'Selesai' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                                {{ $schedule->status == 'Dibatalkan' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                            ">
                                                {{ $schedule->status }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($schedule->images)
                                        @php
                                            $images = is_array($schedule->images) ? $schedule->images : json_decode($schedule->images, true);
                                            $firstImage = $images[0] ?? null;
                                        @endphp
                                        @if($firstImage)
                                            <div class="flex-shrink-0 w-16 h-16 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                                                <img src="{{ asset('storage/' . $firstImage) }}" alt="Gambar jadwal" class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @if($room->schedules->count() > 6)
                            <div class="text-center pt-2">
                                <a href="{{ route('owner.rooms.schedules', $room) }}" class="text-xs font-medium text-cyan-500 hover:underline">
                                    + {{ $room->schedules->count() - 6 }} jadwal lainnya
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-400 text-sm text-center py-8">Belum ada jadwal untuk ruangan ini.</p>
                    @endif
                </div>
            </div>

                        {{-- BOOKING TERAKHIR --}}
            <div class="glass-card rounded-3xl overflow-hidden">
                <div class="header-gradient px-7 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/20">
                            <i class="fas fa-book"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Booking Terakhir</h2>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-700/30 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">{{ $room->bookings->count() }}</span>
                </div>
                <div class="p-5 max-h-[220px] overflow-y-auto space-y-3">
                    @if($room->bookings->count())
                        @foreach($room->bookings->take(5) as $booking)
                            @php
                                $start = \Carbon\Carbon::parse($booking->start_datetime);
                                $end = \Carbon\Carbon::parse($booking->end_datetime);
                                $diffDays = $start->diffInDays($end);
                                $diffHours = $start->diffInHours($end) % 24;
                                $totalHours = $start->diffInHours($end);
                                $dayName = $start->isoFormat('dddd');
                                $roleName = $booking->user ? ($booking->user->level->name ?? 'Guest') : 'Guest';
                            @endphp
                            <div class="flex items-start gap-3 p-3 rounded-2xl bg-gray-50/50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 transition hover:shadow-sm">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-sm font-bold
                                    @if($booking->status == 'Disetujui') bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400
                                    @elseif($booking->status == 'Menunggu Persetujuan') bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @else bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 @endif
                                ">
                                    @if($booking->status == 'Disetujui') <i class="fas fa-check"></i>
                                    @elseif($booking->status == 'Menunggu Persetujuan') <i class="fas fa-clock"></i>
                                    @else <i class="fas fa-times"></i> @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-1">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $booking->title ?? 'Booking' }}</p>
                                        <span class="text-xs font-medium
                                            @if($booking->status == 'Disetujui') text-emerald-600
                                            @elseif($booking->status == 'Menunggu Persetujuan') text-yellow-600
                                            @else text-red-600 @endif
                                        ">
                                            {{ $booking->status }}
                                        </span>
                                    </div>

                                    {{-- Hari --}}
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ $dayName }}, {{ $start->format('d M Y') }}
                                        <span class="text-gray-400 mx-1">→</span>
                                        {{ $end->format('d M Y') }}
                                    </p>

                                    {{-- Durasi --}}
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $start->format('H:i') }} – {{ $end->format('H:i') }}
                                        <span class="text-gray-400 mx-1">•</span>
                                        @if($diffDays > 0)
                                            <span class="font-medium">{{ $diffDays }} hari</span>
                                            @if($diffHours > 0) <span class="text-gray-400">+</span> {{ $diffHours }} jam @endif
                                        @else
                                            <span class="font-medium">{{ $totalHours }} jam</span>
                                        @endif
                                    </p>

                                    {{-- Pemesan & Role --}}
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-user mr-1 text-blue-500"></i>
                                            {{ $booking->requester_name }}
                                        </span>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                            {{ $roleName }}
                                        </span>
                                        @if($booking->priority && $booking->priority != 'Biasa')
                                            <span class="text-xs px-2 py-0.5 rounded-full
                                                @if($booking->priority == 'Urgent') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                                @elseif($booking->priority == 'Tinggi') bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300
                                                @else bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 @endif
                                            ">
                                                {{ $booking->priority }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Disetujui Oleh --}}
                                    @if($booking->approved_by)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            <i class="fas fa-check-circle mr-1 text-emerald-500"></i>
                                            Disetujui oleh: <span class="font-medium">{{ $booking->approver->name ?? 'User' }}</span>
                                            @if($booking->approved_at)
                                                <span class="text-gray-400">• {{ $booking->approved_at->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-400 text-sm text-center py-6">Belum ada booking.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection