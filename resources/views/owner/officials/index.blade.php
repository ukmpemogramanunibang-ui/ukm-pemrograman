@extends('layouts.roles.owner')

@section('title', 'Daftar Ketua & Pengurus – Owner UNICODE')
@section('page-title', 'Daftar Ketua & Pengurus')

@section('content')
@php
    /* ═══════════════════════════════════════════════════════
       HELPER — Defensive route resolver
       ═══════════════════════════════════════════════════════ */
    $safeRoute = fn (string $name, $params = [], string $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;

    /* ═══════════════════════════════════════════════════════
       STATS — Defensive
       ═══════════════════════════════════════════════════════ */
    $totalOfficial   = $officials->total();
    $totalKetua      = $officials->getCollection()->where('level_id', 3)->count();
    $totalPengurus   = $officials->getCollection()->where('level_id', 4)->count();
    $totalUniversitas = $officials->getCollection()
        ->pluck('university.name')->filter()->unique()->count();
    $totalJurusan    = $officials->getCollection()
        ->pluck('major.name')->filter()->unique()->count();
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- ═══ HEADER ═══ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-tie text-cyan-500"></i>
                    Daftar Ketua & Pengurus
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Menampilkan akun dengan role <strong>Ketua</strong> dan <strong>Pengurus</strong>.
                </p>
            </div>
            <a href="{{ $safeRoute('owner.officials.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600
                      text-white font-semibold rounded-lg hover:shadow-lg transition">
                <i class="fas fa-plus"></i> Tambah Pengurus
            </a>
        </div>

        {{-- ═══ STATS ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @php
                $statCards = [
                    ['icon' => 'fa-user-tie',       'label' => 'Ketua',       'value' => $totalKetua,
                     'color' => 'purple', 'from' => 'from-purple-500', 'to' => 'to-fuchsia-500'],
                    ['icon' => 'fa-users',          'label' => 'Pengurus',    'value' => $totalPengurus,
                     'color' => 'green',  'from' => 'from-emerald-500', 'to' => 'to-teal-500'],
                    ['icon' => 'fa-university',     'label' => 'Universitas', 'value' => $totalUniversitas,
                     'color' => 'blue',   'from' => 'from-blue-500', 'to' => 'to-indigo-500'],
                    ['icon' => 'fa-graduation-cap', 'label' => 'Jurusan',     'value' => $totalJurusan,
                     'color' => 'indigo', 'from' => 'from-indigo-500', 'to' => 'to-violet-500'],
                ];
            @endphp

            @foreach($statCards as $s)
            <div class="group relative bg-gradient-to-br {{ $s['from'] }} {{ $s['to'] }}
                        rounded-xl p-5 text-white shadow-lg hover:shadow-2xl
                        hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 opacity-10"
                     style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                <div class="relative flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm
                                flex items-center justify-center text-2xl shrink-0">
                        <i class="fas {{ $s['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-wide opacity-80 font-bold">{{ $s['label'] }}</p>
                        <p class="text-3xl font-black tabular-nums leading-tight">{{ $s['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ═══ FILTER BAR ═══ --}}
        <form method="GET" action="{{ $safeRoute('owner.officials.index') }}"
              class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, NIM, atau email..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-900/50 text-sm
                              focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition">
            </div>

            <select name="level"
                    class="px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-900/50 text-sm font-medium
                           focus:ring-2 focus:ring-cyan-500 outline-none transition">
                <option value="">Semua Role</option>
                <option value="3" {{ request('level') === '3' ? 'selected' : '' }}>Ketua</option>
                <option value="4" {{ request('level') === '4' ? 'selected' : '' }}>Pengurus</option>
            </select>

            <button type="submit"
                    class="px-5 py-2.5 rounded-lg bg-cyan-600 text-white text-sm font-bold
                           hover:bg-cyan-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['q', 'level']))
                <a href="{{ $safeRoute('owner.officials.index') }}"
                   class="px-4 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700
                          text-gray-600 dark:text-gray-300 text-sm font-bold
                          hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>

        {{-- ═══ TABLE ═══ --}}
        <div class="overflow-x-auto -mx-6 sm:mx-0">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300 min-w-[900px]">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">NIM</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">Kontak</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider">Universitas</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider text-center">Poin</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($officials as $official)
                        @php
                            $roleColors = [
                                3 => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-800 dark:text-purple-300', 'label' => 'Ketua'],
                                4 => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-800 dark:text-emerald-300', 'label' => 'Pengurus'],
                            ];
                            $role = $roleColors[$official->level_id] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $official->level->name ?? 'Unknown'];

                            $jabatanUtama = $official->jabatans->where('pivot.is_primary', true)->first()
                                ?? $official->jabatans->first();

                            $initials = strtoupper(substr($official->name ?? '?', 0, 2));
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <td class="px-4 py-3 text-gray-400 tabular-nums">
                                {{ $officials->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full shrink-0
                                                bg-gradient-to-br from-cyan-400 to-blue-500
                                                flex items-center justify-center
                                                text-white font-bold text-xs shadow-md">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 dark:text-white truncate max-w-[180px]">
                                            {{ $official->name ?? '-' }}
                                        </p>
                                        <span class="inline-flex items-center gap-1 text-xs
                                            {{ $official->is_active ? 'text-emerald-600' : 'text-red-500' }}">
                                            <span class="w-1.5 h-1.5 rounded-full
                                                {{ $official->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                                            {{ $official->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 font-mono text-xs">{{ $official->nim ?? '-' }}</td>

                            <td class="px-4 py-3">
                                <div class="text-xs space-y-0.5">
                                    @if($official->email)
                                        <p class="text-gray-600 dark:text-gray-300 truncate max-w-[180px]"
                                           title="{{ $official->email }}">
                                            <i class="fas fa-envelope text-gray-400 mr-1 w-3"></i>
                                            {{ $official->email }}
                                        </p>
                                    @endif
                                    @if($official->phone)
                                        <p class="text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-phone text-gray-400 mr-1 w-3"></i>
                                            {{ $official->phone }}
                                        </p>
                                    @endif
                                    @if(!$official->email && !$official->phone)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                             {{ $role['bg'] }} {{ $role['text'] }}">
                                    <i class="fas fa-circle text-[6px] mr-1.5"></i>
                                    {{ $role['label'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if($jabatanUtama)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                                 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                                        <i class="fas fa-briefcase text-[9px]"></i>
                                        {{ $jabatanUtama->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum ada jabatan</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-xs">
                                @if($official->university)
                                    <p class="font-medium text-gray-700 dark:text-gray-300 truncate max-w-[140px]">
                                        {{ $official->university->name }}
                                    </p>
                                    @if($official->major)
                                        <p class="text-gray-400 truncate max-w-[140px]">{{ $official->major->name }}</p>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                             bg-cyan-50 dark:bg-cyan-900/30
                                             text-cyan-700 dark:text-cyan-300 text-xs font-bold tabular-nums">
                                    <i class="fas fa-star text-[9px]"></i>
                                    {{ (int) ($official->total_points ?? 0) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-1">
                                    <a href="{{ $safeRoute('owner.officials.show', $official->id) }}"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center
                                              text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-900/30
                                              transition" title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ $safeRoute('owner.officials.edit', $official->id) }}"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center
                                              text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30
                                              transition" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ $safeRoute('owner.officials.destroy', $official->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus {{ addslashes($official->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                                       text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30
                                                       transition" title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-20 h-20 rounded-2xl bg-gray-100 dark:bg-gray-700/50
                                                flex items-center justify-center">
                                        <i class="fas fa-users-slash text-3xl text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-700 dark:text-gray-300">
                                            Tidak ada data
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            @if(request()->hasAny(['q', 'level']))
                                                Coba hapus filter atau ubah kriteria pencarian.
                                            @else
                                                Belum ada ketua atau pengurus yang terdaftar.
                                            @endif
                                        </p>
                                    </div>
                                    @if(request()->hasAny(['q', 'level']))
                                        <a href="{{ $safeRoute('owner.officials.index') }}"
                                           class="text-sm text-cyan-600 hover:underline font-semibold">
                                            Hapus semua filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ═══ PAGINATION ═══ --}}
        @if($officials->hasPages())
            <div class="mt-6">
                {{ $officials->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection