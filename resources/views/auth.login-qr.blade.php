@extends('layouts.app')
@section('content')
<div class="container mx-auto py-8" x-data="qrScanner()">
    <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
        <h2 class="text-2xl font-bold mb-4">Scan QR Code</h2>
        <div id="reader" class="w-full"></div>
        <div class="flex justify-between mt-4">
            <button @click="switchCamera" class="bg-gray-200 dark:bg-gray-700 px-4 py-2 rounded">Kamera Depan/Belakang</button>
            <button @click="rescan" class="bg-indigo-500 text-white px-4 py-2 rounded">Scan Ulang</button>
        </div>
        <p x-show="error" class="text-red-500 mt-2" x-text="error"></p>
        <!-- Setelah berhasil scan, tampilkan data user dan form password -->
        <div x-show="userData" class="mt-4">
            <p><strong>Nama:</strong> <span x-text="userData.name"></span></p>
            <p><strong>NIM:</strong> <span x-text="userData.nim"></span></p>
            <p><strong>Jabatan:</strong> <span x-text="userData.jabatan"></span></p>
            <form action="/login/manual" method="POST">
                @csrf
                <input type="hidden" name="user_id" x-model="userData.user_id">
                <input type="password" name="password" placeholder="Password" class="w-full mt-2 border rounded p-2">
                <template x-if="userData.level == 0 || userData.level == 1">
                    <input type="password" name="pin" placeholder="PIN" class="w-full mt-2 border rounded p-2">
                </template>
                <button type="submit" class="mt-3 bg-green-500 text-white py-2 px-4 rounded">Login</button>
            </form>
        </div>
    </div>
</div>
<script>
    function qrScanner() {
        return {
            html5QrCode: null,
            userData: null,
            error: '',
            cameraId: null,
            init() {
                this.html5QrCode = new Html5Qrcode("reader");
                this.startScan();
            },
            startScan() {
                const config = { fps: 10, qrbox: 250 };
                this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        // Kirim token ke server
                        axios.post('/login/qr-scan', { qr_token: decodedText })
                            .then(res => {
                                this.userData = res.data;
                                this.html5QrCode.stop();
                                // Beep sound
                                new Howl({ src: ['/sounds/beep.mp3'] }).play();
                            })
                            .catch(err => {
                                this.error = 'QR tidak valid atau akun bermasalah';
                            });
                    },
                    (errorMessage) => {}
                ).catch(err => this.error = 'Kamera tidak dapat diakses');
            },
            switchCamera() {
                // logika ganti kamera
            },
            rescan() {
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => {
                        this.userData = null;
                        this.error = '';
                        this.startScan();
                    });
                }
            }
        }
    }
</script>
<script>
    // ============================================
// GLOBAL OFFLINE DETECTION
// ============================================
(function() {
    // Simpan halaman saat ini
    const currentPath = window.location.pathname;
    if (currentPath !== '/offline') {
        sessionStorage.setItem('previousPage', currentPath);
    }

    // Fungsi redirect ke offline
    function redirectToOffline() {
        if (window.location.pathname !== '/offline') {
            window.location.href = '/offline';
        }
    }

    // Deteksi offline
    window.addEventListener('offline', function() {
        // Beri delay agar tidak langsung redirect (bisa jadi false alarm)
        setTimeout(() => {
            if (!navigator.onLine) {
                redirectToOffline();
            }
        }, 300);
    });

    // Saat online, redirect balik jika di halaman offline
    window.addEventListener('online', function() {
        if (window.location.pathname === '/offline') {
            const prev = sessionStorage.getItem('previousPage') || '/';
            window.location.href = prev;
        }
    });

    // Cek awal
    if (!navigator.onLine && window.location.pathname !== '/offline') {
        redirectToOffline();
    }

    console.log('🔌 Offline detection aktif');
})();
</script>
@endsection