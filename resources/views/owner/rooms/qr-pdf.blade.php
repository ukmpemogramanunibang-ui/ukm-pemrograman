<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code - {{ $room->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
            text-align: center;
        }
        .qr-container {
            margin-top: 50px;
        }
        .qr-container img {
            width: 300px;
            height: 300px;
        }
        .info {
            margin-top: 30px;
            font-size: 16px;
        }
        .info h2 {
            margin-bottom: 5px;
        }
        .info p {
            margin: 3px 0;
            color: #555;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <img src="{{ $qrBase64 }}" alt="QR Code">
    </div>
    <div class="info">
        <h2>{{ $room->name }}</h2>
        <p>Kode: {{ $room->room_code }}</p>
        <p>Gedung: {{ $room->building ?? '-' }} | Lantai: {{ $room->floor ?? '-' }}</p>
    </div>
    <div class="footer">
        Dicetak dari sistem manajemen ruangan
    </div>
</body>
</html>