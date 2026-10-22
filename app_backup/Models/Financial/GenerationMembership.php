<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class GenerationMembership extends Model
{
    protected $fillable = [
        'generation_id', 'user_id', 'started_at', 'ended_at',
        'status', 'reason', 'assigned_by', 'notes'
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}