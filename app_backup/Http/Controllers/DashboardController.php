<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\LmsAuditLog;
use App\Models\Lms\LmsGroup;
use App\Models\Lms\LmsItem;
use App\Models\Lms\LmsItemUser;
use App\Models\Lms\LmsSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'total'     => LmsItem::count(),
            'tasks'     => LmsItem::where('type', 'task')->count(),
            'materials' => LmsItem::where('type', 'material')->count(),
            'groups'    => LmsGroup::where('status', 'active')->count(),
        ];

        $myStats = [
            'new' => LmsItemUser::where('user_id', $user->id)
                ->where('status', 'new')->count(),

            'progress' => LmsItemUser::where('user_id', $user->id)
                ->whereIn('status', ['viewed', 'in_progress'])->count(),

            'done' => LmsItemUser::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'submitted', 'pending_review'])->count(),

            'late' => LmsItemUser::where('user_id', $user->id)
                ->where('status', 'late')->count(),
        ];

        $myGroups = LmsGroup::query()
            ->where('status', 'active')
            ->with([
                'creator:id,name,avatar_url,level_id',
                'creator.level:id,name',
            ])
            ->withCount('items')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $nextDeadlines = LmsItemUser::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['new', 'viewed', 'in_progress'])
            ->whereHas('item', function ($q) {
                $q->where('type', 'task')
                  ->whereNotNull('deadline_at')
                  ->where('deadline_at', '>=', now());
            })
            ->with([
                'item:id,title,description,type,deadline_at,group_id,created_by,attachments',
                'item.group:id,name',
                'item.creator:id,name,avatar_url,level_id',
                'item.creator.level:id,name',
            ])
            ->limit(5)
            ->get();

        $pendingReview = LmsSubmission::query()
            ->where('status', 'pending_review')
            ->with([
                'item:id,title,type,group_id,created_by',
                'item.group:id,name',
                'user:id,name,avatar_url,level_id',
                'user.level:id,name',
            ])
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        $auditLogs = LmsAuditLog::query()
            ->with(['causer:id,name,avatar_url'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('owner.lms.dashboard', compact(
            'stats',
            'myStats',
            'myGroups',
            'nextDeadlines',
            'pendingReview',
            'auditLogs'
        ));
    }
}