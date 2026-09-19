<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQrCode;
use App\Models\UserQrRecoverySnapshot;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QrRecoveryService
{
    /**
     * Generate recovery snapshot untuk satu user.
     * Simpan ke database dan juga ke file storage (cadangan).
     */
    public function generateSnapshot(User $user): UserQrRecoverySnapshot
    {
        // Ambil QR aktif terbaru
        $qr = $user->qrCodes()->where('is_active', true)->latest()->first();

        // Data profil (array biasa)
        $profile = [
            'user_uuid'        => $user->user_uuid,
            'original_user_id' => $user->id,
            'member_number'    => $user->member_number,
            'nim'              => $user->nim,
            'name'             => $user->name,
            'email'            => $user->email,
            'phone'            => $user->phone,
            'level_id'         => $user->level_id,
            'status_id'        => $user->status_id,
            'ukm_id'           => $user->ukm_id,
            'avatar_url'       => $user->avatar_url,
            'is_active'        => $user->is_active,
            'is_verified'      => $user->is_verified,
            'account_type'     => $user->account_type,
        ];

        // Data kredensial (array)
        $credentials = [
            'password' => $user->password, // sudah hashed
            'pin'      => $user->pin,
            'qr_token' => $qr->token ?? $user->qr_token,
        ];

        // 🔐 Enkripsi credential menjadi string
        $encryptedCredential = Crypt::encryptString(json_encode($credentials));

        $snapshotData = [
            'user_uuid'           => $user->user_uuid,
            'original_user_id'    => $user->id,
            'member_number'       => $user->member_number,
            'nim'                 => $user->nim,
            'name'                => $user->name,
            'email'               => $user->email,
            'phone'               => $user->phone,
            'level_id'            => $user->level_id,
            'status_id'           => $user->status_id,
            'ukm_id'              => $user->ukm_id,
            'qr_uuid'             => $qr->qr_uuid ?? null,
            'qr_token'            => $qr->token ?? $user->qr_token,
            'avatar_url'          => $user->avatar_url,
            'profile_snapshot'    => $profile,              // akan otomatis di-json oleh cast 'array'
            'credential_snapshot' => $encryptedCredential,  // string terenkripsi
            'last_snapshot_at'    => now(),
        ];

        // Simpan ke database
        $snapshot = UserQrRecoverySnapshot::updateOrCreate(
            ['user_uuid' => $user->user_uuid],
            $snapshotData
        );

        // Simpan juga ke file storage (redundansi)
        $this->storeSnapshotFile($user->user_uuid, $snapshotData);

        return $snapshot;
    }

    /**
     * Simpan snapshot ke file terenkripsi di storage/app/private/qr-recovery/
     */
    protected function storeSnapshotFile(string $userUuid, array $data): void
    {
        $path = storage_path('app/private/qr-recovery/' . $userUuid . '.json');
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        // Enkripsi seluruh data sebagai string JSON
        $encrypted = Crypt::encryptString(json_encode($data));
        file_put_contents($path, $encrypted);
    }

    /**
     * Cari snapshot dari file (jika database kosong)
     */
    public function findSnapshotFromFile(string $userUuid): ?array
    {
        $path = storage_path('app/private/qr-recovery/' . $userUuid . '.json');
        if (!file_exists($path)) {
            return null;
        }
        try {
            $encrypted = file_get_contents($path);
            $decrypted = Crypt::decryptString($encrypted);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            Log::error('Gagal mendekripsi snapshot file: ' . $e->getMessage());
            return null;
        }
    }

    // ... (method verifyQrPayload, findUserFromPayload tetap sama)

    /**
     * Pulihkan user dari snapshot.
     * Jika user sudah ada (soft-deleted), restore.
     * Jika tidak ada, buat baru.
     */
    public function restoreUserFromSnapshot(UserQrRecoverySnapshot $snapshot): User
    {
        return DB::transaction(function () use ($snapshot) {
            $user = User::withTrashed()->where('user_uuid', $snapshot->user_uuid)->first();

            // 🔓 Dekripsi credential
            $credentialData = null;
            if ($snapshot->credential_snapshot) {
                try {
                    $decrypted = Crypt::decryptString($snapshot->credential_snapshot);
                    $credentialData = json_decode($decrypted, true);
                } catch (\Exception $e) {
                    Log::error('Gagal mendekripsi credential snapshot', ['user_uuid' => $snapshot->user_uuid]);
                }
            }

            if (!$user) {
                $user = new User();
                $user->user_uuid = $snapshot->user_uuid;
                $user->member_number = $snapshot->member_number;
                $user->nim = $snapshot->nim;
                $user->name = $snapshot->name;
                $user->email = $snapshot->email;
                $user->phone = $snapshot->phone;
                $user->level_id = $snapshot->level_id;
                $user->status_id = $snapshot->status_id;
                $user->ukm_id = $snapshot->ukm_id;
                $user->avatar_url = $snapshot->avatar_url;
                $user->account_type = 'official';

                // Restore kredensial
                if ($credentialData) {
                    $user->password = $credentialData['password'] ?? Hash::make('password_default');
                    $user->pin = $credentialData['pin'] ?? null;
                    $user->qr_token = $credentialData['qr_token'] ?? null;
                }

                $user->save();

                // Set is_active dan is_verified dari snapshot
                $profile = $snapshot->profile_snapshot;
                if ($profile) {
                    $user->is_active = $profile['is_active'] ?? true;
                    $user->is_verified = $profile['is_verified'] ?? false;
                    $user->save();
                }
            } elseif ($user->trashed()) {
                $user->restore();
                $user->is_active = true;
                $user->save();
            }

            // Pulihkan QR Code
            $this->restoreQrCode($user, $snapshot);

            // Update snapshot original_user_id agar terhubung
            $snapshot->original_user_id = $user->id;
            $snapshot->save();

            return $user;
        });
    }

    protected function restoreQrCode(User $user, UserQrRecoverySnapshot $snapshot): void
    {
        UserQrCode::where('user_id', $user->id)->update([
            'is_active' => false,
            'is_revoked' => true,
            'revoked_at' => now(),
        ]);

        $qrUuid = $snapshot->qr_uuid ?? (string) Str::uuid();
        $token = $snapshot->qr_token ?? Str::random(120);

        UserQrCode::updateOrCreate(
            ['qr_uuid' => $qrUuid],
            [
                'token'      => $token,
                'user_id'    => $user->id,
                'is_active'  => true,
                'is_revoked' => false,
                'generated_at' => now(),
                'qr_image'   => null,
                'qr_path'    => null,
            ]
        );

        $user->qr_token = $token;
        $user->save();
    }

    public function generateQrPayload(User $user): string
    {
        $qr = $user->qrCodes()->where('is_active', true)->latest()->first();

        $payloadData = [
            'v'             => 1,
            'ukm_id'        => 1,
            'user_uuid'     => $user->user_uuid,
            'user_id'       => $user->id,
            'member_number' => $user->member_number,
            'nim'           => $user->nim,
            'name'          => $user->name,
            'qr_uuid'       => $qr->qr_uuid ?? (string) Str::uuid(),
            'token'         => $qr->token ?? Str::random(120),
            'role'          => optional($user->level)->name ?? 'guest',
        ];

        ksort($payloadData);
        $canonical = http_build_query($payloadData, '', '&', PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha256', $canonical, config('app.key'));

        $payloadData['signature'] = $signature;

        $parts = [];
        foreach ($payloadData as $key => $value) {
            $parts[] = $key . '=' . urlencode($value);
        }

        return 'UKMQR|' . implode('|', $parts);
    }
}