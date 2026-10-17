@extends('layouts.roles.ketua')
@section('title', 'Pengumuman – UNICODE')
@section('page-title', 'Pengumuman')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
                Announcements
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
                <i class="fas fa-bullhorn text-purple-500 mr-2"></i>Pengumuman
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola pengumuman untuk seluruh anggota.
            </p>
        </div>
        <a href="{{ $safeRoute('ketua.pengumuman.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                  bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold
                  shadow-lg shadow-purple-500/20 transition">
            <i class="fas fa-plus-circle"></i> Buat Pengumuman
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse ($announcements ?? [] as $ann)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                        p-5 shadow-sm hover:shadow-md hover:border-purple-400/40 transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/15 text-purple-500
                                flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bullhorn text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-base text-gray-900 dark:text-white mb-1">
                            {{ $ann->title ?? 'Pengumuman' }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-3">
                            {{ \Illuminate\Support\Str::limit($ann->content ?? $ann->message ?? '', 200) }}
                        </p>
                        <div class="flex items-center gap-3 mt-3 text-[10px] text-gray-400">
                            <span class="inline-flex items-center gap-1">
                                <i class="far fa-clock"></i>
                                {{ isset($ann->created_at) ? $ann->created_at->diffForHumans() : '-' }}
                            </span>
                            @if (isset($ann->author->name))
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-user"></i>
                                    {{ $ann->author->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ $safeRoute('ketua.pengumuman.show', $ann->id ?? 0) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                              text-xs font-semibold text-purple-600 dark:text-purple-400
                              hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <form method="POST" action="{{ $safeRoute('ketua.pengumuman.destroy', $ann->id ?? 0) }}"
                          onsubmit="return confirm('Hapus pengumuman ini?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                       text-xs font-semibold text-red-600 dark:text-red-400
                                       hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                        p-16 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-purple-100 dark:bg-purple-900/30
                            flex items-center justify-center">
                    <i class="fas fa-bullhorn text-3xl text-purple-500"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700 dark:text-gray-200 mb-1">
                    Belum ada pengumuman
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    Buat pengumuman pertama Anda untuk seluruh anggota.
                </p>
                <a href="{{ $safeRoute('ketua.pengumuman.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold transition">
                    <i class="fas fa-plus-circle"></i> Buat Sekarang
                </a>
            </div>
        @endforelse
    </div>

    @if (isset($announcements) && method_exists($announcements, 'hasPages') && $announcements->hasPages())
        <div class="flex justify-center">{{ $announcements->links() }}</div>
    @endif
</div>
</div>
@endsection