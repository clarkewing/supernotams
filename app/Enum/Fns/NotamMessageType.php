<?php

namespace App\Enum\Fns;

enum NotamMessageType: string
{
    /**
     * A NOTAM message formatted per ICAO specifications.
     */
    case ICAO = 'OTHER:ICAO';

    /**
     * A NOTAM message formatted per US Domestic NOTAM specifications — AKA: a shitshow.
     */
    case Domestic = 'LOCAL_FORMAT';
}
