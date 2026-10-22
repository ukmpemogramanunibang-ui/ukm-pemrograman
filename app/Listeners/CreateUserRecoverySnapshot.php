<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\QrRecoveryService;
use Illuminate\Auth\Events\Registered;

class CreateUserRecoverySnapshot
{
    protected QrRecoveryService $recoveryService;

    public function __construct(QrRecoveryService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    public function handle(Registered $event): void
    {
        $user = $event->user;
        if ($user instanceof User) {
            $this->recoveryService->generateSnapshot($user);
        }
    }
}