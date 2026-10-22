@extends('layouts.roles.owner')

@section('title', 'Audit Identitas')
@section('page-title', 'Log Audit Credential')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Log Aktivitas</h3>
        <a href="{{ route('owner.identity-vault.index') }}" class="text-cyan-600 hover:underline">← Kembali</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metadata</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($logs as $log)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $log->action }}</td>
                    <td class="px-6 py-4 text-sm">{{ $log->ip_address ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($log->metadata)
                            <pre class="text-xs">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada log.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
</div>
@endsection