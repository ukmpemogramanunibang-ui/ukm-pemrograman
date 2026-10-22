@php
    $p = \App\Helpers\NotificationPresenter::present($n);

    // Visual styling berdasarkan priority
    $isUrgent    = $p['is_urgent'];
    $isImportant = $p['is_important'];
    $isUnread    = $p['is_unread'];

    // Container class
    $cardClass = 'notif-item group relative flex items-start gap-3 p-4 transition';
    if ($isUnread)    $cardClass .= ' is-unread';
    if ($isUrgent)    $cardClass .= ' is-urgent';
    if ($isImportant && ! $isUrgent) $cardClass .= ' is-important';

    // Icon wrapper class
    $iconBg = match (true) {
        $isUrgent    => 'bg-gradient-to-br from-rose-400 to-red-500 shadow-rose-200 dark:shadow-rose-900/40',
        $isImportant => 'bg-gradient-to-br from-amber-400 to-orange-500 shadow-amber-200 dark:shadow-amber-900/40',
        default      => 'bg-slate-100 dark:bg-slate-700',
    };
    $iconShadow = ($isImportant || $isUrgent) ? 'shadow-md' : '';
@endphp

<div class="{{ $cardClass }}" data-ni-item="{{ $p['id'] }}">

    {{-- ===================================================== --}}
    {{-- PRIORITY STRIPE (kiri) — hanya untuk penting/urgent --}}
    {{-- ===================================================== --}}
    @if($isUrgent)
        <div class="absolute left-0 top-0 bottom-0 w-1
                    bg-gradient-to-b from-rose-500 to-red-600 rounded-l-xl"></div>
    @elseif($isImportant)
        <div class="absolute left-0 top-0 bottom-0 w-1
                    bg-gradient-to-b from-amber-400 to-orange-500 rounded-l-xl"></div>
    @endif

    {{-- ===================================================== --}}
    {{-- ICON --}}
    {{-- ===================================================== --}}
    <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                text-lg {{ $iconBg }} {{ $iconShadow }}">
        @if($isUrgent || $isImportant)
            <span class="text-white">{{ $p['source_emoji'] }}</span>
        @else
            <span>{{ $p['source_emoji'] }}</span>
        @endif
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENT --}}
    {{-- ===================================================== --}}
    <a href="{{ route('owner.notifications.show', $p['id']) }}"
       data-ni-open
       data-id="{{ $p['id'] }}"
       data-read="{{ $isUnread ? 0 : 1 }}"
       class="flex-1 min-w-0">

        {{-- Badge priority --}}
        @if($isUrgent || $isImportant)
            <div class="flex items-center gap-1.5 mb-1">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                             text-[10px] font-black uppercase tracking-wider
                             {{ $isUrgent
                                 ? 'bg-rose-500 text-white shadow-sm shadow-rose-200 dark:shadow-rose-900/40'
                                 : 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm shadow-amber-200 dark:shadow-amber-900/40' }}">
                    <i class="fas {{ $isUrgent ? 'fa-triangle-exclamation' : 'fa-star' }} text-[8px]"></i>
                    {{ $isUrgent ? 'DARURAT' : 'PENTING' }}
                </span>

                @if($isUrgent)
                    <span class="inline-flex items-center gap-1 text-[10px]
                                 text-rose-600 dark:text-rose-400 font-bold animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Tindakan Segera
                    </span>
                @endif
            </div>
        @endif

        {{-- Title --}}
        <div class="flex items-start gap-2 mb-0.5">
            <p class="text-sm font-bold
                      {{ $isImportant ? 'text-slate-900 dark:text-white' : 'text-slate-800 dark:text-slate-100' }}
                      flex-1 min-w-0 leading-snug">
                {{ $p['title'] }}
            </p>

            @if($isUnread)
                <span class="shrink-0 w-2 h-2 rounded-full mt-1.5 ring-2
                             {{ $isImportant
                                 ? 'bg-rose-500 ring-rose-100 dark:ring-rose-900/40'
                                 : 'bg-violet-500 ring-violet-100 dark:ring-violet-900/40' }}"></span>
            @endif

            @if($p['is_starred'])
                <i class="fas fa-star text-amber-400 text-xs mt-1 shrink-0"></i>
            @endif
        </div>

        {{-- Message --}}
        <p class="text-xs text-slate-600 dark:text-slate-400
                  line-clamp-2 leading-relaxed">
            {{ $p['message'] }}
        </p>

        {{-- Sender info (jika ada) --}}
        @if($p['sender_name'])
            <div class="flex items-center gap-1.5 mt-1.5 text-[11px]
                        text-slate-500 dark:text-slate-400">
                <i class="fas fa-user-circle text-[10px]"></i>
                <span>Dari: <strong class="text-slate-700 dark:text-slate-300">{{ $p['sender_name'] }}</strong></span>
            </div>
        @endif

        {{-- Group chips --}}
        @if(! empty($p['group_names']))
            <div class="flex flex-wrap gap-1 mt-2">
                @foreach(array_slice($p['group_names'], 0, 3) as $gName)
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold
                                 bg-blue-50 dark:bg-blue-900/20
                                 text-blue-600 dark:text-blue-300">
                        <i class="fas fa-users text-[8px] mr-0.5"></i>
                        {{ $gName }}
                    </span>
                @endforeach
                @if($p['group_count'] > 3)
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-500 dark:text-slate-400">
                        +{{ $p['group_count'] - 3 }} lainnya
                    </span>
                @endif
            </div>
        @endif

        {{-- Meta footer --}}
        <div class="flex flex-wrap items-center gap-2 mt-2
                    text-[10px] text-slate-400">
            <span class="inline-flex items-center gap-1">
                <i class="far fa-clock"></i>
                {{ $p['time_ago'] }}
            </span>

            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                         bg-slate-100 dark:bg-slate-700
                         text-slate-500 dark:text-slate-300 font-semibold">
                {{ $p['source_label'] }}
            </span>

            @if($p['requires_action'] && ! $p['actioned_at'])
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                             bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300
                             font-bold">
                    <i class="fas fa-bolt text-[8px]"></i>
                    Perlu Aksi
                </span>
            @endif

            @if($p['is_archived'])
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                             bg-slate-100 dark:bg-slate-700
                             text-slate-500 dark:text-slate-400 font-semibold">
                    <i class="fas fa-box-archive text-[8px]"></i>
                    Diarsipkan
                </span>
            @endif
        </div>
    </a>

    {{-- ===================================================== --}}
    {{-- ACTIONS --}}
    {{-- ===================================================== --}}
    <div class="shrink-0 flex items-center gap-0.5
                md:opacity-0 md:group-hover:opacity-100 transition">

        {{-- Star --}}
        <button type="button"
                data-ni-star
                data-id="{{ $p['id'] }}"
                data-starred="{{ $p['is_starred'] ? 1 : 0 }}"
                aria-label="Toggle favorit"
                class="w-8 h-8 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20
                       flex items-center justify-center transition"
                title="{{ $p['is_starred'] ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}">
            <i class="fas fa-star text-xs
                      {{ $p['is_starred'] ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600' }}"></i>
        </button>

        {{-- Archive / Unarchive --}}
        @if($p['is_archived'])
            <button type="button"
                    data-ni-unarchive data-id="{{ $p['id'] }}"
                    aria-label="Kembalikan dari arsip"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700
                           flex items-center justify-center transition"
                    title="Kembalikan dari arsip">
                <i class="fas fa-box-open text-xs text-slate-400 dark:text-slate-500"></i>
            </button>
        @else
            <button type="button"
                    data-ni-archive data-id="{{ $p['id'] }}"
                    aria-label="Arsipkan"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700
                           flex items-center justify-center transition"
                    title="Arsipkan">
                <i class="fas fa-box-archive text-xs text-slate-400 dark:text-slate-500"></i>
            </button>
        @endif

        {{-- Delete --}}
        @if(! $p['is_unread'])
            <button type="button" data-ni-del data-id="{{ $p['id'] }}"
                    aria-label="Hapus"
                    class="w-8 h-8 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20
                           flex items-center justify-center transition"
                    title="Hapus">
                <i class="fas fa-trash text-xs text-slate-300 dark:text-slate-600
                          hover:text-rose-500"></i>
            </button>
        @else
            <button type="button" data-ni-blocked-delete
                    aria-label="Buka dulu untuk menghapus"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           opacity-40 cursor-not-allowed"
                    title="Buka dulu untuk menghapus">
                <i class="fas fa-trash text-xs text-slate-300 dark:text-slate-600"></i>
            </button>
        @endif
    </div>
</div>