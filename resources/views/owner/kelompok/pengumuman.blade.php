@extends('layouts.roles.owner')

@section('title', 'Pengumuman Kelompok')

@push('styles')
<style>
[x-cloak] { display: none !important; }

.capacity-bar-fill { transition: width .6s cubic-bezier(.4,0,.2,1); }

.toast-stack {
    position: fixed;
    top: 1rem; right: 1rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    pointer-events: none;
}
.toast {
    pointer-events: auto;
    min-width: 260px;
    max-width: 400px;
    padding: .75rem 1rem;
    border-radius: .75rem;
    box-shadow: 0 10px 25px -10px rgba(0,0,0,.2);
    font-size: .8rem;
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    background: #fff;
    border-left: 4px solid #10b981;
    animation: toastIn .25s ease;
}
.toast.error { border-left-color: #ef4444; }
.toast.warn  { border-left-color: #f59e0b; }
.dark .toast { background: #1f2937; color: #f3f4f6; }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

.target-item {
    transition: background-color .15s ease, border-color .15s ease;
}
.target-item:hover { background-color: rgba(0,0,0,.015); }
.dark .target-item:hover { background-color: rgba(255,255,255,.02); }
.target-item.selected {
    background-color: rgba(250, 204, 21, .08);
    border-color: rgb(250, 204, 21) !important;
}
.dark .target-item.selected {
    background-color: rgba(250, 204, 21, .06);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@section('content')

@php
    $typeOptions = [
        ''                 => 'Semua Jenis',
        'rapat_bulanan'    => 'Rapat Bulanan',
        'rapat_tahunan'    => 'Rapat Tahunan',
        'rapat_kegiatan'   => 'Rapat Kegiatan',
        'perubahan_jadwal' => 'Perubahan Jadwal',
        'deadline'         => 'Deadline',
        'kegiatan'         => 'Kegiatan',
        'presensi'         => 'Presensi',
        'informasi'        => 'Informasi',
        'darurat'          => 'Darurat',
    ];

    $statusOptions = [
        ''          => 'Semua Status',
        'published' => 'Dipublikasikan',
        'scheduled' => 'Terjadwal',
        'draft'     => 'Draft',
        'expired'   => 'Kedaluwarsa',
        'archived'  => 'Arsip',
    ];
@endphp

<div class="p-4 md:p-6 max-w-[1400px] mx-auto"
     x-data="announcementPage()"
     x-cloak>

    {{-- HEADER --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}"
           class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700
                  hover:bg-gray-200 dark:hover:bg-gray-600
                  flex items-center justify-center text-gray-600 dark:text-gray-300
                  transition shrink-0"
           aria-label="Kembali">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <div class="min-w-0 flex-1">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white
                       flex items-center gap-2">
                <i class="fas fa-bullhorn text-yellow-500"></i>
                Pengumuman Kelompok
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Buat, jadwalkan, dan kirim pengumuman ke notifikasi anggota.
            </p>
        </div>

        <button type="button"
                @click="openCompose()"
                class="px-4 py-2.5 rounded-xl bg-yellow-500 hover:bg-yellow-600
                       text-white text-sm font-semibold shadow-sm
                       transition flex items-center gap-2 shrink-0">
            <i class="fas fa-plus"></i>
            <span class="hidden sm:inline">Buat Pengumuman</span>
        </button>
    </div>

    {{-- SESSION ALERTS --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500
                    text-green-700 dark:text-green-300 p-4 rounded-xl
                    flex items-start gap-3">
            <i class="fas fa-check-circle mt-0.5"></i>
            <div>
                <div>{{ session('success') }}</div>
                @if(session('announcement_summary'))
                    <div class="text-xs mt-1 opacity-80">
                        {{ session('announcement_summary')['recipient_count'] }} penerima •
                        {{ session('announcement_summary')['group_count'] }} kelompok
                    </div>
                @endif
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500
                    text-red-700 dark:text-red-300 p-4 rounded-xl
                    flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- STATS ROW --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30
                            flex items-center justify-center shrink-0">
                    <i class="fas fa-bullhorn text-blue-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                    <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                        {{ $stats['total'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                            flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Dipublikasikan</div>
                    <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                        {{ $stats['published'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30
                            flex items-center justify-center shrink-0">
                    <i class="fas fa-clock text-blue-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Terjadwal</div>
                    <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                        {{ $stats['scheduled'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700
                            flex items-center justify-center shrink-0">
                    <i class="fas fa-pen text-slate-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Draft</div>
                    <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                        {{ $stats['draft'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-rose-200 dark:border-rose-800 p-4
                    {{ $stats['urgent'] > 0 ? 'ring-1 ring-rose-300' : '' }}">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-900/30
                            flex items-center justify-center shrink-0">
                    <i class="fas fa-triangle-exclamation text-rose-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Darurat Aktif</div>
                    <div class="text-xl font-extrabold
                                {{ $stats['urgent'] > 0 ? 'text-rose-600' : 'text-gray-800 dark:text-white' }}">
                        {{ $stats['urgent'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('owner.kelompok.pengumuman') }}"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                 border border-gray-200 dark:border-gray-700 p-4 md:p-5 mb-5">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2
                          text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ $q }}"
                       placeholder="Cari judul atau isi pengumuman..."
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl
                              border-gray-300 dark:border-gray-600
                              bg-gray-50 dark:bg-gray-700
                              text-gray-900 dark:text-white text-sm
                              focus:ring-2 focus:ring-yellow-500
                              focus:border-yellow-500">
            </div>

            <select name="group_id"
                    class="rounded-xl border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700
                           text-gray-900 dark:text-white text-sm
                           px-4 py-2.5 focus:ring-2 focus:ring-yellow-500">
                <option value="">Semua Kelompok</option>
                @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ (int) $groupId === $g->id ? 'selected' : '' }}>
                        {{ $g->name }}
                    </option>
                @endforeach
            </select>

            <select name="type"
                    class="rounded-xl border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700
                           text-gray-900 dark:text-white text-sm
                           px-4 py-2.5 focus:ring-2 focus:ring-yellow-500">
                @foreach($typeOptions as $val => $label)
                    <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="status"
                    class="rounded-xl border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700
                           text-gray-900 dark:text-white text-sm
                           px-4 py-2.5 focus:ring-2 focus:ring-yellow-500">
                @foreach($statusOptions as $val => $label)
                    <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-yellow-500 hover:bg-yellow-600
                           text-white text-sm font-semibold
                           transition flex items-center gap-2 shrink-0 shadow-sm">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if($q || $status || $type || $groupId)
                <a href="{{ route('owner.kelompok.pengumuman') }}"
                   class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700
                          hover:bg-gray-200 dark:hover:bg-gray-600
                          text-gray-700 dark:text-gray-200 text-sm font-medium
                          transition flex items-center gap-2 shrink-0">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </div>
    </form>

    {{-- ANNOUNCEMENT LIST --}}
    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $ann)
                @php
                    $priority = $ann->priority_badge;
                    $status   = $ann->status_badge;
                    $typeMeta = $ann->type_meta;
                    $isExpired = $ann->isExpired();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700
                            p-5 transition hover:shadow-md
                            {{ $ann->is_pinned ? 'ring-2 ring-yellow-300 dark:ring-yellow-700' : '' }}">

                    {{-- Header --}}
                    <div class="flex items-start gap-4">

                        {{-- Icon --}}
                        <div class="w-11 h-11 rounded-xl shrink-0
                                    bg-gradient-to-br from-yellow-400 to-orange-500
                                    flex items-center justify-center text-white shadow-sm">
                            <i class="fas {{ $typeMeta['icon'] }}"></i>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">

                                @if($ann->is_pinned)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full
                                                 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                                 font-bold uppercase tracking-wide">
                                        <i class="fas fa-thumbtack"></i> Disematkan
                                    </span>
                                @endif

                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide
                                             {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>

                                @if($ann->priority !== 'normal')
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide
                                                 {{ $priority['class'] }}">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full {{ $priority['dot'] }} mr-1"></span>
                                        {{ $priority['label'] }}
                                    </span>
                                @endif

                                @if($ann->require_acknowledgement)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full
                                                 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300
                                                 font-bold">
                                        <i class="fas fa-signature"></i> Perlu Konfirmasi
                                    </span>
                                @endif

                                @if($isExpired)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full
                                                 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300
                                                 font-bold">
                                        Kedaluwarsa
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-bold text-gray-800 dark:text-white text-lg">
                                {{ $ann->title }}
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                {{ $ann->content }}
                            </p>

                            {{-- Target groups --}}
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach($ann->groups as $g)
                                    <span class="px-2 py-0.5 rounded-full text-[10px]
                                                 bg-blue-50 dark:bg-blue-900/20
                                                 text-blue-700 dark:text-blue-300 font-semibold">
                                        {{ $g->name }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- Meta --}}
                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1
                                        text-[11px] text-gray-500 dark:text-gray-400">
                                <span><i class="fas fa-user mr-1"></i> {{ $ann->creator->name ?? 'Sistem' }}</span>
                                <span><i class="fas fa-clock mr-1"></i> {{ $ann->created_at->diffForHumans() }}</span>
                                @if($ann->published_at)
                                    <span><i class="fas fa-calendar-check mr-1"></i>
                                        {{ $ann->published_at->translatedFormat('d M Y H:i') }}
                                    </span>
                                @endif
                                @if($ann->expires_at)
                                    <span class="{{ $isExpired ? 'text-amber-600' : '' }}">
                                        <i class="fas fa-hourglass-end mr-1"></i>
                                        {{ $ann->expires_at->translatedFormat('d M Y') }}
                                    </span>
                                @endif

                                {{-- Recipient stats --}}
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $ann->recipient_count }} penerima
                                </span>

                                @if($ann->read_count > 0)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ $ann->read_count }} dibaca
                                    </span>
                                @endif

                                @if($ann->ack_count > 0)
                                    <span class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400">
                                        <i class="fas fa-check-double mr-1"></i>
                                        {{ $ann->ack_count }} konfirmasi
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="shrink-0 flex items-center gap-1">
                            <button type="button"
                                    @click="openDetail(@js([
                                        'id' => $ann->id,
                                        'title' => $ann->title,
                                        'content' => $ann->content,
                                        'type_label' => $typeMeta['label'],
                                        'priority_label' => $priority['label'],
                                        'groups' => $ann->groups->pluck('name'),
                                        'recipient_count' => $ann->recipient_count,
                                        'read_count' => $ann->read_count,
                                        'ack_count' => $ann->ack_count,
                                    ]))"
                                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                                           text-gray-500 dark:text-gray-400 transition"
                                    title="Lihat detail">
                                <i class="fas fa-eye text-sm"></i>
                            </button>

                            <form action="{{ route('owner.kelompok.announcement.destroy', $ann->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus pengumuman ini beserta semua notifikasinya?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20
                                               text-red-500 transition"
                                        title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $announcements->links() }}
        </div>

    @else
        {{-- EMPTY STATE --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700
                    p-10 md:p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl
                        bg-yellow-50 dark:bg-yellow-900/20
                        flex items-center justify-center mb-5">
                <i class="fas fa-bullhorn text-4xl text-yellow-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                {{ ($q || $status || $type || $groupId) ? 'Tidak ada pengumuman yang cocok' : 'Belum ada pengumuman' }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
                {{ ($q || $status || $type || $groupId)
                    ? 'Coba ubah filter atau reset untuk melihat semua pengumuman.'
                    : 'Mulai dengan membuat pengumuman pertama untuk kelompok kamu.' }}
            </p>

            @if($q || $status || $type || $groupId)
                <a href="{{ route('owner.kelompok.pengumuman') }}"
                   class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                          text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
            @else
                <button type="button"
                        @click="openCompose()"
                        class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               bg-yellow-500 hover:bg-yellow-600 text-white text-sm
                               font-semibold shadow-sm transition">
                    <i class="fas fa-plus"></i> Buat Pengumuman Pertama
                </button>
            @endif
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- COMPOSE MODAL --}}
    {{-- ========================================================= --}}
    <div x-show="compose.open"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-start justify-center p-4
                bg-black/50 backdrop-blur-sm overflow-y-auto">

        <div x-show="compose.open"
             x-transition
             @click.away="closeCompose()"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                    max-w-3xl w-full my-8">

            {{-- Modal header --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700
                        flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-yellow-100 dark:bg-yellow-900/30
                            flex items-center justify-center shrink-0">
                    <i class="fas fa-bullhorn text-yellow-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 dark:text-white">Buat Pengumuman</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Isi detail, pilih target, lalu kirim.
                    </p>
                </div>
                <button type="button" @click="closeCompose()"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                               flex items-center justify-center text-gray-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Modal body --}}
            <form :action="'{{ route('owner.kelompok.announcement.store') }}'"
                  method="POST"
                  @submit="onSubmit($event)"
                  class="p-6 space-y-5">
                @csrf

                {{-- Template chips --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
                        Template Cepat
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(tpl, key) in templates" :key="key">
                            <button type="button"
                                    @click="applyTemplate(key)"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                           bg-gray-100 dark:bg-gray-700
                                           text-gray-700 dark:text-gray-200
                                           hover:bg-yellow-100 dark:hover:bg-yellow-900/30
                                           transition flex items-center gap-1.5">
                                <i class="fas" :class="tpl.icon"></i>
                                <span x-text="tpl.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Type + Priority --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jenis Pengumuman
                        </label>
                        <select name="type"
                                x-model="compose.type"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white text-sm px-4 py-2.5
                                       focus:ring-2 focus:ring-yellow-500">
                            @foreach($typeOptions as $val => $label)
                                @if($val)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Prioritas
                        </label>
                        <select name="priority"
                                x-model="compose.priority"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white text-sm px-4 py-2.5
                                       focus:ring-2 focus:ring-yellow-500">
                            <option value="normal">Normal</option>
                            <option value="important">Penting</option>
                            <option value="high">Tinggi</option>
                            <option value="urgent">Darurat</option>
                        </select>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required maxlength="255"
                           x-model="compose.title"
                           placeholder="Contoh: Rapat Bulanan UNICODE"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white text-sm px-4 py-2.5
                                  focus:ring-2 focus:ring-yellow-500">
                </div>

                {{-- Content --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Isi Pengumuman <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" required rows="6"
                              x-model="compose.content"
                              placeholder="Tulis isi pengumuman di sini..."
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                     bg-gray-50 dark:bg-gray-700
                                     text-gray-900 dark:text-white text-sm px-4 py-2.5
                                     focus:ring-2 focus:ring-yellow-500 resize-y"></textarea>
                    <p class="mt-1 text-xs text-gray-400">
                        <span x-text="compose.content.length"></span> karakter
                    </p>
                </div>

                {{-- Target groups --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Target Kelompok <span class="text-red-500">*</span>
                        </label>
                        <button type="button"
                                @click="toggleAll()"
                                class="text-xs font-semibold text-yellow-600 hover:text-yellow-700">
                            <span x-text="allSelected ? 'Hapus Semua' : 'Pilih Semua'"></span>
                        </button>
                    </div>

                    {{-- Search --}}
                    <div class="relative mb-2">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2
                                  text-gray-400 text-xs"></i>
                        <input type="text" x-model="compose.groupSearch"
                               placeholder="Cari kelompok..."
                               class="w-full pl-9 pr-4 py-2 rounded-lg text-xs
                                      border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white
                                      focus:ring-2 focus:ring-yellow-500">
                    </div>

                    {{-- Selected counter --}}
                    <div class="flex items-center justify-between mb-2 text-xs">
                        <span class="font-semibold text-yellow-700 dark:text-yellow-400">
                            <span x-text="selectedGroups.length"></span> kelompok dipilih
                        </span>
                        <span class="text-gray-400"
                              x-show="selectedGroups.length > 0">
                            <span x-text="estimatedRecipients"></span> anggota akan menerima
                        </span>
                    </div>

                    {{-- List --}}
                    <div class="max-h-64 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700
                                divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($groups as $g)
                            <label class="target-item flex items-center gap-3 px-4 py-3 cursor-pointer
                                          border-l-4 border-transparent"
                                   data-group-id="{{ $g->id }}"
                                   data-group-members="{{ $g->members_count ?? 0 }}"
                                   data-group-name="{{ strtolower($g->name) }}">
                                <input type="checkbox"
                                       name="group_ids[]"
                                       value="{{ $g->id }}"
                                       x-model="selectedGroups"
                                       @change="markGroupDirty()"
                                       class="rounded text-yellow-500 focus:ring-yellow-500">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white truncate">
                                        {{ $g->name }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $g->members_count ?? 0 }} anggota aktif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Options --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-start gap-2 p-3 rounded-xl
                                  border border-gray-200 dark:border-gray-700
                                  hover:bg-gray-50 dark:hover:bg-gray-700/50
                                  cursor-pointer transition">
                        <input type="checkbox" name="is_pinned" value="1"
                               x-model="compose.isPinned"
                               class="mt-0.5 rounded text-yellow-500 focus:ring-yellow-500">
                        <div>
                            <div class="text-sm font-semibold text-gray-800 dark:text-white">
                                <i class="fas fa-thumbtack mr-1 text-yellow-500"></i> Sematkan
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Tampil di atas daftar pengumuman
                            </div>
                        </div>
                    </label>

                    <label class="flex items-start gap-2 p-3 rounded-xl
                                  border border-gray-200 dark:border-gray-700
                                  hover:bg-gray-50 dark:hover:bg-gray-700/50
                                  cursor-pointer transition">
                        <input type="checkbox" name="require_acknowledgement" value="1"
                               x-model="compose.requireAck"
                               class="mt-0.5 rounded text-indigo-500 focus:ring-indigo-500">
                        <div>
                            <div class="text-sm font-semibold text-gray-800 dark:text-white">
                                <i class="fas fa-signature mr-1 text-indigo-500"></i> Perlu Konfirmasi
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Anggota harus klik "Saya sudah membaca"
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Schedule & expiry --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Mode Kirim
                        </label>
                        <select name="send_mode"
                                x-model="compose.sendMode"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white text-sm px-4 py-2.5
                                       focus:ring-2 focus:ring-yellow-500">
                            <option value="now">Kirim Sekarang</option>
                            <option value="schedule">Jadwalkan</option>
                            <option value="draft">Simpan sebagai Draft</option>
                        </select>
                    </div>

                    <div x-show="compose.sendMode === 'schedule'">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jadwal Pengiriman
                        </label>
                        <input type="datetime-local" name="scheduled_at"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white text-sm px-4 py-2.5
                                      focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Kadaluwarsa (opsional)
                    </label>
                    <input type="datetime-local" name="expires_at"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                  bg-gray-50 dark:bg-gray-700
                                  text-gray-900 dark:text-white text-sm px-4 py-2.5
                                  focus:ring-2 focus:ring-yellow-500">
                    <p class="mt-1 text-xs text-gray-400">
                        Setelah tanggal ini, pengumuman tidak lagi muncul sebagai aktif.
                    </p>
                </div>

                {{-- Submit --}}
                <div class="flex gap-2 pt-2 border-t border-gray-200 dark:border-gray-700 mt-6">
                    <button type="button"
                            @click="closeCompose()"
                            class="flex-1 px-5 py-2.5 rounded-xl
                                   bg-gray-100 dark:bg-gray-700
                                   hover:bg-gray-200 dark:hover:bg-gray-600
                                   text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="compose.sending || selectedGroups.length === 0"
                            :class="(compose.sending || selectedGroups.length === 0)
                                ? 'opacity-60 cursor-not-allowed'
                                : ''"
                            class="flex-1 px-5 py-2.5 rounded-xl
                                   bg-yellow-500 hover:bg-yellow-600
                                   text-white text-sm font-bold shadow-sm transition
                                   flex items-center justify-center gap-2">
                        <template x-if="!compose.sending">
                            <span>
                                <i class="fas fa-paper-plane mr-1"></i>
                                <span x-text="submitLabel"></span>
                            </span>
                        </template>
                        <template x-if="compose.sending">
                            <span>
                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                Mengirim...
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- DETAIL MODAL --}}
    {{-- ========================================================= --}}
    <div x-show="detail.open"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4
                bg-black/50 backdrop-blur-sm">

        <div x-show="detail.open"
             x-transition
             @click.away="detail.open = false"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                    max-w-2xl w-full max-h-[90vh] overflow-y-auto">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700
                        flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">Detail Pengumuman</h3>
                <button type="button" @click="detail.open = false"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                               flex items-center justify-center text-gray-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">
                        <span x-text="detail.data.type_label"></span>
                    </div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white mt-1"
                        x-text="detail.data.title"></h2>
                </div>

                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line"
                       x-text="detail.data.content"></p>
                </div>

                <div class="grid grid-cols-3 gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-gray-800 dark:text-white"
                             x-text="detail.data.recipient_count"></div>
                        <div class="text-xs text-gray-500">Penerima</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-emerald-600"
                             x-text="detail.data.read_count"></div>
                        <div class="text-xs text-gray-500">Dibaca</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-indigo-600"
                             x-text="detail.data.ack_count"></div>
                        <div class="text-xs text-gray-500">Konfirmasi</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold text-gray-500 mb-2">Target Kelompok</div>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="name in detail.data.groups" :key="name">
                            <span class="px-2 py-0.5 rounded-full text-[11px]
                                         bg-blue-50 dark:bg-blue-900/20
                                         text-blue-700 dark:text-blue-300 font-semibold"
                                  x-text="name"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="toastStack" class="toast-stack"></div>

@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function announcementPage() {
    return {
        compose: {
            open: false,
            type: 'informasi',
            priority: 'normal',
            title: '',
            content: '',
            groupSearch: '',
            isPinned: false,
            requireAck: false,
            sendMode: 'now',
            sending: false,
        },
        detail: {
            open: false,
            data: {},
        },
        selectedGroups: [],
        templates: @json($templates),

        init() {
            // Reactive watcher untuk group search
            this.$watch('compose.groupSearch', (q) => this.applyGroupSearch(q));
            // Reactive watcher untuk selectedGroups → update highlight
            this.$watch('selectedGroups', () => this.updateGroupHighlight());
        },

        get allSelected() {
            const visible = document.querySelectorAll('.target-item:not([style*="display: none"])');
            return visible.length > 0
                && this.selectedGroups.length === document.querySelectorAll('.target-item').length;
        },

        get estimatedRecipients() {
            const ids = this.selectedGroups.map(String);
            let total = 0;
            document.querySelectorAll('.target-item').forEach(el => {
                if (ids.includes(el.dataset.groupId)) {
                    total += parseInt(el.dataset.groupMembers || '0', 10);
                }
            });
            return total;
        },

        get submitLabel() {
            return {
                'now':      'Kirim Sekarang',
                'schedule': 'Jadwalkan',
                'draft':    'Simpan Draft',
            }[this.compose.sendMode] || 'Kirim';
        },

        openCompose() {
            this.compose.open = true;
            document.body.style.overflow = 'hidden';
        },

        closeCompose() {
            if (this.compose.sending) return;
            this.compose.open = false;
            document.body.style.overflow = '';
        },

        openDetail(data) {
            this.detail.data = data;
            this.detail.open = true;
            document.body.style.overflow = 'hidden';
        },

        applyTemplate(key) {
            const tpl = this.templates[key];
            if (!tpl) return;
            this.compose.type = key;
            this.compose.title = tpl.title;
            this.compose.content = tpl.content;
            this.compose.priority = tpl.priority || 'normal';
            this.showToast(`Template "${tpl.label}" diterapkan.`);
        },

        toggleAll() {
            const items = document.querySelectorAll('.target-item');
            const target = ! this.allSelected;
            if (target) {
                this.selectedGroups = Array.from(items).map(el => el.dataset.groupId);
            } else {
                this.selectedGroups = [];
            }
            this.updateGroupHighlight();
        },

        applyGroupSearch(q) {
            const query = (q || '').toLowerCase().trim();
            document.querySelectorAll('.target-item').forEach(el => {
                const name = el.dataset.groupName || '';
                el.style.display = !query || name.includes(query) ? '' : 'none';
            });
        },

        updateGroupHighlight() {
            const ids = this.selectedGroups.map(String);
            document.querySelectorAll('.target-item').forEach(el => {
                el.classList.toggle('selected', ids.includes(el.dataset.groupId));
            });
        },

        markGroupDirty() {
            this.updateGroupHighlight();
        },

        onSubmit(e) {
            if (this.selectedGroups.length === 0) {
                e.preventDefault();
                this.showToast('Pilih minimal 1 kelompok target.', 'error');
                return;
            }
            if (! this.compose.title.trim() || ! this.compose.content.trim()) {
                e.preventDefault();
                this.showToast('Judul dan isi pengumuman wajib diisi.', 'error');
                return;
            }
            this.compose.sending = true;
        },

        showToast(msg, type = 'success') {
            const stack = document.getElementById('toastStack');
            const el = document.createElement('div');
            el.className = 'toast' + (type === 'error' ? ' error' : type === 'warn' ? ' warn' : '');
            el.innerHTML = `
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle text-red-500'
                            : type === 'warn' ? 'fa-exclamation-triangle text-amber-500'
                            : 'fa-check-circle text-emerald-500'} mt-0.5"></i>
                <div class="flex-1">${msg}</div>
            `;
            stack.appendChild(el);
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transform = 'translateX(20px)';
                el.style.transition = 'all .25s ease';
                setTimeout(() => el.remove(), 300);
            }, 3500);
        },
    };
}
</script>
@endpush