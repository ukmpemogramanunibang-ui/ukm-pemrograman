<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    protected $fillable = ['user_id', 'points', 'description', 'status', 'given_by', 'approved_by'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giver()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}