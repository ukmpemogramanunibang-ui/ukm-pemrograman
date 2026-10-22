@extends('layouts.ketua')

@section('title', 'Detail Pengurus – ' . ($official->name ?? ''))

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], string $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;

    $roleName = match ((int) $official->level_id) {
        3 => 'Ketua',
        4 => 'Pengurus',
        default => optional($official->level)->name ?? 'User',
    };

    $roleGradient = match ((int) $official->level_id) {
        3 => 'from-purple-500 via-fuchsia-500 to-pink-500',
        4 => 'from-indigo-500 via-blue-500 to-cyan-500',
        default => 'from-gray-500 to-gray-600',
    };
@endphp

<div class="p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto space-y-6">

    {{-- BACK --}}
    <a href="{{ $safeRoute('ketua.manajemen-pengurus.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
              hover:text-indigo-600 dark:hover:text-indigo-400 transition group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar Pengurus
    </a>

    {{-- HERO --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl
                border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm">
        <div class="h-36 bg-gradient-to-r {{ $roleGradient }} relative overflow-hidden">
            <div class="absolute inset-0 opacity-20"
                 style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;"></div>
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/20 blur-3xl"></div>
        </div>

        <div class="px-6 pb-6 -mt-16">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="shrink-0">
                    <div class="w-32 h-32 rounded-2xl bg-gradient-to-br {{ $roleGradient }}
                                flex items-center justify-center text-white font-black text-5xl
                                ring-4 ring-white dark:ring-gray-900 shadow-xl">
                        {{ strtoupper(substr($official->name ?? '?', 0, 1)) }}
                    </div>
                </div>

                <div class="flex-1 sm:pb-2 min-w-0">
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                     bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                            <i class="fas fa-user-tag text-[9px] mr-1"></i> {{ $roleName }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                     {{ $official->is_active
                                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'
                                        : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                            <span class="w-1.5 h-1.5 rounded-full inline-block mr-1
                                {{ $official->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                            {{ $official->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white truncate">
                        {{ $official->name ?? 'Tanpa Nama' }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono">
                        <i class="fas fa-id-card mr-1"></i> NIM: {{ $official->nim ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $fields = [
                ['icon' => 'fa-envelope',      'label' => 'Email',        'value' => $official->email,        'color' => 'cyan'],
                ['icon' => 'fa-phone',         'label' => 'Telepon',      'value' => $official->phone,        'color' => 'emerald'],
                ['icon' => 'fa-university',    'label' => 'Universitas',  'value' => optional($official->university)->name, 'color' => 'indigo'],
                ['icon' => 'fa-graduation-cap','label' => 'Jurusan',      'value' => optional($official->major)->name,      'color' => 'purple'],
                ['icon' => 'fa-hashtag',       'label' => 'ID',           'value' => $official->id,           'color' => 'blue'],
                ['icon' => 'fa-calendar',      'label' => 'Bergabung',    'value' => optional($official->created_at)->format('d M Y'), 'color' => 'amber'],
            ];

            $colorMap = [
                'cyan'    => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
                'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
                'indigo'  => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
                'purple'  => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                'blue'    => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                'amber'   => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
            ];
        @endphp

        @foreach($fields as $f)
        <div class="bg-white dark:bg-gray-900 rounded-xl
                    border border-gray-200 dark:border-gray-800 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg {{ $colorMap[$f['color']] }}
                            flex items-center justify-center shrink-0">
                    <i class="fas {{ $f['icon'] }} text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        {{ $f['label'] }}
                    </p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ $f['value'] ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- JABATAN --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl
                border border-gray-200 dark:border-gray-800 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500
                        flex items-center justify-center shadow-md">
                <i class="fas fa-briefcase text-white text-sm"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Jabatan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $official->jabatans->count() }} jabatan terdaftar
                </p>
            </div>
        </div>

        @if($official->jabatans && $official->jabatans->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($official->jabatans as $j)
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                 {{ ($j->pivot->is_primary ?? false)
                                    ? 'bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/40 dark:to-yellow-900/30 ring-2 ring-amber-400/40'
                                    : 'bg-indigo-50 dark:bg-indigo-900/30' }}">
                        @if($j->pivot->is_primary ?? false)
                            <i class="fas fa-crown text-amber-500 text-xs"></i>
                        @else
                            <i class="fas fa-briefcase text-indigo-500 text-xs"></i>
                        @endif
                        <span class="text-sm font-bold
                                     {{ ($j->pivot->is_primary ?? false)
                                        ? 'text-amber-900 dark:text-amber-300'
                                        : 'text-indigo-800 dark:text-indigo-300' }}">
                            {{ $j->name }}
                        </span>
                    </span>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <i class="fas fa-briefcase text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Belum ada jabatan yang ditetapkan.
                </p>
            </div>
        @endif
    </div>

    {{-- ACTIONS --}}
    <div class="flex justify-end">
        <a href="{{ $safeRoute('ketua.manajemen-pengurus.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                  bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                  text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>
</div>
@endsection