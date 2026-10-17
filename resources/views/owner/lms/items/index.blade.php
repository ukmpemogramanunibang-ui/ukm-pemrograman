@extends('layouts.roles.owner')
@section('title', 'Item — Tugas & Materi')
@section('content')
<div class="p-5 max-w-7xl mx-auto">

    <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">
                @if($type === 'task') Tugas
                @elseif($type === 'material') Materi
                @else Semua Item
                @endif
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Daftar {{ $type ?? 'item' }} UNICODE.
            </p>
        </div>
        <a href="{{ route('owner.lms.items.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-sm
                  bg-gradient-to-r from-violet-500 to-fuchsia-500 hover:shadow-lg transition-all hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Buat Item
        </a>
    </div>

    <div class="flex items-center gap-2 mb-4 flex-wrap">
        <a href="{{ route('owner.lms.all') }}"
           class="px-3 py-1.5 rounded-lg text-xs font-bold {{ !$type ? 'bg-violet-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">Semua</a>
        <a href="{{ route('owner.lms.tasks') }}"
           class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $type === 'task' ? 'bg-violet-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">Tugas</a>
        <a href="{{ route('owner.lms.materials') }}"
           class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $type === 'material' ? 'bg-violet-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">Materi</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
            <a href="{{ route('owner.lms.items.show', $item->id) }}"
               class="block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all overflow-hidden">
                <div class="h-24 relative"
                     style="background: {{ $item->type === 'task'
                        ? 'linear-gradient(135deg,#8b5cf6,#d946ef)'
                        : 'linear-gradient(135deg,#10b981,#06b6d4)' }};">
                    <div class="absolute inset-0 flex items-center justify-center text-white/90 text-3xl">
                        <i class="fas {{ $item->type === 'task' ? 'fa-tasks' : 'fa-book-open' }}"></i>
                    </div>
                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 backdrop-blur-sm text-white border border-white/30">
                        {{ $item->type === 'task' ? 'TUGAS' : 'MATERI' }}
                    </span>
                </div>
                <div class="p-4">
                    <h3 class="font-black text-sm text-gray-900 dark:text-white line-clamp-1">{{ $item->title }}</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 min-h-[2.4em]">
                        {{ \Illuminate\Support\Str::limit($item->description ?: 'Tanpa deskripsi.', 80) }}
                    </p>
                    <div class="flex items-center gap-2 mt-3 text-[11px] text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-folder text-amber-500"></i> {{ $item->group->name ?? '-' }}</span>
                        <span>·</span>
                        <span><i class="fas fa-users text-cyan-500"></i> {{ $item->recipients_count }}</span>
                    </div>
                    @if($item->deadline_at)
                        <div class="mt-2 text-[11px] font-bold {{ $item->isOverdue() ? 'text-rose-500' : 'text-gray-600 dark:text-gray-300' }}">
                            <i class="fas fa-clock"></i> {{ $item->deadline_at->isoFormat('D MMM Y HH:mm') }}
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-16">
                <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600"></i>
                <p class="mt-4 font-bold text-gray-700 dark:text-gray-300">Belum ada item</p>
                <a href="{{ route('owner.lms.items.create') }}"
                   class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl text-white font-bold text-sm bg-gradient-to-r from-violet-500 to-fuchsia-500">
                    <i class="fas fa-plus"></i> Buat Item Pertama
                </a>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="mt-6">{{ $items->links() }}</div>
    @endif
</div>
@endsection
