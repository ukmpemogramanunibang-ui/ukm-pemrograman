<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LmsItemUser extends Model
{
    protected $table = 'lms_item_users';

    protected $fillable = [
        'item_id', 'user_id', 'assigned_at', 'viewed_at', 'started_at',
        'submitted_at', 'status', 'assignment_source',
    ];

    protected $casts = [
        'assigned_at'  => 'datetime',
        'viewed_at'    => 'datetime',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function item() { return $this->belongsTo(LmsItem::class, 'item_id'); }
    public function user() { return $this->belongsTo(User::class); }

    public const STATUS_NEW            = 'new';
    public const STATUS_VIEWED         = 'viewed';
    public const STATUS_IN_PROGRESS    = 'in_progress';
    public const STATUS_SUBMITTED      = 'submitted';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_REVISION       = 'revision';
    public const STATUS_COMPLETED      = 'completed';
    public const STATUS_LATE           = 'late';
    public const STATUS_MISSED         = 'missed';

    public function isDone(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_SUBMITTED, self::STATUS_PENDING_REVIEW]);
    }
}