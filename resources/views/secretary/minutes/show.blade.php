@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', 'Detail Notulen – Sekretaris UNICODE')
@section('page-title', 'Detail Notulen')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto space-y-6">

    {{-- Tombol Kembali --}}
    <div>
        <a href="{{ route('owner.officials.secretary.minutes.index') }}"
           class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar notulen
        </a>
    </div>

    {{-- Card Detail --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $minute->title }}</h1>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $minute->status_badge }}">
                        {{ $minute->status_label }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                    <span><i class="far fa-calendar mr-1"></i> {{ $minute->formatted_meeting_date }}</span>
                    @if($minute->meeting_time)
                        <span><i class="far fa-clock mr-1"></i> {{ $minute->meeting_time->format('H:i') }}</span>
                    @endif
                    @if($minute->location)
                        <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $minute->location }}</span>
                    @endif
                    @if($minute->leader_name)
                        <span><i class="fas fa-user-tie mr-1"></i> Pimpinan: {{ $minute->leader_name }}</span>
                    @endif
                    <span><i class="fas fa-user-edit mr-1"></i> Notulis: {{ $minute->notulis->name ?? '-' }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($minute->status !== 'archived')
                    <a href="{{ route('owner.officials.secretary.minutes.edit', $minute->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                @endif
                <a href="{{ route('owner.officials.secretary.minutes.pdf', $minute->id) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </a>
                @if($minute->status !== 'archived' && $minute->status !== 'draft')
                    <form action="{{ route('owner.officials.secretary.minutes.archive', $minute->id) }}"
                          method="POST" onsubmit="return confirm('Arsipkan notulen ini?')">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                            <i class="fas fa-archive mr-2"></i> Arsipkan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6">

            {{-- Peserta --}}
            @if($minute->participants)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Peserta</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <pre class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $minute->participants }}</pre>
                    </div>
                </div>
            @endif

            {{-- Agenda --}}
            @if($minute->agenda)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Agenda Rapat</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <pre class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $minute->agenda }}</pre>
                    </div>
                </div>
            @endif

            {{-- Pembahasan --}}
            @if($minute->discussion)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Pembahasan</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <pre class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $minute->discussion }}</pre>
                    </div>
                </div>
            @endif

            {{-- Keputusan --}}
            @if($minute->decisions)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Keputusan Rapat</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <pre class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $minute->decisions }}</pre>
                    </div>
                </div>
            @endif

            {{-- Tindak Lanjut --}}
            @if($minute->follow_up)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Tindak Lanjut</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <pre class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $minute->follow_up }}</pre>
                    </div>
                </div>
            @endif

            {{-- Lampiran --}}
            @if($minute->attachments->count())
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Lampiran</h3>
                    <div class="space-y-2">
                        @foreach($minute->attachments as $attachment)
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-900 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-file-alt text-gray-500 text-lg"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment->file_name }}</span>
                                    <span class="text-xs text-gray-500">({{ $attachment->formatted_size }})</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('owner.officials.secretary.minutes.download-attachment', $attachment->id) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <form action="{{ route('owner.officials.secretary.minutes.delete-attachment', $attachment->id) }}"
                                          method="POST" onsubmit="return confirm('Hapus lampiran ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Informasi Tambahan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Dibuat oleh:</span>
                    {{ $minute->creator->name ?? '-' }}
                </div>
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Dibuat pada:</span>
                    {{ $minute->created_at->format('d F Y H:i') }}
                </div>
                @if($minute->completed_at)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Selesai pada:</span>
                        {{ $minute->completed_at->format('d F Y H:i') }}
                    </div>
                @endif
            </div>

        </div>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-between flex-wrap gap-2">
            @if($minute->status === 'archived')
                <form action="{{ route('owner.officials.secretary.minutes.unarchive', $minute->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <i class="fas fa-undo mr-2"></i> Kembalikan dari Arsip
                    </button>
                </form>
            @endif
            <form action="{{ route('owner.officials.secretary.minutes.destroy', $minute->id) }}"
                  method="POST" onsubmit="return confirm('Hapus notulen ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-trash mr-2"></i> Hapus Permanen
                </button>
            </form>
        </div>

    </div>
</div>
@endsection