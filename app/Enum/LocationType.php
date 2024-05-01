<?php

namespace App\Enum;

enum LocationType: string
{
    case Departure = 'departure';

    case TakeoffAlternate = 'takeoff_alternate';

    case Destination = 'destination';

    case DestinationAlternate = 'destination_alternate';

    case Enroute = 'enroute';

    case Fir = 'fir';
}
