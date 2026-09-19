<?php

namespace App\Http\Controllers\Owner\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQrCode;
use App\Services\QrValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TestingController extends Controller
{
    protected $qrValidationService;

    public function __construct(QrValidationService $qrValidationService)
    {
        $this->qrValidationService = $qrValidationService;
    }
public function uploadQr(Request $request)
{
    $request->validate([
        'files' => 'required|array|max:10',
        'files.*' => 'file|mimes:png,jpg,jpeg,webp,svg,pdf|max:5120',
    ]);

    $results = [];
    $files = $request->file('files');

    foreach ($files as $file) {
        $path = $file->store('temp_qr_uploads');
        $fullPath = Storage::path($path);

        // Decode QR dari file
        $decodedText = $this->decodeQrFromFile($fullPath);

        if (!$decodedText) {
            $results[] = [
                'filename' => $file->getClientOriginalName(),
                'valid' => false,
                'message' => 'QR tidak terbaca / tidak ditemukan dalam file.'
            ];
            continue;
        }

        // Validasi menggunakan logika yang sama
        $validation = $this->validateQrPayload($decodedText);
        $results[] = array_merge(['filename' => $file->getClientOriginalName()], $validation);
    }

    return response()->json(['results' => $results]);
}

/**
 * Helper untuk decode QR dari file (menggunakan API qrserver atau library lokal)
 */
protected function decodeQrFromFile($filePath)
{
    try {
        $response = Http::attach('file', file_get_contents($filePath), 'qr.png')
            ->post('https://api.qrserver.com/v1/read-qr-code/');

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data[0]['symbol'][0]['data'])) {
                $decodedText = $data[0]['symbol'][0]['data'];
                // 🔥 TAMBAHKAN LOG INI
                \Log::info('QR decode result:', ['decoded' => $decodedText]);
                return $decodedText;
            }
        }
    } catch (\Exception $e) {
        \Log::error('QR decode error: ' . $e->getMessage());
    }
    return null;
}


/**
 * Validasi payload QR (dipakai baik dari scanner maupun upload)
 */
protected function validateQrPayload($payload)
{
    $parsed = $this->parsePayload($payload);
    
    // 🔥 TAMBAHKAN LOG INI
    \Log::info('validateQrPayload - parsed:', ['parsed' => $parsed]);
    
    $credential = null;

    if ($parsed) {
        if (isset($parsed['token'])) {
            $credential = UserQrCode::where('token', $parsed['token'])->first();
            \Log::info('Search by token:', ['token' => $parsed['token'], 'found' => $credential ? true : false]);
        }
        if (!$credential && isset($parsed['qr_uuid'])) {
            $credential = UserQrCode::where('qr_uuid', $parsed['qr_uuid'])->first();
            \Log::info('Search by qr_uuid:', ['qr_uuid' => $parsed['qr_uuid'], 'found' => $credential ? true : false]);
        }
    } else {
        $uuid = $this->extractUuid($payload);
        if ($uuid) {
            $credential = UserQrCode::where('qr_uuid', $uuid)->first();
        }
    }

    // Fallback token
    if (!$credential) {
        $credential = UserQrCode::where('token', $payload)->first();
        \Log::info('Fallback search by full payload:', ['found' => $credential ? true : false]);
    }

    if (!$credential) {
        \Log::warning('Credential not found for payload:', ['payload' => $payload]);
        return [
            'valid' => false,
            'message' => 'Credential tidak ditemukan.'
        ];
    }

    $result = $this->qrValidationService->validateCredential($credential);
    return $result;
}
    /**
     * Validasi QR untuk testing (AJAX)
     */
    public function validateQr(Request $request)
{
    $payload = $request->input('payload');
    \Log::info('QR Payload received:', ['payload' => $payload]);

    $credential = null;

    // 1. Coba parse payload UKMQR
    $parsed = $this->parsePayload($payload);

    if ($parsed) {
        // Cari berdasarkan token
        if (isset($parsed['token'])) {
            $credential = UserQrCode::where('token', $parsed['token'])->first();
        }
        // Jika token tidak ditemukan, cari berdasarkan qr_uuid
        if (!$credential && isset($parsed['qr_uuid'])) {
            $credential = UserQrCode::where('qr_uuid', $parsed['qr_uuid'])->first();
        }
    } else {
        // Payload biasa: UUID atau URL
        $uuid = $this->extractUuid($payload);
        if ($uuid) {
            $credential = UserQrCode::where('qr_uuid', $uuid)->first();
        }
    }

    // Fallback terakhir: coba token langsung
    if (!$credential) {
        $credential = UserQrCode::where('token', $payload)->first();
    }

    if (!$credential) {
        \Log::warning('Credential not found. Payload: ' . $payload);
        return response()->json([
            'valid' => false,
            'message' => 'Credential tidak ditemukan. Pastikan QR valid atau regenerasi ulang.',
        ]);
    }

    $result = $this->qrValidationService->validateCredential($credential);
    \Log::info('Validation result:', $result);

    return response()->json($result);
}

    /**
     * Parse payload format "UKMQR|key=value|..."
     */
    protected function parsePayload($payload)
    {
        if (strpos($payload, 'UKMQR|') === 0) {
            $parts = explode('|', $payload);
            $data = [];
            foreach ($parts as $part) {
                $part = urldecode($part);
                if (strpos($part, '=') !== false) {
                    [$key, $value] = explode('=', $part, 2);
                    $data[$key] = $value;
                }
            }
            return $data;
        }
        return null;
    }

    /**
     * Ekstrak UUID dari string payload
     */
    protected function extractUuid($payload)
    {
        if (preg_match('/[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}/', $payload, $matches)) {
            return $matches[0];
        }
        if (preg_match('#/q/([0-9a-fA-F-]+)#', $payload, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^[0-9a-fA-F-]{36}$/', $payload)) {
            return $payload;
        }
        return null;
    }
    /**
     * Cari user berdasarkan NIM (AJAX)
     */
    public function findUserByNim(Request $request)
    {
        $nim = $request->input('nim');
        $user = User::where('nim', $nim)->first();

        if (!$user) {
            return response()->json([
                'found' => false,
                'message' => 'User tidak ditemukan.'
            ]);
        }

        return response()->json([
            'found' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'email' => $user->email,
                'role' => $user->level->name ?? 'N/A',
                'is_active' => $user->is_active,
                'member_number' => $user->member_number,
            ]
        ]);
    }

    /**
     * Identity test: credential → user
     */
    public function identityTest(Request $request)
    {
        $credentialId = $request->input('credential');

        $credential = UserQrCode::where('qr_uuid', $credentialId)->first();

        if (!$credential) {
            return response()->json([
                'found' => false,
                'message' => 'Credential tidak ditemukan.'
            ]);
        }

        $user = $credential->user;

        return response()->json([
            'found' => true,
            'credential' => [
                'uuid' => $credential->qr_uuid,
                'token' => $credential->token,
                'is_active' => $credential->is_active,
                'is_revoked' => $credential->is_revoked,
                'generated_at' => $credential->generated_at,
                'expired_at' => $credential->expired_at,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'email' => $user->email,
                'member_number' => $user->member_number,
                'role' => $user->level->name ?? 'N/A',
                'is_active' => $user->is_active,
                'jabatans' => $user->jabatans->where('pivot.is_primary', true)->pluck('name'),
            ]
        ]);
    }

    /**
     * Deep validation (security check) – dengan pengecekan signature
     */
    public function deepValidate(Request $request)
    {
        $payload = $request->input('payload');
        $parsed = $this->parsePayload($payload);

        // Jika ada format UKMQR, gunakan data tersebut
        if ($parsed) {
            $uuid = $parsed['qr_uuid'] ?? null;
        } else {
            $uuid = $this->extractUuid($payload);
        }

        if (!$uuid) {
            return response()->json([
                'valid' => false,
                'message' => 'UUID tidak valid.',
                'checks' => []
            ]);
        }

        $credential = UserQrCode::where('qr_uuid', $uuid)->first();
        $checks = [];

        // 1. Format
        $checks[] = ['name' => 'Format UUID', 'status' => 'passed', 'detail' => 'UUID valid'];

        // 2. Issuer (selalu UNICODE)
        $checks[] = ['name' => 'Issuer', 'status' => 'passed', 'detail' => 'UNICODE'];

        // 3. Credential exist
        if (!$credential) {
            $checks[] = ['name' => 'Credential', 'status' => 'failed', 'detail' => 'Tidak ditemukan'];
            return response()->json([
                'valid' => false,
                'message' => 'Credential tidak ditemukan.',
                'checks' => $checks
            ]);
        }
        $checks[] = ['name' => 'Credential', 'status' => 'passed', 'detail' => 'Ditemukan'];

        // 4. Active & not revoked
        if (!$credential->is_active || $credential->is_revoked) {
            $checks[] = ['name' => 'Status QR', 'status' => 'failed', 'detail' => 'Tidak aktif atau dicabut'];
            return response()->json([
                'valid' => false,
                'message' => 'QR tidak aktif.',
                'checks' => $checks
            ]);
        }
        $checks[] = ['name' => 'Status QR', 'status' => 'passed', 'detail' => 'ACTIVE'];

        // 5. Expiry
        if ($credential->expired_at && now()->greaterThan($credential->expired_at)) {
            $checks[] = ['name' => 'Expiry', 'status' => 'failed', 'detail' => 'Kedaluwarsa'];
            return response()->json([
                'valid' => false,
                'message' => 'QR kadaluwarsa.',
                'checks' => $checks
            ]);
        }
        $checks[] = ['name' => 'Expiry', 'status' => 'passed', 'detail' => 'Valid'];

        // 6. Verifikasi signature (jika ada di payload)
        if ($parsed && isset($parsed['signature'])) {
            $expectedSignature = $this->generateSignature($credential, $parsed);
            if ($parsed['signature'] !== $expectedSignature) {
                $checks[] = ['name' => 'Signature', 'status' => 'failed', 'detail' => 'Signature tidak cocok'];
                return response()->json([
                    'valid' => false,
                    'message' => 'QR Code tidak valid (signature mismatch).',
                    'checks' => $checks
                ]);
            }
            $checks[] = ['name' => 'Signature', 'status' => 'passed', 'detail' => 'Valid'];
        } else {
            $checks[] = ['name' => 'Signature', 'status' => 'warning', 'detail' => 'Tidak ada signature di payload'];
        }

        $user = $credential->user;
        if (!$user) {
            $checks[] = ['name' => 'User', 'status' => 'failed', 'detail' => 'User tidak ditemukan'];
            return response()->json([
                'valid' => false,
                'message' => 'User tidak ditemukan.',
                'checks' => $checks
            ]);
        }
        $checks[] = ['name' => 'User', 'status' => 'passed', 'detail' => $user->name];

        // 7. Account active
        if (!$user->is_active) {
            $checks[] = ['name' => 'Account', 'status' => 'failed', 'detail' => 'User tidak aktif'];
            return response()->json([
                'valid' => false,
                'message' => 'Akun user tidak aktif.',
                'checks' => $checks
            ]);
        }
        $checks[] = ['name' => 'Account', 'status' => 'passed', 'detail' => 'Aktif'];

        // 8. Role
        $checks[] = ['name' => 'Role', 'status' => 'passed', 'detail' => $user->level->name ?? 'Guest'];

        // 9. Security score (simulasi)
        $score = 100;
        if ($credential->scan_count > 100) $score -= 10;
        if ($credential->last_used_at && now()->diffInHours($credential->last_used_at) > 720) $score -= 5;
        $checks[] = ['name' => 'Risk Score', 'status' => $score >= 80 ? 'passed' : 'warning', 'detail' => $score . '/100'];

        return response()->json([
            'valid' => true,
            'message' => 'Semua validasi lulus.',
            'checks' => $checks,
            'user' => [
                'name' => $user->name,
                'nim' => $user->nim,
                'role' => $user->level->name ?? 'N/A',
                'member_number' => $user->member_number,
            ]
        ]);
    }

    /**
     * Generate signature untuk verifikasi payload (contoh sederhana)
     */
    protected function generateSignature($credential, $parsed)
    {
        // Contoh sederhana: hash dari qr_uuid + token + user_id + secret
        $secret = config('app.key'); // atau gunakan secret khusus
        $data = $parsed['qr_uuid'] . '|' . $parsed['token'] . '|' . $parsed['user_id'] . '|' . $credential->id;
        return hash('sha256', $data . $secret);
    }
}