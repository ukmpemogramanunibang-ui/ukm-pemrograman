@extends('layouts.roles.owner')

@section('title', 'Detail Ruangan — ' . $room->name)

@push('styles')
<style>
    :root { --rc-accent: #06b6d4; }
    .rc-glass {
        background: rgba(255,255,255,0.72);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(15,23,42,.06);
        box-shadow: 0 10px 30px -12px rgba(15,23,42,.10);
        transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
    }
    .rc-glass:hover { transform: translateY(-2px); box-shadow: 0 18px 46px -16px rgba(15,23,42,.18); }
    .dark .rc-glass { background: rgba(30,41,59,.72); border-color: rgba(255,255,255,.06); }

    .rc-header {
        background: linear-gradient(135deg, rgba(6,182,212,.08), rgba(59,130,246,.05), rgba(139,92,246,.08));
        border-bottom: 1px solid rgba(15,23,42,.05);
    }
    .dark .rc-header { border-color: rgba(255,255,255,.05); }

    .rc-chip {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .3rem .7rem; border-radius: 9999px;
        font-size: .7rem; font-weight: 700; line-height: 1;
    }
    .rc-card-link { transition: all .15s ease; }
    .rc-card-link:hover { border-color: var(--rc-accent); box-shadow: 0 10px 26px -14px rgba(6,182,212,.35); }
    .rc-card-link:hover .rc-arrow { opacity: 1; transform: translateX(2px); }
    .rc-arrow { opacity: 0; transition: all .2s ease; }

    .rc-day-row.today { background: rgba(6,182,212,.06); border-color: rgba(6,182,212,.25); }
    .dark .rc-day-row.today { background: rgba(6,182,212,.08); }

    .rc-pulse { animation: rcPulse 2s infinite; }
    @keyframes rcPulse {
        0%,100% { box-shadow: 0 0 0 0 currentColor; opacity: 1; }
        50%     { box-shadow: 0 0 0 6px transparent; opacity: .85; }
    }

    .rc-scroll::-webkit-scrollbar { width: 6px; }
    .rc-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .dark .rc-scroll::-webkit-scrollbar-thumb { background: #475569; }
    .rc-scroll { scrollbar-width: thin; }

    /* Lightbox */
    #rcLightbox { transition: opacity .25s ease, visibility .25s ease; }
    #rcLightbox.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
    #rcLightbox:not(.hidden) { opacity: 1; visibility: visible; pointer-events: auto; }
</style>
@endpush

@php
    /* Urutan hari baku */
    $dayOrder = [
        'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
        'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu',
    ];
    $todayKey = strtolower(now()->format('l'));

    /* Tone classes */
    $toneMap = [
        'emerald' => ['chip' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/25 dark:text-emerald-300 dark:border-emerald-700/40', 'dot' => 'bg-emerald-500 text-emerald-500'],
        'blue'    => ['chip' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/25 dark:text-blue-300 dark:border-blue-700/40',              'dot' => 'bg-blue-500 text-blue-500'],
        'yellow'  => ['chip' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/25 dark:text-yellow-300 dark:border-yellow-700/40',  'dot' => 'bg-yellow-500 text-yellow-500'],
        'orange'  => ['chip' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/25 dark:text-orange-300 dark:border-orange-700/40',  'dot' => 'bg-orange-500 text-orange-500'],
        'red'     => ['chip' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/25 dark:text-red-300 dark:border-red-700/40',                       'dot' => 'bg-red-500 text-red-500'],
        'gray'    => ['chip' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700/40 dark:text-gray-300 dark:border-gray-600',                   'dot' => 'bg-gray-400 text-gray-400'],
    ];
    $tone = $toneMap[$statusInfo['tone']] ?? $toneMap['gray'];

    $images    = is_array($room->images) ? array_values($room->images) : [];
    $documents = is_array($room->documents) ? array_values($room->documents) : [];
    $totalImages = count($images);
@endphp

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-[1500px] mx-auto space-y-5">

    {{-- ══════════ BREADCRUMB + ACTIONS ══════════ --}}
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
        <div class="flex items-center gap-2">
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
            <a href="{{ route('owner.rooms.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                      text-gray-700 dark:text-gray-200 text-xs font-bold transition">
                <i class="fas fa-list"></i> Semua Ruangan
            </a>
        </div>
    </div>

    {{-- ══════════ FLASH ══════════ --}}
    @foreach(['success' => ['emerald','circle-check'], 'error' => ['rose','circle-exclamation'], 'warning' => ['amber','triangle-exclamation']] as $key => $meta)
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

    {{-- ══════════ HEADER — Status Prioritas + Next Event ══════════ --}}
    <div class="rc-glass rounded-3xl overflow-hidden">
        <div class="rc-header px-6 md:px-8 py-6">

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="min-w-0">
                    <div class="flex items-center gap-3 flex-wrap mb-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400">
                            <i class="fas fa-lock mr-1"></i> Room Private
                        </span>
                        <span class="font-mono text-[11px] bg-white/70 dark:bg-gray-800/70 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                            {{ $room->room_code }}
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white leading-tight truncate">
                        {{ $room->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 mt-3" id="rcStatusZone">
                        <span class="rc-chip border {{ $tone['chip'] }}">
                            <span class="w-2 h-2 rounded-full {{ $tone['dot'] }} rc-pulse"></span>
                            <span id="rcStatusLabel">{{ $statusInfo['label'] }}</span>
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400" id="rcStatusNote">
                            {{ $statusInfo['note'] }}
                        </span>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 shrink-0">
                    <div class="px-3 py-2 rounded-xl bg-white/60 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                        <p class="text-[9px] font-black uppercase text-gray-400">Kapasitas</p>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $room->capacity_normal }} <span class="text-[10px] font-medium text-gray-500">org</span></p>
                    </div>
                    <div class="px-3 py-2 rounded-xl bg-white/60 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                        <p class="text-[9px] font-black uppercase text-gray-400">Lantai</p>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $room->floor ?? '—' }}</p>
                    </div>
                    <div class="px-3 py-2 rounded-xl bg-white/60 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                        <p class="text-[9px] font-black uppercase text-gray-400">Kategori</p>
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $room->category ?? 'Umum' }}</p>
                    </div>
                    <div class="px-3 py-2 rounded-xl bg-white/60 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                        <p class="text-[9px] font-black uppercase text-gray-400">Kondisi</p>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $room->condition }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Next Event Strip --}}
        @if($nextSchedule || $nextBooking)
            <div class="px-6 md:px-8 py-3 bg-gradient-to-r from-cyan-50/60 to-blue-50/40 dark:from-cyan-900/10 dark:to-blue-900/5 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 text-white text-[10px] flex items-center justify-center shrink-0">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase text-cyan-700 dark:text-cyan-400">Agenda Berikutnya</p>
                        @php
                            $next = $nextSchedule ?: $nextBooking;
                            $isBooking = !$nextSchedule && $nextBooking;
                        @endphp
                        <p class="text-sm font-bold text-gray-800 dark:text-white truncate">
                            {{ $next->title }}
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 ml-2">
                                {{ \Carbon\Carbon::parse($next->start_datetime)->isoFormat('dddd, D MMM • HH:mm') }}
                                –
                                {{ \Carbon\Carbon::parse($next->end_datetime)->format('H:i') }}
                            </span>
                        </p>
                    </div>
                </div>
                <span class="rc-chip {{ $isBooking ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/25 dark:text-indigo-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300' }} self-start md:self-auto">
                    <i class="fas {{ $isBooking ? 'fa-book' : 'fa-calendar-day' }}"></i>
                    {{ $isBooking ? 'Booking' : 'Jadwal' }}
                </span>
            </div>
        @endif
    </div>

    {{-- ══════════ MAIN GRID ══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ================= LEFT: 2/3 ================= --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ─── INFORMASI ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center">
                        <i class="fas fa-circle-info text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Informasi Ruangan</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @foreach([
                        ['Nama', $room->name],
                        ['Kategori', $room->category ?? '—'],
                        ['Gedung', $room->building ?? '—'],
                        ['Lantai', $room->floor ?? '—'],
                        ['Kapasitas', $room->capacity_normal . ' orang' . ($room->capacity_max > $room->capacity_normal ? " (max {$room->capacity_max})" : '')],
                        ['Kondisi', $room->condition],
                        ['Penanggung Jawab', $room->responsible_person ?? '—'],
                        ['Telepon', $room->responsible_phone ?? '—'],
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

            {{-- ─── JAM OPERASIONAL (urutan baku + sorot hari ini) ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center">
                        <i class="fas fa-clock text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Jam Operasional</h2>
                    <span class="ml-auto text-[10px] font-bold uppercase text-gray-400">Zona Waktu Asia/Jakarta</span>
                </div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @php
                        $opHours = is_array($room->operational_hours) ? $room->operational_hours : [];
                    @endphp
                    @foreach($dayOrder as $key => $label)
                        @php
                            $val = $opHours[$key] ?? null;
                            $isToday = $key === $todayKey;
                            $isOpen = !empty($val);
                        @endphp
                        <div class="rc-day-row {{ $isToday ? 'today' : '' }} px-3.5 py-2.5 rounded-xl border
                                    {{ $isOpen ? 'border-emerald-200/60 dark:border-emerald-700/40' : 'border-rose-200/60 dark:border-rose-700/40' }}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="fas {{ $isOpen ? 'fa-circle-check text-emerald-500' : 'fa-circle-xmark text-rose-500' }} text-xs"></i>
                                    <span class="text-xs font-black text-gray-800 dark:text-white">{{ $label }}</span>
                                    @if($isToday)
                                        <span class="rc-chip bg-cyan-600 text-white text-[9px]">Hari ini</span>
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

            {{-- ─── FASILITAS ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center">
                        <i class="fas fa-couch text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Fasilitas</h2>
                    <span class="ml-auto rc-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ $room->facilities->count() }} item
                    </span>
                </div>
                <div class="p-6">
                    @if($room->facilities->count())
                        <div class="flex flex-wrap gap-2">
                            @foreach($room->facilities as $facility)
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl
                                             bg-gray-100 dark:bg-gray-700/50
                                             text-xs font-semibold text-gray-700 dark:text-gray-200
                                             border border-gray-200 dark:border-gray-600">
                                    <i class="fas fa-check-circle text-emerald-500 text-[10px]"></i>
                                    {{ $facility->name }}
                                    @if($facility->quantity > 1)
                                        <span class="text-[10px] text-gray-400 bg-white/60 dark:bg-gray-800/50 px-1.5 py-0.5 rounded-full">×{{ $facility->quantity }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-couch text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-xs text-gray-400 mt-2">Belum ada fasilitas tercatat.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── GALERI ─── --}}
            @if($totalImages > 0)
                <div class="rc-glass rounded-3xl overflow-hidden">
                    <div class="rc-header px-6 py-4 flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 text-white flex items-center justify-center">
                            <i class="fas fa-images text-sm"></i>
                        </span>
                        <h2 class="font-black text-sm text-gray-900 dark:text-white">Galeri</h2>
                        <span class="ml-auto rc-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                            {{ $totalImages }} foto
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($images as $i => $image)
                                <button type="button"
                                        data-rc-open-lightbox="{{ $i }}"
                                        aria-label="Buka gambar {{ $i+1 }}"
                                        class="group relative rounded-2xl overflow-hidden aspect-[4/3] bg-gray-100 dark:bg-gray-700 border-2 border-transparent hover:border-cyan-500 transition-all">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="{{ $room->name }} — foto {{ $i+1 }}"
                                         loading="lazy" draggable="false"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded-full bg-black/60 text-white text-[10px] font-bold opacity-0 group-hover:opacity-100 transition">
                                        <i class="fas fa-expand mr-1"></i>Lihat
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ─── DOKUMEN (private link) ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 text-white flex items-center justify-center">
                        <i class="fas fa-file-alt text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Dokumen Terlampir</h2>
                    <span class="ml-auto rc-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ count($documents) }} file
                    </span>
                </div>
                <div class="p-6">
                    @if(count($documents))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($documents as $doc)
                                @php
                                    $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                    $icon = match($ext) {
                                        'pdf' => 'fa-file-pdf text-red-500',
                                        'doc','docx' => 'fa-file-word text-blue-600',
                                        'xls','xlsx' => 'fa-file-excel text-emerald-600',
                                        'ppt','pptx' => 'fa-file-powerpoint text-orange-500',
                                        'jpg','jpeg','png','gif','webp' => 'fa-file-image text-purple-500',
                                        default => 'fa-file text-gray-500',
                                    };
                                @endphp
                                <a href="{{ route('owner.rooms.documents.download', ['room' => $room->id, 'filename' => urlencode($doc)]) }}"
                                   rel="noopener noreferrer"
                                   class="group flex items-center gap-3 p-3 rounded-xl
                                          bg-gray-50 dark:bg-gray-700/30
                                          hover:bg-gray-100 dark:hover:bg-gray-700/60
                                          border border-gray-200 dark:border-gray-700 transition">
                                    <i class="fas {{ $icon }} text-xl shrink-0"></i>
                                    <span class="flex-1 min-w-0 text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ basename($doc) }}</span>
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

        {{-- ================= RIGHT: 1/3 ================= --}}
        <div class="space-y-5">

            {{-- ─── QR CODE ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-500 text-white flex items-center justify-center">
                        <i class="fas fa-qrcode text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">QR Code</h2>
                    @if($qrValid)
                        <span class="ml-auto rc-chip bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300">
                            <i class="fas fa-circle-check"></i> Aktif
                        </span>
                    @endif
                </div>
                <div class="p-6 flex flex-col items-center">
                    @if($qrValid)
                        <div class="w-full max-w-[200px] aspect-square bg-white rounded-2xl p-4 shadow-md">
                            <img src="{{ asset('storage/' . $room->qr_code) }}"
                                 alt="QR Code {{ $room->name }}"
                                 class="w-full h-full object-contain">
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 mt-5 w-full max-w-[260px]">
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
                                           bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600
                                           text-white shadow-md shadow-teal-500/20 transition">
                                <i class="fas fa-qrcode mr-1"></i> Generate QR Code
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- ─── JADWAL (clickable) ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Jadwal</h2>
                    <span class="rc-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ $room->schedules_count }}
                    </span>
                    <a href="{{ route('owner.rooms.schedules', $room) }}"
                       class="ml-auto text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                        Lihat Semua <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
                <div class="p-4 max-h-[420px] overflow-y-auto rc-scroll space-y-2.5">
                    @forelse($schedules as $schedule)
                        @php
                            $start = \Carbon\Carbon::parse($schedule->start_datetime);
                            $end   = \Carbon\Carbon::parse($schedule->end_datetime);
                            $desc  = $schedule->description ?? '';
                            $person = $semester = null;
                            if (preg_match('/Pengguna:\s*([^\n]+)/', $desc, $m)) $person = trim($m[1]);
                            if (preg_match('/Semester:\s*([^\n]+)/', $desc, $m)) $semester = trim($m[1]);
                            $statusTone = match($schedule->status) {
                                'Aktif' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300',
                                'Selesai' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/25 dark:text-blue-300',
                                'Dibatalkan' => 'bg-red-50 text-red-700 dark:bg-red-900/25 dark:text-red-300',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300',
                            };
                        @endphp
                        <a href="{{ route('owner.schedules.show', $schedule) }}"
                           class="rc-card-link block p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/40">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[9px] font-bold uppercase leading-none">{{ $start->isoFormat('MMM') }}</span>
                                    <span class="text-sm font-black leading-none mt-0.5">{{ $start->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $schedule->title }}</p>
                                        <span class="rc-chip {{ $statusTone }} shrink-0">
                                            {{ $schedule->status ?? 'Aktif' }}
                                        </span>
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
                                <i class="fas fa-chevron-right rc-arrow text-cyan-500 text-[10px] mt-1"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-xmark text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-xs text-gray-400 mt-2">Belum ada jadwal terdaftar.</p>
                            <a href="{{ route('owner.schedules.upload') }}"
                               class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg text-[11px] font-bold
                                      bg-cyan-50 dark:bg-cyan-900/25 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-100 transition">
                                <i class="fas fa-plus"></i> Buat Jadwal
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ─── BOOKING (clickable) ─── --}}
            <div class="rc-glass rounded-3xl overflow-hidden">
                <div class="rc-header px-6 py-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex items-center justify-center">
                        <i class="fas fa-book text-sm"></i>
                    </span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Booking</h2>
                    <span class="rc-chip bg-white/70 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ $room->bookings_count }}
                    </span>
                    <a href="{{ route('owner.bookings.index', ['room_id' => $room->id]) }}"
                       class="ml-auto text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                        Lihat Semua <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
                <div class="p-4 max-h-[420px] overflow-y-auto rc-scroll space-y-2.5">
                    @forelse($bookings as $booking)
                        @php
                            $start = \Carbon\Carbon::parse($booking->start_datetime);
                            $end   = \Carbon\Carbon::parse($booking->end_datetime);
                            $statusTone = match(true) {
                                $booking->status === 'Disetujui' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-300',
                                $booking->status === 'Menunggu Persetujuan' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/25 dark:text-yellow-300',
                                $booking->status === 'Ditolak' || $booking->status === 'Dibatalkan' => 'bg-red-50 text-red-700 dark:bg-red-900/25 dark:text-red-300',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300',
                            };
                        @endphp
                        <a href="{{ route('owner.bookings.show', $booking) }}"
                           class="rc-card-link block p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/40">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[9px] font-bold uppercase leading-none">{{ $start->isoFormat('MMM') }}</span>
                                    <span class="text-sm font-black leading-none mt-0.5">{{ $start->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $booking->title ?? 'Booking' }}</p>
                                        <span class="rc-chip {{ $statusTone }} shrink-0">{{ $booking->status }}</span>
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
                                <i class="fas fa-chevron-right rc-arrow text-cyan-500 text-[10px] mt-1"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-check text-3xl text-emerald-300 dark:text-emerald-700"></i>
                            <p class="text-xs text-gray-400 mt-2">Belum ada booking aktif.</p>
                            <a href="{{ route('owner.rooms.booking.create', $room) }}"
                               class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg text-[11px] font-bold
                                      bg-indigo-50 dark:bg-indigo-900/25 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition">
                                <i class="fas fa-plus"></i> Booking Ruangan
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ FOOTER — Sync Status ══════════ --}}
    <div class="rc-glass rounded-2xl px-5 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
        <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 rc-pulse"></span>
                <span id="rcSyncLabel" class="font-bold text-emerald-600 dark:text-emerald-400">Live</span>
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

{{-- ══════════ LIGHTBOX ══════════ --}}
@if($totalImages > 0)
<div id="rcLightbox" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-sm items-center justify-center">
    <button id="rcLightboxClose" aria-label="Tutup galeri"
            class="absolute top-4 right-4 text-white/80 hover:text-white text-2xl p-3 z-50 transition">
        <i class="fas fa-times"></i>
    </button>

    <button id="rcLightboxPrev" aria-label="Gambar sebelumnya"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center z-40 transition">
        <i class="fas fa-chevron-left text-xl"></i>
    </button>
    <button id="rcLightboxNext" aria-label="Gambar berikutnya"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center z-40 transition">
        <i class="fas fa-chevron-right text-xl"></i>
    </button>

    <div class="w-full h-full max-w-7xl mx-auto px-4 md:px-8 flex items-center justify-center overflow-hidden">
        <div id="rcLightboxSlides" class="flex transition-transform duration-300 ease-out w-full h-full">
            @foreach($images as $i => $image)
                <div class="min-w-full h-full flex items-center justify-center p-4">
                    <img src="{{ asset('storage/' . $image) }}"
                         alt="{{ $room->name }} — {{ $i+1 }}"
                         draggable="false"
                         class="max-w-full max-h-full object-contain select-none">
                </div>
            @endforeach
        </div>
    </div>

    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/70 text-xs font-bold tracking-wider">
        <span id="rcLightboxCounter">1</span> / {{ $totalImages }}
    </div>
</div>
@endif

@push('scripts')
<script>
(function () {
    'use strict';

    /* ═════════════════════════════════════════════════════════════
       LIGHTBOX — open / navigate / close
    ═════════════════════════════════════════════════════════════ */
    const lb        = document.getElementById('rcLightbox');
    if (lb) {
        const slides  = document.getElementById('rcLightboxSlides');
        const close   = document.getElementById('rcLightboxClose');
        const prev    = document.getElementById('rcLightboxPrev');
        const next    = document.getElementById('rcLightboxNext');
        const counter = document.getElementById('rcLightboxCounter');
        const total   = {{ $totalImages }};
        let idx = 0;

        function go(i) {
            if (i < 0) i = total - 1;
            if (i >= total) i = 0;
            idx = i;
            slides.style.transform = `translateX(-${idx * 100}%)`;
            counter.textContent = idx + 1;
        }
        function open(i) {
            lb.classList.remove('hidden');
            lb.classList.add('flex');
            document.body.style.overflow = 'hidden';
            go(i);
            close.focus();
        }
        function shut() {
            lb.classList.add('hidden');
            lb.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('[data-rc-open-lightbox]').forEach(btn => {
            btn.addEventListener('click', () => open(parseInt(btn.dataset.rcOpenLightbox, 10)));
        });
        close.addEventListener('click', shut);
        prev?.addEventListener('click', (e) => { e.stopPropagation(); go(idx - 1); });
        next?.addEventListener('click', (e) => { e.stopPropagation(); go(idx + 1); });
        lb.addEventListener('click', (e) => { if (e.target === lb) shut(); });
        document.addEventListener('keydown', (e) => {
            if (lb.classList.contains('hidden')) return;
            if (e.key === 'Escape') shut();
            if (e.key === 'ArrowLeft') go(idx - 1);
            if (e.key === 'ArrowRight') go(idx + 1);
        });

        // Touch swipe
        let startX = 0, dragging = false;
        lb.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; dragging = true; });
        lb.addEventListener('touchend', (e) => {
            if (!dragging) return;
            const diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) diff > 0 ? go(idx + 1) : go(idx - 1);
            dragging = false;
        });
    }

    /* ═════════════════════════════════════════════════════════════
       LIVE STATUS POLLING — refresh badge setiap 60 detik
       (ringan — tidak refresh halaman)
    ═════════════════════════════════════════════════════════════ */
    const roomId = {{ $room->id }};
    const statusLabel = document.getElementById('rcStatusLabel');
    const statusNote  = document.getElementById('rcStatusNote');
    const syncLabel   = document.getElementById('rcSyncLabel');

    async function refreshStatus() {
        try {
            const res = await fetch(`{{ route('owner.rooms.show', $room) }}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store'
            });
            // Tidak parse HTML — hanya gunakan sebagai heartbeat jika diperlukan.
            // Untuk polling aktual, endpoint /live-status dianjurkan.
            if (syncLabel) {
                syncLabel.textContent = 'Live';
                syncLabel.classList.remove('text-amber-600', 'dark:text-amber-400');
                syncLabel.classList.add('text-emerald-600', 'dark:text-emerald-400');
            }
        } catch (e) {
            if (syncLabel) {
                syncLabel.textContent = 'Offline';
                syncLabel.classList.remove('text-emerald-600', 'dark:text-emerald-400');
                syncLabel.classList.add('text-amber-600', 'dark:text-amber-400');
            }
        }
    }

    // Poll setiap 60 detik
    setInterval(refreshStatus, 60000);

    /* ═════════════════════════════════════════════════════════════
       TOAST helper — pakai sebagai pengganti alert
    ═════════════════════════════════════════════════════════════ */
    window.rcToast = function (message, tone = 'info') {
        const tones = {
            success: 'bg-emerald-500',
            error:   'bg-rose-500',
            info:    'bg-cyan-500',
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