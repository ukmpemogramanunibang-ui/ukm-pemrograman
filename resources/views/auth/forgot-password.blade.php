@extends('layouts.auth_mini')

@section('title', 'Lupa Password – UKM Pemrograman UNICODE')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gradient-to-br from-[#0B1120] via-[#1E2A4A] to-[#06B6D4]">
    <div class="w-full max-w-md" data-aos="zoom-in">
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 border border-white/20">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-cyan to-blue-600 rounded-full flex items-center justify-center text-white text-3xl shadow-lg mb-4">
                    <i class="fas fa-key"></i>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">Pemulihan Akun</h2>
                <p class="text-sm text-gray-300 mt-1">
                    Masukkan email yang terhubung dengan akun UNICODE.
                    Kami akan mengirimkan kode OTP untuk verifikasi.
                </p>
            </div>

            @if(session('status'))
                <div class="bg-green-100/90 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100/90 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-at"></i>
                        </span>
                        <input type="email" name="email" required placeholder="email@example.com"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/90 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan"
                               value="{{ old('email') }}">
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-6 bg-gradient-to-r from-cyan to-blue-600 text-white font-bold py-3 rounded-full shadow-lg hover:shadow-cyan/30 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Kode OTP
                </button>
            </form>

            <div class="mt-6 rounded-2xl border border-cyan-400/20 bg-cyan-400/10 p-4">
                <div class="flex gap-3">
                    <i class="fas fa-circle-info text-cyan-300 mt-1"></i>
                    <div class="text-sm text-gray-300">
                        <p class="font-semibold text-white mb-1">Belum memiliki akun?</p>
                        <p>
                            Pendaftaran akun UNICODE dilakukan melalui
                            <strong>Superadmin</strong> atau <strong>Owner</strong>.
                            Silakan datang langsung untuk proses verifikasi dan registrasi.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-cyan hover:underline text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection