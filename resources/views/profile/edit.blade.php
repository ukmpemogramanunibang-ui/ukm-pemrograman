{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.roles.ketua')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-user-circle text-primary text-3xl"></i> Profil Saya
        </h2>
        <p class="text-soft-gray mt-1">Kelola informasi akun Anda.</p>

        <form action="{{ route('profile.update') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm text-soft-gray mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" 
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition">
            </div>

            <div>
                <label class="block text-sm text-soft-gray mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition">
            </div>

            <div>
                <label class="block text-sm text-soft-gray mb-1">Jabatan Tertinggi</label>
                <input type="text" name="jabatan_tertinggi" value="{{ old('jabatan_tertinggi', Auth::user()->jabatan_tertinggi ?? '') }}" 
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition">
            </div>

            <div>
                <label class="block text-sm text-soft-gray mb-1">Avatar URL</label>
                <input type="text" name="avatar" value="{{ old('avatar', Auth::user()->avatar ?? '') }}" 
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition">
                <p class="text-xs text-soft-gray mt-1">Masukkan URL gambar avatar, atau biarkan kosong untuk menggunakan inisial.</p>
            </div>

            <div class="flex gap-3 pt-4 border-t border-white/10">
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-semibold px-6 py-2 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('ketua.dashboard') }}" class="text-soft-gray hover:text-white px-6 py-2 rounded-lg border border-white/10 hover:bg-white/5 transition">
                    Batal
                </a>
            </div>
        </form>

        <hr class="border-white/10 my-6">

        <div>
            <h3 class="text-lg font-semibold text-white mb-2">Hapus Akun</h3>
            <p class="text-sm text-soft-gray">Tindakan ini tidak dapat dibatalkan. Akun Anda akan dihapus permanen.</p>
            <form action="{{ route('profile.destroy') }}" method="POST" class="mt-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-danger/20 text-danger hover:bg-danger/30 font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection