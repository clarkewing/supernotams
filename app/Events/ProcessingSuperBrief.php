<?php

namespace App\Events;

class ProcessingSuperBrief extends FlightEvent
{
    public function broadcastAs(): string
    {
        return 'app.superbrief.processing';
    }
}
