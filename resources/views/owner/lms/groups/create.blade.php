@extends('layouts.roles.owner')

@section('title', 'Buat Pengelompokan — LMS')

@section('content')
@php
    $tz       = 'Asia/Jakarta';
    $nowLocal = now($tz)->format('Y-m-d\TH:i');
@endphp

<div class="p-4 sm:p-6 max-w-3xl mx-auto space-y-5">

    {{-- BREADCRUMB --}}
    <div>
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('owner.lms.dashboard') }}" class="hover:text-violet-600">
                <i class="fas fa-home"></i> LMS
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <a href="{{ route('owner.lms.groups.index') }}" class="hover:text-violet-600">
                Pengelompokan
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Buat Baru</span>
        </nav>

        <div class="mt-3">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
                <i class="fas fa-folder-plus text-violet-500 mr-2"></i>Buat Pengelompokan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Folder untuk menampung tugas &amp; materi UNICODE.
            </p>
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
          action="{{ route('owner.lms.groups.store') }}"
          enctype="multipart/form-data"
          id="lmsGroupForm"
          class="space-y-5">

        @csrf

        {{-- SECTION 01 — INFO DASAR --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-violet-50 to-fuchsia-50 dark:from-violet-900/20 dark:to-fuchsia-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-violet-500 text-white text-xs font-black flex items-center justify-center">01</span>
                <h2 class="font-black text-sm text-gray-900 dark:text-white">Informasi Dasar</h2>
            </header>

            <div class="p-5 space-y-4">
                {{-- NAMA --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="inputName" class="text-xs font-bold text-gray-600 dark:text-gray-300">
                            Nama Pengelompokan <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400 font-mono">
                            <span id="nameCount">0</span>/120
                        </span>
                    </div>
                    <input type="text" id="inputName" name="name"
                           value="{{ old('name') }}"
                           required maxlength="120" minlength="3" autocomplete="off"
                           placeholder="Contoh: Tugas Divisi Pengembang — Batch 1"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400
                                  @error('name') border-rose-400 @enderror">
                    @error('name')
                        <p class="text-[11px] text-rose-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- DESKRIPSI --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="inputDesc" class="text-xs font-bold text-gray-600 dark:text-gray-300">
                            Deskripsi
                        </label>
                        <span class="text-[11px] text-gray-400 font-mono">
                            <span id="descCount">0</span>/1000
                        </span>
                    </div>
                    <textarea id="inputDesc" name="description" rows="4" maxlength="1000"
                              placeholder="Jelaskan tujuan atau cakupan pengelompokan ini..."
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                     bg-gray-50 dark:bg-gray-900 text-sm resize-y min-h-[100px]
                                     focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400
                                     @error('description') border-rose-400 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-rose-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- SECTION 02 — GAMBAR SAMPUL --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <header class="px-5 py-3.5 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-orange-500 text-white text-xs font-black flex items-center justify-center">02</span>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white">Gambar Sampul</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300">
                    OPSIONAL
                </span>
            </header>

            <div class="p-5">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-3">
                    Gambar ini akan menjadi <b>cover</b> pada card pengelompokan.
                    Kosongkan untuk memakai gradien default.
                </p>

                <div id="coverPreviewWrap"
                     class="relative aspect-video rounded-xl overflow-hidden
                            bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500
                            border-2 border-dashed border-transparent flex items-center justify-center text-white">

                    <div id="coverPlaceholder"
                         class="absolute inset-0 flex flex-col items-center justify-center text-white/90 p-6 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl mb-3">
                            <i class="fas fa-image"></i>
                        </div>
                        <p class="font-black text-sm">Belum ada gambar sampul</p>
                        <p class="text-[11px] opacity-80 mt-1">Pilih gambar di bawah</p>
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
                    <input type="file" id="inputCover" name="cover[]" multiple
                           accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="pointer-events-none flex items-center justify-center gap-3 text-xs">
                        <i class="fas fa-cloud-arrow-up text-orange-500 text-base"></i>
                        <span class="font-bold text-gray-700 dark:text-gray-200">Pilih gambar sampul</span>
                        <span class="text-gray-400 hidden sm:inline">·</span>
                        <span class="text-gray-500 hidden sm:inline">
                            <b>JPG</b>, <b>PNG</b>, <b>WEBP</b>, <b>GIF</b> — maks 4 MB
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

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-3 p-4
                    bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <a href="{{ route('owner.lms.groups.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                      text-gray-600 dark:text-gray-300
                      bg-gray-100 dark:bg-gray-700
                      hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="submit" id="btnSubmit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-violet-500 to-fuchsia-500
                           hover:shadow-lg hover:shadow-violet-500/30 transition-all active:scale-95
                           disabled:opacity-60">
                <i class="fas fa-folder-plus"></i>
                <span>Buat Pengelompokan</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    const qs  = (s, r = document) => r.querySelector(s);

    /* COUNTERS */
    function bindCounter(input, target) {
        if (!input || !target) return;
        const update = () => target.textContent = input.value.length;
        input.addEventListener('input', update);
        update();
    }
    bindCounter(qs('#inputName'), qs('#nameCount'));
    bindCounter(qs('#inputDesc'), qs('#descCount'));

    /* COVER PREVIEW */
    const inputCover        = qs('#inputCover');
    const coverDropzone     = qs('#coverDropzone');
    const coverImagePreview = qs('#coverImagePreview');
    const coverPlaceholder  = qs('#coverPlaceholder');
    const coverActions      = qs('#coverActions');
    const coverPreviewWrap  = qs('#coverPreviewWrap');
    const coverFileInfo     = qs('#coverFileInfo');
    const coverFileName     = qs('#coverFileName');
    const coverFileSize     = qs('#coverFileSize');

    const MAX_SIZE  = 4 * 1024 * 1024;
    const ALLOWED   = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];

    function fmtSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    function showCoverPreview(url, name, size) {
        coverImagePreview.src = url;
        coverImagePreview.classList.remove('hidden');
        coverPlaceholder.classList.add('hidden');
        coverActions.classList.remove('hidden');
        coverPreviewWrap.classList.remove('border-dashed');
        coverPreviewWrap.classList.add('border-orange-400');
        coverFileInfo.classList.remove('hidden');
        coverFileName.textContent = name;
        coverFileSize.textContent = fmtSize(size || 0);
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
        if (!ALLOWED.includes(file.type)) {
            alert('Format gambar harus JPG, JPEG, PNG, WEBP, atau GIF.');
            inputCover.value = '';
            return;
        }
        if (file.size > MAX_SIZE) {
            alert('Ukuran gambar maksimal 4 MB.');
            inputCover.value = '';
            return;
        }
        showCoverPreview(URL.createObjectURL(file), file.name, file.size);
    }

    if (inputCover) {
        inputCover.addEventListener('change', () => handleCoverFile(inputCover.files?.[0]));
    }
    qs('#btnChangeCover')?.addEventListener('click', () => inputCover.click());
    qs('#btnRemoveCover')?.addEventListener('click', resetCoverPreview);

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

    /* SUBMIT GUARD */
    const form = qs('#lmsGroupForm');
    const btn  = qs('#btnSubmit');
    if (form) {
        form.addEventListener('submit', () => {
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            }
        });
    }
})();
</script>
@endpush
@endsection