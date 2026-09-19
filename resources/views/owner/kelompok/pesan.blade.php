@extends('layouts.roles.owner')

@section('title', 'Beri Pesan Kelompok')

@section('content')
<div class="p-4 md:p-6 max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-comment-dots text-green-500"></i>
            Beri Pesan Kelompok
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Kelompok Tujuan</label>
                <select name="group_id" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <option value="">Pilih Kelompok</option>
                    <option value="1">Kelompok Web Developer</option>
                    <option value="2">Kelompok AI</option>
                    <option value="3">Kelompok Mobile</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Pesan <span class="text-red-500">*</span></label>
                <input type="text" name="title" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="Judul pesan...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pesan <span class="text-red-500">*</span></label>
                <textarea name="message" rows="5" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="Tulis pesan Anda..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">Kirim Pesan</button>
                <a href="{{ route('owner.kelompok.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection