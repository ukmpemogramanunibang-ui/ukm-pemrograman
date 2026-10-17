@extends('layouts.roles.ketua')

@section('page-title', 'Detail QR Credential')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shimmer {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }
    @keyframes pulseSoft {
        0%, 100% { opacity: 1; }
        50%      { opacity: .55; }
    }
    .animate-fadeInUp { animation: fadeInUp .4s cubic-bezier(.16,1,.3,1) both; }
    .animate-pulseSoft { animation: pulseSoft 2s ease-in-out infinite; }

    /* ── Selection row highlight ── */
    .qr-row {
        position: relative;
        transition: background-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .qr-row::before {
        content: '';
        position: absolute;
        inset: 0;
        left: 0;
        width: 3px;
        background: transparent;
        transition: background .18s ease;
    }
    .qr-row:hover { background-color: rgba(139, 92, 246, .045); }
    .dark .qr-row:hover { background-color: rgba(139, 92, 246, .08); }
    .qr-row.is-selected {
        background-color: rgba(16, 185, 129, .07);
    }
    .qr-row.is-selected::before { background: linear-gradient(180deg,#10b981,#059669); }
    .dark .qr-row.is-selected { background-color: rgba(16, 185, 129, .10); }

    .qr-row.is-blocked { opacity: .72; }
    .qr-row.is-blocked:hover { opacity: 1; }

    /* ── Sticky right panel ── */
    @media (min-width: 1024px) {
        .sticky-panel {
            position: sticky;
            top: 5.5rem;
            align-self: flex-start;
        }
    }

    /* ── Download button state ── */
    .qr-download-btn:not(:disabled):hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(139, 92, 246, .28);
    }
    .qr-download-btn:not(:disabled):active { transform: translateY(0) scale(.97); }

    /* ── QR preview frame ── */
    .qr-preview-frame {
        background:
            linear-gradient(135deg, rgba(255,255,255,.5), rgba(255,255,255,.15)),
            repeating-conic-gradient(rgba(0,0,0,.04) 0% 25%, transparent 0% 50%) 50% / 16px 16px;
    }
    .dark .qr-preview-frame {
        background:
            linear-gradient(135deg, rgba(255,255,255,.04), rgba(255,255,255,.01)),
            repeating-conic-gradient(rgba(255,255,255,.04) 0% 25%, transparent 0% 50%) 50% / 16px 16px;
    }

    /* ── Custom checkbox ── */
    .qr-checkbox, #selectAll {
        appearance: none;
        -webkit-appearance: none;
        width: 1.05rem;
        height: 1.05rem;
        border: 2px solid #cbd5e1;
        border-radius: .375rem;
        cursor: pointer;
        position: relative;
        transition: all .15s ease;
        background: white;
        flex-shrink: 0;
    }
    .dark .qr-checkbox, .dark #selectAll {
        border-color: #475569;
        background: #1e293b;
    }
    .qr-checkbox:hover, #selectAll:hover { border-color: #a78bfa; }
    .qr-checkbox:checked, #selectAll:checked,
    #selectAll:indeterminate {
        background: linear-gradient(135deg,#10b981,#059669);
        border-color: #059669;
    }
    .qr-checkbox:checked::after {
        content: '';
        position: absolute;
        left: 4px; top: 1px;
        width: 5px; height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    #selectAll:checked::after {
        content: '';
        position: absolute;
        left: 4px; top: 1px;
        width: 5px; height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    #selectAll:indeterminate::after {
        content: '';
        position: absolute;
        left: 2px; top: 6px;
        width: 9px; height: 2px;
        background: white;
        border-radius: 1px;
    }

    /* ── Badge status ── */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .6rem;
        border-radius: 9999px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .02em;
        border: 1px solid transparent;
    }
    .badge-active {
        background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(5,150,105,.08));
        color: #047857;
        border-color: rgba(16,185,129,.3);
    }
    .dark .badge-active { color: #6ee7b7; }
    .badge-revoked {
        background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08));
        color: #b91c1c;
        border-color: rgba(239,68,68,.3);
    }
    .dark .badge-revoked { color: #fca5a5; }
    .badge-inactive {
        background: linear-gradient(135deg, rgba(245,158,11,.12), rgba(217,119,6,.08));
        color: #b45309;
        border-color: rgba(245,158,11,.3);
    }
    .dark .badge-inactive { color: #fcd34d; }

    /* ── Scrollbar halus di tabel ── */
    .qr-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
    .qr-scroll::-webkit-scrollbar-track { background: transparent; }
    .qr-scroll::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, .35);
        border-radius: 9999px;
    }
    .qr-scroll::-webkit-scrollbar-thumb:hover { background: rgba(148,163,184,.55); }
</style>
@endpush

@section('content')

@php
    $activeQr = $user->qrCodes
        ->where('is_active', true)
        ->where('is_revoked', false)
        ->first();

    $totalQr   = $user->qrCodes->count();
    $activeCt  = $user->qrCodes->where('is_active', true)->where('is_revoked', false)->count();
    $revokedCt = $user->qrCodes->where('is_revoked', true)->count();
    $inactiveCt= $totalQr - $activeCt - $revokedCt;

    $initials = strtoupper(collect(explode(' ', $user->name))
        ->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));

    $isSelf   = auth()->id() === $user->id;
@endphp

<div class="space-y-5 sm:space-y-6">

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HERO HEADER --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl shadow-xl
                bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 text-white">

        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image:
                    radial-gradient(circle at 20% 20%, white 1px, transparent 1px),
                    radial-gradient(circle at 80% 60%, white 1px, transparent 1px);
                    background-size: 32px 32px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-fuchsia-400/20 rounded-full blur-3xl"></div>

        <div class="relative p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            {{-- Left: back + breadcrumb + title --}}
            <div class="flex items-start gap-4 min-w-0">
                <@php
    $backUrl = $isSelf
        ? (\Illuminate\Support\Facades\Route::has('ketua.profile.index')
            ? route('ketua.profile.index')
            : (\Illuminate\Support\Facades\Route::has('ketua.profile')
                ? route('ketua.profile')
                : url('/ketua/profile')))
        : route('ketua.management.qr.index');
    $backLabel = $isSelf ? 'Kembali ke Profil' : 'Kembali ke Daftar QR';
@endphp

<a href="{{ $backUrl }}"
   class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25
          flex items-center justify-center flex-shrink-0
          hover:bg-white/25 active:scale-95 transition-all"
   title="{{ $backLabel }}">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-white/70">
    <i class="fas fa-qrcode"></i>
    <span>QR Credential</span>
    <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
    <span>Detail</span>

    @if($isSelf)
        <span class="px-2 py-0.5 rounded-full bg-emerald-400/25 border border-emerald-300/50 text-white">
            <i class="fas fa-user-shield mr-1"></i> Akun Saya
        </span>

        {{-- Link kembali ke profil --}}
        <a href="{{ route('ketua.profile.index', [], false) ?: url('/ketua/profile') }}"
           class="ml-1 px-2 py-0.5 rounded-full bg-white/20 hover:bg-white/30
                  border border-white/30 transition-all normal-case tracking-normal
                  flex items-center gap-1">
            <i class="fas fa-user-circle text-[9px]"></i>
            Buka Profil
        </a>
    @endif
</div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-white/70">
                        <i class="fas fa-qrcode"></i>
                        <span>QR Credential</span>
                        <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
                        <span>Detail</span>
                        @if($isSelf)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-400/25 border border-emerald-300/50 text-white">
                                <i class="fas fa-user-shield mr-1"></i> Akun Saya
                            </span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight mt-1 truncate">
                        Detail QR Credential
                    </h1>
                    <p class="text-white/75 text-xs sm:text-sm mt-0.5 max-w-lg">
                        Informasi lengkap credential QR — {{ $user->name }}
                    </p>
                </div>
            </div>

            {{-- Right: stat pills --}}
            <div class="flex flex-wrap items-center gap-2 sm:flex-shrink-0">
                <div class="px-3 py-2 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 text-center min-w-[68px]">
                    <div class="text-xl font-black tabular-nums">{{ $activeCt }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider text-white/70">Aktif</div>
                </div>
                <div class="px-3 py-2 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 text-center min-w-[68px]">
                    <div class="text-xl font-black tabular-nums">{{ $revokedCt }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider text-white/70">Dicabut</div>
                </div>
                <div class="px-3 py-2 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 text-center min-w-[68px]">
                    <div class="text-xl font-black tabular-nums">{{ $totalQr }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-wider text-white/70">Total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- USER PROFILE CARD --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                p-5 shadow-sm animate-fadeInUp">
        <div class="flex flex-col sm:flex-row items-start gap-5">
            <div class="relative flex-shrink-0">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700
                            flex items-center justify-center text-white text-xl font-black shadow-lg">
                    {{ $initials ?: strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @if($user->is_active)
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-400
                                 border-2 border-white dark:border-gray-800"></span>
                @endif
            </div>

            <div class="flex-1 min-w-0 w-full">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-lg font-black text-gray-800 dark:text-white truncate">
                        {{ $user->name }}
                    </h2>
                    <span class="badge-status {{ $user->is_active ? 'badge-active' : 'badge-revoked' }}">
                        <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                        {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                    @if($user->level)
                        <span class="px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/30
                                     text-violet-700 dark:text-violet-300 text-[10px] font-bold uppercase tracking-wider">
                            {{ $user->level->name }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2 text-sm">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fas fa-hashtag text-xs text-gray-400 w-4 flex-shrink-0"></i>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">NIM</span>
                        <span class="font-mono text-gray-800 dark:text-gray-200 truncate ml-auto">{{ $user->nim ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fas fa-envelope text-xs text-gray-400 w-4 flex-shrink-0"></i>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Email</span>
                        <span class="text-gray-800 dark:text-gray-200 truncate ml-auto">{{ $user->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fas fa-id-badge text-xs text-gray-400 w-4 flex-shrink-0"></i>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Member #</span>
                        <span class="font-mono text-gray-800 dark:text-gray-200 truncate ml-auto">{{ $user->member_number ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fas fa-calendar-plus text-xs text-gray-400 w-4 flex-shrink-0"></i>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Bergabung</span>
                        <span class="text-gray-800 dark:text-gray-200 truncate ml-auto">
                            {{ optional($user->created_at)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MAIN GRID --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start">

        {{-- ═══ KOLOM KIRI: TABEL QR ═══ --}}
        <div class="lg:col-span-2 space-y-4 min-w-0">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden animate-fadeInUp">

                {{-- Header tabel --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700
                            flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/30
                                    flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-list text-violet-600 dark:text-violet-400 text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-gray-800 dark:text-white">
                                Daftar QR Credential
                            </h3>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                {{ $totalQr }} credential terdaftar
                            </p>
                        </div>
                    </div>

                    <button type="button" onclick="deleteSelected()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                   bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400
                                   text-xs font-bold border border-rose-200 dark:border-rose-800
                                   hover:bg-rose-100 dark:hover:bg-rose-900/30
                                   active:scale-95 transition-all">
                        <i class="fas fa-trash-alt"></i>
                        Hapus Terpilih
                    </button>
                </div>

                <form id="deleteSelectedForm"
                      action="{{ route('ketua.management.qr.delete-selected') }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="selectedIds" value="">

                    <div class="overflow-x-auto qr-scroll">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/40
                                           border-b border-gray-100 dark:border-gray-700/60">
                                    <th class="px-3 py-3 w-10">
                                        <input type="checkbox" id="selectAll" aria-label="Pilih semua">
                                    </th>
                                    <th class="text-left px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                                    <th class="text-left px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">UUID</th>
                                    <th class="text-left px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="text-left px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden md:table-cell">Dibuat</th>
                                    <th class="text-left px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden lg:table-cell">Kadaluarsa</th>
                                    <th class="text-left px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Scan</th>
                                    <th class="text-right px-3 py-3 font-bold text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                @forelse($user->qrCodes as $qr)
                                    @php
                                        $statusKey = $qr->is_active && !$qr->is_revoked
                                            ? 'active'
                                            : ($qr->is_revoked ? 'revoked' : 'inactive');
                                        $isActiveRow = $statusKey === 'active';
                                    @endphp
                                    <tr class="qr-row cursor-pointer {{ !$isActiveRow ? 'is-blocked' : '' }}"
                                        data-qr-id="{{ $qr->id }}"
                                        data-status="{{ $statusKey }}">

                                        <td class="px-3 py-3 text-center">
                                            <input type="checkbox"
                                                   class="qr-checkbox"
                                                   value="{{ $qr->id }}"
                                                   data-status="{{ $statusKey }}"
                                                   aria-label="Pilih QR {{ $qr->qr_uuid }}">
                                        </td>

                                        <td class="px-3 py-3 text-gray-400 tabular-nums text-xs">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="px-3 py-3">
                                            <code class="font-mono text-[11px] text-gray-700 dark:text-gray-300
                                                         bg-gray-100 dark:bg-gray-700/60
                                                         px-2 py-1 rounded-md
                                                         inline-block max-w-[180px] truncate"
                                                  title="{{ $qr->qr_uuid }}">
                                                {{ \Illuminate\Support\Str::limit($qr->qr_uuid, 18) }}
                                            </code>
                                        </td>

                                        <td class="px-3 py-3">
                                            @if($statusKey === 'active')
                                                <span class="badge-status badge-active">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulseSoft"></span>
                                                    Aktif
                                                </span>
                                            @elseif($statusKey === 'revoked')
                                                <span class="badge-status badge-revoked">
                                                    <i class="fas fa-ban text-[9px]"></i> Dicabut
                                                </span>
                                            @else
                                                <span class="badge-status badge-inactive">
                                                    <i class="fas fa-pause-circle text-[9px]"></i> Tidak Aktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                            {{ $qr->generated_at ? $qr->generated_at->format('d M Y') : '-' }}
                                            @if($qr->generated_at)
                                                <div class="text-[10px] text-gray-400">
                                                    {{ $qr->generated_at->diffForHumans() }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                                            {{ $qr->expired_at ? $qr->expired_at->format('d M Y') : '∞ Tidak terbatas' }}
                                        </td>

                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center gap-1 text-xs font-bold tabular-nums
                                                         text-violet-600 dark:text-violet-400">
                                                <i class="fas fa-chart-simple text-[9px]"></i>
                                                {{ $qr->scan_count ?? 0 }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3 text-right" onclick="event.stopPropagation()">
                                            <div class="inline-flex items-center gap-1">
                                                @if($statusKey === 'active')
                                                    <form action="{{ route('ketua.management.qr.revoke', $user->id) }}"
                                                          method="POST" class="inline-block"
                                                          onsubmit="return confirm('Cabut QR ini secara permanen?')">
                                                        @csrf
                                                        <button type="submit"
                                                                title="Cabut QR"
                                                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                                                       text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/30
                                                                       transition">
                                                            <i class="fas fa-ban text-xs"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <button type="button"
                                                        onclick="deleteSingle({{ $qr->id }})"
                                                        title="Hapus QR"
                                                        class="w-8 h-8 rounded-lg flex items-center justify-center
                                                               text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30
                                                               transition">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-16 text-center">
                                            <div class="max-w-xs mx-auto">
                                                <div class="w-16 h-16 mx-auto mb-3 rounded-full
                                                            bg-violet-100 dark:bg-violet-900/30
                                                            flex items-center justify-center">
                                                    <i class="fas fa-qrcode text-2xl text-violet-400"></i>
                                                </div>
                                                <h4 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-1">
                                                    Belum ada QR Code
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                                    User ini belum memiliki credential QR.
                                                </p>
                                                <a href="{{ route('ketua.management.qr.generate') }}"
                                                   class="inline-flex items-center gap-1.5 px-4 py-2
                                                          bg-violet-600 text-white rounded-lg text-xs font-bold
                                                          hover:bg-violet-700 active:scale-95 transition">
                                                    <i class="fas fa-plus"></i> Generate QR
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══ KOLOM KANAN: STICKY PREVIEW ═══ --}}
        <div class="lg:col-span-1 sticky-panel space-y-4 animate-fadeInUp">

            {{-- Preview card --}}
            @if($activeQr)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                            shadow-sm overflow-hidden">

                    {{-- Header --}}
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700
                                flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700
                                    flex items-center justify-center shadow-sm">
                            <i class="fas fa-qrcode text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-bold text-sm text-gray-800 dark:text-white">
                                    QR Aktif
                                </h4>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulseSoft"></span>
                            </div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">
                                Preview credential
                            </p>
                        </div>
                    </div>

                    {{-- QR Image --}}
                    <div class="p-5">
                        <div class="qr-preview-frame rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
                            @php
                                $qrExists = $activeQr->qr_image
                                    && file_exists(public_path('storage/' . $activeQr->qr_image));
                            @endphp
                            @if($qrExists)
                                <img src="{{ asset('storage/' . $activeQr->qr_image) }}"
                                     alt="QR Code {{ $user->name }}"
                                     class="w-full h-auto rounded-lg bg-white p-2 shadow-sm"
                                     style="aspect-ratio: 1/1; object-fit: contain;">
                            @else
                                <div class="aspect-square rounded-lg bg-gray-100 dark:bg-gray-700/50
                                            flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <p class="text-xs">Gambar QR belum tersedia</p>
                                </div>
                            @endif
                        </div>

                        {{-- Meta info --}}
                        <div class="mt-4 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fas fa-fingerprint text-[10px]"></i> UUID
                                </span>
                                <code class="font-mono text-[10px] text-gray-700 dark:text-gray-300
                                             bg-gray-100 dark:bg-gray-700/60 px-1.5 py-0.5 rounded truncate max-w-[150px]"
                                      title="{{ $activeQr->qr_uuid }}">
                                    {{ \Illuminate\Support\Str::limit($activeQr->qr_uuid, 16) }}
                                </code>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fas fa-key text-[10px]"></i> Token
                                </span>
                                <code class="font-mono text-[10px] text-gray-700 dark:text-gray-300
                                             bg-gray-100 dark:bg-gray-700/60 px-1.5 py-0.5 rounded truncate max-w-[150px]">
                                    {{ substr($activeQr->token, 0, 14) }}…
                                </code>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fas fa-calendar-plus text-[10px]"></i> Dibuat
                                </span>
                                <span class="text-gray-700 dark:text-gray-300 font-semibold">
                                    {{ $activeQr->generated_at?->format('d M Y') ?? '-' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fas fa-hourglass-end text-[10px]"></i> Kadaluarsa
                                </span>
                                <span class="text-gray-700 dark:text-gray-300 font-semibold">
                                    {{ $activeQr->expired_at?->format('d M Y') ?? 'Tidak terbatas' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fas fa-chart-simple text-[10px]"></i> Total Scan
                                </span>
                                <span class="text-gray-700 dark:text-gray-300 font-bold tabular-nums">
                                    {{ $activeQr->scan_count ?? 0 }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fas fa-clock text-[10px]"></i> Scan terakhir
                                </span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ $activeQr->last_used_at?->diffForHumans() ?? 'Belum pernah' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty active QR --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                            p-8 text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl
                                bg-gradient-to-br from-amber-100 to-orange-100
                                dark:from-amber-900/30 dark:to-orange-900/30
                                flex items-center justify-center">
                        <i class="fas fa-qrcode text-2xl text-amber-500"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 dark:text-white text-sm mb-1">
                        Tidak ada QR aktif
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">
                        Semua QR sudah dicabut atau belum pernah dibuat.
                    </p>
                    <a href="{{ route('ketua.management.qr.generate') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2
                              bg-violet-600 text-white rounded-lg text-xs font-bold
                              hover:bg-violet-700 active:scale-95 transition">
                        <i class="fas fa-plus"></i> Generate QR
                    </a>
                </div>
            @endif

            {{-- ═══ DOWNLOAD / ROTATE PANEL ═══ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">

                {{-- Selection info --}}
                <div id="selectionInfo"
                     class="px-4 py-2.5 text-xs text-center
                            bg-slate-50 dark:bg-slate-800/60
                            border-b border-slate-200 dark:border-slate-700
                            text-slate-600 dark:text-slate-300 transition-all">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    <span>Belum ada QR dipilih</span>
                </div>

                {{-- Download --}}
                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider
                                  text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1.5">
                            <i class="fas fa-download text-blue-500"></i>
                            Download QR Terpilih
                        </p>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach(['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'] as $fmt)
                                <button type="button"
                                        data-format="{{ $fmt }}"
                                        class="qr-download-btn
                                               py-2 rounded-lg text-[11px] font-bold
                                               bg-gradient-to-br from-violet-500 to-purple-600 text-white
                                               hover:from-violet-600 hover:to-purple-700
                                               disabled:from-slate-200 disabled:to-slate-200 disabled:text-slate-400
                                               dark:disabled:from-slate-700 dark:disabled:to-slate-700 dark:disabled:text-slate-500
                                               disabled:cursor-not-allowed disabled:shadow-none
                                               shadow-sm transition-all"
                                        disabled>
                                    <i class="fas fa-file-arrow-down text-[10px] mr-1"></i>
                                    {{ strtoupper($fmt) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rotate & history --}}
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2">
                        <p class="text-[11px] font-bold uppercase tracking-wider
                                  text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                            <i class="fas fa-sync-alt text-purple-500"></i>
                            Rotate & Riwayat
                        </p>

                        <form action="{{ route('ketua.management.qr.rotate', $user->id) }}"
                              method="POST"
                              onsubmit="return confirm('Rotate QR sekarang?\n\nQR LAMA akan DICABUT.\nQR BARU akan dibuat dan langsung aktif.')">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5
                                           px-4 py-2.5 rounded-xl
                                           bg-gradient-to-r from-purple-600 to-fuchsia-600
                                           text-white text-xs font-bold shadow-sm
                                           hover:from-purple-700 hover:to-fuchsia-700
                                           hover:shadow-lg active:scale-95 transition-all">
                                <i class="fas fa-redo"></i>
                                Rotate QR (Ganti Baru)
                            </button>
                        </form>

                        <a href="{{ route('ketua.management.qr.history', $user->id) }}"
                           class="w-full inline-flex items-center justify-center gap-1.5
                                  px-4 py-2.5 rounded-xl
                                  bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200
                                  text-xs font-bold
                                  hover:bg-gray-200 dark:hover:bg-gray-600
                                  active:scale-95 transition-all">
                            <i class="fas fa-history"></i>
                            Lihat Riwayat QR
                        </a>
                    </div>

                    {{-- Repair (opsional) --}}
                    @if(Route::has('ketua.management.qr.repair') && $activeQr)
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                            <form action="{{ route('ketua.management.qr.repair', $activeQr->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Perbaiki QR ini? (credential tetap sama)')">
                                @csrf
                                <input type="hidden" name="format" value="png">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5
                                               px-4 py-2.5 rounded-xl
                                               bg-amber-100 dark:bg-amber-900/30
                                               text-amber-700 dark:text-amber-300
                                               text-xs font-bold
                                               hover:bg-amber-200 dark:hover:bg-amber-900/50
                                               active:scale-95 transition-all">
                                    <i class="fas fa-wrench"></i>
                                    Perbaiki Gambar QR
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info card --}}
            <div class="rounded-2xl border border-blue-200 dark:border-blue-900
                        bg-gradient-to-br from-blue-50 to-cyan-50
                        dark:from-blue-900/20 dark:to-cyan-900/20 p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-500
                                flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <div class="text-xs text-blue-800 dark:text-blue-200 leading-relaxed">
                        <p class="font-bold mb-0.5">Prinsip Keamanan QR</p>
                        <p class="text-blue-700/90 dark:text-blue-300/80">
                            QR hanya berisi credential UUID. Data user selalu diambil dari database saat scan —
                            jadi perubahan nama, NIM, atau role <strong>tidak perlu cetak ulang QR</strong>.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ══════════════════════════════════════════════════════════════
   TOAST
   ══════════════════════════════════════════════════════════════ */
function showToast(message, duration = 3500, type = 'info') {
    let el = document.getElementById('qr-toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'qr-toast';
        el.style.cssText = `
            position: fixed; bottom: 24px; left: 50%;
            transform: translateX(-50%) translateY(20px);
            padding: 12px 20px; border-radius: 14px;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 12px 32px rgba(0,0,0,.35);
            z-index: 99999; opacity: 0;
            transition: all .3s cubic-bezier(.16,1,.3,1);
            max-width: 90vw; text-align: center;
            color: #fff; background: #0f172a;
        `;
        document.body.appendChild(el);
    }
    const colors = {
        info: '#0f172a', success: '#059669',
        error: '#dc2626', warning: '#d97706',
    };
    el.style.background = colors[type] || colors.info;
    el.textContent = message;
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateX(-50%) translateY(0)';
    });
    clearTimeout(el._timer);
    el._timer = setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(-50%) translateY(20px)';
    }, duration);
}

/* ══════════════════════════════════════════════════════════════
   DELETE HELPERS
   ══════════════════════════════════════════════════════════════ */
function deleteSelected() {
    const ids = Array.from(document.querySelectorAll('.qr-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) {
        showToast('Pilih minimal satu QR untuk dihapus.', 2800, 'warning');
        return;
    }
    if (!confirm(`Hapus ${ids.length} QR yang dipilih?\n\nTindakan ini tidak bisa dibatalkan.`)) return;
    document.getElementById('selectedIds').value = JSON.stringify(ids);
    document.getElementById('deleteSelectedForm').submit();
}

function deleteSingle(id) {
    if (!confirm('Hapus QR ini secara permanen?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('ketua.management.qr.delete-selected') }}";
    form.innerHTML = `@csrf @method('DELETE') <input type="hidden" name="ids" value='[${id}]'>`;
    document.body.appendChild(form);
    form.submit();
}

/* ══════════════════════════════════════════════════════════════
   MAIN
   ══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    const $tbody       = document.querySelector('table tbody');
    const selectAll    = document.getElementById('selectAll');
    const downloadBtns = document.querySelectorAll('.qr-download-btn');
    const infoBox      = document.getElementById('selectionInfo');
    const baseUrl      = '{{ url("ketua/management/qr/download-qr") }}';

    const getCheckboxes = () => Array.from(document.querySelectorAll('.qr-checkbox'));
    const getRows       = () => Array.from(document.querySelectorAll('.qr-row'));
    const getChecked    = () => getCheckboxes().filter(cb => cb.checked);

    /* ---------- UPDATE UI ---------- */
    function updateUI() {
        const checked = getChecked();
        const all     = getCheckboxes();
        const total   = checked.length;

        // Highlight baris
        getRows().forEach(row => {
            const cb = row.querySelector('.qr-checkbox');
            if (cb && cb.checked) {
                row.classList.add('is-selected');
            } else {
                row.classList.remove('is-selected');
            }
        });

        // Select all state
        if (selectAll) {
            selectAll.checked       = total > 0 && total === all.length;
            selectAll.indeterminate = total > 0 && total < all.length;
        }

        // Blokir logic
        const hasBlocked  = checked.some(cb => cb.dataset.status !== 'active');
        const canDownload = total > 0 && !hasBlocked;

        // Tombol download
        downloadBtns.forEach(btn => {
            btn.disabled = !canDownload;
            btn.title = hasBlocked
                ? 'QR dicabut / tidak aktif tidak bisa di-download'
                : (total === 0 ? 'Pilih QR dulu' : 'Klik untuk download');
        });

        // Info box
        if (infoBox) {
            if (hasBlocked) {
                infoBox.className = 'px-4 py-2.5 text-xs text-center bg-rose-50 dark:bg-rose-900/20 border-b border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 transition-all';
                infoBox.innerHTML = `<i class="fas fa-ban"></i>
                    <strong>QR dicabut / tidak aktif terpilih</strong> —
                    hanya QR <strong>Aktif</strong> yang bisa di-download.`;
            } else if (total === 0) {
                infoBox.className = 'px-4 py-2.5 text-xs text-center bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 transition-all';
                infoBox.innerHTML = `<i class="fas fa-info-circle text-blue-500"></i>
                    Belum ada QR dipilih — klik baris tabel untuk memilih`;
            } else {
                infoBox.className = 'px-4 py-2.5 text-xs text-center bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 transition-all';
                infoBox.innerHTML = `<i class="fas fa-check-circle"></i>
                    <strong>${total} QR dipilih</strong> — pilih format download`;
            }
        }
    }

    /* ---------- ROW CLICK (event delegation) ---------- */
    if ($tbody) {
        $tbody.addEventListener('click', function (e) {
            if (e.target.closest('button, a, form, input[type="submit"], input[type="button"]')) {
                return;
            }
            const row = e.target.closest('.qr-row');
            if (!row) return;
            const cb = row.querySelector('.qr-checkbox');
            if (!cb) return;
            cb.checked = !cb.checked;
            updateUI();
        });

        $tbody.addEventListener('change', function (e) {
            if (e.target.classList.contains('qr-checkbox')) {
                updateUI();
            }
        });
    }

    /* ---------- SELECT ALL ---------- */
    if (selectAll) {
        selectAll.addEventListener('click', function (e) {
            e.stopPropagation();
            const checked = this.checked;
            getCheckboxes().forEach(cb => { cb.checked = checked; });
            updateUI();
        });
    }

    /* ---------- DOWNLOAD ---------- */
    function triggerDownload(url) {
        const a = document.createElement('a');
        a.href = url;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        setTimeout(() => a.remove(), 500);
    }

    downloadBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const checked = getChecked();

            if (checked.length === 0) {
                showToast('⚠️ Klik pada baris tabel dulu untuk memilih QR', 3000, 'warning');
                return;
            }

            const blocked = checked.filter(cb => cb.dataset.status !== 'active');
            if (blocked.length > 0) {
                showToast(`❌ ${blocked.length} QR dicabut/tidak aktif tidak bisa di-download.`, 5000, 'error');
                return;
            }

            const format = this.dataset.format;
            const urls = checked.map(cb => `${baseUrl}/${cb.value}/${format}`);

            showToast(`⬇️ Mengunduh ${urls.length} QR (${format.toUpperCase()})...`, 3000, 'info');

            urls.forEach((url, i) => {
                setTimeout(() => triggerDownload(url), i * 500);
            });

            setTimeout(() => {
                showToast(`✅ ${urls.length} file dikirim ke browser. Cek folder Downloads.`, 4000, 'success');
                getCheckboxes().forEach(cb => { cb.checked = false; });
                updateUI();
            }, urls.length * 500 + 800);
        });
    });

    /* ---------- INIT ---------- */
    updateUI();
});
</script>
@endpush