<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $userId;
    public $userName;
    public $redirectUrl;

    public function __construct($userId, $userName, $redirectUrl)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->redirectUrl = $redirectUrl;
    }

    public function broadcastOn()
    {
        return new Channel('user-status');
    }
}
