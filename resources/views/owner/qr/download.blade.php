@extends('layouts.roles.owner')

@section('title', 'Download QR – Owner')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Download QR Code</h1>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <p class="text-gray-600 dark:text-gray-300">Unduh gambar QR Code untuk user tertentu.</p>
        <form action="{{ route('owner.management.qr.download.get') }}" method="GET" class="mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User</label>
                    <select name="user_id" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2">
                        <option value="">-- Pilih --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                    <select name="format" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2">
                        <option value="png">PNG</option>
                        <option value="jpg">JPG</option>
                        <option value="jpeg">JPEG</option>
                        <option value="webp">WEBP</option>
                        <option value="svg">SVG</option>
                        <option value="pdf">PDF (2 Halaman)</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-download mr-2"></i> Download
            </button>
        </form>
    </div>
</div>
@endsection