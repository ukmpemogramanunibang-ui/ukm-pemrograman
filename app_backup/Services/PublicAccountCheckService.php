<?php

namespace App\Services;

use App\Models\AccountCheckLog;
use App\Models\User;
use App\Models\UserQrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class PublicAccountCheckService
{
    public function resolve(string $rawValue, string $method, array $context = []): array
    {
        $startedAt = microtime(true);
        $referenceId = $this->generateReferenceId();

        try {
            $identifier = $this->normalizeIdentifier($rawValue);

            if ($identifier === '') {
                return $this->failure(
                    'EMPTY_INPUT',
                    'Data pemeriksaan kosong',
                    'Sistem tidak menerima UID, NIM, token, atau identifier yang dapat diperiksa.',
                    ['Masukkan NIM atau Member Number.', 'Masukkan UID barcode/QR.', 'Dekatkan kartu NFC.'],
                    $method, 422, $referenceId, $startedAt, $context
                );
            }

            if (mb_strlen($identifier) > 255) {
                return $this->failure(
                    'INPUT_TOO_LONG',
                    'Identifier terlalu panjang',
                    'Data hasil scan melebihi batas yang dapat diproses.',
                    ['Pastikan QR/barcode adalah milik UNICODE.'],
                    $method, 422, $referenceId, $startedAt, $context
                );
            }

            $lookup = match ($method) {
                'barcode', 'upload' => $this->lookupBarcode($identifier),
                'nfc'               => $this->lookupNfc($identifier),
                default             => $this->lookupStandard($identifier),
            };

            if (!empty($lookup['error'])) {
                return $this->finalizeError($lookup['error'], $referenceId, $startedAt, $context, $method, $identifier);
            }

            $user = $lookup['user'] ?? null;

            if (!$user) {
                return $this->failure(
                    'ACCOUNT_NOT_FOUND',
                    'Akun tidak ditemukan',
                    'Identifier diterima oleh sistem, tetapi tidak cocok dengan data akun UNICODE.',
                    ['Periksa kembali NIM atau UID.', 'Pastikan QR/barcode bukan milik sistem lain.', 'Pastikan kartu NFC masih terdaftar.', 'Coba metode pemeriksaan lain.'],
                    $method, 404, $referenceId, $startedAt, $context, $identifier
                );
            }

            return $this->success($user, $method, $lookup['matched_by'] ?? 'Identifier', $referenceId, $startedAt, $context, $identifier);

        } catch (Throwable $e) {
            report($e);

            return $this->failure(
                'ACCOUNT_CHECK_SYSTEM_ERROR',
                'Sistem pemeriksaan mengalami gangguan',
                'Data diterima, tetapi proses pemeriksaan tidak dapat diselesaikan saat ini.',
                ['Ulangi beberapa detik lagi.', 'Gunakan koneksi yang stabil.', 'Coba metode lain.'],
                $method, 500, $referenceId, $startedAt, $context
            );
        }
    }

    protected function userQuery()
    {
        return User::query()
            ->withTrashed()
            ->with([
                'level:id,name',
                'status:id,name',
                'jabatans:id,name',
                'university:id,name',
                'major:id,name',
            ])
            ->select([
                'id', 'user_uuid', 'nim', 'name', 'username',
                'university_id', 'major_id', 'level_id', 'status_id',
                'account_type', 'member_number',
                'is_verified', 'verified_at', 'avatar_url',
                'profile_completion', 'bio', 'portfolio_url', 'social_media',
                'is_active', 'is_online', 'locked_until',
                'created_at', 'deleted_at',
            ]);
    }

    protected function lookupStandard(string $identifier): array
    {
        $user = $this->userQuery()
            ->where(function ($q) use ($identifier) {
                $q->where('user_uuid', $identifier)
                  ->orWhere('nim', $identifier)
                  ->orWhere('member_number', $identifier);
            })
            ->first();

        return [
            'user'       => $user,
            'matched_by' => $this->detectStandardMatchType($identifier),
        ];
    }

    protected function lookupBarcode(string $identifier): array
    {
        $qr = UserQrCode::where('token', $identifier)->first();

        if ($qr) {
            $expired = false;
            if (!empty($qr->expired_at)) {
                try { $expired = Carbon::parse($qr->expired_at)->isPast(); } catch (Throwable) {}
            }

            if (
                (isset($qr->is_revoked) && (bool) $qr->is_revoked) ||
                (isset($qr->is_active) && !(bool) $qr->is_active) ||
                $expired
            ) {
                return ['error' => $this->failure(
                    'BARCODE_NOT_ACTIVE',
                    'Barcode / QR tidak aktif',
                    $expired
                        ? 'Barcode / QR ditemukan, tetapi masa berlakunya sudah berakhir.'
                        : 'Barcode / QR ditemukan, tetapi credential tersebut sudah tidak aktif.',
                    ['Gunakan barcode/QR terbaru.', 'Hubungi admin UNICODE.'],
                    'barcode', 403
                )];
            }

            if (!empty($qr->user_id)) {
                $user = $this->userQuery()->whereKey($qr->user_id)->first();
                return ['user' => $user, 'matched_by' => 'QR Code'];
            }
        }

        $user = $this->userQuery()->where('qr_token', $identifier)->first();
        if ($user) {
            return ['user' => $user, 'matched_by' => 'QR Token'];
        }

        $standard = $this->lookupStandard($identifier);
        return [
            'user'       => $standard['user'],
            'matched_by' => $standard['user'] ? 'Identifier Barcode' : null,
        ];
    }

    protected function lookupNfc(string $identifier): array
    {
        $identifier = $this->normalizeNfcIdentifier($identifier);

        $standard = $this->lookupStandard($identifier);
        if ($standard['user']) {
            return ['user' => $standard['user'], 'matched_by' => 'NFC Identifier'];
        }

        if (!class_exists(\App\Models\NfcCredential::class)) {
            return ['error' => $this->failure(
                'NFC_MODULE_NOT_READY',
                'Modul NFC belum siap',
                'Model credential NFC belum tersedia pada aplikasi.',
                ['Pastikan modul NFC UNICODE sudah aktif.'],
                'nfc', 503
            )];
        }

        $credentialModel = new \App\Models\NfcCredential();
        $table = $credentialModel->getTable();

        $columns = [];
        try {
            $available = Schema::getColumnListing($table);
            $configured = config('account_check.nfc_identifier_columns', []);
            foreach ($configured as $col) {
                if (in_array($col, $available, true)) {
                    $columns[] = $col;
                }
            }
        } catch (Throwable $e) { report($e); }

        if (empty($columns)) {
            return ['error' => $this->failure(
                'NFC_IDENTIFIER_FIELD_NOT_FOUND',
                'Kolom identifier NFC belum terdeteksi',
                'Sistem belum menemukan field penyimpan UID pada tabel credential NFC.',
                ['Periksa config/account_check.php.'],
                'nfc', 503
            )];
        }

        foreach ($columns as $column) {
            $credential = $credentialModel->newQuery()->where($column, $identifier)->first();
            if (!$credential) continue;

            if (isset($credential->is_active) && !(bool) $credential->is_active) {
                return ['error' => $this->failure(
                    'NFC_NOT_ACTIVE',
                    'Credential NFC tidak aktif',
                    'Kartu/tag NFC ditemukan, tetapi credential-nya sedang tidak aktif.',
                    ['Gunakan kartu NFC yang aktif.', 'Hubungi admin UNICODE.'],
                    'nfc', 403
                )];
            }

            if (isset($credential->is_revoked) && (bool) $credential->is_revoked) {
                return ['error' => $this->failure(
                    'NFC_REVOKED',
                    'Credential NFC telah dicabut',
                    'Kartu/tag NFC ditemukan, tetapi credential tersebut telah dicabut.',
                    ['Gunakan kartu NFC pengganti.'],
                    'nfc', 403
                )];
            }

            $userId = $credential->user_id ?? null;
            if (!$userId) {
                return ['error' => $this->failure(
                    'NFC_BINDING_ERROR',
                    'Credential NFC tidak terhubung',
                    'Credential NFC ditemukan, tetapi belum terhubung ke user.',
                    ['Periksa assignment di panel admin.'],
                    'nfc', 422
                )];
            }

            $user = $this->userQuery()->whereKey($userId)->first();
            return ['user' => $user, 'matched_by' => 'NFC Credential'];
        }

        return ['user' => null, 'matched_by' => null];
    }

    protected function success(
        User $user,
        string $method,
        string $matchedBy,
        string $referenceId,
        float $startedAt,
        array $context,
        string $identifier
    ): array {
        $status = $this->resolveAccountStatus($user);
        $tier   = config("account_check.privacy_tier.{$method}", 'limited');

        // Jabatan aktif
        $positions = $user->jabatans
            ->filter(function ($jabatan) {
                $pivot = $jabatan->pivot;
                if (!$pivot) return true;
                if (isset($pivot->status) && strtolower((string) $pivot->status) !== 'active') return false;
                if (isset($pivot->end_date) && !empty($pivot->end_date)) {
                    try { if (Carbon::parse($pivot->end_date)->isPast()) return false; } catch (Throwable) {}
                }
                return true;
            })
            ->values();

        $primaryPosition = $positions->first(fn ($j) => isset($j->pivot->is_primary) && (bool) $j->pivot->is_primary);
        $positionNames = $positions
            ->map(fn ($j) => $j->name ?? $j->title ?? 'Jabatan')
            ->filter()->unique()->values()->all();

        $totalPoints = (int) $user->pointHistories()->where('status', 'approved')->sum('points');

        // Privacy tier: masked data untuk 'limited'
        $isLimited = $tier === 'limited';
        $displayName = $isLimited ? $this->maskName($user->name) : $user->name;
        $displayNim  = $isLimited ? $this->maskNim($user->nim) : $user->nim;
        $displayMember = $isLimited ? null : $user->member_number;

        $socialLinks = $isLimited ? [] : $this->normalizeSocialMedia($user->social_media);
        $portfolioUrl = $isLimited ? null : $this->validUrl($user->portfolio_url);
        $avatarUrl = $isLimited ? null : $this->resolveAvatarUrl($user->avatar_url);
        $bio = $isLimited ? null : $user->bio;

        $diagnostics = $this->buildDiagnostics($user, $status, $matchedBy, $tier);

        // Log audit
        $this->logCheck(
            referenceId:  $referenceId,
            method:       $method,
            tier:         $tier,
            resultCode:   'ACCOUNT_FOUND',
            ok:           true,
            matchedId:    $user->id,
            identifier:   $identifier,
            context:      $context,
            startedAt:    $startedAt,
        );

        return [
            'ok'      => true,
            'message' => 'Akun berhasil ditemukan dan diproses.',
            'data'    => [
                'id'        => $user->id,
                'user_uuid' => $user->user_uuid,

                'privacy_tier' => $tier,

                'identity' => [
                    'name'          => $displayName,
                    'nim'           => $displayNim,
                    'member_number' => $displayMember,
                    'masked'        => $isLimited,
                ],

                'education' => [
                    'university' => $user->university?->name,
                    'major'      => $user->major?->name,
                ],

                'role' => [
                    'level_name'       => $user->level?->name ?? 'Tidak tersedia',
                    'primary_position' => $primaryPosition?->name ?? null,
                    'positions'        => $positionNames,
                ],

                'points' => ['total' => $isLimited ? null : $totalPoints],

                'profile' => [
                    'avatar_url'         => $avatarUrl,
                    'profile_completion' => (int) ($user->profile_completion ?? 0),
                    'bio'                => $bio,
                ],

                'portfolio' => [
                    'url'       => $portfolioUrl,
                    'available' => !$isLimited && filled($user->portfolio_url),
                ],

                'social_media' => $socialLinks,

                'account' => [
                    'status'       => $status,
                    'account_type' => $user->account_type,
                    'verified'     => (bool) $user->is_verified,
                    'verified_at'  => optional($user->verified_at)->toIso8601String(),
                    'online'       => (bool) $user->is_online,
                    'deleted'      => $user->trashed(),
                ],

                'verification' => [
                    'reference_id' => $referenceId,
                    'matched_by'   => $matchedBy,
                    'method'       => $method,
                    'tier'         => $tier,
                    'checked_at'   => now()->toIso8601String(),
                ],

                'diagnostics' => $diagnostics,
            ],
        ];
    }

    protected function buildDiagnostics(User $user, array $status, string $matchedBy, string $tier): array
    {
        $items = [];

        $items[] = [
            'key' => 'tier', 'label' => 'Privacy tier',
            'value' => $tier === 'full' ? 'Credential Verified' : 'Limited Verification',
            'tone' => $tier === 'full' ? 'good' : 'info',
        ];

        $items[] = [
            'key' => 'credential', 'label' => 'Credential',
            'value' => $matchedBy, 'tone' => 'good',
        ];

        $items[] = [
            'key' => 'account_status', 'label' => 'Status akun',
            'value' => $status['label'], 'tone' => $status['tone'],
        ];

        $pc = (int) ($user->profile_completion ?? 0);
        $items[] = [
            'key' => 'profile', 'label' => 'Kelengkapan profil',
            'value' => $pc . '%', 'tone' => $pc >= 80 ? 'good' : 'info',
        ];

        $items[] = [
            'key' => 'verified', 'label' => 'Verifikasi',
            'value' => $user->is_verified ? 'Terverifikasi' : 'Belum diverifikasi',
            'tone' => $user->is_verified ? 'good' : 'warning',
        ];

        return $items;
    }

    protected function resolveAccountStatus(User $user): array
    {
        if ($user->trashed()) {
            return ['key' => 'deleted', 'label' => 'Dihapus', 'tone' => 'danger',
                'description' => 'Data akun telah masuk status penghapusan.'];
        }

        $statusName = strtolower(Str::ascii((string) ($user->status?->name ?? '')));

        $rules = [
            ['alumni','alumni','Alumni','info','Akun berada pada status alumni.'],
            ['lulus','alumni','Alumni','info','Akun tercatat sebagai lulusan.'],
            ['graduated','alumni','Alumni','info','Akun tercatat sebagai lulusan.'],
            ['pensiun','retired','Dipensiunkan','warning','Keanggotaan berakhir karena status pensiun.'],
            ['retired','retired','Dipensiunkan','warning','Keanggotaan berakhir karena pensiun.'],
            ['jeda','paused','Dijeda','warning','Akun sedang dijeda.'],
            ['pause','paused','Dijeda','warning','Akun sedang dijeda.'],
            ['beku','frozen','Dibekukan','danger','Akun sedang dibekukan.'],
            ['freeze','frozen','Dibekukan','danger','Akun sedang dibekukan.'],
            ['suspend','frozen','Dibekukan','danger','Akun sedang dibekukan.'],
            ['blokir','frozen','Dibekukan','danger','Akun sedang diblokir.'],
            ['blocked','frozen','Dibekukan','danger','Akun sedang diblokir.'],
            ['blacklist','frozen','Dibekukan','danger','Akun sedang diblacklist.'],
            ['nonaktif','inactive','Nonaktif','warning','Akun tidak aktif.'],
            ['inactive','inactive','Nonaktif','warning','Akun tidak aktif.'],
            ['cuti','leave','Cuti','warning','Akun sedang cuti.'],
            ['leave','leave','Cuti','warning','Akun sedang cuti.'],
            ['pending','pending','Menunggu Verifikasi','warning','Akun menunggu verifikasi.'],
            ['menunggu','pending','Menunggu Verifikasi','warning','Akun menunggu verifikasi.'],
        ];

        foreach ($rules as [$keyword, $key, $label, $tone, $desc]) {
            if ($keyword !== '' && Str::contains($statusName, $keyword)) {
                return ['key' => $key, 'label' => $label, 'tone' => $tone, 'description' => $desc];
            }
        }

        if (!empty($user->locked_until) && Carbon::parse($user->locked_until)->isFuture()) {
            return ['key' => 'frozen', 'label' => 'Dibekukan Sementara', 'tone' => 'danger',
                'description' => 'Akun memiliki pembatasan keamanan sementara.'];
        }

        if (!$user->is_active) {
            return ['key' => 'inactive', 'label' => 'Nonaktif', 'tone' => 'warning',
                'description' => 'Akun tidak sedang aktif.'];
        }

        return ['key' => 'active', 'label' => 'Aktif', 'tone' => 'good',
            'description' => 'Akun tercatat sebagai akun aktif UNICODE.'];
    }

    protected function normalizeIdentifier(string $rawValue): string
    {
        $value = trim($rawValue);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        $value = preg_replace('/^(unicode:(qr|nfc|barcode|uid):)/i', '', $value);

        if (preg_match('#^https?://#i', $value)) {
            $parts = parse_url($value);
            if ($parts !== false) {
                $query = [];
                if (!empty($parts['query'])) parse_str($parts['query'], $query);
                foreach (['uid','uuid','nim','member_number','member','token','nfc','identifier'] as $key) {
                    if (!empty($query[$key])) return trim((string) $query[$key]);
                }
                if (!empty($parts['path'])) {
                    $segments = array_values(array_filter(explode('/', trim($parts['path'], '/'))));
                    if (!empty($segments)) return trim((string) end($segments));
                }
            }
        }

        return trim($value);
    }

    protected function normalizeNfcIdentifier(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/^(unicode:nfc:|nfc:)/i', '', $value);
        return trim($value);
    }

    protected function detectStandardMatchType(string $identifier): string
    {
        if (Str::isUuid($identifier)) return 'UUID';
        if (preg_match('/^UKM[A-Z0-9\-]+$/i', $identifier)) return 'Member Number';
        return 'NIM / Identifier';
    }

    protected function normalizeSocialMedia($social): array
    {
        if (!is_array($social)) return [];
        $result = [];
        foreach ($social as $network => $value) {
            if (is_array($value)) $value = $value['url'] ?? $value['link'] ?? $value['value'] ?? null;
            if (!is_string($value) || trim($value) === '') continue;
            $result[] = [
                'network' => ucfirst(str_replace(['_','-'], ' ', (string) $network)),
                'url'     => $this->validUrl($value),
                'value'   => trim($value),
            ];
        }
        return $result;
    }

    protected function validUrl(?string $url): ?string
    {
        if (!filled($url)) return null;
        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) $url = 'https://' . $url;
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    protected function resolveAvatarUrl(?string $path): ?string
    {
        if (!filled($path)) return null;
        $path = trim($path);
        if (Str::startsWith($path, ['http://','https://','data:'])) return $path;
        if (Str::startsWith($path, '/')) return asset(ltrim($path, '/'));
        if (Str::startsWith($path, 'storage/')) return asset($path);
        return asset('storage/' . ltrim($path, '/'));
    }

    /* ================== HELPERS ================== */

    protected function generateReferenceId(): string
    {
        $prefix = config('account_check.reference_prefix', 'CHK');
        return sprintf('%s-%s-%s',
            $prefix,
            now()->format('Ymd'),
            strtoupper(Str::random(6))
        );
    }

    protected function maskName(?string $name): string
    {
        if (!$name) return '-';
        $parts = preg_split('/\s+/', trim($name));
        return collect($parts)->map(function ($p, $i) {
            if ($i === 0) return $p;
            $len = mb_strlen($p);
            if ($len <= 2) return mb_substr($p, 0, 1) . '*';
            return mb_substr($p, 0, 1) . str_repeat('*', $len - 1);
        })->implode(' ');
    }

    protected function maskNim(?string $nim): ?string
    {
        if (!$nim) return null;
        $len = strlen($nim);
        if ($len <= 4) return str_repeat('•', $len);
        return substr($nim, 0, 4) . str_repeat('•', $len - 4);
    }

    protected function logCheck(
        string $referenceId,
        string $method,
        string $tier,
        string $resultCode,
        bool $ok,
        ?int $matchedId,
        ?string $identifier,
        array $context,
        float $startedAt
    ): void {
        try {
            if (!class_exists(AccountCheckLog::class)) return;

            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            AccountCheckLog::create([
                'reference_id'    => $referenceId,
                'method'          => $method,
                'privacy_tier'    => $tier,
                'result_code'     => $resultCode,
                'ok'              => $ok,
                'identifier_hash' => $this->hashIdentifier($identifier),
                'ip_hash'         => $this->hashValue($context['ip'] ?? null),
                'user_agent_hash' => $this->hashValue($context['user_agent'] ?? null),
                'matched_user_id' => $matchedId,
                'duration_ms'     => $durationMs,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function hashIdentifier(?string $identifier): string
    {
        return hash_hmac('sha256', (string) $identifier, config('app.key', ''));
    }

    protected function hashValue(?string $value): ?string
    {
        if (!$value) return null;
        return hash_hmac('sha256', $value, config('app.key', ''));
    }

    /* ================== FAILURE ================== */

    protected function failure(
        string $code,
        string $title,
        string $message,
        array $hints,
        string $method,
        int $status = 422,
        ?string $referenceId = null,
        ?float $startedAt = null,
        array $context = [],
        ?string $identifier = null
    ): array {
        $referenceId = $referenceId ?: $this->generateReferenceId();
        $startedAt = $startedAt ?? microtime(true);

        $this->logCheck(
            referenceId: $referenceId,
            method:      $method,
            tier:        config("account_check.privacy_tier.{$method}", 'limited'),
            resultCode:  $code,
            ok:          false,
            matchedId:   null,
            identifier:  $identifier,
            context:     $context,
            startedAt:   $startedAt,
        );

        return [
            'ok'          => false,
            'http_status' => $status,
            'error' => [
                'code'         => $code,
                'title'        => $title,
                'message'      => $message,
                'hints'        => $hints,
                'method'       => $method,
                'reference_id' => $referenceId,
                'occurred_at'  => now()->toIso8601String(),
            ],
        ];
    }

    protected function finalizeError(array $error, string $referenceId, float $startedAt, array $context, string $method, ?string $identifier): array
    {
        if (isset($error['error'])) {
            $error['error']['reference_id'] = $referenceId;
        }
        $this->logCheck(
            referenceId: $referenceId,
            method:      $method,
            tier:        config("account_check.privacy_tier.{$method}", 'limited'),
            resultCode:  $error['error']['code'] ?? 'UNKNOWN',
            ok:          false,
            matchedId:   null,
            identifier:  $identifier,
            context:     $context,
            startedAt:   $startedAt,
        );
        return $error;
    }
}