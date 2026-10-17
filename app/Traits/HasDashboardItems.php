<?php

namespace App\Traits;

use App\Services\DashboardItemService;

trait HasDashboardItems
{
    /**
     * Ambil dashboard folder + shortcut untuk user yang login.
     * Aman dipanggil di controller role apapun (owner, ketua, pengurus, anggota).
     */
    protected function getDashboardData(?DashboardItemService $service = null): array
    {
        $user = auth()->user();

        if (! $user) {
            return [
                'cards'        => collect(),
                'hidden_cards' => collect(),
                'shortcuts'    => collect(),
                'is_owner'     => false,
            ];
        }

        $service = $service ?? app(DashboardItemService::class);

        return $service->resolveFor($user);
    }
}