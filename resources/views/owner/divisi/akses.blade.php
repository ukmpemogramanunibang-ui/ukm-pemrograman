@extends('layouts.roles.owner')

@section('title', 'Hak Akses Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.divisi.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-key text-red-500"></i>
            Hak Akses Divisi
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        @if($divisions->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-sitemap text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada divisi.</p>
            </div>
        @else
            <form method="GET" action="{{ route('owner.divisi.akses') }}" class="mb-6 flex flex-wrap items-end gap-3">
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
                <form action="{{ route('owner.divisi.update-permissions', $selectedDivision) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="p-3 text-left">Pilih</th>
                                    <th class="p-3 text-left">Nama</th>
                                    <th class="p-3 text-left">NIM</th>
                                    <th class="p-3 text-left">Jabatan</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-center" colspan="5">Hak Akses</th>
                                </tr>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th colspan="5"></th>
                                    <th class="p-2 text-center text-xs">Lihat</th>
                                    <th class="p-2 text-center text-xs">Pengumuman</th>
                                    <th class="p-2 text-center text-xs">Pesan</th>
                                    <th class="p-2 text-center text-xs">Lihat Anggota</th>
                                    <th class="p-2 text-center text-xs">Kelola Anggota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="p-3">
                                            <input type="checkbox" name="permissions[{{ $member->id }}][view]" value="1" {{ $member->can_view ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-3">{{ $member->user->name ?? '' }}</td>
                                        <td class="p-3">{{ $member->user->nim ?? '-' }}</td>
                                        <td class="p-3">{{ ucfirst($member->position) }}</td>
                                        <td class="p-3">{{ ucfirst($member->status) }}</td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $member->id }}][announcement]" value="1" {{ $member->can_announcement ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $member->id }}][message]" value="1" {{ $member->can_message ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $member->id }}][view_members]" value="1" {{ $member->can_view_members ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $member->id }}][manage_members]" value="1" {{ $member->can_manage_members ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $member->id }}][manage_content]" value="1" {{ $member->can_manage_content ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition">
                            Simpan Hak Akses
                        </button>
                    </div>
                </form>
            @else
                <p class="text-gray-500">Silakan pilih divisi dari dropdown di atas.</p>
            @endif
        @endif
    </div>
</div>
@endsection