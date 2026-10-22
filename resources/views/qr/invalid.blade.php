<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Tidak Valid – UNICODE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden text-center">
        <div class="bg-red-500 p-4 text-white">
            <i class="fas fa-exclamation-triangle text-4xl"></i>
            <h1 class="text-2xl font-bold">QR Tidak Valid</h1>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-300">{{ $message ?? 'QR Code ini tidak dikenali.' }}</p>
            <a href="{{ route('landing') }}" class="mt-4 inline-block px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>