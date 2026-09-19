<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'name', 'round_number', 'type', 'status',
        'starts_at', 'ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function matches()
    {
        return $this->hasMany(CompetitionMatch::class)->orderBy('match_number');
    }
}