<?php

namespace App\Services\Attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Single source of truth untuk waktu sistem.
 *
 * Semua keputusan valid/tidak absensi HARUS lewat service ini.
 * Tidak ada now() langsung di controller untuk keputusan kritis.
 */
class AttendanceClockService
{
    /** Toleransi drift maksimum (detik) sebelum dianggap suspicious */
    public const DRIFT_WARNING  = 2;
    public const DRIFT_CRITICAL = 10;

    /**
     * Waktu server authoritative.
     */
    public function now(): Carbon
    {
        return Carbon::now('Asia/Jakarta');
    }

    public function nowIso(): string
    {
        return $this->now()->toIso8601String();
    }

    public function unixMs(): int
    {
        return (int) round(microtime(true) * 1000);
    }

    /**
     * Kirim waktu server + zona waktu ke client untuk sinkronisasi.
     */
    public function syncPayload(): array
    {
        return [
            'server_unix_ms' => $this->unixMs(),
            'server_time'    => $this->nowIso(),
            'timezone'       => 'Asia/Jakarta',
            'timestamp'      => $this->now()->toDateTimeString(),
        ];
    }

    /**
     * Deteksi drift clock antara client dan server.
     *
     * @param  int|null  $clientUnixMs  Unix ms dari device
     * @return array{offset_ms:int,status:string,drift_seconds:float}
     */
    public function detectDrift(?int $clientUnixMs): array
    {
        if (!$clientUnixMs) {
            return ['offset_ms' => 0, 'status' => 'unknown', 'drift_seconds' => 0.0];
        }

        $serverMs = $this->unixMs();
        $offsetMs = $clientUnixMs - $serverMs;
        $driftSec = abs($offsetMs) / 1000;

        $status = match (true) {
            $driftSec < self::DRIFT_WARNING  => 'synced',
            $driftSec < self::DRIFT_CRITICAL => 'drifting',
            default                          => 'critical',
        };

        return [
            'offset_ms'     => $offsetMs,
            'status'        => $status,
            'drift_seconds' => round($driftSec, 3),
        ];
    }

    /**
     * Verifikasi bahwa waktu client masih dalam toleransi.
     * Untuk attendance submission.
     */
    public function isClientTimeTrustworthy(?int $clientUnixMs): bool
    {
        $drift = $this->detectDrift($clientUnixMs);
        return $drift['status'] !== 'critical';
    }

    /**
     * Cek apakah saat ini berada dalam window absensi.
     * SERVER AUTHORITATIVE — tidak peduli client bilang apa.
     */
    public function isWithinWindow(Carbon $openAt, Carbon $closeAt): bool
    {
        $now = $this->now();
        return $now->greaterThanOrEqualTo($openAt) && $now->lessThan($closeAt);
    }

    /**
     * Sisa waktu dalam detik sebelum window close.
     * Negatif = sudah lewat.
     */
    public function secondsUntil(Carbon $moment): int
    {
        return (int) $this->now()->diffInSeconds($moment, false);
    }
}