{{-- resources/views/anggota/dashboard.blade.php --}}
@extends('layouts.role.anggota')

@section('title', 'Dashboard Anggota – UNICODE')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $user = Auth::user();
    // Data dari controller: $totalPoin, $totalEvent, $totalPresensi, $totalSertifikat, $rank, $activities
@endphp

<div class="space-y-6 max-w-7xl mx-auto animate-fade-in-up">

   {{-- Welcome Banner --}}
<div class="relative bg-gradient-to-br from-cyan-400 via-blue-400 to-indigo-500 dark:from-cyan-600 dark:via-blue-700 dark:to-indigo-800 p-6 md:p-8 rounded-3xl shadow-2xl text-black dark:text-white overflow-hidden group">
    <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 dark:bg-white/5 rounded-full blur-3xl animate-pulse-slow"></div>
    <div class="absolute -left-20 -bottom-20 w-56 h-56 bg-white/5 dark:bg-white/5 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1.5s;"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/5 dark:from-black/10 to-transparent"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold drop-shadow-lg flex items-center gap-3 text-black dark:text-white">
                    👋 Hallo, <span class="underline decoration-black/30 dark:decoration-white/30 underline-offset-4 hover:decoration-black/70 dark:hover:decoration-white/70 transition-all duration-300">{{ $user->name }}</span>!
                </h1>
                <span class="px-3 py-1 bg-black/10 dark:bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold border border-black/10 dark:border-white/10 animate-pulse-slow text-black dark:text-white">🟢 Online</span>
            </div>
            <p class="text-sm md:text-base text-black/80 dark:text-white/90 mt-2 max-w-2xl drop-shadow-sm">
                Selamat datang di dashboard pribadi Anda. Teruslah berkarya dan berinovasi untuk masa depan yang lebih baik!
            </p>
            <div class="flex flex-wrap gap-2 mt-4">
                <span class="px-3 py-1.5 bg-black/10 dark:bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold border border-black/10 dark:border-white/10 hover:bg-black/20 dark:hover:bg-white/30 transition-all duration-300 text-black dark:text-white">
                    <i class="fas fa-user-tag mr-1"></i> {{ $user->roles->pluck('name')->implode(', ') }}
                </span>
                <span class="px-3 py-1.5 bg-black/10 dark:bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold border border-black/10 dark:border-white/10 hover:bg-black/20 dark:hover:bg-white/30 transition-all duration-300 text-black dark:text-white">
                    <i class="fas fa-id-card mr-1"></i> NIM: {{ $user->nim ?? '-' }}
                </span>
                @if($user->jabatans->count())
                    <span class="px-3 py-1.5 bg-black/10 dark:bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold border border-black/10 dark:border-white/10 hover:bg-black/20 dark:hover:bg-white/30 transition-all duration-300 text-black dark:text-white">
                        <i class="fas fa-briefcase mr-1"></i> {{ $user->jabatans->pluck('name')->implode(', ') }}
                    </span>
                @endif
            </div>
        </div>
        <div class="hidden lg:block text-7xl opacity-20 select-none group-hover:opacity-30 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 text-black dark:text-white">
            <i class="fas fa-user-astronaut"></i>
        </div>
    </div>
</div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="glass-card rounded-2xl p-5 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl hover:scale-[1.03] hover:border-cyan-400/50 transition-all duration-300 cursor-default group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/0 to-cyan-500/0 group-hover:from-cyan-500/5 group-hover:to-cyan-500/10 transition-all duration-500"></div>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Poin Saya</p>
                    <h3 class="text-2xl md:text-3xl font-bold mt-1 text-gray-800 dark:text-white">{{ number_format($totalPoin) }}</h3>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Peringkat #{{ $rank }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-500 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-cyan-500/10">
                    <i class="fas fa-medal text-lg"></i>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl hover:scale-[1.03] hover:border-purple-400/50 transition-all duration-300 cursor-default group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-purple-500/0 group-hover:from-purple-500/5 group-hover:to-purple-500/10 transition-all duration-500"></div>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Event Diikuti</p>
                    <h3 class="text-2xl md:text-3xl font-bold mt-1 text-gray-800 dark:text-white">{{ $totalEvent }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-500/20 text-purple-500 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-purple-500/10">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl hover:scale-[1.03] hover:border-green-400/50 transition-all duration-300 cursor-default group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/0 to-green-500/0 group-hover:from-green-500/5 group-hover:to-green-500/10 transition-all duration-500"></div>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Presensi</p>
                    <h3 class="text-2xl md:text-3xl font-bold mt-1 text-gray-800 dark:text-white">{{ $totalPresensi }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-green-500/20 text-green-500 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-green-500/10">
                    <i class="fas fa-clipboard-check text-lg"></i>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl hover:scale-[1.03] hover:border-yellow-400/50 transition-all duration-300 cursor-default group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/0 to-yellow-500/0 group-hover:from-yellow-500/5 group-hover:to-yellow-500/10 transition-all duration-500"></div>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Sertifikat</p>
                    <h3 class="text-2xl md:text-3xl font-bold mt-1 text-gray-800 dark:text-white">{{ $totalSertifikat }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-yellow-500/20 text-yellow-500 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-yellow-500/10">
                    <i class="fas fa-certificate text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Profil & Aktivitas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profil --}}
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 lg:col-span-1 hover:shadow-2xl transition-all duration-300 group">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white border-b border-gray-200/50 dark:border-gray-700/50 pb-3 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-500 flex items-center justify-center group-hover:scale-110 transition-all duration-300">
                    <i class="fas fa-user-circle text-sm"></i>
                </span>
                Profil Saya
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 py-1 rounded-lg transition-all duration-200">
                    <span class="text-gray-500 dark:text-gray-400">Nama</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 py-1 rounded-lg transition-all duration-200">
                    <span class="text-gray-500 dark:text-gray-400">Email</span>
                    <span class="font-semibold text-gray-800 dark:text-white truncate max-w-[140px]">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 py-1 rounded-lg transition-all duration-200">
                    <span class="text-gray-500 dark:text-gray-400">NIM</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $user->nim ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 py-1 rounded-lg transition-all duration-200">
                    <span class="text-gray-500 dark:text-gray-400">Level</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $user->level->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 py-1 rounded-lg transition-all duration-200">
                    <span class="text-gray-500 dark:text-gray-400">Jabatan</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $user->jabatans->pluck('name')->implode(', ') ?: '-' }}</span>
                </div>
                <div class="flex justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 py-1 rounded-lg transition-all duration-200">
                    <span class="text-gray-500 dark:text-gray-400">Status</span>
                    <span class="px-2.5 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        {{ $user->status->name ?? 'Aktif' }}
                    </span>
                </div>
            </div>
            <a href="{{ route('anggota.profil') }}" class="mt-5 inline-flex items-center gap-2 text-cyan-500 hover:text-cyan-600 dark:hover:text-cyan-400 transition-all duration-300 font-medium text-sm group-hover:gap-3">
                Lihat profil lengkap
                <i class="fas fa-arrow-right text-xs transition-all duration-300 group-hover:translate-x-1"></i>
            </a>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 lg:col-span-2 hover:shadow-2xl transition-all duration-300">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white border-b border-gray-200/50 dark:border-gray-700/50 pb-3 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-500 flex items-center justify-center">
                    <i class="fas fa-clock text-sm"></i>
                </span>
                Aktivitas Terbaru
            </h2>
            @if($activities && $activities->count())
                <ul class="space-y-2">
                    @foreach($activities as $act)
                        <li class="flex items-start gap-3 text-sm border-b border-gray-100 dark:border-gray-800 pb-2.5 last:border-0 last:pb-0 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 rounded-xl p-2.5 transition-all duration-200 group">
                            <span class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-xs flex-shrink-0 group-hover:scale-110 transition-all duration-300">
                                <i class="fas fa-{{ $act->type === 'plus' ? 'plus' : 'minus' }}"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-700 dark:text-white truncate">{{ $act->description ?? 'Aktivitas' }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                    <i class="far fa-clock"></i> {{ $act->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="text-xs font-bold flex-shrink-0 {{ $act->type === 'plus' ? 'text-green-500' : 'text-red-500' }} bg-{{ $act->type === 'plus' ? 'green' : 'red' }}-50 dark:bg-{{ $act->type === 'plus' ? 'green' : 'red' }}-900/20 px-2.5 py-1 rounded-full">
                                {{ $act->type === 'plus' ? '+' : '-' }}{{ $act->points }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-10 text-gray-400 dark:text-gray-500">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="font-medium">Belum ada aktivitas</p>
                    <p class="text-xs mt-1">Mulai ikuti event dan kumpulkan poin!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Quote --}}
    <div class="text-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200 dark:border-gray-700 pt-4 mt-2 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-gray-500 dark:text-gray-400">“Kode adalah puisi, dan kita adalah penyairnya.” — UNICODE</p>
        <div class="flex items-center gap-3 text-[10px]">
            <span class="flex items-center gap-1 text-gray-400 dark:text-gray-500"><i class="fas fa-code text-cyan-400"></i> v1.0</span>
            <span class="text-gray-300 dark:text-gray-600">|</span>
            <span class="text-gray-400 dark:text-gray-500">{{ date('Y') }} UNICODE</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    @keyframes fadeInUp { 0% { opacity:0; transform:translateY(20px); } 100% { opacity:1; transform:translateY(0); } }
    .animate-pulse-slow { animation: pulseSlow 3s ease-in-out infinite; }
    @keyframes pulseSlow { 0%,100% { transform:scale(1); opacity:0.6; } 50% { transform:scale(1.05); opacity:1; } }
    .glass-card { background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
    .dark .glass-card { background: rgba(31,41,55,0.5); }
</style>
{{-- Deteksi Offline/Online Global --}}
<script>
    (function() {
        // Simpan URL sebelum offline
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('previousPage', window.location.href);
        });

        // Saat offline, redirect ke halaman offline (kecuali sudah di offline)
        window.addEventListener('offline', function() {
            if (window.location.pathname !== '/offline') {
                sessionStorage.setItem('previousPage', window.location.href);
                window.location.href = '/offline';
            }
        });

        // Saat online, jika di halaman offline, redirect ke halaman sebelumnya
        window.addEventListener('online', function() {
            if (window.location.pathname === '/offline') {
                const prev = sessionStorage.getItem('previousPage') || '/';
                window.location.href = prev;
            }
        });

        // Cek status saat halaman dimuat
        if (!navigator.onLine && window.location.pathname !== '/offline') {
            sessionStorage.setItem('previousPage', window.location.href);
            window.location.href = '/offline';
        }

        // Jika sudah di offline tapi ternyata online, redirect balik
        if (navigator.onLine && window.location.pathname === '/offline') {
            const prev = sessionStorage.getItem('previousPage') || '/';
            window.location.href = prev;
        }
    })();
</script>
@endpush
@endsection