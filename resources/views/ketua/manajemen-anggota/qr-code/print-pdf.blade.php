<!DOCTYPE html>
<html>
<head>
    <title>QR Code Anggota</title>
    <style>
        body { font-family: sans-serif; }
        .page { page-break-after: always; }
        .qr-card { text-align: center; padding: 20px; }
        .qr-card img { width: 200px; height: 200px; }
        h3 { margin-top: 10px; }
    </style>
</head>
<body>
    @foreach ($users as $index => $user)
        <div class="page">
            <div class="qr-card">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($files[$index])) }}">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->nim }}</p>
            </div>
        </div>
    @endforeach
</body>
</html>