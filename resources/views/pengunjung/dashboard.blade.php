@extends('layouts.roles.pengunjung')

@section('title', 'Dashboard Pengunjung – UNICODE')
@section('page-title', 'Pengunjung Dashboard')

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="space-y-5 md:space-y-6">

    {{-- Banner --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500
                dark:from-cyan-700 dark:via-teal-700 dark:to-emerald-700
                p-5 md:p-7 text-white shadow-2xl">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-white/20 backdrop-blur
                        flex items-center justify-center flex-shrink-0">
                <i class="fas fa-globe text-2xl md:text-3xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-lg md:text-2xl font-extrabold truncate">
                    Halo, {{ $user->name ?? 'Pengunjung' }}! 👋
                </h1>
                <p class="text-xs md:text-sm text-white/90 mt-1">
                    Jelajahi UNICODE dan akses cepat tersedia di bawah.
                </p>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $links = [
                ['route' => 'explore',           'icon' => 'fa-compass',    'label' => 'Jelajahi',      'color' => 'cyan'],
                ['route' => 'room.public.index', 'icon' => 'fa-door-open',  'label' => 'Ruangan',       'color' => 'blue'],
                ['route' => 'kontak',            'icon' => 'fa-envelope',   'label' => 'Kontak',        'color' => 'purple'],
                ['route' => 'dokumentasi',       'icon' => 'fa-book',       'label' => 'Dokumentasi',   'color' => 'amber'],
            ];
        @endphp
        @foreach ($links as $l)
            @if (\Illuminate\Support\Facades\Route::has($l['route']))
                <a href="{{ route($l['route']) }}"
                   class="bg-white dark:bg-gray-800 rounded-2xl p-4
                          border border-gray-200 dark:border-gray-700
                          text-center hover:shadow-lg transition">
                    <i class="fas {{ $l['icon'] }} text-2xl text-{{ $l['color'] }}-500 mb-2"></i>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $l['label'] }}</p>
                </a>
            @endif
        @endforeach
    </div>

    {{-- FOLDER SHORTCUT --}}
    @include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 dark:text-gray-500
                border-t border-gray-200 dark:border-gray-700 pt-4">
        <p>© {{ date('Y') }} UNICODE · Pengunjung</p>
    </div>
</div>
@endsection