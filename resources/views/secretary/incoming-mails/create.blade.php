@extends('layouts.roles.' . auth()->user()->getRoleLayout())
@section('title', 'Tambah Surat Masuk')
@section('page-title', 'Tambah Surat Masuk')
@section('content')

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Form Tambah Surat Masuk</h3>
        <a href="{{ route('owner.officials.secretary.incoming-mails.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('owner.officials.secretary.incoming-mails.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                @error('nomor_surat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                    <option value="baru">Baru</option>
                    <option value="dibaca">Dibaca</option>
                    <option value="diproses">Diproses</option>
                    <option value="diteruskan">Diteruskan</option>
                    <option value="selesai">Selesai</option>
                    <option value="diarsipkan">Diarsipkan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat') }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Diterima <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_diterima" value="{{ old('tanggal_diterima', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Pengirim <span class="text-red-500">*</span></label>
                <input type="text" name="pengirim" value="{{ old('pengirim') }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="kategori" value="{{ old('kategori') }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Perihal <span class="text-red-500">*</span></label>
                <input type="text" name="perihal" value="{{ old('perihal') }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tujuan</label>
                <input type="text" name="tujuan" value="{{ old('tujuan') }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Lampiran</label>
                <input type="file" name="lampiran" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <p class="text-xs text-gray-500 mt-1">Max 5MB (PDF, DOC, JPG, PNG)</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Catatan</label>
                <textarea name="catatan" rows="3" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('owner.officials.secretary.incoming-mails.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</a>
        </div>
    </form>
</div>

@endsection