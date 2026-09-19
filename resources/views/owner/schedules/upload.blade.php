@extends('layouts.roles.owner')

@section('title', 'Atur Jadwal Ruangan – Owner')

@push('styles')
<style>
    .schedule-day-card {
        transition: all .2s ease;
    }

    .schedule-day-card:hover {
        transform: translateY(-1px);
    }

    .schedule-day-card.disabled {
        opacity: .65;
    }

    .outside-warning {
        animation: warningIn .25s ease;
    }

    @keyframes warningIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush


@section('content')

<div class="p-4 md:p-6 max-w-[1600px] mx-auto">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>

            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">

                <span class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/30
                             flex items-center justify-center">

                    <i class="fas fa-calendar-alt text-blue-600"></i>

                </span>

                Atur Jadwal Ruangan

            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">

                Atur siapa yang menggunakan ruangan, kegiatan, hari dan waktu
                sesuai dengan jam operasional ruangan.

            </p>

        </div>


        <a href="{{ route('owner.schedules.index') }}"
           class="px-5 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-700
                  hover:bg-gray-300 dark:hover:bg-gray-600
                  text-gray-700 dark:text-gray-200
                  flex items-center justify-center gap-2">

            <i class="fas fa-arrow-left"></i>

            Kembali

        </a>

    </div>



    {{-- ========================================================= --}}
    {{-- SUCCESS --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="mb-6 bg-green-50 dark:bg-green-900/20
                    border-l-4 border-green-500
                    text-green-700 dark:text-green-300
                    p-4 rounded-xl">

            <div class="flex gap-3 items-center">

                <i class="fas fa-check-circle text-xl"></i>

                <div>
                    {{ session('success') }}
                </div>

            </div>

        </div>

    @endif



    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="mb-6 bg-red-50 dark:bg-red-900/20
                    border-l-4 border-red-500
                    text-red-700 dark:text-red-300
                    p-4 rounded-xl">

            <div class="font-bold mb-2">
                Periksa kembali data berikut:
            </div>

            <ul class="list-disc ml-5 text-sm space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif



    <form
        action="{{ route('owner.schedules.upload.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="scheduleForm">

        @csrf


        {{-- ===================================================== --}}
        {{-- 1. PILIH RUANGAN --}}
        {{-- ===================================================== --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow
                    border border-gray-200 dark:border-gray-700
                    overflow-hidden mb-6">

            <div class="p-6 border-b border-gray-200 dark:border-gray-700">

                <div class="flex items-center gap-3">

                    <span class="w-9 h-9 rounded-xl bg-blue-600 text-white
                                 flex items-center justify-center font-bold">
                        1
                    </span>

                    <div>

                        <h2 class="font-bold text-lg text-gray-800 dark:text-white">
                            Pilih Ruangan
                        </h2>

                        <p class="text-xs text-gray-500">
                            Informasi ruangan akan ditampilkan otomatis.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6">

                <label class="block text-sm font-semibold
                              text-gray-700 dark:text-gray-300 mb-2">

                    Ruangan <span class="text-red-500">*</span>

                </label>


                <select
                    name="room_id"
                    id="room_id"
                    required
                    class="w-full rounded-xl border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700
                           text-gray-900 dark:text-white
                           px-4 py-3 focus:ring-2 focus:ring-blue-500">

                    <option value="">
                        -- Pilih Ruangan --
                    </option>

                    @foreach($rooms as $room)

                        <option
                            value="{{ $room->id }}"

                            {{-- Sesuaikan nama field dengan database Anda --}}
                            data-name="{{ $room->name }}"
                            data-code="{{ $room->room_code }}"
                            data-floor="{{ $room->floor ?? '-' }}"
                            data-category="{{ $room->category ?? '-' }}"

                            data-operational='@json($room->operational_hours ?? [])'>

                            {{ $room->name }}

                            @if($room->room_code)
                                — {{ $room->room_code }}
                            @endif

                        </option>

                    @endforeach

                </select>



                {{-- ================================================= --}}
                {{-- INFORMASI RUANGAN --}}
                {{-- ================================================= --}}

                <div
                    id="roomInformation"
                    class="hidden mt-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2
                                lg:grid-cols-4 gap-4">


                        {{-- NAMA --}}
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20
                                    border border-blue-200 dark:border-blue-800 p-4">

                            <div class="text-xs text-blue-500 mb-1">
                                Nama Ruangan
                            </div>

                            <div
                                id="roomName"
                                class="font-bold text-gray-800 dark:text-white">
                                -
                            </div>

                        </div>


                        {{-- LANTAI --}}
                        <div class="rounded-xl bg-purple-50 dark:bg-purple-900/20
                                    border border-purple-200 dark:border-purple-800 p-4">

                            <div class="text-xs text-purple-500 mb-1">
                                Lantai
                            </div>

                            <div
                                id="roomFloor"
                                class="font-bold text-gray-800 dark:text-white">
                                -
                            </div>

                        </div>


                        {{-- KATEGORI --}}
                        <div class="rounded-xl bg-orange-50 dark:bg-orange-900/20
                                    border border-orange-200 dark:border-orange-800 p-4">

                            <div class="text-xs text-orange-500 mb-1">
                                Kategori
                            </div>

                            <div
                                id="roomCategory"
                                class="font-bold text-gray-800 dark:text-white">
                                -
                            </div>

                        </div>


                        {{-- KODE --}}
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20
                                    border border-emerald-200 dark:border-emerald-800 p-4">

                            <div class="text-xs text-emerald-500 mb-1">
                                Kode Ruangan
                            </div>

                            <div
                                id="roomCode"
                                class="font-bold text-gray-800 dark:text-white">
                                -
                            </div>

                        </div>

                    </div>


                    {{-- OPERASIONAL --}}

                    <div class="mt-5">

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-clock text-blue-500"></i>

                            <h3 class="font-bold text-gray-800 dark:text-white">
                                Jam Operasional Ruangan
                            </h3>

                        </div>


                        <div
                            id="operationalDays"
                            class="grid grid-cols-1 sm:grid-cols-2
                                   lg:grid-cols-4 xl:grid-cols-7 gap-3">

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- 2. IDENTITAS JADWAL --}}
        {{-- ===================================================== --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow
                    border border-gray-200 dark:border-gray-700
                    overflow-hidden mb-6">

            <div class="p-6 border-b border-gray-200 dark:border-gray-700">

                <div class="flex items-center gap-3">

                    <span class="w-9 h-9 rounded-xl bg-purple-600 text-white
                                 flex items-center justify-center font-bold">
                        2
                    </span>

                    <div>

                        <h2 class="font-bold text-lg text-gray-800 dark:text-white">
                            Identitas Jadwal
                        </h2>

                        <p class="text-xs text-gray-500">
                            Informasi umum jadwal ruangan.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">


                {{-- NAMA JADWAL --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold
                                  text-gray-700 dark:text-gray-300 mb-2">

                        Nama Jadwal / Kegiatan
                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Contoh: Jadwal Penggunaan Ruang Kuliah"
                        class="w-full rounded-xl border-gray-300
                               dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700
                               text-gray-900 dark:text-white
                               px-4 py-3">

                </div>


                {{-- TAHUN --}}
                <div>

                    <label class="block text-sm font-semibold
                                  text-gray-700 dark:text-gray-300 mb-2">

                        Tahun Akademik

                    </label>

                    <input
                        type="text"
                        name="academic_year"
                        value="{{ old('academic_year', '2026/2027') }}"
                        placeholder="2026/2027"
                        class="w-full rounded-xl border-gray-300
                               dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700
                               text-gray-900 dark:text-white
                               px-4 py-3">

                </div>


                {{-- SEMESTER --}}
                <div>

                    <label class="block text-sm font-semibold
                                  text-gray-700 dark:text-gray-300 mb-2">

                        Semester

                    </label>

                    <select
                        name="semester"
                        class="w-full rounded-xl border-gray-300
                               dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700
                               text-gray-900 dark:text-white
                               px-4 py-3">

                        <option value="Semester I">Semester I</option>
                        <option value="Semester II">Semester II</option>
                        <option value="Semester III" selected>Semester III</option>
                        <option value="Semester IV">Semester IV</option>
                        <option value="Semester V">Semester V</option>
                        <option value="Semester VI">Semester VI</option>
                        <option value="Semester VII">Semester VII</option>
                        <option value="Semester VIII">Semester VIII</option>

                    </select>

                </div>


                {{-- TIPE --}}
                <div>

                    <label class="block text-sm font-semibold
                                  text-gray-700 dark:text-gray-300 mb-2">

                        Jenis Jadwal

                    </label>

                    <select
                        name="schedule_type"
                        class="w-full rounded-xl border-gray-300
                               dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700
                               text-gray-900 dark:text-white
                               px-4 py-3">

                        <option value="weekly">
                            Jadwal Mingguan
                        </option>

                        <option value="special">
                            Jadwal Khusus
                        </option>

                        <option value="event">
                            Kegiatan / Event
                        </option>

                    </select>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- 3. JADWAL 7 HARI --}}
        {{-- ===================================================== --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow
                    border border-gray-200 dark:border-gray-700
                    overflow-hidden mb-6">

            <div class="p-6 border-b border-gray-200 dark:border-gray-700">

                <div class="flex flex-col lg:flex-row
                            lg:items-center lg:justify-between gap-4">

                    <div class="flex items-center gap-3">

                        <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white
                                     flex items-center justify-center font-bold">
                            3
                        </span>

                        <div>

                            <h2 class="font-bold text-lg text-gray-800 dark:text-white">
                                Atur Jadwal Setiap Hari
                            </h2>

                            <p class="text-xs text-gray-500">
                                Tentukan siapa yang menggunakan ruangan dan waktunya.
                            </p>

                        </div>

                    </div>


                    {{-- COPY --}}
                    <button
                        type="button"
                        id="copyMonday"
                        class="px-4 py-2 rounded-lg
                               bg-gray-100 dark:bg-gray-700
                               hover:bg-gray-200 dark:hover:bg-gray-600
                               text-gray-700 dark:text-gray-200
                               text-sm">

                        <i class="fas fa-copy mr-1"></i>

                        Terapkan Senin ke Hari Lain

                    </button>

                </div>

            </div>


            <div class="p-6 space-y-4">

                @php

                    $days = [
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ];

                @endphp


                @foreach($days as $key => $day)

                    <div
                        class="schedule-day-card border border-gray-200
                               dark:border-gray-700 rounded-2xl p-5"
                        data-day="{{ $key }}">


                        {{-- HEADER HARI --}}

                        <div class="flex flex-col lg:flex-row
                                    lg:items-center lg:justify-between
                                    gap-4 mb-5">

                            <div class="flex items-center gap-3">

                                <div
                                    class="day-icon w-11 h-11 rounded-xl
                                           bg-blue-100 dark:bg-blue-900/30
                                           flex items-center justify-center">

                                    <i class="fas fa-calendar-day text-blue-500"></i>

                                </div>

                                <div>

                                    <div class="font-bold text-lg
                                                text-gray-800 dark:text-white">

                                        {{ $day }}

                                    </div>

                                    <div class="operational-label text-xs text-gray-500">

                                        Pilih ruangan terlebih dahulu

                                    </div>

                                </div>

                            </div>


                            {{-- AKTIF --}}

                            <label class="inline-flex items-center gap-2 cursor-pointer">

                                <input
                                    type="checkbox"
                                    name="schedules[{{ $key }}][enabled]"
                                    value="1"
                                    class="day-enabled w-5 h-5 rounded text-blue-600"
                                    data-day="{{ $key }}">

                                <span class="text-sm font-semibold
                                             text-gray-700 dark:text-gray-300">

                                    Ada jadwal hari ini

                                </span>

                            </label>

                        </div>



                        {{-- FORM HARI --}}

                        <div class="day-form hidden">


                            <div class="grid grid-cols-1 md:grid-cols-2
                                        xl:grid-cols-5 gap-4">


                                {{-- NAMA PENGGUNA --}}

                                <div>

                                    <label class="block text-xs font-semibold
                                                  text-gray-500 mb-2">

                                        Nama Pengguna
                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        name="schedules[{{ $key }}][person]"
                                        class="day-person w-full rounded-xl
                                               border-gray-300 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-700
                                               text-gray-900 dark:text-white
                                               px-4 py-3"
                                        placeholder="Contoh: Pak Budi">

                                </div>


                                {{-- NAMA JADWAL --}}

                                <div class="md:col-span-2">

                                    <label class="block text-xs font-semibold
                                                  text-gray-500 mb-2">

                                        Nama Jadwal / Kegiatan
                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        name="schedules[{{ $key }}][title]"
                                        class="day-title w-full rounded-xl
                                               border-gray-300 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-700
                                               text-gray-900 dark:text-white
                                               px-4 py-3"
                                        placeholder="Contoh: Kuliah Sistem Digital">

                                </div>


                                {{-- JAM MULAI --}}

                                <div>

                                    <label class="block text-xs font-semibold
                                                  text-gray-500 mb-2">

                                        Jam Mulai
                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input
                                        type="time"
                                        name="schedules[{{ $key }}][start_time]"
                                        class="day-start w-full rounded-xl
                                               border-gray-300 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-700
                                               text-gray-900 dark:text-white
                                               px-4 py-3">

                                </div>


                                {{-- JAM SELESAI --}}

                                <div>

                                    <label class="block text-xs font-semibold
                                                  text-gray-500 mb-2">

                                        Jam Selesai
                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input
                                        type="time"
                                        name="schedules[{{ $key }}][end_time]"
                                        class="day-end w-full rounded-xl
                                               border-gray-300 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-700
                                               text-gray-900 dark:text-white
                                               px-4 py-3">

                                </div>


                                {{-- JENIS --}}

                                <div>

                                    <label class="block text-xs font-semibold
                                                  text-gray-500 mb-2">

                                        Jenis

                                    </label>

                                    <select
                                        name="schedules[{{ $key }}][activity_type]"
                                        class="w-full rounded-xl
                                               border-gray-300 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-700
                                               text-gray-900 dark:text-white
                                               px-4 py-3">

                                        <option value="Kuliah">
                                            Kuliah
                                        </option>

                                        <option value="Rapat">
                                            Rapat
                                        </option>

                                        <option value="Praktikum">
                                            Praktikum
                                        </option>

                                        <option value="Pelatihan">
                                            Pelatihan
                                        </option>

                                        <option value="Workshop">
                                            Workshop
                                        </option>

                                        <option value="Organisasi">
                                            Organisasi
                                        </option>

                                        <option value="Lainnya">
                                            Lainnya
                                        </option>

                                    </select>

                                </div>


                                {{-- KETERANGAN --}}

                                <div class="md:col-span-2 xl:col-span-3">

                                    <label class="block text-xs font-semibold
                                                  text-gray-500 mb-2">

                                        Keterangan

                                    </label>

                                    <input
                                        type="text"
                                        name="schedules[{{ $key }}][description]"
                                        class="w-full rounded-xl
                                               border-gray-300 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-700
                                               text-gray-900 dark:text-white
                                               px-4 py-3"
                                        placeholder="Keterangan tambahan">

                                </div>

                            </div>



                            {{-- STATUS OPERASIONAL --}}

                            <div class="day-status mt-5"></div>

                        </div>


                        {{-- EMPTY --}}

                        <div class="day-empty text-sm text-gray-400">

                            <i class="fas fa-moon mr-2"></i>

                            Tidak ada jadwal pada hari {{ $day }}.

                        </div>

                    </div>

                @endforeach

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- 4. ALASAN DI LUAR OPERASIONAL --}}
        {{-- ===================================================== --}}

        <div
            id="outsideOperational"
            class="hidden bg-orange-50 dark:bg-orange-900/20
                   border border-orange-300 dark:border-orange-700
                   rounded-2xl p-6 mb-6 outside-warning">

            <div class="flex gap-4">

                <div class="w-11 h-11 shrink-0 rounded-xl
                            bg-orange-500 text-white
                            flex items-center justify-center">

                    <i class="fas fa-exclamation-triangle"></i>

                </div>


                <div class="flex-1">

                    <h3 class="font-bold text-orange-800
                               dark:text-orange-300">

                        Jadwal berada di luar jam operasional

                    </h3>

                    <p class="text-sm text-orange-700
                              dark:text-orange-400 mt-1">

                        Ada jadwal yang menggunakan ruangan
                        di luar waktu operasional yang ditentukan.

                        Jika tetap digunakan, alasan wajib diisi.

                    </p>


                    <label class="block text-sm font-semibold
                                  text-gray-700 dark:text-gray-300
                                  mt-5 mb-2">

                        Alasan Penggunaan Ruangan
                        <span class="text-red-500">*</span>

                    </label>

                    <textarea
                        name="outside_operational_reason"
                        id="outsideReason"
                        rows="4"
                        placeholder="Contoh: Kegiatan organisasi membutuhkan ruangan sampai pukul 21.00 dan telah mendapatkan persetujuan..."
                        class="w-full rounded-xl
                               border-orange-300
                               dark:border-orange-700
                               bg-white dark:bg-gray-800
                               text-gray-900 dark:text-white
                               px-4 py-3"></textarea>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- 5. PREVIEW JADWAL --}}
        {{-- ===================================================== --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow
                    border border-gray-200 dark:border-gray-700
                    overflow-hidden mb-6">

            <div class="p-6 border-b border-gray-200 dark:border-gray-700">

                <div class="flex items-center gap-3">

                    <span class="w-9 h-9 rounded-xl bg-cyan-600 text-white
                                 flex items-center justify-center font-bold">
                        4
                    </span>

                    <div>

                        <h2 class="font-bold text-lg text-gray-800 dark:text-white">
                            Preview Jadwal
                        </h2>

                        <p class="text-xs text-gray-500">
                            Tampilan jadwal berdasarkan data yang Anda masukkan.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6 overflow-x-auto">

                <div
                    id="schedulePreview"
                    class="min-w-[1000px]">

                    <div class="grid grid-cols-7 gap-3">

                        @foreach($days as $key => $day)

                            <div class="border border-gray-200
                                        dark:border-gray-700
                                        rounded-xl overflow-hidden">

                                <div class="bg-blue-600 text-white
                                            text-center py-3 font-bold">

                                    {{ $day }}

                                </div>

                                <div
                                    id="preview-{{ $key }}"
                                    class="min-h-[180px] p-2
                                           bg-gray-50 dark:bg-gray-900">

                                    <div class="text-center
                                                text-xs text-gray-400 py-12">

                                        Kosong

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- 6. UPLOAD GAMBAR --}}
        {{-- ===================================================== --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow
                    border border-gray-200 dark:border-gray-700
                    overflow-hidden mb-6">

            <div class="p-6 border-b border-gray-200 dark:border-gray-700">

                <div class="flex items-center gap-3">

                    <span class="w-9 h-9 rounded-xl bg-pink-600 text-white
                                 flex items-center justify-center font-bold">
                        5
                    </span>

                    <div>

                        <h2 class="font-bold text-lg text-gray-800 dark:text-white">
                            Upload Gambar Jadwal
                        </h2>

                        <p class="text-xs text-gray-500">
                            Maksimal 4 gambar.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6">

                <label for="scheduleImages"
                       class="block border-2 border-dashed
                              border-gray-300 dark:border-gray-600
                              hover:border-blue-500
                              rounded-2xl p-8 text-center cursor-pointer">

                    <i class="fas fa-cloud-upload-alt
                              text-4xl text-blue-500"></i>

                    <div class="font-semibold text-gray-700
                                dark:text-gray-200 mt-3">

                        Pilih gambar jadwal

                    </div>

                    <div class="text-xs text-gray-400 mt-2">

                        JPG, JPEG, PNG, WEBP

                    </div>

                    <div class="text-xs text-gray-400">

                        Maksimal 4 file

                    </div>

                </label>


                <input
                    type="file"
                    id="scheduleImages"
                    name="schedule_images[]"
                    multiple
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="hidden">


                <div
                    id="imagePreview"
                    class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5">
                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- 7. CATATAN --}}
        {{-- ===================================================== --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow
                    border border-gray-200 dark:border-gray-700
                    p-6 mb-6">

            <label class="block text-sm font-semibold
                          text-gray-700 dark:text-gray-300 mb-2">

                Catatan Tambahan

            </label>

            <textarea
                name="notes"
                rows="4"
                placeholder="Catatan tambahan mengenai jadwal ruangan..."
                class="w-full rounded-xl border-gray-300
                       dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700
                       text-gray-900 dark:text-white
                       px-4 py-3"></textarea>

        </div>



        {{-- ===================================================== --}}
        {{-- BUTTON --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col sm:flex-row justify-end gap-3">

            <button
                type="reset"
                class="px-6 py-3 rounded-xl
                       bg-gray-200 dark:bg-gray-700
                       hover:bg-gray-300 dark:hover:bg-gray-600
                       text-gray-700 dark:text-gray-200">

                <i class="fas fa-undo mr-2"></i>

                Reset

            </button>


            <button
                type="submit"
                id="submitButton"
                class="px-8 py-3 rounded-xl
                       bg-blue-600 hover:bg-blue-700
                       text-white font-bold shadow-lg">

                <i class="fas fa-save mr-2"></i>

                Simpan Jadwal

            </button>

        </div>

    </form>

</div>



@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const roomSelect =
        document.getElementById('room_id');

    const roomInformation =
        document.getElementById('roomInformation');

    const roomName =
        document.getElementById('roomName');

    const roomCode =
        document.getElementById('roomCode');

    const roomFloor =
        document.getElementById('roomFloor');

    const roomCategory =
        document.getElementById('roomCategory');

    const operationalDays =
        document.getElementById('operationalDays');

    const outsideOperational =
        document.getElementById('outsideOperational');

    const outsideReason =
        document.getElementById('outsideReason');



    /*
    |--------------------------------------------------------------------------
    | HARI
    |--------------------------------------------------------------------------
    */

    const dayNames = {

        monday: 'Senin',
        tuesday: 'Selasa',
        wednesday: 'Rabu',
        thursday: 'Kamis',
        friday: 'Jumat',
        saturday: 'Sabtu',
        sunday: 'Minggu'

    };



    /*
    |--------------------------------------------------------------------------
    | PILIH RUANGAN
    |--------------------------------------------------------------------------
    */

    roomSelect.addEventListener('change', function () {

        const option =
            this.options[this.selectedIndex];


        if (!option.value) {

            roomInformation.classList.add('hidden');

            return;

        }


        roomInformation.classList.remove('hidden');


        roomName.textContent =
            option.dataset.name || '-';

        roomCode.textContent =
            option.dataset.code || '-';

        roomFloor.textContent =
            option.dataset.floor || '-';

        roomCategory.textContent =
            option.dataset.category || '-';


        renderOperationalHours(
            option.dataset.operational
        );

        checkAllOperational();

    });



    /*
    |--------------------------------------------------------------------------
    | RENDER JAM OPERASIONAL
    |--------------------------------------------------------------------------
    */

    function renderOperationalHours(data) {

        operationalDays.innerHTML = '';


        let operational = {};

        try {

            operational =
                JSON.parse(data || '{}');

        } catch (error) {

            operational = {};

        }


        Object.keys(dayNames).forEach(day => {

            const value =
                operational[day];


            let start = null;
            let end = null;


            /*
            |--------------------------------------------------------------------------
            | Bisa menerima:
            |
            | monday: {
            |   start: "07:00",
            |   end: "21:00"
            | }
            |
            | atau:
            |
            | monday: "07:00-21:00"
            |--------------------------------------------------------------------------
            */

            if (
                value &&
                typeof value === 'object'
            ) {

                start =
                    value.start ||
                    value.open ||
                    null;

                end =
                    value.end ||
                    value.close ||
                    null;

            } else if (
                typeof value === 'string'
            ) {

                const parts =
                    value.split('-');

                start =
                    parts[0] || null;

                end =
                    parts[1] || null;

            }


            const card =
                document.createElement('div');


            if (start && end) {

                card.className =
                    'rounded-xl p-4 bg-green-50 ' +
                    'dark:bg-green-900/20 border ' +
                    'border-green-200 dark:border-green-800';


                card.innerHTML = `

                    <div class="text-xs font-semibold
                                text-green-600">

                        ${dayNames[day]}

                    </div>

                    <div class="font-bold
                                text-gray-800
                                dark:text-white mt-1">

                        ${start} – ${end}

                    </div>

                    <div class="text-[11px]
                                text-green-600 mt-1">

                        <i class="fas fa-check-circle"></i>

                        Buka

                    </div>

                `;

            } else {

                card.className =
                    'rounded-xl p-4 bg-red-50 ' +
                    'dark:bg-red-900/20 border ' +
                    'border-red-200 dark:border-red-800';


                card.innerHTML = `

                    <div class="text-xs font-semibold
                                text-red-600">

                        ${dayNames[day]}

                    </div>

                    <div class="font-bold
                                text-gray-800
                                dark:text-white mt-1">

                        Tutup

                    </div>

                    <div class="text-[11px]
                                text-red-600 mt-1">

                        <i class="fas fa-times-circle"></i>

                        Tidak beroperasi

                    </div>

                `;

            }


            operationalDays.appendChild(card);

        });

    }



    /*
    |--------------------------------------------------------------------------
    | AMBIL OPERASIONAL RUANGAN
    |--------------------------------------------------------------------------
    */

    function getOperational() {

        const option =
            roomSelect.options[
                roomSelect.selectedIndex
            ];


        if (!option || !option.value) {
            return {};
        }


        try {

            return JSON.parse(
                option.dataset.operational || '{}'
            );

        } catch (error) {

            return {};

        }

    }



    /*
    |--------------------------------------------------------------------------
    | CEK JAM
    |--------------------------------------------------------------------------
    */

    function getOperationalForDay(day) {

        const operational =
            getOperational();


        const value =
            operational[day];


        if (!value) {
            return null;
        }


        if (
            typeof value === 'object'
        ) {

            return {

                start:
                    value.start ||
                    value.open ||
                    null,

                end:
                    value.end ||
                    value.close ||
                    null

            };

        }


        if (
            typeof value === 'string'
        ) {

            const parts =
                value.split('-');


            return {

                start: parts[0] || null,

                end: parts[1] || null

            };

        }


        return null;

    }



    /*
    |--------------------------------------------------------------------------
    | CEK SEMUA HARI
    |--------------------------------------------------------------------------
    */

    function checkAllOperational() {

        let outside = false;


        document
            .querySelectorAll('.schedule-day-card')
            .forEach(card => {

                const checkbox =
                    card.querySelector('.day-enabled');


                if (!checkbox.checked) {
                    return;
                }


                const day =
                    card.dataset.day;


                const start =
                    card.querySelector('.day-start').value;


                const end =
                    card.querySelector('.day-end').value;


                const status =
                    card.querySelector('.day-status');


                if (!start || !end) {
                    return;
                }


                const operational =
                    getOperationalForDay(day);


                if (!operational) {

                    outside = true;


                    status.innerHTML = `

                        <div class="p-4 rounded-xl
                                    bg-orange-100
                                    dark:bg-orange-900/20
                                    border border-orange-300
                                    dark:border-orange-700">

                            <div class="font-bold text-orange-700
                                        dark:text-orange-300">

                                <i class="fas fa-exclamation-triangle mr-2"></i>

                                ${dayNames[day]} tidak memiliki jam operasional.

                            </div>

                            <div class="text-xs mt-1
                                        text-orange-600">

                                Penggunaan pada hari ini membutuhkan alasan.

                            </div>

                        </div>

                    `;

                    return;

                }


                if (
                    start < operational.start ||
                    end > operational.end
                ) {

                    outside = true;


                    status.innerHTML = `

                        <div class="p-4 rounded-xl
                                    bg-orange-100
                                    dark:bg-orange-900/20
                                    border border-orange-300
                                    dark:border-orange-700">

                            <div class="font-bold text-orange-700
                                        dark:text-orange-300">

                                <i class="fas fa-clock mr-2"></i>

                                Di luar jam operasional

                            </div>

                            <div class="text-sm text-orange-700
                                        dark:text-orange-400 mt-1">

                                Jadwal:
                                <strong>${start} – ${end}</strong>

                                <br>

                                Operasional:
                                <strong>
                                    ${operational.start}
                                    –
                                    ${operational.end}
                                </strong>

                            </div>

                        </div>

                    `;

                } else {

                    status.innerHTML = `

                        <div class="p-3 rounded-xl
                                    bg-green-50
                                    dark:bg-green-900/20
                                    border border-green-200
                                    dark:border-green-800">

                            <div class="text-sm font-semibold
                                        text-green-700
                                        dark:text-green-300">

                                <i class="fas fa-check-circle mr-2"></i>

                                Jadwal berada dalam jam operasional.

                            </div>

                            <div class="text-xs text-green-600 mt-1">

                                Operasional:
                                ${operational.start}
                                –
                                ${operational.end}

                            </div>

                        </div>

                    `;

                }

            });


        if (outside) {

            outsideOperational.classList.remove('hidden');

            outsideReason.required = true;

        } else {

            outsideOperational.classList.add('hidden');

            outsideReason.required = false;

        }

    }



    /*
    |--------------------------------------------------------------------------
    | AKTIFKAN / NONAKTIFKAN HARI
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.day-enabled')
        .forEach(checkbox => {

            checkbox.addEventListener('change', function () {

                const card =
                    this.closest('.schedule-day-card');


                const form =
                    card.querySelector('.day-form');


                const empty =
                    card.querySelector('.day-empty');


                if (this.checked) {

                    form.classList.remove('hidden');

                    empty.classList.add('hidden');

                } else {

                    form.classList.add('hidden');

                    empty.classList.remove('hidden');


                    card.querySelector('.day-person').value = '';
                    card.querySelector('.day-title').value = '';
                    card.querySelector('.day-start').value = '';
                    card.querySelector('.day-end').value = '';
                    card.querySelector('.day-status').innerHTML = '';

                }


                checkAllOperational();

                updatePreview();

            });

        });



    /*
    |--------------------------------------------------------------------------
    | INPUT BERUBAH
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '.day-person, .day-title, .day-start, .day-end'
        )
        .forEach(input => {

            input.addEventListener(
                'input',
                function () {

                    checkAllOperational();

                    updatePreview();

                }
            );

        });



    /*
    |--------------------------------------------------------------------------
    | PREVIEW
    |--------------------------------------------------------------------------
    */

    function updatePreview() {

        Object.keys(dayNames).forEach(day => {

            const preview =
                document.getElementById(
                    `preview-${day}`
                );


            const card =
                document.querySelector(
                    `.schedule-day-card[data-day="${day}"]`
                );


            const enabled =
                card.querySelector('.day-enabled').checked;


            preview.innerHTML = '';


            if (!enabled) {

                preview.innerHTML = `

                    <div class="text-center text-xs
                                text-gray-400 py-12">

                        Kosong

                    </div>

                `;

                return;

            }


            const person =
                card.querySelector('.day-person').value ||
                'Belum diisi';


            const title =
                card.querySelector('.day-title').value ||
                'Belum diisi';


            const start =
                card.querySelector('.day-start').value ||
                '--:--';


            const end =
                card.querySelector('.day-end').value ||
                '--:--';


            preview.innerHTML = `

                <div class="p-3 rounded-xl bg-white
                            dark:bg-gray-800
                            border border-gray-200
                            dark:border-gray-700 shadow-sm">

                    <div class="text-xs font-bold
                                text-blue-600">

                        ${escapeHtml(start)}
                        –
                        ${escapeHtml(end)}

                    </div>

                    <div class="font-bold text-sm
                                text-gray-800
                                dark:text-white mt-2">

                        ${escapeHtml(title)}

                    </div>

                    <div class="text-xs text-gray-500 mt-2">

                        <i class="fas fa-user mr-1"></i>

                        ${escapeHtml(person)}

                    </div>

                </div>

            `;

        });

    }



    function escapeHtml(value) {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }



    /*
    |--------------------------------------------------------------------------
    | TERAPKAN SENIN KE SENIN-SABTU
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('copyMonday')
        .addEventListener('click', function () {

            const monday =
                document.querySelector(
                    '.schedule-day-card[data-day="monday"]'
                );


            const enabled =
                monday.querySelector('.day-enabled').checked;


            if (!enabled) {

                alert(
                    'Aktifkan dan isi jadwal Senin terlebih dahulu.'
                );

                return;

            }


            const person =
                monday.querySelector('.day-person').value;


            const title =
                monday.querySelector('.day-title').value;


            const start =
                monday.querySelector('.day-start').value;


            const end =
                monday.querySelector('.day-end').value;


            document
                .querySelectorAll('.schedule-day-card')
                .forEach(card => {

                    if (
                        card.dataset.day === 'sunday' ||
                        card.dataset.day === 'monday'
                    ) {
                        return;
                    }


                    card.querySelector('.day-enabled').checked = true;

                    card.querySelector('.day-form')
                        .classList.remove('hidden');

                    card.querySelector('.day-empty')
                        .classList.add('hidden');

                    card.querySelector('.day-person')
                        .value = person;

                    card.querySelector('.day-title')
                        .value = title;

                    card.querySelector('.day-start')
                        .value = start;

                    card.querySelector('.day-end')
                        .value = end;

                });


            checkAllOperational();

            updatePreview();

        });



    /*
    |--------------------------------------------------------------------------
    | UPLOAD GAMBAR
    |--------------------------------------------------------------------------
    */

    const imageInput =
        document.getElementById('scheduleImages');

    const imagePreview =
        document.getElementById('imagePreview');


    imageInput.addEventListener('change', function () {

        const files =
            Array.from(this.files);


        if (files.length > 4) {

            alert(
                'Maksimal 4 gambar jadwal.'
            );

            this.value = '';

            imagePreview.innerHTML = '';

            return;

        }


        const allowed = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        imagePreview.innerHTML = '';


        files.forEach(file => {

            if (!allowed.includes(file.type)) {

                alert(
                    `${file.name} bukan format gambar yang diperbolehkan.`
                );

                return;

            }


            const reader =
                new FileReader();


            reader.onload = function (event) {

                const div =
                    document.createElement('div');

                div.className =
                    'rounded-xl overflow-hidden border ' +
                    'border-gray-200 dark:border-gray-700';


                div.innerHTML = `

                    <img
                        src="${event.target.result}"
                        class="w-full h-40 object-cover">

                    <div class="p-2 text-xs
                                text-gray-500
                                dark:text-gray-400">

                        ${escapeHtml(file.name)}

                    </div>

                `;


                imagePreview.appendChild(div);

            };


            reader.readAsDataURL(file);

        });

    });



    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('scheduleForm')
        .addEventListener('submit', function (event) {

            const activeDays =
                document.querySelectorAll(
                    '.day-enabled:checked'
                );


            if (activeDays.length === 0) {

                event.preventDefault();

                alert(
                    'Minimal satu hari harus memiliki jadwal.'
                );

                return;

            }


            let invalid = false;


            document
                .querySelectorAll('.schedule-day-card')
                .forEach(card => {

                    const enabled =
                        card.querySelector('.day-enabled').checked;


                    if (!enabled) {
                        return;
                    }


                    const start =
                        card.querySelector('.day-start').value;


                    const end =
                        card.querySelector('.day-end').value;


                    if (
                        !start ||
                        !end ||
                        start >= end
                    ) {

                        invalid = true;

                    }

                });


            if (invalid) {

                event.preventDefault();

                alert(
                    'Periksa jam setiap hari. Jam selesai harus lebih besar daripada jam mulai.'
                );

                return;

            }


            checkAllOperational();


            if (
                !outsideOperational.classList.contains('hidden') &&
                !outsideReason.value.trim()
            ) {

                event.preventDefault();

                outsideReason.focus();

                alert(
                    'Jadwal berada di luar jam operasional. Silakan isi alasan penggunaan ruangan.'
                );

                return;

            }


            const button =
                document.getElementById('submitButton');


            button.disabled = true;

            button.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

        });



    updatePreview();

});

</script>
@endpush

@endsection