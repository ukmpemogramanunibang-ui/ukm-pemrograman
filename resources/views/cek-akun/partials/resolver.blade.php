{{-- resources/views/cek-akun/partials/resolver.blade.php --}}

<script>
window.AccountCheck = (() => {

    const ENDPOINT   = @json(route('account.check.resolve'));
    const CSRF_TOKEN = @json(csrf_token());

    const state = {
        cameraStream: null, cameraTimer: null, cameraBusy: false,
        nfcController: null, pending: new Map(), results: new Map(),
        barcodeFormats: null,
    };

    const $ = (id) => document.getElementById(id);

    function escapeHtml(v) {
        return String(v ?? '')
            .replaceAll('&','&amp;').replaceAll('<','&lt;')
            .replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');
    }

    function showSystemStatus(message, type = 'info') {
        const el = $('systemStatus');
        if (!el) return;
        const classes = {
            info:    'border-cyan-400/15 bg-cyan-400/5 text-cyan-200',
            good:    'border-emerald-400/15 bg-emerald-400/5 text-emerald-200',
            warning: 'border-amber-400/15 bg-amber-400/5 text-amber-200',
            danger:  'border-red-400/15 bg-red-400/5 text-red-200',
        };
        el.className = 'mb-4 rounded-2xl border px-4 py-3 text-sm ' + (classes[type] || classes.info);
        el.textContent = message;
        el.classList.remove('hidden');
    }
    function hideSystemStatus() { $('systemStatus')?.classList.add('hidden'); }

    function toneClass(tone) {
        return ({
            good: 'state-good', info: 'state-info',
            warning: 'state-warning', danger: 'state-danger',
        })[tone] || 'state-info';
    }

    async function resolveIdentifier(value, method) {
        value = String(value || '').trim();
        if (!value) {
            renderError({ error: {
                code: 'EMPTY_INPUT', title: 'Input kosong',
                message: 'Masukkan identifier terlebih dahulu.',
                hints: ['Isi NIM, UID, atau lakukan scan.'], method,
            }});
            return;
        }

        const key = `${method}:${value}`;
        if (state.pending.has(key)) return state.pending.get(key);

        showSystemStatus(`Memeriksa ${method.toUpperCase()}...`, 'info');

        const promise = fetch(ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ value, method }),
        })
        .then(async (response) => {
            const payload = await response.json().catch(() => ({
                ok: false,
                error: {
                    code: 'INVALID_SERVER_RESPONSE',
                    title: 'Respons server tidak valid',
                    message: 'Server memberikan respons yang tidak dapat diproses.',
                    hints: ['Coba ulangi.'], method,
                },
            }));
            if (!response.ok && !payload.error) {
                payload.error = {
                    code: `HTTP_${response.status}`,
                    title: 'Pemeriksaan gagal',
                    message: 'Permintaan tidak dapat diproses.',
                    hints: ['Coba ulangi.'], method,
                };
            }
            return payload;
        })
        .catch(() => ({
            ok: false,
            error: {
                code: 'NETWORK_ERROR', title: 'Koneksi gagal',
                message: 'Browser tidak dapat terhubung ke server UNICODE.',
                hints: ['Periksa koneksi internet.', 'Pastikan server aktif.', 'Ulangi beberapa saat lagi.'],
                method,
            },
        }))
        .finally(() => state.pending.delete(key));

        state.pending.set(key, promise);
        const payload = await promise;

        if (payload.ok) {
            renderResult(payload);
            showSystemStatus('Akun berhasil ditemukan dan diverifikasi.', 'good');
        } else {
            renderError(payload);
            showSystemStatus(payload?.error?.title || 'Pemeriksaan gagal.', 'warning');
        }
    }

    function renderResult(payload) {
        const data = payload.data;
        const identityKey = data.user_uuid || data.identity?.nim || crypto.randomUUID();

        const initials = escapeHtml(
            String(data.identity?.name || 'U').split(/\s+/).map(x => x[0] || '').slice(0, 2).join('').toUpperCase()
        );

        const avatarHtml = data.profile?.avatar_url
            ? `<img src="${escapeHtml(data.profile.avatar_url)}" alt="avatar" class="avatar-main" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`
            : '';
        const fallbackStyle = data.profile?.avatar_url ? 'display:none;' : 'display:flex;';

        const positions = Array.isArray(data.role?.positions) ? data.role.positions : [];
        const positionsHtml = positions.length
            ? positions.map(p => `<span class="inline-flex px-2.5 py-1 rounded-full bg-violet-400/10 border border-violet-400/15 text-violet-200 text-xs">${escapeHtml(p)}</span>`).join('')
            : `<span class="text-sm text-slate-500">Belum ada jabatan khusus.</span>`;

        const socialHtml = Array.isArray(data.social_media) && data.social_media.length
            ? data.social_media.map(s => s.url
                ? `<a href="${escapeHtml(s.url)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800/80 border border-slate-700/70 text-slate-200 text-xs hover:bg-slate-700 transition"><i class="fa-solid fa-link text-cyan-300"></i> ${escapeHtml(s.network)}</a>`
                : `<span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800/80 border border-slate-700/70 text-slate-300 text-xs">${escapeHtml(s.network)}: ${escapeHtml(s.value)}</span>`
            ).join('')
            : `<div class="text-sm text-amber-200/80">Pengguna belum memasukkan link sosial media.</div>`;

        const portfolioHtml = data.portfolio?.url
            ? `<a href="${escapeHtml(data.portfolio.url)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-400/10 border border-cyan-400/15 text-cyan-200 text-sm font-bold hover:bg-cyan-400/15 transition"><i class="fa-solid fa-globe"></i> Buka Portfolio</a>`
            : `<div class="rounded-2xl border border-amber-400/10 bg-amber-400/5 px-4 py-3 text-sm text-amber-200">Pengguna belum memasukkan link portfolio.</div>`;

        const diagnosticsHtml = Array.isArray(data.diagnostics)
            ? data.diagnostics.map(item => `
                <div class="rounded-2xl border border-slate-700/70 bg-slate-950/30 p-4">
                    <div class="text-[10px] uppercase tracking-widest text-slate-500 mb-1">${escapeHtml(item.label)}</div>
                    <div class="px-2.5 py-1 rounded-lg inline-flex ${toneClass(item.tone)} border text-xs font-bold">${escapeHtml(item.value)}</div>
                </div>`).join('')
            : '';

        const verification = data.verification || {};

        const card = document.createElement('article');
        card.dataset.identityKey = identityKey;
        card.className = 'result-card glass-panel rounded-[2rem] overflow-hidden';

        card.innerHTML = `
            <div class="p-5 md:p-8">
                <div class="flex flex-col lg:flex-row gap-7">
                    <div class="flex-shrink-0 flex justify-center lg:justify-start">
                        <div class="text-center">
                            <div class="avatar-ring inline-block">
                                <div class="rounded-full bg-slate-950 p-1">
                                    ${avatarHtml}
                                    <div class="avatar-main avatar-fallback text-2xl font-black text-white" style="${fallbackStyle}">${initials}</div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl md:text-3xl font-black text-white">${escapeHtml(data.identity?.name || 'Tanpa Nama')}</div>
                                <div class="text-sm text-slate-400 mt-1">${escapeHtml(data.role?.level_name || 'Tidak tersedia')}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="px-3 py-1.5 rounded-full ${toneClass(data.account?.status?.tone)} border text-xs font-black">
                                        ${escapeHtml(data.account?.status?.label || 'Tidak diketahui')}
                                    </span>
                                    ${data.account?.verified
                                        ? `<span class="px-3 py-1.5 rounded-full state-good border text-xs font-black"><i class="fa-solid fa-circle-check mr-1"></i> Terverifikasi</span>`
                                        : `<span class="px-3 py-1.5 rounded-full state-warning border text-xs font-black"><i class="fa-solid fa-clock mr-1"></i> Belum Terverifikasi</span>`}
                                </div>
                                <p class="text-sm text-slate-300 max-w-3xl leading-7">${escapeHtml(data.account?.status?.description || '')}</p>
                            </div>
                            <div class="px-4 py-3 rounded-2xl bg-slate-950/40 border border-slate-800">
                                <div class="text-[10px] uppercase tracking-widest text-slate-500">Ditemukan melalui</div>
                                <div class="text-sm font-bold text-cyan-200 mt-1">${escapeHtml(verification.matched_by || 'Identifier')}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
                            <div class="stat-box p-4">
                                <div class="text-[10px] uppercase tracking-widest text-slate-500">NIM</div>
                                <div class="mt-1 text-sm md:text-base font-bold text-white break-all">${escapeHtml(data.identity?.nim || '-')}</div>
                            </div>
                            <div class="stat-box p-4">
                                <div class="text-[10px] uppercase tracking-widest text-slate-500">Poin</div>
                                <div class="mt-1 text-lg font-black text-cyan-300">${escapeHtml(data.points?.total ?? 0)}</div>
                            </div>
                            <div class="stat-box p-4">
                                <div class="text-[10px] uppercase tracking-widest text-slate-500">Jabatan Tertinggi</div>
                                <div class="mt-1 text-sm font-bold text-white">${escapeHtml(data.role?.level_name || '-')}</div>
                            </div>
                            <div class="stat-box p-4">
                                <div class="text-[10px] uppercase tracking-widest text-slate-500">Profil</div>
                                <div class="mt-1 text-sm font-bold text-white">${escapeHtml(data.profile?.profile_completion ?? 0)}%</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mt-6">
                            <div class="stat-box p-5">
                                <div class="text-xs uppercase tracking-widest text-slate-500 mb-3">Informasi Organisasi</div>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between gap-4"><span class="text-slate-500">Member Number</span><span class="text-white font-semibold text-right">${escapeHtml(data.identity?.member_number || '-')}</span></div>
                                    <div class="flex justify-between gap-4"><span class="text-slate-500">Posisi Utama</span><span class="text-white font-semibold text-right">${escapeHtml(data.role?.primary_position || '-')}</span></div>
                                    <div class="flex justify-between gap-4"><span class="text-slate-500">Universitas</span><span class="text-white font-semibold text-right">${escapeHtml(data.education?.university || '-')}</span></div>
                                    <div class="flex justify-between gap-4"><span class="text-slate-500">Program Studi</span><span class="text-white font-semibold text-right">${escapeHtml(data.education?.major || '-')}</span></div>
                                </div>
                            </div>
                            <div class="stat-box p-5">
                                <div class="text-xs uppercase tracking-widest text-slate-500 mb-3">Jabatan Aktif</div>
                                <div class="flex flex-wrap gap-2">${positionsHtml}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                            <div class="stat-box p-5">
                                <div class="text-xs uppercase tracking-widest text-slate-500 mb-3">Portfolio</div>
                                ${portfolioHtml}
                            </div>
                            <div class="stat-box p-5">
                                <div class="text-xs uppercase tracking-widest text-slate-500 mb-3">Sosial Media</div>
                                <div class="flex flex-wrap gap-2">${socialHtml}</div>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-cyan-400/10 bg-cyan-400/5 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-brain text-cyan-300"></i>
                                <div class="text-sm font-extrabold text-white">Smart Account Insight</div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">${diagnosticsHtml}</div>
                        </div>

                        ${data.profile?.bio ? `
                            <div class="mt-5 rounded-2xl border border-slate-800 bg-slate-950/30 p-5">
                                <div class="text-xs uppercase tracking-widest text-slate-500 mb-2">Bio</div>
                                <p class="text-sm text-slate-300 leading-7">${escapeHtml(data.profile.bio)}</p>
                            </div>` : ''}
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-800/80 px-5 md:px-8 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div class="text-[11px] text-slate-500">Checked: ${escapeHtml(new Date(verification.checked_at || Date.now()).toLocaleString('id-ID'))}</div>
                <div class="text-[11px] text-slate-500">UNICODE Public Account Check</div>
            </div>
        `;

        const existing = [...state.results.values()].find(item => item.key === identityKey);
        if (existing) {
            existing.element.replaceWith(card);
            existing.element = card;
        } else {
            $('resultsContainer').querySelector('.empty-result')?.remove();
            $('resultsContainer').prepend(card);
            state.results.set(identityKey, { key: identityKey, element: card });
        }
    }

    function renderError(payload) {
        const error = payload?.error || {
            code: 'UNKNOWN_ERROR', title: 'Pemeriksaan gagal',
            message: 'Sistem gagal memproses data.', hints: [], method: 'unknown',
        };
        const card = document.createElement('article');
        card.className = 'result-card rounded-[2rem] overflow-hidden border border-red-400/15 bg-red-950/10';

        const hints = Array.isArray(error.hints)
            ? error.hints.map(item => `<li class="text-sm text-red-100/80">${escapeHtml(item)}</li>`).join('')
            : '';

        card.innerHTML = `
    <div class="p-5 md:p-7">
        <div class="flex flex-col md:flex-row gap-5">
            <div class="w-14 h-14 flex-shrink-0 rounded-2xl bg-red-500/10 border border-red-400/10 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-xl text-red-300"></i>
            </div>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <div class="text-xl font-black text-white">${escapeHtml(error.title)}</div>
                        <div class="text-sm text-red-100/80 mt-2 leading-6">${escapeHtml(error.message)}</div>
                    </div>
                    <div class="px-3 py-2 rounded-xl bg-red-500/10 border border-red-400/10 text-red-200 text-xs font-black whitespace-nowrap">${escapeHtml(error.code)}</div>
                </div>
                ${hints ? `
                    <div class="mt-5 rounded-2xl border border-red-400/10 bg-black/10 p-4">
                        <div class="text-xs uppercase tracking-widest text-red-300/70 mb-2">Solusi / Parameter</div>
                        <ul class="space-y-2 list-disc list-inside">${hints}</ul>
                    </div>` : ''}
                ${error.reference_id ? `
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-[11px] text-red-200/70">
                        <span class="uppercase tracking-widest">Reference:</span>
                        <code class="px-2 py-1 rounded bg-black/30 text-red-100 font-mono">${escapeHtml(error.reference_id)}</code>
                        <button type="button" onclick="navigator.clipboard?.writeText('${escapeHtml(error.reference_id)}')" class="underline hover:text-red-100">Salin</button>
                    </div>` : ''}
                <div class="mt-3 text-[11px] text-red-200/50">Method: ${escapeHtml(error.method || '-')}</div>
            </div>
        </div>
    </div>
`;
        $('resultsContainer').querySelector('.empty-result')?.remove();
        $('resultsContainer').prepend(card);
    }

    /* ================== BARCODE ================== */
    async function getBarcodeFormats() {
        if (state.barcodeFormats) return state.barcodeFormats;
        if (!('BarcodeDetector' in window)) throw new Error('BarcodeDetector tidak tersedia.');
        const supported = await BarcodeDetector.getSupportedFormats();
        const preferred = ['qr_code','code_128','code_39','code_93','ean_13','ean_8','upc_a','upc_e','itf','codabar','data_matrix','pdf417','aztec'];
        state.barcodeFormats = preferred.filter(f => supported.includes(f));
        if (!state.barcodeFormats.length) throw new Error('Tidak ada format barcode yang didukung.');
        return state.barcodeFormats;
    }

    async function startCamera() {
        try {
            if (!window.isSecureContext && !['localhost','127.0.0.1'].includes(location.hostname)) {
                renderError({ error: {
                    code:'INSECURE_CONTEXT', title:'Kamera membutuhkan HTTPS',
                    message:'Browser hanya mengizinkan kamera pada secure context.',
                    hints:['Gunakan HTTPS.', 'Untuk dev gunakan localhost.'], method:'barcode',
                }});
                return;
            }
            await getBarcodeFormats();
            await stopCamera();

            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false,
            });
            state.cameraStream = stream;
            const video = $('barcodeVideo');
            video.srcObject = stream;
            await video.play();
            $('cameraBox').classList.remove('hidden');
            showSystemStatus('Kamera aktif. Arahkan QR/barcode ke area kamera.', 'info');

            const detector = new BarcodeDetector({ formats: state.barcodeFormats });
            state.cameraTimer = setInterval(async () => {
                if (state.cameraBusy || !state.cameraStream || video.readyState < 2 || video.videoWidth < 1) return;
                state.cameraBusy = true;
                try {
                    const detected = await detector.detect(video);
                    if (Array.isArray(detected) && detected.length) {
                        const first = detected.find(c => c?.rawValue);
                        if (first?.rawValue) {
                            await stopCamera();
                            resolveIdentifier(first.rawValue, 'barcode');
                        }
                    }
                } catch (e) {} finally { state.cameraBusy = false; }
            }, 220);
        } catch (error) {
            renderError({ error: {
                code:'CAMERA_SCAN_FAILED', title:'Kamera tidak dapat digunakan',
                message: error?.message || 'Browser gagal mengaktifkan kamera.',
                hints:['Izinkan permission kamera.', 'Pastikan tidak ada aplikasi lain memakai kamera.', 'Gunakan HTTPS/localhost.', 'Gunakan input UID manual jika tidak didukung.'],
                method:'barcode',
            }});
        }
    }

    async function stopCamera() {
        if (state.cameraTimer) { clearInterval(state.cameraTimer); state.cameraTimer = null; }
        if (state.cameraStream) {
            state.cameraStream.getTracks().forEach(t => t.stop());
            state.cameraStream = null;
        }
        const v = $('barcodeVideo');
        if (v) { v.pause(); v.srcObject = null; }
        $('cameraBox')?.classList.add('hidden');
        state.cameraBusy = false;
    }

    async function decodeUploadedImage(file) {
        if (!file) return;
        try {
            if (!file.type.startsWith('image/')) throw new Error('File harus berupa gambar.');
            const formats = await getBarcodeFormats();
            const detector = new BarcodeDetector({ formats });
            showSystemStatus('Membaca QR/barcode dari gambar...', 'info');
            const detected = await detector.detect(file);
            if (!Array.isArray(detected) || !detected.length) {
                renderError({ error: {
                    code:'IMAGE_CODE_NOT_FOUND', title:'Barcode tidak ditemukan',
                    message:'Sistem membuka gambar tapi tidak menemukan QR/barcode.',
                    hints:['Gunakan gambar yang lebih tajam.', 'QR harus terlihat penuh.', 'Hindari gambar terlalu gelap.', 'Coba scan via kamera.'],
                    method:'upload',
                }});
                return;
            }
            const code = detected.find(item => item?.rawValue);
            if (!code?.rawValue) throw new Error('Barcode ditemukan tetapi nilainya kosong.');
            await resolveIdentifier(code.rawValue, 'upload');
        } catch (error) {
            renderError({ error: {
                code:'IMAGE_SCAN_FAILED', title:'Gambar gagal diproses',
                message: error?.message || 'Browser gagal membaca gambar.',
                hints:['Gunakan PNG/JPG/WebP yang jelas.', 'Pastikan browser mendukung BarcodeDetector.', 'Gunakan kamera atau UID manual.'],
                method:'upload',
            }});
        }
    }

    /* ================== NFC ================== */
    function decodeTextRecord(record) {
        try {
            if (!record?.data) return '';
            const data = record.data;
            const bytes = data instanceof DataView
                ? new Uint8Array(data.buffer, data.byteOffset, data.byteLength)
                : new Uint8Array(data);
            if (!bytes.length) return '';
            if (record.recordType === 'text') {
                const statusByte = bytes[0];
                const langLength = statusByte & 0x3f;
                return new TextDecoder().decode(bytes.slice(1 + langLength));
            }
            return new TextDecoder().decode(bytes);
        } catch { return ''; }
    }

    async function startNfc() {
        try {
            if (!window.isSecureContext && !['localhost','127.0.0.1'].includes(location.hostname)) {
                renderError({ error: {
                    code:'NFC_REQUIRES_HTTPS', title:'NFC membutuhkan HTTPS',
                    message:'Web NFC hanya dapat digunakan pada secure context.',
                    hints:['Buka via HTTPS.', 'Pastikan browser & perangkat mendukung.', 'Gunakan input UID manual bila tidak didukung.'],
                    method:'nfc',
                }});
                return;
            }
            if (!('NDEFReader' in window)) {
                renderError({ error: {
                    code:'NFC_BROWSER_UNSUPPORTED', title:'Web NFC tidak didukung browser',
                    message:'Browser ini tidak menyediakan NDEFReader.',
                    hints:['Gunakan browser/perangkat dengan Web NFC.', 'Aktifkan fitur NFC.', 'Gunakan input UID manual.'],
                    method:'nfc',
                }});
                return;
            }

            const reader = new NDEFReader();
            const controller = new AbortController();
            state.nfcController = controller;

            $('nfcStatus').innerHTML = `
                <div class="flex items-center gap-2 text-cyan-200">
                    <span class="status-dot bg-cyan-300 animate-pulse"></span>
                    <span>NFC aktif. Dekatkan kartu/tag ke area NFC ponsel.</span>
                </div>`;

            reader.addEventListener('readingerror', () => {
                $('nfcStatus').innerHTML = `
                    <div class="flex items-center gap-2 text-amber-200">
                        <span class="status-dot bg-amber-300"></span>
                        <span>Kartu/tag terdeteksi tapi data tidak bisa dibaca.</span>
                    </div>`;
            });

            reader.addEventListener('reading', async (event) => {
                try { controller.abort(); } catch {}
                let identifier = '';
                if (event.serialNumber) identifier = String(event.serialNumber).trim();
                if (!identifier && event.message?.records) {
                    for (const record of event.message.records) {
                        const decoded = decodeTextRecord(record);
                        if (decoded?.trim()) { identifier = decoded.trim(); break; }
                    }
                }
                if (!identifier) {
                    renderError({ error: {
                        code:'NFC_IDENTIFIER_EMPTY', title:'NFC terbaca tetapi identifier kosong',
                        message:'Tag NFC merespons namun tidak memberikan identifier yang dapat diperiksa.',
                        hints:['Pastikan kartu sudah didaftarkan.', 'Tap kartu kembali.', 'Masukkan UID manual.'],
                        method:'nfc',
                    }});
                    return;
                }
                $('nfcStatus').innerHTML = `
                    <div class="flex items-center gap-2 text-emerald-200">
                        <span class="status-dot bg-emerald-300"></span>
                        <span>NFC berhasil dibaca. Memeriksa akun...</span>
                    </div>`;
                await resolveIdentifier(identifier, 'nfc');
            }, { once: true });

            await reader.scan({ signal: controller.signal });
            showSystemStatus('Scanner NFC siap. Dekatkan kartu NFC.', 'info');
        } catch (error) {
            if (error?.name === 'AbortError') return;
            renderError({ error: {
                code:'NFC_SCAN_FAILED', title:'NFC gagal dimulai',
                message: error?.message || 'Browser gagal mengaktifkan scanner NFC.',
                hints:['Pastikan NFC aktif.', 'Izinkan permission NFC.', 'Gunakan HTTPS.', 'Coba input manual.'],
                method:'nfc',
            }});
            $('nfcStatus').innerHTML = `
                <div class="flex items-center gap-2 text-red-200">
                    <span class="status-dot bg-red-300"></span>
                    <span>Scanner NFC gagal dimulai.</span>
                </div>`;
        }
    }

    function clearResults() {
        state.results.clear();
        $('resultsContainer').innerHTML = `
            <div class="glass-panel rounded-3xl empty-result p-8">
                <div class="text-center max-w-lg">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-800/70 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-id-card text-2xl text-slate-500"></i>
                    </div>
                    <h3 class="font-bold text-white mb-2">Belum ada pemeriksaan</h3>
                    <p class="text-sm text-slate-500 leading-6">Isi form di atas atau lakukan scan untuk memulai.</p>
                </div>
            </div>`;
        hideSystemStatus();
    }

    /* Public API */
    return {
        resolve: resolveIdentifier,
        startCamera,
        stopCamera,
        startNfc,
        decodeUploadedImage,
        clearResults,
        showStatus: showSystemStatus,
        hideStatus: hideSystemStatus,
        get state() { return state; },
        get $() { return $; },
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('clearResultsBtn')?.addEventListener('click', () => window.AccountCheck.clearResults());
    window.addEventListener('beforeunload', () => window.AccountCheck.stopCamera());
});
</script>