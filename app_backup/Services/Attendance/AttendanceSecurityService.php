<?php

namespace App\Services\Attendance;

use App\Models\User;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Illuminate\Support\Collection;

/**
 * Security & Fraud Detection Layer.
 *
 * Menghasilkan:
 *  - verification_meta (IP, device, geolocation, dsb)
 *  - confidence_score (0–100)
 *  - risk_score (0–100)
 *  - fraud_flags
 */
class AttendanceSecurityService
{
    public function __construct(protected AttendanceClockService $clock) {}

    /**
     * Bangun verification meta dari request + actor.
     */
    public function evaluateContext(User $actor, array $ctx = []): array
    {
        $drift = $this->clock->detectDrift($ctx['client_unix_ms'] ?? null);

        return [
            'device_id'       => $ctx['device_id'] ?? null,
            'device_type'     => $ctx['device_type'] ?? 'unknown',
            'ip_address'      => request()?->ip(),
            'user_agent'      => request()?->userAgent(),
            'client_unix_ms'  => $ctx['client_unix_ms'] ?? null,
            'server_unix_ms'  => $this->clock->unixMs(),
            'clock_drift_ms'  => $drift['offset_ms'],
            'clock_status'    => $drift['status'],
            'geo'             => $ctx['geo'] ?? null,
            'evaluated_at'    => $this->clock->nowIso(),
        ];
    }

    /**
     * Hitung confidence score untuk satu record.
     *
     * Skala: 0 (sangat tidak yakin) — 100 (verified)
     */
    public function computeConfidence(AttendanceRecord $record, array $meta): array
    {
        $score = 0;
        $flags = [];

        // Method validation
        $methodScore = match ($record->method) {
            'nfc'              => 40,
            'qr'               => 30,
            'manual_by_officer'=> 20,
            'auto_system'      => 25,
            'manual'           => 15,
            'historical'       => 10,
            default            => 5,
        };
        $score += $methodScore;

        // Clock drift
        $drift = abs($meta['clock_drift_ms'] ?? 0);
        if ($drift < 2000) {
            $score += 20;
        } elseif ($drift < 5000) {
            $score += 10;
            $flags[] = 'clock_drift_minor';
        } else {
            $flags[] = 'clock_drift_major';
        }

        // Device trust
        if (!empty($meta['device_id'])) {
            $score += 15;
        } else {
            $flags[] = 'no_device_id';
        }

        // IP & UA present
        if (!empty($meta['ip_address'])) $score += 5;
        if (!empty($meta['user_agent'])) $score += 5;

        // Geolocation (jika ada)
        if (!empty($meta['geo']['latitude'])) {
            $score += 15;
        }

        // Timing check (apakah di window yang wajar?)
        if ($record->session_id && $record->recorded_at) {
            $session = AttendanceSession::find($record->session_id);
            if ($session && $session->attendance_open_at && $session->attendance_close_at) {
                $inWindow = $record->recorded_at->between(
                    $session->attendance_open_at,
                    $session->attendance_close_at
                );
                if ($inWindow) $score += 10;
                else $flags[] = 'outside_window';
            }
        }

        return [
            'confidence_score' => min(100, max(0, $score)),
            'fraud_flags'      => $flags,
        ];
    }

    /**
     * Hitung risk score berdasarkan pola.
     */
    public function computeRisk(User $user, AttendanceSession $session): array
    {
        $flags = [];
        $risk  = 0;

        // Duplicate scan dalam < 60 detik
        $recent = AttendanceRecord::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->where('created_at', '>=', $this->clock->now()->subSeconds(60))
            ->count();

        if ($recent > 1) {
            $risk += 30;
            $flags[] = 'duplicate_scan';
        }

        // User punya record di sesi lain yang overlap waktu
        $overlap = AttendanceRecord::where('user_id', $user->id)
            ->where('session_id', '!=', $session->id)
            ->whereHas('session', function ($q) use ($session) {
                $q->where('attendance_open_at', '<=', $session->attendance_close_at)
                  ->where('attendance_close_at', '>=', $session->attendance_open_at);
            })
            ->count();

        if ($overlap > 0) {
            $risk += 25;
            $flags[] = 'time_overlap_other_session';
        }

        // Suspicious device sharing (10 user same device in 1 jam)
        $device = request()?->header('X-Device-Id');
        if ($device) {
            $sharedDevice = AttendanceRecord::where('device_identifier', $device)
                ->where('created_at', '>=', $this->clock->now()->subHour())
                ->distinct('user_id')
                ->count('user_id');

            if ($sharedDevice > 5) {
                $risk += 40;
                $flags[] = 'device_shared_heavily';
            }
        }

        // Suspicious IP
        $ip = request()?->ip();
        if ($ip) {
            $sharedIp = AttendanceRecord::where('ip_address', $ip)
                ->where('created_at', '>=', $this->clock->now()->subHour())
                ->distinct('user_id')
                ->count('user_id');

            if ($sharedIp > 15) {
                $risk += 35;
                $flags[] = 'ip_shared_heavily';
            }
        }

        return [
            'risk_score'  => min(100, $risk),
            'fraud_flags' => $flags,
            'level'       => match (true) {
                $risk >= 80 => 'BLOCK',
                $risk >= 50 => 'SUSPICIOUS',
                $risk >= 20 => 'REVIEW',
                default     => 'NORMAL',
            },
        ];
    }

    /**
     * Verifikasi apakah actor boleh membuka/mengubah sesi.
     * Cek jam kerja, role, IP internal, dsb.
     */
    public function canActorOperate(User $actor, string $operation): array
    {
        $flags = [];

        // Cek role minimal
        $role = strtolower($actor->level?->slug ?? $actor->level?->name ?? 'guest');
        $allowed = match ($operation) {
            'open', 'close', 'extend', 'cancel' => ['owner', 'superadmin', 'ketua'],
            'pause', 'resume'                   => ['owner', 'superadmin', 'ketua', 'pengurus'],
            default                             => ['owner', 'superadmin', 'ketua', 'pengurus'],
        };

        if (!in_array($role, $allowed, true)) {
            return ['allowed' => false, 'flags' => ['insufficient_role']];
        }

        return ['allowed' => true, 'flags' => $flags];
    }
}