@extends('layouts.app')

@section('title', '404 – Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center text-center space-y-6 max-w-2xl mx-auto px-4">
    <div class="relative w-64 h-64 flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="relative z-10 bg-white dark:bg-gray-800 rounded-full p-8 border border-gray-200 dark:border-gray-700 w-48 h-48 flex items-center justify-center text-7xl text-cyan-500 dark:text-cyan-400 shadow-2xl">
            <i class="fas fa-robot"></i>
        </div>
        <div class="absolute -top-4 -right-4 bg-red-500/10 dark:bg-red-500/20 p-4 rounded-full text-red-500 dark:text-red-400 text-2xl animate-bounce">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
    </div>

    <h1 class="text-6xl font-extrabold text-gray-800 dark:text-white">404</h1>
    <h2 class="text-2xl font-bold text-cyan-500 dark:text-cyan-400">Misi Gagal! Halaman Hilang.</h2>
    <p class="text-gray-500 dark:text-gray-400 max-w-md">
        Sepertinya Anda mencoba mengakses area yang tidak ada di sistem UNICODE. Periksa kembali alamat URL Anda.
    </p>

    <div class="flex flex-wrap gap-4 justify-center">
        {{-- Fallback-safe: pakai route() kalau ada, hard-link kalau tidak --}}
        @php
            $landingUrl = \Illuminate\Support\Facades\Route::has('landing') ? route('landing') : url('/');
            $loginUrl   = \Illuminate\Support\Facades\Route::has('login')   ? route('login')   : url('/login');
        @endphp

        <a href="{{ $landingUrl }}"
           class="px-6 py-3 bg-gray-200 dark:bg-gray-700 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
        <a href="{{ $loginUrl }}"
           class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl text-white font-bold hover:scale-105 transition shadow-lg shadow-cyan-500/20">
            <i class="fas fa-sign-in-alt mr-2"></i> Halaman Login
        </a>
    </div>
</div>
@endsection