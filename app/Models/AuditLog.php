<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $table = 'audit_logs';

    protected $fillable = [
        'event_uuid', 'actor_type', 'actor_id', 'actor_name', 'actor_role', 'actor_position',
        'target_user_id', 'action', 'category', 'severity', 'result',
        'auditable_type', 'auditable_id', 'session_id', 'group_id', 'room_id',
        'description', 'before_data', 'after_data', 'change_data', 'reason',
        'permission', 'authorization_result', 'source', 'ip_address', 'user_agent', 'device_id',
        'request_id', 'correlation_id', 'affected_count', 'affected_ids',
        'previous_hash', 'integrity_hash', 'is_sensitive', 'user_id',
    ];

    protected $casts = [
        'before_data'  => 'array',
        'after_data'   => 'array',
        'change_data'  => 'array',
        'affected_ids' => 'array',
        'is_sensitive' => 'boolean',
        'created_at'   => 'datetime',
    ];

    public const CATEGORIES = [
        'session'    => 'Sesi Absensi',
        'attendance' => 'Kehadiran',
        'permission' => 'Izin',
        'point'      => 'Poin',
        'penalty'    => 'Denda',
        'security'   => 'Keamanan',
        'system'     => 'Sistem',
    ];

    public const SEVERITIES = [
        'info'     => ['label' => 'Info',     'color' => 'sky',     'weight' => 1],
        'notice'   => ['label' => 'Notice',   'color' => 'blue',    'weight' => 2],
        'warning'  => ['label' => 'Warning',  'color' => 'amber',   'weight' => 3],
        'high'     => ['label' => 'High',     'color' => 'orange',  'weight' => 4],
        'critical' => ['label' => 'Critical', 'color' => 'red',     'weight' => 5],
        'security' => ['label' => 'Security', 'color' => 'fuchsia', 'weight' => 6],
    ];

    public const RESULTS = [
        'success'           => ['label' => 'Berhasil',       'color' => 'emerald', 'icon' => 'fa-check'],
        'failed'            => ['label' => 'Gagal',          'color' => 'red',     'icon' => 'fa-xmark'],
        'denied'            => ['label' => 'Ditolak',        'color' => 'rose',    'icon' => 'fa-ban'],
        'blocked'           => ['label' => 'Diblokir',       'color' => 'orange',  'icon' => 'fa-shield'],
        'validation_failed' => ['label' => 'Validasi Gagal', 'color' => 'amber',   'icon' => 'fa-triangle-exclamation'],
        'rate_limited'      => ['label' => 'Rate Limited',   'color' => 'yellow',  'icon' => 'fa-hourglass'],
    ];

    public const SOURCES = [
        'web' => 'Web', 'nfc' => 'NFC Reader', 'barcode' => 'Barcode',
        'manual' => 'Manual', 'api' => 'API', 'system' => 'System',
        'scheduler' => 'Scheduler', 'queue' => 'Queue',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $log->event_uuid ??= (string) Str::uuid();
            $log->created_at ??= now();
        });
    }

    /* ---------- Relations ---------- */
    public function user()    { return $this->belongsTo(User::class, 'actor_id'); }
    public function actor()   { return $this->belongsTo(User::class, 'actor_id'); }
    public function target()  { return $this->belongsTo(User::class, 'target_user_id'); }
    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function room()    { return $this->belongsTo(Room::class, 'room_id'); }

    /* ---------- Scopes ---------- */
    public function scopeCritical(Builder $q): Builder
    {
        return $q->whereIn('severity', ['critical', 'security']);
    }

    public function scopeFailed(Builder $q): Builder
    {
        return $q->whereIn('result', ['failed', 'denied', 'blocked', 'validation_failed', 'rate_limited']);
    }

    public function scopeBetweenDates(Builder $q, $from, $to): Builder
    {
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);
        return $q;
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        if ($term === '') return $q;
        $like = '%' . $term . '%';
        return $q->where(function ($qq) use ($like) {
            $qq->where('description', 'like', $like)
               ->orWhere('actor_name', 'like', $like)
               ->orWhere('action', 'like', $like)
               ->orWhere('reason', 'like', $like)
               ->orWhere('ip_address', 'like', $like)
               ->orWhere('correlation_id', 'like', $like)
               ->orWhere('request_id', 'like', $like);
        });
    }

    /* ---------- Accessors ---------- */
    public function getSeverityMetaAttribute(): array
    {
        return self::SEVERITIES[$this->severity] ?? self::SEVERITIES['info'];
    }

    public function getResultMetaAttribute(): array
    {
        return self::RESULTS[$this->result] ?? self::RESULTS['success'];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? ucfirst((string) $this->source);
    }

    public function hasChange(): bool
    {
        return is_array($this->change_data) && !empty($this->change_data);
    }

    public function changedFields(): array
    {
        return $this->hasChange() ? array_keys($this->change_data) : [];
    }

    /* ---------- Integrity ---------- */
    public function computeHash(): string
    {
        return hash('sha256', json_encode([
            'event_uuid' => $this->event_uuid,
            'action'     => $this->action,
            'actor_id'   => $this->actor_id,
            'auditable_type' => $this->auditable_type,
            'auditable_id'   => $this->auditable_id,
            'before'     => $this->before_data,
            'after'      => $this->after_data,
            'reason'     => $this->reason,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'prev'       => $this->previous_hash,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function isIntegrityValid(): bool
    {
        return $this->integrity_hash && hash_equals($this->integrity_hash, $this->computeHash());
    }
}