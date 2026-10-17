@extends('layouts.ketua')
@section('title', 'Anggota Divisi')

@section('content')
<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">

    {{-- BREADCRUMB --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-violet-600"><i class="fas fa-home"></i> Dashboard</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.divisi.index') }}" class="hover:text-violet-600">Divisi</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Anggota</span>
    </nav>

    {{-- HEADER --}}
    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-users"></i> Manajemen Anggota
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Anggota Divisi</h1>
            <p class="text-sm opacity-90 mt-1">Kelola anggota setiap divisi.</p>
        </div>
    </div>

    {{-- PICKER --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-4">
        <form method="GET" action="{{ route('ketua.divisi.anggota') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">Pilih Divisi</label>
                <select name="division_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-sm font-medium
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" {{ $selectedId == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedDivision)
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h2 class="font-black text-gray-900 dark:text-white">{{ $selectedDivision->name }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Total anggota: <b>{{ $members->count() }}</b>
                    </p>
                </div>
                <a href="{{ route('ketua.divisi.invite', ['division_id' => $selectedDivision->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                          bg-gradient-to-r from-emerald-500 to-teal-600 text-white
                          text-xs font-bold hover:shadow-md transition">
                    <i class="fas fa-user-plus"></i> Tambah Anggota
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">#</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">Nama</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">NIM</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">Jabatan</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($members as $i => $member)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="p-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="p-3 font-bold text-gray-900 dark:text-white">
                                    {{ $member->user->name ?? '-' }}
                                </td>
                                <td class="p-3 font-mono text-xs text-gray-500">{{ $member->user->nim ?? '-' }}</td>
                                <td class="p-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase
                                        @if($member->position == 'ketua') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                        @elseif($member->position == 'pengurus') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                                        @endif">
                                        @if($member->position == 'ketua') <i class="fas fa-crown text-[8px]"></i> @endif
                                        {{ ucfirst($member->position) }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase
                                        {{ $member->status == 'aktif'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-800
                                                flex items-center justify-center mb-3">
                                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Belum ada anggota</p>
                                    <p class="text-xs text-gray-400 mt-1">Tambahkan anggota untuk divisi ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 p-16 text-center">
            <i class="fas fa-hand-point-up text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Silakan pilih divisi</p>
            <p class="text-xs text-gray-500 mt-1">Pilih divisi dari dropdown di atas.</p>
        </div>
    @endif
</div>
@endsection