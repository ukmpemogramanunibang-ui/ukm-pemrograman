<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\LmsGroup;
use App\Models\Lms\LmsItem;
use App\Models\Lms\LmsItemUser;
use App\Models\Lms\LmsSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        /* ============ Statistik untuk owner & staff ============ */
        $scopeItems = LmsItem::query()
            ->when($user->level_id > 2, fn ($q) => $q->where('created_by', $user->id));

        $stats = [
            'total'     => (clone $scopeItems)->count(),
            'tasks'     => (clone $scopeItems)->where('type', 'task')->count(),
            'materials' => (clone $scopeItems)->where('type', 'material')->count(),
            'groups'    => LmsGroup::where('status', 'active')
                ->when($user->level_id > 2, fn ($q) => $q->where('creator_id', $user->id))
                ->count(),
        ];

        /* ============ Status untuk saya sebagai penerima ============ */
        $myAssignments = LmsItemUser::with('item')
            ->where('user_id', $user->id)
            ->orderByDesc('assigned_at');

        $myStats = [
            'new'      => (clone $myAssignments)->where('status', LmsItemUser::STATUS_NEW)->count(),
            'pending'  => (clone $myAssignments)->whereIn('status', [
                LmsItemUser::STATUS_VIEWED,
                LmsItemUser::STATUS_IN_PROGRESS,
            ])->count(),
            'done'     => (clone $myAssignments)->whereIn('status', [
                LmsItemUser::STATUS_COMPLETED,
                LmsItemUser::STATUS_SUBMITTED,
                LmsItemUser::STATUS_PENDING_REVIEW,
            ])->count(),
            'late'     => (clone $myAssignments)->where('status', LmsItemUser::STATUS_LATE)->count(),
        ];

        /* ============ Deadline terdekat ============ */
        $nextDeadlines = LmsItemUser::with('item.group')
            ->where('user_id', $user->id)
            ->whereIn('status', [
                LmsItemUser::STATUS_NEW,
                LmsItemUser::STATUS_VIEWED,
                LmsItemUser::STATUS_IN_PROGRESS,
            ])
            ->whereHas('item', fn ($q) => $q->where('type', 'task')
                ->whereNotNull('deadline_at')
                ->where('deadline_at', '>=', now())
            )
            ->orderBy(
                LmsItem::select('deadline_at')
                    ->whereColumn('lms_items.id', 'lms_item_users.item_id')
            )
            ->limit(5)
            ->get();

        /* ============ Pengelompokan saya ============ */
        $myGroups = LmsGroup::active()
            ->when($user->level_id > 2, fn ($q) => $q->where('creator_id', $user->id))
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        /* ============ Submission perlu review ============ */
        $pendingReview = null;
        if (in_array($user->level_id, [1,2,3,4])) {
            $pendingReview = LmsSubmission::with(['item', 'user'])
                ->where('status', 'pending_review')
                ->latest('submitted_at')
                ->limit(5)
                ->get();
        }

        /* ============ Aktivitas terbaru ============ */
        $activities = \App\Models\Lms\LmsAuditLog::with('actor')
            ->latest()
            ->limit(8)
            ->get();

        return view('owner.lms.dashboard', compact(
            'user', 'stats', 'myStats', 'nextDeadlines',
            'myGroups', 'pendingReview', 'activities'
        ));
    }
}