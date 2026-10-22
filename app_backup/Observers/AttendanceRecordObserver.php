<?php

namespace App\Observers;

use App\Models\AttendanceRecord;
use App\Services\Attendance\PenaltyEngine;
use Illuminate\Support\Facades\Log;

class AttendanceRecordObserver
{
    public function __construct(protected PenaltyEngine $engine) {}

    /**
     * Setiap kali attendance record di-update (misal status diubah jadi absent),
     * engine akan otomatis evaluasi dan generate denda jika memenuhi rule.
     */
    public function updated(AttendanceRecord $record): void
    {
        // Skip kalau record dulunya bukan violation dan sekarang bukan
        if (!$record->isViolation()) return;

        // Skip kalau sudah pernah generate denda untuk record ini
        if ($record->penalties()->exists()) {
            Log::info('Penalty sudah ada untuk record', ['record_id' => $record->id]);
            return;
        }

        try {
            $penalty = $this->engine->evaluate($record);
            if ($penalty) {
                Log::info('Penalty auto-generated', [
                    'penalty_id' => $penalty->id,
                    'case'       => $penalty->case_number,
                    'record_id'  => $record->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('PenaltyEngine failed', [
                'record_id' => $record->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function created(AttendanceRecord $record): void
    {
        if ($record->isViolation()) {
            $this->updated($record);
        }
    }
}