# test-callback.ps1

# 1. Tentukan URL Ngrok kamu (pastikan ini URL yang sedang aktif)
$url = "https://wanted-wreath-justly.ngrok-free.dev/api/midtrans/callback"

# 2. Buat body request (data palsu untuk tes)
$body = @{
    order_id      = "TEST-123"
    status_code   = "200"
    gross_amount  = "10000.00"
    signature_key = "dummy" # Sengaja salah, untuk tes respon
} | ConvertTo-Json

# 3. Kirim request dan tampilkan hasilnya
try {
    $response = Invoke-RestMethod -Uri $url -Method Post -Body $body -ContentType "application/json"
    Write-Host "✅ Berhasil! Response: $($response | ConvertTo-Json -Compress)" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Terjadi error (Ini NORMAL jika signature dummy):" -ForegroundColor Yellow
    Write-Host $_.ErrorDetails.Message
}