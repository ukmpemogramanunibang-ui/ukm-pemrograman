@extends('layouts.roles.owner')
@section('title', 'Dibuat Oleh Saya')
@section('content')
<div class="p-5 max-w-7xl mx-auto">

    <h1 class="text-2xl font-black text-gray-900 dark:text-white">Dibuat Oleh Saya</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Semua tugas &amp; materi yang kamu buat.</p>

    <div class="mt-5 space-y-2">
        @forelse($items as $item)
            <a href="{{ route('owner.lms.items.show', $item->id) }}"
               class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-violet-300 hover:shadow-md transition-all">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                            {{ $item->type === 'task' ? 'bg-violet-100 dark:bg-violet-900/30 text-violet-600' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' }}">
                    <i class="fas {{ $item->type === 'task' ? 'fa-tasks' : 'fa-book-open' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $item->title }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $item->group->name ?? '-' }} · {{ $item->recipients_count }} penerima · {{ $item->created_at?->diffForHumans() }}
                    </p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>
        @empty
            <div class="text-center py-16 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <i class="fas fa-inbox text-5xl text-gray-300 dark:text-gray-600"></i>
                <p class="mt-3 text-sm font-bold text-gray-700 dark:text-gray-300">Belum ada item</p>
                <a href="{{ route('owner.lms.items.create') }}"
                   class="inline-flex items-center gap-1 mt-3 px-4 py-2 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-violet-500 to-fuchsia-500">
                    <i class="fas fa-plus"></i> Buat Item
                </a>
            </div>
        @endforelse
    </div>

    @if($items->hasPages()) <div class="mt-6">{{ $items->links() }}</div> @endif
</div>
@endsection
