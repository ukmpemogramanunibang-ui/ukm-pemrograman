{{-- resources/views/interaksi-ppt.blade.php --}}

@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-midnight via-deepblue to-midnight text-white">

    {{-- STEPPER HEADER --}}
    <section class="border-b border-white/10 bg-black/30 backdrop-blur-md sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-cyan to-electric flex items-center justify-center">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-lg md:text-xl">PPT Interaktif</h1>
                    <p class="text-xs text-gray-400">Presentasi dengan Gestur & AI</p>
                </div>
            </div>

            <div class="flex items-center gap-1 md:gap-3 text-[11px] md:text-xs font-bold">
                <div class="stepper-item flex items-center gap-2" data-step="1">
                    <span class="w-7 h-7 rounded-full bg-cyan text-midnight flex items-center justify-center">1</span>
                    <span class="hidden md:inline">Upload File</span>
                </div>
                <div class="w-6 h-0.5 bg-white/20"></div>
                <div class="stepper-item flex items-center gap-2 opacity-50" data-step="2">
                    <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">2</span>
                    <span class="hidden md:inline">Deteksi Wajah</span>
                </div>
                <div class="w-6 h-0.5 bg-white/20"></div>
                <div class="stepper-item flex items-center gap-2 opacity-50" data-step="3">
                    <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">3</span>
                    <span class="hidden md:inline">Rekam Gestur</span>
                </div>
                <div class="w-6 h-0.5 bg-white/20"></div>
                <div class="stepper-item flex items-center gap-2 opacity-50" data-step="4">
                    <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">4</span>
                    <span class="hidden md:inline">Presentasi</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-4 py-8">

        {{-- ==================== TAHAP 1 — UPLOAD FILE ==================== --}}
        <div id="panel-step1" class="step-panel">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/30 text-cyan-200 text-xs font-bold mb-4">
                        <i class="fas fa-1"></i> Tahap 1 dari 4
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold">
                        Upload File <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan to-violet">Presentasi</span>
                    </h2>
                    <p class="text-gray-400 mt-3 text-sm md:text-base">
                        Maksimal <strong>3 file</strong>, masing-masing <strong>12MB</strong>. Format: PPT, PPTX, PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.
                    </p>
                </div>

                <div id="dropzone"
                     class="border-2 border-dashed border-cyan/40 rounded-3xl p-8 md:p-12 text-center hover:border-cyan hover:bg-cyan/5 transition cursor-pointer bg-white/5">
                    <input type="file" id="fileInput" class="hidden" multiple
                           accept=".ppt,.pptx,.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <i class="fas fa-cloud-arrow-up text-6xl text-cyan/70 mb-4"></i>
                    <p class="font-bold text-lg">Seret & lepas file di sini</p>
                    <p class="text-xs text-gray-400 my-3">atau</p>
                    <button type="button" id="browseBtn"
                            class="px-6 py-3 rounded-2xl bg-gradient-to-r from-cyan to-electric text-midnight font-extrabold shadow-lg hover:shadow-cyan/50 transition">
                        <i class="fas fa-folder-open mr-2"></i> Pilih File
                    </button>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-bold text-sm">File yang diupload</p>
                        <p class="text-xs text-gray-400"><span id="fileCountLabel">0</span> / 3 file</p>
                    </div>
                    <div id="fileList" class="space-y-3">
                        <p class="text-xs text-gray-500 italic">Belum ada file yang diupload.</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button id="toStep2Btn" disabled
                            class="px-8 py-3 rounded-2xl bg-gradient-to-r from-cyan to-electric text-midnight font-extrabold shadow-lg hover:shadow-cyan/50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Lanjut ke Deteksi Wajah <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ==================== TAHAP 2 — DETEKSI WAJAH (REWRITE) ==================== --}}
        <div id="panel-step2" class="step-panel hidden">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-violet/10 border border-violet/30 text-violet-200 text-xs font-bold mb-4">
                        <i class="fas fa-2"></i> Tahap 2 dari 4
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold">
                        Deteksi <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet to-cyan">Wajah & Identitas</span>
                    </h2>
                    <p class="text-gray-400 mt-3 text-sm md:text-base">
                        Arahkan wajah ke kamera. Sistem akan memverifikasi identitas Anda secara otomatis sebelum lanjut.
                    </p>
                </div>

                {{-- MODEL LOADING BAR --}}
                <div id="modelLoadingBar" class="hidden mb-4 bg-black/40 border border-violet/30 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-2 text-xs">
                        <span class="font-bold text-violet-200"><i class="fas fa-brain mr-1"></i> Memuat model AI...</span>
                        <span id="modelLoadingPct" class="text-cyan-300 font-bold">0%</span>
                    </div>
                    <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                        <div id="modelLoadingFill" class="h-full bg-gradient-to-r from-violet to-cyan w-0 transition-all duration-300"></div>
                    </div>
                    <p id="modelLoadingMsg" class="text-[11px] text-gray-400 mt-2">Menyiapkan detektor wajah...</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    {{-- Camera --}}
                    <div class="md:col-span-2">
                        <div class="relative rounded-3xl overflow-hidden border border-white/10 bg-black aspect-video">
                            <video id="faceVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                            <canvas id="faceOverlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                            {{-- Bracket corners --}}
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                                <div class="relative w-56 h-56 md:w-72 md:h-72">
                                    <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-cyan/70 rounded-tl-2xl"></span>
                                    <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-cyan/70 rounded-tr-2xl"></span>
                                    <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-cyan/70 rounded-bl-2xl"></span>
                                    <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-cyan/70 rounded-br-2xl"></span>
                                </div>
                            </div>

                            {{-- Placeholder --}}
                            <div id="faceCameraPlaceholder"
                                 class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 text-center p-4">
                                <i class="fas fa-video-slash text-5xl text-gray-500 mb-3"></i>
                                <p id="facePlaceholderMsg" class="text-sm text-gray-400 mb-4">Kamera belum aktif</p>
                                <button id="startFaceCamBtn"
                                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-cyan to-electric text-midnight font-extrabold shadow-lg">
                                    <i class="fas fa-camera mr-2"></i> Aktifkan Kamera
                                </button>
                            </div>

                            {{-- Status badge --}}
                            <div id="faceStatusBadge"
                                 class="hidden absolute top-4 left-4 px-3 py-1.5 rounded-full bg-amber-500/90 text-white text-xs font-bold">
                                <i class="fas fa-circle text-[8px] mr-1 animate-pulse"></i> <span id="faceStatusBadgeTxt">Menunggu...</span>
                            </div>

                            {{-- Face count --}}
                            <div id="faceCountBadge"
                                 class="hidden absolute top-4 right-4 px-3 py-1.5 rounded-full bg-black/70 text-white text-xs font-bold border border-white/20">
                                <i class="fas fa-user mr-1"></i> <span id="faceCountTxt">0</span> wajah
                            </div>

                            {{-- Countdown verification --}}
                            <div id="verifyCountdown" class="hidden absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <div class="text-8xl font-extrabold text-cyan drop-shadow-[0_0_20px_rgba(0,217,255,0.9)]" id="verifyCountdownNum">3</div>
                                    <p class="text-white font-bold mt-2">Tahan posisi...</p>
                                </div>
                            </div>
                        </div>

                        {{-- Mini instruction --}}
                        <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-violet/10 to-cyan/10 border border-violet/20 text-xs text-gray-300">
                            <p class="font-bold text-violet-200 mb-1"><i class="fas fa-circle-info mr-1"></i> Tips Deteksi Optimal:</p>
                            <ul class="list-disc ml-4 space-y-1">
                                <li>Pastikan pencahayaan cukup, wajah menghadap kamera.</li>
                                <li>Jarak ideal 30–60 cm dari kamera.</li>
                                <li>Hindari terlalu banyak gerakan saat verifikasi.</li>
                                <li>Sistem butuh <strong>wajah stabil</strong> sebelum bisa diverifikasi.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Identity / Verification Panel --}}
                    <div class="bg-white/5 border border-white/10 rounded-3xl p-5 backdrop-blur-xl flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold flex items-center gap-2">
                                <i class="fas fa-id-card text-cyan"></i> Identitas
                            </h3>
                            <span id="verifyStatusPill"
                                  class="px-2.5 py-1 rounded-full bg-gray-500/20 text-gray-300 text-[10px] font-bold">
                                Menunggu
                            </span>
                        </div>

                        {{-- Snapshot --}}
                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden border border-white/10 bg-black/60 mb-3">
                            <canvas id="faceSnapshot" class="w-full h-full object-cover" width="300" height="300"></canvas>
                            <div id="snapshotEmpty" class="absolute inset-0 flex items-center justify-center text-center p-3">
                                <div>
                                    <i class="fas fa-user-circle text-5xl text-gray-600 mb-2"></i>
                                    <p class="text-[11px] text-gray-500">Snapshot wajah akan muncul setelah verifikasi</p>
                                </div>
                            </div>
                        </div>

                        {{-- Live / Verified data --}}
                        <div class="space-y-2 flex-1">
                            <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                                <p class="text-[11px] text-gray-400">Nama</p>
                                <input id="faceNameOut" type="text"
                                       class="w-full bg-transparent font-bold text-cyan-300 text-sm outline-none border-b border-transparent focus:border-cyan/50 transition"
                                       value="Menunggu...">
                            </div>
                            <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                                <p class="text-[11px] text-gray-400">Usia</p>
                                <div class="flex items-center gap-2">
                                    <input id="faceAgeOut" type="number" min="5" max="100"
                                           class="w-16 bg-transparent font-bold text-violet-300 text-sm outline-none border-b border-transparent focus:border-violet/50 transition"
                                           value="">
                                    <span class="text-violet-300 font-bold text-sm">Tahun</span>
                                    <span id="ageConfidence" class="text-[10px] text-gray-500 ml-auto"></span>
                                </div>
                            </div>
                            <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                                <p class="text-[11px] text-gray-400">Emosi</p>
                                <p id="faceEmotionOut" class="font-bold text-emerald-300 text-sm">-</p>
                            </div>
                            <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                                <p class="text-[11px] text-gray-400">Jenis Kelamin</p>
                                <p id="faceGenderOut" class="font-bold text-pink-300 text-sm">-</p>
                            </div>
                            <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                                <p class="text-[11px] text-gray-400">Kualitas Wajah</p>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-white/10 rounded-full overflow-hidden">
                                        <div id="faceQualityFill" class="h-full bg-gradient-to-r from-rose-500 via-amber-400 to-emerald-400 w-0 transition-all"></div>
                                    </div>
                                    <span id="faceQualityPct" class="text-[11px] font-bold text-cyan-300">0%</span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 space-y-2">
                            <button id="verifyFaceBtn" disabled
                                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-violet to-cyan text-midnight font-bold text-sm disabled:opacity-40 disabled:cursor-not-allowed transition">
                                <i class="fas fa-shield-halved mr-2"></i> <span id="verifyFaceBtnTxt">Ambil & Verifikasi</span>
                            </button>
                            <button id="retakeFaceBtn" disabled
                                    class="w-full py-2 rounded-xl bg-white/10 hover:bg-white/20 font-bold text-xs disabled:opacity-40 transition">
                                <i class="fas fa-rotate-left mr-1"></i> Ulangi Deteksi
                            </button>
                        </div>

                        <div id="faceReadyAlert"
                             class="hidden mt-3 p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 text-xs">
                            <i class="fas fa-check-circle mr-1"></i> Identitas terverifikasi. Silakan lanjut.
                        </div>
                        <div id="faceErrorAlert"
                             class="hidden mt-3 p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs">
                            <i class="fas fa-triangle-exclamation mr-1"></i> <span id="faceErrorTxt">Wajah tidak terdeteksi.</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button onclick="Wizard.goTo(1)"
                            class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 font-bold transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </button>
                    <button id="toStep3Btn" disabled
                            class="px-8 py-3 rounded-2xl bg-gradient-to-r from-cyan to-electric text-midnight font-extrabold shadow-lg hover:shadow-cyan/50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Lanjut ke Rekam Gestur <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ==================== TAHAP 3 — REKAM GESTUR ==================== --}}
        <div id="panel-step3" class="step-panel hidden">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald/10 border border-emerald/30 text-emerald-200 text-xs font-bold mb-4">
                        <i class="fas fa-3"></i> Tahap 3 dari 4
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold">
                        Rekam <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald to-cyan">Gestur Anda</span>
                    </h2>
                    <p class="text-gray-400 mt-3 text-sm md:text-base">
                        Lakukan gestur di depan kamera selama <strong>2 detik</strong>. Maksimal <strong>4 gestur</strong>.
                    </p>
                </div>

                <div class="grid lg:grid-cols-2 gap-6">
                    <div>
                        <div class="relative rounded-3xl overflow-hidden border border-white/10 bg-black aspect-video">
                            <video id="gestureVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                            <canvas id="gestureOverlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                            <div id="gestureHintBox"
                                 class="absolute bottom-4 left-4 right-4 p-3 rounded-2xl bg-black/70 backdrop-blur border border-white/10 text-center text-xs">
                                <p class="text-gray-300">Lakukan gestur (Open Palm, Fist, Victory, Pinch...)</p>
                                <p class="text-emerald-300 font-bold mt-1" id="gestureLiveLabel">Menunggu gestur...</p>
                                <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden mt-2">
                                    <div id="gestureHoldBar" class="h-full bg-gradient-to-r from-cyan to-emerald w-0 transition-all"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-cyan/10 to-violet/10 border border-cyan/20 text-xs text-gray-300">
                            <p class="font-bold text-cyan-200 mb-1"><i class="fas fa-circle-info mr-1"></i> Cara Pakai:</p>
                            <ol class="list-decimal ml-4 space-y-1">
                                <li>Lakukan gestur di depan kamera.</li>
                                <li>Tahan 2 detik sampai progress bar penuh.</li>
                                <li>Isi nama gestur & pilih fungsinya.</li>
                                <li>Simpan, ulangi untuk gestur lain (maks 4).</li>
                            </ol>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold flex items-center gap-2">
                                <i class="fas fa-list-check text-cyan"></i> Gestur Tersimpan
                            </h3>
                            <span class="text-xs px-3 py-1 rounded-full bg-cyan/20 text-cyan-200 font-bold">
                                <span id="gestureCount">0</span> / 4
                            </span>
                        </div>

                        <div id="gestureList" class="space-y-3">
                            <p class="text-xs text-gray-500 italic">Belum ada gestur tersimpan.</p>
                        </div>

                        <div id="gestureForm" class="hidden mt-4 p-4 rounded-2xl bg-gradient-to-br from-cyan/10 to-violet/10 border border-cyan/30">
                            <p class="text-xs text-cyan-200 font-bold mb-3">
                                <i class="fas fa-check-circle mr-1"></i> Gestur terdeteksi! Simpan sekarang.
                            </p>

                            <label class="text-xs text-gray-300">Nama Gestur</label>
                            <input type="text" id="gestureNameInput" placeholder="Contoh: Tangan Kanan"
                                   class="w-full mt-1 mb-3 bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">

                            <label class="text-xs text-gray-300">Kategori Terdeteksi</label>
                            <input type="text" id="gestureCategoryInput" readonly
                                   class="w-full mt-1 mb-3 bg-black/60 border border-white/10 rounded-xl px-3 py-2 text-sm text-cyan-300">

                            <label class="text-xs text-gray-300">Fungsi Gestur</label>
                            <select id="gestureFunctionInput"
                                    class="w-full mt-1 mb-3 bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
                                <option value="next">➡️ Slide Selanjutnya</option>
                                <option value="prev">⬅️ Slide Sebelumnya</option>
                                <option value="close">❌ Close Presentasi</option>
                                <option value="zoomIn">➕ Zoom In</option>
                                <option value="zoomOut">➖ Zoom Out</option>
                                <option value="fullscreen">🖥️ Mode Full PPT</option>
                            </select>

                            <div class="flex gap-2">
                                <button id="saveGestureBtn"
                                        class="flex-1 py-2 rounded-xl bg-gradient-to-r from-cyan to-electric text-midnight font-bold text-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                                <button id="cancelGestureBtn"
                                        class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button onclick="Wizard.goTo(2)"
                            class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 font-bold transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </button>
                    <button id="generateBtn" disabled
                            class="px-8 py-3 rounded-2xl bg-gradient-to-r from-emerald to-cyan text-midnight font-extrabold shadow-lg hover:shadow-emerald/50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fas fa-bolt mr-2"></i> Generate Presentasi
                    </button>
                </div>
            </div>
        </div>

       {{-- ==================== FINAL — 2 CARD LAYOUT ==================== --}}
<div id="panel-final" class="step-panel hidden">
    <div class="w-full">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald/10 border border-emerald/30 text-emerald-200 text-xs font-bold mb-2">
                    <i class="fas fa-check-circle"></i> Presentasi Siap
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold">
                    Lembar <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald to-cyan">Presentasi Anda</span>
                </h2>
            </div>
            <button onclick="Wizard.goTo(3)"
                    class="px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-sm font-bold transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali Atur Gestur
            </button>
        </div>

        {{-- Order Bar --}}
        <div class="bg-white/5 border border-white/10 rounded-3xl p-5 backdrop-blur-xl mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="font-bold text-sm mb-1"><i class="fas fa-sort text-cyan mr-1"></i> Urutan Tampil File</p>
                    <p class="text-xs text-gray-400">Pilih urutan file yang akan ditampilkan lebih dulu.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap" id="orderBtnWrap">
                    <button data-order="123" class="orderPresetBtn px-3 py-2 rounded-xl bg-white/10 hover:bg-cyan hover:text-midnight text-xs font-bold transition">1 - 2 - 3</button>
                    <button data-order="321" class="orderPresetBtn px-3 py-2 rounded-xl bg-white/10 hover:bg-cyan hover:text-midnight text-xs font-bold transition">3 - 2 - 1</button>
                    <button data-order="213" class="orderPresetBtn px-3 py-2 rounded-xl bg-white/10 hover:bg-cyan hover:text-midnight text-xs font-bold transition">2 - 1 - 3</button>
                    <button data-order="231" class="orderPresetBtn px-3 py-2 rounded-xl bg-white/10 hover:bg-cyan hover:text-midnight text-xs font-bold transition">2 - 3 - 1</button>
                    <button data-order="312" class="orderPresetBtn px-3 py-2 rounded-xl bg-white/10 hover:bg-cyan hover:text-midnight text-xs font-bold transition">3 - 1 - 2</button>
                    <button data-order="132" class="orderPresetBtn px-3 py-2 rounded-xl bg-white/10 hover:bg-cyan hover:text-midnight text-xs font-bold transition">1 - 3 - 2</button>
                </div>
            </div>
        </div>

        {{-- TWO-CARD GRID --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- KIRI: FILE PREVIEW CARD --}}
            <div class="xl:col-span-2 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-xl overflow-hidden">
                <div class="p-4 md:p-5 border-b border-white/10 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan to-electric flex items-center justify-center text-midnight font-extrabold shrink-0" id="filePosBadge">
                            #1
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold truncate" id="fileTitleTxt">-</p>
                            <p class="text-xs text-gray-400" id="fileMetaTxt">-</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="prevFileBtn" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs">
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        <button id="nextFileBtn" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs">
                            Berikutnya <i class="fas fa-chevron-right"></i>
                        </button>
                        <button id="openFsBtn" class="px-3 py-2 rounded-xl bg-gradient-to-r from-cyan to-electric text-midnight text-xs font-bold">
                            <i class="fas fa-expand"></i> Fullscreen
                        </button>
                    </div>
                </div>

                <div id="filePreviewWrap" class="relative bg-black/60 min-h-[560px] flex items-center justify-center">
                    {{-- Preview content rendered by JS --}}
                    <div class="text-center text-gray-500 italic p-8">
                        <i class="fas fa-file-lines text-6xl text-cyan/30 mb-3"></i>
                        <p>Preview akan muncul di sini</p>
                    </div>
                </div>

                {{-- Navigasi bawah --}}
                <div class="p-4 border-t border-white/10 flex items-center justify-between text-xs text-gray-400">
                    <span>File <strong id="filePosLabel" class="text-cyan-300">1</strong> dari <strong id="fileTotalLabel" class="text-cyan-300">0</strong></span>
                    <span id="fileZoomLabel">Zoom: 100%</span>
                </div>
            </div>

            {{-- KANAN: CAMERA CARD (Identitas + Gestur) --}}
            <div class="bg-white/5 border border-white/10 rounded-3xl backdrop-blur-xl overflow-hidden flex flex-col">
                <div class="p-4 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-emerald to-cyan flex items-center justify-center">
                            <i class="fas fa-camera text-midnight text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm">Kamera & Kontrol</p>
                            <p class="text-[10px] text-gray-400">Identitas + Gestur</p>
                        </div>
                    </div>
                    <span id="camStatusPill" class="px-2.5 py-1 rounded-full bg-gray-500/20 text-gray-300 text-[10px] font-bold">
                        Offline
                    </span>
                </div>

                {{-- Camera feed --}}
                <div class="relative bg-black aspect-video">
                    <video id="finalVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                    <canvas id="finalOverlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                    {{-- Corner brackets --}}
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="relative w-32 h-32">
                            <span class="absolute top-0 left-0 w-5 h-5 border-t-2 border-l-2 border-cyan/70"></span>
                            <span class="absolute top-0 right-0 w-5 h-5 border-t-2 border-r-2 border-cyan/70"></span>
                            <span class="absolute bottom-0 left-0 w-5 h-5 border-b-2 border-l-2 border-cyan/70"></span>
                            <span class="absolute bottom-0 right-0 w-5 h-5 border-b-2 border-r-2 border-cyan/70"></span>
                        </div>
                    </div>

                    {{-- Gesture live label --}}
                    <div id="finalGestureHint"
                         class="absolute bottom-2 left-2 right-2 px-3 py-2 rounded-xl bg-black/70 backdrop-blur border border-white/10 text-[10px]">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Gestur:</span>
                            <span id="finalGestureLabel" class="text-cyan-300 font-bold">-</span>
                        </div>
                        <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden mt-1">
                            <div id="finalGestureBar" class="h-full bg-gradient-to-r from-cyan to-emerald w-0 transition-all"></div>
                        </div>
                    </div>
                </div>

                {{-- Identity info --}}
                <div class="p-4 space-y-2 flex-1">
                    <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                        <p class="text-[10px] text-gray-400">Nama</p>
                        <p id="finalNameTxt" class="font-bold text-cyan-300 text-sm truncate">-</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                            <p class="text-[10px] text-gray-400">Usia</p>
                            <p id="finalAgeTxt" class="font-bold text-violet-300 text-sm">-</p>
                        </div>
                        <div class="bg-black/30 rounded-2xl p-3 border border-white/10">
                            <p class="text-[10px] text-gray-400">Emosi</p>
                            <p id="finalEmotionTxt" class="font-bold text-emerald-300 text-xs truncate">-</p>
                        </div>
                    </div>
                </div>

                {{-- Gesture shortcuts --}}
                <div class="p-4 border-t border-white/10">
                    <p class="text-[10px] text-gray-400 mb-2">Gestur Aktif:</p>
                    <div id="finalGestureChips" class="flex flex-wrap gap-1.5">
                        <span class="text-[10px] text-gray-500 italic">Belum ada</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ACTION LOG --}}
        <div class="mt-6 bg-white/5 border border-white/10 rounded-3xl p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold"><i class="fas fa-bolt text-yellow-300 mr-2"></i> Log Aksi Gestur</h3>
                <button id="clearLogBtn" class="text-xs px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition">Bersihkan</button>
            </div>
            <div id="actionLog" class="text-xs text-gray-400 italic">Belum ada aksi.</div>
        </div>

    </div>
</div>

    </section>
</div>

{{-- FULLSCREEN OVERLAY --}}
<div id="fullscreenOverlay" class="hidden fixed inset-0 z-[100] bg-black/95 backdrop-blur-xl">
    <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
        <button id="closeFullscreenBtn" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-bold">
            <i class="fas fa-times mr-1"></i> Tutup
        </button>
    </div>
    <div id="fullscreenContent" class="w-full h-full flex items-center justify-center p-4"></div>
</div>

<script>
    window.APP_USER_NAME = @json(Auth::user()->name ?? 'Pengguna');
    window.APP_USER_EMAIL = @json(Auth::user()->email ?? '');
    window.APP_USER_ROLE = @json(Auth::user()?->roles?->pluck('name')->implode(', ') ?? 'Guest');
</script>

@verbatim
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script type="module">
    import { GestureRecognizer, FilesetResolver } from "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3";

    /* ============================================================
       GLOBAL STATE
    ============================================================ */
    const State = {
        step: 1,
        files: [],
        gestures: [],
        pendingGesture: null,
        order: [0, 1, 2],
        stream: null,
        faceApiReady: false,
        gestureRecognizer: null,
        lastVideoTime: -1,
        holdStart: 0,
        holdCandidate: null,
        GESTURE_HOLD_MS: 2000,
        // FACE
        face: {
            verified: false,
            name: window.APP_USER_NAME || 'Pengguna',
            age: null,
            emotion: null,
            gender: null,
            quality: 0,
            snapshot: null,
            stability: 0,
            STABILITY_REQUIRED: 12,
            lastData: null
        }
    };

    const FN_LABELS = {
        next: 'Slide Selanjutnya',
        prev: 'Slide Sebelumnya',
        close: 'Close Presentasi',
        zoomIn: 'Zoom In',
        zoomOut: 'Zoom Out',
        fullscreen: 'Mode Full PPT'
    };

    const EMO_MAP = {
        happy: { label: 'Bahagia 😊', emoji: '😊' },
        sad: { label: 'Sedih 😢', emoji: '😢' },
        angry: { label: 'Marah 😠', emoji: '😠' },
        fearful: { label: 'Takut 😨', emoji: '😨' },
        disgusted: { label: 'Jijik 🤢', emoji: '🤢' },
        surprised: { label: 'Terkejut 😲', emoji: '😲' },
        neutral: { label: 'Netral 😐', emoji: '😐' }
    };

    const $ = (id) => document.getElementById(id);

    /* ============================================================
       PERSISTENCE
    ============================================================ */
    function saveSession() {
        try {
            sessionStorage.setItem('ppt_face', JSON.stringify({
                verified: State.face.verified,
                name: State.face.name,
                age: State.face.age,
                emotion: State.face.emotion,
                gender: State.face.gender,
                quality: State.face.quality
            }));
        } catch (e) {}
    }

    function loadSession() {
        try {
            const raw = sessionStorage.getItem('ppt_face');
            if (!raw) return;
            const data = JSON.parse(raw);
            if (data && data.verified) {
                Object.assign(State.face, data);
                renderFaceVerifiedUI();
            }
        } catch (e) {}
    }

    /* ============================================================
       WIZARD
    ============================================================ */
    window.Wizard = {
        goTo(step) {
            State.step = step;
            document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
            $('panel-' + (step === 4 ? 'final' : 'step' + step)).classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            updateStepper();

            if (step === 2) startFaceCamera();
            if (step === 3) startGestureCamera();
            if (step === 4) startFinalCamera();

            if (step !== 2) stopFaceLoop();
            if (step !== 3) stopGestureLoop();
            if (step !== 4) stopFinalLoop();
        }
    };

    function updateStepper() {
        document.querySelectorAll('.stepper-item').forEach(el => {
            const s = parseInt(el.dataset.step);
            const circle = el.querySelector('span:first-child');
            el.classList.toggle('opacity-50', s > State.step);
            if (s < State.step) {
                circle.className = 'w-7 h-7 rounded-full bg-emerald text-midnight flex items-center justify-center';
                circle.innerHTML = '<i class="fas fa-check text-[10px]"></i>';
            } else if (s === State.step) {
                circle.className = 'w-7 h-7 rounded-full bg-cyan text-midnight flex items-center justify-center';
                circle.textContent = s;
            } else {
                circle.className = 'w-7 h-7 rounded-full bg-white/20 flex items-center justify-center';
                circle.textContent = s;
            }
        });
    }

    /* ============================================================
       STEP 1 — FILE UPLOAD
    ============================================================ */
    const dropzone = $('dropzone');
    const fileInput = $('fileInput');

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
    const getExt = (name) => name.split('.').pop().toLowerCase();
    const ALLOWED = ['ppt','pptx','pdf','doc','docx','xls','xlsx','jpg','jpeg','png'];

    function handleFiles(input) {
        const files = Array.from(input);
        for (const f of files) {
            if (State.files.length >= 3) { alert('Maksimal 3 file.'); break; }
            if (f.size > 12 * 1048576) { alert(`${f.name} melebihi 12MB.`); continue; }
            const ext = getExt(f.name);
            if (!ALLOWED.includes(ext)) { alert(`Format .${ext} tidak didukung.`); continue; }
            State.files.push({ file: f, url: URL.createObjectURL(f), ext, name: f.name, size: f.size });
        }
        renderFileList();
    }

    function renderFileList() {
        const list = $('fileList');
        $('fileCountLabel').textContent = State.files.length;

        if (!State.files.length) {
            list.innerHTML = '<p class="text-xs text-gray-500 italic">Belum ada file yang diupload.</p>';
            $('toStep2Btn').disabled = true;
            return;
        }

        list.innerHTML = State.files.map((f, i) => `
            <div class="flex items-center gap-3 bg-black/30 rounded-2xl p-3 border border-white/10">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan to-electric flex items-center justify-center text-midnight font-bold text-xs shrink-0">
                    ${f.ext.toUpperCase()}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">${f.name}</p>
                    <p class="text-[11px] text-gray-400">${formatSize(f.size)} • Urutan #${i + 1}</p>
                </div>
                <button data-i="${i}" class="rmFileBtn text-rose-400 hover:text-rose-200 transition p-2">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('');

        document.querySelectorAll('.rmFileBtn').forEach(b => {
            b.onclick = (e) => {
                const i = +e.currentTarget.dataset.i;
                URL.revokeObjectURL(State.files[i].url);
                State.files.splice(i, 1);
                renderFileList();
            };
        });

        $('toStep2Btn').disabled = State.files.length === 0;
    }

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-cyan', 'bg-cyan/10');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-cyan', 'bg-cyan/10'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-cyan', 'bg-cyan/10');
        handleFiles(e.dataTransfer.files);
    });
    $('browseBtn').onclick = (e) => { e.stopPropagation(); fileInput.click(); };
    fileInput.onchange = (e) => handleFiles(e.target.files);

    $('toStep2Btn').onclick = () => Wizard.goTo(2);

    /* ============================================================
       CAMERA MANAGEMENT
    ============================================================ */
    async function getCameraStream() {
        if (State.stream && State.stream.active) return State.stream;
        State.stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });
        return State.stream;
    }

    function attachStream(videoEl) {
        if (!videoEl) return;
        if (videoEl.srcObject !== State.stream) {
            videoEl.srcObject = State.stream;
        }
        videoEl.play().catch(() => {});
    }

    /* ============================================================
       STEP 2 — FACE DETECTION (REWRITE, ROBUST)
    ============================================================ */
    const faceVideo = $('faceVideo');
    const faceOverlay = $('faceOverlay');
    const faceSnapshot = $('faceSnapshot');
    let faceLoopActive = false;
    let faceRafId = null;

    // Multiple CDN fallbacks
    const FACE_MODEL_URLS = [
        'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights',
        'https://justadudewhohacks.github.io/face-api.js/models',
        'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'
    ];

    function setModelProgress(pct, msg) {
        $('modelLoadingBar').classList.remove('hidden');
        $('modelLoadingFill').style.width = pct + '%';
        $('modelLoadingPct').textContent = Math.round(pct) + '%';
        if (msg) $('modelLoadingMsg').textContent = msg;
    }

    async function tryLoadModels() {
        for (let i = 0; i < FACE_MODEL_URLS.length; i++) {
            const url = FACE_MODEL_URLS[i];
            try {
                setModelProgress(5, `Menghubungi server (${i + 1}/${FACE_MODEL_URLS.length})...`);
                await faceapi.nets.tinyFaceDetector.loadFromUri(url);
                setModelProgress(40, 'Detektor wajah siap. Memuat model usia & gender...');
                await faceapi.nets.ageGenderNet.loadFromUri(url);
                setModelProgress(75, 'Model usia siap. Memuat model emosi...');
                await faceapi.nets.faceExpressionNet.loadFromUri(url);
                setModelProgress(100, 'Semua model AI berhasil dimuat ✓');
                setTimeout(() => $('modelLoadingBar').classList.add('hidden'), 1200);
                State.faceApiReady = true;
                return true;
            } catch (err) {
                console.warn(`[FaceModel] Gagal dari ${url}:`, err);
            }
        }
        return false;
    }

    async function startFaceCamera() {
        // If verified already, just render UI
        if (State.face.verified) {
            renderFaceVerifiedUI();
            $('toStep3Btn').disabled = false;
        }

        // Attach stream to video
        try {
            const stream = await getCameraStream();
            attachStream(faceVideo);
            $('faceCameraPlaceholder').classList.add('hidden');
            $('faceStatusBadge').classList.remove('hidden');
            $('faceCountBadge').classList.remove('hidden');
        } catch (err) {
            console.error(err);
            $('facePlaceholderMsg').textContent = 'Izin kamera ditolak. Klik tombol untuk mencoba lagi.';
            return;
        }

        // Load models
        if (!State.faceApiReady) {
            const ok = await tryLoadModels();
            if (!ok) {
                $('faceStatusBadgeTxt').textContent = 'Gagal memuat AI';
                $('faceErrorAlert').classList.remove('hidden');
                $('faceErrorTxt').textContent = 'Model AI gagal dimuat. Periksa koneksi internet Anda.';
                return;
            }
        }

        // Start detection loop
        faceLoopActive = true;
        if (faceRafId) cancelAnimationFrame(faceRafId);
        loopFace();
    }

    function stopFaceLoop() {
        faceLoopActive = false;
        if (faceRafId) { cancelAnimationFrame(faceRafId); faceRafId = null; }
    }

    async function loopFace() {
        if (!faceLoopActive || State.step !== 2 || !faceVideo.srcObject) return;

        try {
            if (faceVideo.readyState >= 2 && faceVideo.videoWidth > 0 && State.faceApiReady) {
                const W = faceVideo.videoWidth;
                const H = faceVideo.videoHeight;
                faceOverlay.width = W;
                faceOverlay.height = H;
                const ctx = faceOverlay.getContext('2d');
                ctx.clearRect(0, 0, W, H);

                const detections = await faceapi
                    .detectAllFaces(faceVideo, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416, scoreThreshold: 0.5
                    }))
                    .withAgeAndGender()
                    .withFaceExpressions();

                updateFaceCountBadge(detections.length);

                if (detections.length === 1) {
                    const det = detections[0];
                    const box = det.detection.box;
                    const score = det.detection.score;

                    // Pick strongest emotion
                    let emoKey = 'neutral', emoVal = 0;
                    for (const [k, v] of Object.entries(det.expressions)) {
                        if (v > emoVal) { emoVal = v; emoKey = k; }
                    }

                    const age = Math.round(det.age);
                    const ageConf = det.age > 0 ? 1 : 0;
                    const gender = det.gender; // 'male' | 'female'

                    // Quality scoring (0-100)
                    const boxSizeRatio = (box.width * box.height) / (W * H);
                    const sizeScore = Math.min(1, boxSizeRatio / 0.08);
                    const quality = Math.round(Math.min(1, sizeScore) * 100);

                    // Stability counter
                    State.face.stability = Math.min(
                        State.face.STABILITY_REQUIRED,
                        State.face.stability + 1
                    );

                    const stable = State.face.stability >= State.face.STABILITY_REQUIRED;
                    const qualityOK = quality >= 45;

                    State.face.lastData = {
                        name: State.face.name,
                        age,
                        ageConf,
                        emotion: emoKey,
                        gender,
                        score,
                        quality
                    };

                    // Live UI
                    if (!State.face.verified) {
                        $('faceAgeOut').value = age;
                        $('ageConfidence').textContent = '±' + Math.max(2, Math.round((1 - ageConf + 0.15) * 15)) + ' th';
                        $('faceEmotionOut').textContent = (EMO_MAP[emoKey] || { label: emoKey }).label;
                        $('faceGenderOut').textContent = gender === 'male' ? 'Laki-laki' : 'Perempuan';
                        $('faceQualityFill').style.width = quality + '%';
                        $('faceQualityPct').textContent = quality + '%';
                    }

                    // Draw bracket overlay
                    drawFaceBox(ctx, box, stable, qualityOK);

                    // Status
                    if (!State.face.verified) {
                        if (stable && qualityOK) {
                            $('faceStatusBadgeTxt').textContent = 'Wajah terdeteksi stabil';
                            $('faceStatusBadge').className = 'absolute top-4 left-4 px-3 py-1.5 rounded-full bg-emerald-500/90 text-white text-xs font-bold';
                            $('verifyFaceBtn').disabled = false;
                            $('verifyStatusPill').textContent = 'Siap verifikasi';
                            $('verifyStatusPill').className = 'px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold';
                            $('verifyFaceBtnTxt').textContent = 'Ambil & Verifikasi';
                        } else if (!qualityOK) {
                            $('faceStatusBadgeTxt').textContent = 'Dekatkan wajah ke kamera';
                            $('faceStatusBadge').className = 'absolute top-4 left-4 px-3 py-1.5 rounded-full bg-amber-500/90 text-white text-xs font-bold';
                            $('verifyFaceBtn').disabled = true;
                        } else {
                            $('faceStatusBadgeTxt').textContent = 'Menstabilkan...';
                            $('faceStatusBadge').className = 'absolute top-4 left-4 px-3 py-1.5 rounded-full bg-amber-500/90 text-white text-xs font-bold';
                            $('verifyFaceBtn').disabled = true;
                        }
                    }

                } else if (detections.length === 0) {
                    State.face.stability = 0;
                    if (!State.face.verified) {
                        $('faceStatusBadgeTxt').textContent = 'Wajah tidak terdeteksi';
                        $('faceStatusBadge').className = 'absolute top-4 left-4 px-3 py-1.5 rounded-full bg-rose-500/90 text-white text-xs font-bold';
                        $('verifyFaceBtn').disabled = true;
                        $('faceQualityFill').style.width = '0%';
                        $('faceQualityPct').textContent = '0%';
                    }
                } else {
                    // Multiple faces
                    State.face.stability = 0;
                    if (!State.face.verified) {
                        $('faceStatusBadgeTxt').textContent = 'Hanya 1 wajah yang diizinkan';
                        $('faceStatusBadge').className = 'absolute top-4 left-4 px-3 py-1.5 rounded-full bg-rose-500/90 text-white text-xs font-bold';
                        $('verifyFaceBtn').disabled = true;
                    }
                    // Draw boxes for all
                    detections.forEach(d => {
                        drawFaceBox(ctx, d.detection.box, false, false, 'rgba(244,63,94,0.8)');
                    });
                }
            }
        } catch (err) {
            console.warn('[FaceLoop]', err);
        }

        if (faceLoopActive) {
            faceRafId = requestAnimationFrame(loopFace);
        }
    }

    function drawFaceBox(ctx, box, stable, qualityOK, overrideColor) {
        const color = overrideColor || (stable && qualityOK ? '#10b981' : '#f59e0b');
        ctx.strokeStyle = color;
        ctx.lineWidth = 3;
        const L = 22;
        // 4 corner brackets
        // TL
        ctx.beginPath(); ctx.moveTo(box.x, box.y + L); ctx.lineTo(box.x, box.y); ctx.lineTo(box.x + L, box.y); ctx.stroke();
        // TR
        ctx.beginPath(); ctx.moveTo(box.x + box.width - L, box.y); ctx.lineTo(box.x + box.width, box.y); ctx.lineTo(box.x + box.width, box.y + L); ctx.stroke();
        // BL
        ctx.beginPath(); ctx.moveTo(box.x, box.y + box.height - L); ctx.lineTo(box.x, box.y + box.height); ctx.lineTo(box.x + L, box.y + box.height); ctx.stroke();
        // BR
        ctx.beginPath(); ctx.moveTo(box.x + box.width - L, box.y + box.height); ctx.lineTo(box.x + box.width, box.y + box.height); ctx.lineTo(box.x + box.width, box.y + box.height - L); ctx.stroke();
    }

    function updateFaceCountBadge(n) {
        $('faceCountTxt').textContent = n;
        $('faceCountBadge').className = 'absolute top-4 right-4 px-3 py-1.5 rounded-full text-xs font-bold border ' +
            (n === 1 ? 'bg-emerald-500/80 border-emerald-300/50 text-white' :
             n === 0 ? 'bg-black/70 border-white/20 text-gray-300' :
             'bg-rose-500/80 border-rose-300/50 text-white');
    }

    /* ---------- VERIFY / RETAKE ---------- */
    $('startFaceCamBtn').onclick = () => startFaceCamera();

    $('verifyFaceBtn').onclick = async () => {
        if (!State.face.lastData) {
            $('faceErrorAlert').classList.remove('hidden');
            $('faceErrorTxt').textContent = 'Data wajah belum siap.';
            setTimeout(() => $('faceErrorAlert').classList.add('hidden'), 3000);
            return;
        }

        // Countdown 3..2..1
        const cd = $('verifyCountdown');
        const num = $('verifyCountdownNum');
        cd.classList.remove('hidden');
        for (let i = 3; i >= 1; i--) {
            num.textContent = i;
            await new Promise(r => setTimeout(r, 700));
        }
        cd.classList.add('hidden');

        // Capture snapshot
        try {
            const W = faceVideo.videoWidth, H = faceVideo.videoHeight;
            faceSnapshot.width = W; faceSnapshot.height = H;
            const sctx = faceSnapshot.getContext('2d');
            // mirror horizontally since video is mirrored by CSS object-fit
            sctx.save();
            sctx.translate(W, 0);
            sctx.scale(-1, 1);
            sctx.drawImage(faceVideo, 0, 0, W, H);
            sctx.restore();
            $('snapshotEmpty').classList.add('hidden');
            State.face.snapshot = faceSnapshot.toDataURL('image/jpeg', 0.8);
        } catch (e) { console.warn(e); }

        // Lock data
        const nameVal = $('faceNameOut').value.trim() || window.APP_USER_NAME || 'Pengguna';
        const ageVal = parseInt($('faceAgeOut').value) || State.face.lastData.age;
        State.face.name = nameVal;
        State.face.age = ageVal;
        State.face.emotion = State.face.lastData.emotion;
        State.face.gender = State.face.lastData.gender;
        State.face.quality = State.face.lastData.quality;
        State.face.verified = true;

        renderFaceVerifiedUI();
        $('toStep3Btn').disabled = false;
        saveSession();
        logAction(`Identitas terverifikasi: ${nameVal}, ${ageVal} th, ${State.face.emotion}`);
    };

    $('retakeFaceBtn').onclick = () => {
        State.face.verified = false;
        State.face.snapshot = null;
        State.face.stability = 0;
        $('toStep3Btn').disabled = true;
        $('retakeFaceBtn').disabled = true;
        $('faceReadyAlert').classList.add('hidden');
        $('snapshotEmpty').classList.remove('hidden');
        const sctx = faceSnapshot.getContext('2d');
        sctx.clearRect(0, 0, faceSnapshot.width, faceSnapshot.height);
        $('verifyStatusPill').textContent = 'Menunggu';
        $('verifyStatusPill').className = 'px-2.5 py-1 rounded-full bg-gray-500/20 text-gray-300 text-[10px] font-bold';
        $('verifyFaceBtnTxt').textContent = 'Ambil & Verifikasi';
        sessionStorage.removeItem('ppt_face');
    };

    function renderFaceVerifiedUI() {
        $('faceNameOut').value = State.face.name || window.APP_USER_NAME;
        $('faceAgeOut').value = State.face.age || '';
        $('faceEmotionOut').textContent = (EMO_MAP[State.face.emotion] || { label: State.face.emotion || '-' }).label;
        $('faceGenderOut').textContent = State.face.gender === 'male' ? 'Laki-laki' :
                                       State.face.gender === 'female' ? 'Perempuan' : '-';
        $('faceQualityFill').style.width = (State.face.quality || 0) + '%';
        $('faceQualityPct').textContent = (State.face.quality || 0) + '%';

        $('verifyStatusPill').textContent = 'Terverifikasi';
        $('verifyStatusPill').className = 'px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold';
        $('verifyFaceBtnTxt').textContent = 'Verifikasi Ulang';
        $('verifyFaceBtn').disabled = false;
        $('retakeFaceBtn').disabled = false;
        $('faceReadyAlert').classList.remove('hidden');
        $('faceStatusBadgeTxt').textContent = 'Identitas terverifikasi ✓';
        $('faceStatusBadge').className = 'absolute top-4 left-4 px-3 py-1.5 rounded-full bg-emerald-500/90 text-white text-xs font-bold';

        if (State.face.snapshot) {
            const img = new Image();
            img.onload = () => {
                const sctx = faceSnapshot.getContext('2d');
                sctx.clearRect(0, 0, faceSnapshot.width, faceSnapshot.height);
                sctx.drawImage(img, 0, 0, faceSnapshot.width, faceSnapshot.height);
                $('snapshotEmpty').classList.add('hidden');
            };
            img.src = State.face.snapshot;
        }
    }

    $('toStep3Btn').onclick = () => Wizard.goTo(3);

    /* ============================================================
       STEP 3 — GESTURE
    ============================================================ */
    const gestureVideo = $('gestureVideo');
    const gestureOverlay = $('gestureOverlay');
    let gestureLoopActive = false;
    let gestureRafId = null;

    async function initGestureRecognizer() {
        if (State.gestureRecognizer) return;
        const vision = await FilesetResolver.forVisionTasks(
            "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm"
        );
        State.gestureRecognizer = await GestureRecognizer.createFromOptions(vision, {
            baseOptions: {
                modelAssetPath: "https://storage.googleapis.com/mediapipe-models/gesture_recognizer/gesture_recognizer/float16/1/gesture_recognizer.task",
                delegate: "GPU"
            },
            runningMode: "VIDEO",
            numHands: 1
        });
    }

    async function startGestureCamera() {
        try {
            await getCameraStream();
            attachStream(gestureVideo);
            if (!State.gestureRecognizer) await initGestureRecognizer();
            gestureLoopActive = true;
            if (gestureRafId) cancelAnimationFrame(gestureRafId);
            loopGesture();
        } catch (err) {
            console.error(err);
            alert('Tidak dapat mengakses kamera.');
        }
    }

    function stopGestureLoop() {
        gestureLoopActive = false;
        if (gestureRafId) { cancelAnimationFrame(gestureRafId); gestureRafId = null; }
    }

    function detectPinch(lm) {
        if (!lm || lm.length < 21) return false;
        const t = lm[4], i = lm[8], w = lm[0], m = lm[9];
        const size = Math.hypot(w.x - m.x, w.y - m.y) || 1;
        const d = Math.hypot(t.x - i.x, t.y - i.y);
        return d / size < 0.35;
    }

    function resolveGestureCategory(cat, hand, lm) {
        if (detectPinch(lm)) return 'Pinch';
        if (cat === 'Open_Palm') return hand === 'Left' ? 'Open_Palm_Left' : 'Open_Palm_Right';
        if (cat === 'Closed_Fist') return 'Closed_Fist';
        if (cat === 'Victory') return 'Victory';
        if (cat === 'Thumb_Up') return 'Thumb_Up';
        if (cat === 'Thumb_Down') return 'Thumb_Down';
        if (cat === 'ILoveYou') return 'ILoveYou';
        return null;
    }

    async function loopGesture() {
        if (!gestureLoopActive || State.step !== 3 || !gestureVideo.srcObject) return;

        try {
            if (gestureVideo.readyState >= 2 && State.gestureRecognizer) {
                const W = gestureVideo.videoWidth, H = gestureVideo.videoHeight;
                gestureOverlay.width = W; gestureOverlay.height = H;
                const ctx = gestureOverlay.getContext('2d');
                ctx.clearRect(0, 0, W, H);

                const now = performance.now();
                if (gestureVideo.currentTime !== State.lastVideoTime) {
                    State.lastVideoTime = gestureVideo.currentTime;
                    const results = State.gestureRecognizer.recognizeForVideo(gestureVideo, now);

                    let category = null;
                    if (results.gestures?.length) {
                        const cat = results.gestures[0][0]?.categoryName;
                        const hand = results.handedness?.[0]?.[0]?.categoryName || 'Right';
                        const lm = results.landmarks?.[0] || null;
                        category = resolveGestureCategory(cat, hand, lm);

                        if (lm) {
                            ctx.fillStyle = 'rgba(0,217,255,0.7)';
                            lm.forEach(p => {
                                ctx.beginPath();
                                ctx.arc(p.x * W, p.y * H, 4, 0, Math.PI * 2);
                                ctx.fill();
                            });
                        }
                    }
                    processGestureHold(category);
                }
            }
        } catch (e) { console.warn(e); }

        if (gestureLoopActive) {
            gestureRafId = requestAnimationFrame(loopGesture);
        }
    }

    function processGestureHold(category) {
        const bar = $('gestureHoldBar');
        const label = $('gestureLiveLabel');

        if (!category) {
            State.holdCandidate = null;
            State.holdStart = 0;
            bar.style.width = '0%';
            label.textContent = 'Menunggu gestur...';
            return;
        }

        label.textContent = `Terdeteksi: ${category}`;

        if (State.holdCandidate !== category) {
            State.holdCandidate = category;
            State.holdStart = performance.now();
            bar.style.width = '0%';
            return;
        }

        const elapsed = performance.now() - State.holdStart;
        const pct = Math.min(100, (elapsed / State.GESTURE_HOLD_MS) * 100);
        bar.style.width = pct + '%';

        if (elapsed >= State.GESTURE_HOLD_MS) {
            if (State.gestures.length >= 4) {
                label.textContent = '⚠️ Maksimal 4 gestur sudah tercapai.';
                State.holdCandidate = null;
                State.holdStart = 0;
                bar.style.width = '0%';
                return;
            }
            State.pendingGesture = { category, label: category.replace(/_/g, ' ') };
            showGestureForm();
            State.holdCandidate = null;
            State.holdStart = 0;
            bar.style.width = '0%';
            label.textContent = '✓ Gestur terdeteksi!';
        }
    }

    function showGestureForm() {
        $('gestureForm').classList.remove('hidden');
        $('gestureCategoryInput').value = State.pendingGesture.category;
        $('gestureNameInput').value = State.pendingGesture.label;
        $('gestureNameInput').focus();
    }

    $('cancelGestureBtn').onclick = () => {
        $('gestureForm').classList.add('hidden');
        State.pendingGesture = null;
    };

    $('saveGestureBtn').onclick = () => {
        const name = $('gestureNameInput').value.trim();
        const fn = $('gestureFunctionInput').value;
        if (!name) { alert('Isi nama gestur.'); return; }
        if (!State.pendingGesture) return;

        State.gestures.push({
            id: Date.now(),
            name,
            category: State.pendingGesture.category,
            fn
        });

        $('gestureForm').classList.add('hidden');
        State.pendingGesture = null;
        renderGestureList();
    };

    function renderGestureList() {
        const list = $('gestureList');
        $('gestureCount').textContent = State.gestures.length;
        $('generateBtn').disabled = State.gestures.length === 0;

        if (!State.gestures.length) {
            list.innerHTML = '<p class="text-xs text-gray-500 italic">Belum ada gestur tersimpan.</p>';
            return;
        }

        list.innerHTML = State.gestures.map((g) => `
            <div class="flex items-center gap-3 bg-black/30 rounded-2xl p-3 border border-white/10">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald to-cyan flex items-center justify-center shrink-0">
                    <i class="fas fa-hand-sparkles text-midnight"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold truncate">${g.name}</p>
                    <p class="text-[11px] text-gray-400">${g.category} → <span class="text-cyan-300 font-semibold">${FN_LABELS[g.fn] || g.fn}</span></p>
                </div>
                <button data-id="${g.id}" class="rmGestureBtn text-rose-400 hover:text-rose-200 p-2">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('');

        document.querySelectorAll('.rmGestureBtn').forEach(b => {
            b.onclick = (e) => {
                const id = +e.currentTarget.dataset.id;
                State.gestures = State.gestures.filter(g => g.id !== id);
                renderGestureList();
            };
        });
    }

    $('generateBtn').onclick = () => {
        if (State.gestures.length === 0) return;
        renderFinal();
        Wizard.goTo(4);
    };

    /* ============================================================
       STEP 4 — FINAL
    ============================================================ */
    const finalVideo = $('finalVideo');
    const finalOverlay = $('finalOverlay');
    let finalLoopActive = false;
    let finalRafId = null;
    let finalLastVideoTime = -1;

    async function startFinalCamera() {
        try {
            await getCameraStream();
            attachStream(finalVideo);
            if (!State.faceApiReady) await tryLoadModels();
            if (!State.gestureRecognizer) await initGestureRecognizer();
            finalLoopActive = true;
            if (finalRafId) cancelAnimationFrame(finalRafId);
            loopFinal();
        } catch (e) { console.error(e); }
    }

    function stopFinalLoop() {
        finalLoopActive = false;
        if (finalRafId) { cancelAnimationFrame(finalRafId); finalRafId = null; }
    }

    async function loopFinal() {
        if (!finalLoopActive || State.step !== 4 || !finalVideo.srcObject) return;

        try {
            if (finalVideo.readyState >= 2) {
                const W = finalVideo.videoWidth, H = finalVideo.videoHeight;
                finalOverlay.width = W; finalOverlay.height = H;
                const ctx = finalOverlay.getContext('2d');
                ctx.clearRect(0, 0, W, H);

                if (State.faceApiReady) {
                    const detections = await faceapi
                        .detectAllFaces(finalVideo, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }))
                        .withAgeAndGender();

                    if (detections.length) {
                        const det = detections[0];
                        const box = det.detection.box;
                        drawFaceBox(ctx, box, true, true);
                        $('finalIdentity').textContent = `${State.face.name || window.APP_USER_NAME} • ${State.face.age || Math.round(det.age)} th`;
                    }
                }

                if (State.gestureRecognizer) {
                    const now = performance.now();
                    if (finalVideo.currentTime !== finalLastVideoTime) {
                        finalLastVideoTime = finalVideo.currentTime;
                        const res = State.gestureRecognizer.recognizeForVideo(finalVideo, now);
                        if (res.gestures?.length) {
                            const cat = res.gestures[0][0]?.categoryName;
                            const hand = res.handedness?.[0]?.[0]?.categoryName || 'Right';
                            const lm = res.landmarks?.[0] || null;
                            const key = resolveGestureCategory(cat, hand, lm);
                            const found = State.gestures.find(g => g.category === key);
                            if (found) processFinalGesture(found);
                        }
                    }
                }
            }
        } catch (e) { console.warn(e); }

        if (finalLoopActive) finalRafId = requestAnimationFrame(loopFinal);
    }

    let finalHoldStart = 0, finalHoldId = null;
    function processFinalGesture(gesture) {
        if (finalHoldId !== gesture.id) {
            finalHoldId = gesture.id;
            finalHoldStart = performance.now();
            return;
        }
        if (performance.now() - finalHoldStart >= 1800) {
            performFinalAction(gesture);
            finalHoldId = null;
            finalHoldStart = 0;
        }
    }

    function performFinalAction(gesture) {
        logAction(gesture.name + ' → ' + (FN_LABELS[gesture.fn] || gesture.fn));
        switch (gesture.fn) {
            case 'next': scrollToFile(1); break;
            case 'prev': scrollToFile(-1); break;
            case 'close': closeFullscreen(); break;
            case 'zoomIn': zoom(+10); break;
            case 'zoomOut': zoom(-10); break;
            case 'fullscreen': openFullscreen(currentFileIndex()); break;
        }
    }

    $('toggleFinalCam').onclick = () => {
        const wrap = $('finalCameraWrap');
        wrap.classList.toggle('w-56');
        wrap.classList.toggle('w-14');
        finalVideo.classList.toggle('opacity-0');
    };

    function renderFinal() {
        State.order = Array.from({ length: State.files.length }, (_, i) => i);
        renderOrderedFiles();
        updateOrderButtons();
    }

    function renderOrderedFiles() {
        const wrap = $('finalFileList');
        if (!State.files.length) {
            wrap.innerHTML = '<p class="text-center text-gray-500 italic">Tidak ada file.</p>';
            return;
        }

        wrap.innerHTML = State.order.map((idx, pos) => {
            const f = State.files[idx];
            if (!f) return '';
            return `
                <div class="file-card bg-gradient-to-br from-black/40 to-black/10 border border-white/10 rounded-3xl overflow-hidden"
                     data-file-index="${idx}" data-position="${pos}">
                    <div class="p-4 md:p-5 flex items-center justify-between flex-wrap gap-3 border-b border-white/10">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan to-electric flex items-center justify-center text-midnight font-extrabold shrink-0">
                                #${pos + 1}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold truncate">${f.name}</p>
                                <p class="text-xs text-gray-400">${f.ext.toUpperCase()} • ${formatSize(f.size)}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button data-act="open" data-idx="${idx}"
                                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan to-electric text-midnight font-bold text-xs hover:shadow-cyan/50 transition">
                                <i class="fas fa-folder-open mr-1"></i> Buka File
                            </button>
                            <button data-act="full" data-idx="${idx}"
                                    class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs">
                                <i class="fas fa-expand"></i> Full
                            </button>
                        </div>
                    </div>
                    <div class="relative bg-black/60" style="min-height: 380px;">
                        ${renderFilePreview(f, idx)}
                    </div>
                </div>
            `;
        }).join('');

        wrap.querySelectorAll('button[data-act="open"], button[data-act="full"]').forEach(b => {
            b.onclick = () => openFullscreen(+b.dataset.idx);
        });
    }

    function renderFilePreview(f, idx) {
        if (f.ext === 'pdf') {
            return `<iframe src="${f.url}#toolbar=0&navpanes=0&scrollbar=0" class="w-full h-[520px] border-0"></iframe>`;
        }
        if (['jpg', 'jpeg', 'png'].includes(f.ext)) {
            return `<img src="${f.url}" class="w-full max-h-[520px] object-contain mx-auto">`;
        }
        return `
            <div class="flex flex-col items-center justify-center py-20 text-center px-6">
                <i class="fas fa-file-lines text-7xl text-cyan/40 mb-4"></i>
                <p class="font-bold text-lg">${f.name}</p>
                <p class="text-xs text-gray-400 mt-2 mb-4">Preview Office tersedia setelah file dibuka.</p>
                <button data-act="open" data-idx="${idx}"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan to-electric text-midnight font-bold text-sm">
                    <i class="fas fa-folder-open mr-1"></i> Buka File
                </button>
            </div>
        `;
    }

    document.querySelectorAll('.orderPresetBtn').forEach(b => {
        b.onclick = () => {
            const preset = b.dataset.order;
            State.order = preset.split('').map(n => parseInt(n) - 1).filter(i => i < State.files.length);
            while (State.order.length < State.files.length) {
                for (let i = 0; i < State.files.length; i++) {
                    if (!State.order.includes(i)) State.order.push(i);
                }
            }
            renderOrderedFiles();
            updateOrderButtons();
        };
    });

    function updateOrderButtons() {
        document.querySelectorAll('.orderPresetBtn').forEach(b => {
            const expected = b.dataset.order.split('').map(n => parseInt(n) - 1)
                .filter(i => i < State.files.length).join(',');
            const actual = State.order.join(',');
            b.classList.toggle('bg-cyan', expected === actual);
            b.classList.toggle('text-midnight', expected === actual);
        });
    }

    /* ============================================================
       FULLSCREEN
    ============================================================ */
    let zoomLevel = 100;
    let fsCurrentIdx = 0;
    const currentFileIndex = () => fsCurrentIdx;

    function openFullscreen(idx) {
        fsCurrentIdx = idx;
        const f = State.files[idx];
        if (!f) return;

        let html = '';
        if (f.ext === 'pdf') {
            html = `<iframe src="${f.url}#toolbar=1&navpanes=0" class="w-full h-full border-0" style="transform: scale(${zoomLevel / 100})"></iframe>`;
        } else if (['jpg', 'jpeg', 'png'].includes(f.ext)) {
            html = `<img src="${f.url}" class="max-w-full max-h-full object-contain" style="transform: scale(${zoomLevel / 100})">`;
        } else {
            html = `
                <div class="text-center text-white">
                    <i class="fas fa-file-lines text-8xl text-cyan/40 mb-4"></i>
                    <p class="font-bold text-xl mb-4">${f.name}</p>
                    <a href="${f.url}" download class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-cyan to-electric text-midnight font-extrabold">
                        <i class="fas fa-download"></i> Download / Buka File
                    </a>
                </div>`;
        }
        $('fullscreenContent').innerHTML = html;
        $('fullscreenOverlay').classList.remove('hidden');
    }

    function closeFullscreen() {
        $('fullscreenOverlay').classList.add('hidden');
        $('fullscreenContent').innerHTML = '';
    }
    $('closeFullscreenBtn').onclick = closeFullscreen;

    function zoom(delta) {
        zoomLevel = Math.max(50, Math.min(200, zoomLevel + delta));
        const el = $('fullscreenContent').firstElementChild;
        if (el) el.style.transform = `scale(${zoomLevel / 100})`;
        logAction(`Zoom → ${zoomLevel}%`);
    }

    function scrollToFile(direction) {
        const cards = document.querySelectorAll('.file-card');
        if (!cards.length) return;
        let closestIdx = 0, closestDist = Infinity;
        const vh = window.innerHeight / 2;
        cards.forEach((c, i) => {
            const r = c.getBoundingClientRect();
            const d = Math.abs((r.top + r.bottom) / 2 - vh);
            if (d < closestDist) { closestDist = d; closestIdx = i; }
        });
        const next = Math.max(0, Math.min(cards.length - 1, closestIdx + direction));
        cards[next].scrollIntoView({ behavior: 'smooth', block: 'center' });
        logAction(`Slide → File #${next + 1}`);
    }

    /* ============================================================
       ACTION LOG
    ============================================================ */
    function logAction(msg) {
        const log = $('actionLog');
        if (log.classList.contains('italic')) {
            log.classList.remove('italic', 'text-gray-400');
            log.innerHTML = '';
        }
        const line = document.createElement('div');
        line.className = 'py-1.5 border-b border-white/5 flex items-center justify-between';
        line.innerHTML = `
            <span><i class="fas fa-bolt text-yellow-300 mr-2"></i> ${msg}</span>
            <span class="text-gray-500 text-[10px]">${new Date().toLocaleTimeString('id-ID')}</span>
        `;
        log.prepend(line);
    }
    $('clearLogBtn').onclick = () => {
        $('actionLog').innerHTML = '<p class="text-xs text-gray-400 italic">Belum ada aksi.</p>';
    };

    /* ============================================================
       INIT
    ============================================================ */
    updateStepper();
    renderFileList();
    renderGestureList();
    loadSession();

    window.addEventListener('beforeunload', () => {
        State.files.forEach(f => URL.revokeObjectURL(f.url));
        if (State.stream) State.stream.getTracks().forEach(t => t.stop());
    });
</script>
@endverbatim

@endsection