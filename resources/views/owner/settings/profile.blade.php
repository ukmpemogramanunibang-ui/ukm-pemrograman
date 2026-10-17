@extends('layouts.roles.owner')

@section('title', 'Data Diri Owner – UNICODE')
@section('page-title', 'Data Diri Owner')

@push('styles')
<style>
    .profile-tab-btn {
        padding: .55rem 1.15rem;
        border-radius: .75rem;
        font-size: .85rem;
        font-weight: 600;
        color: #6b7280;
        transition: all .2s;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }
    .profile-tab-btn:hover {
        color: #111827;
        background: rgba(0,0,0,.04);
    }
    .profile-tab-btn.active {
        background: white;
        color: #0891b2;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .dark .profile-tab-btn {
        color: #9ca3af;
    }
    .dark .profile-tab-btn:hover {
        color: #e5e7eb;
        background: rgba(255,255,255,.05);
    }
    .dark .profile-tab-btn.active {
        background: #374151;
        color: #22d3ee;
    }

    /* Avatar */
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        line-height: 0;
    }
    .profile-avatar {
        width: 180px;
        height: 180px;
        border-radius: 1.25rem;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 8px 30px rgba(0,0,0,.15);
        background: #f3f4f6;
        transition: all .3s;
        display: block;
    }
    .dark .profile-avatar {
        border-color: #1f2937;
    }
    .avatar-wrapper:hover .profile-avatar {
        transform: scale(1.02);
        box-shadow: 0 12px 40px rgba(99,102,241,.3);
    }
    .upload-avatar-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(6,182,212,.4);
        transition: all .2s;
        border: 3px solid white;
    }
    .dark .upload-avatar-btn {
        border-color: #1f2937;
    }
    .upload-avatar-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 18px rgba(6,182,212,.55);
    }
    .upload-avatar-btn input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Form controls */
    .form-label {
        display: block;
        font-size: .8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: .35rem;
    }
    .dark .form-label {
        color: #d1d5db;
    }
    .form-input {
        width: 100%;
        padding: .6rem .9rem;
        border-radius: .65rem;
        border: 1px solid #d1d5db;
        background: white;
        color: #111827;
        font-size: .875rem;
        transition: all .15s;
    }
    .form-input:focus {
        outline: none;
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6,182,212,.15);
    }
    .dark .form-input {
        background: #374151;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    .form-input:disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .dark .form-input:disabled {
        background: #1f2937;
        color: #6b7280;
    }
    .form-input.has-error {
        border-color: #ef4444;
    }

    /* Social media row */
    .social-row {
        display: flex;
        gap: .5rem;
        align-items: center;
        margin-bottom: .5rem;
    }
    .social-row select {
        min-width: 130px;
        flex-shrink: 0;
    }
    .social-row input {
        flex: 1;
    }
    .social-row button {
        width: 40px;
        height: 40px;
        border-radius: .65rem;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all .15s;
    }
    .social-row button:hover {
        background: #fecaca;
    }
    .dark .social-row button {
        background: rgba(239,68,68,.15);
        color: #f87171;
    }

    /* Info row */
    .info-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: .85rem 1.25rem;
        transition: background .15s;
    }
    .info-row:hover {
        background: #f9fafb;
    }
    .dark .info-row:hover {
        background: rgba(55,65,81,.3);
    }
    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: .65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Section divider */
    .section-title {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 1rem;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, #e5e7eb, transparent);
    }
    .dark .section-title {
        color: #9ca3af;
    }
    .dark .section-title::after {
        background: linear-gradient(90deg, #374151, transparent);
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user()->load(['level', 'status', 'jabatans']);
    $initials = strtoupper(collect(explode(' ', $user->name ?? 'Owner'))
        ->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
    $avatarPath = $user->avatar_url
        ? asset('storage/' . $user->avatar_url)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'O') . '&background=06b6d4&color=fff&size=256&bold=true';

    $socials = old('social_media', $user->social_media ?? []);
    if (!is_array($socials) || empty($socials)) {
        $socials = [['platform' => 'instagram', 'url' => '']];
    }
@endphp

<div class="space-y-6 max-w-6xl">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-cyan-500 font-bold mb-1">
                Account Profile
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
                Data Diri Owner
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola identitas, keamanan, dan informasi kontak akun Owner.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.settings.general') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                <i class="fas fa-arrow-left"></i> Dashboard Builder
            </a>
        </div>
    </div>

    {{-- TABS --}}
    @include('owner.settings._sidebar')

    {{-- FLASH --}}
    @if (session('status') === 'profile-updated' || session('success'))
        <div class="rounded-2xl border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/20 p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-green-500/20 text-green-600 dark:text-green-400 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="flex-1 text-sm">
                <p class="font-semibold text-green-800 dark:text-green-200">Perubahan berhasil disimpan</p>
                <p class="text-green-700/80 dark:text-green-300/80 text-xs mt-0.5">
                    {{ session('success') ?? 'Data profil Anda telah diperbarui.' }}
                </p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-red-800 dark:text-red-200 text-sm">Ada kesalahan pada form</p>
                    <ul class="mt-1.5 space-y-0.5 text-xs text-red-700 dark:text-red-300 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- TABS NAV --}}
    <div class="flex flex-wrap gap-1 p-1 rounded-2xl bg-gray-100 dark:bg-gray-800 w-fit">
        <button type="button" data-profile-tab="overview" class="profile-tab-btn active">
            <i class="fas fa-user-circle text-xs"></i> Overview
        </button>
        <button type="button" data-profile-tab="edit" class="profile-tab-btn">
            <i class="fas fa-user-edit text-xs"></i> Edit Data
        </button>
        <button type="button" data-profile-tab="security" class="profile-tab-btn">
            <i class="fas fa-shield-alt text-xs"></i> Keamanan
        </button>
    </div>

    {{-- ============================================================
         TAB 1: OVERVIEW
         ============================================================ --}}
    <div data-profile-panel="overview">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KARTU PROFIL (KIRI) --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">

                    {{-- Banner --}}
                    <div class="h-24 bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 dark:from-amber-600 dark:via-amber-700 dark:to-amber-800 relative">
                        <div class="absolute -right-12 -top-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    </div>

                    {{-- Avatar --}}
                    <div class="px-6 pb-6 -mt-12 text-center">
                        <div class="inline-block relative">
                            <img src="{{ $avatarPath }}"
                                 alt="{{ $user->name }}"
                                 class="w-28 h-28 rounded-2xl border-4 border-white dark:border-gray-800 object-cover shadow-xl mx-auto bg-gray-100">
                            @if ($user->is_active)
                                <span class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-400 rounded-full border-3 border-white dark:border-gray-800 shadow" title="Aktif"></span>
                            @endif
                        </div>

                        <h2 class="mt-3 text-lg font-bold text-gray-900 dark:text-white truncate">
                            {{ $user->name }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ $user->email ?? '-' }}
                        </p>

                        <div class="flex justify-center mt-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/15 text-amber-700 dark:text-amber-400 text-[11px] font-bold uppercase tracking-wide">
                                <i class="fas fa-crown"></i> Owner
                            </span>
                        </div>

                        {{-- Social links --}}
                        @if (!empty($user->social_media) && is_array($user->social_media))
                            <div class="flex flex-wrap justify-center gap-2 mt-3">
                                @foreach ($user->social_media as $soc)
                                    @if (!empty($soc['url']))
                                        @php
                                            $iconMap = [
                                                'instagram' => 'fab fa-instagram',
                                                'facebook'  => 'fab fa-facebook',
                                                'telegram'  => 'fab fa-telegram',
                                                'x'         => 'fab fa-x-twitter',
                                            ];
                                        @endphp
                                        <a href="{{ $soc['url'] }}" target="_blank" rel="noopener noreferrer"
                                           class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 hover:text-cyan-600 transition"
                                           title="{{ ucfirst($soc['platform']) }}">
                                            <i class="{{ $iconMap[$soc['platform']] ?? 'fas fa-link' }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Share --}}
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <a href="{{ route('profile.public', $user->id) }}" target="_blank"
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 text-xs font-semibold hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition">
                                <i class="fas fa-share-alt"></i> Lihat Publik
                            </a>
                            <button type="button" data-profile-tab-jump="edit"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-xs font-semibold hover:bg-amber-100 dark:hover:bg-amber-900/30 transition">
                                <i class="fas fa-user-edit"></i> Edit Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL GRID (KANAN) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- IDENTITAS --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-id-card text-cyan-500"></i> Identitas
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $identityRows = [
                                ['label' => 'Nama Lengkap', 'value' => $user->name ?? '-',                 'icon' => 'fa-user',       'color' => 'blue'],
                                ['label' => 'NIM',          'value' => $user->nim ?? '-',                  'icon' => 'fa-hashtag',    'color' => 'indigo'],
                                ['label' => 'Email',        'value' => $user->email ?? '-',                'icon' => 'fa-envelope',   'color' => 'cyan'],
                                ['label' => 'No. HP',       'value' => $user->phone ?? '-',                'icon' => 'fa-phone',      'color' => 'green'],
                                ['label' => 'Alamat',       'value' => $user->address ?? '-',              'icon' => 'fa-map-marker-alt', 'color' => 'pink'],
                                ['label' => 'Tanggal Lahir','value' => $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->translatedFormat('d F Y') : '-', 'icon' => 'fa-birthday-cake', 'color' => 'rose'],
                            ];
                        @endphp
                        @foreach ($identityRows as $row)
                            <div class="info-row">
                                <div class="info-icon bg-{{ $row['color'] }}-500/15 text-{{ $row['color'] }}-500">
                                    <i class="fas {{ $row['icon'] }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-bold">{{ $row['label'] }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $row['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- STATUS & ORGANISASI --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-sitemap text-purple-500"></i> Organisasi
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $orgRows = [
                                ['label' => 'Level',    'value' => $user->level->display_name ?? $user->level->name ?? '-', 'icon' => 'fa-layer-group', 'color' => 'amber'],
                                ['label' => 'Status',   'value' => $user->status->display_name ?? $user->status->name ?? ($user->is_active ? 'Aktif' : 'Nonaktif'), 'icon' => 'fa-circle-check', 'color' => 'emerald'],
                                ['label' => 'Jabatan',  'value' => $user->jabatans->pluck('name')->implode(', ') ?: '-', 'icon' => 'fa-briefcase', 'color' => 'purple'],
                                ['label' => 'Bergabung','value' => optional($user->created_at)->translatedFormat('d F Y') ?? '-', 'icon' => 'fa-calendar', 'color' => 'cyan'],
                            ];
                        @endphp
                        @foreach ($orgRows as $row)
                            <div class="info-row">
                                <div class="info-icon bg-{{ $row['color'] }}-500/15 text-{{ $row['color'] }}-500">
                                    <i class="fas {{ $row['icon'] }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-bold">{{ $row['label'] }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $row['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- KEAHLIAN --}}
                @if (!empty($user->skills))
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                        <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2 mb-3">
                            <i class="fas fa-tools text-amber-500"></i> Keahlian
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ((array) $user->skills as $skill)
                                @if (trim($skill))
                                    <span class="px-3 py-1 rounded-full bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 text-xs font-semibold">
                                        {{ trim($skill) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ============================================================
         TAB 2: EDIT DATA
         ============================================================ --}}
    <div data-profile-panel="edit" class="hidden">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- AVATAR + IDENTITAS --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="section-title">
                    <i class="fas fa-camera text-cyan-500"></i> Foto & Identitas
                </h2>

                <div class="grid md:grid-cols-[auto,1fr] gap-6 items-start">
                    {{-- Avatar --}}
                    <div class="text-center md:text-left">
                        <div class="avatar-wrapper">
                            <img src="{{ $avatarPath }}" alt="{{ $user->name }}" class="profile-avatar" id="avatar-preview">
                            <label class="upload-avatar-btn" title="Ubah avatar">
                                <i class="fas fa-camera"></i>
                                <input type="file" name="avatar" accept="image/*" id="avatar-input">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            JPG/PNG/WEBP. Maks 2 MB.
                        </p>
                        @error('avatar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Fields --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="name">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $user->name) }}"
                                   class="form-input @error('name') has-error @enderror">
                            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label" for="nim">NIM</label>
                            <input type="text" name="nim" id="nim"
                                   value="{{ old('nim', $user->nim) }}"
                                   class="form-input @error('nim') has-error @enderror">
                            @error('nim')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label" for="email">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required
                                   value="{{ old('email', $user->email) }}"
                                   class="form-input @error('email') has-error @enderror">
                            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label" for="phone">No. HP</label>
                            <input type="text" name="phone" id="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="form-input @error('phone') has-error @enderror"
                                   placeholder="08123456789">
                            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL PRIBADI --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="section-title">
                    <i class="fas fa-user text-purple-500"></i> Detail Pribadi
                </h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="birth_date">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date"
                               value="{{ old('birth_date', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label" for="gender">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="form-input">
                            <option value="">— Pilih —</option>
                            <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="country">Negara</label>
                        <input type="text" name="country" id="country"
                               value="{{ old('country', $user->country ?? 'Indonesia') }}"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label" for="semester">Semester</label>
                        <input type="text" name="semester" id="semester"
                               value="{{ old('semester', $user->semester) }}"
                               class="form-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="address">Alamat</label>
                        <textarea name="address" id="address" rows="2"
                                  class="form-input">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="bio">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                                  class="form-input"
                                  placeholder="Ceritakan singkat tentang Anda...">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="portfolio_url">Link Portofolio</label>
                        <input type="url" name="portfolio_url" id="portfolio_url"
                               value="{{ old('portfolio_url', $user->portfolio_url) }}"
                               class="form-input"
                               placeholder="https://portofolio-saya.com">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="skills">Keahlian (pisahkan dengan koma)</label>
                        <input type="text" name="skills" id="skills"
                               value="{{ old('skills', is_array($user->skills) ? implode(', ', $user->skills) : '') }}"
                               class="form-input"
                               placeholder="backend, frontend, ui/ux">
                    </div>
                </div>
            </div>

            {{-- SOSIAL MEDIA --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h2 class="section-title">
                    <i class="fas fa-share-alt text-pink-500"></i> Sosial Media
                </h2>

                <div id="social-media-container">
                    @foreach ($socials as $i => $soc)
                        <div class="social-row">
                            <select name="social_media[{{ $i }}][platform]" class="form-input">
                                @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'telegram' => 'Telegram', 'x' => 'X (Twitter)'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($soc['platform'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="url" name="social_media[{{ $i }}][url]"
                                   value="{{ $soc['url'] ?? '' }}"
                                   placeholder="https://..."
                                   class="form-input">
                            <button type="button" class="remove-social-btn" title="Hapus">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-social-btn"
                        class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300">
                    <i class="fas fa-plus-circle"></i> Tambah Sosial Media
                </button>
            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-col sm:flex-row gap-2 justify-end">
                <button type="button" data-profile-tab-jump="overview"
                        class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-bold shadow-lg shadow-cyan-500/20 transition">
                    <i class="fas fa-save mr-1.5"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TAB 3: KEAMANAN
         ============================================================ --}}
    <div data-profile-panel="security" class="hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- PASSWORD --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-11 h-11 rounded-xl bg-red-500/15 text-red-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-gray-900 dark:text-white">Password</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Minimal 8 karakter. Gunakan kombinasi huruf & angka.
                        </p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="form-label" for="password">Password Baru</label>
                        <input type="password" name="password" id="password"
                               class="form-input @error('password') has-error @enderror"
                               placeholder="••••••••">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-input"
                               placeholder="••••••••">
                    </div>

                    {{-- Keep required fields (hidden) --}}
                    <input type="hidden" name="name"  value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition">
                        <i class="fas fa-key mr-1.5"></i> Ubah Password
                    </button>
                </form>
            </div>

            {{-- PIN --}}
            @if ((int) $user->level_id === 1)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/15 text-amber-500 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-sm text-gray-900 dark:text-white">PIN Keamanan</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                PIN 4–10 digit. Dipakai untuk verifikasi aksi sensitif.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="form-label" for="pin">PIN Baru</label>
                            <input type="password" name="pin" id="pin" maxlength="10"
                                   class="form-input @error('pin') has-error @enderror"
                                   placeholder="••••••">
                            @error('pin')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <input type="hidden" name="name"  value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">

                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition">
                            <i class="fas fa-shield-alt mr-1.5"></i> Ubah PIN
                        </button>
                    </form>
                </div>
            @endif

            {{-- INFO TAMBAHAN --}}
            <div class="lg:col-span-2 rounded-2xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/10 p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="text-sm">
                        <p class="font-semibold text-blue-800 dark:text-blue-200">Tips keamanan</p>
                        <ul class="mt-1 text-xs text-blue-700/80 dark:text-blue-300/80 space-y-0.5 list-disc list-inside">
                            <li>Gunakan password minimal 12 karakter dengan kombinasi huruf, angka, dan simbol.</li>
                            <li>Jangan bagikan password atau PIN kepada siapa pun.</li>
                            <li>Ubah password secara berkala, minimal setiap 3 bulan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs   = document.querySelectorAll('[data-profile-tab]');
    const panels = document.querySelectorAll('[data-profile-panel]');

    function switchTab(name) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.profileTab === name));
        panels.forEach(p => p.classList.toggle('hidden', p.dataset.profilePanel !== name));
        try { localStorage.setItem('owner_profile_tab', name); } catch (e) {}
    }

    tabs.forEach(t => {
        t.addEventListener('click', () => switchTab(t.dataset.profileTab));
    });

    // Tombol "Edit Data" di overview / "Batal" di form
    document.querySelectorAll('[data-profile-tab-jump]').forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.profileTabJump));
    });

    // Restore tab terakhir
    try {
        const saved = localStorage.getItem('owner_profile_tab');
        if (saved && document.querySelector(`[data-profile-tab="${saved}"]`)) {
            switchTab(saved);
        }
    } catch (e) {}

    // ---- Avatar preview ----
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function (e) {
            const file = e.target.files?.[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2 MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = ev => avatarPreview.src = ev.target.result;
            reader.readAsDataURL(file);
        });
    }

    // ---- Social media dinamis ----
    const socialContainer = document.getElementById('social-media-container');
    const addSocialBtn    = document.getElementById('add-social-btn');

    function buildRow(index) {
        const row = document.createElement('div');
        row.className = 'social-row';
        row.innerHTML = `
            <select name="social_media[${index}][platform]" class="form-input">
                <option value="instagram">Instagram</option>
                <option value="facebook">Facebook</option>
                <option value="telegram">Telegram</option>
                <option value="x">X (Twitter)</option>
            </select>
            <input type="url" name="social_media[${index}][url]" placeholder="https://..." class="form-input">
            <button type="button" class="remove-social-btn" title="Hapus"><i class="fas fa-times"></i></button>
        `;
        return row;
    }

    function bindRemove(btn) {
        btn.addEventListener('click', () => {
            const row = btn.closest('.social-row');
            if (socialContainer.querySelectorAll('.social-row').length > 1) {
                row.remove();
            } else {
                // Reset row terakhir kalau cuma satu
                row.querySelector('select').selectedIndex = 0;
                row.querySelector('input').value = '';
            }
        });
    }

    if (addSocialBtn && socialContainer) {
        addSocialBtn.addEventListener('click', () => {
            const idx = socialContainer.querySelectorAll('.social-row').length;
            const row = buildRow(idx);
            socialContainer.appendChild(row);
            bindRemove(row.querySelector('.remove-social-btn'));
        });

        socialContainer.querySelectorAll('.remove-social-btn').forEach(bindRemove);
    }
});
</script>
@endpush
@endsection