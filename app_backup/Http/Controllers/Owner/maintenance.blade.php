@extends('layouts.roles.owner')

@section('title', 'Maintenance – ' . $room->name)

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-tools text-orange-500"></i>
                Maintenance – {{ $room->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: {{ $room->room_code }}</p>
        </div>
        <a href="{{ route('owner.rooms.show', $room) }}" class="mt-3 md:mt-0 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6 max-w-2xl">
        <form action="{{ route('owner.rooms.update', $room) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="Maintenance">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Saat Ini</label>
                    <p class="text-lg font-semibold text-orange-600">{{ $room->status }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan Maintenance</label>
                    <textarea name="description" rows="3" class="mt-1 w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Jelaskan alasan maintenance..."></textarea>
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition" onclick="return confirm('Ubah status ruangan menjadi Maintenance?')">
                    Set Maintenance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection