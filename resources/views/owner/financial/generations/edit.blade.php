@extends('layouts.roles.owner')

@section('title', 'Edit Generasi - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.generations.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Generasi: {{ $generation->name }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 max-w-2xl">
        <form action="{{ route('owner.financial.generations.update', $generation) }}" method="POST">
            @csrf @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Generasi <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $generation->code) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-amber-500 focus:border-amber-500" required>
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Generasi <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $generation->name) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-amber-500 focus:border-amber-500" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Masuk <span class="text-red-500">*</span></label>
                    <input type="number" name="entry_year" value="{{ old('entry_year', $generation->entry_year) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-amber-500 focus:border-amber-500" min="2000" max="2099" required>
                    @error('entry_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-amber-500 focus:border-amber-500">{{ old('description', $generation->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ $generation->is_active ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-amber-500 focus:ring-amber-500">
                    <label class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i> Update
                </button>
                <a href="{{ route('owner.financial.generations.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-6 py-2 rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection