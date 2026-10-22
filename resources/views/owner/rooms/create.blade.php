@extends('layouts.roles.owner')

@section('title', 'Tambah Ruangan – Owner')

@php
    $tz = 'Asia/Jakarta';
@endphp

@push('styles')
<style>
    .form-section {
        transition: all .3s ease;
        scroll-margin-top: 80px;
    }
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,.08);
    }
    .input-group-focus:focus-within {
        border-color: #06b6d4;
        box-shadow: 0 0 0 4px rgba(6, 182, 212, .15);
    }
    .step-dot {
        width: 40px;
        height: 4px;
        border-radius: 4px;
        background: #e5e7eb;
        transition: all .3s;
    }
    .step-dot.active { background: #3b82f6; }
    .step-dot.done   { background: #10b981; }

    .check-icon.valid   { color: #10b981; }
    .check-icon.invalid { color: #ef4444; }
    .check-icon.loading { color: #f59e0b; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .file-preview-item { position: relative; transition: all .3s; }
    .file-preview-item:hover .remove-file { opacity: 1; }
    .remove-file { opacity: 0; transition: opacity .3s; }

    .fade-in { animation: fadeInUp .5s ease-out; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .qr-preview {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,.1);
        display: inline-block;
    }

    /* ═══════════════════════════════════════════════════════════
       CUSTOM FACILITY ROW
    ═══════════════════════════════════════════════════════════ */
    .cf-row {
        display: grid;
        grid-template-columns: 1fr 100px 40px;
        gap: .5rem;
        align-items: center;
        padding: .6rem .75rem;
        border-radius: .75rem;
        background: linear-gradient(135deg, rgb(240 249 255) 0%, rgb(238 242 255) 100%);
        border: 1px solid rgb(199 210 254);
        transition: all .15s ease;
    }
    .cf-row:hover {
        box-shadow: 0 4px 12px -4px rgba(99, 102, 241, .15);
    }
    .dark .cf-row {
        background: linear-gradient(135deg, rgb(30 41 59 / .5) 0%, rgb(49 46 129 / .2) 100%);
        border-color: rgb(99 102 241 / .3);
    }
    .cf-row.is-removing {
        animation: cfRemove .25s ease forwards;
    }
    @keyframes cfRemove {
        to { opacity: 0; transform: translateX(-8px) scale(.97); }
    }

    @media (max-width: 480px) {
        .cf-row {
            grid-template-columns: 1fr 60px 36px;
            padding: .5rem;
        }
    }

    /* ═══════════════════════════════════════════════════════════
       ORIENTATION-AWARE
    ═══════════════════════════════════════════════════════════ */
    @media (max-width: 767.98px) and (orientation: portrait) {
        .facility-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
    @media (max-width: 900px) and (orientation: landscape) {
        .facility-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto ssb-shell">

    {{-- ═══════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3">
        <div class="min-w-0">
            <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white
                       flex items-center gap-3">
                <i class="fas fa-plus-circle text-cyan-500"></i>
                <span class="min-w-0 truncate">Tambah Ruangan</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Daftarkan ruangan baru dengan sistem cerdas.
            </p>
        </div>
        <a href="{{ route('owner.rooms.index') }}"
           class="px-4 py-2 rounded-lg shrink-0
                  bg-gray-200 dark:bg-gray-700
                  hover:bg-gray-300 dark:hover:bg-gray-600
                  text-gray-700 dark:text-gray-200
                  text-sm font-bold transition
                  inline-flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         STEP INDICATOR
    ═══════════════════════════════════════════════════════ --}}
    <div class="step-indicator flex gap-2 mb-6">
        @for($i = 1; $i <= 7; $i++)
            <div class="step-dot {{ $i == 1 ? 'active' : '' }}" data-step="{{ $i }}"></div>
        @endfor
    </div>

    {{-- ═══════════════════════════════════════════════════════
         FLASH
    ═══════════════════════════════════════════════════════ --}}
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl
                    bg-red-100 dark:bg-red-900/30
                    border-l-4 border-red-500
                    text-red-700 dark:text-red-300 text-sm">
            <i class="fas fa-circle-exclamation mr-1"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl
                    bg-red-50 dark:bg-red-900/20
                    border border-red-200 dark:border-red-700/40">
            <p class="text-sm font-bold text-red-700 dark:text-red-300 mb-2">
                Ada {{ $errors->count() }} kesalahan:
            </p>
            <ul class="ml-4 list-disc text-xs text-red-600 dark:text-red-300 space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         MAIN FORM
    ═══════════════════════════════════════════════════════ --}}
    <form action="{{ route('owner.rooms.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="roomForm"
          novalidate
          class="space-y-6">

        @csrf

        {{-- ══════════════════════════════════════════════════
             SECTION 1 — IDENTITAS
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-1">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">1</span>
                    Identitas Ruangan
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="roomName"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Ruangan <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group-focus mt-1 flex items-center rounded-lg
                                border border-gray-300 dark:border-gray-600
                                bg-gray-50 dark:bg-gray-700
                                focus-within:ring-2 focus-within:ring-cyan-500">
                        <span class="pl-3 text-gray-400"><i class="fas fa-tag"></i></span>
                        <input type="text" name="name" id="roomName"
                               value="{{ old('name') }}"
                               class="w-full rounded-r-lg border-0 bg-transparent
                                      px-4 py-2 text-gray-900 dark:text-white
                                      focus:outline-none"
                               placeholder="Contoh: Ruang Rapat Utama"
                               required minlength="3" maxlength="255">
                    </div>
                    <div id="nameFeedback" class="text-xs mt-1 flex items-center gap-1">
                        <span id="nameIcon" class="check-icon">
                            <i class="fas fa-circle text-gray-300"></i>
                        </span>
                        <span id="nameMessage" class="text-gray-400">
                            Periksa ketersediaan nama
                        </span>
                    </div>
                </div>

                <div>
                    <label for="roomCategory"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kategori
                    </label>
                    <select name="category" id="roomCategory"
                            class="mt-1 w-full rounded-lg
                                   border-gray-300 dark:border-gray-600
                                   bg-gray-50 dark:bg-gray-700
                                   text-gray-900 dark:text-white
                                   px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        @foreach([
                            'Ruang Rapat', 'Ruang Kelas', 'Laboratorium', 'Studio',
                            'Aula', 'Sekretariat', 'Ruang Penyimpanan', 'Ruang Kreatif',
                            'Ruang Serbaguna', 'Ruang Komputer', 'Ruang Podcast', 'Lainnya'
                        ] as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        Kode ruangan akan dibuat otomatis dari kategori, lantai, dan kapasitas.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label for="description"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="2" maxlength="2000"
                              class="mt-1 w-full rounded-lg
                                     border-gray-300 dark:border-gray-600
                                     bg-gray-50 dark:bg-gray-700
                                     text-gray-900 dark:text-white
                                     px-4 py-2 focus:ring-2 focus:ring-blue-500"
                              placeholder="Deskripsi singkat ruangan...">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-blue-50 dark:bg-blue-900/10
                                border border-blue-200 dark:border-blue-700/30
                                rounded-lg p-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2 flex-wrap">
                            <i class="fas fa-robot"></i>
                            <strong>Smart Detect:</strong>
                            <span id="smartCodePreview">Kode akan muncul otomatis</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 2 — LOKASI
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-2">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">2</span>
                    Lokasi
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="building"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Gedung <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="building" id="building"
                           value="{{ old('building') }}" required maxlength="255"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Gedung A">
                </div>

                <div>
                    <label for="floor"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Lantai <span class="text-red-500">*</span>
                    </label>
                    <select name="floor" id="floor" required
                            class="mt-1 w-full rounded-lg
                                   border-gray-300 dark:border-gray-600
                                   bg-gray-50 dark:bg-gray-700
                                   text-gray-900 dark:text-white
                                   px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Lantai</option>
                        @foreach([
                            'Basement 3', 'Basement 2', 'Basement 1',
                            'Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4', 'Lantai 5',
                            'Lantai 6', 'Lantai 7', 'Lantai 8', 'Lantai 9', 'Lantai 10',
                            'Lantai 11', 'Lantai 12', 'Loteng', 'Aula'
                        ] as $floor)
                            <option value="{{ $floor }}" {{ old('floor') == $floor ? 'selected' : '' }}>
                                {{ $floor }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="address"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Alamat Lengkap
                    </label>
                    <input type="text" name="address" id="address"
                           value="{{ old('address') }}" maxlength="500"
                           placeholder="Jl. Contoh No. 123, Kampus"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 3 — KAPASITAS
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-3">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">3</span>
                    Kapasitas
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="capacityNormal"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kapasitas Normal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacity_normal" id="capacityNormal"
                           value="{{ old('capacity_normal') }}" required min="1"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="capacityMax"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kapasitas Maksimum
                    </label>
                    <input type="number" name="capacity_max" id="capacityMax"
                           value="{{ old('capacity_max') }}" min="0"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">
                        Kosongkan jika sama dengan kapasitas normal.
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 4 — FASILITAS (+ CUSTOM)
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-4">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700
                        flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">4</span>
                    Fasilitas
                </h2>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full
                             bg-indigo-100 dark:bg-indigo-900/30
                             text-indigo-600 dark:text-indigo-300">
                    <i class="fas fa-sliders-h text-[9px]"></i> PRESET + CUSTOM
                </span>
            </div>

            <div class="p-6 space-y-6">

                {{-- ═══ PRESET FACILITIES ═══ --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-list-check text-blue-500"></i>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                            Fasilitas Standar
                        </p>
                        <span class="text-[10px] text-gray-400 italic">
                            Centang &amp; atur jumlah
                        </span>
                    </div>

                    @php
                        $facilityOptions = [
                            'AC', 'WiFi', 'Proyektor', 'Whiteboard', 'Sound System',
                            'Smart TV', 'Kamera', 'Mikrofon', 'Komputer', 'Printer',
                            'CCTV', 'Meja', 'Kursi', 'Lemari', 'Papan Tulis',
                            'Speaker', 'Dispenser', 'Kipas Angin', 'Pemanas', 'Pendingin',
                            'Rak Buku', 'Panggung', 'Layar', 'Podium', 'Karpet', 'Gorden'
                        ];
                    @endphp

                    <div class="facility-grid grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($facilityOptions as $idx => $fac)
                            <div class="flex items-center gap-2 p-2 rounded-lg
                                        bg-gray-50 dark:bg-gray-700/30
                                        hover:bg-blue-50 dark:hover:bg-blue-900/10
                                        transition">
                                <input type="checkbox"
                                       name="facilities[{{ $idx }}][name]"
                                       value="{{ $fac }}"
                                       id="fac-{{ $idx }}"
                                       data-facility-index="{{ $idx }}"
                                       class="facility-check rounded
                                              border-gray-300 text-blue-600
                                              focus:ring-blue-500 shrink-0"
                                       {{ old("facilities.{$idx}.name") == $fac ? 'checked' : '' }}>
                                <label for="fac-{{ $idx }}"
                                       class="text-sm text-gray-700 dark:text-gray-300
                                              flex-1 min-w-0 truncate">
                                    {{ $fac }}
                                </label>
                                <input type="number"
                                       name="facilities[{{ $idx }}][quantity]"
                                       value="{{ old("facilities.{$idx}.quantity", 1) }}"
                                       min="1" max="999"
                                       class="facility-qty w-14 text-center rounded
                                              border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700
                                              text-gray-900 dark:text-white
                                              px-1.5 py-1 text-xs
                                              focus:ring-2 focus:ring-blue-500 shrink-0">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ═══ CUSTOM FACILITIES ═══ --}}
                <div class="pt-5 border-t border-dashed border-gray-300 dark:border-gray-600">
                    <div class="flex flex-col sm:flex-row sm:items-center
                                sm:justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-wand-magic-sparkles text-fuchsia-500"></i>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                Fasilitas Custom
                            </p>
                            <span id="cfCount"
                                  class="text-[10px] font-black px-2 py-0.5 rounded-full
                                         bg-fuchsia-100 dark:bg-fuchsia-900/30
                                         text-fuchsia-600 dark:text-fuchsia-300">
                                0
                            </span>
                        </div>

                        <button type="button" id="addCustomFacility"
                                class="inline-flex items-center justify-center gap-1.5
                                       px-3.5 py-2 rounded-lg
                                       bg-gradient-to-r from-fuchsia-500 to-purple-600
                                       hover:from-fuchsia-600 hover:to-purple-700
                                       text-white text-xs font-bold
                                       shadow-md shadow-fuchsia-500/20
                                       hover:shadow-lg hover:shadow-fuchsia-500/30
                                       active:scale-[.98] transition-all">
                            <i class="fas fa-plus"></i>
                            Tambah Fasilitas Custom
                        </button>
                    </div>

                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-circle-info text-fuchsia-500"></i>
                        Ada fasilitas yang belum ada di daftar standar?
                        Tambahkan di sini — akan muncul di kartu ruangan.
                    </p>

                    <div id="customFacilitiesList"
                         class="space-y-2"
                         data-next-index="{{ count(old('custom_facilities', [])) }}"></div>

                    {{-- Empty state --}}
                    <div id="cfEmpty"
                         class="p-4 rounded-xl
                                border-2 border-dashed border-gray-300 dark:border-gray-600
                                text-center">
                        <i class="fas fa-inbox text-2xl text-gray-300 dark:text-gray-600"></i>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                            Belum ada fasilitas custom.
                        </p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                            Klik tombol <b>Tambah Fasilitas Custom</b> di atas.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 5 — OPERASIONAL
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-5">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">5</span>
                    Operasional &amp; Jam
                </h2>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="status"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status Awal
                        </label>
                        <select name="status" id="status"
                                class="mt-1 w-full rounded-lg
                                       border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white
                                       px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            @foreach(['Tersedia', 'Sedang Dibangun', 'Direnovasi', 'Maintenance'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', 'Tersedia') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="condition"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Kondisi Awal
                        </label>
                        <select name="condition" id="condition"
                                class="mt-1 w-full rounded-lg
                                       border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white
                                       px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            @foreach(['Baik', 'Perlu Perbaikan', 'Rusak', 'Renovasi', 'Bersih'] as $cond)
                                <option value="{{ $cond }}"
                                    {{ old('condition', 'Baik') == $cond ? 'selected' : '' }}>
                                    {{ $cond }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jam Operasional --}}
                <div>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-clock text-cyan-500"></i>
                        Jam Operasional
                        <span class="text-xs font-normal text-gray-400">
                            (centang hari aktif)
                        </span>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @php
                            $days = [
                                'monday' => 'Senin', 'tuesday' => 'Selasa',
                                'wednesday' => 'Rabu', 'thursday' => 'Kamis',
                                'friday' => 'Jumat', 'saturday' => 'Sabtu',
                                'sunday' => 'Minggu',
                            ];
                            $oldDays = old('operational_days', []);
                            $defaultHours = old('operational_hours', []);
                        @endphp

                        @foreach($days as $key => $label)
                            @php $isActive = in_array($key, $oldDays); @endphp
                            <div class="flex items-center gap-2 p-2 rounded-lg
                                        bg-gray-50 dark:bg-gray-700/30">
                                <input type="checkbox"
                                       name="operational_days[]" value="{{ $key }}"
                                       id="day-{{ $key }}"
                                       class="day-checkbox rounded
                                              border-gray-300 text-blue-600
                                              focus:ring-blue-500 shrink-0"
                                       {{ $isActive ? 'checked' : '' }}>
                                <label for="day-{{ $key }}"
                                       class="text-sm font-bold text-gray-700 dark:text-gray-300
                                              min-w-[50px] cursor-pointer">
                                    {{ $label }}
                                </label>

                                <input type="time"
                                       name="operational_hours[{{ $key }}]"
                                       value="{{ $defaultHours[$key] ?? '08:00' }}"
                                       class="day-hour-input w-24 rounded
                                              border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700
                                              text-gray-900 dark:text-white
                                              px-2 py-1 text-xs
                                              focus:ring-2 focus:ring-blue-500
                                              {{ $isActive ? '' : 'opacity-50' }}"
                                       {{ $isActive ? '' : 'disabled' }}>

                                <span class="text-xs text-gray-400">sd</span>

                                <input type="time"
                                       name="operational_hours_end[{{ $key }}]"
                                       value="{{ isset($defaultHours[$key]) && str_contains($defaultHours[$key], '-') ? explode('-', $defaultHours[$key])[1] : '20:00' }}"
                                       class="day-hour-end w-24 rounded
                                              border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700
                                              text-gray-900 dark:text-white
                                              px-2 py-1 text-xs
                                              focus:ring-2 focus:ring-blue-500
                                              {{ $isActive ? '' : 'opacity-50' }}"
                                       {{ $isActive ? '' : 'disabled' }}>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <button type="button" id="applyAllHours"
                                class="text-xs font-bold text-blue-600 dark:text-blue-400
                                       hover:underline">
                            <i class="fas fa-copy mr-1"></i> Terapkan ke semua hari
                        </button>
                        <button type="button" id="clearAllHours"
                                class="text-xs font-bold text-red-500 hover:underline">
                            <i class="fas fa-trash mr-1"></i> Kosongkan semua
                        </button>
                    </div>

                    <p class="text-[11px] text-gray-400 mt-2">
                        Centang hari lalu isi jam. Kosongkan semua = <b>24 jam</b>.
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 6 — PENANGGUNG JAWAB
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-6">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">6</span>
                    Penanggung Jawab
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="responsible_person"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Penanggung Jawab
                    </label>
                    <input type="text" name="responsible_person" id="responsible_person"
                           value="{{ old('responsible_person') }}" maxlength="255"
                           placeholder="Masukkan nama penanggung jawab"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="responsible_phone"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nomor Telepon
                    </label>
                    <input type="text" name="responsible_phone" id="responsible_phone"
                           value="{{ old('responsible_phone') }}" maxlength="30"
                           placeholder="08xx-xxxx-xxxx"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 7 — QR & MEDIA
        ══════════════════════════════════════════════════════ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow
                    border border-gray-200 dark:border-gray-700 overflow-hidden fade-in"
             id="section-7">

            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20
                        border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full
                                 flex items-center justify-center text-xs font-bold">7</span>
                    QR Code &amp; Media
                </h2>
            </div>

            <div class="p-6 space-y-6">

                {{-- QR --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Buat QR Code Ruangan?
                    </label>
                    <div class="mt-2 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer
                                      text-sm text-gray-700 dark:text-gray-300">
                            <input type="radio" name="has_qr" value="1" id="qrYes"
                                   {{ old('has_qr') == '1' ? 'checked' : '' }}>
                            <i class="fas fa-check-circle text-green-500"></i> Ya
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer
                                      text-sm text-gray-700 dark:text-gray-300">
                            <input type="radio" name="has_qr" value="0" id="qrNo"
                                   {{ old('has_qr') == '0' || old('has_qr') === null ? 'checked' : '' }}>
                            <i class="fas fa-times-circle text-red-400"></i> Tidak
                        </label>
                    </div>

                    <div id="qrPreviewContainer"
                         class="mt-3 {{ old('has_qr') == '1' ? '' : 'hidden' }}">
                        <div class="qr-preview">
                            <i class="fas fa-qrcode text-6xl text-gray-300"></i>
                            <p class="text-xs text-gray-400 mt-2">
                                QR Code akan dibuat otomatis
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Foto --}}
                <div>
                    <label for="imageInput"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Foto Ruangan
                        <span class="text-xs text-gray-400 font-normal">(maks 5 file)</span>
                    </label>
                    <input type="file" name="images[]" multiple
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           id="imageInput"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500
                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                  file:border-0 file:text-sm file:font-bold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                  dark:file:bg-gray-700 dark:file:text-blue-300">
                    <div id="imagePreviewContainer"
                         class="flex flex-wrap gap-2 mt-2"></div>
                    <p class="text-xs text-gray-400 mt-1">
                        JPG, JPEG, PNG, WEBP · maks 10MB per file.
                    </p>
                </div>

                {{-- Dokumen --}}
                <div>
                    <label for="documentInput"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dokumen
                        <span class="text-xs text-gray-400 font-normal">(maks 2 file)</span>
                    </label>
                    <input type="file" name="documents[]" multiple
                           accept=".pdf,.doc,.docx,.xls,.xlsx"
                           id="documentInput"
                           class="mt-1 w-full rounded-lg
                                  border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white
                                  px-4 py-2 focus:ring-2 focus:ring-blue-500
                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                  file:border-0 file:text-sm file:font-bold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                  dark:file:bg-gray-700 dark:file:text-blue-300">
                    <div id="documentPreviewContainer"
                         class="flex flex-wrap gap-2 mt-2"></div>
                    <p class="text-xs text-gray-400 mt-1">
                        PDF, DOC, DOCX, XLSX · maks 10MB per file.
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             ACTIONS
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-wrap gap-3">
            <button type="submit" id="submitBtn"
                    class="px-8 py-3 rounded-lg
                           bg-blue-600 hover:bg-blue-700
                           text-white font-bold
                           shadow-lg shadow-blue-500/20
                           inline-flex items-center gap-2
                           active:scale-[.98] transition-all
                           disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="fas fa-save"></i> Simpan &amp; Aktifkan
            </button>

            <button type="reset" id="resetBtn"
                    class="px-6 py-3 rounded-lg
                           bg-gray-200 dark:bg-gray-700
                           hover:bg-gray-300 dark:hover:bg-gray-600
                           text-gray-700 dark:text-gray-200 font-bold
                           inline-flex items-center gap-2 transition">
                <i class="fas fa-undo"></i> Reset
            </button>

            <button type="button" id="previewBtn"
                    class="px-6 py-3 rounded-lg
                           bg-emerald-500 hover:bg-emerald-600
                           text-white font-bold
                           inline-flex items-center gap-2 transition">
                <i class="fas fa-eye"></i> Preview
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const qs  = (s, r = document) => r.querySelector(s);
    const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

    document.addEventListener('DOMContentLoaded', function () {

        /* ═══════════════════════════════════════════════════════
           ROOM CODE GENERATOR
        ═══════════════════════════════════════════════════════ */
        function generateRoomCode() {
            const category = qs('#roomCategory')?.value || '';
            const floor    = qs('#floor')?.value || '';
            const capacity = qs('#capacityNormal')?.value || 0;

            const previewEl = qs('#smartCodePreview');
            if (!previewEl) return;

            if (!category || !floor || !capacity) {
                previewEl.textContent = 'Lengkapi kategori, lantai, dan kapasitas';
                return;
            }

            let catCode = category.substring(0, 2).toUpperCase();
            if (catCode.length < 2) catCode = 'RU';

            let floorMatch = floor.match(/\d+/);
            let floorNum = floorMatch ? floorMatch[0] : '0';
            if (floor.toLowerCase().includes('basement')) floorNum = 'B' + floorNum;

            const capStr = String(capacity % 100).padStart(2, '0');
            const seq    = String(Date.now()).slice(-3);

            previewEl.textContent = 'Kode: ' + catCode + '-' + floorNum + '-' + capStr + '-' + seq;
        }

        ['#roomCategory', '#floor', '#capacityNormal'].forEach(sel => {
            const el = qs(sel);
            if (!el) return;
            el.addEventListener('change', generateRoomCode);
            el.addEventListener('input', generateRoomCode);
        });

        /* ═══════════════════════════════════════════════════════
           LIVE NAME CHECK
        ═══════════════════════════════════════════════════════ */
        const nameInput   = qs('#roomName');
        const nameIcon    = qs('#nameIcon');
        const nameMessage = qs('#nameMessage');
        let nameTimer     = null;

        nameInput?.addEventListener('input', function () {
            clearTimeout(nameTimer);
            const value = this.value.trim();

            if (value.length < 3) {
                nameIcon.innerHTML = '<i class="fas fa-circle text-gray-300"></i>';
                nameMessage.textContent = 'Minimal 3 karakter';
                nameMessage.className = 'text-gray-400';
                return;
            }

            nameIcon.innerHTML = '<i class="fas fa-spinner fa-spin text-amber-500"></i>';
            nameMessage.textContent = 'Memeriksa...';
            nameMessage.className = 'text-amber-500';

            nameTimer = setTimeout(() => {
                fetch('{{ route("owner.rooms.check") }}?field=name&value=' + encodeURIComponent(value), {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.exists) {
                        nameIcon.innerHTML = '<i class="fas fa-times-circle text-rose-500"></i>';
                        nameMessage.textContent = '⚠️ Nama sudah digunakan (bisa tetap disimpan)';
                        nameMessage.className = 'text-rose-500';
                    } else {
                        nameIcon.innerHTML = '<i class="fas fa-check-circle text-emerald-500"></i>';
                        nameMessage.textContent = '✅ Nama tersedia';
                        nameMessage.className = 'text-emerald-500';
                    }
                })
                .catch(() => {
                    nameIcon.innerHTML = '<i class="fas fa-triangle-exclamation text-rose-500"></i>';
                    nameMessage.textContent = 'Gagal memeriksa';
                    nameMessage.className = 'text-rose-500';
                });
            }, 500);
        });

        /* ═══════════════════════════════════════════════════════
           QR TOGGLE
        ═══════════════════════════════════════════════════════ */
        qsa('input[name="has_qr"]').forEach(el => {
            el.addEventListener('change', function () {
                const c = qs('#qrPreviewContainer');
                if (!c) return;
                if (this.value === '1' && this.checked) c.classList.remove('hidden');
                else c.classList.add('hidden');
            });
        });

        /* ═══════════════════════════════════════════════════════
           OPERATIONAL HOURS
        ═══════════════════════════════════════════════════════ */
        qsa('.day-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                const row     = this.closest('div');
                const start   = qs('.day-hour-input', row);
                const end     = qs('.day-hour-end', row);
                if (!start || !end) return;

                if (this.checked) {
                    start.disabled = false;
                    end.disabled   = false;
                    start.classList.remove('opacity-50');
                    end.classList.remove('opacity-50');
                    if (!start.value) start.value = '08:00';
                    if (!end.value)   end.value   = '20:00';
                } else {
                    start.disabled = true;
                    end.disabled   = true;
                    start.classList.add('opacity-50');
                    end.classList.add('opacity-50');
                    start.value = '';
                    end.value   = '';
                }
            });
        });

        qs('#applyAllHours')?.addEventListener('click', () => {
            const firstChecked = qs('.day-checkbox:checked');
            if (!firstChecked) {
                alert('Centang minimal satu hari terlebih dahulu!');
                return;
            }

            const row     = firstChecked.closest('div');
            const startV  = qs('.day-hour-input', row).value;
            const endV    = qs('.day-hour-end', row).value;

            if (!startV || !endV) {
                alert('Isi jam mulai dan selesai pada hari yang dicentang!');
                return;
            }

            qsa('.day-checkbox:checked').forEach(cb => {
                const r = cb.closest('div');
                qs('.day-hour-input', r).value = startV;
                qs('.day-hour-end', r).value   = endV;
            });
        });

        qs('#clearAllHours')?.addEventListener('click', () => {
            qsa('.day-checkbox:checked').forEach(cb => {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            });
        });

        /* ═══════════════════════════════════════════════════════
           ⭐ CUSTOM FACILITIES — CORE FEATURE
        ═══════════════════════════════════════════════════════ */
        const cfList   = qs('#customFacilitiesList');
        const cfEmpty  = qs('#cfEmpty');
        const cfCount  = qs('#cfCount');
        const cfAddBtn = qs('#addCustomFacility');

        function refreshCustomFacilityUI() {
            const rows = qsa('.cf-row', cfList);
            const count = rows.length;

            // Update counter badge
            if (cfCount) cfCount.textContent = count;

            // Empty state toggle
            if (count === 0) {
                cfEmpty?.classList.remove('hidden');
            } else {
                cfEmpty?.classList.add('hidden');
            }

            // Renumber indices (data-index attribute + input names)
            rows.forEach((row, i) => {
                row.dataset.index = i;
                const nameInput = qs('input[type="text"]', row);
                const qtyInput  = qs('input[type="number"]', row);
                if (nameInput) nameInput.name = `custom_facilities[${i}][name]`;
                if (qtyInput)  qtyInput.name  = `custom_facilities[${i}][quantity]`;
            });
        }

        function createCustomFacilityRow(name = '', quantity = 1) {
            const row = document.createElement('div');
            row.className = 'cf-row';

            // Escape untuk value
            const esc = s => String(s).replace(/[&<>"']/g, c => ({
                '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
            }[c]));

            row.innerHTML = `
                <input type="text"
                       value="${esc(name)}"
                       placeholder="Contoh: Proyektor Portable / Kamera DSLR"
                       maxlength="80"
                       class="w-full px-3 py-2 rounded-lg text-xs
                              border border-gray-200 dark:border-gray-700
                              bg-white dark:bg-gray-800
                              text-gray-900 dark:text-white
                              focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30
                              focus:border-fuchsia-400 transition">

                <input type="number"
                       value="${quantity}"
                       min="1" max="999"
                       class="w-full px-2 py-2 rounded-lg text-xs text-center
                              border border-gray-200 dark:border-gray-700
                              bg-white dark:bg-gray-800
                              text-gray-900 dark:text-white
                              focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30
                              focus:border-fuchsia-400 transition">

                <button type="button"
                        data-remove-cf
                        title="Hapus fasilitas"
                        class="w-9 h-9 rounded-lg inline-flex items-center justify-center
                               bg-rose-100 dark:bg-rose-900/30
                               text-rose-600 dark:text-rose-300
                               hover:bg-rose-200 dark:hover:bg-rose-900/50
                               active:scale-95 transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;

            return row;
        }

        function addCustomFacility(name = '', qty = 1) {
            if (!cfList) return;
            const row = createCustomFacilityRow(name, qty);
            cfList.appendChild(row);
            refreshCustomFacilityUI();

            // Focus name input kalau baru
            if (!name) {
                const input = qs('input[type="text"]', row);
                input?.focus();
            }
        }

        // Add button
        cfAddBtn?.addEventListener('click', () => {
            addCustomFacility('', 1);
        });

        // Remove delegation
        cfList?.addEventListener('click', e => {
            const btn = e.target.closest('[data-remove-cf]');
            if (!btn) return;

            const row = btn.closest('.cf-row');
            if (!row) return;

            row.classList.add('is-removing');
            setTimeout(() => {
                row.remove();
                refreshCustomFacilityUI();
            }, 200);
        });

        // Init: restore old input (kalau validasi gagal)
        @if(old('custom_facilities'))
            @foreach(old('custom_facilities') as $cf)
                addCustomFacility(
                    @json($cf['name'] ?? ''),
                    {{ (int) ($cf['quantity'] ?? 1) }}
                );
            @endforeach
        @endif

        // Always call refresh to sync counter & empty state
        refreshCustomFacilityUI();

        /* ═══════════════════════════════════════════════════════
           FILE PREVIEW — IMAGES
        ═══════════════════════════════════════════════════════ */
        const imageInput = qs('#imageInput');
        const imagePreview = qs('#imagePreviewContainer');

        imageInput?.addEventListener('change', function () {
            const files = Array.from(this.files || []);
            imagePreview.innerHTML = '';

            if (files.length === 0) {
                imagePreview.innerHTML = '<span class="text-xs text-gray-400">Belum ada foto dipilih</span>';
                return;
            }

            files.slice(0, 5).forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = ev => {
                    const div = document.createElement('div');
                    div.className = 'file-preview-item relative w-20 h-20 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600';
                    div.innerHTML = `
                        <img src="${ev.target.result}" class="w-full h-full object-cover">
                        <button type="button" data-remove-image="${i}"
                                class="remove-file absolute top-1 right-1 w-5 h-5 rounded-full
                                       bg-rose-500 text-white text-xs font-bold
                                       flex items-center justify-center
                                       hover:bg-rose-600 transition">
                            ×
                        </button>
                    `;
                    imagePreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        imagePreview?.addEventListener('click', e => {
            const btn = e.target.closest('[data-remove-image]');
            if (!btn || !imageInput) return;

            const idx = parseInt(btn.dataset.removeImage, 10);
            const dt = new DataTransfer();
            Array.from(imageInput.files).forEach((f, i) => {
                if (i !== idx) dt.items.add(f);
            });
            imageInput.files = dt.files;
            imageInput.dispatchEvent(new Event('change'));
        });

        /* ═══════════════════════════════════════════════════════
           FILE PREVIEW — DOCUMENTS
        ═══════════════════════════════════════════════════════ */
        const docInput = qs('#documentInput');
        const docPreview = qs('#documentPreviewContainer');

        docInput?.addEventListener('change', function () {
            const files = Array.from(this.files || []);
            docPreview.innerHTML = '';

            if (files.length === 0) {
                docPreview.innerHTML = '<span class="text-xs text-gray-400">Belum ada dokumen dipilih</span>';
                return;
            }

            files.slice(0, 2).forEach((file, i) => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 px-3 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-700';
                div.innerHTML = `
                    <i class="fas fa-file-pdf text-rose-500"></i>
                    <span class="truncate max-w-[160px]">${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
                    <button type="button" data-remove-doc="${i}"
                            class="text-rose-500 hover:text-rose-700 text-xs font-bold">×</button>
                `;
                docPreview.appendChild(div);
            });
        });

        docPreview?.addEventListener('click', e => {
            const btn = e.target.closest('[data-remove-doc]');
            if (!btn || !docInput) return;

            const idx = parseInt(btn.dataset.removeDoc, 10);
            const dt = new DataTransfer();
            Array.from(docInput.files).forEach((f, i) => {
                if (i !== idx) dt.items.add(f);
            });
            docInput.files = dt.files;
            docInput.dispatchEvent(new Event('change'));
        });

        /* ═══════════════════════════════════════════════════════
           STEP INDICATOR
        ═══════════════════════════════════════════════════════ */
        const sections = qsa('.form-section');
        const dots = qsa('.step-dot');

        function updateSteps() {
            let activeIdx = 0;
            sections.forEach((sec, i) => {
                if (sec.getBoundingClientRect().top < window.innerHeight * 0.6) {
                    activeIdx = i;
                }
            });

            dots.forEach((dot, i) => {
                dot.classList.remove('active', 'done');
                if (i < activeIdx) dot.classList.add('done');
                else if (i === activeIdx) dot.classList.add('active');
            });
        }

        window.addEventListener('scroll', updateSteps, { passive: true });
        updateSteps();

        /* ═══════════════════════════════════════════════════════
           SUBMIT — LOADING + VALIDATION
        ═══════════════════════════════════════════════════════ */
        const form = qs('#roomForm');
        const submitBtn = qs('#submitBtn');

        form?.addEventListener('submit', function (e) {
            // Validate custom facilities: nama wajib kalau row ada
            const cfRows = qsa('.cf-row', cfList);
            for (const row of cfRows) {
                const name = qs('input[type="text"]', row)?.value.trim();
                const qty  = parseInt(qs('input[type="number"]', row)?.value || '0', 10);

                if (!name) {
                    e.preventDefault();
                    alert('Isi nama fasilitas custom atau hapus baris kosong.');
                    qs('input[type="text"]', row)?.focus();
                    return;
                }
                if (qty < 1) {
                    e.preventDefault();
                    alert(`Jumlah fasilitas "${name}" minimal 1.`);
                    qs('input[type="number"]', row)?.focus();
                    return;
                }
            }

            // Loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            }
        });

        /* ═══════════════════════════════════════════════════════
           RESET
        ═══════════════════════════════════════════════════════ */
        qs('#resetBtn')?.addEventListener('click', function (e) {
            if (!confirm('Reset semua input?')) {
                e.preventDefault();
                return;
            }
            // Clear custom facilities juga
            if (cfList) cfList.innerHTML = '';
            refreshCustomFacilityUI();
        });

        /* ═══════════════════════════════════════════════════════
           PREVIEW MODAL
        ═══════════════════════════════════════════════════════ */
        qs('#previewBtn')?.addEventListener('click', function () {
            const days = [];
            qsa('.day-checkbox:checked').forEach(cb => {
                const label = cb.nextElementSibling?.textContent || '';
                const row = cb.closest('div');
                const start = qs('.day-hour-input', row)?.value || '--:--';
                const end = qs('.day-hour-end', row)?.value || '--:--';
                days.push(`${label}: ${start} - ${end}`);
            });

            // Preset fasilitas tercentang
            const presetFacs = [];
            qsa('.facility-check:checked').forEach(cb => {
                const row = cb.closest('div');
                const qty = qs('.facility-qty', row)?.value || '1';
                presetFacs.push(`${cb.value} ×${qty}`);
            });

            // Custom fasilitas
            const customFacs = [];
            qsa('.cf-row', cfList).forEach(row => {
                const name = qs('input[type="text"]', row)?.value.trim();
                const qty  = qs('input[type="number"]', row)?.value || '1';
                if (name) customFacs.push(`${name} ×${qty} [custom]`);
            });

            const data = {
                nama:       qs('#roomName')?.value || '-',
                kategori:   qs('#roomCategory')?.value || '-',
                gedung:     qs('#building')?.value || '-',
                lantai:     qs('#floor')?.value || '-',
                kapasitas:  qs('#capacityNormal')?.value || '0',
                kapasitasMax: qs('#capacityMax')?.value || '0',
                status:     qs('select[name="status"]')?.value || '-',
                kondisi:    qs('select[name="condition"]')?.value || '-',
                jam:        days.length ? days.join('\n') : '24 Jam',
                qr:         qs('input[name="has_qr"]:checked')?.value === '1' ? 'Ya' : 'Tidak',
                fasilitas:  [...presetFacs, ...customFacs].join('\n') || 'Belum ada',
            };

            alert(
                '📋 Preview Data Ruangan:\n\n' +
                `Nama: ${data.nama}\n` +
                `Kategori: ${data.kategori}\n` +
                `Gedung: ${data.gedung}\n` +
                `Lantai: ${data.lantai}\n` +
                `Kapasitas: ${data.kapasitas} orang` +
                (data.kapasitasMax > 0 ? ` (max ${data.kapasitasMax})` : '') + '\n' +
                `Status: ${data.status}\n` +
                `Kondisi: ${data.kondisi}\n` +
                `QR Code: ${data.qr}\n\n` +
                `Jam Operasional:\n${data.jam}\n\n` +
                `Fasilitas:\n${data.fasilitas}`
            );
        });

    });
})();
</script>
@endpush