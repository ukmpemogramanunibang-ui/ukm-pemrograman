@extends('layouts.roles.owner')

@section('title', 'Kampanye Keuangan - UNICODE')

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-bullhorn text-purple-500"></i> Kampanye Keuangan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola kampanye seperti ID Card, Event, Reuni, dan lain-lain</p>
        </div>
        <a href="{{ route('owner.financial.campaigns.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 transition shadow-md shadow-purple-500/20">
            <i class="fas fa-plus"></i> Buat Kampanye
        </a>
    </div>

    {{-- NOTIFICATIONS --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl p-4 mb-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <span class="text-green-700 dark:text-green-300">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-4 mb-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <span class="text-red-700 dark:text-red-300">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">&times;</button>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $campaigns->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $campaigns->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Draft</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $campaigns->where('status', 'draft')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
            <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $campaigns->where('status', 'closed')->count() }}</p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($campaigns as $campaign)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $campaign->title }}</div>
                            @if($campaign->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ Str::limit($campaign->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ str_replace('_', ' ', ucfirst($campaign->category)) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-medium text-green-600 dark:text-green-400">
                            Rp {{ number_format($campaign->amount, 0, ',', '.') }}
                            @if(!$campaign->is_required)
                                <span class="text-xs text-gray-400">(Opsional)</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $campaign->due_date ? \Carbon\Carbon::parse($campaign->due_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $campaign->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                   ($campaign->status == 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' : 
                                   ($campaign->status == 'closed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400')) }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('owner.financial.campaigns.show', $campaign) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('owner.financial.campaigns.edit', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('owner.financial.campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kampanye ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-bullhorn text-5xl block mb-4 text-gray-300 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Belum ada kampanye</p>
                            <p class="text-sm">Klik "Buat Kampanye" untuk memulai.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $campaigns->firstItem() ?? 0 }} - {{ $campaigns->lastItem() ?? 0 }} dari {{ $campaigns->total() }} data
            </div>
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection