<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Minute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'meeting_date',
        'meeting_time',
        'location',
        'leader_name',
        'notulis_id',
        'participants',
        'agenda',
        'discussion',
        'decisions',
        'follow_up',
        'summary',
        'status',
        'google_drive_file_id',
        'google_drive_folder_id',
        'completed_at',
        'archived_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'meeting_time' => 'datetime:H:i',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi
    public function notulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notulis_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MinuteAttachment::class);
    }

    // Scope
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('location', 'LIKE', "%{$keyword}%")
                  ->orWhere('leader_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('summary', 'LIKE', "%{$keyword}%");
            });
        }
        return $query;
    }

    // Helper status badge
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'draft'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'archived'  => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        ];
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft'     => 'Draft',
            'completed' => 'Selesai',
            'archived'  => 'Diarsipkan',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getFormattedMeetingDateAttribute(): string
    {
        return $this->meeting_date ? $this->meeting_date->translatedFormat('d F Y') : '-';
    }
}