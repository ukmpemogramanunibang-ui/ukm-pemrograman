{{-- resources/views/owner/rooms/maintenance-index.blade.php --}}
@extends('layouts.roles.owner')

@section('title', 'Daftar Maintenance Ruangan')

@section('content')
<div class="p-4 md:p-6">
    <h1 class="text-2xl font-bold">Daftar Ruangan dalam Maintenance</h1>
    <div class="mt-4">
        @if($rooms->count())
            <table class="w-full border">
                <thead>
                    <tr>
                        <th>Nama Ruangan</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Mulai Maintenance</th>
                        <th>Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr>
                        <td>{{ $room->name }}</td>
                        <td>{{ $room->room_code }}</td>
                        <td>{{ $room->status }}</td>
                        <td>
                            @if($room->maintenances->first())
                                {{ $room->maintenances->first()->start_datetime->format('d/m/Y H:i') }}
                            @else -
                            @endif
                        </td>
                        <td>
                            @if($room->maintenances->first())
                                {{ $room->maintenances->first()->end_datetime->format('d/m/Y H:i') }}
                            @else -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('owner.rooms.show', $room) }}" class="text-blue-600">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $rooms->links() }}
        @else
            <p>Tidak ada ruangan dalam maintenance.</p>
        @endif
    </div>
</div>
@endsection