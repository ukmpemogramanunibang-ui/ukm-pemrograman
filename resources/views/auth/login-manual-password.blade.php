@extends('layouts.app')

@section('title', 'Masukkan Password – UKM Pemrograman UNICODE')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(31, 41, 55, 0.75);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .input-glow:focus {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.5);
            border-color: #06b6d4;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="w-full max-w-md" data-aos="fade-up" data-aos-duration="600">
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-unicode.png') }}" alt="UNICODE Logo" class="w-16 h-16 mx-auto rounded-2xl shadow-lg" onerror="this.onerror=null;this.src='https://placehold.co/100x100/06b6d4/white?text=UN'">
            <h1 class="mt-4 text-2xl font-extrabold bg-gradient-to-r from-cyan to-violet bg-clip-text text-transparent">Selamat Datang</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Halo, {{ $user->name }}</p>
        </div>

        <div class="glass-card rounded-2xl shadow-2xl p-6 md:p-8">
            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.manual') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                {{-- Opsional: tampilkan NIM sebagai info --}}
                <div class="text-center mb-2">
                    <span class="text-sm text-gray-500">NIM: {{ $user->nim }}</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="input-glow w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-cyan focus:border-transparent outline-none transition">
                </div>

                {{-- Tampilkan input PIN hanya jika user level_id 1 (Owner) atau sesuai kebutuhan --}}
                @if(in_array($user->level_id, [1]))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PIN (6 digit)</label>
                    <input type="password" name="pin" maxlength="6"
                           class="input-glow w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-cyan focus:border-transparent outline-none transition">
                </div>
                @endif

                <button type="submit"
                        class="w-full bg-gradient-to-r from-cyan to-blue-600 text-white font-bold py-3 rounded-full shadow-lg hover:shadow-cyan/30 transition-all transform hover:scale-[1.02]">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>

            <div class="mt-4 text-center text-sm">
                <a href="{{ route('login.manual.form') }}" class="text-cyan hover:underline">← Gunakan NIM lain</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 500, once: true });
</script>
@endpush