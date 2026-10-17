<?php

namespace App\Observers;

use App\Models\UserQrCode;
use App\Services\DataProtectionService;
use Illuminate\Support\Facades\Log;

class UserQrCodeObserver
{
    protected DataProtectionService $protection;

    public function __construct(DataProtectionService $protection)
    {
        $this->protection = $protection;
    }

    public function created(UserQrCode $qr): void
    {
        $this->syncQr($qr);
    }

    public function updated(UserQrCode $qr): void
    {
        $this->syncQr($qr);
    }

    public function deleted(UserQrCode $qr): void
    {
        // Hapus dari protected jika diperlukan
    }

    protected function syncQr(UserQrCode $qr): void
    {
        try {
            $this->protection->syncQr($qr);
            Log::info('QR auto-synced to protected DB', ['qr_uuid' => $qr->qr_uuid]);
        } catch (\Exception $e) {
            Log::error('Auto-sync QR failed', ['qr_uuid' => $qr->qr_uuid, 'error' => $e->getMessage()]);
        }
    }
}