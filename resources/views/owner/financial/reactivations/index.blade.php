@extends('layouts.roles.owner')

@section('title', 'Reaktivasi Anggota - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Reaktivasi Anggota</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola permintaan reaktivasi anggota yang dibekukan</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Permohonan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $req->user->name ?? 'User tidak ditemukan' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $req->user->nim ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="text-gray-600 dark:text-gray-400 truncate">{{ $req->reason ?? '-' }}</p>
                            @if($req->commitment)
                                <p class="text-xs text-gray-500 dark:text-gray-400">Komitmen: {{ Str::limit($req->commitment, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $req->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $req->status == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                   ($req->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                   ($req->status == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400')) }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($req->status == 'pending')
                                <div class="flex gap-2">
                                    <form action="{{ route('owner.financial.reactivations.approve', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium">
                                            <i class="fas fa-check-circle"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('owner.financial.reactivations.reject', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                            <i class="fas fa-times-circle"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user-check text-2xl block mb-2 text-gray-300 dark:text-gray-600"></i>
                            Belum ada permintaan reaktivasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection