@extends('layouts.roles.ketua')

@section('title', 'Buat Pengelompokan — LMS Ketua')

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto space-y-5">

    {{-- BREADCRUMB --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-purple-600">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.kegiatan', ['tab' => 'groups']) }}" class="hover:text-purple-600">
            Tugas & Materi
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Buat Pengelompokan</span>
    </nav>

    {{-- HEADER --}}
    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-purple-600 via-fuchsia-600 to-pink-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-fuchsia-300/20 rounded-full blur-3xl"></div>

        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-folder-plus"></i> Pengelompokan Baru
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Buat Pengelompokan</h1>
            <p class="text-sm opacity-90 mt-1">
                Folder untuk menampung tugas &amp; materi yang akan kamu assign ke anggota/pengurus.
            </p>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('warning'))
        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 text-amber-700 dark:text-amber-300 text-sm">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('warning') }}
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
          action="{{ route('ketua.lms.groups.store') }}"
          enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        @csrf

        <div class="p-5 space-y-4">

            {{-- NAMA --}}
            <div>
                <label for="inputName" class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Nama Pengelompokan <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="inputName" name="name"
                       value="{{ old('name') }}"
                       required maxlength="120" minlength="3" autocomplete="off"
                       placeholder="Contoh: Tugas Divisi Pengembang — Batch 1"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-900 text-sm
                              focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-400
                              @error('name') border-rose-400 @enderror">
                @error('name')
                    <p class="text-[11px] text-rose-500 mt-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label for="inputDesc" class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Deskripsi
                </label>
                <textarea id="inputDesc" name="description" rows="4" maxlength="1000"
                          placeholder="Jelaskan tujuan atau cakupan pengelompokan ini..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                 bg-gray-50 dark:bg-gray-900 text-sm resize-y min-h-[100px]
                                 focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-400
                                 @error('description') border-rose-400 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-[11px] text-rose-500 mt-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- COVER --}}
            <div>
                <label for="inputCover" class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Gambar Sampul <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="relative rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600
                            bg-gray-50 dark:bg-gray-900/50 p-5 text-center
                            hover:border-purple-400 hover:bg-purple-50/40 dark:hover:bg-purple-900/10
                            transition-all cursor-pointer">
                    <input type="file" id="inputCover" name="cover[]" multiple
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="pointer-events-none flex items-center justify-center gap-3 text-xs">
                        <i class="fas fa-cloud-arrow-up text-purple-500 text-lg"></i>
                        <span class="font-bold text-gray-700 dark:text-gray-200">Pilih gambar sampul</span>
                        <span class="text-gray-400 hidden sm:inline">·</span>
                        <span class="text-gray-500 hidden sm:inline">Maks 4 MB</span>
                    </div>
                </div>
                @error('cover')
                    <p class="text-[11px] text-rose-500 mt-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-3 p-4
                    bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('ketua.kegiatan', ['tab' => 'groups']) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                      text-gray-600 dark:text-gray-300
                      bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600
                      hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-purple-600 to-fuchsia-600
                           hover:shadow-lg hover:shadow-purple-500/30 transition-all
                           active:scale-95">
                <i class="fas fa-folder-plus"></i> Buat Pengelompokan
            </button>
        </div>
    </form>

</div>
@endsection