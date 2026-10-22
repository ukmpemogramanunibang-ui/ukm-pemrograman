<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardRevision extends Model
{
    protected $fillable = ['revision', 'bumped_by', 'reason'];

    /**
     * Naikkan revisi global.
     * Dipanggil setiap ada perubahan dashboard item / user role / dll.
     */
    public static function bump(?string $reason = null): int
    {
        $row = static::query()->latest('id')->first();

        if (! $row) {
            $row = static::create([
                'revision'  => 1,
                'bumped_by' => auth()->id(),
                'reason'    => $reason,
            ]);
            return $row->revision;
        }

        $row->increment('revision');
        $row->update([
            'bumped_by' => auth()->id(),
            'reason'    => $reason,
        ]);

        return $row->fresh()->revision;
    }

    public static function current(): int
    {
        return (int) static::query()->latest('id')->value('revision') ?: 1;
    }
}