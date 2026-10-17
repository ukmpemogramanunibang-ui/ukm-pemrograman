@extends('layouts.roles.owner')

@section('title', 'Audit Settings – UNICODE')
@section('page-title', 'Audit Settings')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-cyan-500 font-bold mb-1">
            Audit Trail
        </p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            Audit Settings
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Riwayat lengkap perubahan konfigurasi dashboard — termasuk yang sudah dihapus.
        </p>
    </div>

    @include('owner.settings._sidebar')

    {{-- TIMELINE --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">

        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-cyan-500/15 text-cyan-500 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </span>
                <h2 class="font-bold text-gray-900 dark:text-white">Riwayat Perubahan</h2>
            </div>
            <span class="text-xs text-gray-500">{{ $items->total() }} total entri</span>
        </div>

        @if ($items->count())
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($items as $item)
                    @php
                        $actionType = $item->trashed()
                            ? 'deleted'
                            : ($item->created_at->eq($item->updated_at) ? 'created' : 'updated');

                        [$color, $icon, $label] = match ($actionType) {
                            'created' => ['green', 'fa-plus-circle', 'Dibuat'],
                            'deleted' => ['red',   'fa-trash',       'Dihapus'],
                            default   => ['amber', 'fa-edit',        'Diubah'],
                        };
                    @endphp

                    <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-{{ $color }}-500/15 text-{{ $color }}-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $icon }}"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-{{ $color }}-500/10 text-{{ $color }}-600 dark:text-{{ $color }}-400 text-[11px] font-bold uppercase">
                                        {{ $label }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[11px] font-bold uppercase">
                                        <i class="fas {{ $item->type === 'card' ? 'fa-layer-group' : 'fa-link' }}"></i>
                                        {{ $item->type }}
                                    </span>
                                    @if (!$item->is_active && !$item->trashed())
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-bold uppercase">Nonaktif</span>
                                    @endif
                                </div>

                                <p class="font-bold text-gray-900 dark:text-white">
                                    {{ $item->display_title }}
                                    <span class="text-gray-400 font-normal text-sm">({{ $item->name }})</span>
                                </p>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($item->creator)
                                        <span><i class="fas fa-user mr-1"></i> Dibuat oleh <strong>{{ $item->creator->name }}</strong></span>
                                    @endif
                                    @if ($item->updater && $item->updated_by !== $item->created_by)
                                        <span><i class="fas fa-user-edit mr-1"></i> Diubah oleh <strong>{{ $item->updater->name }}</strong></span>
                                    @endif
                                    @if ($item->parent)
                                        <span><i class="fas fa-layer-group mr-1"></i> Induk: {{ $item->parent->name }}</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-gray-400">
                                    <span><i class="far fa-clock mr-1"></i> Update: {{ $item->updated_at->translatedFormat('d M Y, H:i') }}</span>
                                    @if ($item->deleted_at)
                                        <span class="text-red-500"><i class="fas fa-trash mr-1"></i> Dihapus: {{ $item->deleted_at->translatedFormat('d M Y, H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-clipboard-list text-2xl text-gray-400"></i>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-white">Belum ada riwayat</h3>
                <p class="text-sm text-gray-500 mt-1">Perubahan dashboard akan tercatat di sini secara otomatis.</p>
            </div>
        @endif
    </div>

</div>
@endsection