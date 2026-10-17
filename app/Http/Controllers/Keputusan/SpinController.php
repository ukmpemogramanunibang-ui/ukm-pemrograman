<?php

namespace App\Http\Controllers\Keputusan;

use App\Events\WheelSpun;
use App\Events\WheelCompleted;
use App\Http\Controllers\Controller;
use App\Models\DecisionWheel;
use App\Models\DecisionWheelSpin;
use App\Services\WheelEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpinController extends Controller
{
    public function __construct(protected WheelEngineService $engine) {}

    /**
     * POST /keputusan/roda/{token}/spin
     * Idempotent — aman dari double-click & race condition.
     */
    public function store(Request $request, string $token)
    {
        $wheel = DecisionWheel::where('public_token', $token)->firstOrFail();

        $controllerToken = $request->input('controller_token')
            ?? $request->header('X-Controller-Token')
            ?? $request->session()->get('controller_token_' . $wheel->id)
            ?? $request->session()->get('controller_token');

        $isOperator = $wheel->isOperator($controllerToken)
            || ($wheel->user_id && $wheel->user_id === auth()->id());

        if (! $isOperator) {
            return response()->json(['message' => 'Anda bukan operator roda ini.'], 403);
        }

        try {
            $result = $this->engine->spin($wheel, auth()->id());
            $spin   = $result['spin'];
            $reused = $result['reused'];
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // 409 CONFLICT — kembalikan state aktif supaya client bisa recovery
            $active = DecisionWheelSpin::where('wheel_id', $wheel->id)
                ->where('status', 'running')
                ->latest('id')
                ->first();

            return response()->json([
                'message' => $e->getMessage(),
                'recover' => $active ? $this->spinPayload($wheel, $active) : null,
            ], $e->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('[Wheel] Spin gagal', ['err' => $e->getMessage(), 'wheel' => $wheel->id]);
            return response()->json(['message' => 'Gagal memutar roda: ' . $e->getMessage()], 500);
        }

        // Broadcast hanya kalau spin BARU (bukan reused)
        if (! $reused) {
            try {
                broadcast(new WheelSpun($spin->load('wheel')))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('[Wheel] Broadcast gagal', ['err' => $e->getMessage()]);
            }
        }

        return response()->json($this->spinPayload($wheel, $spin));
    }

    /**
     * GET /keputusan/roda/{token}/state
     * Endpoint recovery — dipanggil saat page load, reconnect, atau visibility change.
     */
    public function state(string $token)
    {
        $wheel = DecisionWheel::where('public_token', $token)->firstOrFail();

        // Cari spin yang sedang running
        $active = DecisionWheelSpin::where('wheel_id', $wheel->id)
            ->where('status', 'running')
            ->latest('id')
            ->first();

        // Kalau spin sudah lewat durasi tapi masih running → auto-complete
        if ($active && ! $active->isActive()) {
            $this->engine->complete($active);

            try {
                broadcast(new WheelCompleted($active->load('wheel')))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('[Wheel] Broadcast complete gagal', ['err' => $e->getMessage()]);
            }

            $active = null;
        }

        // Spin terakhir yang selesai (untuk ditampilkan)
        $lastCompleted = DecisionWheelSpin::where('wheel_id', $wheel->id)
            ->where('status', 'completed')
            ->latest('id')
            ->first();

        return response()->json([
            'server_time'    => now()->toIso8601String(),
            'wheel' => [
                'status'        => $wheel->status,
                'is_spinning'   => (bool) $active,
                'locked_until'  => optional($wheel->locked_until)->toIso8601String(),
            ],
            'active_spin'    => $active ? $this->spinPayload($wheel, $active, withRecovery: true) : null,
            'last_completed' => $lastCompleted ? $this->spinPayload($wheel, $lastCompleted) : null,
            'recent_spins'   => DecisionWheelSpin::where('wheel_id', $wheel->id)
                ->latest('id')
                ->take(5)
                ->get()
                ->map(fn ($s) => [
                    'spin_number' => $s->spin_number,
                    'status'      => $s->status,
                    'created_at'  => $s->created_at->toIso8601String(),
                ]),
        ]);
    }

    /**
     * POST /keputusan/roda/{token}/complete/{spin}
     */
    public function complete(string $token, int $spinId)
    {
        $wheel = DecisionWheel::where('public_token', $token)->firstOrFail();
        $spin  = $wheel->spins()->findOrFail($spinId);

        $this->engine->complete($spin);

        try {
            broadcast(new WheelCompleted($spin->load('wheel')))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('[Wheel] Broadcast complete gagal', ['err' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------ */

    /**
     * Payload standar untuk spin — dipakai oleh semua endpoint.
     */
    protected function spinPayload(DecisionWheel $wheel, DecisionWheelSpin $spin, bool $withRecovery = false): array
    {
        $winnerIds = $spin->winner_ids ?? [];
        $winners = empty($winnerIds)
            ? []
            : $wheel->entries()
                ->whereIn('id', $winnerIds)
                ->get(['id', 'name'])
                ->map(fn ($e) => ['id' => $e->id, 'name' => $e->name])
                ->values()
                ->all();

        // Urutkan sesuai winner_ids
        if (! empty($winnerIds)) {
            $map = collect($winners)->keyBy('id');
            $winners = collect($winnerIds)
                ->map(fn ($id) => $map->get($id))
                ->filter()
                ->values()
                ->all();
        }

        $payload = [
            'spin_id'      => $spin->id,
            'spin_number'  => $spin->spin_number,
            'duration'     => (int) $spin->duration,
            'start_angle'  => (float) $spin->start_angle,
            'target_angle' => (float) $spin->target_angle,
            'winners'      => $winners,
            'status'       => $spin->status,
            'started_at'   => optional($spin->started_at)->toIso8601String(),
        ];

        if ($withRecovery) {
            $payload['elapsed_ms']   = $spin->elapsedMs();
            $payload['remaining_ms'] = $spin->remainingMs();
        }

        return $payload;
    }
}