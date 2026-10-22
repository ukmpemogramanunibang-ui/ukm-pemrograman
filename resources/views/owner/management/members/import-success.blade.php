@extends('layouts.roles.owner')

@section('title', 'Import Berhasil – Owner UNICODE')
@section('page-title', 'Import Data Anggota')

@push('styles')
<style>
    .success-icon {
        font-size: 4rem;
        color: #22c55e;
        animation: bounceIn 0.6s ease;
    }
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
    .count-number {
        font-size: 3rem;
        font-weight: 800;
        color: #1f2937;
    }
    .dark .count-number {
        color: #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center border border-gray-200 dark:border-gray-700">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-4">Import Berhasil!</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Sebanyak <span class="count-number">{{ $count }}</span> anggota baru berhasil ditambahkan ke database.
        </p>
        <div class="mt-6 flex justify-center gap-4">
            <a href="{{ route('owner.management.members.index') }}" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-md transition">
                <i class="fas fa-arrow-left mr-2"></i> Ke Daftar Anggota
            </a>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
            Anda akan dialihkan secara otomatis dalam <span id="countdown">5</span> detik...
        </p>
    </div>
</div>

<script>
    let seconds = 5;
    const countdownEl = document.getElementById('countdown');
    const interval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "{{ route('owner.management.members.index') }}";
        }
    }, 1000);
</script>
@if(session('errors'))
    <div class="mt-4 text-left bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-700 max-h-40 overflow-y-auto">
        <p class="font-semibold text-red-700 dark:text-red-300">Beberapa data gagal disimpan:</p>
        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
            @foreach(session('errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection