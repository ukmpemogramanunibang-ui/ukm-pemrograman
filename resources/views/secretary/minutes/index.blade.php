@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', 'Notulen – Sekretaris UNICODE')
@section('page-title', 'Notulen')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                    <i class="fas fa-sticky-note text-yellow-500 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Notulen Rapat</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Kelola catatan, hasil rapat, keputusan, dan tindak lanjut organisasi.
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('owner.officials.secretary.minutes.create') }}"
           class="inline-flex items-center justify-center px-5 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl shadow-lg shadow-yellow-500/20 transition">
            <i class="fas fa-plus mr-2"></i>
            Buat Notulen
        </a>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Notulen</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalMinutes ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Draft</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $draftMinutes ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $completedMinutes ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-500/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Diarsipkan</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $archivedMinutes ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center">
                    <i class="fas fa-archive text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('owner.officials.secretary.minutes.index') }}">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                <div class="lg:col-span-5 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari judul rapat, tempat, pimpinan..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 outline-none">
                </div>
                <div class="lg:col-span-2">
                    <select name="status"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-white focus:ring-2 focus:ring-yellow-500 outline-none">
                        <option value="">Semua Status</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                        <option value="archived" @selected(request('status') === 'archived')>Diarsipkan</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <select name="month"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-white focus:ring-2 focus:ring-yellow-500 outline-none">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected((string) request('month') === (string) $m)>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <select name="year"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-white focus:ring-2 focus:ring-yellow-500 outline-none">
                        <option value="">Semua Tahun</option>
                        @foreach(($years ?? []) as $year)
                            <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <button type="submit"
                            class="w-full h-full min-h-[48px] px-4 py-3 bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-xl transition">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Daftar Notulen --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Notulen</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Seluruh catatan rapat yang tersimpan dalam sistem.</p>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ method_exists($minutes ?? [], 'total') ? $minutes->total() : 0 }} data
            </div>
        </div>

        @forelse($minutes ?? [] as $minute)
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                <div class="flex flex-col xl:flex-row xl:items-center gap-5">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-xl bg-yellow-500/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sticky-note text-yellow-500 text-2xl"></i>
                    </div>

                    {{-- Informasi --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">{{ $minute->title }}</h3>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $minute->status_badge }}">
                                {{ $minute->status_label }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-2 mt-3 text-sm">
                            <div class="text-gray-500 dark:text-gray-400">
                                <i class="far fa-calendar mr-1"></i> {{ $minute->formatted_meeting_date }}
                            </div>
                            <div class="text-gray-500 dark:text-gray-400">
                                <i class="far fa-clock mr-1"></i> {{ $minute->meeting_time ? $minute->meeting_time->format('H:i') : '-' }}
                            </div>
                            <div class="text-gray-500 dark:text-gray-400">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $minute->location ?? '-' }}
                            </div>
                            <div class="text-gray-500 dark:text-gray-400">
                                <i class="fas fa-user-edit mr-1"></i> {{ $minute->notulis->name ?? auth()->user()->name }}
                            </div>
                        </div>
                        @if($minute->summary)
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $minute->summary }}</p>
                        @endif
                        <div class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                            Dibuat {{ optional($minute->created_at)->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex flex-wrap items-center gap-2 xl:justify-end">
                        <a href="{{ route('owner.officials.secretary.minutes.show', $minute->id) }}"
                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-eye mr-2"></i> Lihat
                        </a>
                        @if($minute->status !== 'archived')
                            <a href="{{ route('owner.officials.secretary.minutes.edit', $minute->id) }}"
                               class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm transition">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('owner.officials.secretary.minutes.pdf', $minute->id) }}"
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-file-pdf mr-2"></i> PDF
                        </a>
                        @if($minute->status !== 'archived' && $minute->status !== 'draft')
                            <form action="{{ route('owner.officials.secretary.minutes.archive', $minute->id) }}"
                                  method="POST" onsubmit="return confirm('Arsipkan notulen ini?')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm transition">
                                    <i class="fas fa-archive mr-2"></i> Arsip
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-20 px-6 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-yellow-500/10 flex items-center justify-center">
                    <i class="fas fa-sticky-note text-3xl text-yellow-500"></i>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-gray-700 dark:text-gray-300">Belum ada notulen</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Belum ada catatan rapat yang tersimpan. Buat notulen pertama untuk mulai mendokumentasikan kegiatan organisasi.
                </p>
                <a href="{{ route('owner.officials.secretary.minutes.create') }}"
                   class="inline-flex items-center mt-5 px-5 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-semibold transition">
                    <i class="fas fa-plus mr-2"></i> Buat Notulen
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(isset($minutes) && method_exists($minutes, 'hasPages') && $minutes->hasPages())
        <div>{{ $minutes->withQueryString()->links() }}</div>
    @endif

</div>
@endsection