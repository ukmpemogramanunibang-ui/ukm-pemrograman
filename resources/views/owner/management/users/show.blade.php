@extends('layouts.roles.owner')

@section('title', 'Detail Pengguna – Owner UNICODE')
@section('page-title', 'Detail Pengguna')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .detail-item {
        padding: 0.75rem 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border-left: 3px solid #06b6d4;
    }
    .dark .detail-item {
        background: #1f2937;
        border-left-color: #22d3ee;
    }
    .detail-item label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        display: block;
        margin-bottom: 0.2rem;
    }
    .dark .detail-item label {
        color: #9ca3af;
    }
    .detail-item .value {
        font-size: 0.9rem;
        font-weight: 500;
        color: #1f2937;
    }
    .dark .detail-item .value {
        color: #f3f4f6;
    }
    .avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid #06b6d4;
        box-shadow: 0 4px 14px rgba(6, 182, 212, 0.3);
    }
    .badge-level {
        display: inline-block;
        padding: 0.2rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(6, 182, 212, 0.12);
        color: #06b6d4;
    }
    .dark .badge-level {
        background: rgba(6, 182, 212, 0.2);
        color: #22d3ee;
    }
</style>
@endpush

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200/60 dark:border-gray-700/60">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Detail Pengguna</h3>
    </div>

    {{-- Header Profil --}}
    <div class="flex items-center gap-6 mb-6 p-4 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl border border-gray-200 dark:border-gray-700">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563EB&color=fff&size=128" 
             alt="Avatar" class="avatar-large">
        <div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h4>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="badge-level">{{ optional($user->level)->name ?? 'N/A' }}</span>
                <span class="badge-status {{ $user->is_active ? 'active' : 'inactive' }}">
                    <span class="dot"></span> {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                @if($user->is_verified)
                <span class="badge-role" style="background: rgba(34,197,94,0.15); color: #22c55e;">
                    <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Bergabung: {{ $user->created_at->format('d M Y, H:i') }}
            </p>
        </div>
    </div>

    {{-- Data Detail --}}
    <div class="detail-grid">
        <div class="detail-item">
            <label>NIM</label>
            <div class="value">{{ $user->nim ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Nama Lengkap</label>
            <div class="value">{{ $user->name }}</div>
        </div>
        <div class="detail-item">
            <label>Email</label>
            <div class="value">{{ $user->email ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Nomor HP</label>
            <div class="value">{{ $user->phone ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Username</label>
            <div class="value">{{ $user->username ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Level</label>
            <div class="value">{{ optional($user->level)->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Status</label>
            <div class="value">{{ optional($user->status)->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Universitas</label>
            <div class="value">{{ optional($user->university)->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Jurusan</label>
            <div class="value">{{ optional($user->major)->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Member Number</label>
            <div class="value">{{ $user->member_number ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Tipe Akun</label>
            <div class="value">{{ $user->account_type ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Jenis Kelamin</label>
            <div class="value">{{ $user->gender ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Tanggal Lahir</label>
            <div class="value">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d M Y') : '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Alamat</label>
            <div class="value">{{ $user->address ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Bio</label>
            <div class="value">{{ $user->bio ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Last Login</label>
            <div class="value">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : '-' }}</div>
        </div>
        <div class="detail-item">
            <label>IP Login Terakhir</label>
            <div class="value">{{ $user->last_login_ip ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <label>Dibuat Pada</label>
            <div class="value">{{ $user->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div class="detail-item">
            <label>Diperbarui</label>
            <div class="value">{{ $user->updated_at->format('d M Y, H:i') }}</div>
        </div>
        @if($user->deleted_at)
        <div class="detail-item" style="border-left-color: #ef4444;">
            <label>Dihapus Pada</label>
            <div class="value text-red-600">{{ \Carbon\Carbon::parse($user->deleted_at)->format('d M Y, H:i') }}</div>
        </div>
        @endif
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-6 flex justify-end">
        <a href="{{ url()->previous() }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-status.active {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }
    .badge-status.inactive {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .badge-status .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-status.active .dot { background: #22c55e; }
    .badge-status.inactive .dot { background: #ef4444; }
</style>
@endpush