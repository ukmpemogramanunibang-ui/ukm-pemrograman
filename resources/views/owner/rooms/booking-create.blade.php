@extends('layouts.roles.owner')

@section('title', 'Buat Booking – ' . $room->name)

@push('styles')
<style>
    .booking-form-section {
        transition: all 0.2s ease;
    }
    .booking-form-section:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .conflict-warning {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 1rem;
        border-radius: 12px;
        display: none;
    }
    .dark .conflict-warning {
        background: #1f2937;
        border-left-color: #f87171;
    }

    /* Carousel */
    .carousel-container {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 200px;
        overflow: hidden;
        background: #f3f4f6;
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
    .carousel-btn:hover {
        background: rgba(0,0,0,0.7);
    }
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
    .carousel-dots span.active {
        background: white;
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto">

    @php
        $allImages = $allImages ?? [];
        $totalImages = count($allImages);
    @endphp

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-600 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.rooms.private') }}" class="hover:text-cyan-600 transition">Room Private</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Buat Booking</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white flex items-center gap-3 mt-2">
                <i class="fas fa-calendar-plus text-cyan-500"></i>
                Buat Booking – {{ $room->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Isi form di bawah untuk mengajukan booking ruangan ini.
            </p>
        </div>
        <a href="{{ route('owner.rooms.private') }}" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl transition flex items-center gap-2 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- INFO RUANGAN --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-0">
            {{-- Gambar Carousel --}}
            <div class="md:col-span-1 bg-gray-100 dark:bg-gray-900 relative min-h-[200px]">
                <div class="carousel-container" id="roomCarousel">
                    <div class="carousel-slides" id="carouselSlides">
                        @forelse($allImages as $img)
                            <div class="carousel-slide">
                                <img src="{{ asset('storage/' . $img) }}" alt="{{ $room->name }}" loading="lazy">
                            </div>
                        @empty
                            <div class="carousel-slide">
                                <i class="fas fa-door-open text-5xl text-gray-300"></i>
                            </div>
                        @endforelse
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
            </div>

            {{-- Detail --}}
            <div class="md:col-span-3 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Room Private</span>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $room->name }}</h2>
                        <p class="font-mono text-xs text-gray-400">{{ $room->room_code }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold
                        @if($room->status == 'Tersedia') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                        @elseif($room->status == 'Sedang Digunakan') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                        @elseif($room->status == 'Dipesan') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif
                    ">
                        {{ $room->status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Kapasitas</p>
                        <p class="font-bold text-sm">{{ $room->capacity_normal }} org</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Lantai</p>
                        <p class="font-bold text-sm">{{ $room->floor ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Kategori</p>
                        <p class="font-bold text-sm">{{ $room->category ?: '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Kondisi</p>
                        <p class="font-bold text-sm">{{ $room->condition }}</p>
                    </div>
                </div>

                {{-- Hari & Jam Operasional --}}
                <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300">
                    <div>
                        <span class="font-medium">Hari ini:</span>
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Jam Operasional:</span>
                        <span>
                            @if(is_array($room->operational_hours) && count($room->operational_hours))
                                @php
                                    $today = strtolower(\Carbon\Carbon::now()->isoFormat('dddd'));
                                    $hours = $room->operational_hours[$today] ?? 'Tutup';
                                @endphp
                                {{ $hours }}
                            @else
                                24 Jam
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Fasilitas --}}
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @forelse($room->facilities->take(6) as $facility)
                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-300">
                            {{ $facility->name }} @if($facility->quantity > 1) ×{{ $facility->quantity }} @endif
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">Tidak ada fasilitas tercatat.</span>
                    @endforelse
                </div>

                {{-- Operasional per Hari --}}
                <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-3">
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-clock text-cyan-500 mr-1"></i> Jam Operasional
                    </h4>
                    @if(is_array($room->operational_hours) && count($room->operational_hours))
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1 text-xs">
                            @php
                                $dayNames = ['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'];
                            @endphp
                            @foreach($dayNames as $key => $label)
                                <div class="flex justify-between py-1 px-2 bg-gray-50 dark:bg-gray-700/30 rounded">
                                    <span class="text-gray-500 dark:text-gray-400">{{ $label }}</span>
                                    <span class="font-medium text-gray-700 dark:text-gray-200">
                                        {{ $room->operational_hours[$key] ?? 'Tutup' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span class="text-sm text-gray-400">24 Jam</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JADWAL RUANGAN --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-calendar-alt text-purple-500"></i>
                Jadwal Ruangan
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Jadwal yang sudah terdaftar untuk ruangan ini</p>
        </div>

        <div class="p-4 space-y-3 max-h-[400px] overflow-y-auto">
            @forelse($room->schedules as $schedule)
                @php
                    $scheduleImages = is_array($schedule->images) ? $schedule->images : (json_decode($schedule->images, true) ?? []);
                    $firstImage = $scheduleImages[0] ?? null;
                    $days = is_array($schedule->recurrence_days) ? $schedule->recurrence_days : (json_decode($schedule->recurrence_days, true) ?? []);
                    $dayList = [];
                    $dayMap = ['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'];
                    if (is_array($days)) {
                        foreach ($days as $d) {
                            $dayList[] = $dayMap[$d] ?? $d;
                        }
                    }
                    // Cari nama pembuat dari berbagai kemungkinan kolom
                    $creatorName = 'Sistem';
                    if (isset($schedule->created_by) && $schedule->created_by) {
                        $creatorName = $schedule->created_by;
                    } elseif (isset($schedule->user_id) && $schedule->user_id) {
                        if ($schedule->relationLoaded('user') && $schedule->user) {
                            $creatorName = $schedule->user->name;
                        } else {
                            $creatorName = 'User ID ' . $schedule->user_id;
                        }
                    } elseif (isset($schedule->creator) && $schedule->creator) {
                        $creatorName = $schedule->creator;
                    }
                @endphp
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $schedule->title }}" class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600 flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-alt text-2xl text-gray-300"></i>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-semibold text-gray-800 dark:text-white">{{ $schedule->title }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-medium
                                    @if($schedule->status == 'Aktif') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                    @elseif($schedule->status == 'Selesai') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                    @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif
                                ">
                                    {{ $schedule->status ?? 'Aktif' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 mt-1 text-xs text-gray-600 dark:text-gray-300">
                                <div>
                                    <span class="font-medium">Jenis:</span>
                                    {{ $schedule->activity_type ?: 'Umum' }}
                                </div>
                                <div>
                                    <span class="font-medium">Pembuat:</span>
                                    {{ $creatorName }}
                                </div>
                                <div>
                                    <span class="font-medium">Semester:</span>
                                    {{ $schedule->semester ?? '-' }}
                                </div>
                                <div>
                                    <span class="font-medium">Tahun Akademik:</span>
                                    {{ $schedule->academic_year ?? '-' }}
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="font-medium">Waktu:</span>
                                    @if($schedule->recurrence_type && $schedule->recurrence_type != 'Sekali')
                                        {{ implode(', ', $dayList) }} ·
                                        {{ \Carbon\Carbon::parse($schedule->start_datetime)->format('H:i') }} –
                                        {{ \Carbon\Carbon::parse($schedule->end_datetime)->format('H:i') }}
                                        <span class="text-gray-400 text-[10px] ml-1">(berulang {{ $schedule->recurrence_type }})</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($schedule->start_datetime)->format('d/m/Y H:i') }} –
                                        {{ \Carbon\Carbon::parse($schedule->end_datetime)->format('H:i') }}
                                    @endif
                                </div>
                            </div>

                            @if(count($scheduleImages) > 1)
                                <div class="flex gap-1 mt-2 flex-wrap">
                                    @foreach($scheduleImages as $idx => $img)
                                        @if($idx > 0 && $idx < 4)
                                            <img src="{{ asset('storage/' . $img) }}" alt="jadwal" class="w-8 h-8 rounded object-cover border border-gray-200">
                                        @endif
                                    @endforeach
                                    @if(count($scheduleImages) > 4)
                                        <span class="text-[10px] text-gray-400">+{{ count($scheduleImages) - 4 }} lagi</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-500 dark:text-gray-400 text-sm">
                    <i class="fas fa-calendar-times text-3xl block mb-2 opacity-30"></i>
                    Belum ada jadwal untuk ruangan ini.
                </div>
            @endforelse
        </div>
    </div>

    {{-- FORM BOOKING --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-pen text-cyan-500"></i>
                Form Booking
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">* Wajib diisi</p>
        </div>

        <form action="{{ route('owner.rooms.booking.store', $room) }}" method="POST" id="bookingForm" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
                <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 text-red-700 dark:text-red-300 text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Booking <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                           placeholder="Contoh: Rapat Panitia">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pemesan <span class="text-red-500">*</span></label>
                    <input type="text" name="requester_name" value="{{ old('requester_name') }}" required
                           class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                           placeholder="Nama lengkap">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telepon</label>
                    <input type="text" name="requester_phone" value="{{ old('requester_phone') }}"
                           class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Peserta <span class="text-red-500">*</span></label>
                    <input type="number" name="participants" value="{{ old('participants', 1) }}" required min="1"
                           max="{{ $room->capacity_max ?? $room->capacity_normal }}"
                           class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-400 mt-1">Maksimal {{ $room->capacity_max ?? $room->capacity_normal }} orang</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" value="{{ old('start_datetime') }}" required
                           class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" value="{{ old('end_datetime') }}" required
                           class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                </div>
            </div>

            <div id="scheduleCheckContainer" class="hidden">
                <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4">
                    <p class="text-sm font-semibold text-blue-700 dark:text-blue-300 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> Jadwal yang sudah ada pada rentang waktu tersebut:
                    </p>
                    <div id="scheduleCheckResult" class="mt-2 text-sm text-gray-600 dark:text-gray-300"></div>
                </div>
            </div>

            <div id="conflictWarning" class="conflict-warning">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-sm text-red-700 dark:text-red-300">Terjadi Konflik!</p>
                        <p id="conflictDetails" class="text-sm text-red-600 dark:text-red-400"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Anda tetap bisa mengajukan booking, namun harus menunggu persetujuan Owner.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
                <button type="submit" id="submitBtn"
                        class="px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-cyan-500/20 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Ajukan Booking
                </button>
                <button type="reset" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-xl transition">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== CAROUSEL =====
    const slidesContainer = document.getElementById('carouselSlides');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const dots = document.querySelectorAll('#carouselDots span');
    let currentIndex = 0;
    const totalSlides = {{ $totalImages ?? 0 }};

    function updateCarousel(index) {
        if (totalSlides <= 1) return;
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        currentIndex = index;
        const offset = -currentIndex * 100;
        slidesContainer.style.transform = 'translateX(' + offset + '%)';
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
    }

    if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); updateCarousel(currentIndex - 1); });
    if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); updateCarousel(currentIndex + 1); });
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            updateCarousel(parseInt(this.dataset.index));
        });
    });

    // ===== CEK KONFLIK =====
    const startInput = document.getElementById('start_datetime');
    const endInput = document.getElementById('end_datetime');
    const conflictWarning = document.getElementById('conflictWarning');
    const conflictDetails = document.getElementById('conflictDetails');
    const scheduleContainer = document.getElementById('scheduleCheckContainer');
    const scheduleResult = document.getElementById('scheduleCheckResult');

    function checkConflicts() {
        const start = startInput.value;
        const end = endInput.value;
        if (!start || !end) {
            conflictWarning.style.display = 'none';
            scheduleContainer.classList.add('hidden');
            return;
        }

        fetch('{{ route("owner.rooms.check") }}?room_id={{ $room->id }}&start=' + encodeURIComponent(start) + '&end=' + encodeURIComponent(end), {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.conflicts && data.conflicts.length > 0) {
                conflictWarning.style.display = 'block';
                conflictDetails.innerHTML = data.conflicts.map(c => '<div>• ' + c + '</div>').join('');
                if (data.schedules && data.schedules.length) {
                    scheduleContainer.classList.remove('hidden');
                    scheduleResult.innerHTML = data.schedules.map(s =>
                        `<div class="flex justify-between py-1 border-b border-blue-100 dark:border-blue-700/30">
                            <span>${s.title}</span>
                            <span class="font-mono text-xs">${s.start} - ${s.end}</span>
                        </div>`
                    ).join('');
                } else {
                    scheduleContainer.classList.add('hidden');
                }
            } else {
                conflictWarning.style.display = 'none';
                scheduleContainer.classList.add('hidden');
            }
        })
        .catch(err => console.error('Gagal cek konflik:', err));
    }

    startInput.addEventListener('change', checkConflicts);
    endInput.addEventListener('change', checkConflicts);

    if (startInput.value && endInput.value) checkConflicts();

    const form = document.getElementById('bookingForm');
    form.addEventListener('submit', function(e) {
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        if (start >= end) {
            e.preventDefault();
            alert('Waktu mulai harus sebelum waktu selesai!');
            return false;
        }
    });
});
</script>
@endsection