<?php

namespace App\Observers;

use App\Models\PointHistory;
use App\Services\DataProtectionService;
use Illuminate\Support\Facades\Log;

class PointHistoryObserver
{
    protected DataProtectionService $protection;

    public function __construct(DataProtectionService $protection)
    {
        $this->protection = $protection;
    }

    public function created(PointHistory $point): void
    {
        $this->syncPoint($point);
    }

    public function updated(PointHistory $point): void
    {
        $this->syncPoint($point);
    }

    public function deleted(PointHistory $point): void
    {
        // Hapus dari protected jika diperlukan
    }

    protected function syncPoint(PointHistory $point): void
    {
        try {
            $this->protection->syncPoint($point);
            Log::info('Point auto-synced to protected DB', ['id' => $point->id]);
        } catch (\Exception $e) {
            Log::error('Auto-sync point failed', ['id' => $point->id, 'error' => $e->getMessage()]);
        }
    }
}