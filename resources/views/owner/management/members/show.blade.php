@extends('layouts.owner')

@section('page-title', 'Detail Anggota - ' . ($member->name ?? ''))

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="flex mb-4 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ route('owner.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300"><i class="fas fa-home mr-1"></i> Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('owner.management.members.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Anggota</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-700 dark:text-gray-300 font-medium truncate">{{ $member->name }}</li>
        </ol>
    </nav>

    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('owner.management.members.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar
        </a>
    </div>

    {{-- Kartu Profil --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        {{-- Header --}}
        <div class="relative h-32 bg-gradient-to-r from-blue-500 to-cyan-500">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -bottom-12 left-6 flex items-end gap-4">
                <div class="relative">
                    <img src="{{ $member->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&background=2563EB&color=fff&size=120' }}"
                         alt="{{ $member->name }}"
                         class="w-24 h-24 rounded-full border-4 border-white dark:border-gray-800 shadow-lg object-cover">
                    <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                </div>
                <div class="pb-2">
                    <h1 class="text-2xl font-bold text-white drop-shadow-lg">{{ $member->name }}</h1>
                    <p class="text-sm text-white/80 drop-shadow">{{ $member->level->display_name ?? 'Anggota' }}</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="pt-16 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kolom Kiri --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i> Informasi Pribadi
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">NIM</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $member->nim ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Email</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $member->email ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Nomor Anggota</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $member->member_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Telepon</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $member->phone ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $member->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                {{ $member->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Terverifikasi</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $member->is_verified ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                                {{ $member->is_verified ? 'Ya' : 'Belum' }}
                            </span>
                        </div>
                        @if($member->university)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Universitas</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $member->university->name ?? '-' }}</span>
                        </div>
                        @endif
                        @if($member->major)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Program Studi</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $member->major->name ?? '-' }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                        <i class="fas fa-chart-line text-green-500 mr-2"></i> Statistik
                    </h2>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $member->total_points ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Poin</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $member->rank ?? 'Pemula' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Rank</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $member->pointHistories->count() ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Transaksi Poin</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $member->qrCodes->count() ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">QR Code</p>
                        </div>
                    </div>

                    @if($member->bio)
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bio</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $member->bio }}</p>
                    </div>
                    @endif

                    @if($member->skills && is_array($member->skills))
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Skill</h3>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($member->skills as $skill)
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
                <a href="{{ route('owner.management.members.edit', $member->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form action="{{ route('owner.management.members.destroy', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus anggota ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </form>
                <a href="{{ route('owner.user', $member->user_uuid) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-user mr-2"></i> Lihat Profil Publik
                </a>
                <a href="{{ route('owner.management.members.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection