@extends('layouts.roles.ketua')

@section('title', 'Notification Center – UNICODE')
@section('page-title', 'Notification Center')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], string $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

<div class="space-y-6 max-w-5xl">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
                Pusat Notifikasi
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
                Notifikasi
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Semua notifikasi & aktivitas yang masuk ke akun Anda.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" id="mark-all-read"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                           bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold transition">
                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
            </button>
            <button type="button" id="delete-all"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                           bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40
                           text-red-600 dark:text-red-400 text-sm font-semibold transition">
                <i class="fas fa-trash"></i> Hapus Semua
            </button>
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
            $statCards = [
                ['label' => 'Total Notifikasi', 'value' => $stats['total'],  'icon' => 'fa-bell',      'color' => 'blue'],
                ['label' => 'Belum Dibaca',    'value' => $stats['unread'], 'icon' => 'fa-envelope',  'color' => 'purple'],
                ['label' => 'Hari Ini',        'value' => $stats['today'],  'icon' => 'fa-calendar',  'color' => 'emerald'],
            ];
        @endphp
        @foreach ($statCards as $card)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-bold">{{ $card['label'] }}</p>
                        <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($card['value']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-{{ $card['color'] }}-500/15 text-{{ $card['color'] }}-500 flex items-center justify-center">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- FILTER --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mr-2">Filter:</span>

            @php
                $currentFilter = request('filter', 'all');
                $filterButtons = [
                    'all'    => ['label' => 'Semua',    'icon' => 'fa-inbox'],
                    'unread' => ['label' => 'Belum Dibaca', 'icon' => 'fa-envelope'],
                    'read'   => ['label' => 'Sudah Dibaca', 'icon' => 'fa-envelope-open'],
                ];
            @endphp

            @foreach ($filterButtons as $key => $fb)
                <a href="{{ request()->fullUrlWithQuery(['filter' => $key === 'all' ? null : $key]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition
                          {{ ($currentFilter === $key || ($key === 'all' && !request('filter')))
                            ? 'bg-purple-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fas {{ $fb['icon'] }}"></i>
                    {{ $fb['label'] }}
                </a>
            @endforeach

            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

            @foreach ($categories as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['category' => $key === 'all' ? null : $key]) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition
                          {{ request('category', 'all') === $key
                            ? 'bg-purple-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- LIST --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        @forelse ($notifications as $notif)
            @php
                $color = $notif->colorClass();
                $icon  = $notif->iconOrDefault();
            @endphp
            <div class="notif-row flex items-start gap-4 p-4 border-b border-gray-100 dark:border-gray-700
                        last:border-0 transition
                        {{ $notif->isUnread() ? 'bg-purple-50/40 dark:bg-purple-900/10' : '' }}"
                 data-id="{{ $notif->id }}">

                <div class="w-11 h-11 rounded-xl bg-{{ $color }}-500/15 text-{{ $color }}-500
                            flex items-center justify-center flex-shrink-0 relative">
                    <i class="fas {{ $icon }}"></i>
                    @if ($notif->isUnread())
                        <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full
                                     bg-purple-500 border-2 border-white dark:border-gray-800 animate-pulse"></span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-sm {{ $notif->isUnread() ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $notif->title }}
                            </p>
                            @if ($notif->message)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    {{ $notif->message }}
                                </p>
                            @endif
                        </div>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap flex-shrink-0">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase
                                     bg-{{ $color }}-500/15 text-{{ $color }}-600 dark:text-{{ $color }}-400">
                            {{ $notif->category }}
                        </span>

                        @if ($notif->action_url)
                            <a href="{{ $safeRoute('ketua.notifications.read', ['notification' => $notif->id]) }}"
                               data-mark-read="{{ $notif->id }}"
                               class="inline-flex items-center gap-1 text-[11px] font-semibold
                                      text-purple-600 dark:text-purple-400 hover:text-purple-700">
                                {{ $notif->action_label ?? 'Lihat Detail' }}
                                <i class="fas fa-arrow-right text-[9px]"></i>
                            </a>
                        @endif

                        @if ($notif->isUnread())
                            <button type="button"
                                    data-action="read"
                                    data-id="{{ $notif->id }}"
                                    class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                <i class="fas fa-check"></i> Tandai dibaca
                            </button>
                        @endif

                        <button type="button"
                                data-action="delete"
                                data-id="{{ $notif->id }}"
                                class="text-[11px] font-semibold text-red-500 hover:underline ml-auto">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <i class="fas fa-inbox text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                    Belum ada notifikasi
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Semua notifikasi baru akan muncul di sini.
                </p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($notifications->hasPages())
        <div class="flex justify-center">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@push('scripts')
<script>
(function() {
    const csrf   = '{{ csrf_token() }}';
    const urls = {
        readAll:  '{{ route('ketua.notifications.mark-all-read') }}',
        delAll:   '{{ route('ketua.notifications.destroy-all') }}',
        readTpl:  '{{ route('ketua.notifications.read', ['notification' => '__ID__']) }}',
        delTpl:   '{{ route('ketua.notifications.destroy', ['notification' => '__ID__']) }}',
        unread:   '{{ route('ketua.notifications.unread-count') }}',
    };

    function toast(msg, type = 'info') {
        const colors = { success: 'bg-emerald-500', info: 'bg-blue-500', danger: 'bg-red-500' };
        const el = document.createElement('div');
        el.className = `fixed bottom-6 right-6 z-[9999] ${colors[type] || 'bg-gray-800'}
                        text-white px-4 py-2.5 rounded-xl shadow-2xl text-sm font-semibold
                        transform translate-y-4 opacity-0 transition-all duration-300`;
        el.textContent = msg;
        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-y-4', 'opacity-0'));
        setTimeout(() => {
            el.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => el.remove(), 300);
        }, 2000);
    }

    // Mark all read
    document.getElementById('mark-all-read')?.addEventListener('click', async function() {
        this.disabled = true;
        try {
            const r = await fetch(urls.readAll, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
            });
            if (r.ok) {
                toast('Semua notifikasi ditandai dibaca', 'success');
                setTimeout(() => location.reload(), 600);
            }
        } catch (e) { toast('Gagal', 'danger'); }
    });

    // Delete all
    document.getElementById('delete-all')?.addEventListener('click', async function() {
        if (!confirm('Hapus semua notifikasi?')) return;
        this.disabled = true;
        try {
            const r = await fetch(urls.delAll, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
            });
            if (r.ok) {
                toast('Semua notifikasi dihapus', 'success');
                setTimeout(() => location.reload(), 600);
            }
        } catch (e) { toast('Gagal', 'danger'); }
    });

    // Row action
    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id     = this.dataset.id;
            const action = this.dataset.action;

            if (action === 'delete') {
                if (!confirm('Hapus notifikasi ini?')) return;
                const url = urls.delTpl.replace('__ID__', id);
                const r = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (r.ok) {
                    document.querySelector(`.notif-row[data-id="${id}"]`)?.remove();
                    toast('Notifikasi dihapus', 'success');
                }
            } else if (action === 'read') {
                const url = urls.readTpl.replace('__ID__', id);
                const r = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (r.ok) {
                    const row = document.querySelector(`.notif-row[data-id="${id}"]`);
                    if (row) {
                        row.classList.remove('bg-purple-50/40', 'dark:bg-purple-900/10');
                        row.querySelector('.animate-pulse')?.remove();
                        this.remove();
                    }
                    toast('Ditandai dibaca', 'success');
                }
            }
        });
    });
})();
</script>
@endpush
@endsection