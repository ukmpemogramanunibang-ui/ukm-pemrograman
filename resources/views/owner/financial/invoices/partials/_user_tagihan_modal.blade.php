<div class="space-y-4">
    {{-- Informasi User --}}
    <div class="flex flex-wrap justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Nama: <span class="font-semibold text-gray-800 dark:text-white">{{ $user->name }}</span></p>
            <p class="text-sm text-gray-500 dark:text-gray-400">NIM: <span class="font-semibold text-gray-800 dark:text-white">{{ $user->nim ?? '-' }}</span></p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Role: <span class="font-semibold text-gray-800 dark:text-white">{{ $user->level->name ?? '-' }}</span></p>
        </div>
        <div>
            <a href="{{ route('owner.financial.fee-plans.index') }}" target="_blank" class="text-blue-500 hover:underline text-sm flex items-center gap-1">
                <i class="fas fa-external-link-alt"></i> Lihat Fee Plans
            </a>
        </div>
    </div>

    {{-- Daftar Tagihan --}}
    @if($invoices->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nama</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Generasi</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Jenis</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nominal</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Frekuensi</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Target</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($invoices as $inv)
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap font-mono text-xs text-gray-600 dark:text-gray-400">{{ $inv->invoice_number }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-800 dark:text-white">{{ $inv->feePlan->name ?? '-' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $inv->feePlan->generation->name ?? '-' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', ucfirst($inv->type)) }}</td>
                        <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-800 dark:text-white">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $inv->feePlan->frequency ?? '-' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $inv->feePlan->target ?? '-' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $inv->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                   ($inv->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                   ($inv->status == 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400')) }}">
                                {{ strtoupper($inv->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            Total tagihan: <span class="font-semibold">{{ $invoices->count() }}</span>
        </div>
    @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-file-invoice text-4xl block mb-2 text-gray-300 dark:text-gray-600"></i>
            Belum ada tagihan untuk user ini.
        </div>
    @endif
</div>