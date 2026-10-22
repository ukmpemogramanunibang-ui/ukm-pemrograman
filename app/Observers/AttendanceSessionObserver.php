<?php

namespace App\Observers;

use App\Models\AttendanceSession;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Log;

class AttendanceSessionObserver
{
    public function __construct(protected NotificationService $svc) {}

    /* ==========================================================
     | CREATED — sesi absensi baru dibuat
     * ========================================================== */
    public function created(AttendanceSession $session): void
    {
        try {
            $this->svc->syncEntity($session, 'created', $this->payload($session, 'created'));
        } catch (\Throwable $e) {
            Log::warning('[AttendanceSessionObserver] created failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /* ==========================================================
     | UPDATED — status / jadwal / window berubah
     * ========================================================== */
    public function updated(AttendanceSession $session): void
    {
        // Skip kalau hanya kolom "noise" yang berubah
        $dirtyKeys  = array_keys($session->getDirty());
        $meaningful = array_diff($dirtyKeys, [
            'updated_at',
            'updated_by',
            'last_seen_at',
        ]);

        if (empty($meaningful)) return;

        try {
            $this->svc->syncEntity($session, 'updated', $this->payload($session, 'updated'));
        } catch (\Throwable $e) {
            Log::warning('[AttendanceSessionObserver] updated failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /* ==========================================================
     | DELETED
     * ========================================================== */
    public function deleted(AttendanceSession $session): void
    {
        try {
            $this->svc->syncEntity($session, 'deleted');
        } catch (\Throwable $e) {
            Log::warning('[AttendanceSessionObserver] deleted failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /* ==========================================================
     | RESTORED
     * ========================================================== */
    public function restored(AttendanceSession $session): void
    {
        try {
            $this->svc->syncEntity($session, 'restored', $this->payload($session, 'restored'));
        } catch (\Throwable $e) {
            Log::warning('[AttendanceSessionObserver] restored failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /* ==========================================================
     | PAYLOAD BUILDER
     * ========================================================== */
    protected function payload(AttendanceSession $session, string $event): array
    {
        $roomTitle   = optional($session->room)->name ?? 'Ruangan';
        $sessionName = $session->title ?? ('Sesi #' . $session->id);

        $title = match ($event) {
            'created'  => '📋 Sesi Absensi Baru',
            'updated'  => '📋 Sesi Absensi (Diperbarui)',
            'restored' => '📋 Sesi Absensi (Dipulihkan)',
            default    => '📋 Sesi Absensi',
        };

        $message = match ($event) {
            'created'  => "Sesi \"{$sessionName}\" dibuka untuk {$roomTitle}.",
            'updated'  => "Sesi \"{$sessionName}\" diubah (status: {$session->session_status}).",
            'restored' => "Sesi \"{$sessionName}\" dipulihkan.",
            default    => "Sesi \"{$sessionName}\".",
        };

        return [
            'title'           => $title,
            'message'         => $message,
            'source'          => 'attendance',
            'priority'        => $this->priority($session),
            'action_url'      => $this->safeRoute('owner.attendance.show', $session->id),
            'status'          => $session->session_status,
            'requires_action' => $session->session_status === 'scheduled',
        ];
    }

    protected function priority(AttendanceSession $session): string
    {
        return match ($session->session_status) {
            'open'      => 'high',
            'scheduled' => 'normal',
            'cancelled' => 'urgent',
            default     => 'normal',
        };
    }

    /**
     * Route helper yang aman — kalau route belum ada, return null.
     */
    protected function safeRoute(string $name, $param = null): ?string
    {
        try {
            if (! \Illuminate\Support\Facades\Route::has($name)) {
                return null;
            }
            return $param ? route($name, $param) : route($name);
        } catch (\Throwable $e) {
            return null;
        }
    }
}