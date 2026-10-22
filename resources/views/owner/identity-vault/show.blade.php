@extends('layouts.roles.owner')

@section('title', 'Detail Identitas')
@section('page-title', 'Detail Identitas Permanen')

@section('content')
<div class="container">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">{{ $identity->credential_code }}</h3>
            <a href="{{ route('owner.identity-vault.index') }}" class="text-cyan-600 hover:underline">← Kembali</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p><strong>Credential UUID:</strong> {{ $identity->credential_uuid }}</p>
                <p><strong>User UUID:</strong> {{ $identity->user_uuid ?? '-' }}</p>
                <p><strong>Member Number:</strong> {{ $identity->member_number ?? '-' }}</p>
                <p><strong>Account Type:</strong> {{ $identity->account_type }}</p>
                <p><strong>Status:</strong> 
                    <span class="px-2 py-1 rounded-full text-xs {{ $identity->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $identity->status }}
                    </span>
                </p>
                <p><strong>QR Enabled:</strong> {{ $identity->qr_enabled ? 'Ya' : 'Tidak' }}</p>
                <p><strong>NFC Enabled:</strong> {{ $identity->nfc_enabled ? 'Ya' : 'Tidak' }}</p>
                <p><strong>Dibuat:</strong> {{ $identity->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Snapshot Akun</h4>
                @if($identity->account_snapshot)
                    <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-xs overflow-auto max-h-60">{{ json_encode($identity->account_snapshot, JSON_PRETTY_PRINT) }}</pre>
                @else
                    <p class="text-gray-500">Tidak ada snapshot.</p>
                @endif
            </div>
        </div>

        <div class="mt-6 border-t pt-4">
            <h4 class="font-semibold mb-2">Kartu NFC Terdaftar</h4>
            @if($identity->nfcCredentials->count())
                <ul class="list-disc pl-6">
                    @foreach($identity->nfcCredentials as $nfc)
                        <li>{{ $nfc->nfc_public_id ?? 'ID: '.substr($nfc->nfc_identifier_hash,0,10) }} (v{{ $nfc->version }}) - {{ $nfc->is_active ? 'Aktif' : 'Tidak Aktif' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Belum ada NFC.</p>
            @endif
        </div>

        <div class="mt-6 flex gap-2">
            <form action="{{ route('owner.identity-vault.recover') }}" method="POST">
                @csrf
                <input type="hidden" name="credential_uuid" value="{{ $identity->credential_uuid }}">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    <i class="fas fa-undo-alt mr-2"></i>Recover Akun
                </button>
            </form>
            <a href="{{ route('owner.identity-vault.audit', $identity->credential_uuid) }}" class="bg-cyan-500 text-white px-4 py-2 rounded hover:bg-cyan-600">
                <i class="fas fa-history mr-2"></i>Lihat Audit
            </a>
        </div>
    </div>
</div>
@endsection