@extends('layouts.roles.owner')

@section('title', 'Notification Center')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Notification Center</h1>
        <div class="flex gap-2">
            <form action="{{ route('owner.notifications.read-all') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </button>
            </form>
            <form action="{{ route('owner.notifications.destroy-all') }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    <i class="fas fa-trash"></i> Hapus Semua
                </button>
            </form>
        </div>
    </div>

    @if($notifications->count())
        <ul class="space-y-3">
            @foreach($notifications as $notification)
                <li class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow flex items-start justify-between
                           {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-blue-500' }}">
                    <div>
                        <p class="text-gray-800 dark:text-gray-200">
                            {{ $notification->data['message'] ?? 'Notifikasi' }}
                        </p>
                        <small class="text-gray-500 text-sm">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <div class="flex gap-2">
                        @if(!$notification->read_at)
                            <form action="{{ route('owner.notifications.read', $notification->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('owner.notifications.destroy', $notification->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow text-center">
            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">Belum ada notifikasi.</p>
        </div>
    @endif
</div>
@endsection