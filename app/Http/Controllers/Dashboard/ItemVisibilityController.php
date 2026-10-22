<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DashboardItem;
use App\Models\DashboardItemVisibility;
use Illuminate\Http\Request;

class ItemVisibilityController extends Controller
{
    /**
     * Toggle visibility 1 card untuk user yang login.
     * POST /dashboard/items/{item}/toggle
     */
    public function toggle(Request $request, DashboardItem $item)
    {
        abort_unless($item->type === 'card', 404);

        $user = auth()->user();
        abort_unless($user, 401);

        $record = DashboardItemVisibility::firstOrNew([
            'user_id'           => $user->id,
            'dashboard_item_id' => $item->id,
        ]);

        // Kalau baru, default true → toggle jadi false
        $record->is_visible = ! ($record->exists ? $record->is_visible : true);
        $record->save();

        return response()->json([
            'success'    => true,
            'is_visible' => $record->is_visible,
            'message'    => $record->is_visible
                ? 'Card ditampilkan.'
                : 'Card disembunyikan.',
        ]);
    }

    /**
     * Set visibility 1 card (explicit).
     * POST /dashboard/items/{item}/visibility  { is_visible: bool }
     */
    public function set(Request $request, DashboardItem $item)
    {
        abort_unless($item->type === 'card', 404);

        $data = $request->validate([
            'is_visible' => ['required', 'boolean'],
        ]);

        DashboardItemVisibility::updateOrCreate(
            [
                'user_id'           => auth()->id(),
                'dashboard_item_id' => $item->id,
            ],
            ['is_visible' => $data['is_visible']]
        );

        return response()->json([
            'success'    => true,
            'is_visible' => (bool) $data['is_visible'],
        ]);
    }

    /**
     * Bulk: sembunyikan/tampilkan beberapa card.
     * POST /dashboard/items/bulk-visibility  { item_ids: [], is_visible: bool }
     */
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'item_ids'   => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer', 'exists:dashboard_items,id'],
            'is_visible' => ['required', 'boolean'],
        ]);

        foreach ($data['item_ids'] as $itemId) {
            DashboardItemVisibility::updateOrCreate(
                [
                    'user_id'           => auth()->id(),
                    'dashboard_item_id' => $itemId,
                ],
                ['is_visible' => $data['is_visible']]
            );
        }

        return response()->json([
            'success' => true,
            'count'   => count($data['item_ids']),
        ]);
    }

    /**
     * Reset semua visibility user (tampilkan semua).
     * DELETE /dashboard/items/reset-visibility
     */
    public function reset()
    {
        DashboardItemVisibility::where('user_id', auth()->id())->delete();

        return response()->json(['success' => true]);
    }
}