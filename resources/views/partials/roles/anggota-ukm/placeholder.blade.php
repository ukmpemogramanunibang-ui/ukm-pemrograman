{{-- resources/views/anggota/placeholder.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@php
    $title       = $title       ?? 'Halaman';
    $icon        = $icon        ?? 'fa-tools';
    $tone        = $tone        ?? 'cyan';

    $toneMap = [
        'cyan'    => ['from' => 'from-cyan-500',    'to' => 'to-blue-600'],
        'amber'   => ['from' => 'from-amber-500',   'to' => 'to-orange-600'],
        'emerald' => ['from' => 'from-emerald-500', 'to' => 'to-teal-600'],
        'indigo'  => ['from' => 'from-indigo-500',  'to' => 'to-purple-600'],
        'blue'    => ['from' => 'from-blue-500',    'to' => 'to-indigo-600'],
        'orange'  => ['from' => 'from-orange-500',  'to' => 'to-red-600'],
        'purple'  => ['from' => 'from-purple-500',  'to' => 'to-fuchsia-600'],
    ];
    $c = $toneMap[$tone] ?? $toneMap['cyan'];
@endphp

@section('title', $title . ' — Anggota')
@section('breadcrumb', $title)

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-3xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mb-5">
        <a href="{{ route('anggota.dashboard') }}" class="hover:text-cyan-600 transition">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $title }}</span>
    </nav>

    {{-- Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl
                border border-gray-200 dark:border-gray-700
                overflow-hidden shadow-sm">

        {{-- Top bar gradient --}}
        <div class="h-2 bg-gradient-to-r {{ $c['from'] }} {{ $c['to'] }}"></div>

        {{-- Body --}}
        <div class="p-8 md:p-12 text-center">

            {{-- Icon --}}
            <div class="relative w-24 h-24 mx-auto mb-6">
                <div class="absolute inset-0 rounded-3xl
                            bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                            opacity-20 blur-xl animate-pulse"></div>
                <div class="relative w-24 h-24 rounded-3xl
                            bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                            flex items-center justify-center shadow-2xl">
                    <i class="fas {{ $icon }} text-white text-4xl"></i>
                </div>
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white">
                {{ $title }}
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 max-w-md mx-auto leading-relaxed">
                Halaman ini sedang dalam pengembangan.
                Fitur akan segera tersedia untuk Anda.
            </p>

            {{-- Badge --}}
            <div class="mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-full
                        bg-amber-50 dark:bg-amber-900/20
                        text-amber-700 dark:text-amber-300
                        border border-amber-200 dark:border-amber-700/40
                        text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <i class="fas fa-hourglass-half"></i>
                Segera Hadir
            </div>

            {{-- CTA --}}
            <div class="mt-8">
                <a href="{{ route('anggota.dashboard') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-gradient-to-r {{ $c['from'] }} {{ $c['to'] }}
                          hover:opacity-90 text-white text-sm font-bold
                          shadow-md transition">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <p class="text-center text-[11px] text-gray-400 dark:text-gray-500 mt-6">
        <i class="fas fa-code-branch mr-1"></i>
        Halaman ini dikelola oleh Tim Pengembang UNICODE
    </p>
</div>
@endsection