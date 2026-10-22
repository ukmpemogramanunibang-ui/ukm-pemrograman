@extends('layouts.owner')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-xl text-center">
        <div class="text-6xl mb-4">⚠️</div>
        <h1 class="text-2xl font-bold text-red-600">Terjadi Kesalahan</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-2">{{ $message ?? 'Gagal memuat dashboard.' }}</p>
        @if(isset($error))
            <p class="text-xs text-slate-500 mt-4 p-2 bg-slate-100 dark:bg-slate-800 rounded-lg">{{ $error }}</p>
        @endif
        <a href="{{ route('owner.dashboard') }}" class="mt-6 inline-block px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection