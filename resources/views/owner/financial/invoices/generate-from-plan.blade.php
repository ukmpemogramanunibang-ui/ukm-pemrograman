@extends('layouts.roles.owner')

@section('title', 'Generate Tagihan - ' . $plan->name)

@section('content')
<div class="p-4 md:p-6">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-3">
        <a href="{{ route('owner.financial.dashboard') }}" class="hover:text-blue-600 flex items-center gap-1">
            <i class="fas fa-home"></i> Keuangan
        </a>
        <i class="fas fa-chevron-right mx-2"></i>
        <a href="{{ route('owner.financial.invoices.index') }}" class="hover:text-blue-600">Tagihan</a>
        <i class="fas fa-chevron-right mx-2"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Generate</span>
    </nav>

    @include('owner.financial._partials.submenu', ['active' => 'invoices'])

    {{-- HEADER --}}
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-sm p-6 mb-6 text-white mt-4">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <p class="text-sm opacity-90 mb-1">
                    <i class="fas fa-bolt mr-1"></i> Auto Generate Tagihan
                </p>
                <h1 class="text-2xl font-bold">{{ $plan->name }}</h1>
                <p class="text-sm opacity-90 mt-1">
                    <span class="font-mono bg-white/20 px-2 py-0.5 rounded">{{ $plan->code }}</span>
                    · {{ $plan->fee_type_label }}
                    · {{ $plan->frequency_label }}
                    · Tgl jatuh tempo: {{ $plan->due_day }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs opacity-80">Nominal</p>
                <p class="text-3xl font-bold">Rp {{ number_format($plan->amount, 0, ',', '.') }}</p>
                <p class="text-xs opacity-80">{{ $plan->nominal_type_label }}</p>
            </div>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Periode Tagihan</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $preview['period'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jatuh Tempo</p>
            <p class="text-lg font-bold text-orange-600 dark:text-orange-400">
                {{ \Carbon\Carbon::parse($preview['due_date'])->translatedFormat('d M Y') }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jumlah User</p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $preview['total_users'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Tagihan</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                Rp {{ number_format($preview['total_amount'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    @if($preview['skipped'] > 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 text-yellow-800 dark:text-yellow-400 rounded-lg px-4 py-3 mb-4 text-sm">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>{{ $preview['skipped'] }} user</strong> sudah memiliki invoice untuk periode ini dan akan dilewati.
        </div>
    @endif

    @if($preview['total_users'] === 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <i class="fas fa-user-slash text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Tidak ada user yang eligible</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Semua user yang cocok sudah ditagih untuk periode <strong>{{ $preview['period'] }}</strong>,
                atau belum ada user yang sesuai kriteria target.
            </p>
            <a href="{{ route('owner.financial.invoices.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    @else
        {{-- TABEL PREVIEW USER --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                    <i class="fas fa-list-check text-blue-500 mr-2"></i>
                    Daftar User yang Akan Ditagih
                </h3>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Level</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($preview['users'] as $i => $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-2 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-white">{{ $row['name'] }}</td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $row['nim'] ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ $row['level'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right text-green-600 dark:text-green-400 font-medium">
                                    Rp {{ number_format($row['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-exclamation-circle text-yellow-500 mr-1"></i>
                    Invoice yang dibuat <strong>tidak bisa dibatalkan otomatis</strong>. Pastikan data sudah benar.
                </p>
            </div>
            <form method="POST" action="{{ route('owner.financial.invoices.from-plan.store', $plan) }}"
                  onsubmit="return confirm('Generate {{ $preview['total_users'] }} invoice senilai Rp {{ number_format($preview['total_amount'], 0, ',', '.') }}?')">
                @csrf
                <input type="hidden" name="period" value="{{ $preview['period'] }}">

                <a href="{{ route('owner.financial.dashboard') }}"
                   class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition mr-2">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                    <i class="fas fa-bolt mr-1"></i> Generate {{ $preview['total_users'] }} Tagihan
                </button>
            </form>
        </div>
    @endif
</div>
@endsection