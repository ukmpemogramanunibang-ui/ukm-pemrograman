@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', 'Pusat Notifikasi – UNICODE')
@section('page-title', 'Pusat Notifikasi')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pusat Notifikasi</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua aktivitas dan komunikasi di UNICODE.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('owner.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm transition shadow-md shadow-blue-500/20">
                        <i class="fas fa-check-double mr-2"></i> Tandai semua dibaca
                    </button>
                </form>
            @endif
            <a href="{{ route('owner.notifications.archived') }}" class="px-4 py-2 rounded-lg bg-gray-600 hover:bg-gray-700 text-white text-sm transition">
                <i class="fas fa-archive mr-2"></i> Arsip
            </a>
            <form action="{{ route('owner.notifications.destroy-all') }}" method="POST" onsubmit="return confirm('Hapus semua notifikasi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm transition shadow-md shadow-red-500/20">
                    <i class="fas fa-trash mr-2"></i> Hapus semua
                </button>
            </form>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Notifikasi</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalNotifications }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400">Belum Dibaca</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $unreadCount }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400">Pesan Baru</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $chatUnreadCount }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400">Mendesak</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $urgentCount }}</p>
        </div>
    </div>

    {{-- Pencarian & Filter --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('owner.notifications.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs text-gray-500 dark:text-gray-400">🔍 Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, pesan, pengirim..." class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Status</label>
                <select name="status" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm">
                    <option value="">Semua</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Tipe</label>
                <select name="type" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Prioritas</label>
                <select name="priority" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm">
                    <option value="">Semua</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <input type="checkbox" name="starred" value="1" {{ request('starred') ? 'checked' : '' }} class="rounded border-gray-300">
                    Disimpan
                </label>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                <a href="{{ route('owner.notifications.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar Notifikasi --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $isUrgent = $notif->priority === 'urgent';
                $colorClass = $notif->color_class;
            @endphp

            <div class="relative flex items-start gap-4 p-5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition {{ $isUnread ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                {{-- Indikator Belum Dibaca --}}
                @if($isUnread)
                    <span class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-r"></span>
                @endif
                @if($isUrgent)
                    <span class="absolute left-1 top-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                @endif

                {{-- Icon --}}
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $colorClass }}">
                    <i class="{{ $notif->icon }} text-xl"></i>
                </div>

                {{-- Konten --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold text-gray-800 dark:text-white text-sm md:text-base">{{ $notif->title }}</h3>
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wide bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    {{ ucfirst(str_replace('_', ' ', $notif->type ?? 'Sistem')) }}
                                </span>
                                @if($notif->priority !== 'normal')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $notif->priority_badge }}">{{ ucfirst($notif->priority) }}</span>
                                @endif
                                @if($notif->is_starred)
                                    <span class="text-yellow-500"><i class="fas fa-star text-xs"></i></span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-2">{{ $notif->message }}</p>
                            @if($notif->sender)
                                <div class="mt-1 flex items-center gap-2">
                                    <img src="{{ $notif->sender->avatar_url ? asset('storage/'.$notif->sender->avatar_url) : asset('images/default-avatar.png') }}" class="w-5 h-5 rounded-full object-cover">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notif->sender->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                            <div class="flex items-center gap-1">
                                @if(!$isUnread)
                                    <span class="text-[10px] text-gray-400"><i class="fas fa-check-circle"></i></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Aksi cepat --}}
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <a href="{{ route('owner.notifications.show', $notif->id) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat detail</a>
                        @if(!$notif->is_archived)
                            <form action="{{ route('owner.notifications.archive', $notif->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">Arsipkan</button>
                            </form>
                        @endif
                        <form action="{{ route('owner.notifications.toggle-star', $notif->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-yellow-500 {{ $notif->is_starred ? 'text-yellow-500' : '' }}">
                                <i class="fas {{ $notif->is_starred ? 'fa-star' : 'fa-star' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('owner.notifications.destroy', $notif->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-20 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-bell-slash text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-gray-600 dark:text-gray-300">Belum ada notifikasi</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Notifikasi aktivitas sistem akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection