@extends('layouts.roles.owner')
@section('title', 'Judul Halaman')
@section('page-title', 'Judul Halaman')
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Judul Halaman</h3>
        <a href="{{ route('owner.officials.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>
    <p class="text-gray-500 dark:text-gray-400">Konten halaman ini sedang dalam pengembangan.</p>
</div>
@endsection
