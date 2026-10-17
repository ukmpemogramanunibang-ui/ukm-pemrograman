<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LmsGroup extends Model
{
    use SoftDeletes;

    protected $table = 'lms_groups';

    protected $fillable = [
        'uuid', 'name',  'code', 'description', 'cover_path', 'cover_images',
        'creator_id', 'status', 'items_count', 'tasks_count', 'materials_count',
        'archived_at', 'archived_by', 'deleted_by', 'delete_reason', 'max_members',
    ];

    protected $casts = [
        'cover_images' => 'array',
        'archived_at'  => 'datetime',
        'deleted_at'   => 'datetime',
    ];

protected static function booted(): void
{
    static::creating(function (self $m) {
        $m->uuid ??= (string) \Illuminate\Support\Str::uuid();
        $m->code ??= 'LMS-' . strtoupper(\Illuminate\Support\Str::random(6));
    });
}

    public function creator()      { return $this->belongsTo(User::class, 'creator_id'); }
    public function items()        { return $this->hasMany(LmsItem::class, 'group_id'); }
    public function tasks()        { return $this->hasMany(LmsItem::class, 'group_id')->where('type', 'task'); }
    public function materials()    { return $this->hasMany(LmsItem::class, 'group_id')->where('type', 'material'); }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_path) return null;
        return asset('storage/' . ltrim($this->cover_path, '/'));
    }

    public function refreshCounters(): void
    {
        $this->update([
            'items_count'     => $this->items()->count(),
            'tasks_count'     => $this->tasks()->count(),
            'materials_count' => $this->materials()->count(),
        ]);
    }

    public function scopeActive($q) { return $q->where('status', 'active'); }
}