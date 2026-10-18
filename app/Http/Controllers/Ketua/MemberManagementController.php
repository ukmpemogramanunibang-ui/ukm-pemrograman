<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\OrganizationMembership;
use App\Services\OrganizationContext;
use App\Services\OrganizationLeadershipService;
use App\Notifications\RoleChangedNotification;
use App\Notifications\KetuaTransferredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberManagementController extends Controller
{
    private function getActiveOrgId(): int
    {
        $orgId = OrganizationContext::id();
        if (!$orgId) {
            $orgId = Auth::user()->activeOrganizations()->first()?->id;
            if ($orgId) OrganizationContext::set($orgId);
        }
        return (int) $orgId;
    }

    public function index(Request $request)
    {
        $orgId = $this->getActiveOrgId();

        try {
            $query = User::query()
                ->whereNull('deleted_at')
                ->whereHas('organizationMemberships', function($q) use ($orgId) {
                    $q->where('organization_id', $orgId)->where('membership_status', 'active');
                })
                ->with(['level', 'university', 'major', 'jabatans']);

            if ($q = $request->input('q')) {
                $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('nim', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
            }
            if ($status = $request->input('status')) $query->where('is_active', $status === 'active');
            if ($gender = $request->input('gender')) $query->where('gender', $gender);

            $members = $query->latest()->paginate(20);

        } catch (\Throwable $e) {
            Log::warning('[Ketua] manajemen-anggota error', ['error' => $e->getMessage()]);
            $members = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        $baseQuery = OrganizationMembership::where('organization_id', $orgId)->where('membership_status', 'active');
        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'active'    => (clone $baseQuery)->whereHas('user', fn($u) => $u->where('is_active', true))->count(),
            'male'      => (clone $baseQuery)->whereHas('user', fn($u) => $u->where('gender', 'L'))->count(),
            'female'    => (clone $baseQuery)->whereHas('user', fn($u) => $u->where('gender', 'P'))->count(),
            'pengurus'  => (clone $baseQuery)->where('access_role', 'officer')->count(),
            'ketua'     => (clone $baseQuery)->where('access_role', 'leader')->count(),
        ];

        $availableRoles = \App\Models\Level::whereIn('id', [3, 4, 5])->orderBy('id')->get()->map(fn($l) => ['id' => $l->id, 'name' => $l->name])->toArray();
        $jabatans = Jabatan::orderBy('name')->get();
        $isKetua = OrganizationContext::authorize(Auth::user(), $orgId, 'leader');

        return view('ketua.manajemen-anggota.index', compact('members', 'stats', 'availableRoles', 'jabatans', 'isKetua'));
    }

    public function data(Request $request)
    {
        $orgId = $this->getActiveOrgId();
        try {
            $users = User::query()
                ->whereNull('deleted_at')
                ->whereHas('organizationMemberships', fn($q) => $q->where('organization_id', $orgId)->where('membership_status', 'active'))
                ->with(['level', 'jabatans', 'organizationMemberships' => fn($q) => $q->where('organization_id', $orgId)])
                ->latest()->limit(500)->get()->map(function ($u) {
                    $membership = $u->organizationMemberships->first();
                    return [
                        'id'             => $u->id,
                        'name'           => $u->name,
                        'nim'            => $u->nim,
                        'email'          => $u->email,
                        'avatar_url'     => $u->avatar_url,
                        'level_id'       => $u->level_id,
                        'level_name'     => optional($u->level)->name,
                        'access_role'    => $membership ? $membership->access_role : 'member',
                        'position_label' => $membership ? $membership->position_label : '-',
                        'is_active'      => (bool) $u->is_active,
                        'gender'         => $u->gender,
                        'jabatans'       => $u->jabatans->map(fn($j) => ['id' => $j->id, 'name' => $j->name])->toArray(),
                    ];
                });

            return response()->json($users);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        $orgId = $this->getActiveOrgId();
        $member = User::with(['level', 'university', 'major', 'jabatans', 'organizationMemberships' => fn($q) => $q->where('organization_id', $orgId)])->findOrFail($id);
        return view('ketua.manajemen-anggota.show', compact('member'));
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'action'     => 'required|in:export,delete,block,unblock,freeze,unfreeze,alumni',
        ]);

        $orgId = $this->getActiveOrgId();
        $userIds = $validated['user_ids'];

        if (!OrganizationContext::authorize(Auth::user(), $orgId, 'officer')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Pastikan user yang diubah adalah anggota di org ini
        $validIds = OrganizationMembership::where('organization_id', $orgId)
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')->toArray();

        if (empty($validIds)) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan di organisasi ini.'], 404);
        }

        try {
            DB::beginTransaction();
            $affected = 0;
            
            switch ($validated['action']) {
                case 'delete':
                    User::whereIn('id', $validIds)->delete();
                    $affected = count($validIds);
                    break;
                case 'block':
                    User::whereIn('id', $validIds)->update(['is_active' => false]);
                    $affected = count($validIds);
                    break;
                case 'unblock':
                    User::whereIn('id', $validIds)->update(['is_active' => true]);
                    $affected = count($validIds);
                    break;
                case 'freeze':
                    User::whereIn('id', $validIds)->update(['is_active' => false, 'locked_until' => now()->addDays(30)]);
                    $affected = count($validIds);
                    break;
                case 'unfreeze':
                    User::whereIn('id', $validIds)->update(['is_active' => true, 'locked_until' => null]);
                    $affected = count($validIds);
                    break;
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "{$affected} user berhasil diperbarui.", 'affected' => $affected]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function manageRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids'           => 'required|array|min:1',
            'user_ids.*'         => 'exists:users,id',
            'action'             => 'required|in:promote,demote,add_position,remove_position,transfer_ketua',
            'jabatans'           => 'nullable|array',
            'jabatans.*'         => 'exists:jabatans,id',
            'comment'            => 'nullable|string|max:1000',
        ]);

        $orgId = $this->getActiveOrgId();
        
        if (!OrganizationContext::authorize(Auth::user(), $orgId, 'leader')) {
            return response()->json(['success' => false, 'message' => 'Anda bukan Ketua di organisasi ini.'], 403);
        }

        try {
            DB::beginTransaction();

            if ($validated['action'] === 'transfer_ketua') {
                return $this->handleKetuaTransfer($validated['user_ids'], $validated, $orgId);
            }

            $affected = 0;
            foreach ($validated['user_ids'] as $uid) {
                $membership = OrganizationMembership::where('organization_id', $orgId)->where('user_id', $uid)->first();
                if (!$membership) continue;

                if ($validated['action'] === 'promote') {
                    if ($membership->access_role === 'member') $membership->access_role = 'pj';
                    elseif ($membership->access_role === 'pj') $membership->access_role = 'officer';
                    $membership->save();
                    $affected++;
                } elseif ($validated['action'] === 'demote') {
                    if ($membership->access_role === 'officer') $membership->access_role = 'pj';
                    elseif ($membership->access_role === 'pj') $membership->access_role = 'member';
                    $membership->save();
                    $affected++;
                } elseif ($validated['action'] === 'add_position' && !empty($validated['jabatans'])) {
                    foreach ($validated['jabatans'] as $jid) {
                        DB::table('jabatan_user')->updateOrInsert(
                            ['user_id' => $uid, 'jabatan_id' => $jid],
                            ['status' => 'active', 'start_date' => now(), 'created_at' => now(), 'updated_at' => now()]
                        );
                    }
                    $affected++;
                } elseif ($validated['action'] === 'remove_position' && !empty($validated['jabatans'])) {
                    DB::table('jabatan_user')->where('user_id', $uid)->whereIn('jabatan_id', $validated['jabatans'])->delete();
                    $affected++;
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "{$affected} user berhasil diperbarui."]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    protected function handleKetuaTransfer(array $userIds, array $data, int $orgId)
    {
        $currentUser = Auth::user();

        if (count($userIds) > 1 || empty($userIds)) {
            return response()->json(['success' => false, 'message' => 'Pilih tepat 1 user penerima jabatan.'], 422);
        }

        $targetId = $userIds[0];
        if ($targetId === $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat transfer ke diri sendiri.'], 422);
        }

        $targetUser = User::find($targetId);
        $comment = $data['comment'] ?? 'Transfer jabatan Ketua.';

        try {
            OrganizationLeadershipService::transferLeadership($orgId, $currentUser->id, $targetId);

            try {
                if (method_exists($targetUser, 'notify')) {
                    $targetUser->notify(new KetuaTransferredNotification(Auth::user()->name, $comment));
                }
            } catch (\Throwable $e) {}

            return response()->json(['success' => true, 'message' => "Jabatan Ketua berhasil ditransfer ke {$targetUser->name}."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}