@extends('partials.public-header')

@section('title', 'Eksplor Profil – UNICODE')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-center text-midnight dark:text-white mb-4">
        <span class="bg-gradient-to-r from-cyan to-violet bg-clip-text text-transparent">Eksplor</span> Anggota UNICODE
    </h1>
    <p class="text-center text-gray-500 dark:text-gray-400 mb-10 max-w-xl mx-auto">
        Temukan dan lihat profil anggota UKM Pemrograman UNICODE.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($users as $user)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 p-4 flex items-center gap-4 border border-gray-200 dark:border-gray-700">
                <img src="{{ $user->avatar_url ? asset('storage/'.$user->avatar_url) : asset('images/default-avatar.png') }}"
                     alt="{{ $user->name }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-cyan-400 shadow-md flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-gray-800 dark:text-white text-lg truncate">{{ $user->name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ $user->level->name ?? 'Anggota' }}
                    </div>
                </div>
                <a href="{{ route('profile.public', $user->id) }}"
                   class="flex-shrink-0 bg-gradient-to-r from-cyan to-electric text-white text-sm font-bold px-4 py-2 rounded-full hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-1">
                    <i class="fas fa-eye"></i> Lihat
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-20 text-gray-400">
                <i class="fas fa-users-slash text-6xl mb-4"></i>
                <p class="text-xl">Belum ada anggota terdaftar.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $users->links() }}
    </div>

    <div class="mt-6 text-center text-sm text-gray-400">
        Butuh bantuan? <a href="{{ route('kontak') }}" class="text-cyan hover:underline">Hubungi kami</a>
    </div>
</div>
@endsection