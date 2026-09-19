@extends('layouts.roles.owner')

@section('title', 'Pengaturan Data Diri – Owner UNICODE')
@section('page-title', 'Pengaturan Data Diri')

@push('styles')
<style>
    .profile-avatar {
        width: 220px;
        height: 220px;
        min-width: 220px;
        max-width: 220px;
        border-radius: 0;
        object-fit: cover;
        border: 3px solid #e5e7eb;
        transition: 0.3s;
        display: block;
        background: #f3f4f6;
    }
    .profile-avatar:hover {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99,102,241,0.3);
    }
    .avatar-wrapper {
        display: inline-block;
        line-height: 0;
        position: relative;
    }
    .upload-avatar-btn {
        position: absolute;
        bottom: 6px;
        right: 6px;
        background: #6366f1;
        color: white;
        padding: 10px 12px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        cursor: pointer;
        transition: 0.3s;
        line-height: 1;
        font-size: 14px;
    }
    .upload-avatar-btn:hover {
        background: #4f46e5;
        transform: scale(1.1);
    }
    .upload-avatar-btn input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    .form-divider {
        border-top: 1px solid #e5e7eb;
        margin: 1.5rem 0;
    }
    .dark .form-divider {
        border-color: #374151;
    }
    .social-media-row {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .social-media-row select {
        min-width: 120px;
    }
    .profile-stats {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-top: 0.75rem;
        font-size: 0.9rem;
        color: #6b7280;
    }
    .dark .profile-stats {
        color: #9ca3af;
    }
    .profile-stats span i {
        margin-right: 0.25rem;
    }
    .social-icons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 0.5rem;
    }
    .social-icons a {
        font-size: 1.5rem;
        color: #6b7280;
        transition: 0.2s;
    }
    .social-icons a:hover {
        color: #6366f1;
    }
    .dark .social-icons a {
        color: #9ca3af;
    }
    .dark .social-icons a:hover {
        color: #818cf8;
    }
</style>
@endpush

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Profile</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data diri dan informasi akun Anda.</p>
            </div>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-full text-sm font-medium">
                {{ ucfirst(Auth::user()->level->name ?? 'Owner') }}
            </span>
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 p-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 p-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Kolom Kiri: Avatar & Info -->
                <div class="lg:col-span-1 flex flex-col items-center text-center">
                    <div class="avatar-wrapper">
                        @php
                            $avatarPath = Auth::user()->avatar_url 
                                ? asset('storage/' . Auth::user()->avatar_url) 
                                : asset('images/gif.gif');
                        @endphp
                        <img src="{{ $avatarPath }}"
                             alt="Avatar"
                             class="profile-avatar"
                             id="avatar-preview">
                        <label class="upload-avatar-btn" title="Ubah Avatar">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="avatar" accept="image/*" id="avatar-input">
                        </label>
                    </div>

                    <h4 class="mt-4 text-xl font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Status: {{ Auth::user()->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </p>

                    <div class="profile-stats">
                        <span><i class="fas fa-heart text-red-500"></i> 0</span>
                        <span><i class="fas fa-thumbtack"></i> 0</span>
                        <span><i class="fas fa-bookmark"></i> 0</span>
                    </div>

                    <!-- Sosial Media yang tersimpan -->
                    @if(Auth::user()->social_media)
                        <div class="social-icons">
                            @foreach(Auth::user()->social_media as $soc)
                                @php
                                    $icon = match($soc['platform']) {
                                        'instagram' => 'fab fa-instagram',
                                        'facebook' => 'fab fa-facebook',
                                        'telegram' => 'fab fa-telegram',
                                        'x' => 'fab fa-x-twitter',
                                        default => 'fas fa-link'
                                    };
                                @endphp
                                <a href="{{ $soc['url'] }}" target="_blank" title="{{ ucfirst($soc['platform']) }}">
                                    <i class="{{ $icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Tombol Share Profile -->
                    <div class="mt-4 w-full">
                        <a href="{{ route('profile.public', Auth::user()->id) }}" target="_blank" 
                           class="block w-full py-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-medium hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                            <i class="fas fa-share-alt mr-2"></i> Share Profile
                        </a>
                    </div>

                    <!-- Menu tambahan (statis) -->
                    <div class="mt-2 w-full">
                        <a href="#" class="block w-full py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <i class="fas fa-user-edit mr-2"></i> Edit profile
                        </a>
                    </div>
                    <div class="mt-4 w-full space-y-2">
                        <a href="#" class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <i class="fas fa-user-friends w-6 text-indigo-400"></i> Add friends
                        </a>
                        <a href="#" class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <i class="fas fa-life-ring w-6 text-indigo-400"></i> Help & Support
                        </a>
                        <a href="#" class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <i class="fas fa-file-alt w-6 text-indigo-400"></i> Terms and Policies
                        </a>
                        <a href="#" class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <i class="fas fa-flag w-6 text-indigo-400"></i> Report a problem
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="mt-4 w-full">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan: Form Fields -->
                <div class="lg:col-span-2">
                    <!-- Nama -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                                  class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('bio') border-red-500 @enderror">{{ old('bio', Auth::user()->bio) }}</textarea>
                        @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Row: Tanggal Lahir & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', Auth::user()->birth_date ? Auth::user()->birth_date->format('Y-m-d') : '') }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('birth_date') border-red-500 @enderror">
                            @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Row: Gender & Country -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                            <select name="gender" id="gender"
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                                <option value="">Pilih</option>
                                <option value="male" {{ old('gender', Auth::user()->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ old('gender', Auth::user()->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country/Region</label>
                            <input type="text" name="country" id="country" value="{{ old('country', Auth::user()->country ?? 'Indonesia') }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('country') border-red-500 @enderror">
                            @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <textarea name="address" id="address" rows="2"
                                  class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', Auth::user()->address) }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Row: Semester & Poin/Rank -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                            <input type="text" name="semester" id="semester" value="{{ old('semester', Auth::user()->semester) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('semester') border-red-500 @enderror">
                            @error('semester') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Poin / Rank</label>
                            <div class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                                {{ Auth::user()->total_points ?? 0 }} poin (Rank: #{{ Auth::user()->rank ?? '-' }})
                            </div>
                        </div>
                    </div>

                    <!-- Portofolio -->
                    <div class="mb-4">
                        <label for="portfolio_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link Portofolio</label>
                        <input type="url" name="portfolio_url" id="portfolio_url" value="{{ old('portfolio_url', Auth::user()->portfolio_url) }}"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('portfolio_url') border-red-500 @enderror" placeholder="https://portofolio-saya.com">
                        @error('portfolio_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Sosial Media (dinamis) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sosial Media</label>
                        <div id="social-media-container">
                            @php
                                $socials = old('social_media', Auth::user()->social_media ?? []);
                            @endphp
                            @if(count($socials) > 0)
                                @foreach($socials as $index => $soc)
                                    <div class="social-media-row mb-2">
                                        <select name="social_media[{{ $index }}][platform]" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                            <option value="instagram" {{ $soc['platform'] == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                            <option value="facebook" {{ $soc['platform'] == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                            <option value="telegram" {{ $soc['platform'] == 'telegram' ? 'selected' : '' }}>Telegram</option>
                                            <option value="x" {{ $soc['platform'] == 'x' ? 'selected' : '' }}>X (Twitter)</option>
                                        </select>
                                        <input type="url" name="social_media[{{ $index }}][url]" value="{{ $soc['url'] }}" placeholder="https://..."
                                               class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                                        <button type="button" class="remove-social-btn text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="social-media-row mb-2">
                                    <select name="social_media[0][platform]" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                        <option value="instagram">Instagram</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="telegram">Telegram</option>
                                        <option value="x">X (Twitter)</option>
                                    </select>
                                    <input type="url" name="social_media[0][url]" placeholder="https://..."
                                           class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                                    <button type="button" class="remove-social-btn text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-social-btn" class="mt-1 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                            <i class="fas fa-plus-circle"></i> Tambah Sosial Media
                        </button>
                        @error('social_media') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keahlian -->
                    <div class="mb-4">
                        <label for="skills" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Keahlian (pisahkan dengan koma)</label>
                        <input type="text" name="skills" id="skills" value="{{ old('skills', is_array(Auth::user()->skills) ? implode(', ', Auth::user()->skills) : '') }}"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('skills') border-red-500 @enderror" placeholder="backend, frontend, ui/ux, ...">
                        @error('skills') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- PIN khusus owner -->
                    @if(Auth::user()->level_id == 1)
                        <div class="mb-4">
                            <label for="pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PIN Keamanan</label>
                            <input type="password" name="pin" id="pin" value="{{ old('pin') }}" maxlength="10"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('pin') border-red-500 @enderror" placeholder="Masukkan PIN (4-10 digit)">
                            @error('pin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <!-- Password -->
                    <div class="form-divider"></div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password <span class="text-xs text-gray-400">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="password"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                       <a href="{{ route('profile.public', Auth::user()->id) }}" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">Batal</a>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-bold rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview avatar
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatar-preview').src = event.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    });

    // Tambah sosial media
    document.getElementById('add-social-btn').addEventListener('click', function() {
        const container = document.getElementById('social-media-container');
        const index = container.querySelectorAll('.social-media-row').length;
        const row = document.createElement('div');
        row.className = 'social-media-row mb-2';
        row.innerHTML = `
            <select name="social_media[${index}][platform]" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                <option value="instagram">Instagram</option>
                <option value="facebook">Facebook</option>
                <option value="telegram">Telegram</option>
                <option value="x">X (Twitter)</option>
            </select>
            <input type="url" name="social_media[${index}][url]" placeholder="https://..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-indigo-500">
            <button type="button" class="remove-social-btn text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
        row.querySelector('.remove-social-btn').addEventListener('click', function() {
            row.remove();
        });
    });

    // Hapus sosial media (existing)
    document.querySelectorAll('.remove-social-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.social-media-row').remove();
        });
    });
</script>
@endpush
@endsection