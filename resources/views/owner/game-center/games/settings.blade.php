@extends('layouts.owner')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Pengaturan Game: {{ $game->name }}</h1>
        <a href="{{ route('owner.game.games.index') }}" class="text-slate-600 hover:underline">← Kembali</a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
        <form action="{{ route('owner.game.games.update', $game) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block font-semibold mb-2">Nama Game</label>
                    <input type="text" name="name" value="{{ $game->name }}" class="w-full border rounded-xl p-3 dark:bg-slate-800">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full border rounded-xl p-3 dark:bg-slate-800">{{ $game->description }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Status</label>
                    <select name="status" class="w-full border rounded-xl p-3 dark:bg-slate-800">
                        <option value="draft" {{ $game->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="testing" {{ $game->status == 'testing' ? 'selected' : '' }}>Testing</option>
                        <option value="published" {{ $game->status == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="maintenance" {{ $game->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="archived" {{ $game->status == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection