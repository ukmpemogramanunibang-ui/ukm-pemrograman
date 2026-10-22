@extends('layouts.roles.owner')

@section('title', 'Invite Anggota Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.divisi.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-user-plus text-green-500"></i>
            Invite Anggota ke Divisi
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('owner.divisi.invite') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="w-full md:w-72">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Divisi</label>
                <select name="division_id" onchange="this.form.submit()" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" {{ $division && $division->id == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($division)
            {{-- Info divisi --}}
            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-200 dark:border-blue-700">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    Divisi: <strong>{{ $division->name }}</strong> (Maksimal anggota: {{ $division->max_members ?? 'Tidak dibatasi' }})<br>
                    <span class="text-xs">Jumlah anggota saat ini: <span class="font-bold">{{ $division->members()->count() }}</span></span>
                </p>
            </div>

            {{-- Tabel User --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="p-3 text-left">#</th>
                            <th class="p-3 text-left">Nama</th>
                            <th class="p-3 text-left">NIM</th>
                            <th class="p-3 text-left">Role UKM</th>
                            <th class="p-3 text-left">Status di Divisi Ini</th>
                            <th class="p-3 text-left">Divisi Lain yang Diikuti</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Filter ulang di view untuk memastikan hanya level_id 3,4,5
                            $filteredUsers = $users->filter(function($u) {
                                return in_array($u->level_id, [3,4,5]);
                            });
                        @endphp
                        @forelse($filteredUsers as $index => $user)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="p-3">{{ $loop->iteration }}</td>
                                <td class="p-3 font-medium">{{ $user->name }}</td>
                                <td class="p-3">{{ $user->nim ?? '-' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($user->level_id == 3) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                        @elseif($user->level_id == 4) bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                        @elseif($user->level_id == 5) bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                        @endif
                                    ">
                                        {{ $user->level->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    @if($user->is_member)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Bergabung
                                            ({{ ucfirst($user->member_position) }})
                                            @if($user->member_status == 'pending')
                                                <span class="text-yellow-500">(Pending)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                            <i class="fas fa-clock mr-1"></i> Belum Bergabung
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if($user->other_divisions)
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $user->other_divisions }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($user->is_member)
                                        <span class="text-xs text-gray-400 cursor-not-allowed" title="Sudah menjadi anggota divisi ini">
                                            <i class="fas fa-user-check mr-1"></i> Sudah
                                        </span>
                                    @else
                                        <form action="{{ route('owner.divisi.add-member', $division) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="position" class="text-xs border rounded px-1 py-0.5 dark:bg-gray-700 dark:border-gray-600">
                                                <option value="anggota">Anggota</option>
                                                <option value="pengurus">Pengurus</option>
                                                <option value="ketua">Ketua</option>
                                            </select>
                                            <button type="submit" class="ml-1 px-2 py-1 bg-rose-500 hover:bg-rose-600 text-white text-xs rounded transition">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-users text-3xl mb-2 block opacity-30"></i>
                                    Tidak ada user dengan role Ketua, Pengurus, atau Anggota UKM yang tersedia untuk diinvite ke divisi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Info tambahan --}}
            <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border border-yellow-200 dark:border-yellow-700">
                <p class="text-xs text-yellow-700 dark:text-yellow-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    Hanya user dengan role <strong>Ketua</strong>, <strong>Pengurus</strong>, atau <strong>Anggota UKM</strong> yang dapat ditambahkan ke divisi.
                    User yang sudah bergabung di divisi ini tidak dapat ditambahkan lagi.
                </p>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-hand-point-left text-4xl mb-3 block opacity-30"></i>
                <p class="text-lg">Silakan pilih divisi dari dropdown di atas.</p>
            </div>
        @endif
    </div>
</div>
@endsection