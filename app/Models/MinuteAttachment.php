<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinuteAttachment extends Model
{
    protected $fillable = [
        'minute_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'google_drive_file_id',
    ];

    public function minute(): BelongsTo
    {
        return $this->belongsTo(Minute::class);
    }

    public function getFileUrlAttribute(): string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : '#';
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) return '-';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($this->file_size >= 1024 && $i < count($units) - 1) {
            $this->file_size /= 1024;
            $i++;
        }
        return round($this->file_size, 2) . ' ' . $units[$i];
    }
}