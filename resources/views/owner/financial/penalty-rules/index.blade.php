@extends('layouts.roles.owner')

@section('title', 'Aturan Denda - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Aturan Denda</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola aturan denda keterlambatan pembayaran</p>
        </div>
        <a href="{{ route('owner.financial.penalty-rules.create') }}" class="mt-3 md:mt-0 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
            <i class="fas fa-plus"></i> Tambah Aturan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($penaltyRules ?? [] as $rule)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ $rule->name }}</h3>
                <span class="px-2 py-1 text-xs rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="space-y-1 text-sm">
                <p class="text-gray-600 dark:text-gray-400">Level: <span class="font-medium text-gray-800 dark:text-white">{{ $rule->level }}</span></p>
                <p class="text-gray-600 dark:text-gray-400">Hari: <span class="font-medium text-gray-800 dark:text-white">{{ $rule->start_day }} @if($rule->end_day) - {{ $rule->end_day }} @endif</span></p>
                @if($rule->fixed_amount > 0)
                <p class="text-gray-600 dark:text-gray-400">Denda Tetap: <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($rule->fixed_amount, 0, ',', '.') }}</span></p>
                @endif
                @if($rule->daily_amount > 0)
                <p class="text-gray-600 dark:text-gray-400">Denda Harian: <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($rule->daily_amount, 0, ',', '.') }}</span></p>
                @endif
                @if($rule->max_amount)
                <p class="text-gray-600 dark:text-gray-400">Maksimal: <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($rule->max_amount, 0, ',', '.') }}</span></p>
                @endif
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('owner.financial.penalty-rules.edit', $rule) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('owner.financial.penalty-rules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus aturan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-8">
            <i class="fas fa-gavel text-2xl block mb-2 text-gray-300 dark:text-gray-600"></i>
            Belum ada aturan denda. Klik "Tambah Aturan" untuk memulai.
        </div>
        @endforelse
    </div>
</div>
@endsection