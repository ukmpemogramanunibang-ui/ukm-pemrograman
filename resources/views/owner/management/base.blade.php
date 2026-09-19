@extends('layouts.roles.owner')

@section('title', $title ?? 'Manajemen – Owner UNICODE')
@section('page-title', $pageTitle ?? 'Manajemen')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $heading ?? 'Halaman Manajemen' }}</h3>
        @if(isset($actions))
            <div class="flex gap-2">{{ $actions }}</div>
        @endif
    </div>
    <p class="text-gray-500 dark:text-gray-400">{{ $description ?? 'Konten halaman ini sedang dalam pengembangan.' }}</p>

    @if(isset($content))
        {{ $content }}
    @endif
</div>
@endsection