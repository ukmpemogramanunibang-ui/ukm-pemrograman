@extends('layouts.roles.owner')

@section('title', 'Semua Pengumuman – Owner UNICODE')
@section('page-title', 'Semua Pengumuman')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Semua Pengumuman</h3>
    <p class="text-gray-500 dark:text-gray-400">Daftar semua pengumuman dari seluruh divisi dan pengurus.</p>
    {{-- TODO: Tampilkan data pengumuman dari database --}}
</div>
@endsection
