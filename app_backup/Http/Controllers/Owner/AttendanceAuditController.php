<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AuditAlert;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceAuditController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AuditLog::class);

        $filters = $this->resolveFilters($request);
        $logs    = $this->buildQuery($filters)
            ->with(['actor:id,name', 'session:id,title'])
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $stats    = $this->computeStats();
        $health   = $this->computeHealth();
        $alerts   = AuditAlert::query()->whereNull('acknowledged_at')
            ->orderByDesc('created_at')->limit(5)->get();
        $critical = AuditLog::query()->critical()
            ->where('created_at', '>=', now()->subHours(24))
            ->orderByDesc('created_at')->limit(6)->get();

        $meta = [
            'categories' => AuditLog::CATEGORIES,
            'severities' => AuditLog::SEVERITIES,
            'results'    => AuditLog::RESULTS,
            'sources'    => AuditLog::SOURCES,
            'actions'    => AuditLog::query()->distinct()->orderBy('action')->pluck('action'),
            'actors'     => User::query()->select('id','name')->orderBy('name')->limit(200)->get(),
        ];

        return view('owner.attendance.audit.index', compact(
            'logs','stats','health','alerts','critical','meta','filters'
        ));
    }

    public function show(AuditLog $log)
    {
        Gate::authorize('view', $log);
        $log->load(['actor:id,name,email', 'target:id,name', 'session:id,title']);

        $related = AuditLog::query()
            ->where('correlation_id', $log->correlation_id)
            ->where('id', '!=', $log->id)
            ->orderBy('id')->limit(50)->get();

        return view('owner.attendance.audit.show', compact('log', 'related'));
    }

    public function verify(Request $request)
    {
        Gate::authorize('viewAny', AuditLog::class);
        return response()->json(AuditService::verifyChain((int) $request->input('limit', 500)));
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('export', AuditLog::class);

        $filters  = $this->resolveFilters($request);
        $query    = $this->buildQuery($filters)->orderByDesc('id')->limit(50000);
        $filename = 'audit-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Event UUID','Waktu','Actor','Role','Action','Category','Severity','Result',
                'Entity','Entity ID','Session','Description','Reason',
                'Permission','Authorization','Source','IP','User Agent',
                'Correlation','Request','Before','After','Changes','Integrity Hash',
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $l) {
                    fputcsv($out, [
                        $l->event_uuid,
                        optional($l->created_at)->format('Y-m-d H:i:s'),
                        $l->actor_name, $l->actor_role, $l->action,
                        $l->category_label, $l->severity, $l->result,
                        $l->auditable_type, $l->auditable_id, $l->session_id,
                        $l->description, $l->reason,
                        $l->permission, $l->authorization_result,
                        $l->source, $l->ip_address, $l->user_agent,
                        $l->correlation_id, $l->request_id,
                        json_encode($l->before_data, JSON_UNESCAPED_UNICODE),
                        json_encode($l->after_data,  JSON_UNESCAPED_UNICODE),
                        json_encode($l->change_data, JSON_UNESCAPED_UNICODE),
                        $l->integrity_hash,
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function acknowledgeAlert(AuditAlert $alert)
    {
        Gate::authorize('viewAny', AuditLog::class);
        $alert->update(['acknowledged_at' => now(), 'acknowledged_by' => auth()->id()]);
        return back()->with('success', 'Alert ditandai sudah ditangani.');
    }

    protected function resolveFilters(Request $request): array
    {
        return [
            'q'             => trim((string) $request->input('q', '')),
            'action'        => $request->input('action'),
            'category'      => $request->input('category'),
            'severity'      => (array) $request->input('severity', []),
            'result'        => (array) $request->input('result', []),
            'source'        => (array) $request->input('source', []),
            'actor_id'      => $request->input('actor_id'),
            'session'       => $request->input('session'),
            'from'          => $request->input('from'),
            'to'            => $request->input('to'),
            'only_critical' => $request->boolean('only_critical'),
            'only_failed'   => $request->boolean('only_failed'),
            'only_sensitive'=> $request->boolean('only_sensitive'),
        ];
    }

    protected function buildQuery(array $filters)
    {
        $q = AuditLog::query();

        if ($filters['only_critical'])  $q->critical();
        if ($filters['only_failed'])    $q->failed();
        if ($filters['only_sensitive']) $q->where('is_sensitive', true);
        if ($filters['q'])              $q->search($filters['q']);
        if ($filters['action'])         $q->where('action', $filters['action']);
        if ($filters['category'])       $q->where('category', $filters['category']);
        if ($filters['severity'])       $q->whereIn('severity', $filters['severity']);
        if ($filters['result'])         $q->whereIn('result', $filters['result']);
        if ($filters['source'])         $q->whereIn('source', $filters['source']);
        if ($filters['actor_id'])       $q->where('actor_id', $filters['actor_id']);
        if ($filters['session'])        $q->where('session_id', $filters['session']);
        $q->betweenDates($filters['from'], $filters['to']);

        return $q;
    }

    protected function computeStats(): array
    {
        $now = now();
        return [
            'total'         => AuditLog::query()->count(),
            'today'         => AuditLog::query()->whereDate('created_at', $now->toDateString())->count(),
            'this_week'     => AuditLog::query()->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
            'this_month'    => AuditLog::query()->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),
            'critical_24h'  => AuditLog::query()->critical()->where('created_at', '>=', $now->copy()->subDay())->count(),
            'failed_24h'    => AuditLog::query()->failed()->where('created_at', '>=', $now->copy()->subDay())->count(),
            'sensitive_24h' => AuditLog::query()->where('is_sensitive', true)->where('created_at', '>=', $now->copy()->subDay())->count(),
        ];
    }

    protected function computeHealth(): array
    {
        $check  = AuditService::verifyChain(500);
        $total  = AuditLog::query()->count();
        $broken = count($check['broken'] ?? []);
        $score  = $total === 0 ? 100 : max(0, 100 - ($broken * 5));

        return [
            'ok'      => $check['ok'],
            'score'   => $score,
            'checked' => $check['checked'],
            'broken'  => $broken,
            'label'   => $score >= 99 ? 'Excellent' : ($score >= 90 ? 'Good' : ($score >= 70 ? 'Attention' : 'Critical')),
        ];
    }
}