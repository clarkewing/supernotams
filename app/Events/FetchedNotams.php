<?php

namespace App\Events;

use App\Models\Flight;

class FetchedNotams extends FlightEvent
{
    public function __construct(
        Flight $flight,
        public int $notamCount,
    ) {
        parent::__construct($flight);
    }

    public function broadcastAs(): string
    {
        return 'app.notams.fetched';
    }
}
