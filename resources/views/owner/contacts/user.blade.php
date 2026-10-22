@extends('layouts.roles.owner')

@section('title', 'Profil User – Owner UNICODE')
@section('page-title', 'Profil User')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-user-circle text-blue-600 mr-2"></i> Profil User
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Detail lengkap pengguna.</p>
            </div>
            <a href="{{ route('owner.contacts.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Avatar --}}
            <div class="text-center">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-4xl font-bold mx-auto">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mt-3">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->level->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->member_number ?? '-' }}</p>
            </div>

            {{-- Info --}}
            <div class="md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><span class="text-gray-500 dark:text-gray-400">NIM:</span> <span class="font-medium">{{ $user->nim ?? '-' }}</span></div>
                    <div><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium">{{ $user->email ?? '-' }}</span></div>
                    <div><span class="text-gray-500 dark:text-gray-400">Telepon:</span> <span class="font-medium">{{ $user->phone ?? '-' }}</span></div>
                    <div><span class="text-gray-500 dark:text-gray-400">Status:</span> <span class="font-medium {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></div>
                    <div><span class="text-gray-500 dark:text-gray-400">Online:</span> <span class="font-medium">{{ $user->is_online ? '🟢 Online' : '⚪ Offline' }}</span></div>
                    <div><span class="text-gray-500 dark:text-gray-400">Bergabung:</span> <span class="font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span></div>
                </div>

                @if($user->jabatans->count())
                <div class="mt-4">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">Jabatan</h4>
                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                        @foreach($user->jabatans as $jabatan)
                            <li>{{ $jabatan->name }} @if($jabatan->pivot->is_primary) <span class="text-yellow-500">(Utama)</span> @endif</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('owner.chats.private', $user->user_uuid) }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition text-sm">
                        <i class="fas fa-comment mr-1"></i> Chat
                    </a>
                    <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection