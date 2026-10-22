@extends('layouts.roles.owner')
@section('title', 'History QR – ' . $user->name)
@section('page-title', 'History QR Credential')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-history text-cyan-600 mr-2"></i> History QR — {{ $user->name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Semua QR pernah dibuat untuk user ini. QR lama tetap tersimpan untuk audit.
            </p>
        </div>
        <a href="{{ route('owner.management.qr.detail', $user->id) }}"
           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-3 py-2">Ver</th>
                    <th class="px-3 py-2">UUID</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Dibuat</th>
                    <th class="px-3 py-2">Dicabut</th>
                    <th class="px-3 py-2">Alasan</th>
                    <th class="px-3 py-2">Digantikan Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $qr)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-3 py-2 font-bold text-cyan-600">
                            v{{ $qr->version ?? 1 }}
                        </td>
                        <td class="px-3 py-2 font-mono text-xs">{{ $qr->qr_uuid }}</td>
                        <td class="px-3 py-2">
                            @if($qr->is_active && !$qr->is_revoked)
                                <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full text-xs">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @elseif($qr->is_revoked)
                                <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full text-xs">
                                    <i class="fas fa-ban"></i> Dicabut
                                </span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 rounded-full text-xs">
                                    <i class="fas fa-pause-circle"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs">
                            {{ $qr->generated_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-xs">
                            {{ $qr->revoked_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-500">
                            {{ $qr->reason_revoked ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-xs">
                            @if($qr->replaced_by_id)
                                <a href="#qr-{{ $qr->replaced_by_id }}"
                                   class="text-cyan-600 hover:underline">
                                    #{{ $qr->replaced_by_id }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-gray-400">
                            Belum ada history QR.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection