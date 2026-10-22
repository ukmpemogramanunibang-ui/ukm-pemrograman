@extends('layouts.roles.owner')

@section('title', 'Buat Tugas / Materi — ' . ($group->name ?? 'LMS'))

@section('content')
@php
    $selectedGroupId = old('group_id', $group->id ?? $selectedGroupId ?? null);
    $tz              = 'Asia/Jakarta';
    $nowLocal        = now($tz)->format('Y-m-d\TH:i');
@endphp

<div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-5">

    {{-- BREADCRUMB & HEADER --}}
    <div>
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('owner.lms.dashboard') }}" class="hover:text-violet-600">
                <i class="fas fa-home"></i> LMS
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <a href="{{ route('owner.lms.groups.index') }}" class="hover:text-violet-600">Pengelompokan</a>
            @if($group)
                <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
                <a href="{{ route('owner.lms.groups.show', $group->id) }}" class="hover:text-violet-600">
                    {{ $group->name }}
                </a>
            @endif
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Buat Item</span>
        </nav>

        <div class="mt-3 flex items-start justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">Buat Tugas / Materi</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Isi data dengan lengkap. Semua timestamp memakai server time
                    <b class="text-violet-600 dark:text-violet-400">{{ $tz }}</b>.
                </p>
            </div>

            @if($group)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-violet-50 dark:bg-violet-900/30 border border-violet-200 dark:border-violet-700/50">
                    <i class="fas fa-folder-open text-violet-500"></i>
                    <span class="text-xs font-bold text-violet-700 dark:text-violet-300">{{ $group->name }}</span>
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
                        Ada {{ $errors->count() }} kesalahan pada form:
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
              ? route('owner.lms.groups.items.store', $group->id)
              : route('owner.lms.items.store') }}"
          enctype="multipart/form-data"
          id="lmsItemForm"
          novalidate
          class="space-y-5">

        @csrf

        {{-- SECTION 01 — TIPE KONTEN --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-violet-50 to-fuchsia-50 dark:from-violet-900/20 dark:to-fuchsia-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-violet-500 text-white text-xs font-black flex items-center justify-center">01</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Tipe Konten</h2>
            </header>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer block select-none">
                        <div class="group relative p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 transition-all duration-200
                                    has-[:checked]:border-violet-500
                                    has-[:checked]:bg-violet-50 dark:has-[:checked]:bg-violet-900/20
                                    has-[:checked]:shadow-md has-[:checked]:shadow-violet-500/20">
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
                                <i class="fas fa-check-circle text-violet-500 text-lg opacity-0
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

        {{-- SECTION 02 — PENGELOMPOKAN --}}
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
                            @if($group->code)
                                <p class="text-[11px] text-gray-500 font-mono mt-0.5">{{ $group->code }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Pilih Pengelompokan <span class="text-rose-500">*</span>
                    </label>
                    <select name="group_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-900 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                        <option value="">— Pilih Pengelompokan —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @selected($selectedGroupId == $g->id)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </section>

        {{-- SECTION 03 — KONTEN --}}
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
                                  @error('title') border-rose-400 focus:ring-rose-500/30 @enderror">
                    @error('title')
                        <p class="text-[11px] text-rose-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="inputDesc" class="text-xs font-bold text-gray-600 dark:text-gray-300">Deskripsi</label>
                        <span class="text-[11px] text-gray-400 font-mono">
                            <span id="descCount">0</span>/8000
                        </span>
                    </div>
                    <textarea id="inputDesc" name="description" rows="6" maxlength="8000"
                              placeholder="Tuliskan detail tugas / materi: instruksi, tujuan, catatan, dsb."
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                     bg-gray-50 dark:bg-gray-900 text-sm resize-y min-h-[140px]
                                     focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                     @error('description') border-rose-400 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-rose-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- SECTION 04 — GAMBAR SAMPUL --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-orange-500 text-white text-xs font-black flex items-center justify-center">04</span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Gambar Sampul Card</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300">
                    OPSIONAL
                </span>
            </header>

            <div class="p-5">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-3">
                    Gambar ini akan menjadi <b>thumbnail / cover</b> pada card tugas atau materi.
                    Kosongkan untuk memakai gradien default.
                </p>

                <input type="hidden" name="remove_cover" id="inputRemoveCover" value="0">

                <div id="coverPreviewWrap"
                     class="relative aspect-video sm:aspect-[16/9] rounded-xl overflow-hidden
                            bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500
                            border-2 border-dashed border-transparent flex items-center justify-center text-white">

                    <div id="coverPlaceholder"
                         class="absolute inset-0 flex flex-col items-center justify-center text-white/90 p-6 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl mb-3">
                            <i class="fas fa-image"></i>
                        </div>
                        <p class="font-black text-sm">Belum ada gambar sampul</p>
                        <p class="text-[11px] opacity-80 mt-1">Klik atau drag &amp; drop gambar di bawah</p>
                    </div>

                    <img id="coverImagePreview" src="" alt=""
                         class="hidden absolute inset-0 w-full h-full object-cover">

                    <div id="coverActions"
                         class="hidden absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent
                                flex items-end justify-end p-3 gap-2">
                        <button type="button" id="btnChangeCover"
                                class="px-3 py-1.5 rounded-lg bg-white/90 text-gray-800 text-[11px] font-bold hover:bg-white transition">
                            <i class="fas fa-sync-alt"></i> Ganti
                        </button>
                        <button type="button" id="btnRemoveCover"
                                class="px-3 py-1.5 rounded-lg bg-rose-500 text-white text-[11px] font-bold hover:bg-rose-600 transition">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>

                <div id="coverDropzone"
                     class="mt-3 relative rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600
                            bg-gray-50 dark:bg-gray-900/50 p-4 text-center
                            hover:border-orange-400 hover:bg-orange-50/40 dark:hover:bg-orange-900/10
                            transition-all cursor-pointer">
                    <input type="file" id="inputCover" name="cover"
                           accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="pointer-events-none flex items-center justify-center gap-3 text-xs">
                        <i class="fas fa-cloud-arrow-up text-orange-500 text-base"></i>
                        <span class="font-bold text-gray-700 dark:text-gray-200">Pilih gambar sampul</span>
                        <span class="text-gray-400 hidden sm:inline">·</span>
                        <span class="text-gray-500 hidden sm:inline">
                            <b>JPG</b>, <b>JPEG</b>, <b>PNG</b>, <b>WEBP</b>, <b>GIF</b> — maks 4 MB
                        </span>
                    </div>
                </div>

                <div id="coverFileInfo" class="hidden mt-3 p-3 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700/40">
                    <p class="text-[11px] text-orange-700 dark:text-orange-300 flex items-center justify-between">
                        <span class="truncate">
                            <i class="fas fa-image"></i>
                            <span id="coverFileName" class="font-bold">—</span>
                        </span>
                        <span class="font-mono text-[10px]"><span id="coverFileSize">0 KB</span></span>
                    </p>
                </div>

                @error('cover')
                    <p class="text-[11px] text-rose-500 mt-2">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </section>

        {{-- SECTION 05 — WAKTU --}}
        <section id="sectionTime"
                 class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
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
                                    bg-white dark:bg-gray-900/40 transition-all duration-200
                                    has-[:checked]:border-rose-500
                                    has-[:checked]:bg-rose-50 dark:has-[:checked]:bg-rose-900/20
                                    has-[:checked]:shadow-md has-[:checked]:shadow-rose-500/20">
                            <input type="radio" name="_deadline_mode_radio" value="limited" class="sr-only"
                                   {{ old('deadline_mode', 'limited') === 'limited' ? 'checked' : '' }}>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-clock text-rose-500 text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <b class="text-sm text-gray-900 dark:text-white" id="labelLimited">Gunakan Deadline</b>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                        Tentukan tanggal &amp; jam berakhir.
                                    </p>
                                </div>
                                <i class="fas fa-check-circle text-rose-500 opacity-0
                                          group-has-[:checked]:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer block select-none">
                        <div class="group p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 transition-all duration-200
                                    has-[:checked]:border-slate-500
                                    has-[:checked]:bg-slate-50 dark:has-[:checked]:bg-slate-900/30
                                    has-[:checked]:shadow-md has-[:checked]:shadow-slate-500/20">
                            <input type="radio" name="_deadline_mode_radio" value="unlimited" class="sr-only"
                                   {{ old('deadline_mode') === 'unlimited' ? 'checked' : '' }}>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-infinity text-slate-500 text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <b class="text-sm text-gray-900 dark:text-white" id="labelUnlimited">Tanpa Batas Waktu</b>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                        Tidak ada batas waktu / akses.
                                    </p>
                                </div>
                                <i class="fas fa-check-circle text-slate-500 opacity-0
                                          group-has-[:checked]:opacity-100 transition-opacity"></i>
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
                                  focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-400
                                  @error('deadline_at') border-rose-400 @enderror">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock text-rose-500"></i>
                        Zona waktu: <b>Asia/Jakarta</b> · Waktu server: <span id="serverTime">—</span>
                    </p>
                    @error('deadline_at')
                        <p class="text-[11px] text-rose-500">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div id="unlimitedInfo"
                     class="hidden p-4 rounded-xl bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-700/50">
                    <p class="text-xs text-slate-700 dark:text-slate-300">
                        <i class="fas fa-infinity text-slate-500"></i>
                        <b id="unlimitedLabel">Tanpa Batas Waktu.</b>
                        Konten akan tetap tersedia sampai dihapus atau diarsipkan.
                    </p>
                </div>
            </div>
        </section>

        {{-- SECTION 06 — DOKUMEN LAMPIRAN --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-indigo-500 text-white text-xs font-black flex items-center justify-center">06</span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Dokumen Lampiran</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                    MAKS 3 FILE · 10 MB / FILE
                </span>
            </header>

            <div class="p-5">
                <div id="dropzone"
                     class="relative rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600
                            bg-gray-50 dark:bg-gray-900/50 p-6 text-center
                            hover:border-indigo-400 hover:bg-indigo-50/40 dark:hover:bg-indigo-900/10
                            transition-all cursor-pointer">
                    <input type="file" id="inputFiles" name="attachments[]" multiple
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="pointer-events-none">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center text-xl">
                            <i class="fas fa-cloud-arrow-up"></i>
                        </div>
                        <p class="mt-3 font-bold text-sm text-gray-800 dark:text-gray-200">
                            Klik atau drag &amp; drop dokumen di sini
                        </p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                            Format: <b>PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV</b>
                        </p>
                    </div>
                </div>

                <div id="filePreview" class="hidden mt-3 space-y-2"></div>

                <div id="fileSummary" class="hidden mt-3 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/40">
                    <p class="text-xs text-indigo-700 dark:text-indigo-300 flex items-center justify-between">
                        <span><i class="fas fa-paperclip"></i> <span id="fileCount">0</span> file terpilih</span>
                        <span>Total: <b id="fileTotalSize">0 KB</b></span>
                    </p>
                </div>

                @error('attachments')
                    <p class="text-[11px] text-rose-500 mt-2">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
                @error('attachments.*')
                    <p class="text-[11px] text-rose-500 mt-2">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </section>

        {{-- SECTION 07 — REFERENSI VIDEO --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-pink-500 text-white text-xs font-black flex items-center justify-center">07</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Referensi Video</h2>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-300">
                    OPSIONAL
                </span>
            </header>

            <div class="p-5 space-y-3">
                <div>
                    <label for="inputVideoUrl" class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Link Video
                    </label>
                    <div class="flex items-center">
                        <span class="px-3 py-3 rounded-l-xl bg-gray-100 dark:bg-gray-900 border border-r-0 border-gray-200 dark:border-gray-700 text-gray-500 text-sm">
                            <i class="fas fa-link"></i>
                        </span>
                        <input type="url" id="inputVideoUrl" name="video_url"
                               value="{{ old('video_url') }}"
                               placeholder="https://youtube.com/watch?v=..."
                               class="flex-1 px-4 py-3 rounded-r-xl border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-900 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-pink-500/30 focus:border-pink-400
                                      @error('video_url') border-rose-400 @enderror">
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5">
                        Hanya <b>YouTube</b>, <b>Vimeo</b>, <b>Instagram</b>, atau <b>TikTok</b>.
                    </p>
                    @error('video_url')
                        <p class="text-[11px] text-rose-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div id="videoPreview" class="hidden p-3 rounded-xl bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-700/40">
                    <p class="text-xs text-pink-700 dark:text-pink-300 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span id="videoPreviewText">Video terdeteksi.</span>
                    </p>
                </div>
            </div>
        </section>

        {{-- SECTION 08 — TARGET PENERIMA --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-emerald-500 text-white text-xs font-black flex items-center justify-center">08</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Target Penerima</h2>
            </header>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="cursor-pointer block select-none">
                        <div class="group p-3 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 text-center transition-all duration-200
                                    has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500
                                    has-[:checked]:shadow-md has-[:checked]:shadow-emerald-500/30">
                            <input type="radio" name="target_mode" value="role" class="sr-only"
                                   {{ old('target_mode', 'role') === 'role' ? 'checked' : '' }}>
                            <i class="fas fa-users text-emerald-500 text-lg group-has-[:checked]:text-white transition-colors"></i>
                            <p class="text-xs font-bold mt-1.5 text-gray-800 dark:text-gray-200 group-has-[:checked]:text-white transition-colors">
                                Berdasarkan Role
                            </p>
                        </div>
                    </label>

                    <label class="cursor-pointer block select-none">
                        <div class="group p-3 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 text-center transition-all duration-200
                                    has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500
                                    has-[:checked]:shadow-md has-[:checked]:shadow-emerald-500/30">
                            <input type="radio" name="target_mode" value="individual" class="sr-only"
                                   {{ old('target_mode') === 'individual' ? 'checked' : '' }}>
                            <i class="fas fa-user-check text-emerald-500 text-lg group-has-[:checked]:text-white transition-colors"></i>
                            <p class="text-xs font-bold mt-1.5 text-gray-800 dark:text-gray-200 group-has-[:checked]:text-white transition-colors">
                                Individu
                            </p>
                        </div>
                    </label>

                    <label class="cursor-pointer block select-none">
                        <div class="group p-3 rounded-xl border-2 border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-900/40 text-center transition-all duration-200
                                    has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500
                                    has-[:checked]:shadow-md has-[:checked]:shadow-emerald-500/30">
                            <input type="radio" name="target_mode" value="both" class="sr-only"
                                   {{ old('target_mode') === 'both' ? 'checked' : '' }}>
                            <i class="fas fa-people-arrows text-emerald-500 text-lg group-has-[:checked]:text-white transition-colors"></i>
                            <p class="text-xs font-bold mt-1.5 text-gray-800 dark:text-gray-200 group-has-[:checked]:text-white transition-colors">
                                Keduanya
                            </p>
                        </div>
                    </label>
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
                                            text-gray-700 dark:text-gray-200
                                            border-2 border-gray-200 dark:border-gray-700
                                            hover:border-emerald-400 transition-all duration-200
                                            has-[:checked]:bg-emerald-500
                                            has-[:checked]:border-emerald-500
                                            has-[:checked]:text-white
                                            has-[:checked]:shadow-md
                                            has-[:checked]:shadow-emerald-500/30">

                                    <input type="checkbox" name="target_roles[]" value="{{ $r->id }}"
                                           class="sr-only"
                                           {{ in_array($r->id, old('target_roles', [])) ? 'checked' : '' }}>

                                    <span class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600
                                                 bg-white flex items-center justify-center flex-shrink-0 transition-colors
                                                 group-has-[:checked]:border-white group-has-[:checked]:bg-white">
                                        <i class="fas fa-check text-[9px] text-emerald-600
                                                  opacity-0 transition-opacity
                                                  group-has-[:checked]:opacity-100"></i>
                                    </span>

                                    <span class="text-xs font-bold truncate">{{ $r->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('target_roles')
                        <p class="text-[11px] text-rose-500 mt-2">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
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
                    @error('target_users')
                        <p class="text-[11px] text-rose-500">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ============================================================
           | ACTION — DI DALAM FORM (agar tombol submit berfungsi)
           * ============================================================ --}}
        <div class="flex items-center justify-end gap-3 p-4
                    bg-white dark:bg-gray-800
                    rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <a href="{{ $group
                    ? route('owner.lms.groups.show', $group->id)
                    : route('owner.lms.dashboard') }}"
               class="inline-flex items-center justify-center gap-2
                      px-5 py-2.5 rounded-xl text-sm font-bold
                      text-gray-600 dark:text-gray-300
                      bg-gray-100 dark:bg-gray-700
                      hover:bg-gray-200 dark:hover:bg-gray-600
                      transition-colors">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="submit" id="btnSubmit"
                    class="inline-flex items-center justify-center gap-2
                           px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-violet-500 to-fuchsia-500
                           hover:shadow-lg hover:shadow-violet-500/30
                           transition-all
                           disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="fas fa-paper-plane"></i>
                <span id="btnSubmitText">Publikasikan</span>
            </button>
        </div>
    </form>
    {{-- ✅ FORM DITUTUP DI SINI --}}
</div>
{{-- ✅ WRAPPER DITUTUP DI SINI --}}
@endsection
{{-- ✅ SECTION DITUTUP DI SINI --}}

@push('scripts')
<script>
(function () {
    'use strict';

    const form = document.getElementById('lmsItemForm');
    if (!form) return;

    const qs  = (s, r = document) => r.querySelector(s);
    const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

    /* 1. TYPE */
    const timeSectionTitle = qs('#timeSectionTitle');
    const labelLimited     = qs('#labelLimited');
    const labelUnlimited   = qs('#labelUnlimited');
    const unlimitedLabel   = qs('#unlimitedLabel');

    function updateTypeUI() {
        const type = qs('input[name="type"]:checked')?.value || 'task';
        if (type === 'task') {
            timeSectionTitle.textContent = 'Waktu Deadline';
            labelLimited.textContent     = 'Gunakan Deadline';
            labelUnlimited.textContent   = 'Tanpa Batas Waktu';
            unlimitedLabel.textContent   = 'Tanpa Batas Waktu.';
        } else {
            timeSectionTitle.textContent = 'Batas Akses Materi';
            labelLimited.textContent     = 'Gunakan Batas Akses';
            labelUnlimited.textContent   = 'Tanpa Batas Akses';
            unlimitedLabel.textContent   = 'Tanpa Batas Akses.';
        }
    }
    qsa('input[name="type"]').forEach(el => el.addEventListener('change', updateTypeUI));
    updateTypeUI();

    /* 2. DEADLINE MODE */
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
        el.addEventListener('change', () => applyDeadlineMode(el.value))
    );
    applyDeadlineMode(inputDeadlineMode.value || 'limited');

    /* 3. TARGET MODE */
    const rolePicker = qs('#rolePicker');
    const userPicker = qs('#userPicker');

    function applyTargetMode() {
        const mode = qs('input[name="target_mode"]:checked')?.value || 'role';
        rolePicker.classList.toggle('hidden', !['role', 'both'].includes(mode));
        userPicker.classList.toggle('hidden', !['individual', 'both'].includes(mode));
    }
    qsa('input[name="target_mode"]').forEach(el =>
        el.addEventListener('change', applyTargetMode)
    );
    applyTargetMode();

    /* 4. COUNTERS */
    function bindCounter(input, target) {
        if (!input || !target) return;
        const update = () => target.textContent = input.value.length;
        input.addEventListener('input', update);
        update();
    }
    bindCounter(qs('#inputTitle'), qs('#titleCount'));
    bindCounter(qs('#inputDesc'),  qs('#descCount'));

    /* 5. SERVER TIME */
    const serverTimeEl = qs('#serverTime');
    function tickServerTime() {
        if (!serverTimeEl) return;
        const now = new Date();
        const opts = { timeZone: 'Asia/Jakarta', hour12: false };
        const date = now.toLocaleDateString('id-ID', { ...opts, day: '2-digit', month: 'short', year: 'numeric' });
        const time = now.toLocaleTimeString('id-ID', { ...opts, hour: '2-digit', minute: '2-digit', second: '2-digit' });
        serverTimeEl.textContent = `${date} ${time} WIB`;
    }
    tickServerTime();
    setInterval(tickServerTime, 1000);

    /* 6. COVER IMAGE */
    const inputCover        = qs('#inputCover');
    const coverDropzone     = qs('#coverDropzone');
    const coverImagePreview = qs('#coverImagePreview');
    const coverPlaceholder  = qs('#coverPlaceholder');
    const coverActions      = qs('#coverActions');
    const coverPreviewWrap  = qs('#coverPreviewWrap');
    const coverFileInfo     = qs('#coverFileInfo');
    const coverFileName     = qs('#coverFileName');
    const coverFileSize     = qs('#coverFileSize');
    const inputRemoveCover  = qs('#inputRemoveCover');

    const COVER_MAX_SIZE = 4 * 1024 * 1024;
    const COVER_ALLOWED  = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];

    function fmtSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function showCoverPreview(url, name, size) {
        coverImagePreview.src = url;
        coverImagePreview.classList.remove('hidden');
        coverPlaceholder.classList.add('hidden');
        coverActions.classList.remove('hidden');
        coverPreviewWrap.classList.remove('border-dashed');
        coverPreviewWrap.classList.add('border-orange-400');
        if (name) {
            coverFileInfo.classList.remove('hidden');
            coverFileName.textContent = name;
            coverFileSize.textContent = fmtSize(size || 0);
        }
    }

    function resetCoverPreview() {
        coverImagePreview.src = '';
        coverImagePreview.classList.add('hidden');
        coverPlaceholder.classList.remove('hidden');
        coverActions.classList.add('hidden');
        coverPreviewWrap.classList.add('border-dashed');
        coverPreviewWrap.classList.remove('border-orange-400');
        coverFileInfo.classList.add('hidden');
        inputCover.value = '';
    }

    function handleCoverFile(file) {
        if (!file) return;
        if (!COVER_ALLOWED.includes(file.type)) {
            alert('Format gambar harus JPG, JPEG, PNG, WEBP, atau GIF.');
            inputCover.value = '';
            return;
        }
        if (file.size > COVER_MAX_SIZE) {
            alert('Ukuran gambar maksimal 4 MB.');
            inputCover.value = '';
            return;
        }
        showCoverPreview(URL.createObjectURL(file), file.name, file.size);
        inputRemoveCover.value = '0';
    }

    if (inputCover) inputCover.addEventListener('change', () => handleCoverFile(inputCover.files?.[0]));
    qs('#btnChangeCover')?.addEventListener('click', () => inputCover.click());
    qs('#btnRemoveCover')?.addEventListener('click', () => {
        resetCoverPreview();
        inputRemoveCover.value = '1';
    });

    if (coverDropzone) {
        ['dragover', 'dragenter'].forEach(ev =>
            coverDropzone.addEventListener(ev, e => {
                e.preventDefault();
                coverDropzone.classList.add('border-orange-500', 'bg-orange-50/60');
            })
        );
        ['dragleave', 'drop'].forEach(ev =>
            coverDropzone.addEventListener(ev, e => {
                e.preventDefault();
                coverDropzone.classList.remove('border-orange-500', 'bg-orange-50/60');
            })
        );
        coverDropzone.addEventListener('drop', e => {
            e.preventDefault();
            const file = e.dataTransfer?.files?.[0];
            if (!file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            inputCover.files = dt.files;
            handleCoverFile(file);
        });
    }

    /* 7. DOCUMENT ATTACHMENTS */
    const inputFiles  = qs('#inputFiles');
    const filePreview = qs('#filePreview');
    const fileSummary = qs('#fileSummary');
    const fileCount   = qs('#fileCount');
    const fileTotal   = qs('#fileTotalSize');
    const dropzone    = qs('#dropzone');

    const MAX_FILES   = 3;
    const MAX_SIZE    = 10 * 1024 * 1024;
    const ALLOWED_EXT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];

    let selectedFiles = [];

    function extOf(name) {
        return (name.split('.').pop() || '').toLowerCase();
    }

    function renderFilePreview() {
        if (selectedFiles.length === 0) {
            filePreview.classList.add('hidden');
            fileSummary.classList.add('hidden');
            filePreview.innerHTML = '';
            return;
        }

        filePreview.innerHTML = selectedFiles.map((f, i) => `
            <div class="flex items-center gap-3 p-3 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center text-sm flex-shrink-0">
                    <i class="fas fa-file-${extOf(f.name) === 'pdf' ? 'pdf' : 'alt'}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">${f.name}</p>
                    <p class="text-[10px] text-gray-500">${fmtSize(f.size)} · ${extOf(f.name).toUpperCase()}</p>
                </div>
                <button type="button" data-idx="${i}"
                        class="remove-file text-rose-500 hover:text-rose-700 text-xs px-2 py-1 rounded-md hover:bg-rose-50 dark:hover:bg-rose-900/30">
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
                syncInputFiles();
                renderFilePreview();
            });
        });
    }

    function syncInputFiles() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        inputFiles.files = dt.files;
    }

    inputFiles.addEventListener('change', () => {
        for (const f of Array.from(inputFiles.files || [])) {
            if (selectedFiles.length >= MAX_FILES) { alert(`Maksimal ${MAX_FILES} file.`); break; }
            const ext = extOf(f.name);
            if (!ALLOWED_EXT.includes(ext)) { alert(`File "${f.name}" tidak diizinkan.`); continue; }
            if (f.size > MAX_SIZE) { alert(`File "${f.name}" melebihi 10 MB.`); continue; }
            if (selectedFiles.some(s => s.name === f.name)) { alert(`File "${f.name}" sudah dipilih.`); continue; }
            selectedFiles.push(f);
        }
        syncInputFiles();
        renderFilePreview();
    });

    ['dragover', 'dragenter'].forEach(ev =>
        dropzone.addEventListener(ev, e => {
            e.preventDefault();
            dropzone.classList.add('border-indigo-500', 'bg-indigo-50/60');
        })
    );
    ['dragleave', 'drop'].forEach(ev =>
        dropzone.addEventListener(ev, e => {
            e.preventDefault();
            dropzone.classList.remove('border-indigo-500', 'bg-indigo-50/60');
        })
    );
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        const files = e.dataTransfer?.files;
        if (!files || !files.length) return;
        const dt = new DataTransfer();
        for (const f of Array.from(files)) dt.items.add(f);
        inputFiles.files = dt.files;
        inputFiles.dispatchEvent(new Event('change'));
    });

    /* 8. VIDEO PREVIEW */
    const inputVideoUrl    = qs('#inputVideoUrl');
    const videoPreview     = qs('#videoPreview');
    const videoPreviewText = qs('#videoPreviewText');

    function detectProvider(url) {
        if (/youtu\.?be/i.test(url)) return 'YouTube';
        if (/vimeo/i.test(url))     return 'Vimeo';
        if (/instagram/i.test(url)) return 'Instagram';
        if (/tiktok/i.test(url))    return 'TikTok';
        return null;
    }

    if (inputVideoUrl) {
        inputVideoUrl.addEventListener('input', () => {
            const url = inputVideoUrl.value.trim();
            const provider = url ? detectProvider(url) : null;
            if (provider) {
                videoPreview.classList.remove('hidden');
                videoPreviewText.textContent = `Video ${provider} terdeteksi.`;
            } else {
                videoPreview.classList.add('hidden');
            }
        });
    }

    /* 9. USER PICKER */
    const searchInput = qs('#userSearchInput');
    const resultsBox  = qs('#userResults');
    const selectedBox = qs('#selectedUsers');
    const hiddenBox   = qs('#hiddenUsersInputs');

    let selectedUsers = [];
    let searchTimer   = null;

    const SEARCH_URL = '{{ route('owner.lms.items.search-users') }}';

    function renderSelectedUsers() {
        if (selectedUsers.length === 0) {
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
            .map(u => `<input type="hidden" name="target_users[]" value="${u.id}">`)
            .join('');
        qsa('.remove-user', selectedBox).forEach(btn => {
            btn.addEventListener('click', () => {
                selectedUsers = selectedUsers.filter(u => u.id !== parseInt(btn.dataset.id, 10));
                renderSelectedUsers();
            });
        });
    }
    renderSelectedUsers();

    function renderResults(users) {
        if (!users.length) {
            resultsBox.innerHTML = '<p class="p-3 text-xs text-gray-400 italic">Tidak ada hasil.</p>';
            resultsBox.classList.remove('hidden');
            return;
        }
        resultsBox.innerHTML = users.map(u => `
            <button type="button"
                    class="pick-user w-full text-left flex items-center gap-3 px-3 py-2.5 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                    data-id="${u.id}" data-name="${u.name}"
                    data-nim="${u.nim || ''}" data-role="${u.role_name || '-'}">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white text-[11px] font-black flex items-center justify-center flex-shrink-0">
                    ${u.initials || 'U'}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">${u.name}</p>
                    <p class="text-[10px] text-gray-500">
                        ${u.nim ? 'NIM ' + u.nim : ''} · ${u.role_name}
                    </p>
                </div>
                <i class="fas fa-plus-circle text-emerald-500"></i>
            </button>
        `).join('');
        resultsBox.classList.remove('hidden');
        qsa('.pick-user', resultsBox).forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.id, 10);
                if (selectedUsers.some(u => u.id === id)) return alert('Pengguna ini sudah dipilih.');
                selectedUsers.push({
                    id,
                    name: btn.dataset.name,
                    nim:  btn.dataset.nim,
                    role_name: btn.dataset.role,
                });
                renderSelectedUsers();
                resultsBox.classList.add('hidden');
                searchInput.value = '';
            });
        });
    }

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
                    if (!res.ok) throw new Error('Search failed');
                    const data = await res.json();
                    renderResults(data.users || []);
                } catch (e) {
                    console.error(e);
                    resultsBox.innerHTML = '<p class="p-3 text-xs text-rose-500">Gagal mencari pengguna.</p>';
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

    /* 10. SUBMIT GUARD */
    const btnSubmit = qs('#btnSubmit');

    form.addEventListener('submit', e => {
        const targetMode = qs('input[name="target_mode"]:checked')?.value || 'role';

        if (targetMode === 'individual' && selectedUsers.length === 0) {
            e.preventDefault();
            return alert('Pilih minimal satu pengguna penerima.');
        }
        if (targetMode === 'role' && qsa('input[name="target_roles[]"]:checked').length === 0) {
            e.preventDefault();
            return alert('Pilih minimal satu role penerima.');
        }
        if (targetMode === 'both' &&
            selectedUsers.length === 0 &&
            qsa('input[name="target_roles[]"]:checked').length === 0) {
            e.preventDefault();
            return alert('Pilih minimal satu role atau pengguna penerima.');
        }

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>Mempublikasikan...</span>';
        }
    });

})();
</script>
@endpush