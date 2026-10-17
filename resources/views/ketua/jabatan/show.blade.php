@extends('layouts.roles.ketua')
@section('title', ($jabatan->name ?? 'Detail Jabatan') . ' – UNICODE')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell"><div class="page-content-narrow space-y-6">
    <a href="{{ $safeRoute('ketua.jabatan.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-purple-600 transition">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 rounded-2xl bg-purple-500/15 text-purple-500
                        flex items-center justify-center flex-shrink-0">
                <i class="fas fa-briefcase text-2xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $jabatan->name }}</h1>
                @if (!empty($jabatan->description))
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $jabatan->description }}</p>
                @endif
                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold mt-3">
                    <i class="fas fa-users mr-1"></i>{{ $jabatan->users->count() ?? 0 }} anggota
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white">
                <i class="fas fa-users text-purple-500 mr-2"></i>Anggota dengan Jabatan Ini
            </h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($jabatan->users ?? [] as $u)
                @php
                    $avatar = $u->avatar_url ? asset('storage/'.$u->avatar_url)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=7c3aed&color=fff';
                @endphp
                <div class="flex items-center gap-3 px-5 py-3">
                    <img src="{{ $avatar }}" class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $u->name }}</p>
                        <p class="text-[10px] font-mono text-gray-500">{{ $u->nim ?? '-' }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">Belum ada anggota</div>
            @endforelse
        </div>
    </div>
</div></div>
@endsection