<?php

namespace App\Events;

use App\Models\DecisionWheelSpin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WheelCompleted implements ShouldBroadcastNow
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
        return 'wheel.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'spin_id'     => $this->spin->id,
            'spin_number' => $this->spin->spin_number,
        ];
    }
}