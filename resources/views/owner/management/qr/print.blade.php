@extends('layouts.roles.owner')

@section('title', 'Cetak Kartu QR – Owner')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Cetak Kartu QR</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('owner.management.qr.print.process') }}" method="POST" target="_blank">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih User</label>
                    <select name="user_id" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                        <option value="">-- Pilih --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->nim ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format Kartu</label>
                    <select name="paper_size" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                        <option value="a4">A4</option>
                        <option value="a6">A6</option>
                        <option value="idcard">ID Card (85.6 x 54 mm)</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-print mr-2"></i> Cetak Kartu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection