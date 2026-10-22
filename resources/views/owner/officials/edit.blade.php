@extends('layouts.roles.owner')
@section('title', 'Edit Pengurus')
@section('page-title', 'Edit Pengurus')
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Edit {{ $official->name }}</h3>
        <a href="{{ route('owner.officials.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>
    <form action="{{ route('owner.officials.update', $official->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label><input type="text" name="name" value="{{ $official->name }}" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIM</label><input type="text" name="nim" value="{{ $official->nim }}" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label><input type="email" name="email" value="{{ $official->email }}" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Telepon</label><input type="text" name="phone" value="{{ $official->phone }}" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label><select name="level_id" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"><option value="3" {{ $official->level_id == 3 ? 'selected' : '' }}>Ketua</option><option value="4" {{ $official->level_id == 4 ? 'selected' : '' }}>Pengurus</option></select></div>
        </div>
        <div class="mt-6 flex gap-2"><button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">Simpan Perubahan</button><a href="{{ route('owner.officials.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300">Batal</a></div>
    </form>
</div>
@endsection
