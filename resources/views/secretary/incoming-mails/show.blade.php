<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm text-gray-500">Nomor Surat</label>
            <p class="font-semibold">{{ $incomingMail->nomor_surat }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Status</label>
            <p><span class="px-2 py-1 text-xs rounded-full {{ $incomingMail->status_badge }}">{{ $incomingMail->status_label }}</span></p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Tanggal Surat</label>
            <p>{{ $incomingMail->tanggal_surat->format('d F Y') }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Tanggal Diterima</label>
            <p>{{ $incomingMail->tanggal_diterima->format('d F Y') }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Pengirim</label>
            <p>{{ $incomingMail->pengirim }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Kategori</label>
            <p>{{ $incomingMail->kategori }}</p>
        </div>
        <div class="col-span-2">
            <label class="text-sm text-gray-500">Perihal</label>
            <p class="font-medium">{{ $incomingMail->perihal }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Ditujukan Kepada</label>
            <p>{{ $incomingMail->tujuan ?? '-' }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Dicatat Oleh</label>
            <p>{{ $incomingMail->pencatat->name ?? '-' }}</p>
        </div>
        @if($incomingMail->diteruskan_kepada)
        <div>
            <label class="text-sm text-gray-500">Diteruskan Kepada</label>
            <p>{{ $incomingMail->diteruskanKe->name ?? '-' }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Diteruskan Pada</label>
            <p>{{ $incomingMail->diteruskan_pada ? $incomingMail->diteruskan_pada->format('d F Y H:i') : '-' }}</p>
        </div>
        @endif
        <div class="col-span-2">
            <label class="text-sm text-gray-500">Catatan</label>
            <p class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">{{ $incomingMail->catatan ?? '-' }}</p>
        </div>
        @if($incomingMail->lampiran)
        <div class="col-span-2">
            <label class="text-sm text-gray-500">Lampiran</label>
            <div class="flex items-center gap-3 mt-1">
                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                <span>{{ basename($incomingMail->lampiran) }}</span>
                <a href="{{ asset('storage/' . $incomingMail->lampiran) }}" target="_blank" class="text-blue-600 hover:underline text-sm">
                    <i class="fas fa-eye mr-1"></i> Lihat
                </a>
                <a href="{{ asset('storage/' . $incomingMail->lampiran) }}" download class="text-green-600 hover:underline text-sm">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
            </div>
        </div>
        @endif
    </div>
</div>