<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionMember extends Model
{
    protected $fillable = [
    'division_id', 'user_id', 'position', 'status', 'joined_at', 'left_at',
    'can_view', 'can_announcement', 'can_message', 'can_view_members',
    'can_manage_members', 'can_manage_content', 'custom_position'
];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}