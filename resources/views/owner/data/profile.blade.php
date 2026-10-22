@extends('layouts.roles.owner')

@section('title', 'Data Pribadi – Owner UNICODE')
@section('page-title', 'Data Pribadi')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Data Pribadi</h3>
    <p class="text-gray-500 dark:text-gray-400">Lihat dan kelola data pribadi Anda.</p>
    <div class="mt-4 space-y-2">
        <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>NIM:</strong> {{ Auth::user()->nim }}</p>
    </div>
</div>
@endsection
