@extends('layouts.roles.owner')

@section('title', 'Konversi QR – Owner')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Konversi QR Code</h1>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <p class="text-gray-600 dark:text-gray-300">Fitur ini akan memindahkan QR dari kolom <code>users.qr_token</code> ke tabel <code>user_qr_codes</code>.</p>
        <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
            <p class="text-yellow-700 dark:text-yellow-300"><i class="fas fa-exclamation-triangle mr-2"></i> Pastikan tidak ada duplikasi. Proses ini irreversible.</p>
        </div>
        <form action="{{ route('owner.management.qr.konversi.process') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                <i class="fas fa-sync mr-2"></i> Mulai Konversi
            </button>
        </form>
    </div>
</div>
@endsection