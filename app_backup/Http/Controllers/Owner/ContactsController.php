<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        $query = User::with(['level', 'status']);

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('level_id', $request->role);
        }

        // Filter by status (active, offline, frozen, blocked, etc.)
        if ($request->has('status') && $request->status != '') {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'offline':
                    $query->where('is_online', false);
                    break;
                case 'frozen':
                    $query->whereNotNull('locked_until')->where('locked_until', '>', now());
                    break;
                case 'blocked':
                    $query->where('is_active', false);
                    break;
                default:
                    break;
            }
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);
        $roles = Level::all();
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'offline' => User::where('is_online', false)->count(),
            'blocked' => User::where('is_active', false)->count(),
        ];

        return view('owner.contacts.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Display users by role.
     */
    public function byRole($roleSlug)
    {
        $levelMap = [
            'owner' => 1,
            'superadmin' => 2,
            'ketua' => 3,
            'pengurus' => 4,
            'anggota' => 5,
            'anggota-umum' => 6,
            'pengunjung' => 6, // guest level
        ];

        $levelId = $levelMap[$roleSlug] ?? null;
        if (!$levelId) {
            abort(404);
        }

        $users = User::with(['level', 'status'])
            ->where('level_id', $levelId)
            ->orderBy('name')
            ->paginate(15);

        $roles = Level::all();
        $stats = [
            'total' => $users->total(),
            'active' => $users->where('is_active', true)->count(),
            'offline' => $users->where('is_online', false)->count(),
            'blocked' => $users->where('is_active', false)->count(),
        ];

        return view('owner.contacts.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Display users by status.
     */
    public function byStatus($statusSlug)
    {
        $query = User::with(['level', 'status']);

        switch ($statusSlug) {
            case 'active':
                $query->where('is_active', true);
                break;
            case 'offline':
                $query->where('is_online', false);
                break;
            case 'frozen':
                $query->whereNotNull('locked_until')->where('locked_until', '>', now());
                break;
            case 'blocked':
                $query->where('is_active', false);
                break;
            case 'deactivated':
                $query->onlyTrashed();
                break;
            default:
                abort(404);
        }

        $users = $query->orderBy('name')->paginate(15);
        $roles = Level::all();
        $stats = [
            'total' => $users->total(),
            'active' => $users->where('is_active', true)->count(),
            'offline' => $users->where('is_online', false)->count(),
            'blocked' => $users->where('is_active', false)->count(),
        ];

        return view('owner.contacts.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Display a single user profile.
     */
    public function show($uuid)
    {
        $user = User::with(['level', 'status', 'jabatans', 'university', 'major'])
            ->where('user_uuid', $uuid)
            ->firstOrFail();

        return view('owner.contacts.user', compact('user'));
    }
}