@extends('layouts.roles.owner')

@section('title', 'Jelajah Akun – Owner UNICODE')
@section('page-title', 'Jelajah Akun')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            🔎 Jelajah Akun
        </h1>
        <p class="mt-2 text-gray-500 dark:text-gray-400">
            Fitur khusus Owner untuk memeriksa tampilan dan fungsi akun pengguna.
        </p>
    </div>

    {{-- Security Warning --}}
    <div class="rounded-2xl border border-amber-200
                bg-amber-50 dark:bg-amber-900/20
                dark:border-amber-800 p-5">

        <div class="flex gap-4">
            <div class="text-2xl">🔐</div>
            <div>
                <h3 class="font-bold text-amber-800 dark:text-amber-300">
                    Fitur Khusus Owner
                </h3>
                <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                    Anda dapat memeriksa akun pengguna tanpa mengetahui password pengguna tersebut.
                    Setiap aktivitas akan tercatat di log.
                </p>
            </div>
        </div>
    </div>

    {{-- Search Card --}}
    <div class="bg-white dark:bg-gray-800
                rounded-2xl shadow-sm
                border border-gray-200 dark:border-gray-700
                p-6">

        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            Cari Akun Pengguna
        </h2>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Masukkan NIM atau nama pengguna yang ingin Anda jelajahi.
        </p>

        <form action="{{ route('owner.settings.explore.search') }}" method="POST" class="mt-6">
            @csrf

            <div class="flex flex-col md:flex-row gap-3">
                <input type="text"
                       name="keyword"
                       value="{{ old('keyword') }}"
                       required
                       maxlength="100"
                       placeholder="Masukkan NIM atau nama..."
                       class="flex-1 rounded-xl
                              border-gray-300 dark:border-gray-600
                              dark:bg-gray-700 dark:text-white
                              focus:ring-2 focus:ring-indigo-500
                              px-4 py-3">

                <button type="submit"
                        class="px-6 py-3 rounded-xl
                               bg-indigo-600 hover:bg-indigo-700
                               text-white font-semibold
                               transition duration-200">
                    <i class="fas fa-search mr-2"></i> Jelajahi Akun
                </button>
            </div>

            @error('keyword')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </form>
    </div>

    {{-- Info --}}
    <div class="text-sm text-gray-400 dark:text-gray-500 text-center">
        <i class="fas fa-info-circle mr-1"></i>
        Hanya akun dengan role Ketua, Pengurus, atau Anggota UKM yang dapat dijelajahi.
        Owner dan Superadmin tidak dapat dijelajahi.
    </div>
</div>
@endsection