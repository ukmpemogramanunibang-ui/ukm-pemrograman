@extends('layouts.roles.owner')

@section('title', 'Permohonan Izin – UNICODE')

@section('content')

<div class="min-h-screen bg-slate-50 dark:bg-slate-950"
     x-data="permissionPage()">

    {{-- ============================================================
         HEADER
    ============================================================= --}}
    <div class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <div class="px-4 py-5 md:px-6 lg:px-8">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl
                                    bg-indigo-100 text-indigo-600
                                    dark:bg-indigo-500/15 dark:text-indigo-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 011.414.586l4.414 4.414A2 2 0 0119 9v10a2 2 0 01-2 2z"/>
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                                Permohonan Izin
                            </h1>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Kelola izin tidak hadir, izin terlambat, dan koreksi kehadiran anggota.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span>Absensi</span>
                        <span>/</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">Permohonan Izin</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('owner.attendance.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border
                              border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold
                              text-slate-700 shadow-sm transition hover:bg-slate-50
                              dark:border-slate-700 dark:bg-slate-800
                              dark:text-slate-200 dark:hover:bg-slate-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali
                    </a>

                    <button type="button" @click="refreshPage()"
                            class="inline-flex items-center gap-2 rounded-xl border
                                   border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold
                                   text-slate-700 shadow-sm transition hover:bg-slate-50
                                   dark:border-slate-700 dark:bg-slate-800
                                   dark:text-slate-200 dark:hover:bg-slate-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v6h6M20 20v-6h-6M5.64 9A7 7 0 0117.66 5.34L20 8M4 16l2.34 2.34A7 7 0 0018.36 15"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================================
         SUMMARY CARDS
    ============================================================= --}}
    <div class="px-4 py-6 md:px-6 lg:px-8">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">

            {{-- Pending --}}
            <a href="{{ route('owner.attendance.permissions.index', ['status' => 'pending']) }}"
               class="group rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition
                      hover:-translate-y-0.5 hover:shadow-md
                      dark:border-amber-500/20 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Menunggu Review
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                            {{ $stats['pending'] }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-amber-100 text-amber-600
                                dark:bg-amber-500/10 dark:text-amber-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M12 8v4l3 2"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-amber-600 dark:text-amber-400 font-medium">
                    Perlu ditindaklanjuti
                </div>
            </a>

            {{-- Today --}}
            <div class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm
                        dark:border-blue-500/20 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Pengajuan Hari Ini
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                            {{ $stats['today'] }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-blue-100 text-blue-600
                                dark:bg-blue-500/10 dark:text-blue-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Approved --}}
            <a href="{{ route('owner.attendance.permissions.index', ['status' => 'approved']) }}"
               class="group rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition
                      hover:-translate-y-0.5 hover:shadow-md
                      dark:border-emerald-500/20 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Disetujui
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                            {{ $stats['approved'] }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-emerald-100 text-emerald-600
                                dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Rejected --}}
            <a href="{{ route('owner.attendance.permissions.index', ['status' => 'rejected']) }}"
               class="group rounded-2xl border border-rose-200 bg-white p-5 shadow-sm transition
                      hover:-translate-y-0.5 hover:shadow-md
                      dark:border-rose-500/20 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Ditolak
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                            {{ $stats['rejected'] }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-rose-100 text-rose-600
                                dark:bg-rose-500/10 dark:text-rose-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Late --}}
            <div class="rounded-2xl border border-violet-200 bg-white p-5 shadow-sm
                        dark:border-violet-500/20 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Izin Terlambat
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                            {{ $stats['late'] }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-violet-100 text-violet-600
                                dark:bg-violet-500/10 dark:text-violet-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>


        {{-- ALERT --}}
        @if($stats['pending'] > 0)
            <div class="mt-6 flex flex-col gap-4 rounded-2xl border border-amber-200
                        bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between
                        dark:border-amber-500/20 dark:bg-amber-500/5">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center
                                rounded-xl bg-amber-100 text-amber-600
                                dark:bg-amber-500/15 dark:text-amber-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2"
                                  d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            <path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-900 dark:text-amber-300">
                            Ada {{ $stats['pending'] }} permohonan yang membutuhkan review.
                        </p>
                        <p class="mt-1 text-sm text-amber-800/80 dark:text-amber-200/70">
                            Segera tindak lanjuti agar rekonsiliasi absensi tidak terhambat.
                        </p>
                    </div>
                </div>
                <a href="{{ route('owner.attendance.permissions.index', ['status' => 'pending']) }}"
                   class="shrink-0 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold
                          text-white transition hover:bg-amber-600">
                    Tampilkan Pending
                </a>
            </div>
        @endif


        {{-- FILTER --}}
        <form method="GET" action="{{ route('owner.attendance.permissions.index') }}"
              class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm
                     dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end">

                <div class="flex-1">
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Cari
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Nama, NIM, atau alasan..."
                               class="w-full rounded-xl border border-slate-200 bg-slate-50
                                      py-2.5 pl-10 pr-4 text-sm outline-none transition
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                                      dark:border-slate-700 dark:bg-slate-800
                                      dark:text-white dark:placeholder:text-slate-500">
                    </div>
                </div>

                <div class="w-full xl:w-48">
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Status
                    </label>
                    <select name="status"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5
                                   text-sm outline-none focus:border-indigo-500 focus:ring-2
                                   focus:ring-indigo-500/20
                                   dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="all" @selected(request('status', 'all') === 'all')>Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                        <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                    </select>
                </div>

                <div class="w-full xl:w-56">
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Jenis
                    </label>
                    <select name="type"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5
                                   text-sm outline-none focus:border-indigo-500 focus:ring-2
                                   focus:ring-indigo-500/20
                                   dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="all" @selected(request('type', 'all') === 'all')>Semua Jenis</option>
                        <option value="excused_absent" @selected(request('type') === 'excused_absent')>Izin Tidak Hadir</option>
                        <option value="late_excused" @selected(request('type') === 'late_excused')>Izin Terlambat</option>
                        <option value="late_request" @selected(request('type') === 'late_request')>Pengajuan Terlambat</option>
                        <option value="correction" @selected(request('type') === 'correction')>Koreksi Kehadiran</option>
                    </select>
                </div>

                <div class="w-full xl:w-40">
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Terlambat
                    </label>
                    <select name="late"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5
                                   text-sm outline-none focus:border-indigo-500 focus:ring-2
                                   focus:ring-indigo-500/20
                                   dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua</option>
                        <option value="1" @selected(request('late') === '1')>Hanya Terlambat</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex h-[42px] items-center justify-center rounded-xl
                                   bg-indigo-600 px-5 text-sm font-semibold text-white
                                   shadow-sm transition hover:bg-indigo-500">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14 14.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 018 17v-2.586L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                    </button>

                    <a href="{{ route('owner.attendance.permissions.index') }}"
                       class="inline-flex h-[42px] items-center justify-center rounded-xl
                              border border-slate-200 px-4 text-sm font-semibold text-slate-600
                              transition hover:bg-slate-50
                              dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Reset
                    </a>
                </div>
            </div>
        </form>


        {{-- PRIORITY CARDS --}}
        @if($priority->count() > 0)
            <div class="mt-8">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            Permohonan Prioritas
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Pengajuan yang perlu segera diperiksa.
                        </p>
                    </div>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700
                                 dark:bg-amber-500/10 dark:text-amber-400">
                        {{ $stats['pending'] }} Pending
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach($priority as $perm)
                        @php
                            $initial = strtoupper(substr($perm->user->name ?? 'NA', 0, 2));
                            $typeLabel = match ($perm->permission_type) {
                                'excused_absent' => 'Izin Tidak Hadir',
                                'late_excused'   => 'Izin Terlambat',
                                'late_request'   => 'Pengajuan Terlambat',
                                'correction'     => 'Koreksi Kehadiran',
                                default          => ucfirst($perm->permission_type),
                            };
                            $priorityLevel = $perm->is_late_request ? 'Tinggi' : 'Sedang';
                            $priorityColor = $perm->is_late_request
                                ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'
                                : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';

                            // ⚡ Pre-compute data agar tidak pakai @json() di dalam atribut Alpine
                            $permData = [
                                'id'           => $perm->id,
                                'name'         => $perm->user->name ?? 'Unknown',
                                'nim'          => $perm->user->nim ?? '-',
                                'type'         => $typeLabel,
                                'activity'     => $perm->session->title ?? '-',
                                'date'         => $perm->created_at?->format('d M Y'),
                                'submitted'    => $perm->created_at?->format('H:i'),
                                'reason'       => $perm->reason,
                                'status'       => $perm->status,
                                'priority'     => $priorityLevel,
                                'evidence'     => $perm->evidence_path ? asset('storage/' . $perm->evidence_path) : null,
                                'review_notes' => $perm->review_notes,
                                'reviewer'     => $perm->reviewer->name ?? null,
                                'approve_url'  => route('owner.attendance.permissions.approve', $perm->id),
                                'reject_url'   => route('owner.attendance.permissions.reject', $perm->id),
                            ];
                        @endphp
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm
                                    transition hover:-translate-y-0.5 hover:shadow-md
                                    dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl
                                                bg-indigo-100 font-bold text-indigo-600
                                                dark:bg-indigo-500/15 dark:text-indigo-400">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white">
                                            {{ $perm->user->name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $perm->user->nim ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $priorityColor }}">
                                    {{ $priorityLevel }}
                                </span>
                            </div>

                            <div class="mt-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $typeLabel }}
                                </p>
                                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">
                                    {{ $perm->session->title ?? 'Sesi tidak diketahui' }}
                                </p>

                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <span class="text-slate-500">Diajukan</span>
                                        <p class="mt-1 font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $perm->created_at?->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Jam</span>
                                        <p class="mt-1 font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $perm->created_at?->format('H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 text-xs">
                                    <span class="text-slate-500">Alasan</span>
                                    <p class="mt-1 line-clamp-2 font-medium text-slate-700 dark:text-slate-300">
                                        {{ Str::limit($perm->reason, 80) }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                {{-- ✅ FIX: pakai Js::from() di dalam {{ }}, bukan @json() --}}
                                <button type="button"
                                        @click="openDetail({{ Js::from($permData) }})"
                                        class="flex-1 rounded-xl border border-slate-200 px-3 py-2.5
                                               text-xs font-bold text-slate-700 transition hover:bg-slate-50
                                               dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                    Detail
                                </button>

                                <form method="POST"
                                      action="{{ route('owner.attendance.permissions.approve', $perm) }}"
                                      class="flex-1"
                                      onsubmit="return confirm('Setujui permohonan izin ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="w-full rounded-xl bg-emerald-600 px-3 py-2.5
                                                   text-xs font-bold text-white transition hover:bg-emerald-700">
                                        Setujui
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        {{-- TABEL UTAMA --}}
        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                    dark:border-slate-800 dark:bg-slate-900">

            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4
                        sm:flex-row sm:items-center sm:justify-between
                        dark:border-slate-800">
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-white">Daftar Permohonan</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Total {{ $permissions->total() }} permohonan ditemukan.
                    </p>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    Halaman {{ $permissions->currentPage() }} dari {{ $permissions->lastPage() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500
                                  dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Pengguna</th>
                            <th class="px-5 py-4">Kegiatan</th>
                            <th class="px-5 py-4">Jenis</th>
                            <th class="px-5 py-4">Waktu</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($permissions as $perm)
                            @php
                                $initial = strtoupper(substr($perm->user->name ?? 'NA', 0, 2));
                                $typeLabel = match ($perm->permission_type) {
                                    'excused_absent' => 'Izin Tidak Hadir',
                                    'late_excused'   => 'Izin Terlambat',
                                    'late_request'   => 'Pengajuan Terlambat',
                                    'correction'     => 'Koreksi Kehadiran',
                                    default          => ucfirst($perm->permission_type),
                                };

                                $permData = [
                                    'id'           => $perm->id,
                                    'name'         => $perm->user->name ?? 'Unknown',
                                    'nim'          => $perm->user->nim ?? '-',
                                    'type'         => $typeLabel,
                                    'activity'     => $perm->session->title ?? '-',
                                    'date'         => $perm->created_at?->format('d M Y'),
                                    'submitted'    => $perm->created_at?->format('H:i'),
                                    'reason'       => $perm->reason,
                                    'status'       => $perm->status,
                                    'priority'     => $perm->is_late_request ? 'Tinggi' : 'Sedang',
                                    'evidence'     => $perm->evidence_path ? asset('storage/' . $perm->evidence_path) : null,
                                    'review_notes' => $perm->review_notes,
                                    'reviewer'     => $perm->reviewer->name ?? null,
                                    'approve_url'  => route('owner.attendance.permissions.approve', $perm->id),
                                    'reject_url'   => route('owner.attendance.permissions.reject', $perm->id),
                                ];
                            @endphp
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center
                                                    rounded-xl bg-indigo-100 text-xs font-bold text-indigo-600
                                                    dark:bg-indigo-500/15 dark:text-indigo-400">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">
                                                {{ $perm->user->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                NIM: {{ $perm->user->nim ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-800 dark:text-slate-200">
                                        {{ $perm->session->title ?? '-' }}
                                    </p>
                                    @if($perm->is_late_request)
                                        <span class="mt-1 inline-block text-[10px] font-bold
                                                     rounded-full bg-rose-100 px-2 py-0.5
                                                     text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                                            Terlambat
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-1
                                                 text-xs font-semibold text-violet-700
                                                 dark:bg-violet-500/10 dark:text-violet-400">
                                        {{ $typeLabel }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ $perm->created_at?->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $perm->created_at?->format('H:i') }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    @if($perm->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full
                                                     bg-amber-100 px-2.5 py-1 text-xs font-bold
                                                     text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @elseif($perm->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full
                                                     bg-emerald-100 px-2.5 py-1 text-xs font-bold
                                                     text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full
                                                     bg-rose-100 px-2.5 py-1 text-xs font-bold
                                                     text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        {{-- ✅ FIX: Js::from() di dalam {{ }} --}}
                                        <button type="button"
                                                @click="openDetail({{ Js::from($permData) }})"
                                                class="rounded-lg border border-slate-200 px-3 py-2
                                                       text-xs font-semibold text-slate-600
                                                       transition hover:bg-slate-50
                                                       dark:border-slate-700 dark:text-slate-300
                                                       dark:hover:bg-slate-800">
                                            Detail
                                        </button>

                                        @if($perm->status === 'pending')
                                            <form method="POST"
                                                  action="{{ route('owner.attendance.permissions.approve', $perm) }}"
                                                  onsubmit="return confirm('Setujui permohonan izin ini?')">
                                                @csrf
                                                <button type="submit"
                                                        class="rounded-lg bg-emerald-600 px-3 py-2
                                                               text-xs font-semibold text-white
                                                               hover:bg-emerald-700">
                                                    Setujui
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-16 w-16 items-center justify-center
                                                    rounded-full bg-slate-100 dark:bg-slate-800">
                                            <svg class="h-8 w-8 text-slate-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 011.414.586l4.414 4.414A2 2 0 0119 9v10a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="mt-4 font-semibold text-slate-700 dark:text-slate-300">
                                            Tidak ada permohonan izin
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            Semua permohonan sudah ditindaklanjuti.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($permissions->hasPages())
                <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">
                    {{ $permissions->links() }}
                </div>
            @endif
        </div>

    </div>


    {{-- MODAL DETAIL --}}
    <div x-show="detailOpen"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
         @keydown.escape.window="detailOpen = false">

        <div x-show="detailOpen"
             x-transition
             @click.outside="detailOpen = false"
             class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl
                    dark:bg-slate-900">

            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5
                        dark:border-slate-800">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-500">
                        Detail Permohonan
                    </p>
                    <h3 class="mt-1 text-xl font-bold text-slate-900 dark:text-white"
                        x-text="selected?.name ?? '-'">
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                       x-text="'NIM: ' + (selected?.nim ?? '-')">
                    </p>
                </div>
                <button @click="detailOpen = false"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-100
                               hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="max-h-[70vh] overflow-y-auto p-6">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                        <p class="text-xs text-slate-500">Kegiatan</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-white"
                           x-text="selected?.activity ?? '-'">
                        </p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                        <p class="text-xs text-slate-500">Jenis</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-white"
                           x-text="selected?.type ?? '-'">
                        </p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                        <p class="text-xs text-slate-500">Waktu Pengajuan</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-white"
                           x-text="(selected?.date ?? '-') + ' • ' + (selected?.submitted ?? '-')">
                        </p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="mt-1 font-semibold text-amber-600"
                           x-text="selected?.status ?? 'pending'">
                        </p>
                    </div>
                </div>

                <template x-if="selected?.evidence">
                    <div class="mt-6">
                        <h4 class="font-bold text-slate-900 dark:text-white">Bukti</h4>
                        <a :href="selected.evidence" target="_blank"
                           class="mt-3 inline-flex items-center gap-2 rounded-xl border
                                  border-slate-200 px-4 py-2.5 text-sm font-semibold text-indigo-600
                                  hover:bg-indigo-50 dark:border-slate-700 dark:text-indigo-400
                                  dark:hover:bg-indigo-500/10">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat Bukti
                        </a>
                    </div>
                </template>

                <div class="mt-6">
                    <h4 class="font-bold text-slate-900 dark:text-white">Alasan</h4>
                    <div class="mt-3 rounded-2xl border border-slate-200 p-4
                                dark:border-slate-700">
                        <p class="text-sm leading-6 text-slate-600 dark:text-slate-300"
                           x-text="selected?.reason ?? 'Belum ada keterangan.'">
                        </p>
                    </div>
                </div>

                <template x-if="selected?.review_notes">
                    <div class="mt-6">
                        <h4 class="font-bold text-slate-900 dark:text-white">Catatan Review</h4>
                        <div class="mt-3 rounded-2xl border border-slate-200 p-4
                                    dark:border-slate-700">
                            <p class="text-sm leading-6 text-slate-600 dark:text-slate-300"
                               x-text="selected.review_notes">
                            </p>
                            <p class="mt-2 text-xs text-slate-500"
                               x-text="'Oleh: ' + (selected.reviewer ?? 'Admin')">
                            </p>
                        </div>
                    </div>
                </template>

                <template x-if="selected?.status === 'pending'">
                    <div class="mt-6">
                        <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-white">
                            Catatan Review (wajib untuk tolak)
                        </label>
                        <textarea x-model="reviewNotes" rows="3"
                                  placeholder="Tambahkan catatan persetujuan / penolakan..."
                                  class="w-full rounded-2xl border border-slate-200 bg-slate-50
                                         px-4 py-3 text-sm outline-none transition
                                         focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                                         dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        </textarea>
                    </div>
                </template>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-5
                        sm:flex-row sm:justify-end
                        dark:border-slate-800">
                <button @click="detailOpen = false"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold
                               text-slate-700 hover:bg-slate-50
                               dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Tutup
                </button>

                <template x-if="selected?.status === 'pending'">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row">
                        <form :action="selected.reject_url" method="POST"
                              onsubmit="return confirm('Tolak permohonan izin ini?')">
                            @csrf
                            <input type="hidden" name="review_notes" :value="reviewNotes">
                            <button type="submit"
                                    :disabled="!reviewNotes || reviewNotes.length < 5"
                                    class="w-full rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold
                                           text-white hover:bg-rose-700 disabled:opacity-50
                                           disabled:cursor-not-allowed sm:w-auto">
                                Tolak
                            </button>
                        </form>

                        <form :action="selected.approve_url" method="POST"
                              onsubmit="return confirm('Setujui permohonan izin ini?')">
                            @csrf
                            <input type="hidden" name="review_notes" :value="reviewNotes">
                            <button type="submit"
                                    class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold
                                           text-white hover:bg-emerald-700 sm:w-auto">
                                Setujui Permohonan
                            </button>
                        </form>
                    </div>
                </template>
            </div>

        </div>
    </div>

</div>


{{-- ============================================================
     ALPINE JS
============================================================ --}}
<script>
function permissionPage() {
    return {
        detailOpen: false,
        selected: null,
        reviewNotes: '',

        openDetail(data) {
            this.selected = data;
            this.reviewNotes = '';
            this.detailOpen = true;
        },

        refreshPage() {
            window.location.reload();
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@endsection