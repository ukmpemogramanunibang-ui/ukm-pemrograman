@extends('layouts.roles.superadmin')

@section('title', 'Superadmin Dashboard – UNICODE')
@section('page-title', 'Superadmin Dashboard')

@section('content')
@php
    if (!isset($stats)) {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_pengurus' => \App\Models\User::where('level_id', 4)->count(),
            'total_anggota' => \App\Models\User::where('level_id', 5)->where('name', '!=', 'Anggota Umum')->count(),
            'total_pengunjung' => \App\Models\User::where(function ($q) { $q->where('level_id', 6)->orWhere('account_type', 'guest'); })->count(),
            'total_anggota_umum' => \App\Models\User::where('level_id', 5)->where('name', 'Anggota Umum')->count(),
            'total_blocked' => \App\Models\User::where(function ($q) { $q->where('is_active', false)->orWhereNotNull('locked_until'); })->count(),
            'total_owner' => \App\Models\User::where('level_id', 1)->count(),
            'total_superadmin' => \App\Models\User::where('level_id', 2)->count(),
        ];
    }
    if (!isset($activities)) {
        $activities = collect();
    }
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
    {{-- Card Total Pengguna --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Pengguna</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_users'] }}</p>
                <span class="text-[8px] md:text-xs text-blue-500">Seluruh akun terdaftar</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fas fa-users text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Owner --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Owner</p>
                <p class="text-2xl md:text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['total_owner'] }}</p>
                <span class="text-[8px] md:text-xs text-yellow-500">Level Owner</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                <i class="fas fa-crown text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Superadmin --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Superadmin</p>
                <p class="text-2xl md:text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['total_superadmin'] }}</p>
                <span class="text-[8px] md:text-xs text-red-500">Level Superadmin</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center text-red-600 dark:text-red-400">
                <i class="fas fa-shield-alt text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Pengurus --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Pengurus</p>
                <p class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_pengurus'] }}</p>
                <span class="text-[8px] md:text-xs text-green-500">Level Pengurus</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                <i class="fas fa-user-tie text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Anggota UKM --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Anggota UKM</p>
                <p class="text-2xl md:text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_anggota'] }}</p>
                <span class="text-[8px] md:text-xs text-purple-500">Level Anggota</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fas fa-user-graduate text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Anggota Umum --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Anggota Umum</p>
                <p class="text-2xl md:text-3xl font-bold text-pink-600 dark:text-pink-400">{{ $stats['total_anggota_umum'] }}</p>
                <span class="text-[8px] md:text-xs text-pink-500">Non-divisi</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center text-pink-600 dark:text-pink-400">
                <i class="fas fa-user-friends text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Pengunjung --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Pengunjung</p>
                <p class="text-2xl md:text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ $stats['total_pengunjung'] }}</p>
                <span class="text-[8px] md:text-xs text-cyan-500">Level Guest</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                <i class="fas fa-globe text-base md:text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Card Total Akun Diblokir --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Akun Diblokir</p>
                <p class="text-2xl md:text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['total_blocked'] }}</p>
                <span class="text-[8px] md:text-xs text-red-500">Non-aktif / Terkunci</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center text-red-600 dark:text-red-400">
                <i class="fas fa-ban text-base md:text-xl"></i>
            </div>
        </div>
    </div>
</div>

{{-- Aktivitas & Aksi Cepat --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-4">Aktivitas Terbaru</h3>
        <ul class="space-y-2 md:space-y-3">
            @forelse($activities as $act)
                <li class="flex items-center gap-2 md:gap-3 text-xs md:text-sm">
                    <span class="w-1.5 h-1.5 md:w-2 md:h-2 bg-{{ $act['color'] }}-500 rounded-full flex-shrink-0"></span>
                    <span class="text-gray-600 dark:text-gray-300">
                        <strong>{{ $act['user'] }}</strong> {{ $act['action'] }}
                    </span>
                    <span class="ml-auto text-[8px] md:text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $act['time'] }}</span>
                </li>
            @empty
                <li class="text-gray-400 text-sm">Belum ada aktivitas.</li>
            @endforelse
        </ul>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-2 md:gap-4">
            <a href="{{ route('superadmin.management.members') }}" class="p-3 md:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-center hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                <i class="fas fa-user-plus text-lg md:text-2xl text-blue-600 dark:text-blue-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Tambah Anggota</p>
            </a>
            <a href="{{ route('superadmin.announcements.all') }}" class="p-3 md:p-4 bg-green-50 dark:bg-green-900/20 rounded-xl text-center hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                <i class="fas fa-bullhorn text-lg md:text-2xl text-green-600 dark:text-green-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Buat Pengumuman</p>
            </a>
            <a href="{{ route('superadmin.reports.analysis') }}" class="p-3 md:p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl text-center hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                <i class="fas fa-chart-line text-lg md:text-2xl text-purple-600 dark:text-purple-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Lihat Laporan</p>
            </a>
            <a href="{{ route('superadmin.contacts.all') }}" class="p-3 md:p-4 bg-pink-50 dark:bg-pink-900/20 rounded-xl text-center hover:bg-pink-100 dark:hover:bg-pink-900/40 transition">
                <i class="fas fa-address-book text-lg md:text-2xl text-pink-600 dark:text-pink-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Kelola Kontak</p>
            </a>
        </div>
    </div>
</div>
@include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])
@endsection
