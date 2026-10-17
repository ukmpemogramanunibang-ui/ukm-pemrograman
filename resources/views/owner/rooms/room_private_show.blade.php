@extends('layouts.roles.owner')

@section('title', 'Detail Room – ' . $room->name)

@php
    /* ══════════════════════════════════════════════════════════════
       PREPARE DATA (defensive — aman walau controller lama)
    ══════════════════════════════════════════════════════════════ */
    $images      = is_array($room->images ?? null) ? array_values($room->images) : [];
    $documents   = is_array($room->documents ?? null) ? array_values($room->documents) : [];
    $totalImages = count($images);

    // Status resolver — kalau controller belum kirim, hitung di sini
    $liveStatus = $liveStatus ?? null;

    if (! isset($statusInfo) || ! is_array($statusInfo)) {
        $statusInfo = (function () use ($room, $liveStatus) {
            $status    = $liveStatus ?: $room->status;
            $condition = $room->condition;

            if ($condition === 'Rusak' || in_array($status, ['Dinonaktifkan', 'Ditutup'], true)) {
                return ['key' => 'unavailable', 'label' => 'Tidak Tersedia', 'tone' => 'red',
                        'note' => 'Ruangan tidak dapat digunakan.'];
            }
            if ($status === 'Maintenance' || $condition === 'Renovasi') {
                return ['key' => 'maintenance', 'label' => 'Maintenance', 'tone' => 'orange',
                        'note' => 'Sedang dalam perawatan.'];
            }
            if ($status === 'Sedang Digunakan') {
                return ['key' => 'occupied', 'label' => 'Sedang Digunakan', 'tone' => 'blue',
                        'note' => 'Sedang berlangsung.'];
            }
            if ($status === 'Dipesan') {
                return ['key' => 'booked', 'label' => 'Dipesan', 'tone' => 'yellow',
                        'note' => 'Ada jadwal mendatang.'];
            }
            return ['key' => 'available', 'label' => 'Tersedia', 'tone' => 'emerald',
                    'note' => 'Siap digunakan.'];
        })();
    }

    $toneMap = [
        'emerald' => ['chip' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/25 dark:text-emerald-300 dark:border-emerald-700/40', 'dot' => 'bg-emerald-500 text-emerald-500'],
        'blue'    => ['chip' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/25 dark:text-blue-300 dark:border-blue-700/40',                       'dot' => 'bg-blue-500 text-blue-500'],
        'yellow'  => ['chip' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/25 dark:text-yellow-300 dark:border-yellow-700/40',           'dot' => 'bg-yellow-500 text-yellow-500'],
        'orange'  => ['chip' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/25 dark:text-orange-300 dark:border-orange-700/40',           'dot' => 'bg-orange-500 text-orange-500'],
        'red'     => ['chip' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/25 dark:text-red-300 dark:border-red-700/40',                             'dot' => 'bg-red-500 text-red-500'],
        'gray'    => ['chip' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700/40 dark:text-gray-300 dark:border-gray-600',                         'dot' => 'bg-gray-400 text-gray-400'],
    ];
    $tone = $toneMap[$statusInfo['tone']] ?? $toneMap['gray'];

    $dayOrder = [
        'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
        'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu',
    ];
    $todayKey = strtolower(now()->format('l'));

    // Fallback schedules/bookings kalau controller belum kirim
    $schedules    = $schedules    ?? $room->schedules()->orderByDesc('start_datetime')->limit(5)->get();
    $bookings     = $bookings     ?? $room->bookings()->orderByDesc('start_datetime')->limit(5)->get();
    $nextSchedule = $nextSchedule ?? null;
    $nextBooking  = $nextBooking  ?? null;

    // Count (pakai withCount kalau tersedia, fallback ke collection)
    $schedulesCount = $room->schedules_count ?? $room->schedules->count();
    $bookingsCount  = $room->bookings_count  ?? $room->bookings->count();
@endphp

@push('styles')
<style>
    :root { --rp-accent: #06b6d4; }
    .rp-glass {
        background: rgba(255,255,255,.72);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(15,23,42,.06);
        box-shadow: 0 10px 30px -12px rgba(15,23,42,.10);
        transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
    }
    .rp-glass:hover { transform: translateY(-2px); box-shadow: 0 18px 46px -16px rgba(15,23,42,.18); }
    .dark .rp-glass { background: rgba(30,41,59,.72); border-color: rgba(255,255,255,.06); }

    .rp-header {
        background: linear-gradient(135deg, rgba(6,182,212,.08), rgba(59,130,246,.05), rgba(139,92,246,.08));
        border-bottom: 1px solid rgba(15,23,42,.05);
    }
    .dark .rp-header { border-color: rgba(255,255,255,.05); }

    .rp-chip {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .3rem .7rem; border-radius: 9999px;
        font-size: .7rem; font-weight: 700; line-height: 1;
    }
    .rp-card-link { transition: all .15s ease; }
    .rp-card-link:hover { border-color: var(--rp-accent); box-shadow: 0 10px 26px -14px rgba(6,182,212,.35); }
    .rp-card-link:hover .rp-arrow { opacity: 1; transform: translateX(2px); }
    .rp-arrow { opacity: 0; transition: all .2s ease; }

    .rp-day-row.today { background: rgba(6,182,212,.06); border-color: rgba(6,182,212,.25); }
    .dark .rp-day-row.today { background: rgba(6,182,212,.08); }

    .rp-pulse { animation: rpPulse 2s infinite; }
    @keyframes rpPulse {
        0%,100% { box-shadow: 0 0 0 0 currentColor; opacity: 1; }
        50%     { box-shadow: 0 0 0 6px transparent; opacity: .85; }
    }
    .rp-scroll::-webkit-scrollbar { width: 6px; }
    .rp-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .dark .rp-scroll::-webkit-scrollbar-thumb { background: #475569; }
    .rp-scroll { scrollbar-width: thin; }

    /* Lightbox */
    #rpLightbox { transition: opacity .25s ease, visibility .25s ease; }
    #rpLightbox.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
    #rpLightbox:not(.hidden) { opacity: 1; visibility: visible; pointer-events: auto; }

    /* Carousel */
    #rpCarouselSlides { transition: transform .5s cubic-bezier(.22,1,.36,1); }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-[1500px] mx-auto space-y-5">

    {{-- ═════════════════════════════════════════════════════════════
         BREADCRUMB + ACTIONS
    ═════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-600 transition">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
            <a href="{{ route('owner.rooms.private') }}" class="hover:text-cyan-600 transition">Room Private</a>
            <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
            <span class="font-bold text-gray-700 dark:text-gray-200 truncate max-w-[220px]">{{ $room->name }}</span>
        </nav>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('owner.rooms.booking.create', $room) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                      text-white text-xs font-black shadow-md shadow-cyan-500/20 transition">
                <i class="fas fa-plus-circle"></i> Booking Ruangan
            </a>
            <a href="{{ route('owner.rooms.edit', $room) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      bg-amber-500 hover:bg-amber-600 text-white text-xs font-black transition">
                <i class="fas fa-pen"></i> Edit
            </a>
            <a href="{{ route('owner.rooms.private') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                      text-gray-700 dark:text-gray-200 text-xs font-bold transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
         FLASH MESSAGES
    ═════════════════════════════════════════════════════════════ --}}
    @foreach([
        'success' => ['emerald', 'circle-check'],
        'error'   => ['rose',    'circle-exclamation'],
        'warning' => ['amber',   'triangle-exclamation'],
    ] as $key => $meta)
        @if(session($key))
            <div class="p-4 rounded-2xl bg-{{ $meta[0] }}-50 dark:bg-{{ $meta[0] }}-900/20
                        border border-{{ $meta[0] }}-200 dark:border-{{ $meta[0] }}-700/40
                        text-{{ $meta[0] }}-700 dark:text-{{ $meta[0] }}-300 text-sm
                        flex items-start gap-2.5">
                <i class="fas fa-{{ $meta[1] }} mt-0.5"></i>
                <div class="flex-1">{!! session($key) !!}</div>
                <button onclick="this.parentElement.remove()" class="opacity-60 hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    @endforeach

    {{-- ═════════════════════════════════════════════════════════════
         HERO CARD — Carousel + Info + Status + Next Event
    ═════════════════════════════════════════════════════════════ --}}
    <div class="rp-glass rounded-3xl overflow-hidden">

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-0">

            {{-- ─────── CAROUSEL ─────── --}}
            <div class="lg:col-span-2 relative bg-gray-100 dark:bg-gray-900" style="min-height: 340px;">
                <div id="rpCarouselWrapper" class="relative w-full h-full cursor-zoom-in">
                    <div class="relative w-full h-full overflow-hidden">
                        <div id="rpCarouselSlides" class="flex h-full">
                            @forelse($images as $i => $image)
                                <div class="min-w-full h-full flex items-center justify-center"
                                     data-rp-slide="{{ $i }}">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="{{ $room->name }} — Gambar {{ $i+1 }}"
                                         draggable="false"
                                         class="w-full h-full object-contain select-none">
                                </div>
                            @empty
                                <div class="min-w-full h-full flex items-center justify-center">
                                    <i class="fas fa-door-open text-7xl text-gray-300 dark:text-gray-600"></i>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if($totalImages > 1)
                        <button type="button" id="rpPrevSlide"
                                aria-label="Gambar sebelumnya"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-10
                                       w-10 h-10 rounded-full
                                       bg-white/85 dark:bg-gray-800/85 hover:bg-white dark:hover:bg-gray-700
                                       text-gray-800 dark:text-white shadow-lg transition">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" id="rpNextSlide"
                                aria-label="Gambar berikutnya"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-10
                                       w-10 h-10 rounded-full
                                       bg-white/85 dark:bg-gray-800/85 hover:bg-white dark:hover:bg-gray-700
                                       text-gray-800 dark:text-white shadow-lg transition">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <div id="rpCarouselDots"
                             class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 flex gap-1.5">
                            @for($i = 0; $i < $totalImages; $i++)
                                <button type="button"
                                        class="rp-dot w-2 h-2 rounded-full bg-white/60 hover:bg-white transition {{ $i === 0 ? 'bg-white' : '' }}"
                                        data-rp-dot="{{ $i }}"
                                        aria-label="Ke gambar {{ $i+1 }}"></button>
                            @endfor
                        </div>
                    @endif

                    {{-- Overlay status --}}
                    <div class="absolute top-4 left-4 z-20">
                        <span class="rp-chip shadow-lg {{ $tone['chip'] }} border">
                            <span class="w-2 h-2 rounded-full {{ $tone['dot'] }} rp-pulse"></span>
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>

                    {{-- Badge private --}}
                    <div class="absolute top-4 right-4 z-20">
                        <span class="rp-chip bg-white/90 dark:bg-gray-800/90 text-cyan-600 dark:text-cyan-400 shadow-lg backdrop-blur-sm">
                            <i class="fas fa-lock"></i> Private
                        </span>
                    </div>
                </div>
            </div>

            {{-- ─────── INFO ─────── --}}
            <div class="lg:col-span-3 p-6 md:p-8 flex flex-col">

                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div class="min-w-0">
                        <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-black uppercase tracking-widest">
                            Room Private
                        </span>
                        <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mt-1 leading-tight truncate">
                            {{ $room->name }}
                        </h1>
                        <p class="font-mono text-xs text-gray-400 mt-1">{{ $room->room_code }}</p>
                    </div>
                </div>

                {{-- Status note --}}
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 flex items-center gap-1.5">
                    <i class="fas fa-circle-info text-cyan-500 text-[10px]"></i>
                    {{ $statusInfo['note'] }}
                </p>

                {{-- Quick stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-5">
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/40 p-4">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kapasitas</p>
                        <p class="text-lg font-black text-gray-800 dark:text-white leading-tight">
                            {{ $room->capacity_normal }} <span class="text-xs font-normal">org</span>
                        </p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/40 p-4">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Lantai</p>
                        <p class="text-lg font-black text-gray-800 dark:text-white leading-tight">{{ $room->floor ?? '—' }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/40 p-4">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kategori</p>
                        <p class="text-lg font-black text-gray-800 dark:text-white leading-tight truncate">{{ $room->category ?: '—' }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/40 p-4">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kondisi</p>
                        <p class="text-lg font-black text-gray-800 dark:text-white leading-tight">{{ $room->condition }}</p>
                    </div>
                </div>

                {{-- Fasilitas --}}
                <div class="mt-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-list-check text-cyan-500 text-sm"></i>
                        <h2 class="font-bold text-sm text-gray-700 dark:text-gray-200">Fasilitas</h2>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @forelse($room->facilities->take(8) as $facility)
                            <span class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700/50
                                         text-xs font-semibold text-gray-700 dark:text-gray-200">
                                {{ $facility->name }}
                                @if($facility->quantity > 1)
                                    <span class="text-[10px] text-gray-400 ml-0.5">×{{ $facility->quantity }}</span>
                                @endif
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic">Tidak ada fasilitas tercatat.</span>
                        @endforelse
                        @if($room->facilities->count() > 8)
                            <a href="{{ route('owner.rooms.facilities', $room) }}"
                               class="px-3 py-1.5 rounded-xl text-xs font-bold
                                      bg-cyan-50 dark:bg-cyan-900/25 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-100 transition">
                                +{{ $room->facilities->count() - 8 }} lainnya
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────── NEXT EVENT STRIP ─────── --}}
        @php
            $next = $nextSchedule ?: $nextBooking;
            $isBooking = ! $nextSchedule && $nextBooking;
        @endphp
        @if($next)
            <div class="px-6 md:px-8 py-3
                        bg-gradient-to-r from-cyan-50/70 to-blue-50/40
                        dark:from-cyan-900/10 dark:to-blue-900/5
                        border-t border-gray-200 dark:border-gray-700
                        flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 text-white text-xs flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase text-cyan-700 dark:text-cyan-400 tracking-wider">
                            Agenda Berikutnya
                        </p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white truncate">
                            {{ $next->title }}
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 ml-2">
                                {{ \Carbon\Carbon::parse($next->start_datetime)->isoFormat('dddd, D MMM • HH:mm') }}
                                – {{ \Carbon\Carbon::parse($next->end_datetime)->format('H:i') }}
                            </span>
                        </p>
                    </div>
                </div>
                <span class="rp-chip self-start md:self-auto
                    {{ $isBooking
                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/25 dark:text-indigo-300'
                        : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300' }}">
                    <i class="fas {{ $isBooking ? 'fa-book' : 'fa-calendar-day' }}"></i>
                    {{ $isBooking ? 'Booking' : 'Jadwal' }}
                </span>
            </div>
        @endif
    </div>

    {{-- ═════════════════════════════════════════════════════════════
         MAIN GRID
    ═════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ══════════ LEFT: 2/3 ══════════ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ─── JAM OPERASIONAL ─── --}}
            <div class="rp-glass rounded-3xl overflow-hidden">
                <div class="rp-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center">
                        <i class="fas fa-clock text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Jam Operasional</h2>
                    <span class="ml-auto text-[10px] font-bold uppercase text-gray-400">Asia/Jakarta</span>
                </div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @php $opHours = is_array($room->operational_hours) ? $room->operational_hours : []; @endphp
                    @foreach($dayOrder as $key => $label)
                        @php
                            $val     = $opHours[$key] ?? null;
                            $isToday = $key === $todayKey;
                            $isOpen  = ! empty($val);
                        @endphp
                        <div class="rp-day-row {{ $isToday ? 'today' : '' }} px-3.5 py-2.5 rounded-xl border
                                    {{ $isOpen
                                        ? 'border-emerald-200/60 dark:border-emerald-700/40'
                                        : 'border-rose-200/60 dark:border-rose-700/40' }}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="fas {{ $isOpen ? 'fa-circle-check text-emerald-500' : 'fa-circle-xmark text-rose-500' }} text-xs"></i>
                                    <span class="text-xs font-black text-gray-800 dark:text-white">{{ $label }}</span>
                                    @if($isToday)
                                        <span class="rp-chip bg-cyan-600 text-white text-[9px]">Hari ini</span>
                                    @endif
                                </div>
                                <span class="text-[11px] font-bold {{ $isOpen ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300' }}">
                                    {{ $isOpen ? $val : 'Tutup' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ─── INFORMASI LENGKAP ─── --}}
            <div class="rp-glass rounded-3xl overflow-hidden">
                <div class="rp-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center">
                        <i class="fas fa-circle-info text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Informasi Ruangan</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @foreach([
                        ['Nama',           $room->name],
                        ['Kategori',       $room->category ?? '—'],
                        ['Gedung',         $room->building ?? '—'],
                        ['Lantai',         $room->floor ?? '—'],
                        ['Kapasitas',      $room->capacity_normal . ' orang' . ($room->capacity_max > $room->capacity_normal ? " (max {$room->capacity_max})" : '')],
                        ['Kondisi',        $room->condition],
                        ['Penanggung Jawab', $room->responsible_person ?? '—'],
                        ['Telepon',        $room->responsible_phone ?? '—'],
                    ] as [$label, $value])
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">{{ $label }}</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $value }}</p>
                        </div>
                    @endforeach

                    @if($room->address)
                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">Alamat</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $room->address }}</p>
                        </div>
                    @endif

                    @if($room->description)
                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">Deskripsi</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $room->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── GALERI ─── --}}
            @if($totalImages > 0)
                <div class="rp-glass rounded-3xl overflow-hidden">
                    <div class="rp-header px-6 py-4 flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 text-white flex items-center justify-center">
                            <i class="fas fa-images text-sm"></i>
                        </span>
                        <h2 class="font-black text-sm text-gray-900 dark:text-white">Galeri</h2>
                        <span class="ml-auto rp-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                            {{ $totalImages }} foto
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($images as $i => $image)
                                <button type="button"
                                        data-rp-open-lightbox="{{ $i }}"
                                        aria-label="Buka gambar {{ $i+1 }}"
                                        class="group relative rounded-2xl overflow-hidden aspect-[4/3]
                                               bg-gray-100 dark:bg-gray-700
                                               border-2 border-transparent hover:border-cyan-500 transition-all">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="{{ $room->name }} — foto {{ $i+1 }}"
                                         loading="lazy" draggable="false"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded-full
                                                 bg-black/60 text-white text-[10px] font-bold
                                                 opacity-0 group-hover:opacity-100 transition">
                                        <i class="fas fa-expand mr-1"></i> Lihat
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ─── DOKUMEN ─── --}}
            <div class="rp-glass rounded-3xl overflow-hidden">
                <div class="rp-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 text-white flex items-center justify-center">
                        <i class="fas fa-file-alt text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Dokumen Terlampir</h2>
                    <span class="ml-auto rp-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ count($documents) }} file
                    </span>
                </div>
                <div class="p-6">
                    @if(count($documents))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($documents as $doc)
                                @php
                                    $ext  = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                    $icon = match($ext) {
                                        'pdf'                          => 'fa-file-pdf text-red-500',
                                        'doc','docx'                   => 'fa-file-word text-blue-600',
                                        'xls','xlsx'                   => 'fa-file-excel text-emerald-600',
                                        'ppt','pptx'                   => 'fa-file-powerpoint text-orange-500',
                                        'jpg','jpeg','png','gif','webp' => 'fa-file-image text-purple-500',
                                        default                        => 'fa-file text-gray-500',
                                    };
                                    $privateUrl = Route::has('owner.rooms.documents.download')
                                        ? route('owner.rooms.documents.download', ['room' => $room->id, 'filename' => urlencode($doc)])
                                        : asset('storage/' . $doc);
                                @endphp
                                <a href="{{ $privateUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="group flex items-center gap-3 p-3 rounded-xl
                                          bg-gray-50 dark:bg-gray-700/30
                                          hover:bg-gray-100 dark:hover:bg-gray-700/60
                                          border border-gray-200 dark:border-gray-700 transition">
                                    <i class="fas {{ $icon }} text-xl shrink-0"></i>
                                    <span class="flex-1 min-w-0 text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">
                                        {{ basename($doc) }}
                                    </span>
                                    <i class="fas fa-download text-xs text-gray-400 group-hover:text-cyan-500 transition"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-folder-open text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-xs text-gray-400 mt-2">Belum ada dokumen yang dilampirkan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════ RIGHT: 1/3 ══════════ --}}
        <div class="space-y-5">

            {{-- ─── QR CODE ─── --}}
            <div class="rp-glass rounded-3xl overflow-hidden">
                <div class="rp-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-500 text-white flex items-center justify-center">
                        <i class="fas fa-qrcode text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">QR Code</h2>
                    @if($qrValid ?? false)
                        <span class="ml-auto rp-chip bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300">
                            <i class="fas fa-circle-check"></i> Aktif
                        </span>
                    @endif
                </div>
                <div class="p-6 flex flex-col items-center">
                    @if($qrValid ?? false)
                        <div class="w-full max-w-[200px] aspect-square bg-white rounded-2xl p-4 shadow-md">
                            <img src="{{ asset('storage/' . $room->qr_code) }}"
                                 alt="QR Code {{ $room->name }}"
                                 class="w-full h-full object-contain">
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 mt-5 w-full max-w-[280px]">
                            @foreach(['png', 'jpg', 'webp', 'pdf'] as $fmt)
                                <a href="{{ route('owner.rooms.download-qr', ['room' => $room->id, 'format' => $fmt]) }}"
                                   class="text-center py-2 rounded-lg text-[10px] font-black uppercase
                                          bg-cyan-50 dark:bg-cyan-900/25
                                          text-cyan-700 dark:text-cyan-300
                                          hover:bg-cyan-100 dark:hover:bg-cyan-900/50 transition">
                                    {{ $fmt }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="w-40 h-40 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                            <i class="fas fa-qrcode text-6xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <form action="{{ route('owner.rooms.generate-qr', $room) }}" method="POST" class="mt-4 w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full py-2.5 rounded-xl text-xs font-black
                                           bg-gradient-to-r from-teal-500 to-cyan-500
                                           hover:from-teal-600 hover:to-cyan-600
                                           text-white shadow-md shadow-teal-500/20 transition">
                                <i class="fas fa-qrcode mr-1"></i> Generate QR Code
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- ─── JADWAL ─── --}}
            <div class="rp-glass rounded-3xl overflow-hidden">
                <div class="rp-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Jadwal</h2>
                    <span class="rp-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ $schedulesCount }}
                    </span>
                    <a href="{{ route('owner.rooms.schedules', $room) }}"
                       class="ml-auto text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                        Lihat Semua <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
                <div class="p-4 max-h-[440px] overflow-y-auto rp-scroll space-y-2.5">
                    @forelse($schedules as $schedule)
                        @php
                            $start  = \Carbon\Carbon::parse($schedule->start_datetime);
                            $end    = \Carbon\Carbon::parse($schedule->end_datetime);
                            $desc   = $schedule->description ?? '';
                            $person = $semester = null;
                            if (preg_match('/Pengguna:\s*([^\n]+)/', $desc, $m)) $person   = trim($m[1]);
                            if (preg_match('/Semester:\s*([^\n]+)/', $desc, $m)) $semester = trim($m[1]);
                            $statusTone = match($schedule->status) {
                                'Aktif'      => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300',
                                'Selesai'    => 'bg-blue-50 text-blue-700 dark:bg-blue-900/25 dark:text-blue-300',
                                'Dibatalkan' => 'bg-red-50 text-red-700 dark:bg-red-900/25 dark:text-red-300',
                                default      => 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300',
                            };
                        @endphp
                        <a href="{{ route('owner.schedules.show', $schedule) }}"
                           class="rp-card-link block p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/40">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[9px] font-bold uppercase leading-none">{{ $start->isoFormat('MMM') }}</span>
                                    <span class="text-sm font-black leading-none mt-0.5">{{ $start->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $schedule->title }}</p>
                                        <span class="rp-chip {{ $statusTone }} shrink-0">{{ $schedule->status ?? 'Aktif' }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                        <i class="fas fa-clock text-cyan-500 text-[9px]"></i>
                                        {{ $start->format('H:i') }}–{{ $end->format('H:i') }}
                                        <span class="opacity-40">•</span>
                                        <i class="fas fa-calendar-day text-blue-500 text-[9px]"></i>
                                        {{ $start->isoFormat('dddd') }}
                                    </p>
                                    @if($schedule->activity_type)
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                            <i class="fas fa-tag text-purple-500 text-[9px]"></i>
                                            {{ $schedule->activity_type }}
                                            @if($person)<span class="opacity-40">•</span><i class="fas fa-user text-emerald-500 text-[9px]"></i>{{ $person }}@endif
                                            @if($semester)<span class="opacity-40">•</span><i class="fas fa-layer-group text-amber-500 text-[9px]"></i>{{ $semester }}@endif
                                        </p>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-right rp-arrow text-cyan-500 text-[10px] mt-1"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-xmark text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-xs text-gray-400 mt-2">Belum ada jadwal terdaftar.</p>
                            <a href="{{ route('owner.schedules.upload') }}"
                               class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg text-[11px] font-bold
                                      bg-cyan-50 dark:bg-cyan-900/25 text-cyan-700 dark:text-cyan-300
                                      hover:bg-cyan-100 transition">
                                <i class="fas fa-plus"></i> Buat Jadwal
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ─── BOOKING ─── --}}
            <div class="rp-glass rounded-3xl overflow-hidden">
                <div class="rp-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex items-center justify-center">
                        <i class="fas fa-book text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Booking</h2>
                    <span class="rp-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ $bookingsCount }}
                    </span>
                    @if(Route::has('owner.bookings.index'))
                        <a href="{{ route('owner.bookings.index', ['room_id' => $room->id]) }}"
                           class="ml-auto text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                            Lihat Semua <i class="fas fa-chevron-right text-[9px]"></i>
                        </a>
                    @endif
                </div>
                <div class="p-4 max-h-[440px] overflow-y-auto rp-scroll space-y-2.5">
                    @forelse($bookings as $booking)
                        @php
                            $start = \Carbon\Carbon::parse($booking->start_datetime);
                            $end   = \Carbon\Carbon::parse($booking->end_datetime);
                            $statusTone = match(true) {
                                $booking->status === 'Disetujui'              => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300',
                                $booking->status === 'Menunggu Persetujuan'   => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/25 dark:text-yellow-300',
                                in_array($booking->status, ['Ditolak','Dibatalkan'], true)
                                                                             => 'bg-red-50 text-red-700 dark:bg-red-900/25 dark:text-red-300',
                                default                                       => 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300',
                            };
                            $bookingUrl = Route::has('owner.bookings.show')
                                ? route('owner.bookings.show', $booking)
                                : '#';
                        @endphp
                        <a href="{{ $bookingUrl }}"
                           class="rp-card-link block p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/40">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[9px] font-bold uppercase leading-none">{{ $start->isoFormat('MMM') }}</span>
                                    <span class="text-sm font-black leading-none mt-0.5">{{ $start->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $booking->title ?? 'Booking' }}</p>
                                        <span class="rp-chip {{ $statusTone }} shrink-0">{{ $booking->status }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                        <i class="fas fa-clock text-cyan-500 text-[9px]"></i>
                                        {{ $start->format('H:i') }}–{{ $end->format('H:i') }}
                                        <span class="opacity-40">•</span>
                                        {{ $start->format('d/m/Y') }}
                                    </p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1.5">
                                        <i class="fas fa-user text-blue-500 text-[9px]"></i>
                                        {{ $booking->requester_name ?? '—' }}
                                        @if($booking->approver)
                                            <span class="opacity-40">•</span>
                                            <i class="fas fa-check-circle text-emerald-500 text-[9px]"></i>
                                            {{ $booking->approver->name }}
                                        @endif
                                    </p>
                                </div>
                                <i class="fas fa-chevron-right rp-arrow text-cyan-500 text-[10px] mt-1"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-check text-3xl text-emerald-300 dark:text-emerald-700"></i>
                            <p class="text-xs text-gray-400 mt-2">Belum ada booking aktif.</p>
                            <a href="{{ route('owner.rooms.booking.create', $room) }}"
                               class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg text-[11px] font-bold
                                      bg-indigo-50 dark:bg-indigo-900/25 text-indigo-700 dark:text-indigo-300
                                      hover:bg-indigo-100 transition">
                                <i class="fas fa-plus"></i> Booking Ruangan
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
         FOOTER — Sync Status
    ═════════════════════════════════════════════════════════════ --}}
    <div class="rp-glass rounded-2xl px-5 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
        <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 rp-pulse"></span>
                <span id="rpSyncLabel" class="font-bold text-emerald-600 dark:text-emerald-400">Live</span>
            </span>
            <span class="opacity-60">•</span>
            <span>
                <i class="far fa-clock mr-1"></i>
                Diperbarui {{ $room->updated_at->diffForHumans() }}
            </span>
        </div>
        <span class="text-[10px] text-gray-400">
            <i class="fas fa-shield-halved mr-1"></i>Private room — akses dibatasi
        </span>
    </div>

</div>

{{-- ═════════════════════════════════════════════════════════════════
     LIGHTBOX
═════════════════════════════════════════════════════════════════ --}}
@if($totalImages > 0)
    <div id="rpLightbox" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-sm">
        <div class="relative w-full h-full flex items-center justify-center">

            <button id="rpLightboxClose"
                    aria-label="Tutup galeri"
                    class="absolute top-4 right-4 z-50 text-white/80 hover:text-white text-3xl p-2 transition">
                <i class="fas fa-times"></i>
            </button>

            <div class="w-full h-full max-w-7xl mx-auto px-4 md:px-8 flex items-center justify-center overflow-hidden">
                <div id="rpLightboxSlides" class="flex transition-transform duration-300 ease-out w-full h-full">
                    @foreach($images as $i => $image)
                        <div class="min-w-full h-full flex items-center justify-center p-4">
                            <img src="{{ asset('storage/' . $image) }}"
                                 alt="{{ $room->name }} — gambar {{ $i+1 }}"
                                 draggable="false"
                                 class="max-w-full max-h-full object-contain select-none">
                        </div>
                    @endforeach
                </div>
            </div>

            @if($totalImages > 1)
                <button id="rpLightboxPrev"
                        aria-label="Gambar sebelumnya"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-40
                               w-12 h-12 rounded-full
                               bg-white/15 hover:bg-white/30
                               text-white flex items-center justify-center transition">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button id="rpLightboxNext"
                        aria-label="Gambar berikutnya"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-40
                               w-12 h-12 rounded-full
                               bg-white/15 hover:bg-white/30
                               text-white flex items-center justify-center transition">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>

                <div id="rpLightboxDots"
                     class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-40">
                    @for($i = 0; $i < $totalImages; $i++)
                        <button type="button"
                                class="rp-lb-dot w-2.5 h-2.5 rounded-full transition
                                       {{ $i === 0 ? 'bg-white' : 'bg-white/40 hover:bg-white' }}"
                                data-rp-lb-dot="{{ $i }}"
                                aria-label="Ke gambar {{ $i+1 }}"></button>
                    @endfor
                </div>
            @endif

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-40 text-white/60 text-xs font-bold tracking-wider">
                <span id="rpLightboxCounter">1</span> / {{ $totalImages }}
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
(function () {
    'use strict';

    const qs  = (s, r = document) => r.querySelector(s);
    const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

    /* ═════════════════════════════════════════════════════════════
       CAROUSEL (hero)
    ═════════════════════════════════════════════════════════════ */
    const totalSlides  = {{ $totalImages }};
    const slidesEl     = qs('#rpCarouselSlides');
    const dots         = qsa('.rp-dot');
    let carouselIndex  = 0;

    function renderCarousel() {
        if (!slidesEl || totalSlides <= 1) return;
        slidesEl.style.transform = `translateX(-${carouselIndex * 100}%)`;
        dots.forEach((d, i) => {
            d.classList.toggle('bg-white', i === carouselIndex);
            d.classList.toggle('bg-white/60', i !== carouselIndex);
        });
    }
    function moveCarousel(step) {
        if (totalSlides <= 1) return;
        carouselIndex = (carouselIndex + step + totalSlides) % totalSlides;
        renderCarousel();
    }

    qs('#rpPrevSlide')?.addEventListener('click', (e) => { e.stopPropagation(); moveCarousel(-1); });
    qs('#rpNextSlide')?.addEventListener('click', (e) => { e.stopPropagation(); moveCarousel(1); });
    dots.forEach((d, i) => d.addEventListener('click', (e) => { e.stopPropagation(); carouselIndex = i; renderCarousel(); }));

    // Touch swipe hero
    let carouselStartX = 0;
    qs('#rpCarouselWrapper')?.addEventListener('touchstart', (e) => {
        carouselStartX = e.touches[0].clientX;
    }, { passive: true });
    qs('#rpCarouselWrapper')?.addEventListener('touchend', (e) => {
        const diff = carouselStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) moveCarousel(diff > 0 ? 1 : -1);
    });

    /* ═════════════════════════════════════════════════════════════
       LIGHTBOX
    ═════════════════════════════════════════════════════════════ */
    const lb = qs('#rpLightbox');
    if (lb) {
        const lbSlides  = qs('#rpLightboxSlides');
        const lbClose   = qs('#rpLightboxClose');
        const lbPrev    = qs('#rpLightboxPrev');
        const lbNext    = qs('#rpLightboxNext');
        const lbDots    = qsa('.rp-lb-dot');
        const lbCounter = qs('#rpLightboxCounter');
        let lbIndex = 0;

        function renderLightbox() {
            if (!lbSlides) return;
            lbSlides.style.transform = `translateX(-${lbIndex * 100}%)`;
            lbDots.forEach((d, i) => {
                d.classList.toggle('bg-white', i === lbIndex);
                d.classList.toggle('bg-white/40', i !== lbIndex);
            });
            if (lbCounter) lbCounter.textContent = lbIndex + 1;
        }
        function moveLightbox(step) {
            if (totalSlides <= 1) return;
            lbIndex = (lbIndex + step + totalSlides) % totalSlides;
            renderLightbox();
        }
        function openLightbox(i) {
            if (totalSlides === 0) return;
            lbIndex = i;
            lb.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            renderLightbox();
            lbClose?.focus();
        }
        function closeLightbox() {
            lb.classList.add('hidden');
            document.body.style.overflow = '';
        }

        qsa('[data-rp-open-lightbox]').forEach((btn) => {
            btn.addEventListener('click', () => openLightbox(parseInt(btn.dataset.rpOpenLightbox, 10)));
        });
        // klik hero carousel juga buka lightbox (kecuali tombol/dot)
        qs('#rpCarouselWrapper')?.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            openLightbox(carouselIndex);
        });

        lbClose?.addEventListener('click', closeLightbox);
        lbPrev?.addEventListener('click', (e) => { e.stopPropagation(); moveLightbox(-1); });
        lbNext?.addEventListener('click', (e) => { e.stopPropagation(); moveLightbox(1); });
        lbDots.forEach((d, i) => d.addEventListener('click', (e) => { e.stopPropagation(); lbIndex = i; renderLightbox(); }));

        // Klik area gelap untuk tutup
        lb.addEventListener('click', (e) => { if (e.target === lb) closeLightbox(); });

        // Touch swipe
        let lbStartX = 0, lbDragging = false;
        lb.addEventListener('touchstart', (e) => { lbStartX = e.touches[0].clientX; lbDragging = true; }, { passive: true });
        lb.addEventListener('touchend', (e) => {
            if (!lbDragging) return;
            const diff = lbStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) moveLightbox(diff > 0 ? 1 : -1);
            lbDragging = false;
        });

        // Keyboard
        document.addEventListener('keydown', (e) => {
            if (lb.classList.contains('hidden')) return;
            if (e.key === 'Escape')      closeLightbox();
            if (e.key === 'ArrowLeft')   moveLightbox(-1);
            if (e.key === 'ArrowRight')  moveLightbox(1);
        });
    }

    /* ═════════════════════════════════════════════════════════════
       TOAST helper — pengganti alert untuk interaksi future
    ═════════════════════════════════════════════════════════════ */
    window.rpToast = function (message, tone = 'info') {
        const tones = {
            success: 'bg-emerald-500',
            error:   'bg-rose-500',
            info:    'bg-cyan-500',
            warning: 'bg-amber-500',
        };
        const el = document.createElement('div');
        el.className = `fixed bottom-6 right-6 z-[100] px-4 py-3 rounded-xl text-white text-sm font-bold shadow-lg ${tones[tone] || tones.info} transition-all`;
        el.style.opacity = '0';
        el.style.transform = 'translateY(12px)';
        el.textContent = message;
        document.body.appendChild(el);
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(12px)';
            setTimeout(() => el.remove(), 250);
        }, 2600);
    };
})();
</script>
@endpush
@endsection