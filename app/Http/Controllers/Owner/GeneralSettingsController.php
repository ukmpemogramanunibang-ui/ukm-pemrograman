<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\DashboardItem;
use App\Services\DashboardItemService;

class GeneralSettingsController extends Controller
{
    /**
     * Guard internal — dipanggil di setiap method, BUKAN di __construct.
     * Alasan: __construct dipanggil container saat instantiating controller,
     * termasuk saat `php artisan route:list` (tanpa auth). Guard di constructor
     * akan crash CLI command.
     */
    private function ensureOwner(): void
    {
        abort_unless(auth()->check(), 403, 'Silakan login terlebih dahulu.');

        abort_unless(
            (int) (auth()->user()->level_id ?? 0) === 1,
            403,
            'Hanya Owner yang dapat mengakses halaman ini.'
        );
    }

    // =====================================================================
    // HUB — Dashboard Builder
    // =====================================================================
    public function index(DashboardItemService $service)
    {
        $this->ensureOwner();

$cards = DashboardItem::query()
    ->cards()
    ->with([
        'targets.role',
        'targets.user',
        'creator',
        'shortcuts' => fn ($q) => $q
            ->orderBy('sort_order')
            ->orderBy('name'),           // ✅ eager load
    ])
    ->orderByDesc('priority')
    ->orderBy('sort_order')
    ->get();

        // Shortcut standalone (tanpa parent) — jika ada
        // Tidak boleh pakai ->shortcuts() — sudah di-rename → onlyShortcuts()
        $standaloneShortcuts = DashboardItem::query()
            ->onlyShortcuts()
            ->whereNull('parent_id')
            ->with(['creator'])
            ->orderByDesc('priority')
            ->orderBy('sort_order')
            ->get();

        // Shortcut yang parent-nya hilang (orphan) — data integrity check
        $orphanShortcuts = DashboardItem::query()
            ->onlyShortcuts()
            ->whereNotNull('parent_id')
            ->whereDoesntHave('card')
            ->with(['creator'])
            ->get();

        return view('owner.settings.general', [
            'cards'               => $cards,
            'standaloneShortcuts' => $standaloneShortcuts,
            'orphanShortcuts'     => $orphanShortcuts,
            'stats'               => $service->stats(),
        ]);
    }

    // =====================================================================
    // PROFILE
    // =====================================================================
    public function profile()
    {
        $this->ensureOwner();

        return view('owner.settings.profile', [
            'user' => auth()->user()->load(['level', 'status', 'jabatans']),
        ]);
    }

    // =====================================================================
    // AUDIT
    // =====================================================================
    public function audit()
    {
        $this->ensureOwner();

        return view('owner.settings.audit', [
            'items' => DashboardItem::withTrashed()
                ->with(['creator:id,name', 'updater:id,name', 'parent:id,name'])
                ->orderByDesc('updated_at')
                ->paginate(30),
        ]);
    }

    // =====================================================================
    // HISTORY
    // =====================================================================
    public function history()
    {
        $this->ensureOwner();

        return view('owner.settings.dashboard-history', [
            'items' => DashboardItem::withTrashed()
                ->with(['creator', 'updater', 'parent'])
                ->orderByDesc('updated_at')
                ->paginate(30),
        ]);
    }
}