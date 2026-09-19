@extends('layouts.roles.owner')

@section('title', 'Pengumuman Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.divisi.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-bullhorn text-yellow-500"></i>
            Pengumuman Divisi
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('owner.divisi.announcement.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Pengumuman</label>
                    <input type="text" name="title" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pengumuman</label>
                    <textarea name="content" rows="4" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Divisi</label>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="target_all" value="1" id="target_all">
                            <label for="target_all" class="text-sm text-gray-700 dark:text-gray-300">Semua Divisi</label>
                        </div>
                        @foreach($divisions as $d)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="target_division_ids[]" value="{{ $d->id }}" id="div_{{$d->id}}">
                                <label for="div_{{$d->id}}" class="text-sm text-gray-700 dark:text-gray-300">{{ $d->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition">
                    Kirim Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection