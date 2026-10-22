@extends('layouts.ketua')

@section('title', 'Detail Anggota — ' . ($member->name ?? ''))

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto space-y-6">

    {{-- BACK --}}
    <a href="{{ route('ketua.manajemen-anggota.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar Anggota
    </a>

    {{-- ══════════════ PROFILE HERO ══════════════ --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm">
        {{-- Cover gradient --}}
        <div class="h-40 sm:h-48 bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 relative overflow-hidden">
            <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 30% 40%, white 1px, transparent 1px); background-size: 30px 30px;"></div>
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/20 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-pink-400/30 blur-3xl"></div>
        </div>

        <div class="px-6 sm:px-8 pb-8 -mt-20 sm:-mt-24">
            <div class="flex flex-col sm:flex-row sm:items-end gap-5">
                {{-- Avatar --}}
                <div class="shrink-0">
                    @if($member->avatar_url)
                        <img src="{{ asset('storage/'.$member->avatar_url) }}"
                             alt="{{ $member->name }}"
                             class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl object-cover ring-4 ring-white dark:ring-gray-900 shadow-2xl">
                    @else
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 flex items-center justify-center text-white font-extrabold text-6xl ring-4 ring-white dark:ring-gray-900 shadow-2xl">
                            {{ strtoupper(substr($member->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Name + Status --}}
                <div class="flex-1 sm:pb-2 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider
                            {{ $member->is_active
                                ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-500 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $member->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                            {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @if($member->is_verified)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 ring-1 ring-blue-200 dark:ring-blue-800">
                                <i class="fas fa-check-circle text-[10px]"></i> Verified
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight truncate">
                        {{ $member->name ?? 'Tanpa Nama' }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono">
                        <i class="fas fa-id-card mr-1.5"></i>{{ $member->nim ?? '-' }}
                    </p>
                </div>

                {{-- Member Number --}}
                @if($member->member_number)
                <div class="shrink-0 sm:pb-2">
                    <div class="px-4 py-2.5 rounded-2xl bg-gray-900 dark:bg-black border border-gray-800">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Member No.</p>
                        <p class="text-sm font-mono font-bold text-white mt-0.5">{{ $member->member_number }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════ INFO GRID ══════════════ --}}
    @php
        $infoFields = [
            ['icon' => 'fa-envelope',     'label' => 'Email',         'value' => $member->email,                          'color' => 'blue'],
            ['icon' => 'fa-phone',        'label' => 'Telepon',       'value' => $member->phone,                          'color' => 'emerald'],
            ['icon' => 'fa-venus-mars',   'label' => 'Jenis Kelamin', 'value' => $member->gender === 'L' ? 'Laki-laki' : ($member->gender === 'P' ? 'Perempuan' : '-'), 'color' => 'pink'],
            ['icon' => 'fa-calendar',     'label' => 'Bergabung',     'value' => optional($member->created_at)->format('d M Y'), 'color' => 'amber'],
            ['icon' => 'fa-university',   'label' => 'Universitas',   'value' => optional($member->university)->name,     'color' => 'indigo'],
            ['icon' => 'fa-graduation-cap','label' => 'Program Studi', 'value' => optional($member->major)->name,         'color' => 'violet'],
            ['icon' => 'fa-shield-alt',   'label' => 'Level',         'value' => optional($member->level)->name,          'color' => 'cyan'],
            ['icon' => 'fa-toggle-on',    'label' => 'Status Akun',   'value' => $member->is_active ? 'Aktif' : 'Nonaktif', 'color' => 'rose'],
        ];

        $colorMap = [
            'blue'    => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',       'text' => 'text-blue-600 dark:text-blue-400'],
            'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
            'pink'    => ['bg' => 'bg-pink-50 dark:bg-pink-900/20',       'text' => 'text-pink-600 dark:text-pink-400'],
            'amber'   => ['bg' => 'bg-amber-50 dark:bg-amber-900/20',     'text' => 'text-amber-600 dark:text-amber-400'],
            'indigo'  => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20',   'text' => 'text-indigo-600 dark:text-indigo-400'],
            'violet'  => ['bg' => 'bg-violet-50 dark:bg-violet-900/20',   'text' => 'text-violet-600 dark:text-violet-400'],
            'cyan'    => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/20',       'text' => 'text-cyan-600 dark:text-cyan-400'],
            'rose'    => ['bg' => 'bg-rose-50 dark:bg-rose-900/20',       'text' => 'text-rose-600 dark:text-rose-400'],
        ];
    @endphp

    <div>
        <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="fas fa-circle-info text-purple-500"></i> Informasi Pribadi
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($infoFields as $f)
                @php $c = $colorMap[$f['color']] ?? $colorMap['blue']; @endphp
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 hover:border-purple-200 dark:hover:border-purple-800 hover:shadow-md transition-all">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg {{ $c['bg'] }} flex items-center justify-center">
                            <i class="fas {{ $f['icon'] }} {{ $c['text'] }} text-xs"></i>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $f['label'] }}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                        {{ $f['value'] ?? '-' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════ JABATAN ══════════════ --}}
    @if(isset($member->jabatans) && $member->jabatans->count() > 0)
    <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                <i class="fas fa-briefcase text-white text-sm"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Jabatan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Posisi dalam organisasi</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($member->jabatans as $j)
                <div class="group px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-100 dark:border-purple-800/50 hover:shadow-md hover:scale-105 transition-all">
                    <span class="text-sm font-bold text-purple-700 dark:text-purple-300">{{ $j->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════ QUICK ACTIONS ══════════════ --}}
    <div class="flex flex-wrap gap-2 justify-end">
        <a href="{{ route('ketua.manajemen-anggota.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection