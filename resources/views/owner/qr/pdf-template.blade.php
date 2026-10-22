<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Credential - UNICODE</title>
    <style>
        /* ========== GLOBAL ========== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .page {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            page-break-after: always;
        }
        /* ========== HEADER ========== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0ea5e9;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header .date {
            font-size: 14px;
            color: #64748b;
            text-align: right;
        }
        .header .logo {
            font-size: 20px;
            font-weight: bold;
            color: #0ea5e9;
        }
        /* ========== PROFIL ========== */
        .profile {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            color: #0ea5e9;
            overflow: hidden;
            border: 3px solid #0ea5e9;
        }
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .info {
            flex: 1;
        }
        .info h2 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .info .detail {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
        }
        .info .detail span {
            font-weight: 600;
            color: #0f172a;
        }
        /* ========== CARD QR ========== */
        .qr-card {
            background: #ffffff;
            border: 2px dashed #0ea5e9;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .qr-card img {
            max-width: 200px;
            height: auto;
            margin: 0 auto;
        }
        .qr-card .qr-label {
            margin-top: 10px;
            font-size: 12px;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        /* ========== STATUS ========== */
        .status-badge {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge.inactive { background: #ef4444; }
        /* ========== FOOTER ========== */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        /* ========== HALAMAN 2 (opsional) ========== */
        .page-2 {
            background: #f8fafc;
            padding: 30px;
        }
        .page-2 h3 {
            color: #0ea5e9;
            margin-bottom: 15px;
        }
        .page-2 ul {
            list-style: none;
            padding: 0;
        }
        .page-2 ul li {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .page-2 ul li strong {
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- ======================== HALAMAN 1 ======================== -->
    <div class="page">
        <!-- HEADER: Tanggal di pojok kanan -->
        <div class="header">
            <div class="logo">UNICODE</div>
            <div class="date">
                <strong>Tanggal Cetak:</strong><br>
                {{ $generated_at ?? now()->format('d F Y H:i') }}
            </div>
        </div>

        <!-- PROFIL + INFORMASI -->
        <div class="profile">
            <div class="avatar">
                @if($user->avatar_url)
                    <img src="{{ public_path($user->avatar_url) }}" alt="Avatar">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="info">
                <h2>{{ $user->name }}</h2>
                <div class="detail">
                    <span>NIM:</span> {{ $user->nim ?? '-' }}<br>
                    <span>UUID:</span> {{ $qr->qr_uuid ?? $user->user_uuid ?? '-' }}<br>
                    <span>Role:</span> {{ $role ?? $user->level->name ?? 'Anggota' }} &nbsp;
                    <span class="status-badge {{ $user->is_active ? '' : 'inactive' }}">
                        {{ $user->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                    </span><br>
                    <span>Member Number:</span> {{ $user->member_number ?? '-' }}
                </div>
            </div>
        </div>

        <!-- QR CARD -->
        <div class="qr-card">
            @if($qrImageData)
                <img src="{{ $qrImageData }}" alt="QR Code">
            @else
                <p style="color: #ef4444;">QR tidak tersedia</p>
            @endif
            <div class="qr-label">SCAN QR UNTUK VERIFIKASI</div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Token: {{ substr($qr->token ?? '', 0, 20) }}...
            </div>
        </div>

        <div class="footer">
            Dicetak dari Sistem UNICODE UKM Pemrograman
        </div>
    </div>

    <!-- ======================== HALAMAN 2 ======================== -->
    <div class="page page-2">
        <h3>Informasi Tambahan</h3>
        <ul>
            <li><strong>QR UUID:</strong> {{ $qr->qr_uuid ?? '-' }}</li>
            <li><strong>Dibuat:</strong> {{ $qr->generated_at ? $qr->generated_at->format('d/m/Y H:i') : '-' }}</li>
            <li><strong>Kadaluarsa:</strong> {{ $qr->expired_at ? $qr->expired_at->format('d/m/Y H:i') : 'Tidak terbatas' }}</li>
            <li><strong>Jumlah Scan:</strong> {{ $qr->scan_count ?? 0 }}</li>
            <li><strong>Status:</strong> {{ $qr->is_revoked ? 'Dicabut' : ($qr->is_active ? 'Aktif' : 'Tidak Aktif') }}</li>
        </ul>
        <div style="margin-top: 20px; font-size: 12px; color: #94a3b8;">
            <i>QR ini hanya dapat digunakan selama status aktif dan belum dicabut.</i>
        </div>
        <div class="footer" style="margin-top: 40px;">
            Halaman 2 dari 2
        </div>
    </div>

</body>
</html>