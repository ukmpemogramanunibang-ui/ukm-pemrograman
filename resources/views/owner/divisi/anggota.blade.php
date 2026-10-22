@extends('layouts.roles.owner')

@section('title', 'Anggota Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.divisi.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-users text-blue-500"></i>
            Anggota Divisi
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">

        @if($divisions->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-sitemap text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada divisi.</p>
                <a href="{{ route('owner.divisi.custom') }}" class="text-rose-600 hover:underline mt-2 inline-block">
                    Buat divisi sekarang
                </a>
            </div>
        @else
            <form method="GET" action="{{ route('owner.divisi.anggota') }}" class="mb-6 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Divisi</label>
                    <select name="division_id" onchange="this.form.submit()" class="mt-1 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        @foreach($divisions as $d)
                            <option value="{{ $d->id }}" {{ $selectedDivisionId == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        Tampilkan
                    </button>
                </div>
            </form>

            @if($selectedDivision)
                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Divisi: <strong>{{ $selectedDivision->name }}</strong>
                        <span class="ml-4 text-xs text-gray-400">(Total anggota: {{ $members->count() }})</span>
                    </p>
                    <a href="{{ route('owner.divisi.invite', ['division_id' => $selectedDivision->id]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Tambah Anggota
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="p-3 text-left">#</th>
                                <th class="p-3 text-left">Nama</th>
                                <th class="p-3 text-left">NIM</th>
                                <th class="p-3 text-left">Jabatan</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members as $index => $member)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="p-3">{{ $loop->iteration }}</td>
                                    <td class="p-3">{{ $member->user->name ?? 'User tidak ditemukan' }}</td>
                                    <td class="p-3">{{ $member->user->nim ?? '-' }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($member->position == 'ketua') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                            @elseif($member->position == 'pengurus') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif
                                        ">
                                            {{ ucfirst($member->position) }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($member->status == 'aktif') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                            @elseif($member->status == 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif
                                        ">
                                            {{ ucfirst($member->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-1" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="absolute z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700" style="position: fixed; transform: translateX(-30%);">
                                                <div class="py-1">
                                                    {{-- Keluarkan --}}
                                                    @if($member->position != 'ketua' || $members->where('position', 'ketua')->count() > 1)
                                                        <form action="{{ route('owner.divisi.remove-member', [$selectedDivision, $member->user_id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="return confirm('Yakin ingin mengeluarkan {{ $member->user->name }}?')">
                                                                <i class="fas fa-user-minus mr-2"></i> Keluarkan
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="block px-4 py-2 text-sm text-gray-400">Ketua tidak bisa dikeluarkan</span>
                                                    @endif

                                                    {{-- Ubah Jabatan --}}
                                                    <form action="{{ route('owner.divisi.update-member', [$selectedDivision, $member->id]) }}" method="POST" class="border-t border-gray-200 dark:border-gray-700">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="px-4 py-2">
                                                            <select name="position" class="w-full text-xs border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600">
                                                                <option value="anggota" {{ $member->position == 'anggota' ? 'selected' : '' }}>Anggota</option>
                                                                <option value="pengurus" {{ $member->position == 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                                                                <option value="ketua" {{ $member->position == 'ketua' ? 'selected' : '' }}>Ketua</option>
                                                            </select>
                                                            <button type="submit" class="mt-1 w-full text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">Update Jabatan</button>
                                                        </div>
                                                    </form>

                                                    {{-- Pindahkan --}}
                                                    <form action="{{ route('owner.divisi.move-member', [$selectedDivision, $member->id]) }}" method="POST" class="border-t border-gray-200 dark:border-gray-700">
                                                        @csrf
                                                        @method('POST')
                                                        <div class="px-4 py-2">
                                                            <select name="target_division_id" class="w-full text-xs border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600">
                                                                @foreach($allDivisions as $d)
                                                                    @if($d->id != $selectedDivision->id)
                                                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="mt-1 w-full text-xs bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded">Pindahkan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada anggota dalam divisi ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Silakan pilih divisi dari dropdown di atas.</p>
            @endif
        @endif
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection 