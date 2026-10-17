<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'announcements';

    /**
     * Mapping kolom DB Anda:
     *   title        = judul
     *   content      = isi
     *   created_by   = pembuat
     */
    protected $fillable = [
        'title',
        'content',
        'priority',
        'target_roles',
        'action_url',
        'read_count',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'published_at' => 'datetime',
        'read_count'   => 'integer',
    ];

    /* ==========================================================
     | BOOT — auto publish + auto isi updated_by
     * ========================================================== */

    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if (empty($row->published_at)) {
                $row->published_at = now();
            }
            if (empty($row->created_by) && auth()->check()) {
                $row->created_by = auth()->id();
            }
            if (empty($row->priority)) {
                $row->priority = 'normal';
            }
        });

        static::updating(function (self $row) {
            if (auth()->check()) {
                $row->updated_by = auth()->id();
            }
        });
    }

    /* ==========================================================
     | RELATIONS
     * ========================================================== */

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ==========================================================
     | SCOPES
     * ========================================================== */

    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($q)
    {
        return $q->whereNull('published_at')
            ->orWhere('published_at', '>', now());
    }

    public function scopeUrgent($q)
    {
        return $q->whereIn('priority', ['high', 'urgent']);
    }

    /* ==========================================================
     | ACCESSORS — backward compatibility
     | Biar Blade lama yang pakai `$a->judul` / `$a->isi` tetap jalan
     * ========================================================== */

    public function getJudulAttribute(): ?string
    {
        return $this->attributes['title'] ?? null;
    }

    public function getIsiAttribute(): ?string
    {
        return $this->attributes['content'] ?? null;
    }

    public function getAuthorIdAttribute(): ?int
    {
        return $this->attributes['created_by'] ?? null;
    }

    /* ==========================================================
     | HELPERS
     * ========================================================== */

    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->isPast();
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'rose',
            'high'   => 'amber',
            'low'    => 'gray',
            default  => 'violet',
        };
    }
}