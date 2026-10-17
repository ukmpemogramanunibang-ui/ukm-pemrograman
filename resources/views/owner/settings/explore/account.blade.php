@extends('layouts.roles.owner')

@section('title', 'Sedang Menjelajahi – ' . $user->name)

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- Banner Mode --}}
    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-800 p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">Exploration Mode Aktif</p>
                    <h1 class="mt-1 text-lg font-bold text-indigo-900 dark:text-indigo-200">
                        Sedang menjelajahi: <b>{{ $user->name }}</b>
                    </h1>
                    <p class="text-sm text-indigo-700 dark:text-indigo-300 mt-1">
                        NIM: {{ $user->nim ?? '-' }} · Anda tetap terautentikasi sebagai Owner.
                    </p>
                </div>
            </div>

            <form action="{{ route('owner.settings.explore.exit') }}" method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Keluar dari mode jelajah?')"
                        class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold">
                    <i class="fas fa-sign-out-alt"></i> Keluar dari Jelajah
                </button>
            </form>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <div class="flex flex-col md:flex-row gap-6">
            @php
                $avatar = $user->avatar_url;
                $avatarUrl = $avatar
                    ? (\Illuminate\Support\Str::startsWith($avatar, ['http://','https://'])
                        ? $avatar
                        : \Illuminate\Support\Facades\Storage::url($avatar))
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff&size=200';
            @endphp

            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-28 h-28 rounded-3xl object-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=200';">

            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $user->nim ?? '-' }}</p>

                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold">
                        {{ $user->level->name ?? 'Anggota' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 text-xs font-bold">
                        {{ $user->status->name ?? 'Unknown' }}
                    </span>
                    @if($user->is_active)
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 text-xs font-bold">
                            Aktif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500">Total Poin</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format((int) ($user->total_points ?? 0)) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500">Rank</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $user->rank ?? '—' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500">Email</p>
            <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white truncate">{{ $user->email ?? '—' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500">Jabatan Utama</p>
            <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                {{ $user->jabatans()->wherePivot('is_primary', true)->first()?->name ?? '—' }}
            </p>
        </div>
    </div>

    {{-- Detail --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-id-card text-indigo-500 mr-2"></i> Detail Akun
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Universitas</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->university->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Jurusan</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->major->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Bergabung</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->created_at?->translatedFormat('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Login Terakhir</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-indigo-500 mt-1"></i>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Owner tetap terautentikasi sebagai Owner</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Sistem tidak mengganti <code class="px-1 bg-slate-100 dark:bg-slate-800 rounded text-xs">auth()->user()</code>.
                    Konteks jelajah disimpan di session <code class="px-1 bg-slate-100 dark:bg-slate-800 rounded text-xs">owner.explore.*</code> dan diaudit.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection