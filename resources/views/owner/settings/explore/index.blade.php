{{-- resources/views/owner/settings/explore/index.blade.php --}}
@extends('layouts.roles.owner')

@section('title', 'Jelajah Akun – Owner UNICODE')

@php
    /** @var \Illuminate\Support\Collection|\App\Models\OwnerExploreFolder[] $folders */
    $totalFolders  = $folders->count();
    $totalAccounts = $folders->sum(fn ($f) => $f->cards->count());
    $totalPoints   = $folders->sum(fn ($f) => $f->cards->sum(fn ($c) => (int) ($c->user->total_points ?? 0)));
    $onlineCount   = $folders->sum(fn ($f) => $f->cards->filter(fn ($c) => $c->user && $c->user->is_online)->count());
@endphp

@section('content')
<div
    class="p-4 md:p-6 space-y-6"
    x-data="exploreDashboard({
        folders: {{ Js::from(
            $folders->map(fn ($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'cards' => $f->cards->map(fn ($c) => [
                    'user_id' => $c->user_id,
                    'name' => optional($c->user)->name,
                    'nim' => optional($c->user)->nim,
                ])->values(),
            ])->values()
        ) }}
    })"
    x-cloak
>

    {{-- ═══════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold uppercase tracking-wide">
                <i class="fas fa-user-shield"></i> Owner Control
            </span>
            <h1 class="mt-3 text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                🔎 Jelajah Akun
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-3xl">
                Kelola daftar akun dalam folder. Card otomatis menampilkan data terbaru dari tabel
                <code class="px-1 bg-slate-100 dark:bg-slate-800 rounded text-xs">users</code>.
            </p>
        </div>

        {{-- Live stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 lg:gap-3">
            @php
                $stats = [
                    ['label' => 'Folder',   'value' => $totalFolders,  'icon' => 'fa-folder',       'tone' => 'indigo'],
                    ['label' => 'Akun',     'value' => $totalAccounts, 'icon' => 'fa-users',        'tone' => 'emerald'],
                    ['label' => 'Online',   'value' => $onlineCount,   'icon' => 'fa-circle',       'tone' => 'cyan'],
                    ['label' => 'Poin',     'value' => number_format($totalPoints), 'icon' => 'fa-star', 'tone' => 'amber'],
                ];
            @endphp
            @foreach($stats as $s)
                <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-2 shadow-sm">
                    <div class="flex items-center gap-2 text-[11px] uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wide">
                        <i class="fas {{ $s['icon'] }} text-{{ $s['tone'] }}-500"></i>
                        {{ $s['label'] }}
                    </div>
                    <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $s['value'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         FLASH MESSAGES
    ═══════════════════════════════════════════════════════ --}}
    @foreach(['success' => ['check-circle', 'emerald'], 'error' => ['times-circle', 'rose'], 'warning' => ['exclamation-triangle', 'amber']] as $key => $meta)
        @if(session($key))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 6000)"
                x-transition
                class="rounded-2xl border border-{{ $meta[1] }}-200 bg-{{ $meta[1] }}-50 dark:bg-{{ $meta[1] }}-900/20 dark:border-{{ $meta[1] }}-800 p-4 text-{{ $meta[1] }}-800 dark:text-{{ $meta[1] }}-300 text-sm flex items-start justify-between gap-3"
                role="status"
            >
                <div class="flex items-start gap-2">
                    <i class="fas fa-{{ $meta[0] }} mt-0.5"></i>
                    <span>{{ session($key) }}</span>
                </div>
                <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    @endforeach

    {{-- ═══════════════════════════════════════════════════════
         SECURITY NOTICE
    ═══════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 p-5">
        <div class="flex gap-4">
            <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-300">
                <i class="fas fa-shield-alt text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-amber-800 dark:text-amber-300">Mode Khusus Owner</h3>
                <p class="mt-1 text-sm leading-6 text-amber-700 dark:text-amber-400">
                    Password & PIN akun target <b>tidak pernah digunakan</b>.
                    Hanya password + PIN Owner yang diverifikasi.
                    Setiap aktivitas jelajah tercatat pada audit log.
                </p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CREATE FOLDER FORM
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                <i class="fas fa-folder-plus text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Buat Folder Penjelajahan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelompokkan akun dengan folder. Bisa dibuat banyak folder.
                </p>

                <form action="{{ route('owner.settings.explore.folders.store') }}"
                      method="POST"
                      class="mt-5 grid grid-cols-1 md:grid-cols-12 gap-3">
                    @csrf

                    <div class="md:col-span-4">
                        <label for="folder-name" class="sr-only">Nama Folder</label>
                        <input
                            id="folder-name"
                            type="text" name="name"
                            value="{{ old('name') }}"
                            required maxlength="100"
                            autocomplete="off"
                            placeholder="Contoh: Pengurus 2025/2026"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="md:col-span-4">
                        <label for="folder-description" class="sr-only">Deskripsi</label>
                        <input
                            id="folder-description"
                            type="text" name="description"
                            value="{{ old('description') }}"
                            maxlength="255" autocomplete="off"
                            placeholder="Deskripsi (opsional)"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="folder-color" class="sr-only">Warna</label>
                        <select id="folder-color" name="color"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['indigo' => 'Indigo', 'amber' => 'Amber', 'emerald' => 'Emerald', 'rose' => 'Rose', 'cyan' => 'Cyan', 'purple' => 'Purple'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('color') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold transition focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i class="fas fa-plus"></i> Buat
                        </button>
                    </div>
                </form>

                @error('name')
                    <p class="mt-2 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SEARCH & FILTER BAR
    ═══════════════════════════════════════════════════════ --}}
    @if($totalFolders > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input
                    type="search"
                    x-model.debounce.200ms="search"
                    placeholder="Cari folder, nama, atau NIM..."
                    aria-label="Cari"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center gap-2">
                <select x-model="sort"
                        aria-label="Urutkan"
                        class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-2.5">
                    <option value="recent">Terbaru</option>
                    <option value="name">Nama A-Z</option>
                    <option value="points">Poin tertinggi</option>
                </select>

                <button type="button"
                        @click="expandAll()"
                        class="px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-expand-alt"></i>
                    <span class="hidden sm:inline ml-1">Buka Semua</span>
                </button>
                <button type="button"
                        @click="collapseAll()"
                        class="px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-compress-alt"></i>
                    <span class="hidden sm:inline ml-1">Tutup Semua</span>
                </button>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         FOLDER LIST
    ═══════════════════════════════════════════════════════ --}}
    <div class="space-y-5">
        @forelse($folders as $folder)
            @php
                $color = $folder->color_classes ?? [
                    'bg'   => 'bg-indigo-100 dark:bg-indigo-900/30',
                    'text' => 'text-indigo-600 dark:text-indigo-300',
                ];
                $folderSearchKey = strtolower($folder->name . ' ' . ($folder->description ?? ''));
            @endphp

            <section
                id="folder-{{ $folder->id }}"
                x-data="{ open: true }"
                x-show="matchesFolder(@js($folder->id))"
                x-transition.opacity
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden"
                aria-labelledby="folder-title-{{ $folder->id }}"
            >
                {{-- ─── FOLDER HEADER ────────────────────────── --}}
                <div class="p-5 md:p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-4 text-left flex-1 min-w-0 group">
                            <div class="w-12 h-12 shrink-0 rounded-xl {{ $color['bg'] }} flex items-center justify-center {{ $color['text'] }}">
                                <i class="fas fa-folder text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h2 id="folder-title-{{ $folder->id }}"
                                    class="text-lg font-bold text-gray-900 dark:text-white truncate flex items-center gap-2">
                                    {{ $folder->name }}
                                    <i class="fas text-xs text-gray-400 transition"
                                       :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    {{ $folder->cards->count() }} akun
                                    @if($folder->description)
                                        · {{ $folder->description }}
                                    @endif
                                </p>
                            </div>
                        </button>

                        <div class="flex flex-wrap gap-2">
                            {{-- Edit --}}
                            <button type="button"
                                    onclick="document.getElementById('edit-folder-{{ $folder->id }}').classList.toggle('hidden')"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition focus:ring-2 focus:ring-indigo-500">
                                <i class="fas fa-pen"></i> Edit
                            </button>

                            {{-- Delete --}}
                            <form action="{{ route('owner.settings.explore.folders.destroy', $folder) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus folder &quot;{{ $folder->name }}&quot;? Semua card di dalamnya juga akan terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-300 text-sm font-semibold hover:bg-rose-50 dark:hover:bg-rose-900/20 transition focus:ring-2 focus:ring-rose-500">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit Form (Hidden) --}}
                    <div id="edit-folder-{{ $folder->id }}"
                         class="hidden mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <form action="{{ route('owner.settings.explore.folders.update', $folder) }}"
                              method="POST"
                              class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            @csrf
                            @method('PUT')

                            <div class="md:col-span-4">
                                <input type="text" name="name"
                                       value="{{ $folder->name }}"
                                       required maxlength="100"
                                       aria-label="Nama folder"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="md:col-span-4">
                                <input type="text" name="description"
                                       value="{{ $folder->description }}"
                                       maxlength="255"
                                       placeholder="Deskripsi (opsional)"
                                       aria-label="Deskripsi"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="md:col-span-2">
                                <select name="color" aria-label="Warna folder"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm">
                                    @foreach(['indigo' => 'Indigo','amber' => 'Amber','emerald' => 'Emerald','rose' => 'Rose','cyan' => 'Cyan','purple' => 'Purple'] as $key => $label)
                                        <option value="{{ $key }}" @selected($folder->color === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition focus:ring-2 focus:ring-emerald-500">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Add Card Form --}}
                    <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <form action="{{ route('owner.settings.explore.cards.store', $folder) }}"
                              method="POST"
                              class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="text" name="nim" required maxlength="50"
                                   inputmode="numeric"
                                   autocomplete="off"
                                   placeholder="Masukkan NIM untuk menambah akun..."
                                   aria-label="NIM akun"
                                   class="w-full sm:flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold text-sm transition focus:ring-2 focus:ring-gray-500">
                                <i class="fas fa-search"></i> Tambah Akun
                            </button>
                        </form>
                        @error('nim')
                            <p class="mt-2 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ─── ACCOUNT CARDS ────────────────────────── --}}
                <div class="p-5 md:p-6" x-show="open" x-collapse>
                    @if($folder->cards->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($folder->cards as $card)
                                @php
                                    $user = $card->user;

                                    if (! $user) {
                                        // render "deleted" card
                                    }
                                @endphp

                                @if(! $user)
                                    <div class="rounded-2xl border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-5 text-sm text-red-700 dark:text-red-300">
                                        <i class="fas fa-user-slash mr-2"></i>Akun sudah dihapus permanen.
                                    </div>
                                @else
                                    @php
                                        $levelName = $user->level->name ?? 'Anggota';
                                        $levelColor = match ((int) $user->level_id) {
                                            1 => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                            2 => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                            3 => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                            4 => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
                                            5 => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                        };

                                        $rawAvatar = $user->avatar_url ?? null;
                                        if ($rawAvatar) {
                                            $avatarUrl = \Illuminate\Support\Str::startsWith($rawAvatar, ['http://', 'https://'])
                                                ? $rawAvatar
                                                : \Illuminate\Support\Facades\Storage::url($rawAvatar);
                                        } else {
                                            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name)
                                                . '&background=6366f1&color=fff&size=128';
                                        }

                                        $fallbackAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name)
                                            . '&background=6366f1&color=fff&size=128';

                                        $points = (int) ($user->total_points ?? 0);
                                        $isActive = (bool) ($user->is_active ?? false);
                                        $isOnline = (bool) ($user->is_online ?? false);

                                        $lastSeenText = null;
                                        if ($user->last_seen_at) {
                                            $lastSeenText = \Illuminate\Support\Carbon::parse($user->last_seen_at)->diffForHumans();
                                        }

                                        $searchHaystack = strtolower(
                                            ($user->name ?? '') . ' ' .
                                            ($user->nim ?? '') . ' ' .
                                            ($user->username ?? '') . ' ' .
                                            ($user->email ?? '')
                                        );
                                    @endphp

                                    <article
                                        x-show="matchesCard(@js($folder->id), @js($searchHaystack), {{ $points }})"
                                        x-transition.opacity
                                        class="group relative rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-lg transition duration-200 bg-white dark:bg-gray-800"
                                    >
                                        {{-- Online indicator --}}
                                        @if($isOnline)
                                            <span class="absolute top-4 left-4 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                ONLINE
                                            </span>
                                        @endif

                                        {{-- Delete card button --}}
                                        <form action="{{ route('owner.settings.explore.cards.destroy', $card) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus {{ $user->name }} dari folder ini?');"
                                              class="absolute top-3 right-3 z-10 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-7 h-7 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-md focus:ring-2 focus:ring-rose-400"
                                                    title="Hapus dari folder">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('owner.settings.explore.verify', ['user_id' => $user->id]) }}"
                                           class="block focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-xl"
                                           aria-label="Jelajahi akun {{ $user->name }}">

                                            <div class="flex items-center gap-3">
                                                <img
                                                    src="{{ $avatarUrl }}"
                                                    alt="Avatar {{ $user->name }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                    class="w-14 h-14 rounded-2xl object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                                    onerror="this.onerror=null;this.src='{{ $fallbackAvatar }}';">

                                                <div class="min-w-0 flex-1">
                                                    <h3 class="font-bold text-gray-900 dark:text-white truncate">
                                                        {{ $user->name }}
                                                    </h3>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                                        NIM: {{ $user->nim ?? '-' }}
                                                    </p>
                                                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $levelColor }}">
                                                        {{ strtoupper($levelName) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3 mt-4">
                                                <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Poin</p>
                                                    <p class="mt-1 font-bold text-gray-900 dark:text-white">
                                                        {{ number_format($points) }}
                                                    </p>
                                                </div>
                                                <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Status</p>
                                                    <p class="mt-1 font-bold {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                                <span class="text-xs text-gray-400 dark:text-gray-500"
                                                      title="{{ $lastSeenText ? 'Terakhir dilihat: ' . $lastSeenText : 'Belum pernah login' }}">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    {{ $card->explore_count ?? 0 }}× dijelajahi
                                                </span>
                                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition">
                                                    Jelajahi <i class="fas fa-arrow-right ml-1"></i>
                                                </span>
                                            </div>
                                        </a>
                                    </article>
                                @endif
                            @endforeach
                        </div>

                        {{-- Empty search result inside a folder --}}
                        <div
                            x-show="noVisibleCardIn(@js($folder->id))"
                            x-transition.opacity
                            class="py-8 text-center text-gray-400 dark:text-gray-500"
                        >
                            <i class="fas fa-filter text-2xl mb-2 block opacity-50"></i>
                            <p class="text-sm">Tidak ada akun yang cocok dengan pencarian di folder ini.</p>
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-users text-3xl mb-3 block opacity-40"></i>
                            <p class="text-sm">Belum ada akun di folder ini. Masukkan NIM di atas.</p>
                        </div>
                    @endif
                </div>
            </section>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-500">
                    <i class="fas fa-folder-open text-3xl"></i>
                </div>
                <h3 class="mt-5 text-lg font-bold text-gray-900 dark:text-white">Belum ada folder</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                    Buat folder pertama di atas untuk mulai mengelompokkan akun.
                </p>
            </div>
        @endforelse

        {{-- Empty search state (semua folder) --}}
        @if($totalFolders > 0)
            <div
                x-show="search.trim() !== '' && visibleFolderCount === 0"
                x-transition.opacity
                class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center"
            >
                <i class="fas fa-search-minus text-3xl text-gray-300 dark:text-gray-600"></i>
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                    Tidak ada folder atau akun yang cocok dengan kata kunci
                    "<span class="font-semibold" x-text="search"></span>".
                </p>
            </div>
        @endif
    </div>

    {{-- FOOTER NOTE --}}
    <div class="text-center text-xs text-gray-400 dark:text-gray-500 pt-2">
        <i class="fas fa-info-circle mr-1"></i>
        Hanya akun non-Owner yang dapat dijelajahi. Password target tidak pernah digunakan.
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('exploreDashboard', ({ folders = [] } = {}) => ({
        search: '',
        sort: 'recent',
        folders,

        init() {
            // Watch changes and reorder DOM if needed by sort
            this.$watch('sort', () => this.applySort());
        },

        matchesFolder(folderId) {
            const q = this.search.trim().toLowerCase();
            if (! q) return true;

            const f = this.folders.find(x => x.id === folderId);
            if (! f) return false;

            if ((f.name || '').toLowerCase().includes(q)) return true;

            return (f.cards || []).some(c =>
                (c.name || '').toLowerCase().includes(q) ||
                (c.nim  || '').toLowerCase().includes(q)
            );
        },

        matchesCard(folderId, haystack, points = 0) {
            const q = this.search.trim().toLowerCase();
            if (! q) return true;

            // If folder name matches, show all cards in folder
            const f = this.folders.find(x => x.id === folderId);
            if (f && (f.name || '').toLowerCase().includes(q)) return true;

            return (haystack || '').toLowerCase().includes(q);
        },

        noVisibleCardIn(folderId) {
            const f = this.folders.find(x => x.id === folderId);
            if (! f) return false;
            return (f.cards || []).length > 0
                && ! this.matchesFolder(folderId)
                && this.search.trim() !== '';
        },

        get visibleFolderCount() {
            return this.folders.filter(f => this.matchesFolder(f.id)).length;
        },

        expandAll() {
            document.querySelectorAll('[id^="folder-"]').forEach(el => {
                const data = Alpine.$data(el);
                if (data) data.open = true;
            });
        },

        collapseAll() {
            document.querySelectorAll('[id^="folder-"]').forEach(el => {
                const data = Alpine.$data(el);
                if (data) data.open = false;
            });
        },

        applySort() {
            // Optional: hanya relevan jika Anda ingin reorder. 
            // Karena Blade sudah render sesuai urutan server,
            // fitur sort saat ini opsional (murni UI).
        },
    }));
});
</script>
@endpush