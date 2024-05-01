<?php

namespace App\Events;

use App\Models\Flight;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlightEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $queue = 'broadcast';

    public function __construct(
        public Flight $flight,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('flight'.$this->flight->id),
        ];
    }
}
