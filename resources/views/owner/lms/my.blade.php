@extends('layouts.roles.owner')

@section('title', 'Pengumpulan Saya')

@section('content')
<div class="p-4 sm:p-6 max-w-5xl mx-auto">

    <div class="mb-5">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            <i class="fas fa-user-check text-violet-500 mr-2"></i> Pengumpulan Saya
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Riwayat submission Anda. Total: <b>{{ $submissions->total() }}</b>.
        </p>
    </div>

    @if($submissions->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <i class="fas fa-inbox text-5xl text-gray-300"></i>
            <p class="mt-3 font-bold text-gray-700 dark:text-gray-300">Belum ada submission.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($submissions as $s)
                @php
                    $statusMeta = match ($s->status) {
                        'pending_review' => ['label' => 'Menunggu Review', 'cls' => 'amber'],
                        'completed', 'done' => ['label' => 'Selesai', 'cls' => 'emerald'],
                        'revision' => ['label' => 'Revisi', 'cls' => 'rose'],
                        'late' => ['label' => 'Terlambat', 'cls' => 'rose'],
                        default => ['label' => 'Draft', 'cls' => 'slate'],
                    };
                @endphp
                <a href="{{ route('owner.lms.submissions.show', $s->id) }}"
                   class="block p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 text-white
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $s->item->title ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                <i class="fas fa-folder"></i> {{ $s->item->group->name ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black
                                bg-{{ $statusMeta['cls'] }}-500/10 text-{{ $statusMeta['cls'] }}-700 dark:text-{{ $statusMeta['cls'] }}-300">
                                {{ $statusMeta['label'] }}
                            </span>
                            @if($s->score !== null)
                                <p class="text-xs font-black text-gray-700 dark:text-gray-300 mt-1">Nilai: {{ $s->score }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-5">{{ $submissions->links() }}</div>
    @endif
</div>
@endsection