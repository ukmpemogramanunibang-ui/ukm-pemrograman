@extends('layouts.roles.ketua')

@section('title', 'Buat Tugas / Materi — ' . ($group->name ?? 'LMS Ketua'))

@section('content')
@php
    $selectedGroupId = old('group_id', $group->id ?? $selectedGroupId ?? null);
    $tz              = 'Asia/Jakarta';
    $nowLocal        = now($tz)->format('Y-m-d\TH:i');
@endphp

<div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-5">

    {{-- BREADCRUMB --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-purple-600">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.kegiatan', ['tab' => 'tasks']) }}" class="hover:text-purple-600">
            Tugas & Materi
        </a>
        @if($group)
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <a href="{{ route('ketua.lms.groups.show', $group->id) }}" class="hover:text-purple-600">
                {{ $group->name }}
            </a>
        @endif
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Buat Item</span>
    </nav>

    {{-- HEADER --}}
    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-purple-600 via-fuchsia-600 to-pink-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-graduation-cap"></i> LMS Ketua
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Buat Tugas / Materi</h1>
            <p class="text-sm opacity-90 mt-1">
                Semua timestamp memakai server time
                <b>{{ $tz }}</b>.
            </p>

            @if($group)
                <div class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 rounded-full bg-white/20 border border-white/30">
                    <i class="fas fa-folder-open"></i>
                    <span class="text-xs font-bold">{{ $group->name }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('warning'))
        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 text-amber-700 dark:text-amber-300 text-sm">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-700 dark:text-rose-300 text-sm">
            <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40">
            <div class="flex items-start gap-2">
                <i class="fas fa-times-circle text-rose-500 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-bold text-rose-700 dark:text-rose-300 mb-1">
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

    {{-- FORM --}}
    <form method="POST"
          action="{{ $group
              ? route('ketua.lms.groups.items.store', $group->id)
              : route('ketua.lms.items.store') }}"
          enctype="multipart/form-data"
          id="lmsItemForm"
          novalidate
          class="space-y-5">

        @csrf

        {{-- 01 — TIPE KONTEN --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-purple-50 to-fuchsia-50 dark:from-purple-900/20 dark:to-fuchsia-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-purple-500 text-white text-xs font-black flex items-center justify-center">01</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Tipe Konten</h2>
            </header>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer block select-none">
                        <div class="group relative p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 transition-all duration-200
                                    has-[:checked]:border-purple-500
                                    has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/20
                                    has-[:checked]:shadow-md has-[:checked]:shadow-purple-500/20">
                            <input type="radio" name="type" value="task" class="sr-only"
                                   {{ old('type', 'task') === 'task' ? 'checked' : '' }}>
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center flex-shrink-0 text-lg">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-black text-sm text-gray-900 dark:text-white">Tugas</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                        Memiliki deadline &amp; pengumpulan.
                                    </p>
                                </div>
                                <i class="fas fa-check-circle text-purple-500 text-lg opacity-0
                                          group-has-[:checked]:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer block select-none">
                        <div class="group relative p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 transition-all duration-200
                                    has-[:checked]:border-emerald-500
                                    has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-900/20
                                    has-[:checked]:shadow-md has-[:checked]:shadow-emerald-500/20">
                            <input type="radio" name="type" value="material" class="sr-only"
                                   {{ old('type') === 'material' ? 'checked' : '' }}>
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center flex-shrink-0 text-lg">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-black text-sm text-gray-900 dark:text-white">Materi</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                        Pembelajaran &amp; lampiran.
                                    </p>
                                </div>
                                <i class="fas fa-check-circle text-emerald-500 text-lg opacity-0
                                          group-has-[:checked]:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </section>

        {{-- 02 — PENGELOMPOKAN --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-amber-500 text-white text-xs font-black flex items-center justify-center">02</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Pengelompokan</h2>
            </header>

            <div class="p-5">
                @if($group)
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div>
                            <p class="text-xs uppercase font-bold text-amber-600 dark:text-amber-400 tracking-wide">
                                Pengelompokan Dipilih
                            </p>
                            <p class="font-black text-sm text-gray-900 dark:text-white">{{ $group->name }}</p>
                        </div>
                    </div>
                @else
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Pilih Pengelompokan <span class="text-rose-500">*</span>
                    </label>
                    <select name="group_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-900 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-400">
                        <option value="">— Pilih Pengelompokan —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @selected($selectedGroupId == $g->id)>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </section>

        {{-- 03 — KONTEN --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-blue-500 text-white text-xs font-black flex items-center justify-center">03</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Konten</h2>
            </header>

            <div class="p-5 space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="inputTitle" class="text-xs font-bold text-gray-600 dark:text-gray-300">
                            Judul <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400 font-mono">
                            <span id="titleCount">0</span>/180
                        </span>
                    </div>
                    <input type="text" id="inputTitle" name="title"
                           value="{{ old('title') }}"
                           required maxlength="180" minlength="3" autocomplete="off"
                           placeholder="Contoh: Tugas Pertemuan 4 — Algoritma Sorting"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  @error('title') border-rose-400 @enderror">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="inputDesc" class="text-xs font-bold text-gray-600 dark:text-gray-300">Deskripsi</label>
                        <span class="text-[11px] text-gray-400 font-mono">
                            <span id="descCount">0</span>/8000
                        </span>
                    </div>
                    <textarea id="inputDesc" name="description" rows="6" maxlength="8000"
                              placeholder="Detail tugas / materi: instruksi, tujuan, catatan, dsb."
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                     bg-gray-50 dark:bg-gray-900 text-sm resize-y min-h-[140px]
                                     focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">{{ old('description') }}</textarea>
                </div>
            </div>
        </section>

        {{-- 04 — COVER --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-orange-500 text-white text-xs font-black flex items-center justify-center">04</span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Gambar Sampul</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300">
                    OPSIONAL
                </span>
            </header>

            <div class="p-5">
                <div class="relative rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600
                            bg-gray-50 dark:bg-gray-900/50 p-5 text-center
                            hover:border-orange-400 hover:bg-orange-50/40 dark:hover:bg-orange-900/10
                            transition-all cursor-pointer">
                    <input type="file" id="inputCover" name="cover"
                           accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="pointer-events-none flex items-center justify-center gap-3 text-xs">
                        <i class="fas fa-image text-orange-500 text-lg"></i>
                        <span class="font-bold text-gray-700 dark:text-gray-200">Pilih gambar sampul</span>
                        <span class="text-gray-500 hidden sm:inline">· Maks 4 MB</span>
                    </div>
                </div>
                <div id="coverFileInfo" class="hidden mt-3 p-3 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700/40">
                    <p class="text-[11px] text-orange-700 dark:text-orange-300 flex items-center justify-between">
                        <span><i class="fas fa-image"></i> <span id="coverFileName">—</span></span>
                        <span class="font-mono"><span id="coverFileSize">0 KB</span></span>
                    </p>
                </div>
            </div>
        </section>

        {{-- 05 — WAKTU --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-rose-500 text-white text-xs font-black flex items-center justify-center">05</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">
                    <span id="timeSectionTitle">Waktu Deadline</span>
                </h2>
            </header>

            <div class="p-5 space-y-4">
                <input type="hidden" name="deadline_mode" id="inputDeadlineMode"
                       value="{{ old('deadline_mode', 'limited') }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer block select-none">
                        <div class="group p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 transition-all
                                    has-[:checked]:border-rose-500
                                    has-[:checked]:bg-rose-50 dark:has-[:checked]:bg-rose-900/20">
                            <input type="radio" name="_deadline_mode_radio" value="limited" class="sr-only"
                                   {{ old('deadline_mode', 'limited') === 'limited' ? 'checked' : '' }}>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-clock text-rose-500 text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <b class="text-sm text-gray-900 dark:text-white">Gunakan Deadline</b>
                                    <p class="text-[11px] text-gray-500 mt-1">Tentukan tanggal &amp; jam.</p>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer block select-none">
                        <div class="group p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 transition-all
                                    has-[:checked]:border-slate-500
                                    has-[:checked]:bg-slate-50 dark:has-[:checked]:bg-slate-900/30">
                            <input type="radio" name="_deadline_mode_radio" value="unlimited" class="sr-only"
                                   {{ old('deadline_mode') === 'unlimited' ? 'checked' : '' }}>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-infinity text-slate-500 text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <b class="text-sm text-gray-900 dark:text-white">Tanpa Batas Waktu</b>
                                    <p class="text-[11px] text-gray-500 mt-1">Tidak ada batas.</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <div id="deadlinePicker" class="space-y-2">
                    <label for="inputDeadline" class="block text-xs font-bold text-gray-600 dark:text-gray-300">
                        Tanggal &amp; Jam Berakhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="inputDeadline" name="deadline_at"
                           value="{{ old('deadline_at') }}" min="{{ $nowLocal }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-400">
                    <p class="text-[11px] text-gray-500">
                        <i class="fas fa-clock text-rose-500"></i> Zona waktu: <b>Asia/Jakarta</b>
                    </p>
                </div>

                <div id="unlimitedInfo"
                     class="hidden p-4 rounded-xl bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-700/50">
                    <p class="text-xs text-slate-700 dark:text-slate-300">
                        <i class="fas fa-infinity text-slate-500"></i>
                        Konten akan tetap tersedia sampai dihapus.
                    </p>
                </div>
            </div>
        </section>

        {{-- 06 — LAMPIRAN --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-indigo-500 text-white text-xs font-black flex items-center justify-center">06</span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Dokumen Lampiran</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                    MAKS 3 FILE · 10 MB
                </span>
            </header>

            <div class="p-5">
                <div id="dropzone"
                     class="relative rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600
                            bg-gray-50 dark:bg-gray-900/50 p-6 text-center
                            hover:border-indigo-400 hover:bg-indigo-50/40 transition-all cursor-pointer">
                    <input type="file" id="inputFiles" name="attachments[]" multiple
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="pointer-events-none">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center text-xl">
                            <i class="fas fa-cloud-arrow-up"></i>
                        </div>
                        <p class="mt-3 font-bold text-sm text-gray-800 dark:text-gray-200">
                            Klik atau drag &amp; drop dokumen
                        </p>
                        <p class="text-[11px] text-gray-500 mt-1">
                            PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV
                        </p>
                    </div>
                </div>

                <div id="filePreview" class="hidden mt-3 space-y-2"></div>
                <div id="fileSummary" class="hidden mt-3 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/40">
                    <p class="text-xs text-indigo-700 dark:text-indigo-300 flex items-center justify-between">
                        <span><i class="fas fa-paperclip"></i> <span id="fileCount">0</span> file</span>
                        <span>Total: <b id="fileTotalSize">0 KB</b></span>
                    </p>
                </div>
            </div>
        </section>

        {{-- 07 — VIDEO --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-pink-500 text-white text-xs font-black flex items-center justify-center">07</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Referensi Video</h2>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-300">
                    OPSIONAL
                </span>
            </header>

            <div class="p-5">
                <div class="flex items-center">
                    <span class="px-3 py-3 rounded-l-xl bg-gray-100 dark:bg-gray-900 border border-r-0 border-gray-200 dark:border-gray-700 text-gray-500">
                        <i class="fas fa-link"></i>
                    </span>
                    <input type="url" id="inputVideoUrl" name="video_url"
                           value="{{ old('video_url') }}"
                           placeholder="https://youtube.com/watch?v=..."
                           class="flex-1 px-4 py-3 rounded-r-xl border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-pink-500/30 focus:border-pink-400">
                </div>
                <p class="text-[11px] text-gray-500 mt-1.5">
                    Hanya <b>YouTube</b>, <b>Vimeo</b>, <b>Instagram</b>, atau <b>TikTok</b>.
                </p>
            </div>
        </section>

        {{-- 08 — TARGET --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-emerald-500 text-white text-xs font-black flex items-center justify-center">08</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Target Penerima</h2>
            </header>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach(['role' => ['Berdasarkan Role','fa-users'], 'individual' => ['Individu','fa-user-check'], 'both' => ['Keduanya','fa-people-arrows']] as $val => [$label, $icon])
                        <label class="cursor-pointer block select-none">
                            <div class="group p-3 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                        bg-white dark:bg-gray-900/40 text-center transition-all
                                        has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500
                                        has-[:checked]:shadow-md has-[:checked]:shadow-emerald-500/30">
                                <input type="radio" name="target_mode" value="{{ $val }}" class="sr-only"
                                       {{ old('target_mode', 'role') === $val ? 'checked' : '' }}>
                                <i class="fas {{ $icon }} text-emerald-500 text-lg group-has-[:checked]:text-white transition-colors"></i>
                                <p class="text-xs font-bold mt-1.5 text-gray-800 dark:text-gray-200 group-has-[:checked]:text-white transition-colors">
                                    {{ $label }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div id="rolePicker" class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-300 mb-3">
                        <i class="fas fa-users-cog text-emerald-500"></i> Pilih Role Penerima:
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($roles ?? [] as $r)
                            <label class="cursor-pointer block select-none">
                                <div class="group flex items-center gap-2 px-3 py-2.5 rounded-lg
                                            bg-white dark:bg-gray-800
                                            border-2 border-gray-200 dark:border-gray-700
                                            hover:border-emerald-400 transition-all
                                            has-[:checked]:bg-emerald-500
                                            has-[:checked]:border-emerald-500
                                            has-[:checked]:text-white">
                                    <input type="checkbox" name="target_roles[]" value="{{ $r->id }}"
                                           class="sr-only"
                                           {{ in_array($r->id, old('target_roles', [])) ? 'checked' : '' }}>
                                    <span class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600
                                                 bg-white flex items-center justify-center flex-shrink-0
                                                 group-has-[:checked]:border-white group-has-[:checked]:bg-white">
                                        <i class="fas fa-check text-[9px] text-emerald-600 opacity-0
                                                  group-has-[:checked]:opacity-100"></i>
                                    </span>
                                    <span class="text-xs font-bold truncate">{{ $r->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div id="userPicker" class="hidden p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 space-y-3">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-300">
                        <i class="fas fa-user-plus text-emerald-500"></i> Cari &amp; Pilih Pengguna:
                    </p>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="userSearchInput"
                               placeholder="Ketik nama, NIM, atau username..."
                               autocomplete="off"
                               class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700
                                      bg-white dark:bg-gray-800 text-xs
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400">
                    </div>
                    <div id="selectedUsers" class="flex flex-wrap gap-2 min-h-[20px]"></div>
                    <div id="userResults"
                         class="hidden max-h-60 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700"></div>
                    <div id="hiddenUsersInputs"></div>
                </div>
            </div>
        </section>

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-3 p-4
                    bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <a href="{{ $group
                    ? route('ketua.lms.groups.show', $group->id)
                    : route('ketua.kegiatan', ['tab' => 'tasks']) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                      text-gray-600 dark:text-gray-300
                      bg-gray-100 dark:bg-gray-700
                      hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="submit" id="btnSubmit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-purple-600 to-fuchsia-600
                           hover:shadow-lg hover:shadow-purple-500/30 transition-all active:scale-95
                           disabled:opacity-60">
                <i class="fas fa-paper-plane"></i>
                <span>Publikasikan</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    'use strict';
    const form = document.getElementById('lmsItemForm');
    if (!form) return;

    const qs  = (s, r = document) => r.querySelector(s);
    const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

    /* TYPE */
    qsa('input[name="type"]').forEach(el => el.addEventListener('change', () => {}));

    /* DEADLINE MODE */
    const inputDeadlineMode = qs('#inputDeadlineMode');
    const deadlinePicker    = qs('#deadlinePicker');
    const unlimitedInfo     = qs('#unlimitedInfo');
    const inputDeadline     = qs('#inputDeadline');

    function applyDeadlineMode(mode) {
        inputDeadlineMode.value = mode;
        if (mode === 'unlimited') {
            deadlinePicker.classList.add('hidden');
            unlimitedInfo.classList.remove('hidden');
            inputDeadline.removeAttribute('required');
            inputDeadline.value = '';
        } else {
            deadlinePicker.classList.remove('hidden');
            unlimitedInfo.classList.add('hidden');
            inputDeadline.setAttribute('required', 'required');
        }
    }
    qsa('input[name="_deadline_mode_radio"]').forEach(el =>
        el.addEventListener('change', () => applyDeadlineMode(el.value)));
    applyDeadlineMode(inputDeadlineMode.value || 'limited');

    /* TARGET MODE */
    const rolePicker = qs('#rolePicker');
    const userPicker = qs('#userPicker');

    function applyTargetMode() {
        const mode = qs('input[name="target_mode"]:checked')?.value || 'role';
        rolePicker.classList.toggle('hidden', !['role', 'both'].includes(mode));
        userPicker.classList.toggle('hidden', !['individual', 'both'].includes(mode));
    }
    qsa('input[name="target_mode"]').forEach(el =>
        el.addEventListener('change', applyTargetMode));
    applyTargetMode();

    /* COUNTER */
    function bindCounter(input, target) {
        if (!input || !target) return;
        const update = () => target.textContent = input.value.length;
        input.addEventListener('input', update);
        update();
    }
    bindCounter(qs('#inputTitle'), qs('#titleCount'));
    bindCounter(qs('#inputDesc'),  qs('#descCount'));

    /* COVER */
    const inputCover   = qs('#inputCover');
    const coverFileInfo= qs('#coverFileInfo');
    const coverFileName= qs('#coverFileName');
    const coverFileSize= qs('#coverFileSize');

    function fmtSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    if (inputCover) {
        inputCover.addEventListener('change', () => {
            const f = inputCover.files?.[0];
            if (!f) return;
            coverFileInfo.classList.remove('hidden');
            coverFileName.textContent = f.name;
            coverFileSize.textContent = fmtSize(f.size);
        });
    }

    /* ATTACHMENTS */
    const inputFiles  = qs('#inputFiles');
    const filePreview = qs('#filePreview');
    const fileSummary = qs('#fileSummary');
    const fileCount   = qs('#fileCount');
    const fileTotal   = qs('#fileTotalSize');

    let selectedFiles = [];

    function renderFiles() {
        if (!selectedFiles.length) {
            filePreview.classList.add('hidden');
            fileSummary.classList.add('hidden');
            return;
        }
        filePreview.innerHTML = selectedFiles.map((f, i) => `
            <div class="flex items-center gap-3 p-3 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                <i class="fas fa-file text-indigo-500"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold truncate">${f.name}</p>
                    <p class="text-[10px] text-gray-500">${fmtSize(f.size)}</p>
                </div>
                <button type="button" data-idx="${i}" class="remove-file text-rose-500 hover:text-rose-700 px-2 py-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
        filePreview.classList.remove('hidden');
        fileSummary.classList.remove('hidden');
        fileCount.textContent = selectedFiles.length;
        fileTotal.textContent = fmtSize(selectedFiles.reduce((s, f) => s + f.size, 0));
        qsa('.remove-file', filePreview).forEach(btn => {
            btn.addEventListener('click', () => {
                selectedFiles.splice(parseInt(btn.dataset.idx, 10), 1);
                syncFiles();
                renderFiles();
            });
        });
    }

    function syncFiles() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        inputFiles.files = dt.files;
    }

    inputFiles.addEventListener('change', () => {
        for (const f of Array.from(inputFiles.files || [])) {
            if (selectedFiles.length >= 3) { alert('Maks 3 file.'); break; }
            if (f.size > 10485760) { alert(`File "${f.name}" > 10 MB.`); continue; }
            selectedFiles.push(f);
        }
        syncFiles();
        renderFiles();
    });

    /* USER PICKER */
    const searchInput = qs('#userSearchInput');
    const resultsBox  = qs('#userResults');
    const selectedBox = qs('#selectedUsers');
    const hiddenBox   = qs('#hiddenUsersInputs');
    const SEARCH_URL  = '{{ route("ketua.lms.items.search-users") }}';
    let selectedUsers = [];
    let searchTimer   = null;

    function renderSelected() {
        if (!selectedUsers.length) {
            selectedBox.innerHTML = '<p class="text-[11px] text-gray-400 italic">Belum ada pengguna terpilih.</p>';
            hiddenBox.innerHTML = '';
            return;
        }
        selectedBox.innerHTML = selectedUsers.map(u => `
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                         bg-emerald-100 dark:bg-emerald-900/40
                         text-emerald-700 dark:text-emerald-300 text-[11px] font-bold">
                <i class="fas fa-user"></i> ${u.name}
                <button type="button" class="remove-user ml-1 hover:text-rose-600" data-id="${u.id}">
                    <i class="fas fa-times"></i>
                </button>
            </span>
        `).join('');
        hiddenBox.innerHTML = selectedUsers
            .map(u => `<input type="hidden" name="target_users[]" value="${u.id}">`).join('');
        qsa('.remove-user', selectedBox).forEach(btn => {
            btn.addEventListener('click', () => {
                selectedUsers = selectedUsers.filter(u => u.id !== parseInt(btn.dataset.id, 10));
                renderSelected();
            });
        });
    }
    renderSelected();

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const q = searchInput.value.trim();
            if (q.length < 2) { resultsBox.classList.add('hidden'); return; }
            searchTimer = setTimeout(async () => {
                try {
                    const params = new URLSearchParams({ q, limit: 15 });
                    selectedUsers.forEach(u => params.append('exclude[]', u.id));
                    const res = await fetch(`${SEARCH_URL}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();
                    if (!data.users?.length) {
                        resultsBox.innerHTML = '<p class="p-3 text-xs text-gray-400 italic">Tidak ada hasil.</p>';
                    } else {
                        resultsBox.innerHTML = data.users.map(u => `
                            <button type="button" class="pick-user w-full text-left flex items-center gap-3 px-3 py-2.5 hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                                    data-id="${u.id}" data-name="${u.name}"
                                    data-nim="${u.nim || ''}" data-role="${u.role_name || '-'}">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white text-[11px] font-black flex items-center justify-center">
                                    ${u.initials || 'U'}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold truncate">${u.name}</p>
                                    <p class="text-[10px] text-gray-500">${u.nim ? 'NIM ' + u.nim : ''} · ${u.role_name}</p>
                                </div>
                                <i class="fas fa-plus-circle text-emerald-500"></i>
                            </button>
                        `).join('');
                    }
                    resultsBox.classList.remove('hidden');
                    qsa('.pick-user', resultsBox).forEach(btn => {
                        btn.addEventListener('click', () => {
                            const id = parseInt(btn.dataset.id, 10);
                            if (selectedUsers.some(u => u.id === id)) return alert('Sudah dipilih.');
                            selectedUsers.push({
                                id,
                                name: btn.dataset.name,
                                nim: btn.dataset.nim,
                                role_name: btn.dataset.role,
                            });
                            renderSelected();
                            resultsBox.classList.add('hidden');
                            searchInput.value = '';
                        });
                    });
                } catch (e) {
                    resultsBox.innerHTML = '<p class="p-3 text-xs text-rose-500">Gagal mencari.</p>';
                    resultsBox.classList.remove('hidden');
                }
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    }

    /* SUBMIT GUARD */
    form.addEventListener('submit', e => {
        const targetMode = qs('input[name="target_mode"]:checked')?.value || 'role';
        if (targetMode === 'individual' && !selectedUsers.length) {
            e.preventDefault();
            return alert('Pilih minimal satu pengguna penerima.');
        }
        if (targetMode === 'role' && !qsa('input[name="target_roles[]"]:checked').length) {
            e.preventDefault();
            return alert('Pilih minimal satu role penerima.');
        }
        if (targetMode === 'both' && !selectedUsers.length && !qsa('input[name="target_roles[]"]:checked').length) {
            e.preventDefault();
            return alert('Pilih minimal satu role atau pengguna penerima.');
        }
        const btn = qs('#btnSubmit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mempublikasikan...';
        }
    });

})();
</script>
@endpush
@endsection