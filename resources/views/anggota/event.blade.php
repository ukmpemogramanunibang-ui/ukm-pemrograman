@extends('layouts.role.anggota')
@section('title', 'Event')
@section('breadcrumb', 'Event')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">📅 Event yang Diikuti</h2>

        @php
            $events = \App\Models\Event::whereHas('peserta', function($q) {
                $q->where('user_id', Auth::id());
            })->orderBy('start_date', 'desc')->paginate(10);
        @endphp

        @if($events->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($events as $e)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-lg transition">
                        <h3 class="font-semibold text-gray-800 dark:text-white">{{ $e->name }}</h3>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($e->start_date)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($e->end_date)->format('d M Y H:i') }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 bg-cyan/10 text-cyan text-xs rounded-full">Selesai</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $events->links() }}</div>
        @else
            <p class="text-sm text-gray-400">Belum mengikuti event apapun.</p>
        @endif
    </div>
</div>
@endsection