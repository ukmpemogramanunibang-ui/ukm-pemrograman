@extends('layouts.roles.owner')

@section('title', 'Notifikasi Center')

@push('styles')
<style>
[x-cloak] { display: none !important; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notif-item {
    transition: background-color .15s ease, transform .15s ease;
    background-color: transparent;
    position: relative;
}
.notif-item:hover {
    background-color: rgba(0, 0, 0, .015);
}
.dark .notif-item:hover {
    background-color: rgba(255, 255, 255, .02);
}
.notif-item.is-unread {
    background-color: rgba(139, 92, 246, .04);
}
.dark .notif-item.is-unread {
    background-color: rgba(139, 92, 246, .08);
}


.skeleton {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: skel 1.4s ease-in-out infinite;
    border-radius: 6px;
}
.dark .skeleton {
    background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
    background-size: 200% 100%;
}
@keyframes skel {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.toast-stack {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    pointer-events: none;
}
.toast {
    pointer-events: auto;
    min-width: 280px;
    max-width: 400px;
    padding: .75rem 1rem;
    border-radius: .75rem;
    box-shadow: 0 12px 30px -8px rgba(0,0,0,.25);
    font-size: .8rem;
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    color: #fff;
    animation: toastIn .25s ease;
}
.toast.success { background: #10b981; }
.toast.error   { background: #ef4444; }
.toast.warn    { background: #f59e0b; }
.toast.info    { background: #1e293b; }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(24px); }
    to   { opacity: 1; transform: translateX(0); }
}
.toast.hide {
    opacity: 0;
    transform: translateX(24px);
    transition: all .25s ease;
}
.notif-item.is-important {
    background: linear-gradient(to right, rgba(251, 191, 36, .08), transparent 40%);
    padding-left: 1.25rem;
}
.dark .notif-item.is-important {
    background: linear-gradient(to right, rgba(251, 191, 36, .05), transparent 40%);
}

.notif-item.is-urgent {
    background: linear-gradient(to right, rgba(244, 63, 94, .10), transparent 40%);
    padding-left: 1.25rem;
    animation: urgentPulse 2.5s ease-in-out infinite;
}
.dark .notif-item.is-urgent {
    background: linear-gradient(to right, rgba(244, 63, 94, .08), transparent 40%);
}

@keyframes urgentPulse {
    0%, 100% { background-color: transparent; }
    50%      { background-color: rgba(244, 63, 94, .04); }
}

/* ============================================================
 | NEW NOTIFICATION ANIMATION
 * ============================================================ */
.notif-item.new-flash {
    animation: newFlash 1.5s ease-out;
}
@keyframes newFlash {
    0%   { background-color: rgba(139, 92, 246, .25); }
    100% { background-color: transparent; }
}
</style>
@endpush

@section('content')

<div class="max-w-5xl mx-auto py-6 px-4"
     id="ni-root"
     data-count="{{ $unreadCount }}"
     data-counts='@json($counts)'
     data-tab="{{ $tab }}"
     data-poll-url="{{ route('owner.notifications.poll') }}"
     data-unread-url="{{ route('owner.notifications.unread-count') }}">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100
                       flex items-center gap-2">
                <i class="fas fa-bell text-violet-500"></i>
                Notifikasi Center
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                <span id="ni-count" class="font-bold text-violet-600">{{ $unreadCount }}</span>
                <span id="ni-count-label">belum dibaca</span>
                <span id="ni-sync-indicator"
                      class="ml-2 text-[11px] text-emerald-600 dark:text-emerald-400 hidden">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span>
                    live
                </span>
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" id="ni-readall"
                    class="px-3 py-2 text-xs font-semibold rounded-lg
                           bg-violet-600 text-white hover:bg-violet-700
                           transition flex items-center gap-1.5">
                <i class="fas fa-check-double"></i>
                <span>Tandai semua dibaca</span>
            </button>
            <button type="button" id="ni-clear"
                    class="px-3 py-2 text-xs font-semibold rounded-lg
                           bg-white text-rose-600 border border-rose-200
                           hover:bg-rose-50 transition flex items-center gap-1.5
                           dark:bg-slate-800 dark:border-rose-900/40 dark:hover:bg-rose-900/10">
                <i class="fas fa-broom"></i>
                <span>Bersihkan yang dibaca</span>
            </button>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- TABS --}}
    {{-- ========================================================= --}}
    <div class="flex gap-1 mb-4 overflow-x-auto pb-1
                border-b border-slate-200 dark:border-slate-700
                scrollbar-thin">
        @php
            $tabs = [
                'all'      => ['label' => 'Semua',         'icon' => 'fa-inbox'],
                'unread'   => ['label' => 'Belum Dibaca',  'icon' => 'fa-circle-dot'],
                'starred'  => ['label' => 'Favorit',       'icon' => 'fa-star'],
                'action'   => ['label' => 'Perlu Aksi',    'icon' => 'fa-bolt'],
                'archived' => ['label' => 'Arsip',         'icon' => 'fa-box-archive'],
            ];
        @endphp

        @foreach($tabs as $key => $meta)
            <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'page' => null]) }}"
               aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
               class="px-4 py-2.5 text-sm font-semibold rounded-t-lg
                      whitespace-nowrap transition border-b-2 -mb-px
                      flex items-center gap-1.5
                      {{ $tab === $key
                          ? 'text-violet-700 border-violet-600 bg-violet-50 dark:bg-violet-900/20 dark:text-violet-300'
                          : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="fas {{ $meta['icon'] }} text-xs"></i>
                <span>{{ $meta['label'] }}</span>
                @if(($counts[$key] ?? 0) > 0)
                    <span class="ml-0.5 text-[10px] px-1.5 py-0.5 rounded-full
                                 {{ $tab === $key
                                     ? 'bg-violet-600 text-white'
                                     : 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' }}
                                 font-bold min-w-[18px] text-center">
                        {{ $counts[$key] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- ========================================================= --}}
    {{-- TOOLBAR — SEARCH + FILTER --}}
    {{-- ========================================================= --}}
    <form method="GET" action="{{ route('owner.notifications.index') }}"
          id="ni-toolbar"
          class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                 ring-1 ring-black/5 p-3 md:p-4 mb-4 sticky top-2 z-20">

        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="flex flex-col md:flex-row gap-2 md:gap-3">

            {{-- Search --}}
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2
                          text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari judul atau isi notifikasi..."
                       autocomplete="off"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm
                              border-slate-300 dark:border-slate-600
                              bg-slate-50 dark:bg-slate-700
                              text-slate-900 dark:text-white
                              focus:ring-2 focus:ring-violet-500
                              focus:border-violet-500">
            </div>

            {{-- Source --}}
            <select name="source"
                    class="rounded-xl text-sm px-3 py-2.5
                           border-slate-300 dark:border-slate-600
                           bg-slate-50 dark:bg-slate-700
                           text-slate-900 dark:text-white
                           focus:ring-2 focus:ring-violet-500">
                <option value="">Semua Sumber</option>
                @php
                    $sources = config('unicode_notifications.sources', []);
                @endphp
                @foreach($sources as $key => $meta)
                    <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>
                        {{ $meta['label'] ?? ucfirst($key) }}
                    </option>
                @endforeach
            </select>

            {{-- Range --}}
            <select name="range"
                    class="rounded-xl text-sm px-3 py-2.5
                           border-slate-300 dark:border-slate-600
                           bg-slate-50 dark:bg-slate-700
                           text-slate-900 dark:text-white
                           focus:ring-2 focus:ring-violet-500">
                <option value="">Semua Waktu</option>
                <option value="today" {{ request('range') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="7d"    {{ request('range') === '7d'    ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30d"   {{ request('range') === '30d'   ? 'selected' : '' }}>30 Hari Terakhir</option>
            </select>

            <button type="submit"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-violet-600 hover:bg-violet-700 text-white
                           transition flex items-center justify-center gap-1.5
                           shrink-0">
                <i class="fas fa-filter text-xs"></i>
                <span class="hidden md:inline">Terapkan</span>
            </button>

            @if(request()->filled('q') || request()->filled('source') || request()->filled('range'))
                <a href="{{ route('owner.notifications.index', ['tab' => $tab]) }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold
                          bg-slate-100 dark:bg-slate-700
                          text-slate-600 dark:text-slate-300
                          hover:bg-slate-200 dark:hover:bg-slate-600
                          transition flex items-center justify-center gap-1.5 shrink-0">
                    <i class="fas fa-times text-xs"></i>
                    <span class="hidden md:inline">Reset</span>
                </a>
            @endif
        </div>
    </form>

    {{-- ========================================================= --}}
    {{-- LIST --}}
    {{-- ========================================================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                ring-1 ring-black/5 divide-y divide-slate-100 dark:divide-slate-700
                overflow-hidden"
         id="ni-list">

        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $source   = $notif->source ?? 'default';
                $sourceMeta = config("unicode_notifications.sources.{$source}", []);
                $emoji    = $sourceMeta['emoji'] ?? '🔔';
                $srcLabel = $sourceMeta['label'] ?? ucfirst(str_replace('_', ' ', $source));

                $priority = $notif->priority ?? 'normal';
                $prioBg = match ($priority) {
                    'urgent' => 'bg-rose-100 dark:bg-rose-900/30',
                    'high'   => 'bg-amber-100 dark:bg-amber-900/30',
                    'important' => 'bg-orange-100 dark:bg-orange-900/30',
                    default  => 'bg-violet-100 dark:bg-violet-900/30',
                };

                $groupNames = $notif->data['group_names'] ?? [];
                $groupCount = $notif->data['group_count'] ?? 0;
            @endphp

            <div class="notif-item group flex items-start gap-3 p-4
                        {{ $isUnread ? 'is-unread' : '' }}"
                 data-ni-item="{{ $notif->id }}">

                {{-- Icon --}}
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                            text-lg {{ $prioBg }}">
                    {{ $emoji }}
                </div>

                {{-- Content --}}
                <a href="{{ route('owner.notifications.show', $notif->id) }}"
                   data-ni-open
                   data-id="{{ $notif->id }}"
                   data-read="{{ $isUnread ? 0 : 1 }}"
                   class="flex-1 min-w-0">

                    <div class="flex items-start gap-2 mb-0.5">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100
                                  flex-1 min-w-0 truncate">
                            {{ $notif->title ?? ($notif->data['title'] ?? 'Notifikasi') }}
                        </p>

                        @if($isUnread)
                            <span class="shrink-0 w-2 h-2 bg-violet-500 rounded-full mt-1.5
                                         ring-2 ring-violet-100 dark:ring-violet-900/40"></span>
                        @endif

                        @if($notif->starred_at)
                            <i class="fas fa-star text-amber-400 text-xs mt-1 shrink-0"></i>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400
                              line-clamp-2 leading-relaxed">
                        {{ $notif->message ?? ($notif->data['message'] ?? '') }}
                    </p>

                    {{-- Group chips --}}
                    @if(! empty($groupNames))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach(array_slice($groupNames, 0, 3) as $gName)
                                <span class="px-1.5 py-0.5 rounded-md text-[10px]
                                             bg-blue-50 dark:bg-blue-900/20
                                             text-blue-600 dark:text-blue-300 font-semibold">
                                    {{ $gName }}
                                </span>
                            @endforeach
                            @if($groupCount > 3)
                                <span class="px-1.5 py-0.5 rounded-md text-[10px]
                                             bg-slate-100 dark:bg-slate-700
                                             text-slate-500 dark:text-slate-400 font-semibold">
                                    +{{ $groupCount - 3 }} lainnya
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Meta --}}
                    <div class="flex flex-wrap items-center gap-2 mt-2
                                text-[10px] text-slate-400">
                        <span class="inline-flex items-center gap-1">
                            <i class="far fa-clock"></i>
                            {{ optional($notif->created_at)->diffForHumans() }}
                        </span>

                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                                     bg-slate-100 dark:bg-slate-700
                                     text-slate-500 dark:text-slate-300 font-semibold">
                            {{ $srcLabel }}
                        </span>

                        @if($priority !== 'normal')
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                                         font-bold
                                         {{ $priority === 'urgent' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                                            : ($priority === 'high' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                            : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300') }}">
                                <span class="w-1 h-1 rounded-full bg-current"></span>
                                {{ ucfirst($priority) }}
                            </span>
                        @endif

                        @if($notif->requires_action && ! $notif->actioned_at)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                                         bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300
                                         font-bold">
                                <i class="fas fa-bolt text-[8px]"></i>
                                Perlu Aksi
                            </span>
                        @endif

                        @if($notif->archived_at)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                                         bg-slate-100 dark:bg-slate-700
                                         text-slate-500 dark:text-slate-400 font-semibold">
                                <i class="fas fa-box-archive text-[8px]"></i>
                                Diarsipkan
                            </span>
                        @endif
                    </div>
                </a>

                {{-- Actions --}}
                <div class="shrink-0 flex items-center gap-0.5 md:opacity-0 md:group-hover:opacity-100 transition">

                    {{-- Star --}}
                    <button type="button"
                            data-ni-star
                            data-id="{{ $notif->id }}"
                            data-starred="{{ $notif->starred_at ? 1 : 0 }}"
                            aria-label="Toggle favorit"
                            class="w-8 h-8 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20
                                   flex items-center justify-center transition"
                            title="{{ $notif->starred_at ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}">
                        <i class="fas fa-star text-xs
                                  {{ $notif->starred_at ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600' }}"></i>
                    </button>

                    {{-- Archive / Unarchive --}}
                    @if($notif->archived_at)
                        <button type="button"
                                data-ni-unarchive
                                data-id="{{ $notif->id }}"
                                aria-label="Kembalikan dari arsip"
                                class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700
                                       flex items-center justify-center transition"
                                title="Kembalikan dari arsip">
                            <i class="fas fa-box-open text-xs text-slate-400 dark:text-slate-500"></i>
                        </button>
                    @else
                        <button type="button"
                                data-ni-archive
                                data-id="{{ $notif->id }}"
                                aria-label="Arsipkan"
                                class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700
                                       flex items-center justify-center transition"
                                title="Arsipkan">
                            <i class="fas fa-box-archive text-xs text-slate-400 dark:text-slate-500"></i>
                        </button>
                    @endif

                    {{-- Delete (hanya kalau sudah dibaca) --}}
                    @if($notif->read_at)
                        <button type="button" data-ni-del data-id="{{ $notif->id }}"
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
        @empty
            {{-- Empty state --}}
            <div class="p-12 md:p-16 text-center">
                @php
                    $isEmptyFilter = request()->filled('q') || request()->filled('source') || request()->filled('range');
                    $emptyMessage = match($tab) {
                        'unread'   => ['🎉', 'Tidak ada notifikasi yang belum dibaca', 'Semua notifikasi sudah kamu baca. Bagus!'],
                        'starred'  => ['⭐', 'Belum ada notifikasi favorit', 'Tandai notifikasi penting sebagai favorit agar mudah dicari.'],
                        'action'   => ['✅', 'Tidak ada yang perlu aksi', 'Semua notifikasi dengan aksi sudah kamu selesaikan.'],
                        'archived' => ['📦', 'Arsip masih kosong', 'Notifikasi yang kamu arsipkan akan muncul di sini.'],
                        default    => $isEmptyFilter
                            ? ['🔍', 'Tidak ada notifikasi yang cocok', 'Coba ubah kata kunci pencarian atau reset filter.']
                            : ['🔔', 'Belum ada notifikasi', 'Notifikasi baru akan muncul di sini saat ada aktivitas.'],
                    };
                @endphp
                <div class="text-5xl mb-4">{{ $emptyMessage[0] }}</div>
                <h3 class="text-base font-bold text-slate-700 dark:text-slate-200 mb-1">
                    {{ $emptyMessage[1] }}
                </h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">
                    {{ $emptyMessage[2] }}
                </p>

                @if($isEmptyFilter)
                    <a href="{{ route('owner.notifications.index', ['tab' => $tab]) }}"
                       class="mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-xl
                              bg-slate-100 dark:bg-slate-700
                              hover:bg-slate-200 dark:hover:bg-slate-600
                              text-slate-700 dark:text-slate-200 text-sm font-semibold transition">
                        <i class="fas fa-times text-xs"></i>
                        Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</div>

{{-- Toast stack --}}
<div id="ni-toast" class="toast-stack"></div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* =========================================================
     | CONFIG
     * ========================================================= */
    const root     = document.getElementById('ni-root');
    if (! root) return;

    const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const BASE     = @json(route('owner.notifications.index'));
    const POLL_URL = root.dataset.pollUrl;
    const UNREAD_URL = root.dataset.unreadUrl;

    let unreadCount = parseInt(root.dataset.count || '0', 10);
    let lastPollId  = null;
    let pollTimer   = null;

    /* =========================================================
     | API HELPER
     * ========================================================= */
    async function api(url, method = 'GET', body = null) {
        const opts = {
            method,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            credentials: 'same-origin',
        };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }

        const res = await fetch(url, opts);
        let data = null;
        try { data = await res.json(); } catch (e) {}
        return { ok: res.ok, data, status: res.status };
    }

    /* =========================================================
     | BADGE SYNC
     * ========================================================= */
    function setUnreadCount(n) {
        unreadCount = Math.max(0, parseInt(n, 10) || 0);

        const el = document.getElementById('ni-count');
        if (el) el.textContent = unreadCount;

        const label = document.getElementById('ni-count-label');
        if (label) {
            label.textContent = unreadCount === 0 ? 'semua sudah dibaca' : 'belum dibaca';
        }

        // Update badge di navbar global (kalau ada)
        if (window.__nbSetCount) window.__nbSetCount(unreadCount);
    }

    /* =========================================================
     | TOAST
     * ========================================================= */
    function toast(msg, type = 'info', duration = 3200) {
        const box = document.getElementById('ni-toast');
        if (! box) return;

        const t = document.createElement('div');
        t.className = `toast ${type}`;
        const icons = {
            success: 'fa-check-circle',
            error:   'fa-exclamation-circle',
            warn:    'fa-exclamation-triangle',
            info:    'fa-info-circle',
        };
        t.innerHTML = `
            <i class="fas ${icons[type] || icons.info} mt-0.5"></i>
            <div class="flex-1">${msg}</div>
        `;
        box.appendChild(t);

        setTimeout(() => {
            t.classList.add('hide');
            setTimeout(() => t.remove(), 300);
        }, duration);
    }

    /* =========================================================
     | TOGGLE STAR
     * ========================================================= */
    document.querySelectorAll('[data-ni-star]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const id = btn.dataset.id;
            const wasStarred = btn.dataset.starred === '1';
            const icon = btn.querySelector('i');

            // Optimistic
            btn.dataset.starred = wasStarred ? '0' : '1';
            icon.classList.toggle('text-amber-400', ! wasStarred);
            icon.classList.toggle('text-slate-300', wasStarred);
            icon.classList.toggle('dark:text-slate-600', wasStarred);

            const { ok, data } = await api(`${BASE}/${id}/toggle-star`, 'POST');

            if (! ok) {
                // Revert
                btn.dataset.starred = wasStarred ? '1' : '0';
                icon.classList.toggle('text-amber-400', wasStarred);
                icon.classList.toggle('text-slate-300', ! wasStarred);
                icon.classList.toggle('dark:text-slate-600', ! wasStarred);
                toast(data?.message || 'Gagal memperbarui favorit.', 'error');
            } else {
                toast(data?.message || (! wasStarred ? 'Ditambahkan ke Favorit' : 'Dihapus dari Favorit'), 'success');
            }
        });
    });

    /* =========================================================
     | ARCHIVE / UNARCHIVE
     * ========================================================= */
    document.querySelectorAll('[data-ni-archive]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            btn.disabled = true;

            const { ok, data } = await api(`${BASE}/${id}/archive`, 'POST');

            if (ok) {
                document.querySelector(`[data-ni-item="${id}"]`)?.remove();
                toast('Notifikasi diarsipkan.', 'success');
            } else {
                btn.disabled = false;
                toast(data?.message || 'Gagal mengarsipkan.', 'error');
            }
        });
    });

    document.querySelectorAll('[data-ni-unarchive]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            btn.disabled = true;

            const { ok, data } = await api(`${BASE}/${id}/unarchive`, 'POST');

            if (ok) {
                document.querySelector(`[data-ni-item="${id}"]`)?.remove();
                toast('Notifikasi dikembalikan dari arsip.', 'success');
            } else {
                btn.disabled = false;
                toast(data?.message || 'Gagal mengembalikan.', 'error');
            }
        });
    });

    /* =========================================================
     | DELETE
     * ========================================================= */
    document.querySelectorAll('[data-ni-del]').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (! confirm('Hapus notifikasi ini?')) return;
            const id = btn.dataset.id;

            const { ok, data } = await api(`${BASE}/${id}`, 'DELETE');

            if (ok && data?.success) {
                document.querySelector(`[data-ni-item="${id}"]`)?.remove();
                toast('Notifikasi dihapus.', 'success');
            } else {
                toast(data?.message || 'Gagal menghapus.', 'error');
            }
        });
    });

    document.querySelectorAll('[data-ni-blocked-delete]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toast('Buka notifikasi dulu sebelum menghapus.', 'warn');
        });
    });

    /* =========================================================
     | OPEN NOTIFICATION — optimistic read
     * ========================================================= */
    document.addEventListener('click', (e) => {
        const a = e.target.closest('[data-ni-open]');
        if (! a) return;
        if (a.dataset.read === '1') return;

        a.dataset.read = '1';
        setUnreadCount(unreadCount - 1);

        // Optimistic UI update
        const row = a.closest('[data-ni-item]');
        row?.classList.remove('is-unread');
        row?.querySelector('.bg-violet-500.rounded-full')?.remove();

        // Fire & forget
        api(`${BASE}/${a.dataset.id}/read`, 'POST').catch(() => {});
    });

    /* =========================================================
     | READ ALL
     * ========================================================= */
    document.getElementById('ni-readall')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        const { ok } = await api(@json(route('owner.notifications.read-all')), 'POST');

        if (ok) {
            setUnreadCount(0);
            document.querySelectorAll('[data-ni-open]').forEach(a => a.dataset.read = '1');
            document.querySelectorAll('.notif-item.is-unread').forEach(el => el.classList.remove('is-unread'));
            document.querySelectorAll('.bg-violet-500.rounded-full').forEach(el => el.remove());
            toast('Semua notifikasi ditandai dibaca.', 'success');
        } else {
            toast('Gagal menandai semua dibaca.', 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-double"></i> Tandai semua dibaca';
    });

    /* =========================================================
     | CLEAR READ
     * ========================================================= */
    document.getElementById('ni-clear')?.addEventListener('click', async (e) => {
        if (! confirm('Hapus semua notifikasi yang sudah dibaca?')) return;

        const btn = e.currentTarget;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membersihkan...';

        const { ok, data } = await api(@json(route('owner.notifications.destroy-all')), 'DELETE');

        if (ok && data?.success) {
            toast(data.message || 'Dibersihkan.', 'success');
            setTimeout(() => window.location.reload(), 700);
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-broom"></i> Bersihkan yang dibaca';
            toast(data?.message || 'Gagal membersihkan.', 'error');
        }
    });

    /* =========================================================
     | POLLING — sync unread + toast kalau ada notifikasi baru
     * ========================================================= */
    async function poll() {
        if (document.hidden) return;

        try {
            const { ok, data } = await api(POLL_URL);

            if (! ok || ! data) return;

            const newCount = parseInt(data.unread_count || 0, 10);

            // Deteksi notifikasi baru
            if (data.has_new && data.items?.length > 0) {
                const newest = data.items[0];
                toast(
                    `Notifikasi baru: <strong>${newest.title || 'Notifikasi baru'}</strong>`,
                    'info',
                    5000
                );
            }

            setUnreadCount(newCount);
            lastPollId = data.latest_id || lastPollId;

            // Show live indicator
            document.getElementById('ni-sync-indicator')?.classList.remove('hidden');

        } catch (err) {
            console.debug('Polling error:', err);
        }
    }

    // Polling tiap 30 detik
    pollTimer = setInterval(poll, 30000);

    // Resume polling saat tab kembali aktif
    document.addEventListener('visibilitychange', () => {
        if (! document.hidden) poll();
    });

    // First poll setelah 3 detik
    setTimeout(poll, 3000);

    // Cleanup saat halaman ditinggalkan
    window.addEventListener('beforeunload', () => {
        if (pollTimer) clearInterval(pollTimer);
    });

    /* =========================================================
     | KEYBOARD SHORTCUTS
     * ========================================================= */
    document.addEventListener('keydown', (e) => {
        // Ctrl+K → fokus ke search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('#ni-toolbar input[name="q"]')?.focus();
        }
    });

})();
</script>
@endpush