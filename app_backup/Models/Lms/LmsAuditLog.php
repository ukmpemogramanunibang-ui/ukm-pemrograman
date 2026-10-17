<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LmsAuditLog extends Model
{
    protected $table = 'lms_audit_logs';

    protected $fillable = [
        'action', 'subject_type', 'subject_id', 'actor_id', 'actor_role',
        'changes', 'reason', 'ip_address', 'user_agent',
    ];

    protected $casts = ['changes' => 'array'];

    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
    public function subject() { return $this->morphTo(); }
}