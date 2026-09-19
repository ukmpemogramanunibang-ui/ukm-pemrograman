@extends('layouts.roles.owner')

@section('title', 'Riwayat NFC')
@section('page-title', 'Riwayat NFC Credential')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold mb-4">Riwayat NFC</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">UUID</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Dibuat</th>
                    <th class="px-4 py-2">Kedaluwarsa</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nfcCredentials as $nfc)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2">{{ $nfc->user->name ?? 'User dihapus' }}</td>
                    <td class="px-4 py-2 font-mono text-xs">{{ $nfc->nfc_uuid }}</td>
                    <td class="px-4 py-2">
                        @if($nfc->trashed())
                            <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-xs">Dihapus</span>
                        @elseif($nfc->is_revoked)
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Dicabut</span>
                        @elseif($nfc->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $nfc->generated_at ? $nfc->generated_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-4 py-2">{{ $nfc->expired_at ? $nfc->expired_at->format('d/m/Y H:i') : 'Tidak terbatas' }}</td>
                    <td class="px-4 py-2">
                        @if(!$nfc->trashed())
                            <form action="{{ route('owner.management.nfc.destroy', $nfc->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $nfcCredentials->links() }}</div>
</div>
@endsection