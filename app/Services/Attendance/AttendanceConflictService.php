<?php

namespace App\Services\Attendance;

use App\Models\AttendanceSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Deteksi konflik jadwal ruangan dengan interval overlap.
 *
 * Sebelumnya: grup by room_id yang sama → SALAH
 * Sekarang: cek overlap [start, end)
 */
class AttendanceConflictService
{
    /**
     * Cek apakah interval [aStart, aEnd) overlap dengan [bStart, bEnd).
     * Half-open interval: end exclusive.
     */
    public function intervalsOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lessThan($bEnd) && $aEnd->greaterThan($bStart);
    }

    /**
     * Cari semua konflik untuk session yang diberikan.
     *
     * @return Collection<AttendanceSession>
     */
    public function findConflicts(AttendanceSession $session, ?Carbon $overrideStart = null, ?Carbon $overrideEnd = null): Collection
    {
        if (!$session->room_id) {
            return collect(); // Online session — tidak ada konflik ruangan
        }

        $start = $overrideStart ?? $session->scheduled_start_at;
        $end   = $overrideEnd   ?? $session->scheduled_end_at;

        if (!$start || !$end) {
            return collect();
        }

        return AttendanceSession::where('id', '!=', $session->id)
            ->where('room_id', $session->room_id)
            ->whereIn('session_status', ['scheduled', 'open', 'extended', 'paused'])
            ->where(function ($q) use ($start, $end) {
                // Interval overlap: existing_start < new_end AND existing_end > new_start
                $q->where('scheduled_start_at', '<', $end)
                  ->where('scheduled_end_at',   '>', $start);
            })
            ->get();
    }

    /**
     * Pre-flight check: apakah sesi baru boleh dibuat di ruangan & waktu ini?
     */
    public function assertNoConflict(AttendanceSession $session): void
    {
        $conflicts = $this->findConflicts($session);

        if ($conflicts->isNotEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'scheduled_start_at' => "Konflik jadwal: {$conflicts->pluck('title')->join(', ')}",
            ]);
        }
    }

    /**
     * Cari semua konflik di seluruh sistem (untuk alert engine).
     */
    public function detectAllConflicts(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $from = $from ?? now()->startOfDay();
        $to   = $to   ?? now()->addDays(7);

        $sessions = AttendanceSession::whereIn('session_status', ['scheduled','open','extended','paused'])
            ->whereNotNull('room_id')
            ->where('scheduled_start_at', '<', $to)
            ->where('scheduled_end_at',   '>', $from)
            ->orderBy('room_id')
            ->orderBy('scheduled_start_at')
            ->get();

        $conflicts = collect();

        foreach ($sessions->groupBy('room_id') as $roomId => $roomSessions) {
            $sorted = $roomSessions->sortBy('scheduled_start_at')->values();

            for ($i = 0; $i < $sorted->count(); $i++) {
                for ($j = $i + 1; $j < $sorted->count(); $j++) {
                    $a = $sorted[$i];
                    $b = $sorted[$j];

                    // Early exit: karena sudah sorted, jika b.start >= a.end, tidak ada overlap lagi
                    if ($b->scheduled_start_at->greaterThanOrEqualTo($a->scheduled_end_at)) {
                        break;
                    }

                    if ($this->intervalsOverlap(
                        $a->scheduled_start_at, $a->scheduled_end_at,
                        $b->scheduled_start_at, $b->scheduled_end_at
                    )) {
                        $conflicts->push([
                            'room_id' => $roomId,
                            'sessions' => [$a, $b],
                        ]);
                    }
                }
            }
        }

        return $conflicts;
    }
}