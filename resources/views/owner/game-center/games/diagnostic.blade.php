@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
    <div class="container mx-auto px-4 py-6 md:py-8 max-w-7xl">

        {{-- ============================================================ --}}
        {{-- HEADER WITH BREADCRUMB --}}
        {{-- ============================================================ --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('owner.game.games.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Game Center</a>
            <span>/</span>
            <a href="{{ route('owner.game.games.show', $game) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">{{ $game->name }}</a>
            <span>/</span>
            <span class="text-slate-700 dark:text-slate-300 font-medium">Diagnostic</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <i class="fas fa-stethoscope text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                            Diagnostic Report
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Pemeriksaan kelengkapan dan kesiapan game
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $game->name }}</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500">(ID: {{ $game->id }})</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <i class="far fa-calendar-alt text-slate-400"></i>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ now()->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <i class="fas fa-code-branch text-slate-400"></i>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">v{{ $game->version ?? '1.0.0' }}</span>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- OVERALL STATUS BANNER --}}
        {{-- ============================================================ --}}
        @php
            $statusColor = $overallStatus === 'passed' ? 'emerald' : ($overallStatus === 'warning' ? 'amber' : 'red');
            $statusIcon = $overallStatus === 'passed' ? 'fa-check-circle' : ($overallStatus === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle');
            $statusText = $overallStatus === 'passed' ? '✅ All checks passed!' : ($overallStatus === 'warning' ? '⚠️ Some issues found' : '❌ Critical errors detected');
            $statusBg = $overallStatus === 'passed' ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-800' : ($overallStatus === 'warning' ? 'bg-amber-50 border-amber-200 dark:bg-amber-950/30 dark:border-amber-800' : 'bg-red-50 border-red-200 dark:bg-red-950/30 dark:border-red-800');
            $statusTextColor = $overallStatus === 'passed' ? 'text-emerald-700 dark:text-emerald-300' : ($overallStatus === 'warning' ? 'text-amber-700 dark:text-amber-300' : 'text-red-700 dark:text-red-300');
            $statusDescColor = $overallStatus === 'passed' ? 'text-emerald-600 dark:text-emerald-400' : ($overallStatus === 'warning' ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
        @endphp

        <div class="p-5 rounded-2xl border-2 {{ $statusBg }} flex items-start gap-4 shadow-sm transition-all duration-300">
            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-2xl {{ $overallStatus === 'passed' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : ($overallStatus === 'warning' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400') }}">
                <i class="fas {{ $statusIcon }}"></i>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-lg font-bold {{ $statusTextColor }}">{{ $statusText }}</h2>
                    <span class="text-xs px-3 py-1 rounded-full bg-white/70 dark:bg-slate-800/70 text-slate-600 dark:text-slate-400 font-medium shadow-sm">
                        {{ $summary['passed'] }}/{{ $summary['total_checks'] }} passed
                    </span>
                </div>
                <p class="text-sm {{ $statusDescColor }} mt-1">
                    @if($summary['warnings'] > 0)
                        <span class="inline-flex items-center gap-1 mr-3"><i class="fas fa-exclamation-circle text-amber-500"></i> {{ $summary['warnings'] }} peringatan</span>
                    @endif
                    @if($summary['failed'] > 0)
                        <span class="inline-flex items-center gap-1"><i class="fas fa-times-circle text-red-500"></i> {{ $summary['failed'] }} gagal</span>
                    @endif
                    @if($summary['warnings'] == 0 && $summary['failed'] == 0)
                        <span class="inline-flex items-center gap-1"><i class="fas fa-check-circle text-emerald-500"></i> Semua pemeriksaan berhasil</span>
                    @endif
                </p>
            </div>
            @if($overallStatus !== 'passed')
                <div class="flex-shrink-0 hidden sm:block">
                    <span class="inline-block px-3 py-1.5 text-xs font-bold uppercase tracking-wider rounded-lg {{ $overallStatus === 'failed' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                        {{ $overallStatus === 'failed' ? 'Failed' : 'Warning' }}
                    </span>
                </div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- SUMMARY CARDS WITH ANIMATION --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 text-center hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
                <div class="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 mb-1">
                    <i class="fas fa-clipboard-list text-indigo-500"></i>
                    <span class="text-xs font-medium uppercase tracking-wider">Total</span>
                </div>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $summary['total_checks'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">pemeriksaan</p>
            </div>
            <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 text-center hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
                <div class="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 mb-1">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="text-xs font-medium uppercase tracking-wider">Passed</span>
                </div>
                <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $summary['passed'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">berhasil</p>
            </div>
            <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 text-center hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
                <div class="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 mb-1">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    <span class="text-xs font-medium uppercase tracking-wider">Warnings</span>
                </div>
                <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $summary['warnings'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">peringatan</p>
            </div>
            <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 text-center hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
                <div class="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 mb-1">
                    <i class="fas fa-times-circle text-red-500"></i>
                    <span class="text-xs font-medium uppercase tracking-wider">Failed</span>
                </div>
                <p class="text-3xl font-black text-red-600 dark:text-red-400">{{ $summary['failed'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">gagal</p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ERRORS & WARNINGS SECTION --}}
        {{-- ============================================================ --}}
        @if(!empty($errors) || !empty($warnings))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
                @if(!empty($errors))
                    <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-5 py-3 bg-red-100 dark:bg-red-900/30 border-b border-red-200 dark:border-red-800 flex items-center gap-3">
                            <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-lg"></i>
                            <h3 class="font-bold text-red-800 dark:text-red-200">Error Fatal</h3>
                            <span class="ml-auto text-xs font-medium px-2.5 py-1 rounded-full bg-red-200 dark:bg-red-800 text-red-700 dark:text-red-300">{{ count($errors) }}</span>
                        </div>
                        <div class="px-5 py-4 space-y-3 max-h-80 overflow-y-auto">
                            @foreach($errors as $idx => $error)
                                <div class="flex items-start gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl border border-red-200 dark:border-red-800 shadow-sm">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs font-bold">{{ $idx + 1 }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-red-700 dark:text-red-300 font-medium">{{ $error }}</p>
                                        @if(isset($suggestions[$idx]))
                                            <p class="text-sm text-red-600 dark:text-red-400 mt-1 flex items-start gap-1.5">
                                                <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                                                <span>{{ $suggestions[$idx] }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($warnings))
                    <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-5 py-3 bg-amber-100 dark:bg-amber-900/30 border-b border-amber-200 dark:border-amber-800 flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-lg"></i>
                            <h3 class="font-bold text-amber-800 dark:text-amber-200">Peringatan</h3>
                            <span class="ml-auto text-xs font-medium px-2.5 py-1 rounded-full bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-300">{{ count($warnings) }}</span>
                        </div>
                        <div class="px-5 py-4 space-y-2 max-h-80 overflow-y-auto">
                            @foreach($warnings as $idx => $warning)
                                <div class="flex items-start gap-3 p-2.5 hover:bg-amber-100/50 dark:hover:bg-amber-900/20 rounded-lg transition">
                                    <i class="fas fa-exclamation-circle text-amber-500 mt-0.5"></i>
                                    <span class="text-amber-700 dark:text-amber-300">{{ $warning }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- AUTO FIX BUTTON (jika ada error typing_settings kosong) --}}
        {{-- ============================================================ --}}
        @if(!empty($errors) && $game->game_type === 'typing' && empty($game->typing_settings))
            <div class="mt-6 bg-gradient-to-r from-emerald-50 to-emerald-100/50 dark:from-emerald-950/30 dark:to-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl p-6 shadow-md">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-wrench text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-emerald-800 dark:text-emerald-200 text-lg">Perbaikan Otomatis Tersedia</h3>
                            <p class="text-sm text-emerald-700 dark:text-emerald-300">
                                Sistem akan mengisi pengaturan typing dengan nilai default sehingga game bisa dimainkan.
                            </p>
                            <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                <span class="px-2 py-1 bg-white/70 dark:bg-slate-800/70 rounded-lg text-emerald-700 dark:text-emerald-300">⏱️ Durasi: 60 detik</span>
                                <span class="px-2 py-1 bg-white/70 dark:bg-slate-800/70 rounded-lg text-emerald-700 dark:text-emerald-300">👤 Max: 1 pemain</span>
                                <span class="px-2 py-1 bg-white/70 dark:bg-slate-800/70 rounded-lg text-emerald-700 dark:text-emerald-300">📝 Teks default tersedia</span>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('owner.game.games.fix-diagnostic', $game) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl font-bold transition shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2">
                            <i class="fas fa-play"></i>
                            Perbaiki Sekarang
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- DETAIL CHECKS TABLE --}}
        {{-- ============================================================ --}}
        <div class="mt-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/20">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-list-ul text-indigo-500"></i>
                    Detail Pemeriksaan
                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ count($checks) }})</span>
                </h2>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                        <i class="fas fa-circle text-emerald-500 text-[6px]"></i> Passed
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                        <i class="fas fa-circle text-amber-500 text-[6px]"></i> Warning
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                        <i class="fas fa-circle text-red-500 text-[6px]"></i> Failed
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-center w-12">#</th>
                            <th class="px-6 py-3">Komponen</th>
                            <th class="px-6 py-3 text-center w-28">Status</th>
                            <th class="px-6 py-3">Detail</th>
                            <th class="px-6 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($checks as $idx => $check)
                            @php
                                $rowBg = $check['status'] === 'passed' ? 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10' : ($check['status'] === 'warning' ? 'hover:bg-amber-50/50 dark:hover:bg-amber-900/10' : 'hover:bg-red-50/50 dark:hover:bg-red-900/10');
                                $statusBadge = $check['status'] === 'passed' 
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' 
                                    : ($check['status'] === 'warning' 
                                        ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800' 
                                        : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 border border-red-200 dark:border-red-800');
                                $statusIcon = $check['status'] === 'passed' ? 'fa-check-circle' : ($check['status'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle');
                            @endphp
                            <tr class="{{ $rowBg }} transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-medium text-slate-500 dark:text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">
                                    {{ $check['component'] }}
                                    @if($check['status'] === 'failed')
                                        <span class="ml-2 text-xs text-red-500">⚠️</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $statusBadge }}">
                                        <i class="fas {{ $statusIcon }}"></i>
                                        {{ ucfirst($check['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $check['detail'] }}">
                                    {{ $check['detail'] }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                    {{ $check['message'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RECOMMENDATIONS / SUGGESTIONS --}}
        {{-- ============================================================ --}}
        @if(!empty($suggestions))
            <div class="mt-6 bg-gradient-to-r from-indigo-50 to-indigo-100/50 dark:from-indigo-950/20 dark:to-indigo-900/10 border border-indigo-200 dark:border-indigo-800 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 bg-indigo-100/50 dark:bg-indigo-900/20 border-b border-indigo-200 dark:border-indigo-800 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center shadow-md shadow-indigo-500/30">
                        <i class="fas fa-list-check text-white text-sm"></i>
                    </div>
                    <h3 class="font-bold text-indigo-800 dark:text-indigo-200">Daftar Tindakan Perbaikan</h3>
                    <span class="ml-auto text-xs font-medium px-2.5 py-1 rounded-full bg-indigo-200 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-300">{{ count($suggestions) }}</span>
                </div>
                <div class="px-6 py-4">
                    <ul class="space-y-2.5">
                        @foreach($suggestions as $idx => $suggestion)
                            <li class="flex items-start gap-3 p-3 bg-white/60 dark:bg-slate-800/60 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition shadow-sm border border-indigo-100 dark:border-indigo-900/30">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold shadow-md shadow-indigo-500/20">{{ $idx + 1 }}</span>
                                <span class="text-indigo-700 dark:text-indigo-300">{{ $suggestion }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- GAME PREVIEW --}}
        {{-- ============================================================ --}}
        @if($overallStatus === 'passed' || $overallStatus === 'warning')
            <div class="mt-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-950/10 dark:to-purple-950/10">
                    <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-gamepad text-indigo-500"></i>
                        Preview Game
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">
                            @if($overallStatus === 'passed')
                                <span class="text-emerald-500">✅ Siap dimainkan</span>
                            @else
                                <span class="text-amber-500">⚠️ Dapat dimainkan dengan catatan</span>
                            @endif
                        </span>
                    </h2>
                    <a href="{{ route('owner.game.games.gameplay', ['game' => $game->id, 'token' => md5($game->id . auth()->id() . 'secret')]) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white rounded-xl text-sm font-bold transition shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-play"></i> Mainkan
                    </a>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3">
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Nama Game</span>
                            <p class="font-bold text-slate-800 dark:text-white truncate">{{ $game->name }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3">
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Tipe Game</span>
                            <p class="font-bold text-slate-800 dark:text-white">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                    {{ $game->game_type ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3">
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Status</span>
                            <p class="font-bold text-slate-800 dark:text-white">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs
                                    @if($game->status === 'published') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                    @elseif($game->status === 'testing') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300
                                    @else bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 @endif">
                                    {{ ucfirst($game->status ?? 'draft') }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3">
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Jumlah Pemain</span>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $game->player_count ?? 0 }}</p>
                        </div>
                    </div>
                    @if($game->description)
                        <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $game->description }}</p>
                        </div>
                    @endif
                    @if($game->game_type === 'typing' && !empty($game->typing_settings))
                        <div class="mt-3 p-3 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Typing Settings:</p>
                            <div class="flex flex-wrap gap-3 mt-1 text-xs">
                                <span class="px-2 py-1 bg-white dark:bg-slate-700 rounded-lg text-slate-700 dark:text-slate-300">
                                    ⏱️ {{ $game->typing_settings['duration'] ?? 60 }}s
                                </span>
                                <span class="px-2 py-1 bg-white dark:bg-slate-700 rounded-lg text-slate-700 dark:text-slate-300">
                                    👤 {{ $game->typing_settings['max_players'] ?? 1 }} player
                                </span>
                                <span class="px-2 py-1 bg-white dark:bg-slate-700 rounded-lg text-slate-700 dark:text-slate-300">
                                    📝 {{ str_word_count($game->typing_settings['custom_text'] ?? '') }} kata
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- RAW DATA (Collapsible) --}}
        {{-- ============================================================ --}}
        <div class="mt-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition group" type="button" onclick="toggleRawData()">
                <span class="font-medium flex items-center gap-2">
                    <i class="fas fa-code text-indigo-500"></i>
                    Data Mentah (Debug)
                    <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">JSON</span>
                </span>
                <i id="rawToggleIcon" class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:text-indigo-500"></i>
            </button>
            <div id="rawDataContainer" class="hidden">
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/20">
                    <div class="relative">
                        <button onclick="copyRawData()" class="absolute top-3 right-3 px-3 py-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition shadow-md shadow-indigo-500/20">
                            <i class="fas fa-copy mr-1"></i> Salin
                        </button>
                        <pre id="rawDataContent" class="text-xs bg-white dark:bg-slate-900 p-4 rounded-xl overflow-auto max-h-96 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-mono shadow-inner"><code>{{ json_encode($rawData, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ACTION BUTTONS --}}
        {{-- ============================================================ --}}
        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-slate-200 dark:border-slate-700">
            <a href="{{ route('owner.game.games.show', $game) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold transition active:scale-95">
                <i class="fas fa-arrow-left"></i> Kembali ke Game
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('owner.game.games.edit', $game) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-amber-500/20">
                    <i class="fas fa-edit"></i> Edit Game
                </a>
                <a href="{{ route('owner.game.games.diagnostic', $game) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/20">
                    <i class="fas fa-sync-alt"></i> Jalankan Ulang
                </a>
                @if($overallStatus === 'passed')
                    <a href="{{ route('owner.game.games.gameplay', ['game' => $game->id, 'token' => md5($game->id . auth()->id() . 'secret')]) }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-emerald-500/20">
                        <i class="fas fa-play"></i> Mainkan
                    </a>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function toggleRawData() {
    const container = document.getElementById('rawDataContainer');
    const icon = document.getElementById('rawToggleIcon');
    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        container.style.display = 'block';
        icon.classList.add('rotate-180');
    } else {
        container.classList.add('hidden');
        container.style.display = 'none';
        icon.classList.remove('rotate-180');
    }
}

function copyRawData() {
    const content = document.getElementById('rawDataContent');
    if (!content) return;
    const text = content.textContent || content.innerText;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Tersalin!';
        setTimeout(() => { btn.innerHTML = original; }, 2000);
    }).catch(() => {
        // Fallback
        const range = document.createRange();
        range.selectNode(content);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
    });
}

// Auto collapse raw data if too long
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('rawDataContainer');
    if (container) {
        const pre = container.querySelector('pre');
        if (pre && pre.textContent.length > 5000) {
            // Keep collapsed by default
            container.classList.add('hidden');
            container.style.display = 'none';
        }
    }
});
</script>
<style>
.rotate-180 { transform: rotate(180deg); transition: transform 0.3s ease; }
#rawDataContainer { transition: all 0.3s ease; }
</style>
@endpush

@endsection