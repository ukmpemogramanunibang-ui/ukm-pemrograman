<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\LmsAuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $logs = LmsAuditLog::with('actor')
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->latest()
            ->paginate(30);

        return view('owner.lms.audit', compact('logs'));
    }

    public function activities()
    {
        $logs = LmsAuditLog::with('actor')
            ->latest()
            ->paginate(30);

        return view('owner.lms.activities', compact('logs'));
    }
}