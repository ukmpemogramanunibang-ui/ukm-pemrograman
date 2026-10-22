@extends('layouts.roles.owner')

@section('title', 'Identity Vault')
@section('page-title', 'Identity Vault')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Daftar Identitas Permanen</h2>
        <div class="flex gap-2">
            <form action="{{ route('owner.identity-vault.recover-all') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600">
                    <i class="fas fa-undo-alt mr-2"></i>Recovery Massal
                </button>
            </form>
            <a href="{{ route('owner.identity-vault.test') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                <i class="fas fa-flask mr-2"></i>Test Recovery
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-sm text-gray-600">Total Identitas</p>
            <p class="text-2xl font-bold">{{ $total }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-sm text-gray-600">Aktif</p>
            <p class="text-2xl font-bold">{{ $active }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <p class="text-sm text-gray-600">Diblokir / Dicabut</p>
            <p class="text-2xl font-bold">{{ $blocked }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credential</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User UUID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NFC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($identities as $identity)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium">{{ $identity->credential_code }}</div>
                        <div class="text-xs text-gray-500">{{ $identity->credential_uuid }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $identity->user_uuid ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs {{ $identity->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $identity->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $identity->nfcCredentials->count() }} kartu
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('owner.identity-vault.show', $identity->credential_uuid) }}" class="text-cyan-600 hover:underline">Detail</a>
                        <form action="{{ route('owner.identity-vault.recover') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="credential_uuid" value="{{ $identity->credential_uuid }}">
                            <button type="submit" class="text-green-600 hover:underline ml-2">Recover</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada identitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $identities->links() }}
</div>
@endsection