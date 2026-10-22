@extends('layouts.roles.owner')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Riwayat Absensi</h1>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        @forelse($sessions as $s)
            <div class="border-b py-2">
                <div class="font-medium">{{ $s->title }}</div>
                <div class="text-xs text-gray-500">{{ $s->scheduled_start_at }}</div>
            </div>
        @empty
            <p class="text-gray-500">Belum ada riwayat.</p>
        @endforelse
        <div class="mt-4">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection