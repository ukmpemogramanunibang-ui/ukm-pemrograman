<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $minute->title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; padding: 40px; color: #1a202c; }
        .header { text-align: center; border-bottom: 3px solid #ecc94b; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 24px; font-weight: bold; margin: 0; color: #1a202c; }
        .header h2 { font-size: 18px; font-weight: normal; margin: 5px 0 0; color: #718096; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: bold; font-size: 14px; color: #4a5568; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .section-content { font-size: 13px; line-height: 1.8; padding-left: 5px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .info-item { font-size: 13px; }
        .info-item strong { color: #4a5568; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-draft { background: #fefcbf; color: #975a16; }
        .badge-completed { background: #c6f6d5; color: #276749; }
        .badge-archived { background: #e9d8fd; color: #553c9a; }
        .footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 12px; color: #a0aec0; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NOTULEN RAPAT</h1>
        <h2>{{ $minute->title }}</h2>
        <div style="margin-top: 8px;">
            <span class="badge badge-{{ $minute->status }}">
                {{ strtoupper($minute->status_label) }}
            </span>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item"><strong>Tanggal:</strong> {{ $minute->formatted_meeting_date }}</div>
        @if($minute->meeting_time)<div class="info-item"><strong>Waktu:</strong> {{ $minute->meeting_time->format('H:i') }}</div>@endif
        @if($minute->location)<div class="info-item"><strong>Tempat:</strong> {{ $minute->location }}</div>@endif
        @if($minute->leader_name)<div class="info-item"><strong>Pimpinan:</strong> {{ $minute->leader_name }}</div>@endif
        <div class="info-item"><strong>Notulis:</strong> {{ $minute->notulis->name ?? '-' }}</div>
        <div class="info-item"><strong>Dibuat:</strong> {{ $minute->created_at->format('d F Y H:i') }}</div>
    </div>

    @if($minute->participants)
        <div class="section">
            <div class="section-title">Peserta Rapat</div>
            <div class="section-content">{{ $minute->participants }}</div>
        </div>
    @endif

    @if($minute->agenda)
        <div class="section">
            <div class="section-title">Agenda Rapat</div>
            <div class="section-content">{{ $minute->agenda }}</div>
        </div>
    @endif

    @if($minute->discussion)
        <div class="section">
            <div class="section-title">Pembahasan</div>
            <div class="section-content">{{ $minute->discussion }}</div>
        </div>
    @endif

    @if($minute->decisions)
        <div class="section">
            <div class="section-title">Keputusan Rapat</div>
            <div class="section-content">{{ $minute->decisions }}</div>
        </div>
    @endif

    @if($minute->follow_up)
        <div class="section">
            <div class="section-title">Tindak Lanjut</div>
            <div class="section-content">{{ $minute->follow_up }}</div>
        </div>
    @endif

    <div class="footer">
        Dokumen ini dibuat secara otomatis dari sistem UNICODE • {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>