<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login QR – UNICODE</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Sama seperti sebelumnya, tambahkan tombol refresh */
        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 16px;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .qr-frame {
            position: relative;
            width: 100%;
            max-width: 320px;
            aspect-ratio: 1/1;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            background: #1a1a2e;
        }
        .qr-frame video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .qr-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 2px solid rgba(0, 255, 255, 0.6);
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
            pointer-events: none;
        }
        .qr-overlay::before,
        .qr-overlay::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: #00ffff;
            border-style: solid;
            border-width: 0;
        }
        .qr-overlay::before {
            top: -2px;
            left: -2px;
            border-top-width: 3px;
            border-left-width: 3px;
        }
        .qr-overlay::after {
            bottom: -2px;
            right: -2px;
            border-bottom-width: 3px;
            border-right-width: 3px;
        }
        .input-underline {
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            transition: border-color 0.3s ease;
        }
        .input-underline:focus {
            outline: none;
            border-color: #00ffff;
            box-shadow: 0 2px 10px rgba(0, 255, 255, 0.1);
        }
        .input-underline::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        .btn-primary {
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            transition: all 0.3s ease;
        }
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 210, 255, 0.3);
        }
        .btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none !important;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
            flex-shrink: 0;
        }
        .status-dot.ready { background: #4ade80; }
        .status-dot.scanning { background: #facc15; animation: pulse 1s infinite; }
        .status-dot.error { background: #f87171; }
        .status-dot.detected { background: #60a5fa; }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .camera-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: rgba(255,255,255,0.3);
            font-size: 14px;
        }
        .camera-placeholder i {
            font-size: 48px;
            margin-bottom: 12px;
        }
        .btn-refresh {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-refresh:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

<div class="w-full max-w-md mx-auto">
    <div class="glass rounded-2xl p-6 shadow-2xl">

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-white tracking-tight">UNICODE</h1>
            <p class="text-sm text-cyan-400/80">UKM Programming — QR Login</p>
        </div>

        {{-- Status --}}
        <div id="statusBar" class="flex items-center justify-center text-sm text-gray-300 mb-4">
            <span id="statusDot" class="status-dot ready"></span>
            <span id="statusText">Memulai kamera...</span>
        </div>

        {{-- QR Scanner --}}
        <div id="scannerContainer" class="qr-frame">
            <div id="reader" class="w-full h-full"></div>
            <div class="qr-overlay"></div>
            <div id="scanOverlay" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity duration-300 pointer-events-none">
                <div class="text-center text-white">
                    <i class="fas fa-spinner fa-spin text-3xl text-cyan-400"></i>
                    <p class="text-sm mt-2">Memproses QR...</p>
                </div>
            </div>
            {{-- Placeholder saat kamera belum aktif --}}
            <div id="cameraPlaceholder" class="absolute inset-0 flex items-center justify-center bg-black/80 text-white/30">
                <div class="text-center">
                    <i class="fas fa-camera text-5xl mb-3"></i>
                    <p class="text-sm">Menunggu akses kamera...</p>
                </div>
            </div>
        </div>

        {{-- Tombol Refresh / Retry --}}
        <div id="cameraControls" class="mt-3 flex justify-center gap-2">
            <button onclick="refreshCamera()" class="btn-refresh text-sm">
                <i class="fas fa-sync-alt mr-1"></i> Refresh Kamera
            </button>
            <button onclick="retryCamera()" class="btn-refresh text-sm">
                <i class="fas fa-redo mr-1"></i> Coba Ulang
            </button>
        </div>

        {{-- Manual Fallback --}}
        <div class="mt-4 text-center">
            <button onclick="showManualInput()" class="text-sm text-cyan-400 hover:text-cyan-300 transition">
                <i class="fas fa-keyboard mr-1"></i> Masukkan QR manual
            </button>
        </div>

        {{-- Manual Input (hidden) --}}
        <div id="manualArea" class="hidden mt-4 fade-in">
            <div class="flex gap-2">
                <input type="text" id="manualInput" placeholder="Tempel URL atau UUID QR"
                       class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-cyan-400 focus:outline-none">
                <button onclick="manualScan()" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-2">Contoh: http://localhost/q/00d4b669-fcb4-45c7-bfca-28220fa6316d</p>
        </div>

        {{-- User Info & Login Form --}}
        <div id="loginForm" class="hidden mt-6 fade-in">
            <!-- Sama seperti sebelumnya -->
            <div id="userCard" class="bg-white/5 rounded-xl p-4 border border-white/10 mb-4">
                <div class="flex items-center gap-4">
                    <div id="userAvatar" class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">?</div>
                    <div class="flex-1 min-w-0">
                        <p id="userName" class="text-white font-semibold text-lg truncate">Bima Santoso</p>
                        <p id="userNim" class="text-sm text-gray-400 truncate">NIM: 2310xxxx</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span id="userRole" class="text-xs px-2 py-0.5 bg-cyan-500/20 text-cyan-300 rounded-full">Pengurus</span>
                            <span id="userStatus" class="text-xs text-green-400">● Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <form id="authForm" class="space-y-4">
                <input type="hidden" name="challenge" id="challengeInput">

                <div>
                    <label class="block text-sm text-gray-400 mb-1">Password</label>
                    <input type="password" name="password" id="passwordInput" placeholder="Masukkan password Anda"
                           class="w-full input-underline py-3 text-white text-lg" autocomplete="current-password">
                </div>

                <div id="pinField" class="hidden">
                    <label class="block text-sm text-gray-400 mb-1">PIN</label>
                    <input type="password" name="pin" id="pinInput" placeholder="Masukkan PIN 6 digit"
                           class="w-full input-underline py-3 text-white text-lg" maxlength="6" autocomplete="off">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="rememberCheck" class="rounded border-gray-600 bg-transparent text-cyan-500">
                    <label for="rememberCheck" class="text-sm text-gray-400">Ingat saya</label>
                </div>

                <div id="authError" class="hidden text-red-400 text-sm bg-red-500/10 border border-red-500/20 rounded-lg p-2"></div>

                <button type="submit" id="loginBtn" disabled
                        class="w-full py-3 btn-primary text-white font-semibold rounded-xl text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>

            <button onclick="resetToScan()" class="mt-3 w-full text-center text-sm text-gray-500 hover:text-gray-300 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali scan QR
            </button>
        </div>

        {{-- Link ke login manual --}}
        <div class="mt-4 text-center border-t border-white/5 pt-4">
            <a href="{{ route('login.manual.form') }}" class="text-sm text-gray-400 hover:text-white transition">
                <i class="fas fa-user mr-1"></i> Login manual dengan NIM
            </a>
        </div>
    </div>
</div>

{{-- QR Scanner Library --}}
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    // ============================================================
    // STATE
    // ============================================================
    let html5QrCode = null;
    let scannerActive = false;
    let currentChallenge = null;
    let userId = null;
    let isOwner = false;
    let scannerStarted = false;
    let cameraRefreshAttempts = 0;

    // DOM refs
    const statusDot = document.getElementById('statusDot');
    const statusText = document.getElementById('statusText');
    const reader = document.getElementById('reader');
    const scanOverlay = document.getElementById('scanOverlay');
    const loginForm = document.getElementById('loginForm');
    const userAvatar = document.getElementById('userAvatar');
    const userName = document.getElementById('userName');
    const userNim = document.getElementById('userNim');
    const userRole = document.getElementById('userRole');
    const userStatus = document.getElementById('userStatus');
    const challengeInput = document.getElementById('challengeInput');
    const passwordInput = document.getElementById('passwordInput');
    const pinInput = document.getElementById('pinInput');
    const pinField = document.getElementById('pinField');
    const loginBtn = document.getElementById('loginBtn');
    const authError = document.getElementById('authError');
    const authForm = document.getElementById('authForm');
    const manualArea = document.getElementById('manualArea');
    const cameraPlaceholder = document.getElementById('cameraPlaceholder');

    // ============================================================
    // STATUS UPDATE
    // ============================================================
    function setStatus(type, message) {
        statusDot.className = 'status-dot ' + type;
        statusText.textContent = message;
        console.log('[QR Login] Status:', type, message);
    }

    // ============================================================
    // REFRESH & RETRY
    // ============================================================
    function refreshCamera() {
        setStatus('scanning', 'Menyegarkan kamera...');
        stopScanner();
        cameraRefreshAttempts = 0;
        setTimeout(() => {
            startScanner();
        }, 300);
    }

    function retryCamera() {
        setStatus('scanning', 'Mencoba ulang...');
        stopScanner();
        cameraRefreshAttempts++;
        setTimeout(() => {
            startScanner();
        }, 500);
    }

    // ============================================================
    // SCANNER
    // ============================================================
    async function startScanner() {
        if (scannerStarted) return;
        scannerStarted = true;

        // Sembunyikan placeholder
        cameraPlaceholder.style.display = 'none';
        cameraPlaceholder.innerHTML = '<i class="fas fa-camera text-5xl mb-3"></i><p class="text-sm">Menunggu akses kamera...</p>';

        // Cek apakah browser mendukung kamera
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setStatus('error', 'Browser tidak mendukung kamera.');
            cameraPlaceholder.innerHTML = '<i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i><p class="text-sm mt-2">Kamera tidak didukung</p>';
            cameraPlaceholder.style.display = 'flex';
            manualArea.classList.remove('hidden');
            return;
        }

        // Cek izin kamera
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            stream.getTracks().forEach(track => track.stop());
        } catch (err) {
            console.error('Camera permission error:', err);
            setStatus('error', 'Izin kamera ditolak. Gunakan manual.');
            cameraPlaceholder.innerHTML = '<i class="fas fa-camera-slash text-4xl text-red-400"></i><p class="text-sm mt-2">Akses kamera ditolak</p>';
            cameraPlaceholder.style.display = 'flex';
            manualArea.classList.remove('hidden');
            scannerStarted = false;
            return;
        }

        setStatus('scanning', 'Mengaktifkan kamera...');

        try {
            if (html5QrCode) {
                await html5QrCode.clear();
                html5QrCode = null;
            }

            html5QrCode = new Html5Qrcode("reader");
            const config = {
                fps: 15,
                qrbox: { width: 220, height: 220 },
                aspectRatio: 1.0,
            };

            await html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanError
            );

            scannerActive = true;
            setStatus('scanning', 'Arahkan QR ke kamera');
            cameraPlaceholder.style.display = 'none';
        } catch (err) {
            console.error('Scanner start error:', err);
            setStatus('error', 'Gagal akses kamera: ' + err.message);
            cameraPlaceholder.innerHTML = '<i class="fas fa-exclamation-circle text-4xl text-red-400"></i><p class="text-sm mt-2">' + err.message + '</p>';
            cameraPlaceholder.style.display = 'flex';
            manualArea.classList.remove('hidden');
            scannerStarted = false;
        }
    }

    function stopScanner() {
        if (html5QrCode && scannerActive) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                scannerActive = false;
                scannerStarted = false;
                setStatus('ready', 'Scanner dihentikan');
            }).catch(() => {});
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (!scannerActive) return;
        setStatus('detected', '✓ QR terdeteksi!');
        scanOverlay.classList.add('opacity-100');
        stopScanner();
        processQrPayload(decodedText);
    }

    function onScanError(err) {
        // abaikan error scanning normal
    }

    // ============================================================
    // MANUAL INPUT
    // ============================================================
    function showManualInput() {
        manualArea.classList.toggle('hidden');
        if (!manualArea.classList.contains('hidden')) {
            document.getElementById('manualInput').focus();
        }
    }

    function manualScan() {
        const input = document.getElementById('manualInput').value.trim();
        if (!input) {
            alert('Masukkan URL atau UUID QR.');
            return;
        }
        processQrPayload(input);
    }

    // ============================================================
    // PROCESS QR PAYLOAD
    // ============================================================
    function processQrPayload(payload) {
        setStatus('scanning', 'Memverifikasi QR...');
        scanOverlay.classList.add('opacity-100');

        fetch('{{ route('login.qr-scan') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_payload: payload })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Server error');
            return data;
        })
        .then(data => {
            scanOverlay.classList.remove('opacity-100');
            if (data.valid) {
                showLoginForm(data);
            } else {
                setStatus('error', '❌ ' + data.message);
                setTimeout(() => setStatus('ready', 'Siap scan QR'), 3000);
            }
        })
        .catch(err => {
            console.error('Validate error:', err);
            scanOverlay.classList.remove('opacity-100');
            setStatus('error', '❌ ' + err.message);
            setTimeout(() => setStatus('ready', 'Siap scan QR'), 3000);
        });
    }

    // ============================================================
    // SHOW LOGIN FORM
    // ============================================================
    function showLoginForm(data) {
        const user = data.user;
        userId = user.id;
        isOwner = user.needPin === true;
        currentChallenge = data.challenge;

        userAvatar.textContent = user.name.charAt(0).toUpperCase();
        userName.textContent = user.name;
        userNim.textContent = 'NIM: ' + (user.nim || '-');
        userRole.textContent = user.role || 'Guest';
        userStatus.textContent = user.is_active ? '● Aktif' : '● Tidak Aktif';
        userStatus.className = 'text-xs ' + (user.is_active ? 'text-green-400' : 'text-red-400');

        if (isOwner) {
            pinField.classList.remove('hidden');
        } else {
            pinField.classList.add('hidden');
            pinInput.value = '';
        }

        challengeInput.value = currentChallenge;
        passwordInput.value = '';
        pinInput.value = '';
        loginBtn.disabled = true;
        authError.classList.add('hidden');

        loginForm.classList.remove('hidden');
        loginForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

        setStatus('detected', '✓ Akun teridentifikasi — silakan login');

        setTimeout(() => passwordInput.focus(), 300);
        checkInputs();
    }

    function resetToScan() {
        loginForm.classList.add('hidden');
        manualArea.classList.add('hidden');
        setStatus('ready', 'Siap scan QR');
        startScanner();
    }

    // ============================================================
    // INPUT VALIDATION & BUTTON STATE
    // ============================================================
    function checkInputs() {
        const pw = passwordInput.value.trim();
        const pin = pinInput.value.trim();
        const pwOk = pw.length >= 6;
        const pinOk = isOwner ? pin.length >= 6 : true;
        const allOk = pwOk && pinOk;

        loginBtn.disabled = !allOk;
        if (allOk) {
            loginBtn.classList.remove('opacity-50');
            loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Masuk';
        } else {
            loginBtn.classList.add('opacity-50');
            let label = 'Masuk';
            if (!pwOk) label = 'Password minimal 6 karakter';
            else if (!pinOk) label = 'PIN 6 digit';
            loginBtn.innerHTML = '<i class="fas fa-lock mr-2"></i> ' + label;
        }
    }

    passwordInput.addEventListener('input', checkInputs);
    pinInput.addEventListener('input', checkInputs);

    // ============================================================
    // SUBMIT LOGIN
    // ============================================================
    authForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const pw = passwordInput.value.trim();
        const pin = pinInput.value.trim();

        if (isOwner && pin.length < 6) {
            showError('PIN harus 6 digit.');
            return;
        }
        if (pw.length < 6) {
            showError('Password minimal 6 karakter.');
            return;
        }

        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

        fetch('{{ route('login.qr-authenticate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                challenge: currentChallenge,
                password: pw,
                pin: isOwner ? pin : null,
                remember: document.getElementById('rememberCheck').checked,
            })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Auth failed');
            return data;
        })
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Masuk';
                showError(data.message || 'Login gagal.');
            }
        })
        .catch(err => {
            console.error('Auth error:', err);
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Masuk';
            showError(err.message || 'Terjadi kesalahan server.');
        });
    });

    function showError(msg) {
        authError.textContent = msg;
        authError.classList.remove('hidden');
        setTimeout(() => authError.classList.add('hidden'), 5000);
    }

    // ============================================================
    // INIT
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        setStatus('scanning', 'Memulai kamera...');
        startScanner();
    });

    window.addEventListener('beforeunload', function() {
        stopScanner();
    });
</script>
</body>
</html>