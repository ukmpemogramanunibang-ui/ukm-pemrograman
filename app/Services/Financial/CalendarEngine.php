<?php

namespace App\Services\Financial;

use Carbon\Carbon;
use App\Models\Financial\Holiday;
use App\Models\Financial\AcademicPeriod;

class CalendarEngine
{
    /**
     * Get next active day (not weekend, not holiday, not non-active day)
     */
    public function getNextActiveDate(Carbon $date)
    {
        $date = $date->copy();

        // Loop until find active day (max 10 days forward)
        for ($i = 0; $i < 10; $i++) {
            if ($this->isActiveDay($date)) {
                return $date;
            }
            $date->addDay();
        }

        return $date; // fallback
    }

    public function isActiveDay(Carbon $date)
    {
        // Weekend? (if UKM only active on weekdays)
        if ($date->isWeekend()) {
            return false;
        }

        // Check holidays
        $holiday = Holiday::where('date', $date->toDateString())->where('is_active', true)->exists();
        if ($holiday) {
            return false;
        }

        // Check academic non-active (UTS, UAS, etc.)
        $academic = AcademicPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('is_non_active', true)
            ->exists();
        if ($academic) {
            return false;
        }

        return true;
    }
}