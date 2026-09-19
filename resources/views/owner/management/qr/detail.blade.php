@extends('layouts.roles.owner')

@section('title', 'Detail QR Code – Owner UNICODE')
@section('page-title', 'Detail QR Credential')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-qrcode text-cyan-600 mr-2"></i> Detail QR Code
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Informasi lengkap credential QR untuk user.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('owner.management.qr.index') }}"
                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <form action="{{ route('owner.management.qr.cleanup-revoked') }}" method="POST" class="inline" onsubmit="return confirm('Hapus semua QR yang sudah dicabut?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus QR Dicabut
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: INFORMASI USER & DAFTAR QR --}}
            <div class="lg:col-span-2 space-y-4">
                {{-- Kartu Profil User --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white">{{ $user->name }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 text-sm">
                                <div><span class="text-gray-500 dark:text-gray-400">NIM:</span> <span class="font-medium">{{ $user->nim ?? '-' }}</span></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium">{{ $user->email ?? '-' }}</span></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Role:</span> <span class="px-2 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200 rounded-full text-xs">{{ $user->level->name ?? 'N/A' }}</span></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Status Akun:</span> <span class="px-2 py-1 {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }} rounded-full text-xs">{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Member Number:</span> <span class="font-mono">{{ $user->member_number ?? '-' }}</span></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Bergabung:</span> <span class="text-gray-700 dark:text-gray-300">{{ $user->created_at->format('d/m/Y H:i') }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Daftar QR Codes --}}
                <div>
                    <form id="deleteSelectedForm" action="{{ route('owner.management.qr.delete-selected') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ids" id="selectedIds" value="">

                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-list mr-2"></i> Daftar QR Credential
                            </h4>
                            <button type="button" onclick="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                    <tr>
<th class="px-2 py-2">
    <input
        type="checkbox"
        id="selectAll"
        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600
               text-emerald-500 focus:ring-emerald-500
               accent-emerald-500 cursor-pointer"
    >
</th>                                        <th class="px-4 py-2">#</th>
                                        <th class="px-4 py-2">UUID</th>
                                        <th class="px-4 py-2">Status</th>
                                        <th class="px-4 py-2">Dibuat</th>
                                        <th class="px-4 py-2">Kadaluarsa</th>
                                        <th class="px-4 py-2">Scan</th>
                                        <th class="px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                               <tbody>
    @forelse($user->qrCodes as $qr)
        @php
            $statusKey = $qr->is_active && !$qr->is_revoked
                ? 'active'
                : ($qr->is_revoked ? 'revoked' : 'inactive');
        @endphp
        <tr class="qr-row border-b border-gray-200 dark:border-gray-700
                   hover:bg-emerald-50 dark:hover:bg-emerald-900/10
                   transition-colors cursor-pointer
                   {{ $statusKey !== 'active' ? 'opacity-75' : '' }}"
            data-qr-id="{{ $qr->id }}"
            data-status="{{ $statusKey }}">

            <td class="px-2 py-2 text-center">
                <input
                    type="checkbox"
                    class="qr-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600
       text-emerald-500 focus:ring-emerald-500
       accent-emerald-500 cursor-pointer"
                    value="{{ $qr->id }}"
                    data-status="{{ $statusKey }}"
                >
            </td>
            <td class="px-4 py-2">{{ $loop->iteration }}</td>
            <td class="px-4 py-2 font-mono text-xs">{{ $qr->qr_uuid }}</td>
            <td class="px-4 py-2">
                @if($statusKey === 'active')
                    <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full text-xs">
                        <i class="fas fa-check-circle"></i> Aktif
                    </span>
                @elseif($statusKey === 'revoked')
                    <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full text-xs">
                        <i class="fas fa-ban"></i> Dicabut
                    </span>
                @else
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 rounded-full text-xs">
                        <i class="fas fa-pause-circle"></i> Tidak Aktif
                    </span>
                @endif
            </td>
            <td class="px-4 py-2">{{ $qr->generated_at ? $qr->generated_at->format('d/m/Y H:i') : '-' }}</td>
            <td class="px-4 py-2">{{ $qr->expired_at ? $qr->expired_at->format('d/m/Y H:i') : 'Tidak terbatas' }}</td>
            <td class="px-4 py-2">{{ $qr->scan_count ?? 0 }}</td>
            <td class="px-4 py-2 text-center" onclick="event.stopPropagation()">
                @if($statusKey === 'active')
                    <form action="{{ route('owner.management.qr.revoke', $user->id) }}"
                          method="POST"
                          class="inline-block"
                          onsubmit="return confirm('Cabut QR ini secara permanen?')">
                        @csrf
                        <button type="submit"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                            <i class="fas fa-ban"></i> Cabut
                        </button>
                    </form>
                    <span class="text-gray-300 mx-1">|</span>
                @endif
                <button type="button"
                        onclick="event.stopPropagation(); deleteSingle({{ $qr->id }})"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                    <i class="fas fa-trash-alt"></i> Hapus
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-4 py-4 text-center text-gray-400">
                User ini belum memiliki QR Code.
            </td>
        </tr>
    @endforelse
</tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KOLOM KANAN: PREVIEW QR AKTIF --}}
            <div class="lg:col-span-1 space-y-4">
                @php $activeQr = $user->qrCodes->where('is_active', true)->where('is_revoked', false)->first(); @endphp

                @if($activeQr)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-5 border border-gray-200 dark:border-gray-600 text-center">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">QR Aktif</h4>

                        @php $qrExists = $activeQr->qr_image && file_exists(public_path('storage/' . $activeQr->qr_image)); @endphp
                        @if($qrExists)
                            <img src="{{ asset('storage/' . $activeQr->qr_image) }}" alt="QR Code" class="mx-auto max-w-full h-auto" style="max-height: 200px;">
                        @else
                            <div class="bg-gray-200 dark:bg-gray-600 rounded-lg p-4 text-gray-500 dark:text-gray-400 text-sm"><i class="fas fa-image mr-1"></i> Gambar QR belum tersedia</div>
                        @endif

                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 break-all"><strong>UUID:</strong> {{ $activeQr->qr_uuid }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><strong>Token:</strong> {{ substr($activeQr->token, 0, 20) }}...</div>

                        {{-- Info tambahan --}}
                        <div class="mt-3 grid grid-cols-2 gap-1 text-xs">
                            <div class="text-gray-500">Dibuat:</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ $activeQr->generated_at ? $activeQr->generated_at->format('d/m/Y H:i') : '-' }}</div>
                            <div class="text-gray-500">Kadaluarsa:</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ $activeQr->expired_at ? $activeQr->expired_at->format('d/m/Y H:i') : 'Tidak terbatas' }}</div>
                            <div class="text-gray-500">Scan count:</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ $activeQr->scan_count ?? 0 }}</div>
                            <div class="text-gray-500">Terakhir scan:</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ $activeQr->last_used_at ? $activeQr->last_used_at->diffForHumans() : 'Belum pernah' }}</div>
                        </div>
{{-- ============================================================
     TOMBOL DOWNLOAD — AKTIF HANYA SAAT ADA CENTANG
     ============================================================ --}}
<div class="mt-4 space-y-4">

    {{-- Info selection --}}
    <div id="selectionInfo"
         class="rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/60 px-3 py-2 text-xs text-center text-slate-600 dark:text-slate-300">
        <i class="fas fa-info-circle text-blue-500"></i>
        <span id="selectionCount">Belum ada QR dipilih</span>
        —
        <span class="text-slate-500">
            centang kotak pada tabel di kiri untuk mengaktifkan tombol download
        </span>
    </div>

    {{-- DOWNLOAD --}}
    <div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 text-center">
            <i class="fas fa-download text-blue-500"></i>
            <strong>Download QR Terpilih</strong>
        </p>
        <div class="flex flex-wrap justify-center gap-2">
            @foreach(['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'] as $fmt)
                <button type="button"
                        data-format="{{ $fmt }}"
                        class="qr-download-btn px-3 py-1 rounded-lg text-xs font-bold transition
                               bg-blue-600 text-white hover:bg-blue-700
                               disabled:bg-slate-300 disabled:text-slate-500
                               disabled:cursor-not-allowed
                               dark:disabled:bg-slate-700 dark:disabled:text-slate-500"
                        disabled>
                    <i class="fas fa-download"></i> {{ strtoupper($fmt) }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ROTATE --}}
    <div class="border-t border-amber-200 dark:border-amber-800 pt-4">
        <p class="text-xs text-amber-700 dark:text-amber-300 mb-2 text-center">
            <i class="fas fa-sync-alt text-amber-500"></i>
            <strong>Rotate</strong> — QR lama dicabut, QR baru dibuat &amp; aktif
        </p>
        <div class="flex flex-wrap justify-center gap-2">
            <form action="{{ route('owner.management.qr.rotate', $user->id) }}"
                  method="POST"
                  class="inline-block"
                  onsubmit="return confirm('Rotate QR sekarang?\n\nQR LAMA akan DICABUT (tidak berlaku lagi).\nQR BARU akan dibuat dan langsung aktif.')">
                @csrf
                <button type="submit"
                        class="px-4 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-bold hover:bg-purple-700 transition">
                    <i class="fas fa-redo"></i> Rotate QR
                </button>
            </form>

            <a href="{{ route('owner.management.qr.history', $user->id) }}"
               class="px-4 py-1.5 bg-slate-600 text-white rounded-lg text-xs font-bold hover:bg-slate-700 transition">
                <i class="fas fa-history"></i> History QR
            </a>
        </div>
    </div>
</div>

                        {{-- 🔥 Tombol Repair (dengan pengecekan route) --}}
                        @if(Route::has('owner.management.qr.repair'))
                        <div class="mt-3">
                            <form action="{{ route('owner.management.qr.repair', $activeQr->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Perbaiki QR ini? (credential tetap sama)')">
                                @csrf
                                <input type="hidden" name="format" value="png">
                                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm hover:bg-amber-700 transition">
                                    <i class="fas fa-wrench mr-1"></i> Perbaiki QR
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="mt-3 text-xs text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i> Fitur repair tidak tersedia
                        </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-5 border border-gray-200 dark:border-gray-600 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-qrcode text-4xl mb-2"></i>
                        <p>Tidak ada QR aktif</p>
                        <a href="{{ route('owner.management.qr.generate') }}" class="mt-3 inline-block px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm hover:bg-cyan-700 transition">
                            <i class="fas fa-plus mr-1"></i> Generate QR
                        </a>
                    </div>
                @endif

                {{-- Info --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-sm text-blue-700 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Prinsip:</strong> QR hanya berisi credential UUID. Data user diambil dari database saat scan.
                </div>
            </div>
        </div>
    </div>
</div>
<script>
/* ============================================================
   TOAST
   ============================================================ */
function showToast(message, duration = 3500, type = 'info') {
    let el = document.getElementById('qr-toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'qr-toast';
        el.style.cssText = `
            position: fixed; bottom: 24px; left: 50%;
            transform: translateX(-50%) translateY(20px);
            padding: 12px 20px; border-radius: 12px;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 12px 32px rgba(0,0,0,.35);
            z-index: 99999; opacity: 0;
            transition: all .3s ease;
            max-width: 90vw; text-align: center;
            color: #fff; background: #0f172a;
        `;
        document.body.appendChild(el);
    }
    const colors = {
        info: '#0f172a', success: '#059669',
        error: '#dc2626', warning: '#d97706',
    };
    el.style.background = colors[type] || colors.info;
    el.textContent = message;
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateX(-50%) translateY(0)';
    });
    clearTimeout(el._timer);
    el._timer = setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(-50%) translateY(20px)';
    }, duration);
}

/* ============================================================
   DELETE HELPERS
   ============================================================ */
function deleteSelected() {
    const ids = Array.from(document.querySelectorAll('.qr-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) { alert('Pilih minimal satu QR untuk dihapus.'); return; }
    if (!confirm(`Hapus ${ids.length} QR yang dipilih?`)) return;
    document.getElementById('selectedIds').value = JSON.stringify(ids);
    document.getElementById('deleteSelectedForm').submit();
}

function deleteSingle(id) {
    if (!confirm('Hapus QR ini secara permanen?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('owner.management.qr.delete-selected') }}";
    form.innerHTML = `@csrf @method('DELETE') <input type="hidden" name="ids" value='[${id}]'>`;
    document.body.appendChild(form);
    form.submit();
}

/* ============================================================
   MAIN
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    const $tbody          = document.querySelector('table tbody');
    const selectAll       = document.getElementById('selectAll');
    const downloadBtns    = document.querySelectorAll('.qr-download-btn');
    const infoBox         = document.getElementById('selectionInfo');
    const baseUrl         = '{{ url("owner/management/qr/download-qr") }}';

    const getCheckboxes = () => Array.from(document.querySelectorAll('.qr-checkbox'));
    const getRows       = () => Array.from(document.querySelectorAll('.qr-row'));
    const getChecked    = () => getCheckboxes().filter(cb => cb.checked);

    /* ---------- UPDATE UI ---------- */
    function updateUI() {
        const checked = getChecked();
        const all     = getCheckboxes();
        const total   = checked.length;

        // Highlight baris
        getRows().forEach(row => {
            const cb = row.querySelector('.qr-checkbox');
            if (cb && cb.checked) {
                row.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                row.style.boxShadow = 'inset 4px 0 0 #10b981';
            } else {
                row.style.backgroundColor = '';
                row.style.boxShadow = '';
            }
        });

        // Select all
        if (selectAll) {
            selectAll.checked       = total > 0 && total === all.length;
            selectAll.indeterminate = total > 0 && total < all.length;
        }

        // Cek blokir
        const hasBlocked  = checked.some(cb => cb.dataset.status !== 'active');
        const canDownload = total > 0 && !hasBlocked;

        // Tombol
        downloadBtns.forEach(btn => {
            btn.disabled = !canDownload;
            btn.title = hasBlocked
                ? 'QR dicabut / tidak aktif tidak bisa di-download'
                : (total === 0 ? 'Pilih QR dulu' : 'Klik untuk download');
        });

        // Info box
        if (infoBox) {
            if (hasBlocked) {
                infoBox.className = 'rounded-lg border border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-3 py-2 text-xs text-center text-red-700 dark:text-red-300';
                infoBox.innerHTML = `<i class="fas fa-ban text-red-500"></i>
                    <strong>QR dicabut / tidak aktif terpilih</strong> —
                    hanya QR <strong>Aktif</strong> yang bisa di-download.`;
            } else if (total === 0) {
                infoBox.className = 'rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/60 px-3 py-2 text-xs text-center text-slate-600 dark:text-slate-300';
                infoBox.innerHTML = `<i class="fas fa-info-circle text-blue-500"></i>
                    Belum ada QR dipilih —
                    <span class="text-slate-500">klik pada baris tabel untuk memilih</span>`;
            } else {
                infoBox.className = 'rounded-lg border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-2 text-xs text-center text-emerald-700 dark:text-emerald-300';
                infoBox.innerHTML = `<i class="fas fa-check-circle text-emerald-500"></i>
                    <strong>${total} QR dipilih</strong> — pilih format download di bawah`;
            }
        }
    }

    /* ============================================================
       ROW CLICK — EVENT DELEGATION (paling reliable)
       ============================================================ */
    if ($tbody) {
        $tbody.addEventListener('click', function (e) {
            // Kalau klik di tombol/link/form → skip
            if (e.target.closest('button, a, form, input[type="submit"], input[type="button"]')) {
                return;
            }

            // Cari row terdekat
            const row = e.target.closest('.qr-row');
            if (!row) return;

            // Toggle checkbox di row ini
            const cb = row.querySelector('.qr-checkbox');
            if (!cb) return;

            cb.checked = !cb.checked;
            updateUI();
        });

        // Prevent checkbox langsung diklik (biar konsisten lewat row)
        $tbody.addEventListener('change', function (e) {
            if (e.target.classList.contains('qr-checkbox')) {
                // Kalau checkbox ke-trigger by keyboard (space/enter), sinkronkan UI
                updateUI();
            }
        });
    }

    /* ---------- SELECT ALL ---------- */
    if (selectAll) {
        selectAll.addEventListener('click', function (e) {
            e.stopPropagation();
            const checked = this.checked;
            getCheckboxes().forEach(cb => { cb.checked = checked; });
            updateUI();
        });
    }

    /* ============================================================
       DOWNLOAD — PAKAI <a> ELEMENT (paling reliable)
       ============================================================ */
    function triggerDownload(url) {
        const a = document.createElement('a');
        a.href = url;
        a.style.display = 'none';
        // Jangan set download="" karena server sudah set Content-Disposition
        document.body.appendChild(a);
        a.click();
        setTimeout(() => a.remove(), 500);
    }

    downloadBtns.forEach(btn => {
        btn.addEventListener('click', async function () {
            const checked = getChecked();

            if (checked.length === 0) {
                showToast('⚠️ Klik pada baris tabel dulu untuk memilih QR', 3000, 'warning');
                return;
            }

            const blocked = checked.filter(cb => cb.dataset.status !== 'active');
            if (blocked.length > 0) {
                showToast(`❌ ${blocked.length} QR dicabut/tidak aktif. Hanya QR Aktif yang bisa di-download.`, 5000, 'error');
                return;
            }

            const format = this.dataset.format;
            const urls = checked.map(cb => `${baseUrl}/${cb.value}/${format}`);

            console.log('[QR] Download URLs:', urls);

            showToast(`⬇️ Mengunduh ${urls.length} QR (${format.toUpperCase()})...`, 3000, 'info');

            // Trigger download satu-per-satu dengan delay
            urls.forEach((url, i) => {
                setTimeout(() => {
                    triggerDownload(url);
                }, i * 500);
            });

            // Feedback selesai
            setTimeout(() => {
                showToast(`✅ ${urls.length} file dikirim ke browser. Cek folder Downloads.`, 4000, 'success');
                // Clear selection
                getCheckboxes().forEach(cb => { cb.checked = false; });
                updateUI();
            }, urls.length * 500 + 800);
        });
    });

    /* ---------- INIT ---------- */
    updateUI();
});
</script>
@endsection