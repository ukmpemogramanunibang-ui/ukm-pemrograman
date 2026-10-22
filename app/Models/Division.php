<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['code', 'name', 'description', 'photo', 'status', 'max_members', 'ketua_label', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(DivisionMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'division_members')
            ->withPivot('position', 'status', 'joined_at', 'left_at')
            ->withTimestamps();
    }
}