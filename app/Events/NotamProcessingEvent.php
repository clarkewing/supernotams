<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;

class NotamProcessingEvent implements ShouldBroadcast
{
    public string $queue = 'broadcast';

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        protected string $channelName,
        public string $message,
        public string $type = 'success',
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel($this->channelName);
    }
}
