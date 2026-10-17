<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DivisiController extends Controller
{
    /**
     * INDEX — Daftar semua divisi
     */
    public function index(Request $request): View
    {
        try {
            $query = Division::query();

            // Search
            if ($q = $request->input('q')) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                });
            }

            // Filter status
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            $divisions = $query
                ->withCount('members')
                ->orderBy('name')
                ->paginate(20);

            // Stats
            $stats = [
                'total'       => Division::count(),
                'aktif'       => Division::where('status', 'aktif')->count(),
                'nonaktif'    => Division::where('status', '!=', 'aktif')->count(),
                'total_anggota' => DivisionMember::where('status', 'aktif')->count(),
            ];

        } catch (\Throwable $e) {
            Log::error('[Ketua] divisi.index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $divisions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $stats = ['total' => 0, 'aktif' => 0, 'nonaktif' => 0, 'total_anggota' => 0];
        }

        return view('ketua.divisi.index', compact('divisions', 'stats'));
    }

    /**
     * SHOW — Detail satu divisi
     */
    public function show(int $id): View
    {
        $division = null;

        try {
            $division = Division::with([
                'members' => function ($q) {
                    $q->where('status', 'aktif')->orderBy('joined_at');
                },
                'members.user' => function ($q) {
                    $q->select('id', 'name', 'nim', 'email', 'avatar_url', 'level_id');
                },
                'members.user.level:id,name,display_name',
            ])->find($id);

            // Kalau relasi 'members.user' gagal, fallback tanpa user
            if ($division && ! $division->relationLoaded('members')) {
                $division->load('members');
            }

        } catch (\Throwable $e) {
            Log::error('[Ketua] divisi.show error', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Coba load tanpa relasi
            try {
                $division = Division::find($id);
            } catch (\Throwable $e2) {
                $division = null;
            }
        }

        if (! $division) {
            return view('ketua.divisi.not-found', ['divisionId' => $id]);
        }

        return view('ketua.divisi.show', compact('division'));
    }
}