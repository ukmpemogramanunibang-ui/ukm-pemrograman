@extends('layouts.roles.owner')

@section('title', 'Manajemen Skema Biaya - UNICODE')

@section('content')
<div class="p-4 md:p-6">

    {{-- ============== BREADCRUMB ============== --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-3" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1">
            <i class="fas fa-home"></i> Keuangan
        </a>
        <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Skema Biaya</span>
    </nav>

    {{-- ============== SUBMENU KEUANGAN ============== --}}
    @include('owner.financial._partials.submenu', ['active' => 'fee-plans'])

    {{-- ============== HEADER ============== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 mt-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Skema Biaya</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola semua skema pembayaran UKM</p>
        </div>
        <div class="mt-3 md:mt-0 flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.fee-plans.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-plus"></i> Tambah Skema
            </a>
            <a href="#"
               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <i class="fas fa-file-import"></i> Import Historis
            </a>
        </div>
    </div>

    {{-- ============== ALERT (HANYA SEKALI) ============== --}}
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 dark:text-green-400">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 dark:text-red-400">&times;</button>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 dark:bg-blue-900/30 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
            <span><i class="fas fa-info-circle mr-2"></i>{{ session('info') }}</span>
            <button onclick="this.parentElement.remove()" class="text-blue-700 dark:text-blue-400">&times;</button>
        </div>
    @endif

    {{-- ============== FILTER ============== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="all">Semua</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Arsip</option>
                    <option value="superseded" {{ request('status') == 'superseded' ? 'selected' : '' }}>Digantikan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis</label>
                <select name="fee_type" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="all">Semua</option>
                    <option value="monthly_cash" {{ request('fee_type') == 'monthly_cash' ? 'selected' : '' }}>Kas Bulanan</option>
                    <option value="registration" {{ request('fee_type') == 'registration' ? 'selected' : '' }}>Uang Pangkal</option>
                    <option value="special_contribution" {{ request('fee_type') == 'special_contribution' ? 'selected' : '' }}>Iuran Khusus</option>
                    <option value="penalty" {{ request('fee_type') == 'penalty' ? 'selected' : '' }}>Denda</option>
                    <option value="activity" {{ request('fee_type') == 'activity' ? 'selected' : '' }}>Kegiatan</option>
                    <option value="other" {{ request('fee_type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Generasi</label>
                <select name="generation_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Semua</option>
                    @foreach($generations ?? [] as $gen)
                        <option value="{{ $gen->id }}" {{ request('generation_id') == $gen->id ? 'selected' : '' }}>{{ $gen->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm w-40"
                       placeholder="Nama / Kode">
            </div>
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('owner.financial.fee-plans.index') }}"
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm ml-2">Reset</a>
            </div>
        </form>
    </div>

    {{-- ============== STATISTIK ============== --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $feePlans->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $feePlans->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Nonaktif</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $feePlans->where('status', 'inactive')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Arsip</p>
            <p class="text-xl font-bold text-gray-500 dark:text-gray-400">{{ $feePlans->where('status', 'archived')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Digantikan</p>
            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $feePlans->where('status', 'superseded')->count() }}</p>
        </div>
    </div>

    {{-- ============== TABEL ============== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Generasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Frekuensi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($feePlans as $plan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $plan->code }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $plan->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($plan->description, 40) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $plan->generation->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ $plan->fee_type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-green-600 dark:text-green-400 font-medium">Rp {{ number_format($plan->amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->nominal_type_label }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $plan->frequency_label }}
                            <div class="text-xs text-gray-500 dark:text-gray-400">Tgl {{ $plan->due_day }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-xs">{{ $plan->target_type_label }}</span>
                            @if($plan->target_type == 'group' || $plan->target_type == 'individual')
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">({{ count($plan->target_data ?? []) }} target)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $plan->status_badge }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                            @if($plan->isActive())
                                <span class="text-xs text-green-500 ml-1">●</span>
                            @endif
                            @if($plan->isSuperseded())
                                <span class="text-xs text-purple-500 ml-1">↻</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('owner.financial.fee-plans.show', $plan) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('owner.financial.fee-plans.edit', $plan) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('owner.financial.fee-plans.duplicate', $plan) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300" title="Duplikasi" onclick="return confirm('Duplikasi skema ini?')">
                                    <i class="fas fa-copy"></i>
                                </a>
                                <form action="{{ route('owner.financial.fee-plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus skema ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-file-invoice-dollar text-4xl block mb-3 text-gray-300 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Belum ada skema biaya</p>
                            <p class="text-sm">Klik "Tambah Skema" untuk membuat skema pembayaran pertama.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $feePlans->firstItem() ?? 0 }} - {{ $feePlans->lastItem() ?? 0 }} dari {{ $feePlans->total() }} data
            </div>
            {{ $feePlans->links() }}
        </div>
    </div>
</div>
@endsection