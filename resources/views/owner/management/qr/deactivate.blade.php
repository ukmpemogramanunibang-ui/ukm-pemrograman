@extends('layouts.roles.owner')

@section('title', 'Nonaktifkan QR – Owner')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Nonaktifkan QR Code</h1>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <p class="text-gray-600 dark:text-gray-300">Pilih user yang QR-nya akan dinonaktifkan (status menjadi BLOCKED).</p>
        <form action="{{ route('owner.management.qr.deactivate.process') }}" method="POST">
            @csrf
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih User</label>
                <select name="user_id" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                    <option value="">-- Pilih --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan</label>
                <textarea name="reason" rows="3" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2" placeholder="Contoh: Pelanggaran aturan"></textarea>
            </div>
            <button type="submit" class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-ban mr-2"></i> Nonaktifkan QR
            </button>
        </form>
    </div>
</div>
@endsection