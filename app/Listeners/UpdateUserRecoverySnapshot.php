<?php

namespace App\Listeners;

use App\Events\UserUpdated;
use App\Services\QrRecoveryService;

class UpdateUserRecoverySnapshot
{
    protected QrRecoveryService $recoveryService;

    public function __construct(QrRecoveryService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    public function handle(UserUpdated $event): void
    {
        $this->recoveryService->generateSnapshot($event->user);
    }
}