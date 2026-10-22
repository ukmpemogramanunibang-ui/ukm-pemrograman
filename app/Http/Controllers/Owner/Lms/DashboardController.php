<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\LmsAuditLog;
use App\Models\Lms\LmsGroup;
use App\Models\Lms\LmsItem;
use App\Models\Lms\LmsItemUser;
use App\Models\Lms\LmsSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        /* ═══════════════════════════════════════════════════
           GLOBAL STATS
           ═══════════════════════════════════════════════════ */
        $stats = [
            'total'      => LmsItem::count(),
            'tasks'      => LmsItem::where('type', 'task')->count(),
            'materials'  => LmsItem::where('type', 'material')->count(),
            'groups'     => LmsGroup::where('status', 'active')->count(),
            'trash'      => LmsItem::onlyTrashed()->count(),
            'submissions'=> LmsSubmission::count(),
        ];

        /* ═══════════════════════════════════════════════════
           MY STATS
           ═══════════════════════════════════════════════════ */
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

        $totalMyItems = array_sum($myStats);
        $myStats['progress_percent'] = $totalMyItems > 0
            ? round(($myStats['done'] / $totalMyItems) * 100)
            : 0;

        /* ═══════════════════════════════════════════════════
           MY GROUPS
           ═══════════════════════════════════════════════════ */
        $myGroups = LmsGroup::query()
            ->where('status', 'active')
            ->with([
                'creator:id,name,avatar_url,level_id',
                'creator.level:id,name',
            ])
            ->withCount([
                'items',
                'items as tasks_count'     => fn ($q) => $q->where('type', 'task'),
                'items as materials_count' => fn ($q) => $q->where('type', 'material'),
            ])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        /* ═══════════════════════════════════════════════════
           NEXT DEADLINES
           ═══════════════════════════════════════════════════ */
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
            ->get()
            ->sortBy(fn ($item) => $item->item?->deadline_at)
            ->values();

        /* ═══════════════════════════════════════════════════
           PENDING REVIEW
           ═══════════════════════════════════════════════════ */
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

        /* ═══════════════════════════════════════════════════
           AUDIT LOGS — FIX FINAL
           Pakai relasi `actor()` (belongsTo via actor_id)
           sesuai struktur tabel Anda
           ═══════════════════════════════════════════════════ */
        $auditLogs = $this->getAuditLogs();

        return view('owner.lms.dashboard', compact(
            'stats',
            'myStats',
            'myGroups',
            'nextDeadlines',
            'pendingReview',
            'auditLogs'
        ));
    }

    /**
     * Ambil audit logs dengan relasi actor yang aman.
     */
    private function getAuditLogs()
    {
        try {
            return LmsAuditLog::query()
                ->with([
                    'actor:id,name,email,avatar_url,level_id',
                    'actor.level:id,name',
                ])
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

        } catch (\Throwable $e) {
            Log::warning('[LMS Dashboard] audit logs gagal load', [
                'error' => $e->getMessage(),
            ]);

            try {
                return LmsAuditLog::query()
                    ->orderByDesc('created_at')
                    ->limit(8)
                    ->get();
            } catch (\Throwable $e2) {
                return collect();
            }
        }
    }
}