@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', $notif->title . ' – Notifikasi UNICODE')

@section('content')
@php
    // ═══════════════════════════════════════════════════════
    // 1. STATUS & META
    // ═══════════════════════════════════════════════════════
    $isUnread   = is_null($notif->read_at);
    $isStarred  = ! is_null($notif->starred_at);
    $isArchived = ! is_null($notif->archived_at);
    $isActioned = ! is_null($notif->actioned_at);
    $priority   = $notif->priority ?? 'normal';

    // ═══════════════════════════════════════════════════════
    // 2. AUTO-DETECT LINK TUJUAN
    //    Prioritas: action_url → data.url → entity → fallback
    // ═══════════════════════════════════════════════════════
    $targetUrl = null;
    $targetLabel = 'Buka Halaman Terkait';

    if (! empty($notif->action_url) && $notif->action_url !== '#') {
        $targetUrl = $notif->action_url;
    } elseif (! empty($notif->data['url'])) {
        $targetUrl = $notif->data['url'];
    } elseif (! empty($notif->data['link'])) {
        $targetUrl = $notif->data['link'];
    } else {
        // Auto build dari entity_type + entity_id
        $entityShort = $notif->entity_type ? class_basename($notif->entity_type) : null;
        $entityId    = $notif->entity_id;

        if ($entityShort && $entityId) {
            $targetUrl = match ($entityShort) {
                'LmsItem'           => route('owner.lms.items.show', $entityId),
                'Group'             => route('owner.lms.groups.show', $entityId),
                'Booking'           => route('owner.bookings.show', $entityId),
                'User'              => route('owner.user', $entityId),
                'GroupAnnouncement' => route('owner.kelompok.pengumuman'),
                default             => null,
            };
        }
    }

    // Label tombol sesuai konteks
    $typeKey = $notif->data['type'] ?? $notif->type ?? '';
    if (str_contains($typeKey, 'lms_reminder')) {
        $targetLabel = 'Buka Tugas & Kumpulkan';
    } elseif (str_contains($typeKey, 'lms')) {
        $targetLabel = 'Lihat Konten LMS';
    }

    // ═══════════════════════════════════════════════════════
    // 3. THEME berdasarkan tipe notifikasi
    // ═══════════════════════════════════════════════════════
    $theme = (function () use ($typeKey, $priority) {
        if (str_contains($typeKey, 'lms_reminder')) return [
            'gradient' => 'from-violet-500 via-fuchsia-500 to-pink-500',
            'icon'     => 'fa-bell',
            'emoji'    => '📚',
            'label'    => 'Pengingat Tugas',
        ];
        if (str_contains($typeKey, 'lms')) return [
            'gradient' => 'from-violet-500 to-indigo-600',
            'icon'     => 'fa-graduation-cap',
            'emoji'    => '🎓',
            'label'    => 'LMS',
        ];
        if (str_contains($typeKey, 'booking')) return [
            'gradient' => 'from-emerald-500 to-teal-600',
            'icon'     => 'fa-calendar-check',
            'emoji'    => '📅',
            'label'    => 'Booking',
        ];
        if (str_contains($typeKey, 'announcement')) return [
            'gradient' => 'from-amber-500 to-orange-600',
            'icon'     => 'fa-bullhorn',
            'emoji'    => '📣',
            'label'    => 'Pengumuman',
        ];
        return [
            'gradient' => 'from-slate-600 to-slate-800',
            'icon'     => 'fa-bell',
            'emoji'    => '🔔',
            'label'    => 'Notifikasi',
        ];
    })();

    $priorityBadge = match ($priority) {
        'urgent'    => ['label' => 'Mendesak', 'cls' => 'bg-rose-500 text-white'],
        'high'      => ['label' => 'Tinggi',    'cls' => 'bg-orange-500 text-white'],
        'important' => ['label' => 'Penting',   'cls' => 'bg-amber-500 text-white'],
        default     => null,
    };

    // Data tambahan — sembunyikan yang teknis
    $extraData = collect($notif->data ?? [])
        ->except(['title', 'message', 'url', 'link', 'type', 'group_names', 'group_count'])
        ->filter(fn ($v) => ! is_null($v) && $v !== '' && $v !== [])
        ->all();
@endphp

<div class="min-h-full bg-gradient-to-b from-slate-50 to-white dark:from-slate-900 dark:to-slate-950">
    <div class="p-4 md:p-6 max-w-3xl mx-auto">

        {{-- ═══════════════ BACK ═══════════════ --}}
        <div class="mb-4">
            <a href="{{ route('owner.notifications.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold
                      text-slate-600 dark:text-slate-300
                      hover:text-violet-600 dark:hover:text-violet-400 transition">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali ke Notifikasi
            </a>
        </div>

        {{-- ═══════════════ CARD ═══════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-900/5 dark:shadow-black/20
                    ring-1 ring-black/5 dark:ring-white/5 overflow-hidden">

            {{-- ═════════ HERO HEADER ═════════ --}}
            <div class="relative overflow-hidden bg-gradient-to-br {{ $theme['gradient'] }} text-white">
                {{-- Decorative circles --}}
                <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-20 -left-12 w-64 h-64 rounded-full bg-black/10 blur-3xl"></div>

                <div class="relative px-6 py-7 md:px-8 md:py-9">

                    {{-- Priority ribbon --}}
                    @if($priorityBadge)
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                         text-[10px] font-black uppercase tracking-wider
                                         {{ $priorityBadge['cls'] }} shadow-lg">
                                <i class="fas fa-bolt text-[9px]"></i>
                                {{ $priorityBadge['label'] }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-start gap-4 md:gap-5">
                        {{-- Icon --}}
                        <div class="shrink-0 w-16 h-16 md:w-20 md:h-20 rounded-2xl
                                    bg-white/20 backdrop-blur-sm
                                    ring-1 ring-white/30
                                    flex items-center justify-center
                                    text-3xl md:text-4xl shadow-lg">
                            {{ $theme['emoji'] }}
                        </div>

                        {{-- Title --}}
                        <div class="flex-1 min-w-0 pt-1">
                            <p class="text-[11px] font-black uppercase tracking-[0.15em] text-white/75 mb-1.5">
                                {{ $theme['label'] }}
                            </p>
                            <h1 class="text-xl md:text-2xl lg:text-3xl font-black leading-tight
                                       break-words text-white">
                                {{ $notif->title ?? 'Notifikasi' }}
                            </h1>

                            {{-- Status pills --}}
                            <div class="flex flex-wrap items-center gap-1.5 mt-3">
                                @if($isStarred)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-[10px] font-bold bg-white/25 backdrop-blur-sm
                                                 ring-1 ring-white/30">
                                        <i class="fas fa-star text-amber-300 text-[9px]"></i> Favorit
                                    </span>
                                @endif

                                @if($isUnread)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-[10px] font-bold bg-white/25 backdrop-blur-sm
                                                 ring-1 ring-white/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-300 animate-pulse"></span>
                                        Belum Dibaca
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-[10px] font-bold bg-white/15 backdrop-blur-sm
                                                 ring-1 ring-white/20">
                                        <i class="fas fa-check-circle text-[9px]"></i> Dibaca
                                    </span>
                                @endif

                                @if($isArchived)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-[10px] font-bold bg-white/15 backdrop-blur-sm
                                                 ring-1 ring-white/20">
                                        <i class="fas fa-box-archive text-[9px]"></i> Diarsipkan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Timestamp --}}
                    <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-1
                                text-[11px] md:text-xs text-white/80">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="far fa-clock"></i>
                            {{ optional($notif->created_at)->translatedFormat('d F Y, H:i') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-hourglass-half"></i>
                            {{ optional($notif->created_at)->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ═════════ PRIMARY CTA — AUTO LINK ═════════ --}}
            @if($targetUrl)
                <div class="px-6 py-5 md:px-8 md:py-6
                            bg-gradient-to-r from-slate-50 to-white dark:from-slate-900 dark:to-slate-800
                            border-b border-slate-200 dark:border-slate-700">
                    <a href="{{ $targetUrl }}"
                       class="group flex items-center gap-4 p-4 rounded-2xl
                              bg-white dark:bg-slate-800
                              ring-2 ring-violet-200 dark:ring-violet-700/40
                              hover:ring-violet-400 dark:hover:ring-violet-500
                              hover:shadow-lg hover:shadow-violet-500/10
                              transition-all active:scale-[0.99]">
                        <div class="shrink-0 w-12 h-12 rounded-xl
                                    bg-gradient-to-br from-violet-500 to-fuchsia-600
                                    flex items-center justify-center
                                    text-white shadow-md shadow-violet-500/30
                                    group-hover:scale-105 transition-transform">
                            <i class="fas fa-arrow-right text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-wider
                                      text-violet-600 dark:text-violet-400">
                                Tindakan Diperlukan
                            </p>
                            <p class="text-sm md:text-base font-black text-slate-900 dark:text-white mt-0.5">
                                {{ $targetLabel }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                {{ $targetUrl }}
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-slate-400 group-hover:text-violet-500
                                  group-hover:translate-x-1 transition-all"></i>
                    </a>
                </div>
            @endif

            {{-- ═════════ BODY ═════════ --}}
            <div class="px-6 py-6 md:px-8 md:py-8 space-y-6">

                {{-- MESSAGE --}}
                @if($notif->message || ($notif->data['message'] ?? null))
                    <div>
                        <div class="flex items-center gap-2 mb-2.5">
                            <i class="fas fa-quote-left text-violet-500 text-xs"></i>
                            <h3 class="text-[11px] font-black uppercase tracking-wider
                                       text-slate-500 dark:text-slate-400">
                                Pesan
                            </h3>
                        </div>
                        <div class="rounded-2xl p-5
                                    bg-gradient-to-br from-slate-50 to-white
                                    dark:from-slate-900 dark:to-slate-800/50
                                    border border-slate-200 dark:border-slate-700
                                    text-sm md:text-[15px] leading-relaxed whitespace-pre-line
                                    text-slate-700 dark:text-slate-200
                                    shadow-sm">
                            {{ $notif->message ?? $notif->data['message'] }}
                        </div>
                    </div>
                @endif

                {{-- GROUP TARGETS --}}
                @php
                    $groupNames = $notif->data['group_names'] ?? [];
                    $groupCount = $notif->data['group_count'] ?? 0;
                @endphp
                @if(! empty($groupNames))
                    <div>
                        <div class="flex items-center gap-2 mb-2.5">
                            <i class="fas fa-users text-blue-500 text-xs"></i>
                            <h3 class="text-[11px] font-black uppercase tracking-wider
                                       text-slate-500 dark:text-slate-400">
                                Target Kelompok
                                @if($groupCount > 0)
                                    <span class="text-slate-400 font-normal normal-case">({{ $groupCount }})</span>
                                @endif
                            </h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($groupNames as $gName)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                             text-xs font-bold
                                             bg-blue-50 dark:bg-blue-900/20
                                             text-blue-700 dark:text-blue-300
                                             border border-blue-200 dark:border-blue-800">
                                    <i class="fas fa-folder-open text-[10px]"></i>
                                    {{ $gName }}
                                </span>
                            @endforeach
                            @if($groupCount > count($groupNames))
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl
                                             text-xs font-bold
                                             bg-slate-100 dark:bg-slate-700
                                             text-slate-500 dark:text-slate-400">
                                    +{{ $groupCount - count($groupNames) }} lainnya
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- REQUIRES ACTION NOTICE --}}
                @if($notif->requires_action && ! $isActioned)
                    <div class="rounded-2xl p-4
                                bg-gradient-to-r from-amber-50 to-orange-50
                                dark:from-amber-900/20 dark:to-orange-900/20
                                border border-amber-200 dark:border-amber-700/40
                                flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-xl
                                    bg-amber-500 text-white
                                    flex items-center justify-center shadow-sm">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-black text-amber-900 dark:text-amber-200">
                                Notifikasi ini membutuhkan tindakan
                            </p>
                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-1 leading-relaxed">
                                Selesaikan tindakan yang diperlukan, lalu tandai sebagai sudah ditangani di bawah.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- EXTRA DATA — Accordion (default collapsed) --}}
                @if(! empty($extraData))
                    <details class="group">
                        <summary class="cursor-pointer list-none flex items-center justify-between
                                        p-3 rounded-xl
                                        bg-slate-50 dark:bg-slate-900/50
                                        border border-slate-200 dark:border-slate-700
                                        hover:bg-slate-100 dark:hover:bg-slate-900
                                        transition">
                            <span class="flex items-center gap-2 text-[11px] font-black uppercase
                                         tracking-wider text-slate-500 dark:text-slate-400">
                                <i class="fas fa-circle-info"></i>
                                Detail Teknis ({{ count($extraData) }})
                            </span>
                            <i class="fas fa-chevron-down text-slate-400
                                      group-open:rotate-180 transition-transform text-xs"></i>
                        </summary>
                        <div class="mt-2 rounded-xl p-4
                                    bg-slate-50 dark:bg-slate-900/50
                                    border border-slate-200 dark:border-slate-700
                                    overflow-x-auto">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                @foreach($extraData as $key => $value)
                                    <div class="min-w-0">
                                        <dt class="text-slate-500 dark:text-slate-400 font-bold uppercase
                                                   tracking-wider text-[10px]">
                                            {{ str_replace(['_', '-'], ' ', $key) }}
                                        </dt>
                                        <dd class="text-slate-800 dark:text-slate-200 mt-1 break-words
                                                   font-mono text-[11px]">
                                            @if(is_array($value))
                                                {{ implode(', ', array_map(fn ($v) => is_scalar($v) ? $v : json_encode($v), $value)) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </details>
                @endif

                {{-- STATUS TIMELINE --}}
                <div class="pt-2">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-timeline text-slate-400 text-xs"></i>
                        <h3 class="text-[11px] font-black uppercase tracking-wider
                                   text-slate-500 dark:text-slate-400">
                            Timeline
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-7 h-7 rounded-full bg-emerald-500 text-white
                                        flex items-center justify-center text-[10px] shadow-sm">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="pt-0.5">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Dibuat</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ optional($notif->created_at)->translatedFormat('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        @if(! $isUnread)
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-7 h-7 rounded-full bg-blue-500 text-white
                                            flex items-center justify-center text-[10px] shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Dibaca</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ optional($notif->read_at)->translatedFormat('d M Y, H:i') ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($isActioned)
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-7 h-7 rounded-full bg-violet-500 text-white
                                            flex items-center justify-center text-[10px] shadow-sm">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Ditangani</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ optional($notif->actioned_at)->translatedFormat('d M Y, H:i') ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($isArchived)
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-7 h-7 rounded-full bg-slate-500 text-white
                                            flex items-center justify-center text-[10px] shadow-sm">
                                    <i class="fas fa-box-archive"></i>
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Diarsipkan</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ optional($notif->archived_at)->translatedFormat('d M Y, H:i') ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═════════ FOOTER ACTIONS ═════════ --}}
            <div class="px-6 py-4 md:px-8 md:py-5
                        border-t border-slate-200 dark:border-slate-700
                        bg-slate-50/60 dark:bg-slate-900/40
                        flex flex-wrap items-center justify-between gap-3">

                {{-- Kiri: sekunder --}}
                <div class="flex flex-wrap items-center gap-2">

                    {{-- Star --}}
                    <form action="{{ route('owner.notifications.toggle-star', $notif->id) }}"
                          method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-3 py-2 rounded-xl text-xs font-bold
                                       border transition inline-flex items-center gap-1.5
                                       {{ $isStarred
                                           ? 'text-amber-600 border-amber-300 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700'
                                           : 'text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                            <i class="fas fa-star text-[11px]"></i>
                            {{ $isStarred ? 'Batal Favorit' : 'Favorit' }}
                        </button>
                    </form>

                    {{-- Archive --}}
                    @if($isArchived)
                        <form action="{{ route('owner.notifications.unarchive', $notif->id) }}"
                              method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-2 rounded-xl text-xs font-bold
                                           text-slate-600 dark:text-slate-300
                                           border border-slate-300 dark:border-slate-600
                                           hover:bg-slate-100 dark:hover:bg-slate-700
                                           transition inline-flex items-center gap-1.5">
                                <i class="fas fa-box-open text-[11px]"></i>
                                Kembalikan
                            </button>
                        </form>
                    @else
                        <form action="{{ route('owner.notifications.archive', $notif->id) }}"
                              method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-2 rounded-xl text-xs font-bold
                                           text-slate-600 dark:text-slate-300
                                           border border-slate-300 dark:border-slate-600
                                           hover:bg-slate-100 dark:hover:bg-slate-700
                                           transition inline-flex items-center gap-1.5">
                                <i class="fas fa-box-archive text-[11px]"></i>
                                Arsipkan
                            </button>
                        </form>
                    @endif

                    {{-- Actioned --}}
                    @if($notif->requires_action && ! $isActioned)
                        <form action="{{ route('owner.notifications.actioned', $notif->id) }}"
                              method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-2 rounded-xl text-xs font-bold
                                           bg-emerald-600 hover:bg-emerald-700 text-white
                                           transition inline-flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-check-double text-[11px]"></i>
                                Tandai Sudah Ditangani
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Kanan: hapus --}}
                @if(! $isUnread)
                    <form action="{{ route('owner.notifications.destroy', $notif->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus notifikasi ini secara permanen?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3 py-2 rounded-xl text-xs font-bold
                                       bg-rose-600 hover:bg-rose-700 text-white
                                       transition inline-flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-trash text-[11px]"></i>
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- ═════════ AUTO-MARK AS READ ═════════ --}}
        @if($isUnread)
            <script>
                // Auto-mark as read 1.2 detik setelah halaman dibuka
                setTimeout(() => {
                    fetch('{{ route('owner.notifications.read', $notif->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    }).catch(() => {});
                }, 1200);
            </script>
        @endif

    </div>
</div>
@endsection