@extends('layouts.ketua')
@section('title', 'Detail Divisi – ' . ($division->name ?? ''))

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto space-y-6">

    {{-- BACK --}}
    <a href="{{ route('ketua.divisi.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
              hover:text-cyan-600 dark:hover:text-cyan-400 transition group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar Divisi
    </a>

    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-cyan-600 via-blue-600 to-indigo-600
                p-6 sm:p-8 shadow-2xl shadow-cyan-500/20">
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-white/15 blur-3xl"></div>

        <div class="relative flex items-start gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-xl
                        border border-white/30 flex items-center justify-center
                        shadow-lg shrink-0">
                <i class="fas fa-sitemap text-white text-2xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur text-white text-[10px]
                             font-bold uppercase tracking-widest border border-white/20 inline-flex items-center">
                    <i class="fas fa-users-cog text-[8px] mr-1"></i> Divisi
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-2">
                    {{ $division->name ?? 'Tanpa Nama' }}
                </h1>
                @if(!empty($division->description))
                    <p class="text-sm text-white/80 mt-1 leading-relaxed">
                        {{ $division->description }}
                    </p>
                @endif
                @if(!empty($division->code))
                    <p class="text-[11px] text-white/60 mt-2 font-mono">
                        <i class="fas fa-tag text-[9px] mr-1"></i>{{ $division->code }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- INFO GRID --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $membersCount = isset($division->members) ? $division->members->count() : 0;
            $infoItems = [
                ['icon' => 'fa-users',      'label' => 'Anggota',  'value' => $membersCount,                       'bg' => 'bg-cyan-100 dark:bg-cyan-900/30',       'color' => 'text-cyan-600 dark:text-cyan-400'],
                ['icon' => 'fa-hashtag',    'label' => 'ID',       'value' => $division->id ?? '-',                'bg' => 'bg-indigo-100 dark:bg-indigo-900/30',   'color' => 'text-indigo-600 dark:text-indigo-400'],
                ['icon' => 'fa-toggle-on',  'label' => 'Status',   'value' => strtoupper($division->status ?? '-'),'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'color' => 'text-emerald-600 dark:text-emerald-400'],
                ['icon' => 'fa-calendar',   'label' => 'Dibuat',   'value' => optional($division->created_at)->format('d M Y') ?? '-', 'bg' => 'bg-amber-100 dark:bg-amber-900/30', 'color' => 'text-amber-600 dark:text-amber-400'],
            ];
        @endphp

        @foreach($infoItems as $item)
        <div class="bg-white dark:bg-gray-900 rounded-2xl
                    border border-gray-200 dark:border-gray-800 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $item['bg'] }}
                            flex items-center justify-center shrink-0">
                    <i class="fas {{ $item['icon'] }} {{ $item['color'] }} text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $item['label'] }}
                    </p>
                    <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums truncate">
                        {{ $item['value'] }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- MEMBERS LIST --}}
    <div class="bg-white dark:bg-gray-900 rounded-3xl
                border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <h2 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-users text-cyan-500"></i> Anggota Divisi
            </h2>
            @if(isset($division->max_members))
                <span class="text-xs text-gray-400">
                    Max: {{ $division->max_members }} orang
                </span>
            @endif
        </div>

        @if(isset($division->members) && $division->members->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($division->members as $member)
                    @php
                        $user = $member->user ?? null;
                        $name = $user->name ?? 'User #' . ($member->user_id ?? '?');
                        $subtext = $user->nim ?? ($user->email ?? '');
                        $position = $member->custom_position ?? $member->position ?? 'anggota';
                    @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl
                                bg-gray-50 dark:bg-gray-800/50
                                hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition
                                border border-transparent hover:border-cyan-200 dark:hover:border-cyan-800">
                        <div class="w-11 h-11 rounded-full shrink-0
                                    bg-gradient-to-br from-cyan-500 to-blue-600
                                    flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                {{ $name }}
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                {{ $subtext }}
                            </p>
                            <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded
                                         text-[9px] font-bold uppercase tracking-wide
                                         bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400">
                                {{ $position }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-user-slash text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Belum ada anggota di divisi ini.
                </p>
            </div>
        @endif
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex justify-end gap-2">
        <a href="{{ route('ketua.divisi.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                  bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                  text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection