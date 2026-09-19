@extends('layouts.app')

@section('title', '500 – Internal Server Error')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center text-center space-y-6 max-w-2xl mx-auto px-4">
    <div class="relative w-64 h-64 flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-pink-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="relative z-10 bg-white dark:bg-gray-800 rounded-full p-8 border border-gray-200 dark:border-gray-700 w-48 h-48 flex items-center justify-center text-7xl text-orange-500 dark:text-orange-400 shadow-2xl">
            <i class="fas fa-code-branch"></i>
        </div>
        <div class="absolute -top-2 -right-2 bg-orange-500/10 p-3 rounded-full text-orange-500 text-xl animate-bounce">
            <i class="fas fa-bug"></i>
        </div>
    </div>

    <h1 class="text-6xl font-extrabold text-gray-800 dark:text-white">500</h1>
    <h2 class="text-2xl font-bold text-orange-500 dark:text-orange-400">Sistem Sedang Error!</h2>
    <p class="text-gray-500 dark:text-gray-400 max-w-md">
        Maaf, terjadi kesalahan pada server. Tim pengembang sedang bekerja untuk memperbaikinya.
    </p>

    <a href="{{ route('landing') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        <i class="fas fa-sync-alt mr-2"></i> Coba Lagi
    </a>
</div>
@endsection