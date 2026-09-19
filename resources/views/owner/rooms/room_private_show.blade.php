@extends('layouts.roles.owner')

@section('title', 'Detail Room – ' . $room->name)

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

        {{-- Back button --}}
        <a href="{{ route('owner.rooms.private') }}" class="inline-flex items-center gap-2 mb-6 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Room
        </a>

        {{-- Main Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-xl">

            {{-- Top Section: Image + Info --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-0">

                {{-- Image Carousel --}}
                <div class="lg:col-span-2 bg-gray-100 dark:bg-gray-900 relative overflow-hidden" style="min-height: 320px;">
                    @php
                        $images = is_array($room->images) ? $room->images : [];
                        $totalImages = count($images);
                    @endphp

                    {{-- Carousel wrapper --}}
                    <div class="relative w-full h-full cursor-pointer" id="carouselWrapper">
                        <div id="roomCarousel" class="relative w-full h-full">
                            {{-- Slides container --}}
                            <div class="relative w-full h-full overflow-hidden">
                                <div id="carouselSlides" class="flex transition-transform duration-500 ease-in-out h-full">
                                    @forelse($images as $index => $image)
                                        <div class="min-w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-800" data-index="{{ $index }}">
                                            <img src="{{ asset('storage/' . $image) }}" 
                                                 alt="{{ $room->name }} - Gambar {{ $index+1 }}" 
                                                 class="w-full h-full object-contain select-none">
                                        </div>
                                    @empty
                                        <div class="min-w-full h-full flex items-center justify-center">
                                            <i class="fas fa-door-open text-7xl text-gray-300"></i>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Tombol navigasi --}}
                            @if($totalImages > 1)
                                <button id="prevSlide" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 text-gray-800 dark:text-white p-2 rounded-full shadow-lg transition z-10">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="nextSlide" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 text-gray-800 dark:text-white p-2 rounded-full shadow-lg transition z-10">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif

                            {{-- Indikator (dots) --}}
                            @if($totalImages > 1)
                                <div id="carouselIndicators" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                                    @for($i = 0; $i < $totalImages; $i++)
                                        <button class="dot w-2.5 h-2.5 rounded-full bg-white/50 hover:bg-white transition {{ $i === 0 ? 'bg-white' : '' }}" data-index="{{ $i }}"></button>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Overlay status dan badge --}}
                    <div class="absolute top-4 left-4 z-20">
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold shadow-lg
                            @if($liveStatus == 'Tersedia') bg-emerald-500 text-white
                            @elseif($liveStatus == 'Sedang Digunakan') bg-blue-600 text-white
                            @elseif($liveStatus == 'Dipesan') bg-yellow-500 text-white
                            @else bg-gray-600 text-white @endif
                        ">
                            {{ $liveStatus }}
                        </span>
                    </div>
                    <div class="absolute top-4 right-4 z-20">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-white/90 dark:bg-gray-800/90 text-cyan-600 dark:text-cyan-400 shadow-lg backdrop-blur-sm">
                            <i class="fas fa-lock mr-1"></i> Private
                        </span>
                    </div>
                </div>

                {{-- Info --}}
                <div class="lg:col-span-3 p-6 md:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs text-cyan-600 dark:text-cyan-400 font-bold uppercase tracking-wider">Room Private</span>
                            <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mt-1">{{ $room->name }}</h1>
                            <p class="font-mono text-xs text-gray-400 mt-1">{{ $room->room_code }}</p>
                        </div>
                    </div>

                    {{-- Info Grid 4 columns --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
                        <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-4">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Kapasitas</p>
                            <p class="text-lg font-black text-gray-800 dark:text-white">{{ $room->capacity_normal }} <span class="text-xs font-normal">org</span></p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-4">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Lantai</p>
                            <p class="text-lg font-black text-gray-800 dark:text-white">{{ $room->floor ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-4">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Kategori</p>
                            <p class="text-lg font-black text-gray-800 dark:text-white">{{ $room->category ?: '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-4">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Kondisi</p>
                            <p class="text-lg font-black text-gray-800 dark:text-white">{{ $room->condition }}</p>
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="mt-6">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-list-check text-cyan-500 text-sm"></i>
                            <h2 class="font-bold text-sm text-gray-700 dark:text-gray-200">Fasilitas</h2>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @forelse($room->facilities as $facility)
                                <span class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-300">
                                    {{ $facility->name }} @if($facility->quantity > 1) ×{{ $facility->quantity }} @endif
                                </span>
                            @empty
                                <span class="text-xs text-gray-400">Tidak ada fasilitas tercatat.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Section: Operational Hours, Schedules, Booking --}}
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 md:p-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Operational Hours --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-clock text-cyan-500 text-sm"></i>
                        <h2 class="font-bold text-sm text-gray-700 dark:text-gray-200">Jam Operasional</h2>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/30 p-4 space-y-2">
                        @if(is_array($room->operational_hours) && count($room->operational_hours))
                            @foreach($room->operational_hours as $day => $hour)
                                <div class="flex justify-between gap-4 text-sm border-b border-gray-200 dark:border-gray-600 last:border-0 py-1.5">
                                    <span class="text-gray-600 dark:text-gray-300">{{ ucfirst($day) }}</span>
                                    <strong class="text-gray-800 dark:text-white">{{ $hour }}</strong>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-400">24 Jam</p>
                        @endif
                    </div>
                </div>

                {{-- Center: Schedules --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-purple-500 text-sm"></i>
                            <h2 class="font-bold text-sm text-gray-700 dark:text-gray-200">Jadwal Ruangan</h2>
                        </div>
                        <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $room->schedules->count() }}</span>
                    </div>
                    <div class="space-y-2 max-h-[280px] overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
                        @forelse($room->schedules as $schedule)
                            @php
                                $scheduleImages = is_array($schedule->images) ? $schedule->images : json_decode($schedule->images, true);
                                $firstImage = $scheduleImages[0] ?? null;
                                $days = is_array($schedule->recurrence_days) ? $schedule->recurrence_days : [];
                                $dayNames = ['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'];
                                $dayList = array_map(fn($d) => $dayNames[$d] ?? $d, $days);
                                $desc = $schedule->description ?? '';
                                $person = '';
                                if (preg_match('/Pengguna:\s*([^\n]+)/', $desc, $match)) $person = trim($match[1]);
                            @endphp
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition bg-white dark:bg-gray-800/50">
                                <div class="flex items-start gap-3">
                                    @if($firstImage)
                                        <img src="{{ asset('storage/' . $firstImage) }}" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0 border border-gray-200 dark:border-gray-600">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-1">
                                            <h4 class="font-semibold text-sm text-gray-800 dark:text-white truncate">{{ $schedule->title }}</h4>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-medium whitespace-nowrap
                                                @if($schedule->status == 'Aktif') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                                @elseif($schedule->status == 'Selesai') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                                @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 @endif
                                            ">
                                                {{ $schedule->status ?? 'Aktif' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-1 text-xs text-gray-600 dark:text-gray-300">
                                            <span><i class="fas fa-clock text-cyan-500 w-3.5"></i> {{ $schedule->start_datetime->format('H:i') }} – {{ $schedule->end_datetime->format('H:i') }}</span>
                                            @if($schedule->activity_type)
                                                <span><i class="fas fa-tag text-purple-500 w-3.5"></i> {{ $schedule->activity_type }}</span>
                                            @endif
                                            <span><i class="fas fa-calendar-day text-blue-500 w-3.5"></i> {{ !empty($dayList) ? implode(', ', $dayList) : $schedule->start_datetime->isoFormat('dddd') }}</span>
                                        </div>
                                        @if($person)
                                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-user text-green-500 w-3.5"></i> {{ $person }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-700/30 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-calendar-times text-xl mb-1 block"></i>
                                Belum ada jadwal.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Right: Booking --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-check text-cyan-500 text-sm"></i>
                            <h2 class="font-bold text-sm text-gray-700 dark:text-gray-200">Booking</h2>
                        </div>
                        <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $room->bookings->count() }}</span>
                    </div>
                    <div class="space-y-2 max-h-[280px] overflow-y-auto pr-1">
                        @forelse($room->bookings as $booking)
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition bg-white dark:bg-gray-800/50">
                                <div class="flex justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800 dark:text-white">{{ $booking->title }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $booking->requester_name }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $booking->start_datetime->format('d/m/Y') }}</p>
                                        <p class="text-xs text-cyan-600 dark:text-cyan-400">{{ $booking->start_datetime->format('H:i') }} – {{ $booking->end_datetime->format('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-4 text-center text-sm text-emerald-700 dark:text-emerald-300">
                                <i class="fas fa-check-circle mr-1"></i> Tidak ada booking
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Dokumen Terlampir --}}
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 md:px-8 py-6">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-file-alt text-indigo-500 text-sm"></i>
                    <h2 class="font-bold text-sm text-gray-700 dark:text-gray-200">Dokumen Terlampir</h2>
                    <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ count($room->documents ?? []) }}</span>
                </div>
                @php
                    $documents = is_array($room->documents) ? $room->documents : [];
                @endphp
                @if(count($documents) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($documents as $doc)
                            @php
                                $extension = pathinfo($doc, PATHINFO_EXTENSION);
                                $icon = match(strtolower($extension)) {
                                    'pdf' => 'fa-file-pdf text-red-500',
                                    'doc', 'docx' => 'fa-file-word text-blue-600',
                                    'xls', 'xlsx' => 'fa-file-excel text-green-600',
                                    'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                                    'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-purple-500',
                                    default => 'fa-file text-gray-500'
                                };
                            @endphp
                            <a href="{{ asset('storage/' . $doc) }}" target="_blank" 
                               class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700">
                                <i class="fas {{ $icon }} text-xl"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ basename($doc) }}</span>
                                <i class="fas fa-external-link-alt text-xs text-gray-400 ml-auto"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/30 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-file-circle-plus text-xl mb-1 block"></i>
                        Belum ada dokumen yang dilampirkan.
                    </div>
                @endif
            </div>

            {{-- Footer actions --}}
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 md:px-8 py-4 bg-gray-50/50 dark:bg-gray-700/10 flex flex-wrap items-center justify-between gap-3">
                <span class="text-xs text-gray-400">
                    <i class="far fa-clock mr-1"></i> Terakhir diupdate: {{ $room->updated_at->diffForHumans() }}
                </span>
                <div class="flex gap-2">
                    <a href="{{ route('owner.rooms.edit', $room) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold rounded-lg transition shadow-sm hover:shadow flex items-center gap-1.5">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('owner.rooms.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                        <i class="fas fa-list"></i> Semua Ruangan
                    </a>
                </div>
            </div>

        </div> <!-- end Main Card -->
    </div>
</div>

{{-- ===================== LIGHTBOX MODAL ===================== --}}
<div id="lightboxModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm hidden transition-opacity duration-300">
    <div class="relative w-full h-full flex items-center justify-center">

        {{-- Tombol close --}}
        <button id="closeLightbox" class="absolute top-4 right-4 z-50 text-white/80 hover:text-white text-3xl p-2 transition">
            <i class="fas fa-times"></i>
        </button>

        {{-- Container slide lightbox --}}
        <div class="relative w-full h-full max-w-7xl mx-auto px-4 md:px-8 flex items-center justify-center">
            <div id="lightboxSlides" class="flex transition-transform duration-300 ease-in-out w-full h-full">
                @foreach($images as $index => $image)
                    <div class="min-w-full h-full flex items-center justify-center p-4">
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="{{ $room->name }} - Gambar {{ $index+1 }}" 
                             class="max-w-full max-h-full object-contain select-none">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tombol navigasi lightbox --}}
        @if($totalImages > 1)
            <button id="prevLightbox" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white p-3 rounded-full transition z-40">
                <i class="fas fa-chevron-left text-2xl"></i>
            </button>
            <button id="nextLightbox" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white p-3 rounded-full transition z-40">
                <i class="fas fa-chevron-right text-2xl"></i>
            </button>

            {{-- Indikator lightbox --}}
            <div id="lightboxIndicators" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-40">
                @for($i = 0; $i < $totalImages; $i++)
                    <button class="lb-dot w-3 h-3 rounded-full bg-white/40 hover:bg-white transition {{ $i === 0 ? 'bg-white' : '' }}" data-index="{{ $i }}"></button>
                @endfor
            </div>
        @endif

        {{-- Info gambar (opsional) --}}
        <div id="lightboxCounter" class="absolute bottom-16 left-1/2 -translate-x-1/2 text-white/60 text-sm z-40">
            <span id="currentIndexDisplay">1</span> / <span id="totalIndexDisplay">{{ $totalImages }}</span>
        </div>
    </div>
</div>

{{-- Tambahan CSS untuk scrollbar dan lightbox --}}
@push('styles')
<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 9999px;
    }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    .scrollbar-thin {
        scrollbar-width: thin;
    }

    /* Lightbox transisi */
    #lightboxModal {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #lightboxModal.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #lightboxModal:not(.hidden) {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
</style>
@endpush

{{-- Script untuk carousel dan lightbox --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---- Variabel utama ----
        const slidesContainer = document.getElementById('carouselSlides');
        const prevBtn = document.getElementById('prevSlide');
        const nextBtn = document.getElementById('nextSlide');
        const dots = document.querySelectorAll('.dot');
        let currentIndex = 0;
        const totalSlides = {{ $totalImages ?? 0 }};

        // ---- Lightbox elements ----
        const lightboxModal = document.getElementById('lightboxModal');
        const lightboxSlides = document.getElementById('lightboxSlides');
        const closeLightbox = document.getElementById('closeLightbox');
        const prevLightbox = document.getElementById('prevLightbox');
        const nextLightbox = document.getElementById('nextLightbox');
        const lbDots = document.querySelectorAll('.lb-dot');
        const currentIndexDisplay = document.getElementById('currentIndexDisplay');
        const totalIndexDisplay = document.getElementById('totalIndexDisplay');

        // ---- Fungsi update carousel utama ----
        function updateSlide(index) {
            if (totalSlides <= 1) return;
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;
            const offset = -currentIndex * 100;
            slidesContainer.style.transform = `translateX(${offset}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === currentIndex);
                dot.classList.toggle('bg-white/50', i !== currentIndex);
            });
        }

        // ---- Fungsi update lightbox ----
        function updateLightbox(index) {
            if (totalSlides <= 1) return;
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;
            const offset = -currentIndex * 100;
            lightboxSlides.style.transform = `translateX(${offset}%)`;
            lbDots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === currentIndex);
                dot.classList.toggle('bg-white/40', i !== currentIndex);
            });
            currentIndexDisplay.textContent = currentIndex + 1;
        }

        // ---- Buka lightbox ----
        function openLightbox(index) {
            if (totalSlides === 0) return;
            lightboxModal.classList.remove('hidden');
            // Set posisi awal
            updateLightbox(index);
            // Sinkronkan dengan carousel utama (agar tidak beda)
            if (index !== currentIndex) {
                updateSlide(index);
            }
            // Mencegah scroll body
            document.body.style.overflow = 'hidden';
        }

        // ---- Tutup lightbox ----
        function closeLightboxFn() {
            lightboxModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---- Event klik pada gambar carousel (buka lightbox) ----
        const carouselWrapper = document.getElementById('carouselWrapper');
        if (carouselWrapper && totalSlides > 0) {
            carouselWrapper.addEventListener('click', function(e) {
                // Jangan jika klik tombol navigasi atau dot
                if (e.target.closest('button')) return;
                openLightbox(currentIndex);
            });
        }

        // ---- Event tombol carousel ----
        if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); updateSlide(currentIndex - 1); });
        if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); updateSlide(currentIndex + 1); });
        dots.forEach((dot, i) => {
            dot.addEventListener('click', (e) => {
                e.stopPropagation();
                updateSlide(i);
            });
        });

        // ---- Event lightbox ----
        if (closeLightbox) closeLightbox.addEventListener('click', closeLightboxFn);
        // Klik di luar gambar (area gelap) tutup
        lightboxModal.addEventListener('click', function(e) {
            if (e.target === lightboxModal || e.target === this.querySelector('.relative.w-full.h-full')) {
                closeLightboxFn();
            }
        });

        if (prevLightbox) prevLightbox.addEventListener('click', (e) => { e.stopPropagation(); updateLightbox(currentIndex - 1); });
        if (nextLightbox) nextLightbox.addEventListener('click', (e) => { e.stopPropagation(); updateLightbox(currentIndex + 1); });
        lbDots.forEach((dot, i) => {
            dot.addEventListener('click', (e) => {
                e.stopPropagation();
                updateLightbox(i);
            });
        });

        // ---- Touch swipe untuk lightbox ----
        let startX = 0;
        let isDragging = false;
        const lightboxContainer = lightboxModal.querySelector('.relative.w-full.h-full');
        if (lightboxContainer) {
            lightboxContainer.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            });
            lightboxContainer.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                const endX = e.changedTouches[0].clientX;
                const diff = startX - endX;
                if (Math.abs(diff) > 40) {
                    if (diff > 0) updateLightbox(currentIndex + 1);
                    else updateLightbox(currentIndex - 1);
                }
                isDragging = false;
            });
        }

        // ---- Keyboard support ----
        document.addEventListener('keydown', function(e) {
            if (lightboxModal.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeLightboxFn();
            if (e.key === 'ArrowLeft') updateLightbox(currentIndex - 1);
            if (e.key === 'ArrowRight') updateLightbox(currentIndex + 1);
        });

        // ---- Set total indicator ----
        if (totalIndexDisplay) totalIndexDisplay.textContent = totalSlides;

        // Inisialisasi tampilan awal
        if (totalSlides > 0) {
            updateSlide(0);
            updateLightbox(0);
        }
    });
</script>
@endpush
@endsection