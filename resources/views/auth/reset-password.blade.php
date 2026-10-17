@extends('layouts.auth_mini')

@section('title', 'Reset Password – UNICODE')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gradient-to-br from-[#0B1120] via-[#1E2A4A] to-[#06B6D4]">
    <div class="w-full max-w-md">
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 border border-white/20">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-green-500 to-cyan-500 rounded-full flex items-center justify-center text-white text-3xl shadow-lg mb-4">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Buat Password Baru</h2>
                <p class="text-sm text-gray-300 mt-1">
                    Minimal 8 karakter, kombinasi huruf besar, huruf kecil, dan angka.
                </p>
            </div>

            @if($errors->any())
                <div class="bg-red-100/90 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update.custom') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Password Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/90 text-gray-800 focus:outline-none focus:ring-2 focus:ring-cyan"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password_confirmation" required minlength="8"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/90 text-gray-800 focus:outline-none focus:ring-2 focus:ring-cyan"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-6 bg-gradient-to-r from-green-500 to-cyan-600 text-white font-bold py-3 rounded-full shadow-lg transition-all duration-300">
                    <i class="fas fa-save"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection