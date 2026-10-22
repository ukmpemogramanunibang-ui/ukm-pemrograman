<?php

namespace App\Services;

use App\Models\IdentityVault\IdentityRegistry;
use App\Models\IdentityVault\NfcCredential;
use App\Models\IdentityVault\CredentialAudit;
use App\Models\IdentityVault\CredentialBackup;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class IdentityVaultService
{
    /**
     * Buat identitas baru untuk user
     */
    public function createIdentity(User $user, array $options = [])
    {
        $credentialUuid = (string) Str::uuid();
        $credentialCode = $this->generateCredentialCode();

        $snapshot = $this->buildSnapshot($user);

        $identity = IdentityRegistry::create([
            'credential_uuid' => $credentialUuid,
            'credential_code' => $credentialCode,
            'user_uuid' => $user->user_uuid,
            'member_number' => $user->member_number,
            'account_type' => $user->account_type ?? 'official',
            'status' => 'active',
            'qr_enabled' => true,
            'nfc_enabled' => false,
            'account_snapshot' => $snapshot,
        ]);

        // Backup awal
        $this->createBackup($identity, $snapshot);

        return $identity;
    }

    /**
     * Buat snapshot akun untuk recovery
     */
    public function buildSnapshot(User $user)
    {
        return [
            'user_uuid' => $user->user_uuid,
            'name' => $user->name,
            'email' => $user->email,
            'nim' => $user->nim,
            'phone' => $user->phone,
            'member_number' => $user->member_number,
            'level_id' => $user->level_id,
            'status_id' => $user->status_id,
            'account_type' => $user->account_type,
            'password_hash' => $user->password,
            'pin_hash' => $user->pin,
            'is_verified' => $user->is_verified,
            'is_active' => $user->is_active,
            'preferences' => $user->preferences,
            'settings' => $user->settings,
            'avatar_url' => $user->avatar_url,
            'gender' => $user->gender,
            'birth_date' => $user->birth_date?->toDateString(),
            'address' => $user->address,
            'bio' => $user->bio,
            'timezone' => $user->timezone,
            'locale' => $user->locale,
        ];
    }

    /**
     * Recovery: rebuild user dari identity registry
     */
    public function recoverIdentity(IdentityRegistry $identity): ?User
    {
        // Cek apakah user sudah ada
        if ($identity->user_uuid) {
            $existingUser = User::where('user_uuid', $identity->user_uuid)->first();
            if ($existingUser) {
                // Update snapshot jika perlu
                $this->updateSnapshot($identity, $existingUser);
                return $existingUser;
            }
        }

        // Buat user baru dari snapshot
        $snapshot = $identity->account_snapshot;
        if (!$snapshot) {
            // fallback: gunakan data minimal
            $snapshot = [
                'name' => 'Recovered User',
                'email' => 'recovered@unicode.test',
                'password_hash' => Hash::make('password'),
            ];
        }

        $user = User::create([
            'user_uuid' => $identity->user_uuid ?? (string) Str::uuid(),
            'name' => $snapshot['name'] ?? 'Unknown',
            'email' => $snapshot['email'] ?? null,
            'nim' => $snapshot['nim'] ?? null,
            'phone' => $snapshot['phone'] ?? null,
            'member_number' => $identity->member_number ?? $snapshot['member_number'] ?? null,
            'level_id' => $snapshot['level_id'] ?? 5, // default anggota
            'status_id' => $snapshot['status_id'] ?? 2,
            'account_type' => $identity->account_type ?? $snapshot['account_type'] ?? 'official',
            'password' => $snapshot['password_hash'] ?? Hash::make('password'),
            'pin' => $snapshot['pin_hash'] ?? null,
            'is_verified' => $snapshot['is_verified'] ?? 0,
            'is_active' => $snapshot['is_active'] ?? 1,
            'preferences' => $snapshot['preferences'] ?? null,
            'settings' => $snapshot['settings'] ?? null,
            'avatar_url' => $snapshot['avatar_url'] ?? null,
            'gender' => $snapshot['gender'] ?? null,
            'birth_date' => isset($snapshot['birth_date']) ? Carbon::parse($snapshot['birth_date']) : null,
            'address' => $snapshot['address'] ?? null,
            'bio' => $snapshot['bio'] ?? null,
            'timezone' => $snapshot['timezone'] ?? 'Asia/Jakarta',
            'locale' => $snapshot['locale'] ?? 'id',
        ]);

        // Update identity dengan user_uuid yang baru
        $identity->user_uuid = $user->user_uuid;
        $identity->member_number = $user->member_number;
        $identity->save();

        // Buat QR Code untuk user ini (jika belum ada)
        $this->ensureQrCode($user, $identity);

        return $user;
    }

    /**
     * Pastikan user memiliki QR Code yang mengarah ke credential_uuid
     */
    protected function ensureQrCode(User $user, IdentityRegistry $identity)
    {
        // Cek apakah sudah ada QR di tabel user_qr_codes
        $existing = $user->qrCodes()->where('token', $identity->credential_uuid)->first();
        if (!$existing) {
            // Buat QR baru dengan token = credential_uuid
            $qrService = app(\App\Services\QrCodeService::class);
            $qrService->generateForUser($user, [
                'token' => $identity->credential_uuid, // gunakan credential_uuid sebagai token
                'replace' => true,
                'is_permanent' => true,
            ]);
        }
    }

    /**
     * Backup snapshot terbaru
     */
    public function createBackup(IdentityRegistry $identity, array $snapshot)
    {
        $checksum = md5(json_encode($snapshot));
        CredentialBackup::create([
            'credential_uuid' => $identity->credential_uuid,
            'snapshot' => $snapshot,
            'checksum' => $checksum,
            'backup_at' => now(),
        ]);
    }

    /**
     * Update snapshot di identity dan buat backup baru
     */
    public function updateSnapshot(IdentityRegistry $identity, User $user)
    {
        $snapshot = $this->buildSnapshot($user);
        $identity->account_snapshot = $snapshot;
        $identity->save();
        $this->createBackup($identity, $snapshot);
    }

    /**
     * Log audit
     */
    public function logAudit($credentialUuid, $userUuid, $action, $metadata = [], $ip = null, $userAgent = null)
    {
        CredentialAudit::create([
            'credential_uuid' => $credentialUuid,
            'user_uuid' => $userUuid,
            'action' => $action,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Generate credential code unik
     */
    protected function generateCredentialCode()
    {
        $last = IdentityRegistry::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'UNICODE-CRED-' . str_pad($nextId, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Verifikasi NFC: cek signature dan status
     */
    public function verifyNfc($nfcIdentifier, $signature)
    {
        $hash = hash('sha256', $nfcIdentifier);
        $nfc = NfcCredential::where('nfc_identifier_hash', $hash)->first();
        if (!$nfc) {
            return ['valid' => false, 'message' => 'NFC tidak terdaftar'];
        }

        // Verifikasi signature (misal dengan HMAC)
        $expectedSignature = hash_hmac('sha256', $nfcIdentifier, config('app.key'));
        if (!hash_equals($expectedSignature, $signature)) {
            return ['valid' => false, 'message' => 'Signature tidak valid'];
        }

        $identity = $nfc->identity;
        if (!$identity || $identity->status !== 'active') {
            return ['valid' => false, 'message' => 'Kartu tidak aktif atau dicabut'];
        }

        $nfc->last_seen_at = now();
        $nfc->save();

        return [
            'valid' => true,
            'credential_uuid' => $identity->credential_uuid,
            'user_uuid' => $identity->user_uuid,
            'identity' => $identity,
        ];
    }

    /**
     * Recovery massal untuk semua identitas di vault
     */
    public function recoverAllIdentities()
    {
        $identities = IdentityRegistry::where('status', 'active')->get();
        $results = [];
        foreach ($identities as $identity) {
            try {
                $user = $this->recoverIdentity($identity);
                $results[$identity->credential_uuid] = $user ? 'success' : 'failed';
            } catch (\Exception $e) {
                $results[$identity->credential_uuid] = 'error: ' . $e->getMessage();
            }
        }
        return $results;
    }
}