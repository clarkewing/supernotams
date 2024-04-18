<?php

namespace App\Enum\Fns;

enum NotamClassification: string
{
    /**
     * Any NOTAM intended for distribution to more than one country would be considered an international NOTAM.
     * This would include NOTAMs stored in ICAO format in the United States NOTAM System (USNS) or published in the
     * International NOTAMs section of the NTAP. The USNS stores international NOTAMs separately from domestic NOTAMs,
     * but only for selected locations both inside and outside the United States.
     */
    case International = 'INTL';

    /**
     * NOTAMs that are primarily distributed within the United States, although they may also be available in Canada.
     * Domestic NOTAMs stored in the USNS are coded in a domestic format rather than an ICAO format.
     */
    case Domestic = 'DOM';

    /**
     * Any NOTAM that is part of the military NOTAM system which primarily includes NOTAMs on military airports
     * and military airspace.
     */
    case Military = 'MIL';

    /**
     * Flight Data Center NOTAMs are NOTAMs that are regulatory in nature such as changes to an instrument approach
     * procedure or airway. Temporary Flight Restrictions (TFRs) are also issued as FDC NOTAMs.
     */
    case FlightDataCenter = 'FDC';
}
