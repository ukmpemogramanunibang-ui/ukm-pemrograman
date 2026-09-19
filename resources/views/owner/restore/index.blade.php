@extends('layouts.owner')

@section('page-title', '🛡️ Pemulihan Data')

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-shield-alt text-emerald-500"></i>
                Pemulihan Data
            </h1>
            <span class="text-sm text-gray-500 dark:text-gray-400 mt-2 md:mt-0">
                <i class="fas fa-database mr-1"></i>
                Database: <strong>{{ config('database.connections.mysql.database') }}</strong>
            </span>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-4 py-3 rounded shadow-sm mb-6 flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 px-4 py-3 rounded shadow-sm mb-6 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Tombol Aksi Cepat --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            {{-- Backup --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <i class="fas fa-database text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-white">📦 Backup Sekarang</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Simpan snapshot semua data ke protected database & file JSON.</p>
                        <form action="{{ route('owner.restore.backup') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow">
                                <i class="fas fa-save mr-2"></i> Backup Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Restore dari Protected DB --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                        <i class="fas fa-undo-alt text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-white">♻️ Pulihkan dari Protected DB</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kembalikan semua data dari database proteksi ke database utama.</p>
                        <form action="{{ route('owner.restore.run') }}" method="POST" class="mt-3" onsubmit="return confirm('⚠️ Yakin ingin memulihkan semua data dari protected database? Data saat ini akan ditimpa.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow">
                                <i class="fas fa-undo-alt mr-2"></i> Pulihkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar File Backup dalam Tabel --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-file-archive text-amber-500"></i>
                    File Backup Tersimpan
                    <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full text-gray-600 dark:text-gray-300">
                        {{ count($files ?? []) }}
                    </span>
                </h2>
                @if(!empty($files) && count($files) > 0)
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i> Klik Restore untuk memulihkan dari file tertentu
                    </span>
                @endif
            </div>

            @if(empty($files) || count($files) === 0)
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                    <p class="text-sm">Belum ada file backup. Klik tombol <strong>"Backup Data"</strong> untuk membuat yang pertama.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-5 py-3">Nama File</th>
                                <th scope="col" class="px-5 py-3">Ukuran</th>
                                <th scope="col" class="px-5 py-3">Terakhir Diubah</th>
                                <th scope="col" class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-5 py-3 font-mono text-xs">
                                        <i class="fas fa-file-code text-amber-400 mr-2"></i>
                                        {{ $file }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $size = Storage::size('backups/' . $file);
                                            $sizeFormatted = number_format($size / 1024, 1) . ' KB';
                                            if ($size > 1024 * 1024) $sizeFormatted = number_format($size / 1048576, 1) . ' MB';
                                        @endphp
                                        {{ $sizeFormatted }}
                                    </td>
                                    <td class="px-5 py-3 text-xs">
                                        {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified('backups/' . $file))->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Restore dari file --}}
                                            <form action="{{ route('owner.restore.run') }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Yakin ingin memulihkan dari file {{ $file }}? Data saat ini akan ditimpa.');">
                                                @csrf
                                                <input type="hidden" name="file" value="{{ $file }}">
                                                <button type="submit" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium px-2 py-1 rounded hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition" title="Restore dari file ini">
                                                    <i class="fas fa-undo-alt mr-1"></i> Restore
                                                </button>
                                            </form>
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            {{-- Download --}}
                                            <a href="{{ route('owner.restore.download', ['filename' => $file]) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition" title="Download file">
                                                <i class="fas fa-download mr-1"></i> Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/50">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan {{ count($files) }} file backup. File yang lebih lama otomatis dihapus (maksimal 30).
                </div>
            @endif
        </div>

        {{-- Informasi Tambahan --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-700/30 rounded-xl p-4 text-sm text-yellow-800 dark:text-yellow-200 flex items-start gap-3">
                <i class="fas fa-lightbulb text-yellow-500 mt-0.5"></i>
                <div>
                    <strong>Tips:</strong> Backup otomatis berjalan setiap hari jam 02.00 dan setiap 6 jam. Anda juga bisa backup manual kapan saja.
                </div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-700/30 rounded-xl p-4 text-sm text-blue-800 dark:text-blue-200 flex items-start gap-3">
                <i class="fas fa-shield-alt text-blue-500 mt-0.5"></i>
                <div>
                    <strong>Keamanan:</strong> Data yang dipulihkan dari protected database atau file JSON akan mengembalikan semua akun, poin, QR, dan transaksi sesuai snapshot terakhir.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection