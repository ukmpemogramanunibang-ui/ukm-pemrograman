@extends('layouts.ketua')
@section('title', 'Divisi Tidak Ditemukan')

@section('content')
<div class="p-6 lg:p-8 max-w-4xl mx-auto">

    <a href="{{ route('ketua.divisi.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
              hover:text-cyan-600 dark:hover:text-cyan-400 transition mb-6 group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar Divisi
    </a>

    <div class="bg-white dark:bg-gray-900 rounded-3xl
                border border-gray-200 dark:border-gray-800 p-12 text-center shadow-sm">
        <div class="w-20 h-20 mx-auto rounded-3xl
                    bg-gradient-to-br from-amber-100 to-orange-100
                    dark:from-amber-900/30 dark:to-orange-900/20
                    flex items-center justify-center mb-5">
            <i class="fas fa-search text-amber-500 text-3xl"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">
            Divisi Tidak Ditemukan
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
            Divisi dengan ID
            <code class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 font-mono text-xs">
                {{ $divisionId }}
            </code>
            tidak ada di database.
        </p>
        <a href="{{ route('ketua.divisi.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                  bg-gradient-to-r from-cyan-600 to-blue-600 text-white
                  text-sm font-bold hover:shadow-lg transition">
            <i class="fas fa-list"></i> Lihat Semua Divisi
        </a>
    </div>
</div>
@endsection