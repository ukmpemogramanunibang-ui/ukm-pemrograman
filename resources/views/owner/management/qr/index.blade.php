@extends('layouts.roles.owner')

@section('title', 'Manajemen QR Code – Owner UNICODE')
@section('page-title', 'Manajemen QR Code')

@section('content')

@php
    $me = auth()->user();
    if ($me) { $me->loadMissing(['qrCodes', 'level']); }
    $myQr = $me?->qrCodes->where('is_active', true)->first();
@endphp

<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- KARTU QR PRIBADI OWNER — render hanya kalau owner punya QR --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($myQr && $me)
        <div class="relative overflow-hidden rounded-2xl shadow-xl
                    bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 text-white">

            {{-- dekorasi --}}
            <div class="absolute inset-0 opacity-[0.07]"
                 style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px),
                                          radial-gradient(circle at 80% 60%, white 1px, transparent 1px);
                        background-size: 32px 32px;"></div>
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-fuchsia-400/20 rounded-full blur-3xl"></div>

            <div class="relative p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center gap-5">

                {{-- Ikon --}}
                <div class="relative w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm
                            border border-white/25 flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i class="fas fa-qrcode text-2xl"></i>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full
                                 bg-emerald-400 border-2 border-white animate-pulse"></span>
                </div>

                {{-- Info utama --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest
                                     bg-white/20 px-2.5 py-0.5 rounded-full">
                            <i class="fas fa-user-shield mr-1"></i> QR Pribadi Saya
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-widest
                                     bg-emerald-400/25 border border-emerald-300/50 px-2.5 py-0.5 rounded-full">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                    </div>

                    <h3 class="text-lg sm:text-xl font-black tracking-tight truncate">
                        {{ $me->name }}
                    </h3>

                    <div class="text-white/80 text-xs mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span><i class="fas fa-hashtag mr-1 opacity-70"></i>{{ $me->nim ?? '-' }}</span>
                        <span class="truncate"><i class="fas fa-envelope mr-1 opacity-70"></i>{{ $me->email }}</span>
                        <span><i class="fas fa-layer-group mr-1 opacity-70"></i>{{ $me->level->name ?? 'Owner' }}</span>
                    </div>

                    <div class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-white/70">
                        <span>
                            <i class="fas fa-clock mr-1"></i>
                            Dibuat: {{ optional($myQr->created_at)->translatedFormat('d F Y, H:i') }}
                        </span>
                        @if($myQr->expired_at)
                            <span>
                                <i class="fas fa-hourglass-end mr-1"></i>
                                Berlaku s/d {{ \Carbon\Carbon::parse($myQr->expired_at)->translatedFormat('d F Y') }}
                            </span>
                        @else
                            <span><i class="fas fa-infinity mr-1"></i> Tanpa kedaluwarsa</span>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('owner.management.qr.detail', $me->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5
                              bg-white text-purple-700 rounded-xl font-bold text-sm
                              shadow-md hover:shadow-xl hover:-translate-y-0.5
                              active:scale-95 transition-all">
                        <i class="fas fa-eye"></i> Detail QR Saya
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- KARTU UTAMA — Manajemen QR --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">

            {{-- ─── HEADER ─────────────────────────────────────── --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-7">
                <div class="min-w-0">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-qrcode text-cyan-600 dark:text-cyan-400"></i>
                        QR Code Management
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Kelola QR Code anggota dan pengurus.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('owner.management.qr.generate') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-sm
                              shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                        <i class="fas fa-plus"></i> Generate QR
                    </a>

                    <a href="{{ route('owner.management.qr.recovery') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm
                              shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                        <i class="fas fa-undo-alt"></i> Pulihkan QR
                    </a>

                    <a href="{{ route('owner.identity-vault.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold text-sm
                              shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                        <i class="fas fa-vault"></i> Identity Vault
                    </a>

                    <form action="{{ route('owner.identity-vault.backup') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                                       bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm
                                       shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                            <i class="fas fa-database"></i> Backup Identitas
                        </button>
                    </form>
                </div>
            </div>

            {{-- ─── STATISTIK ───────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
                {{-- Belum QR --}}
                <div class="group relative overflow-hidden rounded-2xl p-5 border
                            bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800
                            shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 rounded-full
                                bg-gradient-to-br from-orange-400/10 to-transparent -mr-6 -mt-6"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-800/30
                                    flex items-center justify-center text-2xl text-orange-600 dark:text-orange-400">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Belum QR</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white tabular-nums">{{ $usersWithoutQr }}</p>
                            <p class="text-[10px] text-gray-400">User belum punya QR</p>
                        </div>
                    </div>
                </div>

                {{-- Sudah QR --}}
                <div class="group relative overflow-hidden rounded-2xl p-5 border
                            bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800
                            shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 rounded-full
                                bg-gradient-to-br from-emerald-400/10 to-transparent -mr-6 -mt-6"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-800/30
                                    flex items-center justify-center text-2xl text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sudah QR</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white tabular-nums">{{ $usersWithQr }}</p>
                            <p class="text-[10px] text-gray-400">User sudah punya QR</p>
                        </div>
                    </div>
                </div>

                {{-- QR Diblokir --}}
                <div class="group relative overflow-hidden rounded-2xl p-5 border
                            bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800
                            shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 rounded-full
                                bg-gradient-to-br from-rose-400/10 to-transparent -mr-6 -mt-6"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-800/30
                                    flex items-center justify-center text-2xl text-rose-600 dark:text-rose-400">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">QR Diblokir</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white tabular-nums">{{ $qrBlocked }}</p>
                            <p class="text-[10px] text-gray-400">QR status blocked</p>
                        </div>
                    </div>
                </div>

                {{-- Akun Dibekukan --}}
                <div class="group relative overflow-hidden rounded-2xl p-5 border
                            bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800
                            shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 rounded-full
                                bg-gradient-to-br from-amber-400/10 to-transparent -mr-6 -mt-6"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-800/30
                                    flex items-center justify-center text-2xl text-amber-600 dark:text-amber-400">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akun Beku</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white tabular-nums">{{ $usersBlocked }}</p>
                            <p class="text-[10px] text-gray-400">User tidak aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── FILTER ──────────────────────────────────────── --}}
            <div class="flex flex-wrap items-center gap-3 mb-5 p-3 rounded-xl
                        bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60">
                <form method="GET" action="{{ route('owner.management.qr.index') }}"
                      class="flex flex-wrap items-center gap-2 flex-1">
                    <label for="role" class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fas fa-filter text-cyan-500 text-xs"></i> Filter Role:
                    </label>
                    <select name="role" id="role"
                            class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                                   text-gray-900 dark:text-white px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition"
                            onchange="this.form.submit()">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <noscript>
                        <button type="submit" class="px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm">Filter</button>
                    </noscript>

                    @if(request('role'))
                        <a href="{{ route('owner.management.qr.index') }}"
                           class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-semibold">
                            <i class="fas fa-times-circle mr-1"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- ─── TABEL ───────────────────────────────────────── --}}
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700/60">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700/60 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-3 font-bold text-[11px] uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-3 font-bold text-[11px] uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 font-bold text-[11px] uppercase tracking-wider hidden md:table-cell">Email</th>
                            <th class="px-4 py-3 font-bold text-[11px] uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 font-bold text-[11px] uppercase tracking-wider">Status QR</th>
                            <th class="px-4 py-3 font-bold text-[11px] uppercase tracking-wider text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($qrUsers as $user)
                            <tr class="hover:bg-cyan-50/40 dark:hover:bg-cyan-900/10 transition-colors">
                                <td class="px-4 py-3 text-gray-400 tabular-nums">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500
                                                    text-white text-xs font-bold flex items-center justify-center
                                                    shadow-sm flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800 dark:text-white truncate">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 hidden md:table-cell">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200 rounded-full text-xs font-semibold">
                                        {{ $user->level->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->qrCodes->where('is_active', 1)->count() > 0)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
                                    @elseif($user->qrCodes->where('is_active', 0)->count() > 0)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400">
                                            <i class="fas fa-ban"></i> Diblokir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                                            <i class="fas fa-minus-circle"></i> Tidak ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('owner.management.qr.detail', $user->id) }}"
                                       class="inline-flex w-8 h-8 rounded-lg items-center justify-center
                                              text-cyan-600 dark:text-cyan-400
                                              hover:bg-cyan-50 dark:hover:bg-cyan-900/30 transition"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="w-16 h-16 mx-auto mb-3 rounded-full
                                                    bg-gray-100 dark:bg-gray-700
                                                    flex items-center justify-center">
                                            <i class="fas fa-qrcode text-2xl text-gray-400"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-1">Tidak ada data</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Tidak ada user dengan QR Code.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ─── PAGINATION ─────────────────────────────────── --}}
            @if($qrUsers->hasPages())
                <div class="mt-6">
                    {{ $qrUsers->withQueryString()->links() }}
                </div>
            @endif

            {{-- ─── INFO ───────────────────────────────────────── --}}
            <div class="mt-6 rounded-xl p-5 flex items-start gap-3
                        bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl mt-0.5 flex-shrink-0"></i>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <p class="font-semibold text-blue-800 dark:text-blue-200 mb-0.5">Tentang halaman ini</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        Halaman ini menampilkan daftar user yang sudah memiliki QR Code beserta statusnya.
                        Gunakan filter di atas untuk melihat berdasarkan role.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection