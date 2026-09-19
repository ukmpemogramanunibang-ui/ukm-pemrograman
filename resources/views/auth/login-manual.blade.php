@extends('layouts.auth_mini')

@section('title', 'Login Manual – UNICODE')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { background: #050A30; font-family: 'Poppins', sans-serif; }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 32px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            padding: 2rem;
        }
        .input-field {
            width: 100%;
            padding: 14px 20px 14px 48px;
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
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
        }
        .toggle-eye {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            cursor: pointer;
        }
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
        .btn-login:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 20px 40px rgba(0,217,255,0.4); }
        .field-container {
            transition: all 0.5s;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
        }
        .field-container.show {
            max-height: 300px;
            opacity: 1;
            margin-top: 20px;
        }
        .link-cyan { color: #00D9FF; text-decoration: none; }
        .link-cyan:hover { color: #fff; }
        .link-gray { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 13px; }
        .link-gray:hover { color: #00D9FF; }
        .feedback-text { font-size: 13px; margin-top: 6px; min-height: 24px; }
        .shake { animation: shake 0.4s ease; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-8px)} 75%{transform:translateX(8px)} }
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
    </style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12" style="background: linear-gradient(135deg, #050A30 0%, #0B1F6B 40%, #1A3A8A 70%, #00D9FF 100%);">
    <div class="w-full max-w-md">
        <div class="glass-card">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-cyan to-blue-600 rounded-full flex items-center justify-center text-white text-3xl shadow-lg mb-4">
                    <i class="fas fa-code"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Login Manual</h2>
                <p class="text-sm text-white/50 mt-1">Masuk menggunakan NIM & password</p>
            </div>

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-5 text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.manual') }}" method="POST" id="loginForm" novalidate>
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-1.5">Nomor Induk Mahasiswa (NIM)</label>
                    <div class="relative">
                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="nim" id="nim" required placeholder="Masukkan NIM"
                               class="input-field" autocomplete="off" inputmode="numeric"
                               oninput="this.value = this.value.replace(/\D/g, '')">
                        <span id="nimStatus" class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1">
                            <i class="fas fa-spinner hidden text-cyan-400" id="nimSpinner"></i>
                            <i class="fas fa-check-circle hidden text-emerald-400" id="nimCheck"></i>
                            <i class="fas fa-times-circle hidden text-red-400" id="nimCross"></i>
                        </span>
                    </div>
                    <div id="nimFeedback" class="feedback-text text-white/60"></div>
                </div>

                <div class="field-container" id="passwordContainer">
                    <label class="block text-sm font-medium text-white/70 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" placeholder="Masukkan password"
                               class="input-field" autocomplete="off" disabled>
                        <span class="toggle-eye" onclick="togglePassword(this)"><i class="fas fa-eye"></i></span>
                    </div>
                </div>

                <div class="field-container" id="pinContainer">
                    <label class="block text-sm font-medium text-white/70 mb-1.5">PIN (6 digit)</label>
                    <div class="relative">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" name="pin" id="pin" placeholder="Masukkan PIN 6 digit"
                               class="input-field" autocomplete="off" disabled maxlength="6" inputmode="numeric"
                               oninput="this.value = this.value.replace(/\D/g, '')">
                        <span class="toggle-eye" onclick="togglePassword(this)"><i class="fas fa-eye"></i></span>
                    </div>
                </div>

                <button type="submit" id="loginBtn" class="btn-login mt-6" disabled>
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="mt-6 text-center space-y-2 text-sm">
                <p>Belum punya akun? <a href="{{ route('guest.register') }}" class="link-cyan">Daftar di sini</a></p>
                <p><a href="{{ route('password.request') }}" class="link-gray"><i class="fas fa-key mr-1"></i> Lupa password?</a></p>
                <p><a href="{{ route('login') }}" class="link-gray"><i class="fas fa-arrow-left mr-1"></i> Kembali ke pilihan login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true });

    function togglePassword(el) {
        const input = el.parentElement.querySelector('input');
        const icon = el.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    const nimInput = document.getElementById('nim');
    const passwordInput = document.getElementById('password');
    const pinInput = document.getElementById('pin');
    const loginBtn = document.getElementById('loginBtn');
    const passwordContainer = document.getElementById('passwordContainer');
    const pinContainer = document.getElementById('pinContainer');
    const nimFeedback = document.getElementById('nimFeedback');
    const nimSpinner = document.getElementById('nimSpinner');
    const nimCheck = document.getElementById('nimCheck');
    const nimCross = document.getElementById('nimCross');
    const loginForm = document.getElementById('loginForm');

    let nimValid = false;
    let needPin = false;
    let debounceTimer = null;

    function resetFields() {
        nimValid = false;
        needPin = false;
        passwordContainer.classList.remove('show');
        pinContainer.classList.remove('show');
        passwordInput.disabled = true;
        pinInput.disabled = true;
        passwordInput.value = '';
        pinInput.value = '';
        nimFeedback.textContent = '';
        nimFeedback.className = 'feedback-text text-white/60';
        nimSpinner.classList.add('hidden');
        nimCheck.classList.add('hidden');
        nimCross.classList.add('hidden');
        updateLoginBtn();
    }

    function updateLoginBtn() {
        const isPasswordReady = passwordInput.value.trim() !== '' && !passwordInput.disabled;
        const isPinReady = pinInput.value.trim() !== '' || !needPin || pinInput.disabled;
        const allFilled = nimValid && isPasswordReady && isPinReady;

        loginBtn.disabled = !allFilled;
        if (allFilled) {
            loginBtn.style.background = 'linear-gradient(135deg, #34d399, #059669)';
            loginBtn.style.boxShadow = '0 10px 30px rgba(52, 211, 153, 0.3)';
        } else {
            loginBtn.style.background = 'linear-gradient(135deg, #00D9FF, #2563EB)';
            loginBtn.style.boxShadow = '0 10px 30px rgba(0, 217, 255, 0.25)';
        }
    }

    nimInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const nim = this.value.trim();

        if (nim.length < 3) {
            resetFields();
            return;
        }

        nimSpinner.classList.remove('hidden');
        nimCheck.classList.add('hidden');
        nimCross.classList.add('hidden');
        nimFeedback.textContent = 'Memeriksa NIM...';
        nimFeedback.className = 'feedback-text text-cyan-300';

        debounceTimer = setTimeout(() => {
            fetch('{{ route('login.check-nim-json') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nim: nim })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 50)}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                nimSpinner.classList.add('hidden');
                if (data.found) {
                    nimValid = true;
                    nimCheck.classList.remove('hidden');
                    nimCross.classList.add('hidden');
                    nimFeedback.innerHTML = `<span class="text-emerald-300">✅ Ditemukan: ${data.name} (${data.role})</span>`;
                    nimFeedback.className = 'feedback-text text-emerald-300';

                    passwordContainer.classList.add('show');
                    passwordInput.disabled = false;
                    setTimeout(() => passwordInput.focus(), 350);

                    needPin = data.needPin || false;
                    if (needPin) {
                        pinContainer.classList.add('show');
                        pinInput.disabled = false;
                    } else {
                        pinContainer.classList.remove('show');
                        pinInput.disabled = true;
                        pinInput.value = '';
                    }
                    updateLoginBtn();
                } else {
                    nimValid = false;
                    nimCheck.classList.add('hidden');
                    nimCross.classList.remove('hidden');
                    nimFeedback.innerHTML = `<span class="text-red-300">❌ NIM tidak terdaftar</span>`;
                    nimFeedback.className = 'feedback-text text-red-300';
                    passwordContainer.classList.remove('show');
                    pinContainer.classList.remove('show');
                    passwordInput.disabled = true;
                    pinInput.disabled = true;
                    passwordInput.value = '';
                    pinInput.value = '';
                    updateLoginBtn();
                    nimInput.classList.add('shake');
                    setTimeout(() => nimInput.classList.remove('shake'), 400);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                nimSpinner.classList.add('hidden');
                nimCross.classList.remove('hidden');
                nimCheck.classList.add('hidden');
                nimFeedback.innerHTML = `<span class="text-red-300">⚠️ Gagal memeriksa NIM. ${error.message}</span>`;
                nimFeedback.className = 'feedback-text text-red-300';
                nimValid = false;
                passwordContainer.classList.remove('show');
                pinContainer.classList.remove('show');
                passwordInput.disabled = true;
                pinInput.disabled = true;
                passwordInput.value = '';
                pinInput.value = '';
                updateLoginBtn();
            });
        }, 400);
    });

    passwordInput.addEventListener('input', updateLoginBtn);
    pinInput.addEventListener('input', updateLoginBtn);

    loginForm.addEventListener('submit', function(e) {
        if (loginBtn.disabled) {
            e.preventDefault();
            return;
        }
        loginBtn.innerHTML = '<span class="spinner"></span> Memproses...';
        loginBtn.disabled = true;
    });

    loginForm.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && loginBtn.disabled) e.preventDefault();
    });
</script>
@endpush
