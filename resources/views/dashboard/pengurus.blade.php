@extends('layouts.roles.pengurus')

@section('title', 'Pengurus Dashboard – UNICODE')
@section('page-title', 'Pengurus Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6 mb-4 md:mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Tugas</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">12</p>
                <span class="text-[8px] md:text-xs text-teal-500">3 belum selesai</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-teal-100 dark:bg-teal-900/30 rounded-full flex items-center justify-center text-teal-600 dark:text-teal-400">
                <i class="fas fa-tasks text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Anggota</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">45</p>
                <span class="text-[8px] md:text-xs text-blue-500">8 baru</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fas fa-users text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Pengumuman</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">5</p>
                <span class="text-[8px] md:text-xs text-yellow-500">2 baru</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                <i class="fas fa-bullhorn text-base md:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-4">Aktivitas Terbaru</h3>
        <ul class="space-y-2 md:space-y-3">
            <li class="flex items-center gap-2 md:gap-3 text-xs md:text-sm">
                <span class="w-1.5 h-1.5 md:w-2 md:h-2 bg-teal-500 rounded-full flex-shrink-0"></span>
                <span class="text-gray-600 dark:text-gray-300"><strong>Anda</strong> menyelesaikan tugas</span>
                <span class="ml-auto text-[8px] md:text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">2 jam lalu</span>
            </li>
            <li class="flex items-center gap-2 md:gap-3 text-xs md:text-sm">
                <span class="w-1.5 h-1.5 md:w-2 md:h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                <span class="text-gray-600 dark:text-gray-300"><strong>Anggota</strong> bergabung</span>
                <span class="ml-auto text-[8px] md:text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">5 jam lalu</span>
            </li>
        </ul>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-2 md:gap-4">
            <a href="{{ route('pengurus.tugas') }}" class="p-3 md:p-4 bg-teal-50 dark:bg-teal-900/20 rounded-xl text-center hover:bg-teal-100 dark:hover:bg-teal-900/40 transition">
                <i class="fas fa-plus-circle text-lg md:text-2xl text-teal-600 dark:text-teal-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Tambah Tugas</p>
            </a>
            <a href="{{ route('pengurus.pengumuman') }}" class="p-3 md:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-center hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                <i class="fas fa-bullhorn text-lg md:text-2xl text-blue-600 dark:text-blue-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Buat Pengumuman</p>
            </a>
            <a href="{{ route('pengurus.anggota') }}" class="p-3 md:p-4 bg-green-50 dark:bg-green-900/20 rounded-xl text-center hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                <i class="fas fa-user-plus text-lg md:text-2xl text-green-600 dark:text-green-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Tambah Anggota</p>
            </a>
            <a href="{{ route('pengurus.kontak') }}" class="p-3 md:p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl text-center hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                <i class="fas fa-address-book text-lg md:text-2xl text-purple-600 dark:text-purple-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Kelola Kontak</p>
            </a>
        </div>
    </div>
</div>
@include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])
@endsection