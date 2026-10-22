<?php

namespace App\Services;

use App\Models\DecisionWheel;
use App\Models\DecisionWheelSpin;
use Illuminate\Support\Facades\DB;

class WheelEngineService
{
    /**
     * @return array{spin: DecisionWheelSpin, reused: bool}
     */
    public function spin(DecisionWheel $wheel, ?int $operatorId = null): array
    {
        return DB::transaction(function () use ($wheel, $operatorId) {
            $wheel = DecisionWheel::whereKey($wheel->id)->lockForUpdate()->first();

            // ✅ IDEMPOTENT — kalau ada spin aktif dalam 2 detik terakhir, kembalikan itu
            $active = DecisionWheelSpin::where('wheel_id', $wheel->id)
                ->where('status', 'running')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($active && $active->isActive()) {
                // Kalau baru dibuat (< 2 detik), return yang sama → idempotent
                if ($active->started_at->diffInSeconds(now()) < 2) {
                    return ['spin' => $active, 'reused' => true];
                }

                // Kalau sudah berjalan > 2 detik, tolak (mencegah double spin)
                abort(409, 'Roda sedang diputar.');
            }

            $entries = $wheel->entries()->where('is_active', true)->get();
            if ($entries->count() < 2) {
                abort(422, 'Minimal 2 peserta untuk memutar roda.');
            }

            $winnerCount = $wheel->result_mode === 'single' ? 1 : max(1, $wheel->winner_count);

            $winners = match ($wheel->result_mode) {
                'single'  => [$this->pickWeighted($entries, $wheel->selection_mode)],
                'multi'   => $this->pickMultiple($entries, $winnerCount, $wheel->selection_mode),
                'ranking' => $this->pickRanking($entries, $wheel->selection_mode),
                default   => [$this->pickWeighted($entries, $wheel->selection_mode)],
            };

            $startAngle  = (float) ($wheel->spins()->max('target_angle') ?? 0);
            $targetAngle = $this->computeTargetAngle($wheel, $entries, $winners[0]->id, $startAngle);

            $spin = DecisionWheelSpin::create([
                'wheel_id'         => $wheel->id,
                'operator_user_id' => $operatorId,
                'spin_number'      => ($wheel->spins()->max('spin_number') ?? 0) + 1,
                'duration'         => $wheel->duration,
                'winner_id'        => $winners[0]->id,
                'winner_ids'       => collect($winners)->pluck('id')->all(),
                'start_angle'      => $startAngle,
                'target_angle'     => $targetAngle,
                'started_at'       => now(),
                'status'           => 'running',
            ]);

            $wheel->update([
                'status'       => 'spinning',
                'locked_until' => now()->addSeconds($wheel->duration + 2),
            ]);

            return ['spin' => $spin, 'reused' => false];
        });
    }

    public function complete(DecisionWheelSpin $spin): DecisionWheelSpin
    {
        if ($spin->status === 'completed') return $spin;

        $spin->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $spin->wheel->update([
            'status'       => 'finished',
            'locked_until' => null,
        ]);

        return $spin->fresh();
    }

    /* ------------------------------------------------------------------ */

    protected function pickWeighted($entries, string $mode)
    {
        if ($mode === 'secret_random') {
            return $entries->random();
        }

        $total = $entries->sum('weight');
        if ($total === 0) return $entries->random();

        $rand = random_int(1, $total);
        $acc  = 0;

        foreach ($entries as $entry) {
            $acc += $entry->weight;
            if ($rand <= $acc) return $entry;
        }

        return $entries->last();
    }

    protected function pickMultiple($entries, int $count, string $mode): array
    {
        $pool = $entries->all();
        $picked = [];
        $count = min($count, count($pool));

        while (count($picked) < $count && ! empty($pool)) {
            $candidate = $this->pickWeighted(collect($pool), $mode);
            $pool = array_values(array_filter($pool, fn ($e) => $e->id !== $candidate->id));
            $picked[] = $candidate;
        }

        return $picked;
    }

    protected function pickRanking($entries, string $mode): array
    {
        $pool = $entries->all();
        $result = [];

        while (! empty($pool)) {
            $candidate = $this->pickWeighted(collect($pool), $mode);
            $pool = array_values(array_filter($pool, fn ($e) => $e->id !== $candidate->id));
            $result[] = $candidate;
        }

        return $result;
    }

    protected function computeTargetAngle(
        DecisionWheel $wheel,
        $entries,
        int $winnerId,
        float $startAngle
    ): float {
        $total = $wheel->selection_mode === 'weighted'
            ? max(1, $entries->sum('weight'))
            : $entries->count();

        $cursor = 0;
        $center = 0;

        foreach ($entries as $entry) {
            $size = $wheel->selection_mode === 'weighted'
                ? (($entry->weight / $total) * 360)
                : (360 / $entries->count());

            if ($entry->id === $winnerId) {
                $center = $cursor + ($size / 2);
                break;
            }
            $cursor += $size;
        }

        $fullSpins    = random_int(5, 8) * 360;
        $pointerAngle = 360 - $center;

        return $startAngle + $fullSpins + $pointerAngle;
    }
}