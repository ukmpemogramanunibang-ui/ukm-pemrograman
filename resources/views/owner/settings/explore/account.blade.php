@extends('layouts.roles.owner')

@section('title', 'Jelajah Akun – ' . $user->name)

@section('content')
<div class="p-4 md:p-6">

    {{-- Banner mode Explorer --}}
    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 rounded-xl">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🔎</span>
                <div>
                    <p class="font-bold text-amber-800 dark:text-amber-300">MODE JELAJAH AKUN AKTIF</p>
                    <p class="text-sm text-amber-700 dark:text-amber-400">
                        Anda sedang melihat sistem sebagai:
                        <strong>{{ $user->name }}</strong>
                        (Role: {{ $user->level->name ?? 'Unknown' }}) – ID: {{ $user->id }}
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-500">
                        Owner: <strong>{{ auth()->user()->name }}</strong> tetap menjadi akun yang terautentikasi.
                    </p>
                </div>
            </div>
            <form action="{{ route('owner.settings.explore.exit') }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition duration-200"
                        onclick="return confirm('Yakin ingin keluar dari mode Jelajah Akun?')">
                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar dari Jelajah
                </button>
            </form>
        </div>
    </div>

    {{-- Konten utama akun target --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-3xl">
                👤
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    NIM: {{ $user->nim ?? '-' }} | Role: {{ $user->level->name ?? 'Unknown' }}
                </p>
                <p class="text-xs text-gray-400">
                    User ID: {{ $user->id }} | Bergabung: {{ $user->created_at->format('d M Y') }}
                </p>
            </div>
        </div>

        {{-- Informasi detail --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="font-medium">{{ $user->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <p class="font-medium">{{ $user->status->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Universitas</p>
                <p class="font-medium">{{ $user->university->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Jurusan</p>
                <p class="font-medium">{{ $user->major->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aktif</p>
                <p class="font-medium">
                    @if($user->is_active)
                        <span class="text-green-500"><i class="fas fa-check-circle"></i> Aktif</span>
                    @else
                        <span class="text-red-500"><i class="fas fa-times-circle"></i> Tidak Aktif</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Terverifikasi</p>
                <p class="font-medium">
                    @if($user->is_verified)
                        <span class="text-green-500"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                    @else
                        <span class="text-red-500"><i class="fas fa-times-circle"></i> Belum</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Menu navigasi akun target --}}
        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Akses Cepat Akun Target</p>
            <div class="flex flex-wrap gap-2">
                <a href="#" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-user mr-1"></i> Profil
                </a>
                <a href="#" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-users mr-1"></i> Kelompok
                </a>
                <a href="#" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-sitemap mr-1"></i> Divisi
                </a>
                <a href="#" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-calendar mr-1"></i> Jadwal
                </a>
                <a href="#" class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-qrcode mr-1"></i> QR Code
                </a>
                <a href="#" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-cog mr-1"></i> Pengaturan
                </a>
            </div>
            <p class="text-xs text-gray-400 mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                Semua tindakan yang Anda lakukan pada akun ini akan tercatat di log dengan identitas Owner.
            </p>
        </div>
    </div>
</div>
@endsection