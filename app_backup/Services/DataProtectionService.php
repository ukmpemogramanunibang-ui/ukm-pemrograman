<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointHistory;
use App\Models\UserQrCode;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DataProtectionService
{
    protected string $connection = 'mysql_protected';
    protected array $requiredTables = [
        'protected_users',
        'protected_points',
        'protected_qr_codes',
        'protected_transactions',
        'protected_activity_logs',
        'protected_backup_metadata',
    ];

    /**
     * Pastikan semua tabel proteksi ada
     */
   protected function ensureTablesExist(): void
{
    $missing = [];
    foreach ($this->requiredTables as $table) {
        if (!Schema::connection($this->connection)->hasTable($table)) {
            $missing[] = $table;
        }
    }

    if (!empty($missing)) {
        $message = "Tabel proteksi tidak ditemukan: " . implode(', ', $missing) .
                   ". Jalankan: php artisan protected:install";
        Log::error($message);
        throw new \Exception($message);
    }
}

    /**
     * Backup semua data ke protected database
     */
    public function syncAllToProtected(?string $backupName = null): int
    {
        $this->ensureTablesExist();

        $backupName = $backupName ?? 'backup_' . now()->format('Ymd_His');

        // Catat metadata backup
        $backupId = DB::connection($this->connection)
            ->table('protected_backup_metadata')
            ->insertGetId([
                'backup_name' => $backupName,
                'backup_type' => 'manual',
                'started_at' => now(),
                'status' => 'running',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        try {
            $counts = [
                'users' => 0,
                'points' => 0,
                'qrs' => 0,
                'transactions' => 0,
                'logs' => 0,
            ];

            // 1. Users
            User::withTrashed()->chunk(100, function ($users) use (&$counts) {
                foreach ($users as $user) {
                    $this->syncUser($user);
                    $counts['users']++;
                }
            });

            // 2. Point histories
            PointHistory::chunk(100, function ($points) use (&$counts) {
                foreach ($points as $point) {
                    $this->syncPoint($point);
                    $counts['points']++;
                }
            });

            // 3. QR Codes
            UserQrCode::withTrashed()->chunk(100, function ($qrs) use (&$counts) {
                foreach ($qrs as $qr) {
                    $this->syncQr($qr);
                    $counts['qrs']++;
                }
            });

            // 4. Transactions
            // Ambil dari point_histories jika ada
            PointHistory::chunk(100, function ($transactions) use (&$counts) {
                foreach ($transactions as $tx) {
                    $this->syncTransaction($tx);
                    $counts['transactions']++;
                }
            });

            // 5. Activity logs (AuditLog)
            if (class_exists(AuditLog::class)) {
                AuditLog::chunk(100, function ($logs) use (&$counts) {
                    foreach ($logs as $log) {
                        $this->syncActivityLog($log);
                        $counts['logs']++;
                    }
                });
            }

            // Update metadata
            DB::connection($this->connection)
                ->table('protected_backup_metadata')
                ->where('id', $backupId)
                ->update([
                    'stats' => json_encode($counts),
                    'completed_at' => now(),
                    'status' => 'success',
                    'updated_at' => now(),
                ]);

            return array_sum($counts);
        } catch (\Exception $e) {
            DB::connection($this->connection)
                ->table('protected_backup_metadata')
                ->where('id', $backupId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now(),
                ]);
            throw $e;
        }
    }

    public function syncUser(User $user): void
{
    DB::connection($this->connection)
        ->table('protected_users')
        ->updateOrInsert(
            ['user_uuid' => $user->user_uuid],
            [
                'email' => $user->email,
                'name' => $user->name,
                'nim' => $user->nim,
                'member_number' => $user->member_number,
                'level_id' => $user->level_id,
                'level_name' => $user->level?->name,
                'status_id' => $user->status_id,
                'status_name' => $user->status?->name,
                'full_data' => json_encode($user->toArray()),
                'related_data' => json_encode($this->collectRelatedData($user)),
                'last_synced_at' => now(),
                'updated_at' => now(),
                'deleted_at' => $user->deleted_at,
            ]
        );
}


    protected function collectRelatedData(User $user): array
    {
        return [
            'points_total' => $user->pointHistories()->sum('points'),
            'qr_codes_count' => $user->qrCodes()->count(),
            'last_login' => $user->last_login_at,
            'member_since' => $user->created_at,
            'jabatan' => $user->jabatans()->wherePivot('is_primary', true)->first()?->name,
        ];
    }

   public function syncPoint(PointHistory $point): void
{
    $userUuid = $point->user?->user_uuid ?? 'unknown';
    DB::connection($this->connection)
        ->table('protected_points')
        ->updateOrInsert(
            ['user_uuid' => $userUuid],
            [
                'total_points' => $point->user ? $point->user->pointHistories()->sum('points') : $point->points,
                'point_details' => json_encode($point->toArray()),
                'last_synced_at' => now(),
                'updated_at' => now(),
            ]
        );
}

  public function syncQr(UserQrCode $qr): void
{
    $userUuid = $qr->user?->user_uuid ?? $qr->user_uuid ?? 'unknown';
    DB::connection($this->connection)
        ->table('protected_qr_codes')
        ->updateOrInsert(
            ['qr_uuid' => $qr->qr_uuid],
            [
                'user_uuid' => $userUuid,
                'qr_token' => $qr->token,
                'is_active' => $qr->is_active,
                'qr_data' => json_encode($qr->toArray()),
                'last_synced_at' => now(),
                'updated_at' => now(),
            ]
        );
}
    protected function syncTransaction(PointHistory $tx): void
    {
        $userUuid = $tx->user?->user_uuid ?? 'unknown';
        DB::connection($this->connection)
            ->table('protected_transactions')
            ->insert([
                'user_uuid' => $userUuid,
                'points' => $tx->points,
                'type' => $tx->type ?? 'point',
                'description' => $tx->description ?? $tx->note,
                'transaction_date' => $tx->created_at,
                'extra_data' => json_encode($tx->toArray()),
                'last_synced_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    protected function syncActivityLog(AuditLog $log): void
    {
        DB::connection($this->connection)
            ->table('protected_activity_logs')
            ->insert([
                'user_uuid' => $log->user?->user_uuid ?? null,
                'action' => $log->action,
                'description' => $log->description,
                'metadata' => json_encode($log->toArray()),
                'logged_at' => $log->created_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Pulihkan semua data dari protected database ke main database
     */
    public function restoreAllFromProtected(): int
    {
        $this->ensureTablesExist();

        $count = 0;
        $protectedUsers = DB::connection($this->connection)
            ->table('protected_users')
            ->orderBy('id')
            ->get();

        DB::beginTransaction();
        try {
            foreach ($protectedUsers as $protected) {
                $data = json_decode($protected->full_data, true);
                if (!$data) continue;

                // Cari user berdasarkan UUID atau email
                $user = User::withTrashed()->where('user_uuid', $protected->user_uuid)->first()
                         ?? User::withTrashed()->where('email', $protected->email)->first();

                if ($user) {
                    // Update data yang ada, JANGAN reset password/pin
                    $fillable = array_intersect_key($data, array_flip((new User())->getFillable()));
                    unset($fillable['password'], $fillable['pin']);
                    $user->update($fillable);
                    if ($user->trashed()) {
                        $user->restore();
                    }
                } else {
                    // Buat user baru dari snapshot
                    unset($data['password'], $data['pin']);
                    $data['password'] = bcrypt('default_password_change_me');
                    $data['pin'] = null;
                    $user = User::create($data);
                }

                $count++;
            }

            // Restore poin, QR, dll. bisa ditambahkan di sini

            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Simpan backup ke file JSON
     */
    public function createFileBackup(): string
    {
        $data = [
            'metadata' => [
                'timestamp' => now()->toDateTimeString(),
                'app_version' => app()->version(),
                'environment' => app()->environment(),
            ],
            'users' => User::withTrashed()->get()->toArray(),
            'points' => PointHistory::all()->toArray(),
            'qrs' => UserQrCode::withTrashed()->get()->toArray(),
        ];

        $filename = 'backup_' . now()->format('Ymd_His') . '.json';
        $path = 'backups/' . $filename;
        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Hapus backup lama (simpan 30 terakhir)
        $files = Storage::files('backups');
        if (count($files) > 30) {
            $oldest = collect($files)->sort()->take(count($files) - 30);
            foreach ($oldest as $old) {
                Storage::delete($old);
            }
        }

        return $path;
    }

    /**
     * Restore dari file backup
     */
    public function restoreFromFile(string $filename): int
    {
        $content = Storage::get('backups/' . $filename);
        $data = json_decode($content, true);

        if (!$data) {
            throw new \Exception('File backup tidak valid atau rusak.');
        }

        DB::beginTransaction();
        try {
            $count = 0;

            // Restore users
            foreach ($data['users'] as $userData) {
                $user = User::withTrashed()->where('user_uuid', $userData['user_uuid'])->first();
                if ($user) {
                    $fillable = array_intersect_key($userData, array_flip((new User())->getFillable()));
                    unset($fillable['password'], $fillable['pin']);
                    $user->update($fillable);
                    if ($user->trashed()) $user->restore();
                } else {
                    unset($userData['password'], $userData['pin']);
                    $userData['password'] = bcrypt('default_password_change_me');
                    User::create($userData);
                }
                $count++;
            }

            // Restore points, qr, dll. (bisa ditambahkan)

            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Dapatkan daftar backup yang tersedia
     */
    public function getBackupList(): array
    {
        $files = Storage::files('backups');
        $list = [];

        foreach ($files as $file) {
            $list[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => Storage::size($file),
                'modified' => Storage::lastModified($file),
                'url' => Storage::url($file),
            ];
        }

        // Urutkan dari yang terbaru
        usort($list, fn($a, $b) => $b['modified'] <=> $a['modified']);

        return $list;
    }
}