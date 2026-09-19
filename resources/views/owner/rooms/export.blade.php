@extends('layouts.roles.owner')

@section('title', 'Export Data Ruangan – Owner')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-file-export text-emerald-500"></i>
            Export Data Ruangan
        </h1>
        <a href="{{ route('owner.rooms.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl text-green-600 dark:text-green-400 mb-4">
                <i class="fas fa-file-excel"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Excel</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Export data ruangan ke file Excel (.xlsx)</p>
            <a href="{{ route('owner.rooms.export', 'excel') }}" class="mt-4 inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                Download Excel
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl text-red-600 dark:text-red-400 mb-4">
                <i class="fas fa-file-pdf"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">PDF</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Export data ruangan ke file PDF</p>
            <a href="{{ route('owner.rooms.export', 'pdf') }}" class="mt-4 inline-block px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                Download PDF
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl text-blue-600 dark:text-blue-400 mb-4">
                <i class="fas fa-file-csv"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">CSV</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Export data ruangan ke file CSV</p>
            <a href="{{ route('owner.rooms.export', 'csv') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Download CSV
            </a>
        </div>
    </div>
</div>
@endsection