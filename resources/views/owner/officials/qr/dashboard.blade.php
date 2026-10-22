@extends('layouts.roles.owner')

@section('title', 'Dashboard Pengurus')
@section('page-title', 'Dashboard Pengurus')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Pengurus</h3>
    <p class="text-gray-500 dark:text-gray-400 mt-2">Halaman ini menampilkan informasi pengurus.</p>
    <div class="mt-4">
        <a href="{{ route('owner.officials.index') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
            <i class="fas fa-list mr-1"></i> Lihat Daftar Pengurus
        </a>
    </div>
</div>
@endsection