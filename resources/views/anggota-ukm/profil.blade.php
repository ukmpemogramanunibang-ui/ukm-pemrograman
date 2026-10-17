@extends('layouts.role.anggota')
@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card rounded-2xl p-6 md:p-8 shadow-xl border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Profil Saya</h2>

        <div class="flex flex-col md:flex-row gap-8">
            {{-- Avatar --}}
            <div class="flex flex-col items-center">
                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=06b6d4&color=fff&size=120' }}"
                     alt="Avatar" class="w-32 h-32 rounded-full border-4 border-cyan shadow-lg">
                <button class="mt-3 text-sm text-cyan hover:underline">Ganti Foto</button>
            </div>

            {{-- Detail --}}
            <div class="flex-1 space-y-4 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><span class="text-gray-500">Nama Lengkap</span><br><span class="font-semibold">{{ Auth::user()->name }}</span></div>
                    <div><span class="text-gray-500">Email</span><br><span class="font-semibold">{{ Auth::user()->email }}</span></div>
                    <div><span class="text-gray-500">NIM</span><br><span class="font-semibold">{{ Auth::user()->nim ?? '-' }}</span></div>
                    <div><span class="text-gray-500">Level</span><br><span class="font-semibold">{{ Auth::user()->level->name ?? '-' }}</span></div>
                    <div><span class="text-gray-500">Jabatan</span><br><span class="font-semibold">{{ Auth::user()->jabatans->pluck('name')->implode(', ') ?: '-' }}</span></div>
                    <div><span class="text-gray-500">Status</span><br><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full inline-block">{{ Auth::user()->status->name ?? 'Aktif' }}</span></div>
                    <div><span class="text-gray-500">Bergabung</span><br><span class="font-semibold">{{ Auth::user()->created_at->format('d M Y') }}</span></div>
                    <div><span class="text-gray-500">Terakhir Login</span><br><span class="font-semibold">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : '-' }}</span></div>
                </div>
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button class="px-4 py-2 bg-cyan text-white rounded-lg hover:bg-cyan/90 transition">Edit Profil</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection