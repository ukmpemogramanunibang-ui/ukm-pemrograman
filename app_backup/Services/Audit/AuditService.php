<?php

namespace App\Services\Audit;

use App\Models\AuditAlert;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditService
{
    protected static ?string $correlationId = null;

    public static function withCorrelation(?string $id): void { self::$correlationId = $id; }

    public static function correlationId(): string
    {
        return self::$correlationId ??= 'REQ-' . strtoupper(Str::random(8));
    }

    public static function resetCorrelation(): void { self::$correlationId = null; }

    /**
     * Rekam satu event audit.
     */
    public static function record(array $data): AuditLog
    {
        return DB::transaction(function () use ($data) {
            $req   = request();
            $actor = Auth::user();

            $actorType = $data['actor_type'] ?? ($actor ? 'user' : 'system');
            $actorId   = $data['actor_id']   ?? optional($actor)->id;
            $actorName = $data['actor_name'] ?? optional($actor)->name
                         ?? ($actorType === 'system' ? 'SYSTEM' : 'Unknown');

            $before = $data['before'] ?? null;
            $after  = $data['after']  ?? null;
            $change = self::diff($before, $after);

            $previousHash = AuditLog::query()->orderByDesc('id')->value('integrity_hash');

            $log = new AuditLog();
            $log->fill([
                'actor_type'           => $actorType,
                'actor_id'             => $actorId,
                'actor_name'           => $actorName,
                'actor_role'           => $data['actor_role']     ?? null,
                'actor_position'       => $data['actor_position'] ?? null,
                'target_user_id'       => $data['target_user_id'] ?? null,
                'action'               => $data['action'],
                'category'             => $data['category']       ?? self::inferCategory($data['action']),
                'severity'             => $data['severity']       ?? 'info',
                'result'               => $data['result']         ?? 'success',
                'auditable_type'       => $data['auditable_type'] ?? null,
                'auditable_id'         => $data['auditable_id']   ?? null,
                'session_id'           => $data['session_id']     ?? null,
                'group_id'             => $data['group_id']       ?? null,
                'room_id'              => $data['room_id']        ?? null,
                'description'          => $data['description']    ?? null,
                'before_data'          => $before,
                'after_data'           => $after,
                'change_data'          => $change,
                'reason'               => $data['reason']         ?? null,
                'permission'           => $data['permission']     ?? null,
                'authorization_result' => $data['authorization_result'] ?? null,
                'source'               => $data['source']         ?? 'web',
                'ip_address'           => $req?->ip(),
                'user_agent'           => substr((string) $req?->userAgent(), 0, 1000),
                'device_id'            => $data['device_id']      ?? null,
                'request_id'           => $data['request_id']     ?? $req?->header('X-Request-Id'),
                'correlation_id'       => $data['correlation_id'] ?? self::correlationId(),
                'affected_count'       => $data['affected_count'] ?? 0,
                'affected_ids'         => $data['affected_ids']   ?? null,
                'previous_hash'        => $previousHash,
                'is_sensitive'         => (bool) ($data['is_sensitive'] ?? self::isSensitive($data['action'])),
                'user_id'              => $actorId,
            ]);

            $log->integrity_hash = $log->computeHash();
            $log->save();

            self::detectAnomalies($log);

            return $log;
        });
    }

    public static function diff(?array $before, ?array $after): ?array
    {
        if (!$before && !$after) return null;
        if (!$before) return $after;
        if (!$after)  return $before;

        $changed = [];
        foreach (array_unique(array_merge(array_keys($before), array_keys($after))) as $key) {
            $b = $before[$key] ?? null;
            $a = $after[$key]  ?? null;
            if ($b !== $a) $changed[$key] = ['before' => $b, 'after' => $a];
        }
        return $changed ?: null;
    }

    protected static function inferCategory(string $action): string
    {
        return match (strtolower(explode('_', $action)[0] ?? '')) {
            'session'    => 'session',
            'attendance' => 'attendance',
            'permission' => 'permission',
            'point'      => 'point',
            'penalty'    => 'penalty',
            'security'   => 'security',
            default      => 'system',
        };
    }

    protected static function isSensitive(string $action): bool
    {
        return in_array($action, [
            'SESSION_RESCHEDULED','SESSION_CANCELLED','SESSION_EXTENDED',
            'ATTENDANCE_CORRECTED','ATTENDANCE_OVERRIDE',
            'POINT_CORRECTED','POINT_REVERSED','POINT_GRANTED',
            'PENALTY_CANCELLED','PENALTY_DISPUTED',
            'PERMISSION_GRANTED','PERMISSION_REVOKED','ROOM_OVERRIDE',
        ], true);
    }

    protected static function detectAnomalies(AuditLog $log): void
    {
        // Mass override
        if ($log->affected_count >= 20
            && in_array($log->action, ['ATTENDANCE_OVERRIDE','ATTENDANCE_CORRECTED'], true)) {
            AuditAlert::create([
                'type'     => 'mass_override',
                'severity' => 'critical',
                'title'    => 'Mass Override Terdeteksi',
                'message'  => "{$log->actor_name} mengubah {$log->affected_count} record sekaligus.",
                'context'  => ['action' => $log->action, 'actor_id' => $log->actor_id],
                'related_event_uuid' => $log->event_uuid,
            ]);
        }

        // Rate-limited failures
        if (in_array($log->result, ['failed','denied','rate_limited'], true)) {
            $recent = AuditLog::query()
                ->where('actor_id', $log->actor_id)
                ->where('action', $log->action)
                ->whereIn('result', ['failed','denied','rate_limited'])
                ->where('created_at', '>=', now()->subMinutes(2))
                ->count();

            if ($recent >= 5) {
                AuditAlert::create([
                    'type'     => 'security_anomaly',
                    'severity' => 'security',
                    'title'    => 'Percobaan Berulang Gagal',
                    'message'  => "{$recent}× {$log->action} gagal oleh {$log->actor_name} dalam 2 menit.",
                    'context'  => ['ip' => $log->ip_address, 'action' => $log->action],
                    'related_event_uuid' => $log->event_uuid,
                ]);
            }
        }
    }

    public static function verifyChain(int $limit = 1000): array
    {
        $logs = AuditLog::query()->orderBy('id')->limit($limit)->get(['id','previous_hash','integrity_hash','event_uuid','action','actor_id','auditable_type','auditable_id','before_data','after_data','reason','created_at']);
        $prev = null;
        $broken = [];

        foreach ($logs as $log) {
            if ($log->previous_hash !== $prev) {
                $broken[] = ['id' => $log->id, 'reason' => 'previous_hash mismatch'];
            }
            if (!$log->isIntegrityValid()) {
                $broken[] = ['id' => $log->id, 'reason' => 'integrity_hash mismatch'];
            }
            $prev = $log->integrity_hash;
        }

        return ['ok' => empty($broken), 'checked' => $logs->count(), 'broken' => $broken];
    }
}