@extends('layouts.roles.owner')

@section('title', 'Edit Custom Card – Owner UNICODE')
@section('page-title', 'Edit Custom Card')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('owner.settings.general') }}" class="text-sm text-cyan-500 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard Builder
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-3">Edit Custom Card</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Perubahan akan langsung berlaku di dashboard target.
        </p>
    </div>

    @include('owner.settings.dashboard-cards._form', ['item' => $item])
</div>
@endsection