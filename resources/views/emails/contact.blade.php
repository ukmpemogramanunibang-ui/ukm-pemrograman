<!DOCTYPE html>
<html>
<head>
    <title>Pesan Baru dari Form Kontak</title>
</head>
<body>
    <h2>Pesan Baru dari Form Kontak</h2>
    <p><strong>Nama:</strong> {{ $contact->name }}</p>
    <p><strong>Email:</strong> {{ $contact->email }}</p>
    <p><strong>Subjek:</strong> {{ $contact->subject }}</p>
    <p><strong>Pesan:</strong></p>
    <p>{{ $contact->message }}</p>
    <hr>
    <small>Dikirim dari website UKM Pemrograman UNICODE</small>
</body>
</html>