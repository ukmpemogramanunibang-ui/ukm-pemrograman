@extends('layouts.roles.owner')

@section('title', 'Create Custom Card – Owner UNICODE')
@section('page-title', 'Create Custom Card')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('owner.settings.general') }}"
               class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-semibold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard Builder
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-2">
                Create Custom Card
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Card otomatis muncul di dashboard target tanpa perlu ubah kode.
            </p>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 p-4">
            <p class="font-bold text-red-800 dark:text-red-200 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> Ada kesalahan pada form
            </p>
            <ul class="mt-2 space-y-0.5 text-xs text-red-700 dark:text-red-300 list-disc list-inside">
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    @include('owner.settings.dashboard-cards._form')
</div>
@endsection