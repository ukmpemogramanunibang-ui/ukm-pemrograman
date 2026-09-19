@extends('layouts.roles.owner')
@section('title', 'Detail Pengurus')
@section('page-title', 'Detail Pengurus')
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Detail {{ $official->name }}</h3>
        <a href="{{ route('owner.officials.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Nama:</strong> {{ $official->name }}</p>
        <p><strong>NIM:</strong> {{ $official->nim ?? '-' }}</p>
        <p><strong>Email:</strong> {{ $official->email ?? '-' }}</p>
        <p><strong>No. Telepon:</strong> {{ $official->phone ?? '-' }}</p>
        <p><strong>Role:</strong> {{ $official->level->name ?? '-' }}</p>
        <p><strong>Poin:</strong> {{ $official->total_points ?? 0 }}</p>
    </div>
</div>
@endsection
