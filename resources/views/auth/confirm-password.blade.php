@extends('layouts.app')

@section('title', 'Konfirmasi Password – UKM Pemrograman UNICODE')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gradient-to-br from-[#0B1120] via-[#1E2A4A] to-[#06B6D4]">
    <div class="w-full max-w-md" data-aos="zoom-in">
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 border border-white/20">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-cyan to-blue-600 rounded-full flex items-center justify-center text-white text-3xl shadow-lg mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">Konfirmasi Password</h2>
                <p class="text-sm text-gray-300 mt-1">Masukkan password Anda untuk melanjutkan</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100/90 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" required placeholder="Masukkan password"
                               class="input-field w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/90 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400" onclick="togglePassword(this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full mt-6 bg-gradient-to-r from-cyan to-blue-600 text-white font-bold py-3 rounded-full shadow-lg hover:shadow-cyan/30 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Konfirmasi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(el) {
        const input = el.parentElement.previousElementSibling;
        const icon = el.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endpush