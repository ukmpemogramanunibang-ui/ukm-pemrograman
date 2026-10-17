<?php

namespace App\Services;

use App\Models\DashboardItem;
use App\Models\DashboardItemVisibility;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class DashboardItemService
{
    /**
     * Mapping level_id → kandidat nama role.
     * Dibandingkan case-insensitive, juga varian snake_case / spasi.
     */
    private const LEVEL_TO_ROLE = [
        1 => ['owner'],
        2 => ['superadmin', 'super-admin', 'super admin'],
        3 => ['ketua'],
        4 => ['pengurus', 'pengurus-inti', 'pengurus inti'],
        5 => ['anggota', 'anggota-ukm', 'anggota ukm', 'anggota kelompok'],
        6 => ['anggota-umum', 'anggota umum'],
        7 => ['pengunjung', 'guest'],
    ];

    /* ============================================================
     |  PUBLIC API
     * ============================================================ */

    public function resolveFor(User $user): array
    {
        $isOwner = (int) ($user->level_id ?? 0) === 1;

        // Personal visibility (hide/show per-user via localStorage + backend)
        $visibility = DashboardItemVisibility::query()
            ->where('user_id', $user->id)
            ->pluck('is_visible', 'dashboard_item_id')
            ->toArray();

        $baseQuery = DashboardItem::query()
            ->cards()
            ->with([
                'targets.role',
                'targets.user',
                'shortcuts' => fn ($q) => $q->orderBy('sort_order')->orderBy('name'),
            ])
            ->orderByDesc('priority')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($isOwner) {
            // Owner: preview semua card (termasuk yang non-active)
            $cards = $baseQuery->get();
        } else {
            $roleIds   = $this->getUserRoleIds($user);
            $roleNames = $this->getUserRoleNames($user);

            $cards = $baseQuery
                ->currentlyVisible()
                ->where(function ($q) use ($user, $roleIds, $roleNames) {

                    // 1) Untuk semua akun
                    $q->where('audience_mode', 'all');

                    // 2) Target role (by ID Spatie)
                    if (! empty($roleIds)) {
                        $q->orWhere(function ($sub) use ($roleIds) {
                            $sub->where('audience_mode', 'roles')
                                ->whereHas('targets', fn ($t) => $t->whereIn('role_id', $roleIds));
                        });
                    }

                    // 3) Target role (fallback by NAMA, case-insensitive)
                    if (! empty($roleNames)) {
                        $q->orWhere(function ($sub) use ($roleNames) {
                            $sub->where('audience_mode', 'roles')
                                ->whereHas('targets.role', function ($r) use ($roleNames) {
                                    $lower = array_map('mb_strtolower', $roleNames);
                                    $r->whereIn(DB::raw('LOWER(name)'), $lower);
                                });
                        });
                    }

                    // 4) Target user pribadi
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->where('audience_mode', 'users')
                            ->whereHas('targets', fn ($t) => $t->where('user_id', $user->id));
                    });
                })
                ->get();

            // Non-owner: buang shortcut yang inactive
            $cards->each(function ($card) {
                $card->setRelation(
                    'shortcuts',
                    $card->shortcuts->where('is_active', true)->values()
                );
            });

            // 🔥 FALLBACK: kalau user tidak punya role match sama sekali,
            // tetap tampilkan card `audience_mode = 'all'` agar dashboard tidak kosong.
            if ($cards->isEmpty() && empty($roleIds)) {
                Log::warning('[DashboardItemService] User tidak punya role match', [
                    'user_id'  => $user->id,
                    'level_id' => $user->level_id,
                    'roles'    => $roleNames,
                ]);

                $cards = $baseQuery
                    ->currentlyVisible()
                    ->where('audience_mode', 'all')
                    ->get();

                $cards->each(function ($card) {
                    $card->setRelation(
                        'shortcuts',
                        $card->shortcuts->where('is_active', true)->values()
                    );
                });
            }
        }

        [$visibleCards, $hiddenCards] = $cards->partition(
            fn ($card) => ($visibility[$card->id] ?? true) === true
        );

        return [
            'cards'        => $visibleCards->values(),
            'hidden_cards' => $hiddenCards->values(),
            'shortcuts'    => $visibleCards->pluck('shortcuts')->flatten(1)->values(),
            'is_owner'     => $isOwner,
        ];
    }

    public function stats(): array
    {
        return [
            'total_cards'      => DashboardItem::cards()->count(),
            'active_cards'     => DashboardItem::cards()->active()->count(),
            'total_shortcuts'  => DashboardItem::onlyShortcuts()->count(),
            'active_shortcuts' => DashboardItem::onlyShortcuts()->active()->count(),
        ];
    }

    /* ============================================================
     |  DEBUG — tinker: app(DashboardItemService::class)->debug($user)
     * ============================================================ */

    public function debug(User $user): array
    {
        $roleIds   = $this->getUserRoleIds($user);
        $roleNames = $this->getUserRoleNames($user);
        $isOwner   = (int) ($user->level_id ?? 0) === 1;

        $spatieRoleIds = method_exists($user, 'roles')
            ? $user->roles()->pluck('roles.id')->toArray()
            : [];

        $cards = DashboardItem::query()
            ->cards()
            ->with(['targets.role', 'targets.user'])
            ->get()
            ->map(function ($card) use ($user, $roleIds, $roleNames, $isOwner) {
                $targetRoleIds = $card->targets->pluck('role_id')->filter()->toArray();
                $targetRoleNames = $card->targets->pluck('role.name')->filter()->toArray();
                $targetUserIds = $card->targets->pluck('user_id')->filter()->toArray();

                $matchAll  = $card->audience_mode === 'all';
                $matchRole = $card->audience_mode === 'roles'
                    && (
                        ! empty(array_intersect($roleIds, $targetRoleIds))
                        || ! empty(array_intersect(
                            array_map('mb_strtolower', $roleNames),
                            array_map('mb_strtolower', $targetRoleNames)
                        ))
                    );
                $matchUser = $card->audience_mode === 'users'
                    && in_array($user->id, $targetUserIds, true);

                return [
                    'id'                   => $card->id,
                    'name'                 => $card->name,
                    'is_active'            => $card->is_active,
                    'schedule_type'        => $card->schedule_type,
                    'is_currently_visible' => method_exists($card, 'isCurrentlyVisible')
                        ? $card->isCurrentlyVisible() : null,
                    'audience_mode'        => $card->audience_mode,
                    'target_role_ids'      => $targetRoleIds,
                    'target_role_names'    => $targetRoleNames,
                    'target_user_ids'      => $targetUserIds,
                    'match_all'            => $matchAll,
                    'match_role'           => $matchRole,
                    'match_user'           => $matchUser,
                    'would_show'           => $isOwner || $matchAll || $matchRole || $matchUser,
                ];
            });

        return [
            'user' => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'level_id'           => $user->level_id,
                'spatie_role_ids'    => $spatieRoleIds,
                'effective_role_ids' => $roleIds,
                'fallback_role_names'=> $roleNames,
                'is_owner'           => $isOwner,
            ],
            'cards' => $cards,
        ];
    }

    /* ============================================================
     |  PRIVATE
     * ============================================================ */

    private function getUserRoleIds(User $user): array
    {
        $roleIds = [];

        // 1) Spatie roles
        if (method_exists($user, 'roles')) {
            try {
                $roleIds = $user->roles()->pluck('roles.id')->toArray();
            } catch (\Throwable $e) {
                $roleIds = [];
            }
        }

        // 2) Fallback by level_id → nama role → role_id (case-insensitive)
        $names = $this->getUserRoleNames($user);

        if (! empty($names)) {
            $lower = array_map('mb_strtolower', $names);
            $fallbackIds = Cache::remember(
                'role_ids_' . md5(implode('|', $lower)),
                now()->addMinutes(10),
                fn () => Role::query()
                    ->whereIn(DB::raw('LOWER(name)'), $lower)
                    ->pluck('id')
                    ->toArray()
            );

            $roleIds = array_unique(array_merge($roleIds, $fallbackIds));
        }

        return array_values(array_filter($roleIds));
    }

    private function getUserRoleNames(User $user): array
    {
        $levelId = (int) ($user->level_id ?? 0);
        return self::LEVEL_TO_ROLE[$levelId] ?? [];
    }
}