@extends('layouts.roles.ketua')
@section('title', 'Buat Pengumuman – UNICODE')
@section('page-title', 'Buat Pengumuman')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    <a href="{{ $safeRoute('ketua.pengumuman.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-purple-600 transition">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
            New Announcement
        </p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-plus-circle text-purple-500 mr-2"></i>Buat Pengumuman Baru
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Tulis pengumuman untuk dibagikan ke semua anggota.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 p-4">
            <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $safeRoute('ketua.pengumuman.store') }}" class="space-y-5">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Judul Pengumuman <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                       placeholder="Contoh: Rapat Pengurus Bulan Oktober"
                       class="w-full px-4 py-2.5 rounded-xl text-sm
                              border border-gray-300 dark:border-gray-600
                              bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                              focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Kategori
                </label>
                <select name="category"
                        class="w-full px-4 py-2.5 rounded-xl text-sm
                               border border-gray-300 dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                               focus:ring-2 focus:ring-purple-500">
                    @foreach (['umum' => 'Umum', 'penting' => 'Penting', 'urgent' => 'Mendesak', 'info' => 'Info'] as $k => $v)
                        <option value="{{ $k }}" {{ old('category') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Isi Pengumuman <span class="text-red-500">*</span>
                </label>
                <textarea name="content" rows="10" required maxlength="5000"
                          placeholder="Tulis detail pengumuman di sini..."
                          class="w-full px-4 py-3 rounded-xl text-sm
                                 border border-gray-300 dark:border-gray-600
                                 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                 leading-relaxed">{{ old('content') }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Maksimal 5000 karakter</p>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 dark:bg-purple-900/20">
                <input type="checkbox" name="notify_all" value="1" id="notify_all"
                       {{ old('notify_all', true) ? 'checked' : '' }}
                       class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
                <label for="notify_all" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer flex-1">
                    <span class="font-semibold">Kirim notifikasi ke semua anggota</span>
                    <span class="block text-[10px] text-gray-500 mt-0.5">
                        Anggota akan menerima notif real-time
                    </span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ $safeRoute('ketua.pengumuman.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold
                      bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                      text-gray-700 dark:text-gray-200 transition">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold
                           bg-purple-600 hover:bg-purple-700 text-white
                           shadow-lg shadow-purple-500/20 transition">
                <i class="fas fa-paper-plane"></i> Kirim Pengumuman
            </button>
        </div>
    </form>
</div>
</div>
@endsection