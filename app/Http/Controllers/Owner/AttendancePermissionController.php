<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AttendancePermission;
use App\Models\AttendanceSession;
use App\Models\AttendanceAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendancePermissionController extends Controller
{
    /**
     * INDEX — Daftar semua permohonan izin
     */
    public function index(Request $request)
    {
        $query = AttendancePermission::with(['user', 'session', 'reviewer'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        // Filter: status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter: jenis izin
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('permission_type', $request->type);
        }

        // Filter: late request
        if ($request->filled('late') && $request->late === '1') {
            $query->where('is_late_request', true);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $permissions = $query->paginate(15)->withQueryString();

        // ============ STATISTIK ============
        $stats = [
            'pending'  => AttendancePermission::where('status', 'pending')->count(),
            'today'    => AttendancePermission::whereDate('created_at', today())->count(),
            'approved' => AttendancePermission::where('status', 'approved')->count(),
            'rejected' => AttendancePermission::where('status', 'rejected')->count(),
            'late'     => AttendancePermission::where('is_late_request', true)->count(),
        ];

        // Prioritas: pending terbaru (max 6)
        $priority = AttendancePermission::with(['user', 'session'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('owner.attendance.permissions.index', compact(
            'permissions', 'stats', 'priority'
        ));
    }

    /**
     * STORE — Kirim permohonan izin baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id'      => 'required|exists:attendance_sessions,id',
            'permission_type' => 'required|in:excused_absent,late_excused,late_request,correction',
            'reason'          => 'required|string|min:10',
            'evidence'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $session = AttendanceSession::findOrFail($validated['session_id']);
        $isLate = $session->attendance_close_at && now()->gte($session->attendance_close_at);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('attendance/permissions', 'public');
        }

        AttendancePermission::create([
            'session_id'      => $session->id,
            'user_id'         => Auth::id(),
            'permission_type' => $validated['permission_type'],
            'reason'          => $validated['reason'],
            'evidence_path'   => $evidencePath,
            'requested_at'    => now(),
            'is_late_request' => $isLate,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Permohonan izin berhasil dikirim. Menunggu verifikasi pengurus.');
    }

    /**
     * APPROVE — Setujui permohonan
     */
    public function approve(Request $request, AttendancePermission $permission)
    {
        if ($permission->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah pernah diproses.');
        }

        DB::transaction(function () use ($permission, $request) {
            $permission->update([
                'status'       => 'approved',
                'reviewed_by'  => Auth::id(),
                'reviewed_at'  => now(),
                'review_notes' => $request->input('review_notes'),
            ]);

            // Update record kehadiran jika ada
            if ($permission->record) {
                $newStatus = match ($permission->permission_type) {
                    'late_excused'   => 'late_excused',
                    'late_request'   => 'late_excused',
                    'excused_absent' => 'excused_absent',
                    'correction'     => 'corrected',
                    default          => 'excused_absent',
                };
                $newPoints = match ($newStatus) {
                    'late_excused'   => 2,
                    'corrected'      => 3,
                    default          => 0,
                };
                $permission->record->update([
                    'status' => $newStatus,
                    'points' => $newPoints,
                ]);
            }

            AttendanceAuditLog::create([
                'session_id'  => $permission->session_id,
                'actor_id'    => Auth::id(),
                'action'      => 'permission_approved',
                'description' => 'Izin disetujui: ' . ($request->input('review_notes') ?? '-'),
                'after_data'  => $permission->fresh()->toArray(),
            ]);
        });

        return back()->with('success', 'Permohonan izin berhasil disetujui.');
    }

    /**
     * REJECT — Tolak permohonan
     */
    public function reject(Request $request, AttendancePermission $permission)
    {
        $request->validate([
            'review_notes' => 'required|string|min:5',
        ]);

        if ($permission->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah pernah diproses.');
        }

        DB::transaction(function () use ($permission, $request) {
            $permission->update([
                'status'       => 'rejected',
                'reviewed_by'  => Auth::id(),
                'reviewed_at'  => now(),
                'review_notes' => $request->review_notes,
            ]);

            AttendanceAuditLog::create([
                'session_id'  => $permission->session_id,
                'actor_id'    => Auth::id(),
                'action'      => 'permission_rejected',
                'description' => 'Izin ditolak: ' . $request->review_notes,
                'after_data'  => $permission->fresh()->toArray(),
            ]);
        });

        return back()->with('success', 'Permohonan izin telah ditolak.');
    }
}