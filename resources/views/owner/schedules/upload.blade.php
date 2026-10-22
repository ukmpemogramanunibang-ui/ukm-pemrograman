@extends('layouts.roles.owner')

@section('title', 'Smart Schedule Builder — UNICODE')

@php
    // ═══════════════════════════════════════════════════════════
    // PREPARE DATA (defensive — kalau controller tidak kirim)
    // ═══════════════════════════════════════════════════════════
    $rooms           = $rooms           ?? collect();
    $kelompokOptions = $kelompokOptions ?? collect();
    $generasiOptions = $generasiOptions ?? collect();
    $pengurusOptions = $pengurusOptions ?? collect();
    $lmsGroupOptions = $lmsGroupOptions ?? collect();
    $roleOptions     = $roleOptions     ?? collect();

    $semesterOptions = [
        'Semester I', 'Semester II', 'Semester III', 'Semester IV',
        'Semester V', 'Semester VI', 'Semester VII', 'Semester VIII',
    ];

    $days = [
        'monday'    => 'Senin',
        'tuesday'   => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday'  => 'Kamis',
        'friday'    => 'Jumat',
        'saturday'  => 'Sabtu',
        'sunday'    => 'Minggu',
    ];

    // Audience dataset untuk JS
    $audienceData = [
        'kelompok'  => $kelompokOptions->map(fn ($x) => ['id' => $x->id, 'name' => $x->name])->values(),
        'generasi'  => $generasiOptions->map(fn ($x) => ['id' => $x->id, 'name' => $x->name])->values(),
        'pengurus'  => $pengurusOptions->map(fn ($x) => ['id' => $x->id, 'name' => $x->name])->values(),
        'lms_group' => $lmsGroupOptions->map(fn ($x) => ['id' => $x->id, 'name' => $x->name])->values(),
        'role'      => $roleOptions->map(fn ($x) => ['id' => $x->id, 'name' => $x->name])->values(),
    ];
@endphp

@push('styles')
<style>
    .ssb-section {
        transition: box-shadow .2s ease;
    }
    .ssb-section:hover {
        box-shadow: 0 8px 24px -8px rgba(0,0,0,.08);
    }
    .ssb-day {
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .ssb-day:hover { transform: translateY(-1px); }
    .ssb-day.is-active {
        border-color: rgb(16 185 129);
        background: rgb(240 253 244);
    }
    .dark .ssb-day.is-active {
        border-color: rgb(16 185 129 / .4);
        background: rgb(6 78 59 / .15);
    }
    .ssb-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .2rem .55rem;
        border-radius: 9999px;
        font-size: .65rem;
        font-weight: 700;
        line-height: 1;
    }
    .ssb-card-pick {
        transition: all .15s ease;
    }
    .ssb-card-pick:hover {
        border-color: rgb(99 102 241);
    }
    .ssb-preview-day {
        min-height: 200px;
        transition: background .2s ease;
    }
    .ssb-audience-item {
        transition: all .12s ease;
    }
    .ssb-audience-item:has(input:checked) {
        background: rgb(79 70 229 / .08);
        border-color: rgb(99 102 241);
    }
    .ssb-audience-item:has(input:checked) span {
        color: rgb(79 70 229);
        font-weight: 700;
    }
    .ssb-progress-bar {
        transition: width .4s ease;
    }
    .ssb-legend-dot {
        display: inline-block;
        width: .5rem;
        height: .5rem;
        border-radius: 9999px;
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 max-w-[1600px] mx-auto space-y-5">

    {{-- ═══════════════════════════════════════════════════════════
         BREADCRUMB + HEADER
    ═══════════════════════════════════════════════════════════ --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 transition">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('owner.schedules.index') }}" class="hover:text-blue-600 transition">
            Jadwal
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Smart Builder</span>
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white
                       flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl
                             bg-gradient-to-br from-blue-500 to-indigo-600
                             text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                    <i class="fas fa-calendar-plus"></i>
                </span>
                Smart Schedule Builder
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 max-w-2xl">
                Buat jadwal ruangan yang terintegrasi dengan <b>Kelompok</b>, <b>Generasi</b>,
                <b>Pengurus</b>, dan <b>LMS Group</b> — dengan mode absensi Digital atau Upload.
            </p>
        </div>

        <a href="{{ route('owner.schedules.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                  bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                  text-gray-700 dark:text-gray-200 text-sm font-bold transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         FLASH MESSAGES
    ═══════════════════════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20
                    border border-emerald-200 dark:border-emerald-700/40
                    text-emerald-700 dark:text-emerald-300 text-sm
                    flex items-start gap-2.5">
            <i class="fas fa-circle-check mt-0.5"></i>
            <div>{!! session('success') !!}</div>
        </div>
    @endif

    @if(session('warning'))
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20
                    border border-amber-200 dark:border-amber-700/40
                    text-amber-700 dark:text-amber-300 text-sm
                    flex items-start gap-2.5">
            <i class="fas fa-triangle-exclamation mt-0.5"></i>
            <div>{!! session('warning') !!}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20
                    border border-rose-200 dark:border-rose-700/40">
            <div class="flex items-start gap-2.5">
                <i class="fas fa-circle-exclamation text-rose-500 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-rose-700 dark:text-rose-300 mb-1.5">
                        Ada {{ $errors->count() }} kesalahan:
                    </p>
                    <ul class="ml-4 list-disc text-xs text-rose-600 dark:text-rose-300 space-y-0.5">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         MAIN FORM
    ═══════════════════════════════════════════════════════════ --}}
    <form action="{{ route('owner.schedules.upload.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="scheduleForm"
          novalidate
          class="space-y-5">

        @csrf

        {{-- ══════════════════════════════════════════════════════
             STEP 01 — PILIH RUANGAN
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">

            <header class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                           bg-gradient-to-r from-blue-50 to-cyan-50
                           dark:from-blue-900/15 dark:to-cyan-900/15
                           flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-blue-500 text-white
                             text-xs font-black flex items-center justify-center">01</span>
                <div class="flex-1">
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Ruangan</h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Pilih ruangan & jam operasional otomatis ditampilkan.
                    </p>
                </div>
            </header>

            <div class="p-5 space-y-4">
                <div>
                    <label for="room_id"
                           class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <select name="room_id" id="room_id" required
                            class="w-full px-4 py-3 rounded-xl
                                   border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-900
                                   text-sm text-gray-900 dark:text-white
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-500/30 focus:border-blue-400">
                        <option value="">— Pilih Ruangan —</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}"
                                    data-name="{{ $room->name }}"
                                    data-code="{{ $room->room_code ?? '' }}"
                                    data-floor="{{ $room->floor ?? '—' }}"
                                    data-category="{{ $room->category ?? 'Umum' }}"
                                    data-operational='@json($room->operational_hours ?? [])'>
                                {{ $room->name }}@if($room->room_code) — {{ $room->room_code }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Room Info --}}
                <div id="roomInformation" class="hidden space-y-4">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20
                                    border border-blue-200 dark:border-blue-700/50">
                            <p class="text-[10px] font-black uppercase text-blue-500">Nama</p>
                            <p id="roomName" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">—</p>
                        </div>
                        <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/20
                                    border border-purple-200 dark:border-purple-700/50">
                            <p class="text-[10px] font-black uppercase text-purple-500">Lantai</p>
                            <p id="roomFloor" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">—</p>
                        </div>
                        <div class="p-3 rounded-xl bg-orange-50 dark:bg-orange-900/20
                                    border border-orange-200 dark:border-orange-700/50">
                            <p class="text-[10px] font-black uppercase text-orange-500">Kategori</p>
                            <p id="roomCategory" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">—</p>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20
                                    border border-emerald-200 dark:border-emerald-700/50">
                            <p class="text-[10px] font-black uppercase text-emerald-500">Kode</p>
                            <p id="roomCode" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5
                                                   font-mono">—</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">
                            <i class="fas fa-clock text-blue-500"></i> Jam Operasional
                        </p>
                        <div id="operationalDays"
                             class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-2"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════
             STEP 02 — IDENTITAS JADWAL
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">

            <header class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                           bg-gradient-to-r from-purple-50 to-fuchsia-50
                           dark:from-purple-900/15 dark:to-fuchsia-900/15
                           flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-purple-500 text-white
                             text-xs font-black flex items-center justify-center">02</span>
                <div class="flex-1">
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Identitas Jadwal</h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Nama kegiatan, semester, dan tahun akademik.
                    </p>
                </div>
            </header>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="md:col-span-2 lg:col-span-2">
                    <label for="title"
                           class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Nama Jadwal / Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required maxlength="255"
                           value="{{ old('title') }}"
                           placeholder="Contoh: Pelatihan Laravel Generasi 1"
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 text-sm
                                  text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-2 focus:ring-purple-500/30
                                  focus:border-purple-400">
                </div>

                <div>
                    <label for="academic_year"
                           class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Tahun Akademik
                    </label>
                    <input type="text" id="academic_year" name="academic_year"
                           value="{{ old('academic_year', '2026/2027') }}"
                           placeholder="2026/2027"
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 text-sm
                                  text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-2 focus:ring-purple-500/30
                                  focus:border-purple-400">
                </div>

                <div>
                    <label for="schedule_type"
                           class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Jenis Jadwal
                    </label>
                    <select id="schedule_type" name="schedule_type"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-900 text-sm
                                   text-gray-900 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-purple-500/30
                                   focus:border-purple-400">
                        <option value="weekly">Mingguan</option>
                        <option value="special">Khusus</option>
                        <option value="event">Event</option>
                    </select>
                </div>

                {{-- Semester Range --}}
                <div class="md:col-span-2 lg:col-span-4">
                    <div class="p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-900/15
                                border border-indigo-200 dark:border-indigo-700/50">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="w-9 h-9 rounded-xl bg-indigo-500 text-white
                                         flex items-center justify-center shrink-0">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-sm text-gray-900 dark:text-white">
                                    Rentang Semester
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    Pilih dari semester berapa sampai semester berapa. Cocok untuk
                                    kegiatan gabungan yang mencakup beberapa semester.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="semester_start"
                                       class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Dari Semester <span class="text-rose-500">*</span>
                                </label>
                                <select id="semester_start" name="semester_start" required
                                        class="w-full px-4 py-3 rounded-xl
                                               border border-gray-200 dark:border-gray-700
                                               bg-white dark:bg-gray-800 text-sm
                                               text-gray-900 dark:text-white">
                                    @foreach($semesterOptions as $sem)
                                        <option value="{{ $sem }}"
                                            {{ old('semester_start', 'Semester I') === $sem ? 'selected' : '' }}>
                                            {{ $sem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="semester_end"
                                       class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Sampai Semester <span class="text-rose-500">*</span>
                                </label>
                                <select id="semester_end" name="semester_end" required
                                        class="w-full px-4 py-3 rounded-xl
                                               border border-gray-200 dark:border-gray-700
                                               bg-white dark:bg-gray-800 text-sm
                                               text-gray-900 dark:text-white">
                                    @foreach($semesterOptions as $sem)
                                        <option value="{{ $sem }}"
                                            {{ old('semester_end', 'Semester VIII') === $sem ? 'selected' : '' }}>
                                            {{ $sem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="semesterRangeInfo"
                             class="mt-3 text-xs font-bold text-indigo-700 dark:text-indigo-300
                                    flex items-center gap-1.5">
                            <i class="fas fa-circle-info"></i>
                            <span id="semesterRangeText">Semester I s.d. Semester VIII</span>
                        </div>

                        <div id="semesterRangeError"
                             class="hidden mt-2 p-2.5 rounded-lg
                                    bg-rose-100 dark:bg-rose-900/30
                                    border border-rose-300 dark:border-rose-700
                                    text-xs text-rose-700 dark:text-rose-300">
                            <i class="fas fa-triangle-exclamation mr-1"></i>
                            Semester akhir tidak boleh lebih kecil dari semester awal.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════
             STEP 03 — TARGET & MODE ABSENSI
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">

            <header class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                           bg-gradient-to-r from-emerald-50 to-teal-50
                           dark:from-emerald-900/15 dark:to-teal-900/15
                           flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white
                             text-xs font-black flex items-center justify-center">03</span>
                <div class="flex-1">
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">
                        Target Penerima &amp; Mode Absensi
                    </h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Pilih siapa yang menerima jadwal & bagaimana absensinya.
                    </p>
                </div>
            </header>

            <div class="p-5 space-y-5">

                {{-- ═══ AUDIENCE TYPE ═══ --}}
                <div>
                    <p class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2.5">
                        Target Penerima <span class="text-rose-500">*</span>
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                        @php
                            $audienceOptions = [
                                'all'       => ['icon' => 'fa-globe',         'label' => 'Semua',      'color' => 'slate'],
                                'kelompok'  => ['icon' => 'fa-users',         'label' => 'Kelompok',   'color' => 'blue'],
                                'generasi'  => ['icon' => 'fa-layer-group',   'label' => 'Generasi',   'color' => 'amber'],
                                'pengurus'  => ['icon' => 'fa-user-tie',      'label' => 'Pengurus',   'color' => 'emerald'],
                                'lms_group' => ['icon' => 'fa-graduation-cap','label' => 'LMS Group',  'color' => 'purple'],
                                'role'      => ['icon' => 'fa-user-shield',   'label' => 'Role',       'color' => 'pink'],
                            ];
                        @endphp

                        @foreach($audienceOptions as $key => $opt)
                            <label class="cursor-pointer select-none">
                                <input type="radio" name="audience_type" value="{{ $key }}"
                                       class="peer sr-only audience-radio"
                                       {{ old('audience_type', 'all') === $key ? 'checked' : '' }}>
                                <div class="ssb-card-pick p-3 rounded-xl
                                            border-2 border-gray-200 dark:border-gray-700
                                            bg-white dark:bg-gray-800/60
                                            text-center transition
                                            hover:border-indigo-400
                                            peer-checked:border-indigo-500
                                            peer-checked:bg-indigo-50
                                            dark:peer-checked:bg-indigo-900/20
                                            peer-checked:shadow-md">
                                    <i class="fas {{ $opt['icon'] }} text-lg text-indigo-500"></i>
                                    <p class="text-[11px] font-black mt-1.5 text-gray-700 dark:text-gray-200">
                                        {{ $opt['label'] }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Detail picker --}}
                    <div id="audienceDetail"
                         class="hidden mt-4 p-4 rounded-xl
                                bg-gray-50 dark:bg-gray-900/50
                                border border-gray-200 dark:border-gray-700">

                        <div class="flex items-center justify-between mb-3 gap-2 flex-wrap">
                            <p class="text-xs font-black text-gray-700 dark:text-gray-200"
                               id="audienceDetailLabel">
                                Pilih target
                            </p>
                            <div class="flex items-center gap-2">
                                <input type="text" id="audienceSearch"
                                       placeholder="Cari..."
                                       autocomplete="off"
                                       class="px-3 py-1.5 rounded-lg text-xs
                                              border border-gray-200 dark:border-gray-700
                                              bg-white dark:bg-gray-800
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <button type="button" id="selectAllAudience"
                                        class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400
                                               hover:underline">
                                    <i class="fas fa-check-double"></i> Pilih Semua
                                </button>
                            </div>
                        </div>

                        <div id="audienceOptions"
                             class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2
                                    max-h-64 overflow-y-auto pr-1"></div>

                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700
                                    flex items-center justify-between gap-2 flex-wrap">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-users text-indigo-500"></i>
                                <span id="audienceCountText" class="font-bold">0 penerima terpilih</span>
                            </p>
                            <p class="text-[10px] text-gray-400 italic">
                                <i class="fas fa-shield-halved"></i>
                                Estimasi penerima dihitung saat submit
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ═══ ATTENDANCE MODE ═══ --}}
                <div>
                    <p class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2.5">
                        Mode Absensi <span class="text-rose-500">*</span>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @php
                            $attendanceModes = [
                                'none' => [
                                    'icon'  => 'fa-minus-circle',
                                    'label' => 'Tanpa Absensi',
                                    'desc'  => 'Hanya jadwal ruangan biasa.',
                                    'tone'  => 'slate',
                                ],
                                'digital' => [
                                    'icon'  => 'fa-qrcode',
                                    'label' => 'Digital (QR / NFC)',
                                    'desc'  => 'Real-time scan oleh penerima.',
                                    'tone'  => 'emerald',
                                ],
                                'upload' => [
                                    'icon'  => 'fa-file-upload',
                                    'label' => 'Upload Manual',
                                    'desc'  => 'Owner upload daftar hadir (PDF/gambar).',
                                    'tone'  => 'orange',
                                ],
                            ];
                        @endphp

                        @foreach($attendanceModes as $val => $mode)
                            <label class="cursor-pointer select-none">
                                <input type="radio" name="attendance_mode" value="{{ $val }}"
                                       class="peer sr-only attendance-radio"
                                       {{ old('attendance_mode', 'none') === $val ? 'checked' : '' }}>
                                <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                            bg-white dark:bg-gray-800/60 transition
                                            peer-checked:border-{{ $mode['tone'] }}-500
                                            peer-checked:bg-{{ $mode['tone'] }}-50
                                            dark:peer-checked:bg-{{ $mode['tone'] }}-900/20
                                            peer-checked:shadow-md">
                                    <div class="flex items-start gap-3">
                                        <i class="fas {{ $mode['icon'] }} text-xl text-{{ $mode['tone'] }}-500
                                                  mt-0.5"></i>
                                        <div class="min-w-0">
                                            <p class="font-black text-sm text-gray-800 dark:text-white">
                                                {{ $mode['label'] }}
                                            </p>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $mode['desc'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Options (visible kalau mode != none) --}}
                    <div id="attendanceOptions"
                         class="hidden mt-4 p-4 rounded-xl
                                bg-indigo-50 dark:bg-indigo-900/20
                                border border-indigo-200 dark:border-indigo-700/50
                                space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="hidden" name="auto_create_attendance" value="0">
                            <input type="checkbox" name="auto_create_attendance" value="1"
                                   class="w-5 h-5 rounded text-indigo-600 mt-0.5"
                                   {{ old('auto_create_attendance') ? 'checked' : '' }}>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    Otomatis buat sesi absensi
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    Sistem akan membuat sesi absensi &amp; men-assign target penerima.
                                </p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="hidden" name="requires_review" value="0">
                            <input type="checkbox" name="requires_review" value="1"
                                   class="w-5 h-5 rounded text-indigo-600 mt-0.5"
                                   {{ old('requires_review', true) ? 'checked' : '' }}>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    Perlu review sebelum valid
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    Absensi akan ditandai "pending review" sampai diverifikasi Owner.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </section>
{{-- ══════════════════════════════════════════════════════
     STEP 04 — JADWAL MINGGUAN (MULTI-TAHAP)
══════════════════════════════════════════════════════════ --}}
<section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                border border-gray-200 dark:border-gray-700
                shadow-sm overflow-hidden">

    <header class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                   bg-gradient-to-r from-cyan-50 to-blue-50
                   dark:from-cyan-900/15 dark:to-blue-900/15
                   flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-cyan-500 text-white
                         text-xs font-black flex items-center justify-center">04</span>
            <div>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">
                    Jadwal Mingguan — Multi-Tahap
                </h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                    Aktifkan hari, lalu tambahkan beberapa tahap kegiatan.
                    Setiap tahap dapat menargetkan generasi &amp; kelompok yang berbeda.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" id="collapseAllDays"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                           bg-white dark:bg-gray-700
                           border border-gray-200 dark:border-gray-600
                           text-xs font-bold text-gray-700 dark:text-gray-200
                           hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                <i class="fas fa-compress"></i> Tutup Semua
            </button>
            <button type="button" id="resetDays"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                           bg-white dark:bg-gray-700
                           border border-gray-200 dark:border-gray-600
                           text-xs font-bold text-gray-500 dark:text-gray-400
                           hover:bg-rose-50 dark:hover:bg-rose-900/20
                           hover:text-rose-600 transition">
                <i class="fas fa-eraser"></i> Reset
            </button>
        </div>
    </header>

    <div class="p-5 space-y-3">
        @foreach($days as $key => $day)
            <div class="ssb-day rounded-2xl border-2 border-gray-200 dark:border-gray-700
                        overflow-hidden transition"
                 data-day="{{ $key }}">

                {{-- Head --}}
                <div class="p-4 flex flex-col md:flex-row md:items-center
                            md:justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="day-icon w-10 h-10 rounded-xl shrink-0
                                    bg-blue-100 dark:bg-blue-900/30
                                    flex items-center justify-center">
                            <i class="fas fa-calendar-day text-blue-500"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-black text-sm text-gray-800 dark:text-white">
                                {{ $day }}
                            </p>
                            <p class="operational-label text-[11px] text-gray-500 dark:text-gray-400">
                                Pilih ruangan terlebih dahulu
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="day-stage-count hidden inline-flex items-center gap-1
                                     px-2.5 py-1 rounded-full
                                     bg-cyan-50 dark:bg-cyan-900/30
                                     text-cyan-700 dark:text-cyan-300
                                     text-[10px] font-black tracking-wider">
                            <i class="fas fa-layer-group"></i>
                            <span class="count-text">0 tahap</span>
                        </span>
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="schedules[{{ $key }}][enabled]"
                                   value="1"
                                   class="day-enabled w-5 h-5 rounded text-emerald-600"
                                   data-day="{{ $key }}">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-200">
                                Aktifkan
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Body --}}
                <div class="day-form hidden p-4 pt-0
                            border-t border-dashed border-gray-200 dark:border-gray-700">
                    <div class="day-activities space-y-3 mt-4"
                         data-day="{{ $key }}"></div>

                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center
                                sm:justify-between gap-2">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            <i class="fas fa-circle-info text-cyan-500"></i>
                            Klik <b>Tambah Kegiatan</b> untuk membuat tahap baru.
                            Tahap dihitung otomatis (Tahap&nbsp;1, 2, 3, …).
                        </p>
                        <button type="button"
                                class="add-activity-btn inline-flex items-center justify-center gap-2
                                       px-4 py-2.5 rounded-xl
                                       bg-gradient-to-r from-cyan-500 to-blue-600
                                       hover:from-cyan-600 hover:to-blue-700
                                       text-white text-xs font-black
                                       shadow-md shadow-cyan-500/20
                                       transition">
                            <i class="fas fa-plus-circle"></i>
                            Tambah Kegiatan di Hari Ini
                        </button>
                    </div>

                    <div class="day-status mt-3"></div>
                </div>
            </div>
        @endforeach
    </div>
</section>

        {{-- ══════════════════════════════════════════════════════
             STEP 05 — PREVIEW TIMELINE
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">

            <header class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                           bg-gradient-to-r from-amber-50 to-orange-50
                           dark:from-amber-900/15 dark:to-orange-900/15
                           flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-amber-500 text-white
                             text-xs font-black flex items-center justify-center">05</span>
                <div class="flex-1">
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Preview Jadwal</h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Tampilan jadwal berdasarkan data yang Anda masukkan.
                    </p>
                </div>
                <span id="previewStats"
                      class="hidden sm:inline-flex items-center gap-1.5
                             text-[11px] font-bold text-gray-500 dark:text-gray-400">
                    <i class="fas fa-circle-info"></i>
                    <span id="previewStatsText">0 hari aktif</span>
                </span>
            </header>

            <div class="p-5 overflow-x-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-3 min-w-[900px]">
                    @foreach($days as $key => $day)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500
                                        text-white text-center py-2.5 text-xs font-black
                                        tracking-wider uppercase">
                                {{ $day }}
                            </div>
                            <div id="preview-{{ $key }}"
                                 class="ssb-preview-day p-3 bg-gray-50 dark:bg-gray-900/40">
                                <div class="text-center text-[11px] text-gray-400 py-8">
                                    <i class="fas fa-inbox text-2xl opacity-30"></i>
                                    <p class="mt-2">Kosong</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════
             STEP 06 — OUTSIDE OPERATIONAL (conditional)
        ══════════════════════════════════════════════════════════ --}}
        <section id="outsideOperational"
                 class="hidden bg-orange-50 dark:bg-orange-900/20
                        border-2 border-orange-300 dark:border-orange-700/60
                        rounded-2xl p-5">

            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-orange-500 text-white
                            flex items-center justify-center shrink-0 shadow-md">
                    <i class="fas fa-triangle-exclamation text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-black text-sm text-orange-800 dark:text-orange-200">
                        Jadwal di luar jam operasional
                    </h3>
                    <p class="text-xs text-orange-700 dark:text-orange-300 mt-1 leading-relaxed">
                        Ada jadwal yang menggunakan ruangan di luar waktu operasional.
                        Jika tetap dilanjutkan, <b>alasan wajib diisi</b> untuk audit.
                    </p>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-200
                                  mt-4 mb-1.5">
                        Alasan Penggunaan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="outside_operational_reason" id="outsideReason"
                              rows="3" maxlength="2000"
                              placeholder="Contoh: Kegiatan organisasi membutuhkan ruangan sampai pukul 21.00 dan telah mendapatkan persetujuan..."
                              class="w-full px-3.5 py-3 rounded-xl
                                     border-2 border-orange-300 dark:border-orange-700
                                     bg-white dark:bg-gray-800 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-orange-500/30"></textarea>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════
             STEP 07 — UPLOAD GAMBAR
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">

            <header class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700
                           bg-gradient-to-r from-pink-50 to-rose-50
                           dark:from-pink-900/15 dark:to-rose-900/15
                           flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-pink-500 text-white
                             text-xs font-black flex items-center justify-center">06</span>
                <div class="flex-1">
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">
                        Lampiran Gambar Jadwal
                    </h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Opsional · Maksimal 4 file · JPG/PNG/WEBP · 2MB per file.
                    </p>
                </div>
            </header>

            <div class="p-5">
                <label for="scheduleImages"
                       class="block border-2 border-dashed border-gray-300 dark:border-gray-600
                              hover:border-pink-500 rounded-2xl p-8 text-center cursor-pointer
                              transition">
                    <input type="file" id="scheduleImages" name="schedule_images[]" multiple
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           class="hidden">
                    <i class="fas fa-cloud-upload-alt text-4xl text-pink-500"></i>
                    <p class="mt-3 font-bold text-sm text-gray-700 dark:text-gray-200">
                        Klik untuk pilih gambar
                    </p>
                    <p class="text-[11px] text-gray-400 mt-1">
                        Maks 4 file · JPG, PNG, WEBP · 2MB/file
                    </p>
                </label>

                <div id="imagePreview"
                     class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4"></div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════
             STEP 08 — CATATAN
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">
            <div class="p-5">
                <label for="notes"
                       class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Catatan Tambahan
                </label>
                <textarea id="notes" name="notes" rows="3" maxlength="2000"
                          placeholder="Catatan tambahan mengenai jadwal ruangan..."
                          class="w-full px-4 py-3 rounded-xl
                                 border border-gray-200 dark:border-gray-700
                                 bg-gray-50 dark:bg-gray-900 text-sm
                                 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════
             ACTION BUTTONS
        ══════════════════════════════════════════════════════════ --}}
        <section class="ssb-section bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700
                        shadow-sm p-4
                        flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
            <a href="{{ route('owner.schedules.index') }}"
               class="inline-flex items-center justify-center gap-2
                      px-5 py-3 rounded-xl
                      bg-gray-100 dark:bg-gray-700
                      hover:bg-gray-200 dark:hover:bg-gray-600
                      text-gray-700 dark:text-gray-200 text-sm font-bold transition">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="reset"
                    class="inline-flex items-center justify-center gap-2
                           px-5 py-3 rounded-xl
                           bg-amber-100 dark:bg-amber-900/30
                           hover:bg-amber-200 dark:hover:bg-amber-900/50
                           text-amber-700 dark:text-amber-300 text-sm font-bold transition">
                <i class="fas fa-undo"></i> Reset
            </button>

            <button type="submit" id="submitButton"
                    class="inline-flex items-center justify-center gap-2
                           px-6 py-3 rounded-xl
                           bg-gradient-to-r from-blue-500 to-indigo-600
                           hover:from-blue-600 hover:to-indigo-700
                           text-white text-sm font-black
                           shadow-lg shadow-blue-500/30
                           hover:shadow-blue-500/40
                           disabled:opacity-60 disabled:cursor-not-allowed
                           transition-all">
                <i class="fas fa-paper-plane" id="submitIcon"></i>
                <span id="submitText">Publikasikan Jadwal</span>
            </button>
        </section>

    </form>
</div>
@endsection
@push('scripts')
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       CONSTANTS
    ═══════════════════════════════════════════════════════════ */
    const AUDIENCE_DATA = {!! json_encode($audienceData) !!};
    const SEMESTER_OPTS = {!! json_encode($semesterOptions) !!};
    const DAY_NAMES = {
        monday: 'Senin', tuesday: 'Selasa', wednesday: 'Rabu',
        thursday: 'Kamis', friday: 'Jumat', saturday: 'Sabtu', sunday: 'Minggu'
    };

    const qs  = (s, r = document) => r.querySelector(s);
    const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    /* ═══════════════════════════════════════════════════════════
       ELEMENTS
    ═══════════════════════════════════════════════════════════ */
    const form          = qs('#scheduleForm');
    const roomSelect    = qs('#room_id');
    const roomInfo      = qs('#roomInformation');
    const roomName      = qs('#roomName');
    const roomCode      = qs('#roomCode');
    const roomFloor     = qs('#roomFloor');
    const roomCategory  = qs('#roomCategory');
    const opDays        = qs('#operationalDays');

    const semStart      = qs('#semester_start');
    const semEnd        = qs('#semester_end');
    const semRangeText  = qs('#semesterRangeText');
    const semRangeError = qs('#semesterRangeError');

    const outsideOper   = qs('#outsideOperational');
    const outsideReason = qs('#outsideReason');

    /* ═══════════════════════════════════════════════════════════
       SEMESTER RANGE
    ═══════════════════════════════════════════════════════════ */
    const semIdx = l => SEMESTER_OPTS.indexOf(l);

    function updateSemesterRange() {
        if (!semStart || !semEnd) return true;
        const s = semIdx(semStart.value);
        const e = semIdx(semEnd.value);
        semRangeText.textContent = `${semStart.value} s.d. ${semEnd.value}`;
        if (s > e) {
            semRangeError.classList.remove('hidden');
            semEnd.classList.add('border-rose-500');
            return false;
        }
        semRangeError.classList.add('hidden');
        semEnd.classList.remove('border-rose-500');
        return true;
    }
    semStart?.addEventListener('change', () => {
        if (semIdx(semStart.value) > semIdx(semEnd.value)) semEnd.value = semStart.value;
        updateSemesterRange();
    });
    semEnd?.addEventListener('change', updateSemesterRange);
    updateSemesterRange();

    /* ═══════════════════════════════════════════════════════════
       ROOM PICKER + OPERATIONAL RENDER
    ═══════════════════════════════════════════════════════════ */
    roomSelect?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) { roomInfo.classList.add('hidden'); return; }

        roomInfo.classList.remove('hidden');
        roomName.textContent     = opt.dataset.name || '—';
        roomCode.textContent     = opt.dataset.code || '—';
        roomFloor.textContent    = opt.dataset.floor || '—';
        roomCategory.textContent = opt.dataset.category || '—';

        renderOperational(opt.dataset.operational);
        checkAllOperational();
    });

    function parseOp(data) {
        try { return JSON.parse(data || '{}'); } catch (e) { return {}; }
    }

    function renderOperational(data) {
        opDays.innerHTML = '';
        const op = parseOp(data);

        Object.entries(DAY_NAMES).forEach(([key, label]) => {
            const val = op[key];
            let start = null, end = null;

            if (val && typeof val === 'object') {
                start = val.start || val.open || null;
                end   = val.end   || val.close || null;
            } else if (typeof val === 'string' && val.includes('-')) {
                [start, end] = val.split('-').map(s => s.trim());
            }

            const isOpen = !!(start && end);
            const card = document.createElement('div');
            card.className = isOpen
                ? 'p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50'
                : 'p-2.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/50';
            card.innerHTML = `
                <p class="text-[10px] font-black uppercase ${isOpen ? 'text-emerald-600' : 'text-rose-500'}">
                    ${label}
                </p>
                <p class="text-xs font-bold mt-1 text-gray-800 dark:text-white">
                    ${isOpen ? `${escapeHtml(start)} – ${escapeHtml(end)}` : 'Tutup'}
                </p>`;
            opDays.appendChild(card);
        });
    }

    function getOpForDay(day) {
        const opt = roomSelect.options[roomSelect.selectedIndex];
        if (!opt || !opt.value) return null;
        const op = parseOp(opt.dataset.operational);
        const val = op[day];
        if (!val) return null;
        if (typeof val === 'object') {
            return { start: val.start || val.open || null, end: val.end || val.close || null };
        }
        if (typeof val === 'string' && val.includes('-')) {
            const [s, e] = val.split('-').map(x => x.trim());
            return { start: s, end: e };
        }
        return null;
    }

    /* ═══════════════════════════════════════════════════════════
       OPERATIONAL CHECK — PER ACTIVITY
    ═══════════════════════════════════════════════════════════ */
    function checkAllOperational() {
        let outside = false;

        qsa('.ssb-day').forEach(card => {
            const cb = qs('.day-enabled', card);
            const status = qs('.day-status', card);
            if (!status) return;

            status.innerHTML = '';

            if (!cb.checked) return;

            const day = card.dataset.day;
            const activities = qsa('.activity-card', card);
            const problems = [];

            activities.forEach((actCard, idx) => {
                const start = qs('.activity-start', actCard)?.value;
                const end   = qs('.activity-end', actCard)?.value;
                if (!start || !end) return;

                const op = getOpForDay(day);

                if (!op || !op.start || !op.end) {
                    outside = true;
                    problems.push(`Tahap ${idx + 1}: ${DAY_NAMES[day]} tidak memiliki jam operasional.`);
                    return;
                }
                if (start < op.start || end > op.end) {
                    outside = true;
                    problems.push(`Tahap ${idx + 1}: ${start}–${end} di luar operasional (${op.start}–${op.end}).`);
                }
            });

            if (problems.length > 0) {
                status.innerHTML = `
                    <div class="p-3 rounded-xl bg-orange-100 dark:bg-orange-900/30
                                border border-orange-300 dark:border-orange-700/50">
                        <p class="text-xs font-bold text-orange-700 dark:text-orange-300 mb-1.5">
                            <i class="fas fa-clock"></i>
                            ${problems.length} kegiatan di luar jam operasional
                        </p>
                        <ul class="ml-4 list-disc text-[11px] text-orange-600 dark:text-orange-400 space-y-0.5">
                            ${problems.map(p => `<li>${escapeHtml(p)}</li>`).join('')}
                        </ul>
                    </div>`;
            } else if (activities.length > 0) {
                status.innerHTML = `
                    <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20
                                border border-emerald-200 dark:border-emerald-700/50">
                        <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300">
                            <i class="fas fa-check-circle"></i>
                            Semua kegiatan sesuai jam operasional.
                        </p>
                    </div>`;
            }
        });

        if (outside) {
            outsideOper.classList.remove('hidden');
            if (outsideReason) outsideReason.required = true;
        } else {
            outsideOper.classList.add('hidden');
            if (outsideReason) outsideReason.required = false;
        }
    }

    /* ═══════════════════════════════════════════════════════════
       ACTIVITY CARD BUILDER
    ═══════════════════════════════════════════════════════════ */
    function buildOptions(type, prefix) {
        const items = AUDIENCE_DATA[type] || [];
        const field = type === 'generasi' ? 'generation_ids' : 'kelompok_ids';

        if (items.length === 0) {
            return `<p class="col-span-full text-[10px] italic text-gray-400 p-2">
                Belum ada data ${type}.
            </p>`;
        }

        return items.map(item => `
            <label class="activity-check-item cursor-pointer flex items-center gap-1.5
                          p-1.5 rounded-lg border border-gray-200 dark:border-gray-700
                          bg-white dark:bg-gray-800
                          hover:border-indigo-400 transition">
                <input type="checkbox"
                       name="${prefix}[${field}][]"
                       value="${item.id}"
                       class="w-3.5 h-3.5 rounded text-indigo-600">
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-200 truncate">
                    ${escapeHtml(item.name)}
                </span>
            </label>`).join('');
    }

    function buildActivityHTML(dayKey, stage) {
        const P = `schedules[${dayKey}][activities][${stage}]`;

        return `
        <div class="activity-card rounded-xl border-2 border-gray-200 dark:border-gray-700
                    overflow-hidden bg-white dark:bg-gray-800/40"
             data-stage="${stage}">

            {{-- Header Tahap --}}
            <div class="activity-header flex items-center justify-between gap-2
                        px-3 py-2.5
                        bg-gradient-to-r from-blue-50 to-indigo-50
                        dark:from-blue-900/20 dark:to-indigo-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 flex-wrap min-w-0 flex-1">
                    <span class="stage-badge inline-flex items-center gap-1
                                 px-2.5 py-1 rounded-full
                                 bg-blue-600 text-white
                                 text-[10px] font-black tracking-wider shrink-0">
                        <i class="fas fa-layer-group"></i> Tahap ${stage}
                    </span>
                    <input type="text"
                           name="${P}[stage_name]"
                           placeholder="Nama tahap (contoh: Pembukaan)"
                           class="stage-name flex-1 min-w-[180px] px-3 py-1.5 rounded-lg
                                  border border-gray-200 dark:border-gray-600
                                  bg-white dark:bg-gray-900 text-xs font-semibold
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                </div>
                <button type="button"
                        class="remove-activity-btn inline-flex items-center gap-1
                               px-2.5 py-1.5 rounded-lg
                               bg-rose-50 dark:bg-rose-900/30
                               text-rose-600 dark:text-rose-300
                               hover:bg-rose-100 dark:hover:bg-rose-900/50
                               text-[11px] font-bold transition shrink-0">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            {{-- Body --}}
            <div class="p-3.5 space-y-3">

                {{-- Baris 1: jenis, waktu, person --}}
                <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1.5">
                            Jenis
                        </label>
                        <select name="${P}[activity_type]"
                                class="activity-type w-full px-3 py-2 rounded-lg
                                       border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-900 text-xs">
                            <option value="Kuliah">Kuliah</option>
                            <option value="Rapat">Rapat</option>
                            <option value="Praktikum">Praktikum</option>
                            <option value="Pelatihan">Pelatihan</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Organisasi">Organisasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1.5">
                            Mulai
                        </label>
                        <input type="time" name="${P}[start_time]"
                               class="activity-start w-full px-3 py-2 rounded-lg
                                      border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-900 text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1.5">
                            Selesai
                        </label>
                        <input type="time" name="${P}[end_time]"
                               class="activity-end w-full px-3 py-2 rounded-lg
                                      border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-900 text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1.5">
                            Nama Pengguna
                        </label>
                        <input type="text" name="${P}[person]"
                               placeholder="Contoh: Pak Budi"
                               class="activity-person w-full px-3 py-2 rounded-lg
                                      border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-900 text-xs">
                    </div>
                </div>

                {{-- Baris 2: generasi & kelompok --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <div class="p-3 rounded-xl
                                bg-amber-50/60 dark:bg-amber-900/10
                                border border-amber-200 dark:border-amber-700/50">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-300">
                                <i class="fas fa-layer-group"></i> Generasi yang Bergabung
                            </p>
                            <button type="button"
                                    class="toggle-all-generations
                                           text-[10px] font-bold
                                           text-amber-700 dark:text-amber-300
                                           hover:underline">
                                Pilih Semua
                            </button>
                        </div>
                        <div class="generation-options grid grid-cols-2 gap-1.5
                                    max-h-40 overflow-y-auto pr-1">
                            ${buildOptions('generasi', P)}
                        </div>
                    </div>

                    <div class="p-3 rounded-xl
                                bg-blue-50/60 dark:bg-blue-900/10
                                border border-blue-200 dark:border-blue-700/50">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-black uppercase text-blue-700 dark:text-blue-300">
                                <i class="fas fa-users"></i> Kelompok
                            </p>
                            <button type="button"
                                    class="toggle-all-kelompok
                                           text-[10px] font-bold
                                           text-blue-700 dark:text-blue-300
                                           hover:underline">
                                Pilih Semua
                            </button>
                        </div>
                        <div class="kelompok-options grid grid-cols-2 gap-1.5
                                    max-h-40 overflow-y-auto pr-1">
                            ${buildOptions('kelompok', P)}
                        </div>
                    </div>
                </div>

                {{-- Baris 3: keterangan --}}
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-1.5">
                        Keterangan
                    </label>
                    <textarea name="${P}[description]" rows="2"
                              placeholder="Keterangan tambahan untuk tahap ini..."
                              class="activity-description w-full px-3 py-2 rounded-lg
                                     border border-gray-200 dark:border-gray-700
                                     bg-gray-50 dark:bg-gray-900 text-xs resize-none"></textarea>
                </div>
            </div>
        </div>`;
    }

    /* ═══════════════════════════════════════════════════════════
       ACTIVITY MANAGEMENT
    ═══════════════════════════════════════════════════════════ */
    function updateDayStageCount(dayKey) {
        const card = qs(`.ssb-day[data-day="${dayKey}"]`);
        const badge = qs('.day-stage-count', card);
        const text  = qs('.count-text', badge);
        const n = qsa('.activity-card', qs('.day-activities', card)).length;

        if (n > 0) {
            badge.classList.remove('hidden');
            text.textContent = `${n} tahap`;
        } else {
            badge.classList.add('hidden');
        }
    }

    function renumberActivities(dayKey) {
        const container = qs(`.day-activities[data-day="${dayKey}"]`);
        if (!container) return;

        qsa('.activity-card', container).forEach((card, idx) => {
            const stage = idx + 1;
            card.dataset.stage = stage;

            const badge = qs('.stage-badge', card);
            if (badge) badge.innerHTML = `<i class="fas fa-layer-group"></i> Tahap ${stage}`;

            // Rename all name attributes
            qsa('[name]', card).forEach(el => {
                el.name = el.name.replace(
                    new RegExp(`schedules\\[${dayKey}\\]\\[activities\\]\\[\\d+\\]`),
                    `schedules[${dayKey}][activities][${stage}]`
                );
            });
        });
    }

    function attachActivityEvents(card) {
        const dayKey = card.closest('.ssb-day').dataset.day;

        // Remove
        qs('.remove-activity-btn', card)?.addEventListener('click', () => {
            if (!confirm('Hapus tahap kegiatan ini?')) return;
            card.remove();
            renumberActivities(dayKey);
            updateDayStageCount(dayKey);
            checkAllOperational();
            updatePreview();
        });

        // Toggle all generations
        qs('.toggle-all-generations', card)?.addEventListener('click', e => {
            const boxes = qsa('.generation-options input[type="checkbox"]', card);
            const allChecked = boxes.every(b => b.checked);
            boxes.forEach(b => b.checked = !allChecked);
            e.currentTarget.textContent = allChecked ? 'Pilih Semua' : 'Kosongkan';
        });

        // Toggle all kelompok
        qs('.toggle-all-kelompok', card)?.addEventListener('click', e => {
            const boxes = qsa('.kelompok-options input[type="checkbox"]', card);
            const allChecked = boxes.every(b => b.checked);
            boxes.forEach(b => b.checked = !allChecked);
            e.currentTarget.textContent = allChecked ? 'Pilih Semua' : 'Kosongkan';
        });

        // Live updates
        qsa('.activity-start, .activity-end, .activity-person, .stage-name, .activity-type',
            card).forEach(el => {
            el.addEventListener('input',  () => { checkAllOperational(); updatePreview(); });
            el.addEventListener('change', () => { checkAllOperational(); updatePreview(); });
        });
    }

    function addActivity(dayKey, data = null) {
        const container = qs(`.day-activities[data-day="${dayKey}"]`);
        if (!container) return;

        const stage = qsa('.activity-card', container).length + 1;
        container.insertAdjacentHTML('beforeend', buildActivityHTML(dayKey, stage));

        const newCard = container.lastElementChild;

        if (data) {
            const setVal = (sel, val) => { const el = qs(sel, newCard); if (el) el.value = val || ''; };
            setVal('.stage-name', data.stage_name);
            setVal('.activity-type', data.activity_type || 'Kuliah');
            setVal('.activity-start', data.start_time);
            setVal('.activity-end', data.end_time);
            setVal('.activity-person', data.person);
            setVal('.activity-description', data.description);

            (data.generation_ids || []).forEach(id => {
                const cb = qs(`.generation-options input[value="${id}"]`, newCard);
                if (cb) cb.checked = true;
            });
            (data.kelompok_ids || []).forEach(id => {
                const cb = qs(`.kelompok-options input[value="${id}"]`, newCard);
                if (cb) cb.checked = true;
            });
        }

        attachActivityEvents(newCard);
        updateDayStageCount(dayKey);
        checkAllOperational();
        updatePreview();
    }

    // Event delegation: klik tombol "Tambah Kegiatan"
    document.addEventListener('click', e => {
        const btn = e.target.closest('.add-activity-btn');
        if (!btn) return;
        const dayKey = btn.closest('.ssb-day').dataset.day;
        addActivity(dayKey);
    });

    /* ═══════════════════════════════════════════════════════════
       DAY TOGGLE
    ═══════════════════════════════════════════════════════════ */
    qsa('.day-enabled').forEach(cb => {
        cb.addEventListener('change', function () {
            const card = this.closest('.ssb-day');
            const dayKey = card.dataset.day;
            const formEl = qs('.day-form', card);
            const container = qs('.day-activities', card);

            if (this.checked) {
                formEl.classList.remove('hidden');
                card.classList.add('is-active');

                // Auto-create tahap 1 kalau masih kosong
                if (qsa('.activity-card', container).length === 0) {
                    addActivity(dayKey);
                }
            } else {
                formEl.classList.add('hidden');
                card.classList.remove('is-active');
                container.innerHTML = '';
                qs('.day-status', card).innerHTML = '';
            }

            updateDayStageCount(dayKey);
            checkAllOperational();
            updatePreview();
        });
    });

    /* ═══════════════════════════════════════════════════════════
       PREVIEW
    ═══════════════════════════════════════════════════════════ */
    function updatePreview() {
        let activeDays = 0;
        let totalActivities = 0;

        Object.keys(DAY_NAMES).forEach(day => {
            const preview = qs(`#preview-${day}`);
            const card    = qs(`.ssb-day[data-day="${day}"]`);
            if (!preview || !card) return;

            const enabled = qs('.day-enabled', card)?.checked;
            preview.innerHTML = '';

            if (!enabled) {
                preview.innerHTML = `
                    <div class="text-center text-[11px] text-gray-400 py-8">
                        <i class="fas fa-inbox text-2xl opacity-30"></i>
                        <p class="mt-2">Kosong</p>
                    </div>`;
                return;
            }

            const activities = qsa('.activity-card', card);
            activeDays++;

            if (activities.length === 0) {
                preview.innerHTML = `
                    <div class="text-center text-[11px] text-gray-400 py-8">
                        <i class="fas fa-hourglass-start text-2xl opacity-30"></i>
                        <p class="mt-2">Belum ada tahap</p>
                    </div>`;
                return;
            }

            const blocks = activities.map((actCard, idx) => {
                totalActivities++;
                const stageName = qs('.stage-name', actCard)?.value || 'Tanpa nama tahap';
                const person    = qs('.activity-person', actCard)?.value || '—';
                const start     = qs('.activity-start', actCard)?.value || '--:--';
                const end       = qs('.activity-end', actCard)?.value || '--:--';
                const actType   = qs('.activity-type', actCard)?.value || 'Umum';

                return `
                    <div class="p-2.5 rounded-lg bg-white dark:bg-gray-800
                                border border-gray-200 dark:border-gray-700 shadow-sm mb-2 last:mb-0">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                                         bg-blue-100 dark:bg-blue-900/30
                                         text-blue-700 dark:text-blue-300
                                         text-[9px] font-black tracking-wider">
                                <i class="fas fa-layer-group"></i> Tahap ${idx + 1}
                            </span>
                            <span class="text-[9px] font-black text-blue-600 dark:text-blue-400">
                                ${escapeHtml(start)}–${escapeHtml(end)}
                            </span>
                        </div>
                        <p class="text-[11px] font-bold text-gray-800 dark:text-white leading-tight break-words">
                            ${escapeHtml(stageName)}
                        </p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                            <i class="fas fa-user text-[8px]"></i>${escapeHtml(person)}
                        </p>
                        <span class="ssb-chip mt-1.5 inline-flex
                                     bg-blue-50 dark:bg-blue-900/30
                                     text-blue-700 dark:text-blue-300">
                            ${escapeHtml(actType)}
                        </span>
                    </div>`;
            }).join('');

            preview.innerHTML = blocks;
        });

        const statsEl = qs('#previewStatsText');
        if (statsEl) {
            statsEl.textContent = `${activeDays} hari aktif · ${totalActivities} kegiatan`;
        }
    }

    /* ═══════════════════════════════════════════════════════════
       COLLAPSE ALL
    ═══════════════════════════════════════════════════════════ */
    qs('#collapseAllDays')?.addEventListener('click', () => {
        qsa('.ssb-day').forEach(card => {
            const cb = qs('.day-enabled', card);
            if (cb.checked) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    /* ═══════════════════════════════════════════════════════════
       RESET DAYS
    ═══════════════════════════════════════════════════════════ */
    qs('#resetDays')?.addEventListener('click', () => {
        if (!confirm('Reset semua jadwal harian?')) return;
        qsa('.ssb-day').forEach(card => {
            const cb = qs('.day-enabled', card);
            if (cb.checked) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    /* ═══════════════════════════════════════════════════════════
       AUDIENCE PICKER (top-level target notifikasi)
    ═══════════════════════════════════════════════════════════ */
    const audienceDetail      = qs('#audienceDetail');
    const audienceOptionsEl   = qs('#audienceOptions');
    const audienceDetailLabel = qs('#audienceDetailLabel');
    const audienceCountText   = qs('#audienceCountText');
    const audienceSearch      = qs('#audienceSearch');

    let currentAudienceType = 'all';

    function renderAudienceOptions(type, keyword = '') {
        currentAudienceType = type;

        if (type === 'all') { audienceDetail.classList.add('hidden'); return; }

        audienceDetail.classList.remove('hidden');
        audienceDetailLabel.textContent = 'Pilih target ' + type.replace('_', ' ');

        let list = AUDIENCE_DATA[type] || [];
        if (keyword) {
            const kw = keyword.toLowerCase();
            list = list.filter(item => (item.name || '').toLowerCase().includes(kw));
        }

        if (list.length === 0) {
            audienceOptionsEl.innerHTML = `
                <div class="col-span-full p-4 rounded-xl
                            bg-amber-50 dark:bg-amber-900/20
                            border border-amber-200 dark:border-amber-700/50 text-center">
                    <i class="fas fa-circle-info text-amber-500"></i>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                        ${keyword ? 'Tidak ada hasil untuk pencarian.' : 'Belum ada data ' + type.replace('_', ' ') + '.'}
                    </p>
                </div>`;
            updateAudienceCount();
            return;
        }

        audienceOptionsEl.innerHTML = list.map(item => `
            <label class="ssb-audience-item cursor-pointer flex items-center gap-2
                          p-2.5 rounded-lg border border-gray-200 dark:border-gray-700
                          bg-white dark:bg-gray-800 hover:border-indigo-400 transition">
                <input type="checkbox" name="audience_ids[]" value="${item.id}"
                       class="audience-checkbox w-4 h-4 rounded text-indigo-600">
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">
                    ${escapeHtml(item.name)}
                </span>
            </label>`).join('');

        qsa('.audience-checkbox', audienceOptionsEl).forEach(cb => {
            cb.addEventListener('change', updateAudienceCount);
        });

        updateAudienceCount();
    }

    function updateAudienceCount() {
        const n = qsa('.audience-checkbox:checked', audienceOptionsEl).length;
        if (audienceCountText) audienceCountText.textContent = n + ' penerima terpilih';
    }

    qsa('.audience-radio').forEach(r => {
        r.addEventListener('change', () => {
            if (r.checked) {
                if (audienceSearch) audienceSearch.value = '';
                renderAudienceOptions(r.value);
            }
        });
    });

    const initialAudience = qs('.audience-radio:checked');
    if (initialAudience) renderAudienceOptions(initialAudience.value);

    audienceSearch?.addEventListener('input', () => {
        renderAudienceOptions(currentAudienceType, audienceSearch.value.trim());
    });

    qs('#selectAllAudience')?.addEventListener('click', () => {
        const all = qsa('.audience-checkbox', audienceOptionsEl);
        if (!all.length) return;
        const allChecked = all.every(cb => cb.checked);
        all.forEach(cb => cb.checked = !allChecked);
        updateAudienceCount();
    });

    /* ═══════════════════════════════════════════════════════════
       ATTENDANCE MODE
    ═══════════════════════════════════════════════════════════ */
    const attOptions = qs('#attendanceOptions');
    qsa('.attendance-radio').forEach(r => {
        r.addEventListener('change', () => {
            if (r.checked) attOptions.classList.toggle('hidden', r.value === 'none');
        });
    });
    const initialAtt = qs('.attendance-radio:checked');
    if (initialAtt && initialAtt.value !== 'none') attOptions.classList.remove('hidden');

    /* ═══════════════════════════════════════════════════════════
       IMAGE PREVIEW
    ═══════════════════════════════════════════════════════════ */
    const imgInput   = qs('#scheduleImages');
    const imgPreview = qs('#imagePreview');
    const ALLOWED    = ['image/jpeg', 'image/png', 'image/webp'];
    const MAX_SIZE   = 2 * 1024 * 1024;

    imgInput?.addEventListener('change', function () {
        const files = Array.from(this.files || []);
        if (files.length > 4) {
            alert('Maksimal 4 gambar.');
            this.value = '';
            imgPreview.innerHTML = '';
            return;
        }
        imgPreview.innerHTML = '';
        files.forEach(f => {
            if (!ALLOWED.includes(f.type)) { alert(`${f.name} bukan format gambar yang diperbolehkan.`); return; }
            if (f.size > MAX_SIZE) { alert(`${f.name} melebihi 2MB.`); return; }

            const reader = new FileReader();
            reader.onload = ev => {
                const div = document.createElement('div');
                div.className = 'rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700';
                div.innerHTML = `
                    <img src="${ev.target.result}" class="w-full h-32 object-cover">
                    <p class="p-2 text-[10px] text-gray-500 truncate">${escapeHtml(f.name)}</p>`;
                imgPreview.appendChild(div);
            };
            reader.readAsDataURL(f);
        });
    });

    /* ═══════════════════════════════════════════════════════════
       SUBMIT VALIDATION
    ═══════════════════════════════════════════════════════════ */
    const btnSubmit = qs('#submitButton');

    form?.addEventListener('submit', e => {
        // Semester
        if (!updateSemesterRange()) {
            e.preventDefault();
            alert('Rentang semester tidak valid.');
            semEnd?.focus();
            return;
        }

        // Minimal 1 hari aktif
        const activeDays = qsa('.day-enabled:checked');
        if (activeDays.length === 0) {
            e.preventDefault();
            alert('Minimal satu hari harus memiliki jadwal.');
            return;
        }

        // Validasi setiap kegiatan
        let invalid = false;
        let invalidMsg = '';

        qsa('.ssb-day').forEach(card => {
            if (!qs('.day-enabled', card).checked) return;
            const dayLabel = DAY_NAMES[card.dataset.day];
            const activities = qsa('.activity-card', card);

            if (activities.length === 0) {
                invalid = true;
                invalidMsg = `Hari ${dayLabel} aktif tapi belum punya kegiatan.`;
                return;
            }

            const toMin = t => { const [h, m] = t.split(':').map(Number); return h * 60 + m; };

            activities.forEach((actCard, idx) => {
                const start = qs('.activity-start', actCard)?.value;
                const end   = qs('.activity-end', actCard)?.value;

                if (!start || !end) {
                    invalid = true;
                    invalidMsg = `Tahap ${idx + 1} di hari ${dayLabel}: jam mulai & selesai wajib diisi.`;
                    return;
                }
                if (toMin(start) >= toMin(end)) {
                    invalid = true;
                    invalidMsg = `Tahap ${idx + 1} di hari ${dayLabel}: jam selesai harus lebih besar dari jam mulai.`;
                }
            });
        });

        if (invalid) {
            e.preventDefault();
            alert(invalidMsg);
            return;
        }

        // Outside operational
        checkAllOperational();
        if (!outsideOper.classList.contains('hidden')
            && outsideReason && !outsideReason.value.trim()) {
            e.preventDefault();
            outsideReason.focus();
            alert('Jadwal di luar jam operasional. Alasan wajib diisi.');
            return;
        }

        // Audience top-level
        const audType = qs('.audience-radio:checked')?.value || 'all';
        if (audType !== 'all') {
            const checked = qsa('.audience-checkbox:checked', audienceOptionsEl).length;
            if (checked === 0) {
                e.preventDefault();
                alert(`Pilih minimal satu target untuk audience "${audType}".`);
                return;
            }
        }

        // Loading state
        if (btnSubmit) {
            btnSubmit.disabled = true;
            const icon = qs('#submitIcon');
            const text = qs('#submitText');
            if (icon) icon.className = 'fas fa-circle-notch fa-spin';
            if (text) text.textContent = 'Menyimpan...';
        }
    });

    /* ═══════════════════════════════════════════════════════════
       INIT PREVIEW
    ═══════════════════════════════════════════════════════════ */
    updatePreview();

})();
</script>
@endpush