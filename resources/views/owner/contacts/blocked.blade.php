@extends('layouts.roles.owner')

@section('title', 'Akun Diblokir – Kontak')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Akun Diblokir</h1>
        <a href="{{ route('owner.dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-300">Daftar akun yang diblokir.</p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 text-center">
                    <i class="fas fa-users text-3xl text-blue-500 dark:text-blue-400"></i>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">0</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5 text-center">
                    <i class="fas fa-check-circle text-3xl text-green-500 dark:text-green-400"></i>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">0</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Aktif</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5 text-center">
                    <i class="fas fa-times-circle text-3xl text-red-500 dark:text-red-400"></i>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">0</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Tidak Aktif</p>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Avatar</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIM</th>
                            <th class="px-4 py-3">Telepon</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">
                                <i class="fas fa-info-circle mr-2"></i> Data akan tampil di sini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 text-sm text-yellow-700 dark:text-yellow-300">
                <i class="fas fa-info-circle mr-2"></i> Halaman ini masih dalam pengembangan. Data akan diambil dari database secara real-time.
            </div>
        </div>
    </div>
</div>
@endsection
