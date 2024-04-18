<?php

namespace App\Exceptions;

use Exception;

class UnknownFnsNotamMessageType extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct("Unhandled message type: [$type]");
    }
}
