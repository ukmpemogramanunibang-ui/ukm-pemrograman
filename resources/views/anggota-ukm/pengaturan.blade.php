@extends('layouts.role.anggota')
@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">⚙️ Pengaturan Akun</h2>

        <form method="POST" action="{{ route('anggota.pengaturan.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                <input type="text" name="name" value="{{ Auth::user()->name }}"
                       class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-cyan outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" value="{{ Auth::user()->email }}"
                       class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-cyan outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" placeholder="********"
                       class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-cyan outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="********"
                       class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-cyan outline-none">
            </div>

            <div class="pt-4">
                <button type="submit" class="px-6 py-2 bg-cyan text-white rounded-lg hover:bg-cyan/90 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection