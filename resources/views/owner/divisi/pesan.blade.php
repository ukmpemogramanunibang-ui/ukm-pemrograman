@extends('layouts.roles.owner')

@section('title', 'Beri Pesan Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.divisi.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-comment-dots text-green-500"></i>
            Beri Pesan Divisi
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <form action="#" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Divisi</label>
                    <select name="division_id" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500">
                        @foreach($divisions as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Pesan</label>
                    <input type="text" name="subject" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pesan</label>
                    <textarea name="message" rows="4" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" required></textarea>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition">
                    Kirim Pesan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection