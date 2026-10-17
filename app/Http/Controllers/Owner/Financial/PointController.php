<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PointController extends Controller
{
    /**
     * Kandidat nama model poin.
     */
    protected array $modelCandidates = [
        \App\Models\Financial\PointAccount::class,
        \App\Models\PointAccount::class,
        \App\Models\Point\PointAccount::class,
        \App\Models\Poin::class,
        \App\Models\Point::class,
        \App\Models\PointBalance::class,
        \App\Models\UserPoint::class,
    ];

    /**
     * Kandidat nama tabel bila model tidak ditemukan.
     */
    protected array $tableCandidates = [
        'point_accounts',
        'poin_accounts',
        'user_points',
        'user_poin',
        'points',
    ];

    /**
     * Menyimpan sumber data terakhir (untuk debug/log).
     */
    protected string $resolvedSource = 'unknown';

    public function index()
    {
        $points = $this->resolvePoints();

        return view('owner.financial.points.index', [
            'points' => $points,
            'source' => $this->resolvedSource,
        ]);
    }

    public function leaderboard()
    {
        $points = $this->resolvePoints(limit: 100);

        return view('owner.financial.points.leaderboard', [
            'leaderboard' => $points,
            'points'      => $points,
            'source'      => $this->resolvedSource,
        ]);
    }

    /**
     * Bangun data poin dari sumber yang paling memungkinkan.
     * Selalu mengembalikan paginator atau collection — tidak pernah crash.
     */
    protected function resolvePoints(?int $limit = null)
    {
        // ─── Strategi 1: model class ────────────────────────────
        foreach ($this->modelCandidates as $class) {
            if (!class_exists($class)) continue;

            try {
                $query = $class::query()->with('user')->orderByDesc('balance');
                $this->resolvedSource = 'model: ' . class_basename($class);

                return $limit
                    ? $query->limit($limit)->get()
                    : $query->paginate(15);
            } catch (\Throwable $e) {
                Log::warning("[PointController] {$class} exists but query failed", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ─── Strategi 2: tabel langsung ─────────────────────────
        foreach ($this->tableCandidates as $table) {
            if (!Schema::hasTable($table)) continue;

            try {
                $hasBalance = Schema::hasColumn($table, 'balance');
                $orderCol   = $hasBalance ? 'balance' : 'id';

                $query = DB::table($table)
                    ->leftJoin('users', "{$table}.user_id", '=', 'users.id')
                    ->select(
                        "{$table}.*",
                        'users.name as __user_name',
                        'users.nim  as __user_nim',
                        'users.id   as __user_id'
                    )
                    ->orderByDesc("{$table}.{$orderCol}");

                $rows = $limit
                    ? $query->limit($limit)->get()
                    : $query->paginate(15);

                // Normalisasi bentuk agar view cukup akses ->user->name & ->balance
                $rows->through(fn ($row) => (object) [
                    'id'      => $row->id ?? null,
                    'balance' => (int) ($row->balance ?? 0),
                    'user'    => (object) [
                        'id'   => $row->__user_id ?? $row->user_id ?? null,
                        'name' => $row->__user_name ?? 'Tanpa nama',
                        'nim'  => $row->__user_nim ?? null,
                    ],
                ]);

                $this->resolvedSource = 'tabel: ' . $table;

                return $rows;
            } catch (\Throwable $e) {
                Log::warning("[PointController] tabel {$table} gagal", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ─── Strategi 3: derive dari users + point_histories ────
        if (Schema::hasTable('point_histories')) {
            try {
                $query = User::query()
                    ->whereIn('level_id', [3, 4, 5])
                    ->withSum(
                        ['pointHistories as balance' => fn ($q) => $q->where('status', 'approved')],
                        'points'
                    )
                    ->orderByDesc('balance');

                $rows = $limit
                    ? $query->limit($limit)->get()
                    : $query->paginate(15);

                $rows->through(fn ($u) => (object) [
                    'id'      => $u->id,
                    'balance' => (int) ($u->balance ?? 0),
                    'user'    => (object) [
                        'id'   => $u->id,
                        'name' => $u->name,
                        'nim'  => $u->nim,
                    ],
                ]);

                $this->resolvedSource = 'derive: users + point_histories';

                return $rows;
            } catch (\Throwable $e) {
                Log::warning('[PointController] derivation via point_histories gagal', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ─── Strategi 4: fallback aman (halaman kosong) ─────────
        Log::info('[PointController] Tidak ada sumber poin ditemukan. Menampilkan halaman kosong.');

        $this->resolvedSource = 'kosong';

        if ($limit) {
            return collect();
        }

        return new LengthAwarePaginator([], 0, 15);
    }
}