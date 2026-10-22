@extends('layouts.ketua')
@section('title', 'Hak Akses Divisi')

@section('content')
<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">

    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-violet-600"><i class="fas fa-home"></i> Dashboard</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.divisi.index') }}" class="hover:text-violet-600">Divisi</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Hak Akses</span>
    </nav>

    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-rose-600 via-red-600 to-orange-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-key"></i> Manajemen Akses
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Hak Akses Divisi</h1>
            <p class="text-sm opacity-90 mt-1">Atur permission per anggota divisi.</p>
        </div>
    </div>

    {{-- PICKER --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-4">
        <form method="GET" action="{{ route('ketua.divisi.akses') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">Pilih Divisi</label>
                <select name="division_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-sm font-medium
                               focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" {{ $selectedDivisionId == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedDivision)
        <form action="{{ route('ketua.divisi.update-permissions', $selectedDivision->id) }}" method="POST">
            @csrf
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-black text-gray-900 dark:text-white">{{ $selectedDivision->name }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total: {{ $members->count() }} anggota</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="p-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-500">Nama</th>
                                <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Lihat</th>
                                <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Pengumuman</th>
                                <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Pesan</th>
                                <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Lihat Anggota</th>
                                <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Kelola Anggota</th>
                                <th class="p-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-500">Kelola Konten</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($members as $member)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-500 to-orange-500
                                                        text-white text-[10px] font-black flex items-center justify-center">
                                                {{ strtoupper(substr($member->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white text-xs">{{ $member->user->name ?? '-' }}</p>
                                                <p class="text-[10px] text-gray-500">{{ ucfirst($member->position) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="permissions[{{ $member->id }}][view]" value="1"
                                               {{ $member->can_view ? 'checked' : '' }}
                                               class="w-4 h-4 accent-rose-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="permissions[{{ $member->id }}][announcement]" value="1"
                                               {{ $member->can_announcement ? 'checked' : '' }}
                                               class="w-4 h-4 accent-rose-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="permissions[{{ $member->id }}][message]" value="1"
                                               {{ $member->can_message ? 'checked' : '' }}
                                               class="w-4 h-4 accent-rose-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="permissions[{{ $member->id }}][view_members]" value="1"
                                               {{ $member->can_view_members ? 'checked' : '' }}
                                               class="w-4 h-4 accent-rose-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="permissions[{{ $member->id }}][manage_members]" value="1"
                                               {{ $member->can_manage_members ? 'checked' : '' }}
                                               class="w-4 h-4 accent-rose-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="permissions[{{ $member->id }}][manage_content]" value="1"
                                               {{ $member->can_manage_content ? 'checked' : '' }}
                                               class="w-4 h-4 accent-rose-500 cursor-pointer">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center">
                                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Belum ada anggota</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($members->count() > 0)
                    <div class="p-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                                       bg-gradient-to-r from-rose-500 to-orange-600 text-white
                                       font-bold text-sm hover:shadow-lg transition active:scale-95">
                            <i class="fas fa-save"></i> Simpan Hak Akses
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 p-16 text-center">
            <i class="fas fa-key text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Pilih divisi terlebih dahulu</p>
        </div>
    @endif
</div>
@endsection