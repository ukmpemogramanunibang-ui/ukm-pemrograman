@extends('layouts.roles.owner')

@section('title', 'Edit Kampanye - ' . ($campaign->title ?? 'UNICODE'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    @keyframes fadeSlideUp { from{opacity:0;transform:translateY(6px);} to{opacity:1;transform:translateY(0);} }
    .anim-in { animation: fadeSlideUp .3s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay:.04s; } .anim-in-2 { animation-delay:.08s; } .anim-in-3 { animation-delay:.12s; }
    .focus-ring:focus-visible { outline: 2px solid rgb(147,51,234); outline-offset: 2px; border-radius:.5rem; }

    .form-label { display:block; font-size:.8125rem; font-weight:600; color:rgb(55,65,81); margin-bottom:.375rem; }
    .dark .form-label { color: rgb(209,213,219); }
    .form-label .req { color: rgb(239,68,68); margin-left:2px; }

    .form-control { width:100%; padding:.6875rem .875rem; border-radius:.625rem; border:1px solid rgb(209,213,219); background:#fff; color:rgb(31,41,55); font-size:.875rem; transition:border-color .15s, box-shadow .15s, background .15s; }
    .dark .form-control { background: rgb(55,65,81); border-color: rgb(75,85,99); color: rgb(243,244,246); }
    .form-control:focus { border-color: rgb(147,51,234); box-shadow: 0 0 0 3px rgba(147,51,234,.18); outline:none; }
    .form-control.error { border-color: rgb(239,68,68); box-shadow: 0 0 0 3px rgba(239,68,68,.15); }

    .form-error { color: rgb(239,68,68); font-size:.75rem; margin-top:.3125rem; display:flex; align-items:center; gap:.375rem; }
    .form-hint  { color: rgb(107,114,128); font-size:.75rem; margin-top:.3125rem; display:flex; align-items:center; gap:.375rem; }
    .dark .form-hint { color: rgb(156,163,175); }

    .section-card { background:#fff; border:1px solid rgb(243,244,246); border-radius:1rem; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
    .dark .section-card { background: rgb(31,41,55); border-color: rgb(55,65,81); }

    .btn-primary { background: linear-gradient(145deg, rgb(147,51,234), rgb(126,34,206)); color:#fff; padding:.75rem 1.75rem; border-radius:.75rem; font-weight:600; font-size:.875rem; border:none; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.5rem; box-shadow: 0 4px 12px rgba(147,51,234,.25); text-decoration:none; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(147,51,234,.35); }
    .btn-primary:disabled { opacity:.6; cursor:not-allowed; transform:none; }

    .btn-secondary { background: rgb(243,244,246); color: rgb(31,41,55); padding:.75rem 1.5rem; border-radius:.75rem; font-weight:500; font-size:.875rem; border:none; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.5rem; text-decoration:none; }
    .dark .btn-secondary { background: rgb(55,65,81); color: rgb(243,244,246); }
    .btn-secondary:hover { background: rgb(229,231,235); }
    .dark .btn-secondary:hover { background: rgb(75,85,99); }

    .select2-container--default .select2-selection--multiple { border-radius: .625rem !important; border-color: rgb(209,213,219) !important; padding: 3px 6px !important; min-height: 44px; }
    .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: rgb(147,51,234) !important; box-shadow: 0 0 0 3px rgba(147,51,234,.18) !important; }
    .dark .select2-container--default .select2-selection--multiple { background-color: rgb(55,65,81) !important; border-color: rgb(75,85,99) !important; }
    .dark .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: rgb(75,85,99) !important; color: rgb(243,244,246) !important; border-color: rgb(107,114,128) !important; }
    .dark .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: rgb(243,244,246) !important; }
    .dark .select2-container--default .select2-search--inline .select2-search__field { color: rgb(243,244,246) !important; }
    .dark .select2-dropdown { background-color: rgb(31,41,55) !important; border-color: rgb(55,65,81) !important; }
    .dark .select2-results__option { color: rgb(243,244,246) !important; }
    .dark .select2-results__option--highlighted { background-color: rgb(147,51,234) !important; }
</style>
@endpush

@section('content')
@php
    $categoryMap = [
        'id_card'    => ['label' => 'ID Card',    'icon' => 'fa-id-card',            'desc' => 'Kartu identitas anggota'],
        'event'      => ['label' => 'Event',      'icon' => 'fa-calendar-check',     'desc' => 'Kegiatan / acara'],
        'reuni'      => ['label' => 'Reuni',      'icon' => 'fa-users',              'desc' => 'Acara reuni'],
        'seragam'    => ['label' => 'Seragam',    'icon' => 'fa-shirt',              'desc' => 'Seragam / jacket UKM'],
        'study_tour' => ['label' => 'Study Tour', 'icon' => 'fa-plane',              'desc' => 'Kunjungan industri'],
        'donasi'     => ['label' => 'Donasi',     'icon' => 'fa-hand-holding-heart', 'desc' => 'Penggalangan dana'],
        'lainnya'    => ['label' => 'Lainnya',    'icon' => 'fa-circle-dot',         'desc' => 'Lain-lain'],
    ];

    $statusMap = [
        'draft'     => ['label' => 'Draft',      'icon' => 'fa-pen',          'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
        'active'    => ['label' => 'Berjalan',   'icon' => 'fa-circle-play',  'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'closed'    => ['label' => 'Selesai',    'icon' => 'fa-circle-check', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'fa-circle-xmark', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
    ];

    // Data dari controller (fallback aman)
    $generations = $generations ?? collect();
    $members     = $members     ?? collect();

    $currentCategory = old('category', $campaign->category ?? '');
    $currentTarget   = old('target_type', $campaign->target_type ?? 'all');
    $currentStatus   = old('status', $campaign->status ?? 'draft');

    // Target data lama
    $targetData = is_array($campaign->target_data ?? null) ? $campaign->target_data : [];
    $oldGenIds  = old('generation_ids', $targetData['generation_ids'] ?? []);
    $oldUserIds = old('user_ids', $targetData['user_ids'] ?? []);

    $statMeta = $statusMap[$currentStatus] ?? $statusMap['draft'];

    // Safe date formatting
    $fmtDate = function ($v) {
        if (!$v) return '';
        try { return \Carbon\Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) { return ''; }
    };
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}" class="hover:text-purple-600 dark:hover:text-purple-400 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-home text-[11px]" aria-hidden="true"></i><span>Keuangan</span>
        </a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <a href="{{ route('owner.financial.campaigns.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Kampanye</a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Edit</span>
    </nav>

    {{-- HEADER --}}
    <div class="flex items-start gap-3 mb-6 anim-in">
        <a href="{{ route('owner.financial.campaigns.show', $campaign) }}"
           class="focus-ring mt-1 inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-all"
           aria-label="Kembali">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="flex items-center gap-2.5 flex-wrap">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                <i class="fas fa-pen text-white text-base" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">Edit Kampanye</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-md">{{ $campaign->title ?? 'Tanpa judul' }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $statMeta['class'] }} text-[11px] font-semibold ml-auto">
                <i class="fas {{ $statMeta['icon'] }}" aria-hidden="true"></i>{{ $statMeta['label'] }}
            </span>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div role="alert" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 mb-5">
            <i class="fas fa-circle-exclamation mt-0.5 shrink-0" aria-hidden="true"></i>
            <div class="flex-1">
                <p class="font-semibold text-sm mb-1">Periksa kembali {{ $errors->count() }} field berikut:</p>
                <ul class="text-xs space-y-0.5 list-disc list-inside">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('owner.financial.campaigns.update', $campaign) }}" method="POST" id="campaignForm">
        @csrf @method('PUT')

        {{-- ═══════════ BAGIAN 1 ═══════════ --}}
        <div class="section-card anim-in anim-in-1 p-5 md:p-6 mb-4">
            <div class="flex items-center gap-2 mb-5">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-xs font-bold">1</span>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Informasi Dasar</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="title" class="form-label">Judul Kampanye <span class="req">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $campaign->title) }}"
                           class="form-control @error('title') error @enderror" maxlength="255" required>
                    @error('title') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Kategori <span class="req">*</span></label>
                    <input type="hidden" name="category" id="categoryInput" value="{{ $currentCategory }}" required>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2" role="radiogroup" aria-label="Pilih kategori">
                        @foreach($categoryMap as $key => $meta)
                            @php $isSelected = $currentCategory === $key; @endphp
                            <button type="button" role="radio" aria-checked="{{ $isSelected ? 'true' : 'false' }}" data-category="{{ $key }}"
                                    class="category-option focus-ring text-left p-3 rounded-lg border-2 transition-all
                                           {{ $isSelected ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30 dark:border-purple-400' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700' }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas {{ $meta['icon'] }} text-sm text-purple-600 dark:text-purple-400" aria-hidden="true"></i>
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ $meta['label'] }}</span>
                                </div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-snug">{{ $meta['desc'] }}</p>
                            </button>
                        @endforeach
                    </div>
                    @error('category') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="form-label">Deskripsi <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-1">(opsional)</span></label>
                    <textarea id="description" name="description" rows="3" class="form-control @error('description') error @enderror" maxlength="5000">{{ old('description', $campaign->description) }}</textarea>
                    @error('description') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- ═══════════ BAGIAN 2 ═══════════ --}}
        <div class="section-card anim-in anim-in-2 p-5 md:p-6 mb-4">
            <div class="flex items-center gap-2 mb-5">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-xs font-bold">2</span>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Nominal & Target</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="form-label">Nominal per Anggota <span class="req">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500 dark:text-gray-400 pointer-events-none">Rp</span>
                        <input type="number" id="amount" name="amount" value="{{ old('amount', $campaign->amount) }}"
                               class="form-control @error('amount') error @enderror pl-10 tabular-nums"
                               min="0" step="1000" required>
                    </div>
                    @error('amount') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                <div class="flex items-end">
                    <label for="is_required" class="w-full flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700 cursor-pointer transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30">
                        <input type="checkbox" id="is_required" name="is_required" value="1"
                               {{ old('is_required', $campaign->is_required) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-purple-500 focus:ring-purple-500">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Wajib diikuti</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Anggota target harus membayar</p>
                        </div>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Siapa yang Ditagih? <span class="req">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2" role="radiogroup" aria-label="Pilih target">
                        @php
                            $targetOptions = [
                                'all'        => ['label' => 'Semua Anggota',    'icon' => 'fa-users',       'desc' => 'Seluruh anggota aktif'],
                                'generation' => ['label' => 'Generasi Tertentu','icon' => 'fa-layer-group', 'desc' => 'Pilih satu atau lebih generasi'],
                                'specific'   => ['label' => 'Individu Tertentu','icon' => 'fa-user-check',  'desc' => 'Pilih anggota secara manual'],
                            ];
                        @endphp
                        @foreach($targetOptions as $key => $opt)
                            @php $isSel = $currentTarget === $key; @endphp
                            <button type="button" role="radio" aria-checked="{{ $isSel ? 'true' : 'false' }}" data-target="{{ $key }}"
                                    class="target-option focus-ring text-left p-3 rounded-lg border-2 transition-all
                                           {{ $isSel ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30 dark:border-purple-400' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700' }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas {{ $opt['icon'] }} text-sm text-purple-600 dark:text-purple-400" aria-hidden="true"></i>
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ $opt['label'] }}</span>
                                </div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-snug">{{ $opt['desc'] }}</p>
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="target_type" id="targetTypeInput" value="{{ $currentTarget }}" required>
                    @error('target_type') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                {{-- ═══ GENERASI ═══ --}}
                <div id="target_generation_container" class="md:col-span-2 {{ $currentTarget === 'generation' ? '' : 'hidden' }}">
                    <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                        <label for="target_generation_select" class="form-label mb-0">Pilih Generasi <span class="req">*</span></label>
                        @if($generations->isNotEmpty())
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/60 px-2 py-0.5 rounded-md">
                                    {{ $generations->count() }} tersedia
                                </span>
                                <button type="button" id="selectAllGenerations"
                                        class="text-[10px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                                    Pilih semua
                                </button>
                                <span class="text-gray-300 dark:text-gray-600">·</span>
                                <button type="button" id="clearAllGenerations"
                                        class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 hover:underline">
                                    Kosongkan
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($generations->isEmpty())
                        <div class="rounded-lg border-2 border-dashed border-amber-300 dark:border-amber-700/50 bg-amber-50 dark:bg-amber-900/20 p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                                    <i class="fas fa-triangle-exclamation text-amber-600 dark:text-amber-400 text-xs" aria-hidden="true"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Belum ada data generasi</p>
                                    <p class="text-xs text-amber-800 dark:text-amber-300/90 mt-0.5">
                                        Tambahkan generasi terlebih dahulu sebelum menggunakan sasaran per generasi.
                                    </p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <a href="{{ route('owner.financial.generations.index') }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold transition-colors">
                                            <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
                                            Kelola Generasi
                                        </a>
                                        <button type="button" onclick="switchTargetMode('all')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-white dark:bg-gray-800 border border-amber-300 dark:border-amber-700/50 hover:bg-amber-50 dark:hover:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-xs font-medium transition-colors">
                                            <i class="fas fa-users text-[10px]" aria-hidden="true"></i>
                                            Pilih "Semua Anggota" saja
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <select id="target_generation_select" name="generation_ids[]" multiple style="width:100%">
                            @foreach($generations as $gen)
                                <option value="{{ $gen->id }}" {{ in_array($gen->id, $oldGenIds) ? 'selected' : '' }}>
                                    {{ $gen->name }}{{ !empty($gen->code) ? ' ('.$gen->code.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('generation_ids')   <div class="form-error mt-2"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                    @error('generation_ids.*') <div class="form-error mt-2"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                {{-- ═══ INDIVIDU ═══ --}}
                <div id="target_individual_container" class="md:col-span-2 {{ $currentTarget === 'specific' ? '' : 'hidden' }}">
                    <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                        <label for="target_individual_select" class="form-label mb-0">Pilih Anggota <span class="req">*</span></label>
                        @if($members->isNotEmpty())
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/60 px-2 py-0.5 rounded-md">
                                {{ $members->count() }} tersedia
                            </span>
                        @endif
                    </div>

                    @if($members->isEmpty())
                        <div class="rounded-lg border-2 border-dashed border-amber-300 dark:border-amber-700/50 bg-amber-50 dark:bg-amber-900/20 p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-triangle-exclamation text-amber-600 dark:text-amber-400 mt-0.5" aria-hidden="true"></i>
                                <div>
                                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Belum ada anggota terdaftar</p>
                                    <p class="text-xs text-amber-800 dark:text-amber-300/90 mt-0.5">
                                        Tidak ada user dengan level 3, 4, atau 5 yang aktif.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <select id="target_individual_select" name="user_ids[]" multiple style="width:100%">
                            @foreach($members as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, $oldUserIds) ? 'selected' : '' }}>
                                    {{ $user->name }}{{ !empty($user->nim) ? ' ('.$user->nim.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('user_ids')   <div class="form-error mt-2"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                    @error('user_ids.*') <div class="form-error mt-2"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- ═══════════ BAGIAN 3 ═══════════ --}}
        <div class="section-card anim-in anim-in-3 p-5 md:p-6 mb-4">
            <div class="flex items-center gap-2 mb-5">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-xs font-bold">3</span>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Jadwal & Status</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="start_date" class="form-label">Tanggal Mulai <span class="req">*</span></label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date', $fmtDate($campaign->start_date)) }}"
                           class="form-control @error('start_date') error @enderror" required>
                    @error('start_date') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="end_date" class="form-label">Tanggal Berakhir</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date', $fmtDate($campaign->end_date)) }}"
                           class="form-control @error('end_date') error @enderror">
                    @error('end_date') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="due_date" class="form-label">Tanggal Jatuh Tempo <span class="req">*</span></label>
                    <input type="date" id="due_date" name="due_date"
                           value="{{ old('due_date', $fmtDate($campaign->due_date)) }}"
                           class="form-control @error('due_date') error @enderror" required>
                    @error('due_date') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control @error('status') error @enderror">
                        @foreach([
                            'draft'     => 'Draft — belum diumumkan',
                            'active'    => 'Berjalan — menerima pembayaran',
                            'closed'    => 'Selesai — ditutup',
                            'cancelled' => 'Dibatalkan',
                        ] as $key => $label)
                            <option value="{{ $key }}" @selected($currentStatus === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="form-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- SUBMIT BAR --}}
        <div class="anim-in anim-in-3 sticky bottom-3 z-20 flex flex-wrap items-center gap-2 p-3 rounded-xl bg-white/95 dark:bg-gray-800/95 backdrop-blur border border-gray-200 dark:border-gray-700 shadow-lg">
            <p class="text-xs text-gray-500 dark:text-gray-400 flex-1 min-w-0">
                <i class="fas fa-clock-rotate-left mr-1" aria-hidden="true"></i>
                Terakhir diubah {{ $campaign->updated_at?->diffForHumans() ?? 'baru saja' }}
            </p>
            <a href="{{ route('owner.financial.campaigns.show', $campaign) }}" class="btn-secondary">
                <i class="fas fa-xmark" aria-hidden="true"></i>Batal
            </a>
            <button type="submit" class="btn-primary" id="submitBtn">
                <i class="fas fa-circle-check" aria-hidden="true"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoryInput = document.getElementById('categoryInput');
    document.querySelectorAll('.category-option').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-option').forEach(b => {
                b.setAttribute('aria-checked', 'false');
                b.classList.remove('border-purple-500','bg-purple-50','dark:bg-purple-900/30','dark:border-purple-400');
                b.classList.add('border-gray-200','dark:border-gray-700');
            });
            btn.setAttribute('aria-checked', 'true');
            btn.classList.add('border-purple-500','bg-purple-50','dark:bg-purple-900/30','dark:border-purple-400');
            btn.classList.remove('border-gray-200','dark:border-gray-700');
            categoryInput.value = btn.dataset.category;
        });
    });

    const targetTypeInput = document.getElementById('targetTypeInput');
    const genContainer = document.getElementById('target_generation_container');
    const indContainer = document.getElementById('target_individual_container');

    window.switchTargetMode = function (mode) {
        targetTypeInput.value = mode;
        genContainer.classList.toggle('hidden', mode !== 'generation');
        indContainer.classList.toggle('hidden', mode !== 'specific');
        document.querySelectorAll('.target-option').forEach(b => {
            const on = b.dataset.target === mode;
            b.setAttribute('aria-checked', on ? 'true' : 'false');
            b.classList.toggle('border-purple-500', on);
            b.classList.toggle('bg-purple-50', on);
            b.classList.toggle('dark:bg-purple-900/30', on);
            b.classList.toggle('dark:border-purple-400', on);
            b.classList.toggle('border-gray-200', !on);
            b.classList.toggle('dark:border-gray-700', !on);
        });
    };

    document.querySelectorAll('.target-option').forEach(btn => {
        btn.addEventListener('click', () => window.switchTargetMode(btn.dataset.target));
    });

    if (window.jQuery) {
        if (document.getElementById('target_generation_select')) {
            jQuery('#target_generation_select').select2({ placeholder: 'Pilih generasi…', allowClear: true, width: '100%' });
        }
        if (document.getElementById('target_individual_select')) {
            jQuery('#target_individual_select').select2({ placeholder: 'Cari anggota…', allowClear: true, width: '100%' });
        }
    }

    document.getElementById('selectAllGenerations')?.addEventListener('click', function () {
        const sel = document.getElementById('target_generation_select');
        if (!sel) return;
        Array.from(sel.options).forEach(o => o.selected = true);
        if (window.jQuery) jQuery(sel).trigger('change');
    });

    document.getElementById('clearAllGenerations')?.addEventListener('click', function () {
        const sel = document.getElementById('target_generation_select');
        if (!sel) return;
        Array.from(sel.options).forEach(o => o.selected = false);
        if (window.jQuery) jQuery(sel).trigger('change');
    });

    const startInput = document.getElementById('start_date');
    const dueInput   = document.getElementById('due_date');
    const endInput   = document.getElementById('end_date');
    startInput?.addEventListener('change', () => {
        if (dueInput) dueInput.min = startInput.value;
        if (endInput) endInput.min = startInput.value;
    });

    const form = document.getElementById('campaignForm');
    const submitBtn = document.getElementById('submitBtn');
    form?.addEventListener('submit', () => {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Menyimpan…';
        }
    });
});
</script>
@endpush