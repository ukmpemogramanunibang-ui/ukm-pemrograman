<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LmsItem extends Model
{
    use SoftDeletes;

    protected $table = 'lms_items';
protected $fillable = [
    'uuid',
    'group_id',
    'type',
    'title',
    'description',

    'attachments',
    'attachment_meta',
    'video_url',
    'video_provider',

    'created_by',
    'published_at',
    'deadline_at',
    'deadline_mode',       
    'timezone',
 'cover_path',      
    'cover_images',   
    'cover_meta',      

    'target_mode',
    'target_roles',
    'target_user_ids',
    'recipients_count',
    'recipient_count',

    'status',
    'visibility',
    'allow_resubmit',
    'requires_review',

    'archived_at',
    'archived_by',
    'deleted_by',
    'delete_reason',
    'organization_id',
];

protected $casts = [
     'cover_images'   => 'array',   
    'cover_meta'     => 'array',   
    'attachments'      => 'array',
    'attachment_meta'  => 'array',   
    'target_roles'     => 'array',
    'target_user_ids'  => 'array',

    'published_at'     => 'datetime',
    'deadline_at'      => 'datetime',
    'archived_at'      => 'datetime',

    'allow_resubmit'   => 'boolean',
    'requires_review'  => 'boolean',

    'recipients_count' => 'integer',
    'recipient_count'  => 'integer',
];
    /* =========================================================
     | BOOT — Auto uuid
     * ========================================================= */
 protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->uuid ??= (string) Str::uuid();
            if (empty($m->organization_id)) {
                $m->organization_id = \App\Services\OrganizationContext::id();
            }
        });

        static::addGlobalScope('organization', function ($builder) {
            $orgId = \App\Services\OrganizationContext::id();
            if ($orgId) {
                $builder->where('lms_items.organization_id', $orgId);
            }
        });
    }
public function hasUnlimitedDeadline(): bool
{
    return $this->deadline_mode === 'unlimited' || is_null($this->deadline_at);
}

public function deadlineLabel(): string
{
    if ($this->hasUnlimitedDeadline()) {
        return $this->isTask() ? 'Tanpa batas waktu' : 'Tanpa batas akses';
    }
    return $this->deadline_at->isoFormat('D MMM Y · HH:mm') . ' WIB';
}
    /* =========================================================
     | RELATIONS
     * ========================================================= */
    public function group()
    {
        return $this->belongsTo(LmsGroup::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targets()
    {
        return $this->hasMany(LmsItemTarget::class, 'item_id');
    }

    public function recipients()
    {
        return $this->hasMany(LmsItemUser::class, 'item_id');
    }

    public function submissions()
    {
        return $this->hasMany(LmsSubmission::class, 'item_id');
    }

    /* =========================================================
     | HELPERS
     * ========================================================= */
    public function isTask(): bool
    {
        return $this->type === 'task';
    }

    public function isMaterial(): bool
    {
        return $this->type === 'material';
    }

    public function isOverdue(): bool
    {
        return $this->isTask()
            && $this->deadline_at
            && now()->gt($this->deadline_at);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /* =========================================================
     | SCOPES
     * ========================================================= */
    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeTasks($q)
    {
        return $q->where('type', 'task');
    }

    public function scopeMaterials($q)
    {
        return $q->where('type', 'material');
    }

    /* =========================================================
     | QUERY HELPERS
     * ========================================================= */
    public function hasUser(User $user): bool
    {
        return $this->recipients()->where('user_id', $user->id)->exists();
    }
/**
 * URL cover — baca dari semua kolom yang mungkin terisi.
 * Prioritas: cover_path → cover → cover_images[0] → cover_images[0]['path']
 */
public function getCoverUrlAttribute(): ?string
{
    // 1️⃣ Coba cover_path
    $path = $this->cover_path ?? null;

    // 2️⃣ Fallback ke kolom 'cover'
    if (! $path && isset($this->attributes['cover'])) {
        $path = $this->attributes['cover'];
    }

    // 3️⃣ Fallback ke cover_images (bisa string / array)
    if (! $path && ! empty($this->cover_images)) {
        $ci = $this->cover_images;
        if (is_string($ci)) {
            $decoded = json_decode($ci, true);
            $path = is_array($decoded) ? ($decoded[0] ?? null) : $ci;
        } elseif (is_array($ci)) {
            $first = $ci[0] ?? null;
            $path = is_array($first) ? ($first['path'] ?? null) : $first;
        }
    }

    if (! $path) return null;

    // Kalau path sudah URL absolute atau leading slash, kembalikan as-is
    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])) {
        return $path;
    }

    // Path relatif → storage URL
    return asset('storage/' . ltrim($path, '/'));
}
/**
 * Metric monitoring per item (dipakai halaman /owner/lms/monitoring).
 * Semua angka dihitung dari tabel lms_item_users.
 *
 * Return keys:
 *   total, viewed, started, submitted, completed, late, missing
 */
public function getMetricAttribute(): array
{
    // ═══════════════════════════════════════════════════════
    // FAST PATH — kalau controller sudah preload (withCount)
    // ═══════════════════════════════════════════════════════
    if (array_key_exists('metric_total', $this->attributes)) {
        $total     = (int) $this->attributes['metric_total'];
        $viewed    = (int) $this->attributes['metric_viewed'];
        $started   = (int) $this->attributes['metric_started'];
        $submitted = (int) $this->attributes['metric_submitted'];
        $completed = (int) $this->attributes['metric_completed'];
        $late      = (int) $this->attributes['metric_late'];

        return [
            'total'     => $total,
            'viewed'    => $viewed,
            'started'   => $started,
            'submitted' => $submitted,
            'completed' => $completed,
            'late'      => $late,
            'missing'   => max(0, $total - $submitted),
        ];
    }

    // ═══════════════════════════════════════════════════════
    // SLOW PATH — fallback kalau dipanggil tanpa preload
    // (N+1, tapi tetap benar)
    // ═══════════════════════════════════════════════════════
    $base = $this->recipients();

    $total     = (clone $base)->count();
    $viewed    = (clone $base)->whereNotNull('viewed_at')->count();
    $started   = (clone $base)->whereNotNull('started_at')->count();
    $submitted = (clone $base)->whereNotNull('submitted_at')->count();
    $completed = (clone $base)->whereIn('status', ['completed', 'done'])->count();
    $late      = (clone $base)->where('status', 'late')->count();

    return [
        'total'     => $total,
        'viewed'    => $viewed,
        'started'   => $started,
        'submitted' => $submitted,
        'completed' => $completed,
        'late'      => $late,
        'missing'   => max(0, $total - $submitted),
    ];
}
}