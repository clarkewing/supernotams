<?php

namespace App\Exceptions;

use Exception;

class MissingFnsNotamMessageType extends Exception
{
    public function __construct()
    {
        parent::__construct('NOTAM message type missing.');
    }
}
