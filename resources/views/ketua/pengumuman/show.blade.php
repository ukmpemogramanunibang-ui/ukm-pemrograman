@extends('layouts.roles.ketua')
@section('title', $announcement->title ?? 'Detail Pengumuman')
@section('page-title', 'Detail Pengumuman')

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

    <article class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                    overflow-hidden shadow-sm">
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 px-6 py-8 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                             bg-white/20 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider">
                    <i class="fas fa-bullhorn"></i>
                    {{ strtoupper($announcement->category ?? 'umum') }}
                </span>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-white mt-3">
                    {{ $announcement->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-3 mt-4 text-xs text-white/80">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-user"></i>
                        {{ $announcement->author->name ?? 'Admin' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="far fa-clock"></i>
                        {{ $announcement->created_at->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 lg:p-8">
            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                {!! nl2br(e($announcement->content)) !!}
            </div>
        </div>
    </article>
</div>
</div>
@endsection