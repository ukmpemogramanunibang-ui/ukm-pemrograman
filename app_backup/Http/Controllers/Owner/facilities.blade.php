@extends('layouts.roles.owner')

@section('title', 'Kelola Fasilitas – ' . $room->name)

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-couch text-purple-500"></i>
                Kelola Fasilitas – {{ $room->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: {{ $room->room_code }}</p>
        </div>
        <a href="{{ route('owner.rooms.show', $room) }}" class="mt-3 md:mt-0 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        @if($room->facilities->count())
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($room->facilities as $facility)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-medium">{{ $facility->name }}</span>
                            <span class="text-sm text-gray-500 ml-2">×{{ $facility->quantity }}</span>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('owner.rooms.facilities.destroy', ['room' => $room, 'facility' => $facility]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus fasilitas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-400 text-center py-4">Belum ada fasilitas.</p>
        @endif

        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold mb-2">Tambah Fasilitas</h3>
            <form action="{{ route('owner.rooms.facilities.store', $room) }}" method="POST" class="flex flex-wrap gap-3">
                @csrf
                <input type="text" name="name" placeholder="Nama fasilitas" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                <input type="number" name="quantity" value="1" min="1" class="w-20 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Tambah</button>
            </form>
        </div>
    </div>
</div>
@endsection