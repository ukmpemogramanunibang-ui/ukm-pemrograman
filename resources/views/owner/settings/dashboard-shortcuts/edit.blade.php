@extends('layouts.roles.owner')

@section('title', 'Edit Icon Shortcut – Owner UNICODE')
@section('page-title', 'Edit Icon Shortcut')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('owner.settings.general') }}" class="text-sm text-cyan-500 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard Builder
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-3">Edit Icon Shortcut</h1>
    </div>

    @include('owner.settings.dashboard-shortcuts._form', ['item' => $item])
</div>
@endsection