@extends('layouts.roles.' . auth()->user()->getRoleLayout())
@section('title', 'Arsip Sekretaris')
@section('page-title', 'Arsip Sekretaris')
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Daftar Arsip</h3>
        <a href="{{ route('owner.officials.secretary.archives.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-1"></i> Tambah Arsip
        </a>
    </div>
    <p class="text-gray-500 dark:text-gray-400">Halaman ini sedang dalam pengembangan.</p>
</div>
@endsection
