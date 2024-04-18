<?php

namespace App\Enum\Fns;

enum NotamStatus: string
{
    /**
     * A NOTAM which is currently valid and active.
     */
    case Active = 'ACTIVE';

    /**
     * A NOTAM which has expired.
     */
    case Expired = 'EXPIRED';

    /**
     * Any NOTAM that has been cancelled by a NOTAMC message.
     */
    case Cancelled = 'CANCELLED';
}
