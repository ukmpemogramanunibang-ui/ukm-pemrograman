@extends('layouts.roles.owner')

@section('title', 'Verifikasi Keamanan – Explorer')

@section('content')
<div class="p-4 md:p-6 max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">

        <div class="text-center mb-6">
            <div class="text-4xl mb-2">🔐</div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Verifikasi Keamanan Owner</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Untuk melanjutkan menjelajahi akun <strong>{{ $user->name }}</strong>,
                masukkan password khusus fitur Jelajah.
            </p>
        </div>

        <form action="{{ route('owner.settings.explore.verify.post') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Explorer</label>
                    <input type="password" name="password" required
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                           placeholder="Masukkan password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                           placeholder="Ketik ulang password">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    Verifikasi & Lanjutkan
                </button>
                <a href="{{ url('/owner/settings/explore') }}"
                   class="px-6 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>

        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
            <p class="text-xs text-yellow-700 dark:text-yellow-300">
                <i class="fas fa-info-circle mr-1"></i>
                Password Explorer adalah password khusus, bukan password akun Owner.
            </p>
        </div>
    </div>
</div>
@endsection