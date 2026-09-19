@extends('layouts.roles.owner')

@section('title', 'Pengumuman Kelompok')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-bullhorn text-yellow-500"></i>
            Pengumuman Kelompok
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">

        @if($groups->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-layer-group text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada kelompok.</p>
                <a href="{{ route('owner.kelompok.custom') }}" class="text-purple-600 hover:underline mt-2 inline-block">
                    Buat kelompok sekarang
                </a>
            </div>
        @else
            {{-- Form Pilih Kelompok --}}
            <form method="GET" action="{{ route('owner.kelompok.pengumuman') }}" class="mb-6 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Kelompok</label>
                    <select name="group_id" onchange="this.form.submit()" class="mt-1 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-yellow-500">
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition">
                        Tampilkan
                    </button>
                </div>
            </form>

            @if($selectedGroup)
                {{-- Form Buat Pengumuman --}}
                <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/20">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-3">Buat Pengumuman</h3>
                    <form action="{{ route('owner.kelompok.announcement.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="group_ids[]" value="{{ $selectedGroup->id }}">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                                <input type="text" name="title" required
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-yellow-500"
                                    placeholder="Judul pengumuman...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pengumuman</label>
                                <textarea name="content" required rows="3"
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-yellow-500"
                                    placeholder="Tulis isi pengumuman..."></textarea>
                            </div>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="is_important" value="1">
                                    <span class="font-medium">⚠️ Pengumuman Penting</span>
                                </label>
                                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                                    <i class="fas fa-paper-plane"></i> Kirim Pengumuman
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Daftar Pengumuman --}}
                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Kelompok: <strong>{{ $selectedGroup->name }}</strong>
                        <span class="ml-4 text-xs text-gray-400">({{ $announcements->count() }} pengumuman)</span>
                    </p>
                </div>

                <div class="space-y-3">
                    @forelse($announcements as $announcement)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition
                            {{ $announcement->is_important ? 'border-l-4 border-l-red-500 bg-red-50/30 dark:bg-red-900/10' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-gray-800 dark:text-white">
                                            {{ $announcement->title }}
                                        </h3>
                                        @if($announcement->is_important)
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-full text-[9px] font-bold">
                                                <i class="fas fa-exclamation-circle"></i> Penting
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 rounded-full text-[9px]">
                                            {{ $announcement->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $announcement->content }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400">
                                        <span><i class="fas fa-user mr-1"></i> {{ $announcement->creator->name ?? 'Sistem' }}</span>
                                        <span><i class="fas fa-clock mr-1"></i> {{ $announcement->created_at->diffForHumans() }}</span>
                                        @if($announcement->published_at)
                                            <span><i class="fas fa-calendar-check mr-1"></i> {{ $announcement->published_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                        <i class="fas fa-users mr-1"></i> {{ $announcement->groups->count() }} kelompok
                                    </span>
                                </div>
                            </div>
                            {{-- Tampilkan target kelompok --}}
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($announcement->groups as $g)
                                    <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-[9px]">
                                        {{ $g->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-bullhorn text-3xl block mb-2 opacity-30"></i>
                            Belum ada pengumuman untuk kelompok ini.
                        </div>
                    @endforelse
                </div>
            @else
                <p class="text-gray-500">Silakan pilih kelompok dari dropdown di atas.</p>
            @endif
        @endif
    </div>
</div>
@endsection