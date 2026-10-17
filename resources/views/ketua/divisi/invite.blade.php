@extends('layouts.ketua')
@section('title', 'Invite Anggota Divisi')

@section('content')
<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">

    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-violet-600"><i class="fas fa-home"></i> Dashboard</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.divisi.index') }}" class="hover:text-violet-600">Divisi</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Invite Anggota</span>
    </nav>

    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-user-plus"></i> Invite
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Invite Anggota</h1>
            <p class="text-sm opacity-90 mt-1">Tambahkan user ke dalam divisi.</p>
        </div>
    </div>

    {{-- PICKER --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-4">
        <form method="GET" action="{{ route('ketua.divisi.invite') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">Pilih Divisi</label>
                <select name="division_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-sm font-medium
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" {{ optional($selectedDivision)->id == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedDivision)
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-black text-gray-900 dark:text-white">{{ $selectedDivision->name }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Max anggota: {{ $selectedDivision->max_members ?? '∞' }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">#</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">Nama</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">NIM</th>
                            <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">Role</th>
                            <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($users as $i => $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="p-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="p-3 font-bold text-gray-900 dark:text-white">{{ $user->name }}</td>
                                <td class="p-3 font-mono text-xs text-gray-500">{{ $user->nim ?? '-' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                        @if($user->level_id == 3) bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                        @elseif($user->level_id == 4) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @else bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                        @endif">
                                        {{ $user->level->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('ketua.divisi.add-member', $selectedDivision->id) }}"
                                          method="POST" class="inline-flex items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <select name="position"
                                                class="text-xs px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-700
                                                       bg-white dark:bg-gray-800">
                                            <option value="anggota">Anggota</option>
                                            <option value="pengurus">Pengurus</option>
                                            <option value="ketua">Ketua</option>
                                        </select>
                                        <button type="submit"
                                                class="px-2 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600
                                                       text-white text-xs font-bold transition">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center text-gray-500">
                                    Tidak ada user yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border-t border-amber-200 dark:border-amber-700/40">
                <p class="text-xs text-amber-700 dark:text-amber-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    Hanya user dengan level <b>Ketua</b>, <b>Pengurus</b>, atau <b>Anggota UKM</b> yang dapat diinvite.
                </p>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 p-16 text-center">
            <i class="fas fa-user-plus text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Pilih divisi terlebih dahulu</p>
        </div>
    @endif
</div>
@endsection