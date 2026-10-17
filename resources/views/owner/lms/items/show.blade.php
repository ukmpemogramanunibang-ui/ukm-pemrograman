@extends('layouts.roles.owner')

@section('title', $item->title)

@section('content')
@php
    $authId   = auth()->id();
    $isOwner  = $authId && (int) $authId === (int) ($item->created_by ?? 0);

    $group   = $item->group ?? null;
    $creator = $item->creator ?? null;

    $recipientsCount = (int) ($item->recipients_count ?? ($item->recipients?->count() ?? 0));
    $deadline        = $item->deadline_at ?? null;
    $deadlineMode    = $item->deadline_mode ?? ($deadline ? 'limited' : 'unlimited');
    $isTask          = ($item->type ?? '') === 'task';
    $isOverdue       = $deadline ? (method_exists($item, 'isOverdue') ? $item->isOverdue() : $deadline->isPast()) : false;
    $publishedAt     = $item->published_at ?? null;
    $status          = $item->status ?? null;

    if (!$status) {
        $status = $isOverdue ? 'closed' : ($publishedAt ? 'published' : 'draft');
    }

    $statusMeta = match ($status) {
        'published', 'active' => [
            'label' => 'Dipublikasikan', 'icon' => 'fa-circle-check',
            'classes' => 'bg-emerald-500/10 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700/40',
        ],
        'scheduled' => [
            'label' => 'Terjadwal', 'icon' => 'fa-clock',
            'classes' => 'bg-blue-500/10 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-700/40',
        ],
        'closed', 'expired' => [
            'label' => 'Selesai', 'icon' => 'fa-lock',
            'classes' => 'bg-slate-500/10 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        ],
        'archived' => [
            'label' => 'Diarsipkan', 'icon' => 'fa-box-archive',
            'classes' => 'bg-amber-500/10 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-700/40',
        ],
        default => [
            'label' => 'Draft', 'icon' => 'fa-file-pen',
            'classes' => 'bg-violet-500/10 text-violet-700 border-violet-200 dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-700/40',
        ],
    };

    $cover = $item->cover_url;

    $videoUrl    = $item->video_url ?? null;
    $videoProvider = $item->video_provider ?? null;

    // ---- Normalisasi attachments jadi array terstruktur ----
    $rawAttachments = is_iterable($item->attachments ?? null) ? $item->attachments : [];
    $attachments = collect($rawAttachments)->map(function ($a) {
        if (is_string($a)) {
            $path = $a;
            $name = basename($a);
            $size = null;
        } else {
            $path = data_get($a, 'path') ?? data_get($a, 'file') ?? null;
            $name = data_get($a, 'name') ?? data_get($a, 'original_name') ?? ($path ? basename($path) : 'Lampiran');
            $size = data_get($a, 'size');
        }
        $ext = strtolower(pathinfo((string) $name, PATHINFO_EXTENSION));
        return [
            'path' => $path,
            'name' => $name,
            'size' => $size,
            'ext'  => $ext,
            'url'  => $path ? asset('storage/' . ltrim((string) $path, '/')) : null,
        ];
    })->filter(fn ($a) => $a['url'])->values();

    $isoDeadline = $deadline ? $deadline->copy()->timezone('Asia/Jakarta')->toIso8601String() : null;
@endphp

@push('styles')
<style>
    .lms-show-shell { width: 100%; max-width: 1080px; margin-inline: auto; }

    .lms-show-cover { aspect-ratio: 16 / 7; }
    @media (max-width: 640px) { .lms-show-cover { aspect-ratio: 16 / 9; } }

    .lms-copy-btn.copied { pointer-events: none; }

    /* ---------- FILE CARD ---------- */
    .lms-file-card {
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .lms-file-card:hover { transform: translateY(-2px); }

    /* ---------- PREVIEW MODAL ---------- */
    .lms-preview {
        position: fixed;
        inset: 0;
        z-index: 3000;
        background: rgba(8, 12, 26, 0.96);
        backdrop-filter: blur(4px);
        display: none;
        flex-direction: column;
        opacity: 0;
        transition: opacity .2s ease;
    }
    .lms-preview.is-open { display: flex; opacity: 1; }
    .lms-preview.is-fullscreen { background: #000; }

    .lms-preview-header {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        background: rgba(15, 23, 42, 0.85);
        border-bottom: 1px solid rgba(255,255,255,.08);
        color: #fff;
    }

    .lms-preview-body {
        flex: 1;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
    }

    .lms-preview-content {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lms-preview-content iframe,
    .lms-preview-content img,
    .lms-preview-content video {
        max-width: 100%;
        max-height: 100%;
        border-radius: 12px;
        background: #000;
    }
    .lms-preview-content iframe {
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
    }

    .lms-preview-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
        z-index: 10;
    }
    .lms-preview-nav:hover { background: rgba(255,255,255,0.22); }
    .lms-preview-nav:active { transform: translateY(-50%) scale(.94); }
    .lms-preview-nav.prev { left: 12px; }
    .lms-preview-nav.next { right: 12px; }

    .lms-preview-thumbs {
        flex-shrink: 0;
        display: flex;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(15, 23, 42, 0.85);
        border-top: 1px solid rgba(255,255,255,.08);
        overflow-x: auto;
        scrollbar-width: thin;
    }
    .lms-preview-thumbs::-webkit-scrollbar { height: 4px; }
    .lms-preview-thumbs::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 99px; }

    .lms-thumb {
        flex-shrink: 0;
        width: 80px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid transparent;
        background: rgba(255,255,255,.06);
        color: #cbd5e1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
        padding: 4px;
    }
    .lms-thumb:hover { background: rgba(255,255,255,.12); }
    .lms-thumb.active { border-color: #8b5cf6; background: rgba(139,92,246,.2); color: #fff; }
    .lms-thumb i { font-size: 18px; margin-bottom: 2px; }
    .lms-thumb span { display: block; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    @media print {
        .lms-no-print { display: none !important; }
        body { background: #fff !important; }
        .lms-print-card { box-shadow: none !important; border-color: #d1d5db !important; }
    }
</style>
@endpush

<div class="lms-show-shell p-3 sm:p-5 lg:p-6">

    {{-- BREADCRUMB --}}
    <div class="lms-no-print flex items-center gap-2 text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 flex-wrap">
        <a href="{{ route('owner.lms.dashboard') }}" class="hover:text-violet-600 transition-colors">
            <i class="fas fa-home mr-1"></i>LMS
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
        <a href="{{ route('owner.lms.groups.show', $item->group_id) }}" class="hover:text-violet-600 transition-colors">
            {{ $group->name ?? 'Pengelompokan' }}
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
        <span class="font-semibold text-slate-700 dark:text-slate-200 truncate max-w-[16rem]">Detail</span>
    </div>

    {{-- VIEWER BANNER (muncul kalau BUKAN owner) --}}
    @unless($isOwner)
        <div class="lms-no-print mt-3 flex items-start gap-3 p-3.5 rounded-2xl
                    bg-slate-100 dark:bg-slate-800/70
                    border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs">
            <i class="fas fa-eye mt-0.5 text-slate-500"></i>
            <div>
                <p class="font-black">Mode Lihat</p>
                <p class="mt-0.5 text-slate-500 dark:text-slate-400">
                    Anda melihat konten ini sebagai penerima. Aksi edit / hapus hanya tersedia untuk pembuat.
                </p>
            </div>
        </div>
    @endunless

    {{-- HEADER --}}
    <div class="mt-3 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-extrabold tracking-wide {{ $isTask ? 'bg-violet-500/10 text-violet-700 border-violet-200 dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-700/40' : 'bg-emerald-500/10 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700/40' }}">
                    <i class="fas {{ $isTask ? 'fa-clipboard-list' : 'fa-book-open' }}"></i>
                    {{ $isTask ? 'TUGAS' : 'MATERI' }}
                </span>

                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-extrabold {{ $statusMeta['classes'] }}">
                    <i class="fas {{ $statusMeta['icon'] }}"></i>
                    {{ $statusMeta['label'] }}
                </span>

                @if($isOwner)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-extrabold bg-emerald-500/10 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700/40">
                        <i class="fas fa-user-shield"></i> KONTEN SAYA
                    </span>
                @endif
            </div>

            <h1 class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900 dark:text-white leading-tight break-words">
                {{ $item->title }}
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Detail konten LMS untuk pengelompokan
                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $group->name ?? '-' }}</span>.
            </p>
        </div>

        <div class="lms-no-print flex items-center gap-2 flex-wrap">
            <a href="{{ route('owner.lms.groups.show', $item->group_id) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-extrabold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <button type="button" id="copyLinkBtn"
                    class="lms-copy-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-extrabold hover:border-violet-400 hover:text-violet-600 transition-colors">
                <i id="copyLinkIcon" class="fas fa-link"></i>
                <span id="copyLinkText">Salin Tautan</span>
            </button>

            <button type="button" onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-extrabold hover:bg-slate-700 transition-colors dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                <i class="fas fa-print"></i> Cetak
            </button>

            {{-- ════════ OWNER-ONLY ACTIONS ════════ --}}
            @if($isOwner)
                @if($isTask)
                    <a href="{{ route('owner.lms.monitoring') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 text-xs font-extrabold hover:bg-cyan-500/20 transition-colors">
                        <i class="fas fa-chart-line"></i> Monitoring
                    </a>
                @endif

                <a href="{{ route('owner.lms.items.edit', $item->id) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-violet-600 text-white text-xs font-extrabold hover:bg-violet-700 transition-colors shadow-sm">
                    <i class="fas fa-pen"></i> Edit
                </a>

                <form id="deleteItemForm" method="POST"
                      action="{{ route('owner.lms.items.destroy', $item->id) }}"
                      class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="btnDeleteItem"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                   bg-rose-600 text-white text-xs font-extrabold
                                   hover:bg-rose-700 transition-colors shadow-sm">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="lms-no-print mt-4 flex items-start gap-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 text-emerald-700 dark:text-emerald-300 text-sm">
            <i class="fas fa-circle-check mt-0.5"></i>
            <div class="min-w-0">
                <p class="font-black">Berhasil</p>
                <p class="mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <article class="lms-print-card mt-5 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">

        {{-- COVER / HERO --}}
        @if($cover)
    <div class="lms-show-cover relative overflow-hidden bg-slate-100 dark:bg-slate-800">
        <img src="{{ $cover }}" 
             alt="Cover {{ $item->title }}" 
             class="w-full h-full object-cover"
             onerror="this.onerror=null;this.parentElement.innerHTML='';this.parentElement.classList.add('bg-gradient-to-br','from-violet-600','via-purple-600','to-fuchsia-600');">
                <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/70 to-transparent pointer-events-none"></div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <div class="text-[10px] font-extrabold tracking-[0.18em] uppercase opacity-80">UNICODE LMS</div>
                    <div class="mt-1 text-lg sm:text-xl font-black truncate">{{ $item->title }}</div>
                </div>
            </div>
        @else
            <div class="lms-show-cover relative flex items-center overflow-hidden px-5 sm:px-8 text-white bg-gradient-to-br {{ $isTask ? 'from-violet-600 via-purple-600 to-fuchsia-600' : 'from-emerald-600 via-teal-600 to-cyan-600' }}">
                <div class="absolute -right-10 -top-16 w-52 h-52 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -left-12 -bottom-20 w-60 h-60 rounded-full bg-black/10 blur-2xl"></div>
                <div class="relative max-w-3xl">
                    <div class="text-[10px] font-extrabold tracking-[0.2em] uppercase text-white/70">UNICODE LMS</div>
                    <div class="mt-2 text-2xl sm:text-3xl font-black leading-tight">{{ $item->title }}</div>
                    <div class="mt-2 text-xs sm:text-sm text-white/85">
                        {{ $isTask ? 'Tugas dan pengumpulan' : 'Materi pembelajaran dan referensi' }}
                    </div>
                </div>
            </div>
        @endif

        {{-- SUMMARY GRID --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 border-b border-slate-200 dark:border-slate-700">
            <div class="lms-meta-card p-4 sm:p-5 border-r border-b lg:border-b-0 border-slate-200 dark:border-slate-700">
                <p class="text-[10px] uppercase tracking-wide font-extrabold text-slate-400">Pengelompokan</p>
                <p class="mt-1 text-sm font-black text-slate-900 dark:text-white truncate">{{ $group->name ?? '-' }}</p>
            </div>
            <div class="lms-meta-card p-4 sm:p-5 lg:border-r border-b lg:border-b-0 border-slate-200 dark:border-slate-700">
                <p class="text-[10px] uppercase tracking-wide font-extrabold text-slate-400">Pembuat</p>
                <p class="mt-1 text-sm font-black text-slate-900 dark:text-white truncate">{{ $creator->name ?? '-' }}</p>
            </div>

            {{-- Penerima: hanya owner --}}
            <div class="lms-meta-card p-4 sm:p-5 border-r border-slate-200 dark:border-slate-700">
                <p class="text-[10px] uppercase tracking-wide font-extrabold text-slate-400">
                    {{ $isOwner ? 'Penerima' : 'Distribusi' }}
                </p>
                <p class="mt-1 text-sm font-black text-slate-900 dark:text-white">
                    @if($isOwner)
                        {{ number_format($recipientsCount) }} pengguna
                    @else
                        <span class="text-slate-400 dark:text-slate-500">—</span>
                    @endif
                </p>
            </div>

            <div class="lms-meta-card p-4 sm:p-5">
                <p class="text-[10px] uppercase tracking-wide font-extrabold text-slate-400">Dipublikasikan</p>
                <p class="mt-1 text-sm font-black text-slate-900 dark:text-white">
                    {{ $publishedAt ? $publishedAt->copy()->timezone('Asia/Jakarta')->isoFormat('D MMM Y, HH:mm') . ' WIB' : 'Belum' }}
                </p>
            </div>
        </div>

        <div class="p-5 sm:p-7 lg:p-8 space-y-7">

            {{-- TIME --}}
            <section>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Waktu</p>
                        <h2 class="mt-1 text-sm sm:text-base font-black text-slate-900 dark:text-white">
                            {{ $isTask ? 'Deadline Pengumpulan' : 'Masa Akses Materi' }}
                        </h2>
                    </div>
                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full border {{ $deadline ? ($isOverdue ? 'bg-rose-500/10 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-300 dark:border-rose-700/40' : 'bg-amber-500/10 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-700/40') : 'bg-slate-500/10 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
                        <i class="fas {{ $deadline ? 'fa-calendar-check' : 'fa-infinity' }} mr-1"></i>
                        {{ $deadline ? 'Terbatas' : 'Tanpa batas' }}
                    </span>
                </div>

                @if($deadline)
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3 items-stretch">
                        <div class="flex items-center gap-3 p-4 rounded-2xl {{ $isOverdue ? 'bg-rose-50 border border-rose-200 dark:bg-rose-900/20 dark:border-rose-700/40' : 'bg-amber-50 border border-amber-200 dark:bg-amber-900/20 dark:border-amber-700/40' }}">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $isOverdue ? 'bg-rose-500' : 'bg-amber-500' }} text-white shadow-sm">
                                <i class="fas {{ $isOverdue ? 'fa-lock' : 'fa-clock' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] uppercase font-extrabold {{ $isOverdue ? 'text-rose-600 dark:text-rose-300' : 'text-amber-600 dark:text-amber-300' }}">
                                    {{ $isOverdue ? 'Sudah melewati batas' : 'Batas waktu' }}
                                </p>
                                <p class="mt-1 text-sm sm:text-base font-black text-slate-900 dark:text-white break-words">
                                    {{ $deadline->copy()->timezone('Asia/Jakarta')->isoFormat('dddd, D MMMM Y · HH:mm') }} WIB
                                </p>
                            </div>
                        </div>

                        @if(!$isOverdue)
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 min-w-[180px]">
                                <p class="text-[10px] uppercase font-extrabold text-slate-400">Sisa waktu</p>
                                <p id="deadlineCountdown" class="mt-1 text-lg font-black text-slate-900 dark:text-white">Menghitung...</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-200 flex items-center justify-center shrink-0">
                            <i class="fas fa-infinity"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900 dark:text-white">Tidak ada batas waktu</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                {{ $isTask ? 'Pengumpulan tidak ditutup oleh waktu.' : 'Materi tetap tersedia sampai dinonaktifkan atau dihapus.' }}
                            </p>
                        </div>
                    </div>
                @endif
            </section>

            {{-- DESCRIPTION --}}
            @if($item->description)
                <section class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Konten</p>
                    <h2 class="mt-1 text-sm sm:text-base font-black text-slate-900 dark:text-white">Deskripsi</h2>
                    <div class="mt-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                        <p class="text-sm leading-7 text-slate-700 dark:text-slate-300 whitespace-pre-line break-words">{{ $item->description }}</p>
                    </div>
                </section>
            @endif

            {{-- ATTACHMENTS --}}
            @if($attachments->isNotEmpty())
                <section class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Dokumen</p>
                            <h2 class="mt-1 text-sm sm:text-base font-black text-slate-900 dark:text-white">Lampiran</h2>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">
                            {{ $attachments->count() }} file · <i class="fas fa-hand-pointer"></i> klik untuk pratinjau
                        </span>
                    </div>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($attachments as $idx => $att)
                            @php
                                $ext      = $att['ext'];
                                $isImage  = in_array($ext, ['jpg','jpeg','png','webp','gif','svg'], true);
                                $isPdf    = $ext === 'pdf';
                                $isVideo  = in_array($ext, ['mp4','webm','ogg','mov'], true);
                                $isOffice = in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx','csv','txt'], true);

                                $icon = match (true) {
                                    $isPdf            => 'fa-file-pdf',
                                    in_array($ext, ['doc','docx']) => 'fa-file-word',
                                    in_array($ext, ['xls','xlsx','csv']) => 'fa-file-excel',
                                    in_array($ext, ['ppt','pptx']) => 'fa-file-powerpoint',
                                    $ext === 'txt'    => 'fa-file-lines',
                                    $isImage          => 'fa-file-image',
                                    $isVideo          => 'fa-file-video',
                                    default           => 'fa-file',
                                };

                                $color = match (true) {
                                    $isPdf            => 'bg-rose-500/10 text-rose-600 dark:text-rose-300',
                                    in_array($ext, ['doc','docx']) => 'bg-blue-500/10 text-blue-600 dark:text-blue-300',
                                    in_array($ext, ['xls','xlsx','csv']) => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
                                    in_array($ext, ['ppt','pptx']) => 'bg-orange-500/10 text-orange-600 dark:text-orange-300',
                                    $ext === 'txt'    => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
                                    $isImage          => 'bg-fuchsia-500/10 text-fuchsia-600 dark:text-fuchsia-300',
                                    $isVideo          => 'bg-purple-500/10 text-purple-600 dark:text-purple-300',
                                    default           => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-300',
                                };
                            @endphp

                            <div class="lms-file-card group flex items-center gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/70 p-3 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md"
                                 data-att-index="{{ $idx }}"
                                 data-att-name="{{ $att['name'] }}"
                                 data-att-url="{{ $att['url'] }}"
                                 data-att-ext="{{ $ext }}"
                                 data-att-size="{{ $att['size'] ?? '' }}"
                                 role="button"
                                 tabindex="0">

                                @if($isImage)
                                    <img src="{{ $att['url'] }}" alt="{{ $att['name'] }}"
                                         class="w-14 h-14 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                @else
                                    <div class="w-14 h-14 rounded-xl {{ $color }} flex items-center justify-center text-xl shrink-0">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ $att['name'] ?: 'Lampiran' }}</p>
                                    <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-bold">
                                            {{ $ext ? strtoupper($ext) : 'FILE' }}
                                        </span>
                                        @if($att['size'])
                                            <span>{{ is_numeric($att['size']) ? number_format($att['size'] / 1024, 1).' KB' : $att['size'] }}</span>
                                        @endif
                                        <span class="text-indigo-500 dark:text-indigo-400 font-bold">
                                            <i class="fas fa-eye"></i> Pratinjau
                                        </span>
                                    </p>
                                </div>

                                <i class="fas fa-expand text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- VIDEO --}}
            @if($videoUrl)
                <section class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Referensi</p>
                    <h2 class="mt-1 text-sm sm:text-base font-black text-slate-900 dark:text-white">Video Pembelajaran</h2>
                    <div class="mt-3 rounded-2xl border border-pink-200 dark:border-pink-800/50 bg-pink-50/70 dark:bg-pink-900/10 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-xl bg-pink-500 text-white flex items-center justify-center shrink-0">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $videoProvider ? ucfirst($videoProvider) : 'Video eksternal' }}</p>
                                <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $videoUrl }}</p>
                            </div>
                        </div>
                        <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer"
                           class="lms-no-print inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-pink-500 text-white text-xs font-extrabold hover:bg-pink-600 transition-colors shrink-0">
                            <i class="fas fa-external-link-alt"></i> Buka Video
                        </a>
                    </div>
                </section>
            @endif

            {{-- ════════ RECIPIENTS — OWNER ONLY ════════ --}}
            @if($isOwner)
                <section class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Distribusi</p>
                            <h2 class="mt-1 text-sm sm:text-base font-black text-slate-900 dark:text-white">Penerima</h2>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">{{ number_format($recipientsCount) }} pengguna</span>
                    </div>

                    @if($item->recipients && $item->recipients->count() > 0)
                        <div class="mt-3 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($item->recipients->take(100) as $recipient)
                                    @php
                                        $recipientUser = $recipient->user ?? null;
                                        $name = $recipientUser->name ?? '-';
                                        $nim = $recipientUser->nim ?? null;
                                        $initials = collect(preg_split('/\s+/', trim($name)))->filter()->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
                                    @endphp
                                    <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center text-[10px] font-black shrink-0">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ $name }}</p>
                                            <p class="mt-0.5 text-[10px] text-slate-500 dark:text-slate-400">
                                                {{ $nim ? 'NIM ' . $nim : 'Pengguna terdaftar' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($item->recipients->count() > 100)
                                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/70 border-t border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                    Menampilkan 100 penerima pertama dari {{ number_format($item->recipients->count()) }}.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 p-5 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-center">
                            <i class="fas fa-user-group text-2xl text-slate-300 dark:text-slate-600"></i>
                            <p class="mt-2 text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada penerima yang dapat ditampilkan.</p>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </article>

    <div class="lms-no-print mt-4 flex items-center justify-between gap-3 text-[10px] text-slate-400">
        <span>ID Item: {{ $item->id }}</span>
        <span>UNICODE LMS · Asia/Jakarta</span>
    </div>
</div>

{{-- ═════════════════════════════════════════════════════════════
     PREVIEW MODAL (FULLSCREEN)
═════════════════════════════════════════════════════════════ --}}
<div id="lmsPreview" class="lms-preview lms-no-print" role="dialog" aria-modal="true" aria-hidden="true">
    {{-- HEADER --}}
    <div class="lms-preview-header">
        <div class="min-w-0 flex-1">
            <p id="lmsPreviewTitle" class="text-xs sm:text-sm font-black truncate">—</p>
            <p id="lmsPreviewMeta" class="text-[10px] text-slate-400 mt-0.5 truncate">—</p>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <a id="lmsPreviewOpen" href="#" target="_blank" rel="noopener noreferrer"
               class="w-9 h-9 rounded-lg hover:bg-white/10 flex items-center justify-center text-white transition-colors"
               title="Buka di tab baru">
                <i class="fas fa-external-link-alt text-sm"></i>
            </a>
            <a id="lmsPreviewDownload" href="#" download
               class="w-9 h-9 rounded-lg hover:bg-white/10 flex items-center justify-center text-white transition-colors"
               title="Unduh">
                <i class="fas fa-download text-sm"></i>
            </a>
            <button type="button" id="lmsPreviewFullscreen"
                    class="w-9 h-9 rounded-lg hover:bg-white/10 flex items-center justify-center text-white transition-colors"
                    title="Fullscreen">
                <i class="fas fa-expand text-sm"></i>
            </button>
            <button type="button" id="lmsPreviewClose"
                    class="w-9 h-9 rounded-lg hover:bg-rose-500/20 flex items-center justify-center text-white transition-colors"
                    title="Tutup (Esc)">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    {{-- BODY --}}
    <div class="lms-preview-body">
        <button type="button" id="lmsPreviewPrev" class="lms-preview-nav prev" title="Sebelumnya">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="lms-preview-content" id="lmsPreviewContent">
            {{-- content injected via JS --}}
        </div>

        <button type="button" id="lmsPreviewNext" class="lms-preview-nav next" title="Berikutnya">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    {{-- THUMBNAILS --}}
    <div id="lmsPreviewThumbs" class="lms-preview-thumbs"></div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.7.2/mammoth.browser.min.js"></script>
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       COPY LINK
    ═══════════════════════════════════════════════════════════ */
    const copyBtn  = document.getElementById('copyLinkBtn');
    const copyText = document.getElementById('copyLinkText');
    const copyIcon = document.getElementById('copyLinkIcon');

    if (copyBtn) {
        copyBtn.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(window.location.href);
                copyBtn.classList.add('copied');
                copyText.textContent = 'Tersalin';
                copyIcon.className = 'fas fa-check';
                setTimeout(() => {
                    copyBtn.classList.remove('copied');
                    copyText.textContent = 'Salin Tautan';
                    copyIcon.className = 'fas fa-link';
                }, 1800);
            } catch (e) {
                window.prompt('Salin tautan halaman ini:', window.location.href);
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════
       DEADLINE COUNTDOWN
    ═══════════════════════════════════════════════════════════ */
    const countdown   = document.getElementById('deadlineCountdown');
    const deadlineIso = @json($isoDeadline);

    function updateCountdown() {
        if (!countdown || !deadlineIso) return;
        const target = new Date(deadlineIso).getTime();
        const diff = target - Date.now();

        if (diff <= 0) {
            countdown.textContent = 'Waktu berakhir';
            countdown.classList.remove('text-slate-900', 'dark:text-white');
            countdown.classList.add('text-rose-600', 'dark:text-rose-300');
            return;
        }

        const t = Math.floor(diff / 1000);
        const d = Math.floor(t / 86400);
        const h = Math.floor((t % 86400) / 3600);
        const m = Math.floor((t % 3600) / 60);
        const s = t % 60;

        const parts = [];
        if (d > 0) parts.push(d + ' hari');
        parts.push(String(h).padStart(2, '0') + ' jam');
        parts.push(String(m).padStart(2, '0') + ' mnt');
        if (d === 0) parts.push(String(s).padStart(2, '0') + ' dtk');

        countdown.textContent = parts.join(' · ');
    }
    updateCountdown();
    if (countdown) setInterval(updateCountdown, 1000);

    /* ═══════════════════════════════════════════════════════════
       DELETE (OWNER ONLY)
    ═══════════════════════════════════════════════════════════ */
    const btnDelete   = document.getElementById('btnDeleteItem');
    const deleteForm  = document.getElementById('deleteItemForm');

    if (btnDelete && deleteForm) {
        btnDelete.addEventListener('click', function () {
            const doDelete = () => deleteForm.submit();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus konten ini?',
                    html: 'Tindakan ini akan menghapus <b>{{ addslashes($item->title) }}</b>.<br>Data dapat dipulihkan dari arsip.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    reverseButtons: true,
                    focusCancel: true,
                }).then((res) => { if (res.isConfirmed) doDelete(); });
            } else if (confirm('Hapus konten ini?')) {
                doDelete();
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════
       FILE PREVIEW MODAL
    ═══════════════════════════════════════════════════════════ */
    const cards = Array.from(document.querySelectorAll('.lms-file-card'));
    if (cards.length === 0) return;

    const modal        = document.getElementById('lmsPreview');
    const modalTitle   = document.getElementById('lmsPreviewTitle');
    const modalMeta    = document.getElementById('lmsPreviewMeta');
    const modalContent = document.getElementById('lmsPreviewContent');
    const modalThumbs  = document.getElementById('lmsPreviewThumbs');
    const btnClose     = document.getElementById('lmsPreviewClose');
    const btnPrev      = document.getElementById('lmsPreviewPrev');
    const btnNext      = document.getElementById('lmsPreviewNext');
    const btnFull      = document.getElementById('lmsPreviewFullscreen');
    const btnOpen      = document.getElementById('lmsPreviewOpen');
    const btnDown      = document.getElementById('lmsPreviewDownload');

    const files = cards.map((c) => ({
        name: c.dataset.attName || 'Lampiran',
        url:  c.dataset.attUrl  || '',
        ext:  (c.dataset.attExt || '').toLowerCase(),
        size: c.dataset.attSize || '',
    }));

    let current = 0;

    // ---- Build thumbs ----
    const iconFor = (ext) => {
        if (ext === 'pdf') return 'fa-file-pdf';
        if (['doc','docx'].includes(ext)) return 'fa-file-word';
        if (['xls','xlsx','csv'].includes(ext)) return 'fa-file-excel';
        if (['ppt','pptx'].includes(ext)) return 'fa-file-powerpoint';
        if (ext === 'txt') return 'fa-file-lines';
        if (['jpg','jpeg','png','webp','gif','svg'].includes(ext)) return 'fa-file-image';
        if (['mp4','webm','ogg','mov'].includes(ext)) return 'fa-file-video';
        return 'fa-file';
    };

    files.forEach((f, i) => {
        const t = document.createElement('button');
        t.type = 'button';
        t.className = 'lms-thumb';
        t.dataset.idx = i;
        t.innerHTML = `<i class="fas ${iconFor(f.ext)}"></i><span>${escapeHtml(f.name)}</span>`;
        t.addEventListener('click', () => show(i));
        modalThumbs.appendChild(t);
    });

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, (c) => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    function humanSize(bytes) {
        if (!bytes || isNaN(bytes)) return '';
        const n = parseInt(bytes, 10);
        if (n < 1024) return n + ' B';
        if (n < 1024 * 1024) return (n / 1024).toFixed(1) + ' KB';
        return (n / (1024 * 1024)).toFixed(2) + ' MB';
    }

    // ---- Render viewer content ----
    async function renderContent(f) {
    const ext = f.ext;
    const url = f.url;

    // ---------- IMAGE ----------
    if (['jpg','jpeg','png','webp','gif','svg'].includes(ext)) {
        return `<img src="${url}" alt="${escapeHtml(f.name)}" class="max-w-full max-h-full object-contain rounded-xl">`;
    }

    // ---------- VIDEO ----------
    if (['mp4','webm','ogg','mov'].includes(ext)) {
        return `<video src="${url}" controls class="max-w-full max-h-full rounded-xl bg-black"></video>`;
    }

    // ---------- PDF (native browser, jalan di localhost) ----------
    if (ext === 'pdf') {
        return `<iframe src="${url}#toolbar=1&navpanes=0" title="${escapeHtml(f.name)}"></iframe>`;
    }

    // ---------- DOCX → mammoth.js (offline, jalan di localhost) ----------
    if (ext === 'docx') {
        if (typeof mammoth === 'undefined') {
            return fallbackDownload(f, 'Pustaka pratinjau DOCX belum siap. Coba refresh.');
        }
        try {
            const res  = await fetch(url, { credentials: 'same-origin' });
            const buf  = await res.arrayBuffer();
            const out  = await mammoth.convertToHtml({ arrayBuffer: buf });
            const html = out.value || '<p><em>Dokumen kosong.</em></p>';

            return `
                <div class="w-full h-full overflow-auto rounded-xl bg-white text-slate-900"
                     style="padding:2rem 2.5rem;">
                    <div class="docx-preview" style="max-width:820px;margin:0 auto;font-family:Georgia,serif;line-height:1.65;font-size:15px;">
                        ${html}
                    </div>
                </div>
                <style>
                    .docx-preview h1{font-size:1.7em;font-weight:800;margin:.8em 0 .4em;}
                    .docx-preview h2{font-size:1.4em;font-weight:700;margin:.8em 0 .35em;}
                    .docx-preview h3{font-size:1.15em;font-weight:700;margin:.7em 0 .3em;}
                    .docx-preview p{margin:.6em 0;}
                    .docx-preview ul,.docx-preview ol{margin:.6em 0 .6em 1.4em;}
                    .docx-preview li{margin:.25em 0;}
                    .docx-preview table{border-collapse:collapse;margin:1em 0;width:100%;font-size:.92em;}
                    .docx-preview th,.docx-preview td{border:1px solid #cbd5e1;padding:.5em .7em;}
                    .docx-preview th{background:#f1f5f9;font-weight:700;}
                    .docx-preview img{max-width:100%;height:auto;margin:.6em 0;}
                    .docx-preview blockquote{border-left:3px solid #8b5cf6;padding-left:1em;color:#475569;margin:.8em 0;}
                </style>
            `;
        } catch (e) {
            console.error('[docx-preview]', e);
            return fallbackDownload(f, 'Gagal memuat pratinjau DOCX.');
        }
    }

    // ---------- DOC (format lama, tidak bisa mammoth) ----------
    if (ext === 'doc') {
        return fallbackDownload(f, 'Format .doc (Word lama) tidak dapat dipratinjau di browser. Unduh untuk melihat.');
    }

    // ---------- XLSX / XLS ----------
    if (['xlsx','xls','csv'].includes(ext)) {
        // CSV → tampilkan sebagai tabel
        if (ext === 'csv') {
            try {
                const txt = await (await fetch(url, { credentials: 'same-origin' })).text();
                const rows = txt.split(/\r?\n/).filter(Boolean).map(r => r.split(','));
                const head = rows[0] || [];
                const body = rows.slice(1);
                return `
                    <div class="w-full h-full overflow-auto rounded-xl bg-white text-slate-900 p-6">
                        <table style="border-collapse:collapse;width:100%;font-size:.85rem;">
                            <thead>
                                <tr>${head.map(h => `<th style="border:1px solid #cbd5e1;padding:.5em .7em;background:#f1f5f9;">${escapeHtml(h)}</th>`).join('')}</tr>
                            </thead>
                            <tbody>
                                ${body.map(r => `<tr>${r.map(c => `<td style="border:1px solid #cbd5e1;padding:.4em .6em;">${escapeHtml(c)}</td>`).join('')}</tr>`).join('')}
                            </tbody>
                        </table>
                    </div>`;
            } catch (e) {
                return fallbackDownload(f, 'Gagal memuat CSV.');
            }
        }
        return fallbackDownload(f, 'Pratinjau XLS/XLSX tidak tersedia. Unduh untuk membuka di Excel.');
    }

    // ---------- PPTX / PPT ----------
    if (['ppt','pptx'].includes(ext)) {
        return fallbackDownload(f, 'Pratinjau PowerPoint tidak tersedia. Unduh untuk membuka di PowerPoint.');
    }

    // ---------- TXT / MD / JSON / XML ----------
    if (['txt','md','log','json','xml'].includes(ext)) {
        try {
            const txt = await (await fetch(url, { credentials: 'same-origin' })).text();
            return `
                <div class="w-full h-full overflow-auto rounded-xl bg-slate-900 text-emerald-300 p-6"
                     style="font-family:'JetBrains Mono',Consolas,monospace;font-size:.85rem;line-height:1.7;white-space:pre-wrap;word-break:break-word;">
                    ${escapeHtml(txt)}
                </div>`;
        } catch (e) {
            return fallbackDownload(f, 'Gagal memuat teks.');
        }
    }

    // ---------- Fallback ----------
    return fallbackDownload(f, `Format .${ext} tidak dapat dipratinjau.`);
}

function fallbackDownload(f, msg) {
    return `
        <div class="text-center text-white/80 px-6">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-white/10 flex items-center justify-center mb-4">
                <i class="fas ${iconFor(f.ext)} text-3xl"></i>
            </div>
            <p class="text-sm font-bold mb-1">${escapeHtml(msg)}</p>
            <p class="text-xs opacity-70 mb-4">${escapeHtml(f.name)}</p>
            <a href="${f.url}" download class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-xs font-bold">
                <i class="fas fa-download"></i> Unduh File
            </a>
        </div>`;
}

   async function show(idx) {
    if (idx < 0 || idx >= files.length) return;
    current = idx;
    const f = files[idx];

    modalTitle.textContent = f.name;
    modalMeta.textContent  = `${(f.ext || 'FILE').toUpperCase()}${f.size ? ' · ' + humanSize(f.size) : ''} · ${idx + 1} / ${files.length}`;

    btnOpen.href = f.url;
    btnDown.href = f.url;
    btnDown.setAttribute('download', f.name);

    modalContent.innerHTML = '<div class="text-white/60 text-xs"><i class="fas fa-circle-notch fa-spin mr-2"></i> Memuat pratinjau...</div>';
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    modalContent.innerHTML = await renderContent(f);

    // thumbs
    modalThumbs.querySelectorAll('.lms-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === idx);
    });

    btnPrev.style.display = files.length > 1 ? '' : 'none';
    btnNext.style.display = files.length > 1 ? '' : 'none';
}

    function close() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modalContent.innerHTML = '';
        document.body.style.overflow = '';
        if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
    }

    function prev() { show((current - 1 + files.length) % files.length); }
    function next() { show((current + 1) % files.length); }

    // ---- Bind cards ----
    cards.forEach((c, i) => {
        c.addEventListener('click', () => show(i));
        c.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); show(i); }
        });
    });

    // ---- Modal controls ----
    btnClose.addEventListener('click', close);
    btnPrev.addEventListener('click', prev);
    btnNext.addEventListener('click', next);

    btnFull.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            modal.requestFullscreen?.().catch(() => {});
        } else {
            document.exitFullscreen?.().catch(() => {});
        }
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });

    // ---- Keyboard ----
    document.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('is-open')) return;
        if (e.key === 'Escape')      close();
        if (e.key === 'ArrowLeft')   prev();
        if (e.key === 'ArrowRight')  next();
        if (e.key === 'f' || e.key === 'F') btnFull.click();
    });
})();
</script>
@endpush