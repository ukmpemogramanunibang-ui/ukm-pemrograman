{{-- resources/views/cek-akun/partials/capability.blade.php --}}

<div class="glass-panel rounded-3xl p-5 mb-5">
    <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-800/80 flex items-center justify-center text-slate-300">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div>
                <div class="text-white font-extrabold text-sm">Device Capabilities</div>
                <div class="text-xs text-slate-500">Pemeriksaan otomatis perangkat Anda</div>
            </div>
        </div>
        <button type="button" onclick="window.AccountCheckCapability.refresh()" class="secondary-btn text-xs">
            <i class="fa-solid fa-rotate mr-1"></i> Refresh
        </button>
    </div>

    <div id="capabilityGrid" class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
        {{-- diisi JS --}}
    </div>
</div>

@push('scripts')
<script>
window.AccountCheckCapability = (() => {
    const $ = (id) => document.getElementById(id);

    function row(label, ok, extra = '') {
        const cls = ok
            ? 'border-emerald-400/20 bg-emerald-400/5 text-emerald-200'
            : 'border-red-400/20 bg-red-400/5 text-red-200';
        const icon = ok ? 'fa-circle-check' : 'fa-circle-xmark';
        return `
            <div class="rounded-xl border ${cls} px-3 py-2 flex items-center gap-2">
                <i class="fa-solid ${icon}"></i>
                <div class="flex-1">
                    <div class="font-bold">${label}</div>
                    ${extra ? `<div class="text-[10px] opacity-80">${extra}</div>` : ''}
                </div>
            </div>`;
    }

    function refresh() {
        const grid = $('capabilityGrid');
        if (!grid) return;

        const secure = window.isSecureContext ||
            ['localhost','127.0.0.1'].includes(location.hostname);

        const hasCamera = !!navigator.mediaDevices?.getUserMedia;
        const hasBarcode = 'BarcodeDetector' in window;
        const hasNfc = 'NDEFReader' in window;

        grid.innerHTML = [
            row('HTTPS / Secure', secure, secure ? 'Aktif' : 'Wajib untuk kamera & NFC'),
            row('Camera', hasCamera, hasCamera ? 'Tersedia' : 'Tidak didukung'),
            row('Barcode Detection', hasBarcode, hasBarcode ? 'Native browser' : 'Gunakan upload/manual'),
            row('Web NFC', hasNfc, hasNfc ? 'Tersedia' : 'Gunakan input manual'),
        ].join('');
    }

    document.addEventListener('DOMContentLoaded', refresh);

    return { refresh };
})();
</script>
@endpush