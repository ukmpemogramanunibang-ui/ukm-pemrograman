@extends('layouts.roles.ketua')
@section('title', 'LeaderPoint – UNICODE')
@section('page-title', 'LeaderPoint')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
            Leaderboard
        </p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-medal text-purple-500 mr-2"></i>LeaderPoint
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Top 20 anggota dengan poin tertinggi.
        </p>
    </div>

    {{-- TOP 3 PODIUM --}}
    @if ($topUsers->count() >= 3)
        <div class="grid grid-cols-3 gap-3 md:gap-6 items-end">
            @php
                $podiumOrder = [1 => ['order' => 'order-2', 'h' => 'h-32', 'c' => 'from-gray-300 to-gray-400', 'emoji' => '🥈'],
                                0 => ['order' => 'order-1', 'h' => 'h-40', 'c' => 'from-amber-400 to-amber-600', 'emoji' => '🥇'],
                                2 => ['order' => 'order-3', 'h' => 'h-28', 'c' => 'from-orange-400 to-orange-600', 'emoji' => '🥉']];
            @endphp
            @foreach ($topUsers->take(3) as $index => $u)
                @php
                    $meta = $podiumOrder[$index];
                    $avatar = $u->avatar_url
                        ? asset('storage/' . $u->avatar_url)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=7c3aed&color=fff&size=128';
                @endphp
                <div class="{{ $meta['order'] }} text-center">
                    <div class="bg-gradient-to-br {{ $meta['c'] }} rounded-2xl {{ $meta['h'] }}
                                shadow-lg flex flex-col items-center justify-end pb-3 pt-3 relative">
                        <div class="absolute -top-10">
                            <img src="{{ $avatar }}" class="w-16 h-16 rounded-full border-4 border-white
                                                           shadow-xl object-cover">
                        </div>
                        <span class="text-2xl">{{ $meta['emoji'] }}</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $u->name }}</p>
                        <p class="text-lg font-extrabold text-purple-600 dark:text-purple-400">
                            {{ number_format($u->total_points ?? 0) }}
                        </p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">poin</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- LIST TABLE --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-ranking-star text-purple-500"></i> Peringkat Lengkap
            </h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($topUsers as $index => $u)
                @php
                    $avatar = $u->avatar_url
                        ? asset('storage/' . $u->avatar_url)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=7c3aed&color=fff';
                    $rank = $index + 1;
                    $medalColor = match($rank) {
                        1 => 'text-amber-400',
                        2 => 'text-gray-400',
                        3 => 'text-orange-500',
                        default => 'text-gray-300 dark:text-gray-600',
                    };
                @endphp
                <div class="flex items-center gap-4 px-5 py-3 hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition">
                    <div class="w-9 text-center flex-shrink-0">
                        <span class="text-lg font-extrabold {{ $medalColor }}">
                            {{ $rank }}
                        </span>
                    </div>
                    <img src="{{ $avatar }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $u->name }}</p>
                        <p class="text-[10px] text-gray-500 font-mono">{{ $u->nim ?? '-' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-lg font-extrabold text-purple-600 dark:text-purple-400">
                            {{ number_format($u->total_points ?? 0) }}
                        </p>
                        <p class="text-[9px] text-gray-400 uppercase font-bold">poin</p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-medal text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada data poin</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
</div>
@endsection