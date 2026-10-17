<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsItemTarget extends Model
{
    protected $table = 'lms_item_targets';

    protected $fillable = [
        'item_id',
        'target_type',   // role | user | group
        'target_id',
        'level_id',
    ];

    protected $casts = [
        'target_id' => 'integer',
        'level_id'  => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(LmsItem::class, 'item_id');
    }

    public function level()
    {
        return $this->belongsTo(\App\Models\Level::class, 'level_id');
    }
}