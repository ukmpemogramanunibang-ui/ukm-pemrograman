<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionSponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'name', 'logo', 'url', 'description', 'position', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}