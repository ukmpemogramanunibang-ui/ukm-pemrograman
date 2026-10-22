@extends('layouts.roles.owner')
@section('title', 'Arsip & Terhapus')
@section('content')
<div class="p-5 max-w-7xl mx-auto">

    <h1 class="text-2xl font-black text-gray-900 dark:text-white">Arsip &amp; Terhapus</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Item yang di-soft delete. Owner dapat restore.</p>

    <div class="mt-5 space-y-2">
        @forelse($items as $item)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 opacity-80">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500">
                    <i class="fas {{ $item->type === 'task' ? 'fa-tasks' : 'fa-book-open' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-gray-700 dark:text-gray-300 truncate">{{ $item->title }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $item->group->name ?? '-' }} · dihapus {{ $item->deleted_at?->diffForHumans() }}
                        @if($item->delete_reason) · alasan: {{ $item->delete_reason }} @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-16 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <i class="fas fa-box-archive text-5xl text-gray-300 dark:text-gray-600"></i>
                <p class="mt-3 text-sm font-bold text-gray-700 dark:text-gray-300">Arsip kosong</p>
            </div>
        @endforelse
    </div>

    @if($items->hasPages()) <div class="mt-6">{{ $items->links() }}</div> @endif
</div>
@endsection
