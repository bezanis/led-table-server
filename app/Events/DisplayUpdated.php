<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisplayUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $state) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('display'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'display.updated';
    }

    public function broadcastWith(): array
    {
        return ['state' => $this->state];
    }
}
