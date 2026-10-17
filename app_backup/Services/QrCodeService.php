<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQrCode;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QrCodeService
{
    private const ROOM_QR_PATH = 'qr_codes/rooms';
    private const USER_QR_PATH = 'qr_codes/users';
    private const QR_SECRET_KEY = 'unicode_qr_secret'; // bisa dari config

    public function __construct()
    {
        $this->ensureDirectoryExists(self::ROOM_QR_PATH);
        $this->ensureDirectoryExists(self::USER_QR_PATH);
    }

    private function ensureDirectoryExists(string $path): void
    {
        $fullPath = Storage::disk('public')->path($path);
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }
/**
 * Rotate QR untuk user.
 *
 * - Revoke QR aktif lama (tetap tersimpan di DB sebagai history)
 * - Buat QR baru dengan token & UUID baru
 * - Link QR lama → QR baru
 *
 * @return UserQrCode QR baru yang aktif
 */
public function rotateForUser(\App\Models\User $user, array $options = []): \App\Models\UserQrCode
{
    $reason    = $options['reason'] ?? 'rotated';
    $revokedBy = $options['revoked_by'] ?? null;

    // 1. Cari QR aktif lama
    $oldQr = \App\Models\UserQrCode::where('user_id', $user->id)
        ->where('is_active', true)
        ->where('is_revoked', false)
        ->latest('id')
        ->first();

    $version    = 1;
    $replacesId = null;

    if ($oldQr) {
        $version    = ((int) ($oldQr->version ?? 1)) + 1;
        $replacesId = $oldQr->id;

        // 2. Revoke QR lama (tetap ada di DB!)
        $oldQr->update([
            'is_active'      => false,
            'is_revoked'     => true,
            'revoked_at'     => now(),
            'revoked_by'     => $revokedBy,
            'reason_revoked' => $reason,
        ]);
    }

    // 3. Buat QR baru
    $newQr = \App\Models\UserQrCode::create([
        'user_id'      => $user->id,
        'qr_uuid'      => (string) \Illuminate\Support\Str::uuid(),
        'token'        => \Illuminate\Support\Str::random(120),
        'version'      => $version,
        'replaces_id'  => $replacesId,
        'generated_at' => now(),
        'is_active'    => true,
        'is_revoked'   => false,
    ]);

    // 4. Update QR lama → tunjuk QR baru
    if ($oldQr) {
        $oldQr->update(['replaced_by_id' => $newQr->id]);
    }

    \Log::info('QR rotated', [
        'user_id'     => $user->id,
        'old_qr_id'   => $oldQr?->id,
        'new_qr_id'   => $newQr->id,
        'new_uuid'    => $newQr->qr_uuid,
        'version'     => $version,
        'reason'      => $reason,
        'by'          => $revokedBy,
    ]);

    return $newQr;
}
    // ============================================================
    //  GENERATE QR UNTUK USER
    // ============================================================
    public function generateForUser(User $user, array $options = []): ?UserQrCode
    {
        $token = $options['token'] ?? (string) Str::uuid();
        $expiredAt = $options['expired_at'] ?? null;
        $replace = $options['replace'] ?? false;
        $format = $options['format'] ?? 'png';

        if (!$replace && $user->qrCodes()->where('is_active', true)->exists()) {
            Log::info('User already has active QR, skipped', ['user_id' => $user->id]);
            return null;
        }

        if ($replace) {
            $oldQrs = $user->qrCodes()->where('is_active', true)->get();
            foreach ($oldQrs as $oldQr) {
                if ($oldQr->qr_image && Storage::disk('public')->exists($oldQr->qr_image)) {
                    Storage::disk('public')->delete($oldQr->qr_image);
                }
                $oldQr->update(['is_active' => false]);
            }
        }

        $qr = $user->qrCodes()->create([
            'qr_uuid'      => (string) Str::uuid(),
            'token'        => $token,
            'expired_at'   => $expiredAt,
            'is_active'    => true,
            'is_revoked'   => false,
            'generated_at' => now(),
            'created_by'   => auth()->id(),
        ]);

        // Generate gambar dan lakukan self-test
        $path = $this->generateAndSaveQrImage($qr, $format);
        if (!$path) {
            // Jika gagal, hapus record QR
            $qr->delete();
            Log::error('QR generation failed for user', ['user_id' => $user->id]);
            return null;
        }

        // Self-test: decode ulang QR yang baru dibuat
        $selfTest = $this->selfTestQr($qr);
        if (!$selfTest['valid']) {
            // Jika self-test gagal, hapus QR dan file
            $this->deleteQrImage($qr);
            $qr->delete();
            Log::error('QR self-test failed', ['qr_uuid' => $qr->qr_uuid, 'error' => $selfTest['message']]);
            return null;
        }

        return $qr;
    }
/**
 * Cek apakah QR code valid (file exists dan tidak kadaluarsa)
 */
/**
 * Cek apakah QR code valid (file exists dan tidak kadaluarsa)
 */
public function isQrValid($qrCode): bool
{
    // Jika QR code adalah string (path file)
    if (is_string($qrCode)) {
        return Storage::disk('public')->exists($qrCode);
    }

    // Jika QR code adalah model Room, UserQrCode, atau objek lain
    if (is_object($qrCode)) {
        // Cek apakah ada properti 'qr_code' atau 'qr_path'
        $path = $qrCode->qr_code ?? $qrCode->qr_path ?? null;
        if ($path) {
            return Storage::disk('public')->exists($path);
        }

        // Jika model punya method is_active (untuk UserQrCode)
        if (method_exists($qrCode, 'is_active') && !$qrCode->is_active) {
            return false;
        }
    }

    // Jika QR code adalah array dengan key 'qr_code' atau 'qr_path'
    if (is_array($qrCode)) {
        $path = $qrCode['qr_code'] ?? $qrCode['qr_path'] ?? null;
        if ($path) {
            return Storage::disk('public')->exists($path);
        }
    }

    return false;
}
    // ============================================================
    //  REPAIR QR (MEMPERBAIKI GAMBAR, TANPA MENGGANTI CREDENTIAL)
    // ============================================================
    public function repairQr(UserQrCode $qr, string $format = 'png'): ?UserQrCode
    {
        if ($qr->is_revoked || !$qr->is_active) {
            Log::warning('Cannot repair revoked/inactive QR', ['qr_uuid' => $qr->qr_uuid]);
            return null;
        }

        // Backup image lama jika ada
        $oldImage = $qr->qr_image;

        // Generate gambar baru
        $path = $this->generateAndSaveQrImage($qr, $format);
        if (!$path) {
            return null;
        }

        // Self-test
        $selfTest = $this->selfTestQr($qr);
        if (!$selfTest['valid']) {
            // Rollback: hapus gambar baru, kembalikan gambar lama
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $qr->qr_image = $oldImage;
            $qr->save();
            Log::error('QR repair self-test failed', ['qr_uuid' => $qr->qr_uuid]);
            return null;
        }

        // Hapus gambar lama (jika berbeda)
        if ($oldImage && $oldImage !== $path && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        $qr->updated_at = now();
        $qr->save();

        Log::info('QR repaired successfully', ['qr_uuid' => $qr->qr_uuid, 'user_id' => $qr->user_id]);

        return $qr->fresh();
    }

    // ============================================================
    //  BUILD PAYLOAD UKMQR DENGAN SIGNATURE HMAC
    // ============================================================
    private function buildPayload(UserQrCode $qr): string
    {
        $version = 2;
        $issuer = 'UNICODE';
        $data = implode('|', [
            $issuer,
            $version,
            $qr->qr_uuid,
            $qr->token,
            $qr->user_id,
        ]);
        $secret = config('app.key'); // atau gunakan config khusus
        $signature = hash_hmac('sha256', $data, $secret);

        return implode('|', [
            'UKMQR',
            'version=' . $version,
            'issuer=' . $issuer,
            'qr_uuid=' . $qr->qr_uuid,
            'token=' . $qr->token,
            'user_id=' . $qr->user_id,
            'signature=' . $signature,
        ]);
    }

    // ============================================================
    //  GENERATE & SAVE GAMBAR QR
    // ============================================================
    public function generateAndSaveQrImage(UserQrCode $qr, string $format = 'png'): ?string
{
    $dir = self::USER_QR_PATH;
    $this->ensureDirectoryExists($dir);

    $user = $qr->user;

    // Sanitize nama file — hanya huruf, angka, underscore
    $safeName = $user
        ? preg_replace('/[^a-z0-9]+/', '_', strtolower($user->name))
        : 'unknown';
    $safeName = trim($safeName, '_') ?: 'unknown';

    // Pakai UUID sebagai identifier utama (anti-tabrakan)
    $filename = "qr_{$safeName}_{$qr->qr_uuid}.{$format}";
    $path = $dir . '/' . $filename;

    $payload = $this->buildPayload($qr);

    try {
        $imageData = $this->generateQrWithChillerlan($payload, $format);
        if ($imageData) {
            Storage::disk('public')->put($path, $imageData);
            $qr->qr_image = $path;
            $qr->save();
            return $path;
        }
    } catch (Exception $e) {
        Log::warning('Chillerlan QR generation failed', [
            'error' => $e->getMessage(),
            'qr_uuid' => $qr->qr_uuid,
        ]);
    }

    return $this->generateWithApiFallback($qr, $format, $payload);
}

    private function generateWithApiFallback(UserQrCode $qr, string $format, ?string $payload = null): ?string
    {
        $data = $payload ?? $this->buildPayload($qr);
        $url = 'https://api.qrserver.com/v1/create-qr-code/';
        $query = http_build_query([
            'size'   => '300x300',
            'data'   => $data,
            'format' => $format,
            'margin' => 10,
        ]);

        try {
            $response = Http::timeout(10)->get($url . '?' . $query);
            if ($response->successful()) {
                $dir = self::USER_QR_PATH;
                $this->ensureDirectoryExists($dir);
                $user = $qr->user;
                $username = $user ? Str::slug($user->name, '_') : 'unknown';
                $filename = "ukm_programming_{$username}_{$qr->qr_uuid}.{$format}";
                $path = $dir . '/' . $filename;
                Storage::disk('public')->put($path, $response->body());
                $qr->qr_image = $path;
                $qr->save();
                return $path;
            }
        } catch (Exception $e) {
            Log::error('API QR fallback failed: ' . $e->getMessage());
        }
        return null;
    }

    // ============================================================
    //  SELF-TEST: DECODE ULANG QR UNTUK MEMASTIKAN VALID
    // ============================================================
    public function selfTestQr(UserQrCode $qr): array
    {
        $path = $qr->qr_image;
        if (!$path || !Storage::disk('public')->exists($path)) {
            return ['valid' => false, 'message' => 'File QR tidak ditemukan.'];
        }

        $fullPath = Storage::disk('public')->path($path);
        try {
            $response = Http::attach('file', file_get_contents($fullPath), 'qr.png')
                ->post('https://api.qrserver.com/v1/read-qr-code/');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data[0]['symbol'][0]['data'])) {
                    $decoded = $data[0]['symbol'][0]['data'];
                    // Cek apakah decoded mengandung qr_uuid yang sama
                    if (strpos($decoded, $qr->qr_uuid) !== false) {
                        return ['valid' => true, 'message' => 'Self-test passed.'];
                    }
                    return ['valid' => false, 'message' => 'QR decoded but UUID mismatch.'];
                }
                return ['valid' => false, 'message' => 'No QR data found in image.'];
            }
            return ['valid' => false, 'message' => 'API decode failed.'];
        } catch (Exception $e) {
            Log::error('Self-test error: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'Exception during decode.'];
        }
    }

  public function downloadResponse(UserQrCode $qr, string $format): ?\Symfony\Component\HttpFoundation\BinaryFileResponse
{
    $format = strtolower($format);

    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
    if (!in_array($format, $allowed)) {
        Log::warning('[QR] Invalid format', ['format' => $format]);
        return null;
    }

    if (empty($qr->token)) {
        Log::error('[QR] Empty token', ['qr_uuid' => $qr->qr_uuid]);
        return null;
    }

    // ============ TENTUKAN PATH ============
    $targetPath = null;

    // Pakai existing kalau ekstensinya cocok
    if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
        $existingExt = strtolower(pathinfo($qr->qr_image, PATHINFO_EXTENSION));
        if ($existingExt === $format) {
            $targetPath = $qr->qr_image;
        }
    }

    // Generate kalau belum ada / format beda
    if (!$targetPath) {
        $targetPath = $this->generateAndSaveQrImage($qr, $format);
    }

    if (!$targetPath || !Storage::disk('public')->exists($targetPath)) {
        Log::error('[QR] File missing', [
            'qr_uuid' => $qr->qr_uuid,
            'format'  => $format,
            'path'    => $targetPath,
        ]);
        return null;
    }

    // ============ FULL PATH & VERIFY ============
    $fullPath = Storage::disk('public')->path($targetPath);

    if (!file_exists($fullPath) || filesize($fullPath) === 0) {
        Log::error('[QR] File invalid/empty', [
            'qr_uuid'  => $qr->qr_uuid,
            'fullPath' => $fullPath,
            'size'     => file_exists($fullPath) ? filesize($fullPath) : -1,
        ]);
        return null;
    }

    // ============ NAMA FILE DOWNLOAD ============
    $nim = $qr->user->nim ?? $qr->user->id;
    $nim = preg_replace('/[^A-Za-z0-9\-_]/', '', (string) $nim) ?: 'user';
    $ver = (int) ($qr->version ?? 1);

    $downloadName = "QR_{$nim}_v{$ver}.{$format}";

    // ============ MIME ============
    $mime = match ($format) {
        'png'         => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'webp'        => 'image/webp',
        'svg'         => 'image/svg+xml',
        default       => 'application/octet-stream',
    };

    Log::info('[QR] Download OK', [
        'qr_uuid'  => $qr->qr_uuid,
        'format'   => $format,
        'file'     => $targetPath,
        'size'     => filesize($fullPath),
        'filename' => $downloadName,
    ]);

    // ============ RETURN BINARY RESPONSE ============
    return response()->download($fullPath, $downloadName, [
        'Content-Type' => $mime,
        'Content-Length' => filesize($fullPath),
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'X-Content-Type-Options' => 'nosniff',
    ]);
}

    // ============================================================
    //  GENERATE QR UNTUK ROOM
    // ============================================================
    public function generateForRoom(Room $room, array $options = []): ?string
    {
        // ... (sama seperti sebelumnya, tidak diubah)
        if ($room->has_qr && is_null($room->qr_code)) {
            $room->update(['has_qr' => false, 'qr_code' => null]);
            $room->refresh();
        }

        $token = $options['token'] ?? $room->room_token ?? Str::random(64);
        $room->update(['room_token' => $token]);

        $url = $options['url'] ?? url('/room_publik?room_token=' . urlencode($token));
        $filename = self::ROOM_QR_PATH . '/room_' . $room->id . '.png';
        $fullPath = Storage::disk('public')->path($filename);

        try {
            if (class_exists(QRCode::class)) {
                $imageData = $this->generateQrWithChillerlan($url, 'png');
                if ($imageData) {
                    Storage::disk('public')->put($filename, $imageData);
                    $room->update(['has_qr' => true, 'qr_code' => $filename]);
                    Log::info('Room QR generated', ['room_id' => $room->id]);
                    return $filename;
                }
            }
        } catch (Exception $e) {
            Log::warning('chillerlan failed for room, using fallback', ['error' => $e->getMessage()]);
        }

        try {
            $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
            $query = http_build_query([
                'size' => '300x300',
                'data' => $url,
                'format' => 'png',
                'margin' => 10,
            ]);
            $response = Http::timeout(10)->get($apiUrl . '?' . $query);
            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
                $room->update(['has_qr' => true, 'qr_code' => $filename]);
                Log::info('Room QR generated via API', ['room_id' => $room->id]);
                return $filename;
            }
        } catch (Exception $e) {
            Log::error('Room QR API failed: ' . $e->getMessage());
        }

        Log::error('All QR generation methods failed for room', ['room_id' => $room->id]);
        return null;
    }

    // ============================================================
    //  UTILITY
    // ============================================================
    private function generateQrWithChillerlan(string $data, string $format): ?string
    {
        $options = new QROptions([
            'version'    => 6,
            'outputType' => $this->getChillerlanOutputType($format),
            'eccLevel'   => 'M',
            'scale'      => 10,
            'bgColor'    => '#ffffff',
            'fgColor'    => '#000000',
        ]);

        $qrCode = new QRCode($options);
        $result = $qrCode->render($data);

        if ($format === 'png') {
            if (strpos($result, 'data:image/') === 0) {
                $result = substr($result, strpos($result, ',') + 1);
            }
            $result = base64_decode($result);
            if ($result === false) {
                throw new Exception('Base64 decode failed');
            }
        }
        return $result;
    }

    private function getChillerlanOutputType(string $format): string
    {
        return match ($format) {
            'png'   => 'png',
            'jpg', 'jpeg' => 'jpg',
            'webp'  => 'webp',
            'svg'   => 'svg',
            default => 'png',
        };
    }

    public function deleteQrImage(UserQrCode $qr): bool
    {
        if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
            return Storage::disk('public')->delete($qr->qr_image);
        }
        return true;
    }

    public function revokeExistingQr($user, ?string $reason = 'Replaced by new QR'): int
    {
        if ($user instanceof User) {
            return $user->qrCodes()
                ->where('is_active', true)
                ->update([
                    'is_active'   => false,
                    'is_revoked'  => true,
                    'revoked_at'  => now(),
                    'revoked_by'  => auth()->id(),
                    'notes'       => $reason,
                ]);
        }
        return 0;
    }

    public function validateToken(string $token): ?UserQrCode
    {
        return UserQrCode::where('token', $token)
            ->where('is_active', true)
            ->where('is_revoked', false)
            ->first();
    }

    public function getStats(?int $userId = null): array
    {
        $query = UserQrCode::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return [
            'total'   => $query->count(),
            'active'  => (clone $query)->where('is_active', true)->count(),
            'revoked' => (clone $query)->where('is_revoked', true)->count(),
        ];
    }
}