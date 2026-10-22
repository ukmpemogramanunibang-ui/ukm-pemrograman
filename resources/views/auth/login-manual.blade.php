@extends('layouts.auth_mini')

@section('title', 'Login Manual – UNICODE')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { background: #050A30; font-family: 'Poppins', sans-serif; }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 32px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            padding: 2rem;
        }
        .input-field {
            width: 100%;
            padding: 14px 48px 14px 48px;
            background: rgba(255,255,255,0.07);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s;
        }
        .input-field:focus {
            border-color: #00D9FF;
            background: rgba(255,255,255,0.12);
            box-shadow: 0 0 0 4px rgba(0,217,255,0.15);
            outline: none;
        }
        .input-field:disabled { opacity: 0.4; cursor: not-allowed; }
        .input-field.is-invalid {
            border-color: rgba(239,68,68,0.6);
            box-shadow: 0 0 0 4px rgba(239,68,68,0.12);
        }
        .input-field.is-valid {
            border-color: rgba(16,185,129,0.5);
            box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
        }
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            pointer-events: none;
        }
        .toggle-eye {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            cursor: pointer;
            transition: color 0.2s;
            z-index: 2;
        }
        .toggle-eye:hover { color: #00D9FF; }
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00D9FF, #2563EB);
            border: none;
            border-radius: 50px;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,217,255,0.25);
        }
        .btn-login:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-login:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,217,255,0.4);
        }
        .field-container {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            max-height: 0;
            opacity: 0;
        }
        .field-container.show {
            max-height: 400px;
            opacity: 1;
            margin-top: 20px;
        }
        .link-cyan { color: #00D9FF; text-decoration: none; }
        .link-cyan:hover { color: #fff; }
        .link-gray { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 13px; }
        .link-gray:hover { color: #00D9FF; }
        .feedback-text { font-size: 13px; margin-top: 6px; min-height: 24px; line-height: 1.35; }
        .field-hint {
            font-size: 11px;
            margin-top: 4px;
            color: rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .field-hint .hint-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 1px 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            background: rgba(0,217,255,0.15);
            color: #7DD3FC;
        }
        .field-hint .hint-badge.role-owner {
            background: rgba(245,158,11,0.18);
            color: #FCD34D;
        }
        .shake { animation: shake 0.4s ease; }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%     { transform: translateX(-8px); }
            75%     { transform: translateX(8px); }
        }
        .spinner {
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* role badge di info box */
        .role-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .role-chip.owner       { background: rgba(245,158,11,0.2);  color: #FCD34D; border: 1px solid rgba(245,158,11,0.35); }
        .role-chip.superadmin  { background: rgba(239,68,68,0.2);   color: #FCA5A5; border: 1px solid rgba(239,68,68,0.35); }
        .role-chip.ketua       { background: rgba(168,85,247,0.2);  color: #D8B4FE; border: 1px solid rgba(168,85,247,0.35); }
        .role-chip.pengurus    { background: rgba(16,185,129,0.2);  color: #6EE7B7; border: 1px solid rgba(16,185,129,0.35); }
        .role-chip.anggota     { background: rgba(59,130,246,0.2);  color: #93C5FD; border: 1px solid rgba(59,130,246,0.35); }
        .role-chip.pengunjung  { background: rgba(148,163,184,0.2); color: #CBD5E1; border: 1px solid rgba(148,163,184,0.35); }
    </style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12"
     style="background: linear-gradient(135deg, #050A30 0%, #0B1F6B 40%, #1A3A8A 70%, #00D9FF 100%);">

    <div class="w-full max-w-md">
        <div class="glass-card" data-aos="fade-up">

            {{-- ─────── HEADER ─────── --}}
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-cyan-400 to-blue-600
                            rounded-full flex items-center justify-center text-white text-3xl shadow-lg mb-4">
                    <i class="fas fa-code"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Login Manual</h2>
                <p class="text-sm text-white/50 mt-1">Masuk menggunakan NIM & password</p>
            </div>

            {{-- ─────── FLASH ERROR ─────── --}}
            @if(session('error'))
                <div class="bg-red-500/10 border border-red-400/30 text-red-200
                            px-4 py-3 rounded-xl mb-5 text-sm flex items-start gap-2"
                     data-aos="shake">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-400/30 text-red-200
                            px-4 py-3 rounded-xl mb-5 text-sm space-y-1">
                    @foreach($errors->all() as $err)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <span>{{ $err }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ─────── FORM ─────── --}}
            <form action="{{ route('login.manual') }}" method="POST" id="loginForm" novalidate autocomplete="off">
                @csrf

                {{-- ═══ NIM ═══ --}}
                <div>
                    <label for="nim" class="block text-sm font-medium text-white/70 mb-1.5">
                        Nomor Induk Mahasiswa (NIM)
                    </label>
                    <div class="relative">
                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="nim" id="nim" required
                               placeholder="Masukkan NIM"
                               class="input-field" autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 20)">
                        <span id="nimStatus"
                              class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1">
                            <i class="fas fa-spinner hidden text-cyan-400" id="nimSpinner"></i>
                            <i class="fas fa-check-circle hidden text-emerald-400" id="nimCheck"></i>
                            <i class="fas fa-times-circle hidden text-red-400" id="nimCross"></i>
                        </span>
                    </div>
                    <div id="nimFeedback" class="feedback-text text-white/60"></div>
                </div>

                {{-- ═══ PASSWORD ═══ --}}
                <div class="field-container" id="passwordContainer">
                    <label for="password" class="block text-sm font-medium text-white/70 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password"
                               placeholder="Masukkan password"
                               class="input-field" autocomplete="off" disabled>
                        <span class="toggle-eye" onclick="toggleVisibility('password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div id="passwordHint" class="field-hint">
                        <span class="hint-badge" id="passwordMinHint">
                            <i class="fas fa-shield-alt"></i>
                            <span id="passwordMinText">Min 6 karakter</span>
                        </span>
                    </div>
                    <div id="passwordFeedback" class="feedback-text"></div>
                </div>

                {{-- ═══ PIN ═══ --}}
                <div class="field-container" id="pinContainer">
                    <label for="pin" class="block text-sm font-medium text-white/70 mb-1.5">
                        <span id="pinLabel">PIN</span>
                    </label>
                    <div class="relative">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" name="pin" id="pin"
                               placeholder="Masukkan PIN"
                               class="input-field" autocomplete="off" disabled>
                        {{-- ⚠️ Perhatikan: TIDAK ada maxlength="6",
                             TIDAK ada inputmode="numeric",
                             TIDAK ada strip non-digit ⚠️ --}}
                        <span class="toggle-eye" onclick="toggleVisibility('pin', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div id="pinHint" class="field-hint">
                        <span class="hint-badge" id="pinMinHint">
                            <i class="fas fa-shield-alt"></i>
                            <span id="pinMinText">Min 6 karakter</span>
                        </span>
                        <span class="hint-badge" id="pinRoleHint" style="display:none">
                            <i class="fas fa-crown"></i>
                            <span id="pinRoleText">Owner wajib 8 karakter</span>
                        </span>
                    </div>
                    <div id="pinFeedback" class="feedback-text"></div>
                </div>

                {{-- ═══ SUBMIT ═══ --}}
                <button type="submit" id="loginBtn" class="btn-login mt-6" disabled>
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            {{-- ─────── FOOTER LINKS ─────── --}}
            <div class="mt-6 text-center space-y-2 text-sm">
                <p>Belum punya akun?
                    <a href="{{ route('guest.register') }}" class="link-cyan">Daftar di sini</a>
                </p>
                <p>
                    <a href="{{ route('password.request') }}" class="link-gray">
                        <i class="fas fa-key mr-1"></i> Lupa password?
                    </a>
                </p>
                <p>
                    <a href="{{ route('login') }}" class="link-gray">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke pilihan login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
/* ══════════════════════════════════════════════════════════════
 |  LOGIN MANUAL — v2
 |  • Owner  : password & PIN min 8 karakter
 |  • Lainnya : password min 6, PIN min 6
 |  • PIN tidak lagi dipotong 6 / dibatasi angka saja
 ══════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    AOS.init({ duration: 700, once: true });

    /* ─────────── KONFIGURASI ─────────── */
    const RULES = {
        owner:      { pwMin: 8, pinMin: 8, label: 'Owner' },
        superadmin: { pwMin: 8, pinMin: 8, label: 'Superadmin' },
        ketua:      { pwMin: 8, pinMin: 6, label: 'Ketua' },
        pengurus:   { pwMin: 8, pinMin: 6, label: 'Pengurus' },
        anggota:    { pwMin: 6, pinMin: 6, label: 'Anggota' },
        pengunjung: { pwMin: 6, pinMin: 6, label: 'Pengunjung' },
    };
    const DEFAULT_RULE = { pwMin: 6, pinMin: 6, label: 'User' };

    /* ─────────── STATE ─────────── */
    let state = {
        nimValid:  false,
        needPin:   false,
        role:      null,          // slug role dari server
        rule:      { ...DEFAULT_RULE },
        submitting: false,
    };

    /* ─────────── ELEMEN ─────────── */
    const $ = (id) => document.getElementById(id);
    const nimInput         = $('nim');
    const passwordInput    = $('password');
    const pinInput         = $('pin');
    const loginBtn         = $('loginBtn');
    const passwordContainer= $('passwordContainer');
    const pinContainer     = $('pinContainer');
    const nimFeedback      = $('nimFeedback');
    const nimSpinner       = $('nimSpinner');
    const nimCheck         = $('nimCheck');
    const nimCross         = $('nimCross');
    const loginForm        = $('loginForm');

    const passwordFeedback = $('passwordFeedback');
    const pinFeedback      = $('pinFeedback');
    const pinLabel         = $('pinLabel');
    const passwordMinText  = $('passwordMinText');
    const pinMinText       = $('pinMinText');
    const pinRoleHint      = $('pinRoleHint');
    const pinRoleText      = $('pinRoleText');

    let debounceTimer = null;

    /* ═══════════════════════════════════════════════════════════
       UTIL — Set feedback state ke field
       ═══════════════════════════════════════════════════════════ */
    function setFeedback(el, type, html) {
        if (!el) return;
        const colorMap = {
            info:    'text-cyan-300',
            success: 'text-emerald-300',
            error:   'text-red-300',
            mute:    'text-white/60',
        };
        el.className = 'feedback-text ' + (colorMap[type] || colorMap.mute);
        el.innerHTML = html || '';
    }

    function setInputState(input, stateName) {
        if (!input) return;
        input.classList.remove('is-valid', 'is-invalid');
        if (stateName === 'valid')   input.classList.add('is-valid');
        if (stateName === 'invalid') input.classList.add('is-invalid');
    }

    /* ═══════════════════════════════════════════════════════════
       RESET semua field di bawah NIM
       ═══════════════════════════════════════════════════════════ */
    function resetSubFields() {
        state.nimValid = false;
        state.needPin  = false;
        state.role     = null;
        state.rule     = { ...DEFAULT_RULE };

        passwordContainer.classList.remove('show');
        pinContainer.classList.remove('show');

        passwordInput.disabled = true;
        pinInput.disabled      = true;
        passwordInput.value    = '';
        pinInput.value         = '';

        setInputState(passwordInput, null);
        setInputState(pinInput, null);

        setFeedback(passwordFeedback, 'mute', '');
        setFeedback(pinFeedback, 'mute', '');

        passwordMinText.textContent = `Min ${DEFAULT_RULE.pwMin} karakter`;
        pinMinText.textContent      = `Min ${DEFAULT_RULE.pinMin} karakter`;
        pinLabel.textContent        = 'PIN';
        pinRoleHint.style.display   = 'none';

        updateLoginBtn();
    }

    /* ═══════════════════════════════════════════════════════════
       Update state tombol login
       ═══════════════════════════════════════════════════════════ */
    function updateLoginBtn() {
        const pwOk  = !passwordInput.disabled
                   && passwordInput.value.length >= state.rule.pwMin;
        const pinOk = !state.needPin
                   || (!pinInput.disabled && pinInput.value.length >= state.rule.pinMin);

        const allOk = state.nimValid && pwOk && pinOk;
        loginBtn.disabled = !allOk;

        if (allOk) {
            loginBtn.style.background = 'linear-gradient(135deg, #34d399, #059669)';
            loginBtn.style.boxShadow  = '0 10px 30px rgba(52, 211, 153, 0.3)';
        } else {
            loginBtn.style.background = 'linear-gradient(135deg, #00D9FF, #2563EB)';
            loginBtn.style.boxShadow  = '0 10px 30px rgba(0, 217, 255, 0.25)';
        }
    }

    /* ═══════════════════════════════════════════════════════════
       Terapkan rule berdasarkan role (dari server)
       ═══════════════════════════════════════════════════════════ */
    function applyRoleRules(roleSlug) {
        const slug = String(roleSlug || '').toLowerCase();
        state.role = slug;
        state.rule = RULES[slug] || DEFAULT_RULE;

        const { pwMin, pinMin, label } = state.rule;

        // Update hint text
        passwordMinText.textContent = `Min ${pwMin} karakter`;
        pinMinText.textContent      = `Min ${pinMin} karakter`;

        // Update label PIN kalau role khusus
        if (pinMin >= 8) {
            pinLabel.textContent      = `PIN (${pinMin} karakter)`;
            pinRoleHint.style.display = 'inline-flex';
            pinRoleText.textContent   = `${label} wajib ${pinMin} karakter`;
        } else {
            pinLabel.textContent      = `PIN (min ${pinMin} karakter)`;
            pinRoleHint.style.display = 'none';
        }

        // Warnai role hint kalau owner
        if (slug === 'owner' || slug === 'superadmin') {
            pinRoleHint.classList.add('role-owner');
        } else {
            pinRoleHint.classList.remove('role-owner');
        }

        // Update placeholder
        passwordInput.placeholder = `Min ${pwMin} karakter`;
        pinInput.placeholder      = `Min ${pinMin} karakter`;
    }

    /* ═══════════════════════════════════════════════════════════
       Validasi field password realtime
       ═══════════════════════════════════════════════════════════ */
    function validatePassword() {
        const val = passwordInput.value;
        const min = state.rule.pwMin;

        if (val.length === 0) {
            setInputState(passwordInput, null);
            setFeedback(passwordFeedback, 'mute', '');
            updateLoginBtn();
            return false;
        }
        if (val.length < min) {
            setInputState(passwordInput, 'invalid');
            setFeedback(passwordFeedback, 'error',
                `<i class="fas fa-exclamation-circle mr-1"></i>Minimal ${min} karakter (${val.length}/${min})`);
            updateLoginBtn();
            return false;
        }
        setInputState(passwordInput, 'valid');
        setFeedback(passwordFeedback, 'success',
            `<i class="fas fa-check-circle mr-1"></i>Password valid`);
        updateLoginBtn();
        return true;
    }

    /* ═══════════════════════════════════════════════════════════
       Validasi field PIN realtime
       ═══════════════════════════════════════════════════════════ */
    function validatePin() {
        const val = pinInput.value;
        const min = state.rule.pinMin;

        if (val.length === 0) {
            setInputState(pinInput, null);
            setFeedback(pinFeedback, 'mute', '');
            updateLoginBtn();
            return false;
        }
        if (val.length < min) {
            setInputState(pinInput, 'invalid');
            setFeedback(pinFeedback, 'error',
                `<i class="fas fa-exclamation-circle mr-1"></i>Minimal ${min} karakter (${val.length}/${min})`);
            updateLoginBtn();
            return false;
        }
        setInputState(pinInput, 'valid');
        setFeedback(pinFeedback, 'success',
            `<i class="fas fa-check-circle mr-1"></i>PIN valid`);
        updateLoginBtn();
        return true;
    }

    /* ═══════════════════════════════════════════════════════════
       Toggle visibility password / pin
       ═══════════════════════════════════════════════════════════ */
    window.toggleVisibility = function (id, el) {
        const input = document.getElementById(id);
        if (!input) return;
        const icon = el.querySelector('i');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        if (icon) {
            icon.className = show
                ? 'fas fa-eye-slash'
                : 'fas fa-eye';
        }
    };

    /* ═══════════════════════════════════════════════════════════
       NIM — fetch role → apply rules
       ═══════════════════════════════════════════════════════════ */
    nimInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const nim = this.value.trim();

        if (nim.length < 3) {
            resetSubFields();
            setFeedback(nimFeedback, 'mute', '');
            nimSpinner.classList.add('hidden');
            nimCheck.classList.add('hidden');
            nimCross.classList.add('hidden');
            return;
        }

        nimSpinner.classList.remove('hidden');
        nimCheck.classList.add('hidden');
        nimCross.classList.add('hidden');
        setFeedback(nimFeedback, 'info', 'Memeriksa NIM...');

        debounceTimer = setTimeout(() => {
            fetch('{{ route('login.check-nim-json') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nim: nim })
            })
            .then(async (res) => {
                if (!res.ok) {
                    const txt = await res.text();
                    throw new Error(`HTTP ${res.status}: ${txt.substring(0, 80)}`);
                }
                return res.json();
            })
            .then((data) => {
                nimSpinner.classList.add('hidden');

                if (data && data.found) {
                    /* ✅ NIM ditemukan */
                    state.nimValid = true;
                    nimCheck.classList.remove('hidden');
                    nimCross.classList.add('hidden');

                    const roleSlug = (data.role_slug || data.role || '').toString().toLowerCase() || null;
                    applyRoleRules(roleSlug);

                    // Tampilkan role chip
                    const roleChipClass = ['owner','superadmin','ketua','pengurus','anggota','pengunjung']
                        .includes(roleSlug) ? roleSlug : 'anggota';
                    setFeedback(nimFeedback, 'success',
                        `<i class="fas fa-check-circle mr-1"></i>`
                        + `Ditemukan: <b>${escapeHtml(data.name || '-')}</b> `
                        + `<span class="role-chip ${roleChipClass}">${escapeHtml(data.role || roleSlug || 'User')}</span>`);

                    // Show password
                    passwordContainer.classList.add('show');
                    passwordInput.disabled = false;
                    setTimeout(() => passwordInput.focus(), 350);

                    // Show PIN hanya kalau perlu
                    state.needPin = !!data.needPin;
                    if (state.needPin) {
                        pinContainer.classList.add('show');
                        pinInput.disabled = false;
                    } else {
                        pinContainer.classList.remove('show');
                        pinInput.disabled = true;
                        pinInput.value = '';
                    }
                    updateLoginBtn();

                } else {
                    /* ❌ NIM tidak ditemukan */
                    resetSubFields();
                    nimCheck.classList.add('hidden');
                    nimCross.classList.remove('hidden');
                    setFeedback(nimFeedback, 'error',
                        `<i class="fas fa-times-circle mr-1"></i>NIM tidak terdaftar`);

                    nimInput.classList.add('shake');
                    setTimeout(() => nimInput.classList.remove('shake'), 400);
                }
            })
            .catch((error) => {
                console.error('[check-nim]', error);
                nimSpinner.classList.add('hidden');
                nimCross.classList.remove('hidden');
                nimCheck.classList.add('hidden');
                setFeedback(nimFeedback, 'error',
                    `<i class="fas fa-exclamation-triangle mr-1"></i>`
                    + `Gagal memeriksa NIM. ${escapeHtml(error.message || '')}`);
                resetSubFields();
            });
        }, 400);
    });

    /* ═══════════════════════════════════════════════════════════
       Listener realtime
       ═══════════════════════════════════════════════════════════ */
    passwordInput.addEventListener('input', validatePassword);
    pinInput.addEventListener('input', validatePin);

    /* ═══════════════════════════════════════════════════════════
       Submit — prevent double submit, validasi final
       ═══════════════════════════════════════════════════════════ */
    loginForm.addEventListener('submit', function (e) {
        if (state.submitting) { e.preventDefault(); return; }

        const pwOk  = validatePassword();
        const pinOk = !state.needPin || validatePin();

        if (!state.nimValid || !pwOk || !pinOk) {
            e.preventDefault();
            // Fokus ke field pertama yang invalid
            if (!passwordInput.disabled && !pwOk) passwordInput.focus();
            else if (!pinInput.disabled && !pinOk) pinInput.focus();
            return;
        }

        state.submitting = true;
        loginBtn.innerHTML = '<span class="spinner"></span> Memproses...';
        loginBtn.disabled  = true;
        loginBtn.style.background = 'linear-gradient(135deg, #00D9FF, #2563EB)';
    });

    loginForm.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && loginBtn.disabled) e.preventDefault();
    });

    /* ═══════════════════════════════════════════════════════════
       Util — escape HTML agar aman dari XSS feedback
       ═══════════════════════════════════════════════════════════ */
    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Inisialisasi
    resetSubFields();
})();
</script>
@endpush