@extends('layouts.roles.ketua')
@section('title', 'Kelompok – UNICODE')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell"><div class="page-content-narrow space-y-6">

    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">Group Management</p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-layer-group text-purple-500 mr-2"></i>Kelompok
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar kelompok dalam organisasi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($groups as $grp)
            <a href="{{ $safeRoute('ketua.kelompok.show', $grp->id) }}"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                      p-5 shadow-sm hover:shadow-md hover:border-emerald-400/40 transition group">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-500
                                flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $grp->name }}</p>
                        @if (!empty($grp->description))
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $grp->description }}</p>
                        @endif
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-2">
                            <i class="fas fa-users mr-1"></i>{{ $grp->members_count ?? 0 }} anggota
                        </p>
                    </div>
                    <i class="fas fa-arrow-right text-xs text-gray-400 group-hover:text-emerald-500 transition"></i>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                        p-12 text-center">
                <i class="fas fa-layer-group text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada kelompok</p>
            </div>
        @endforelse
    </div>

    @if (method_exists($groups, 'hasPages') && $groups->hasPages())
        <div class="flex justify-center">{{ $groups->links() }}</div>
    @endif
</div></div>
@endsection