<?php

namespace App\Events;

class FetchingNotams extends FlightEvent
{
    public function broadcastAs(): string
    {
        return 'app.notams.fetching';
    }
}
