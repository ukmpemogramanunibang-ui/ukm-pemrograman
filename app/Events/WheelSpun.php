<?php

namespace App\Events;

use App\Models\DecisionWheelSpin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;   // ⬅️ UBAH
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WheelSpun implements ShouldBroadcastNow                   // ⬅️ UBAH
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DecisionWheelSpin $spin)
    {
        $this->spin->loadMissing('wheel');
    }

    public function broadcastOn(): array
    {
        return [new Channel('wheel.' . $this->spin->wheel->public_token)];
    }

    public function broadcastAs(): string
    {
        return 'wheel.spun';
    }

    public function broadcastWith(): array
    {
        $wheel = $this->spin->wheel;
        $winnerIds = $this->spin->winner_ids ?? [];

        $winners = empty($winnerIds)
            ? []
            : $wheel->entries()
                ->whereIn('id', $winnerIds)
                ->get(['id', 'name'])
                ->map(fn ($e) => ['id' => $e->id, 'name' => $e->name])
                ->values()
                ->toArray();

        return [
            'spin_id'      => $this->spin->id,
            'spin_number'  => $this->spin->spin_number,
            'duration'     => (int) $this->spin->duration,
            'start_angle'  => (float) $this->spin->start_angle,
            'target_angle' => (float) $this->spin->target_angle,
            'winners'      => $winners,
        ];
    }
}