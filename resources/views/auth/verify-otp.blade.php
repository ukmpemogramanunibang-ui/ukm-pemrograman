@extends('layouts.auth_mini')

@section('title', 'Verifikasi OTP – UNICODE')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gradient-to-br from-[#0B1120] via-[#1E2A4A] to-[#06B6D4]">
    <div class="w-full max-w-md">
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 border border-white/20">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-cyan to-blue-600 rounded-full flex items-center justify-center text-white text-3xl shadow-lg mb-4">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Verifikasi OTP</h2>
                <p class="text-sm text-gray-300 mt-1">
                    Kode 6 digit telah dikirim ke:<br>
                    <span class="font-mono text-cyan">{{ $emailMask ?? '***' }}</span>
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

            <form method="POST" action="{{ route('password.otp.verify') }}" id="otpForm">
                @csrf
                <label class="block text-sm font-medium text-gray-200 mb-2 text-center">
                    Masukkan kode OTP
                </label>

                <input type="text" name="otp" id="otpInput" maxlength="6" inputmode="numeric"
                       pattern="\d{6}" autocomplete="one-time-code" autofocus
                       class="w-full text-center text-3xl font-mono tracking-[0.5em] py-4 rounded-xl border-2 border-cyan-400/40 bg-white/95 text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan"
                       placeholder="______">

                <p class="text-xs text-gray-400 text-center mt-2">
                    <i class="fas fa-clock mr-1"></i> Kode berlaku 5 menit
                </p>

                <button type="submit"
                        class="w-full mt-6 bg-gradient-to-r from-cyan to-blue-600 text-white font-bold py-3 rounded-full shadow-lg hover:shadow-cyan/30 transition-all duration-300">
                    <i class="fas fa-check"></i> Verifikasi
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="{{ route('password.request') }}" class="text-cyan hover:underline">
                    <i class="fas fa-rotate-left mr-1"></i> Kirim ulang / ganti email
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('otpInput');
    input.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
        if (e.target.value.length === 6) {
            document.getElementById('otpForm').submit();
        }
    });
</script>
@endsection