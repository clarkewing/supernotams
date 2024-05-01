<?php

namespace App\Exceptions;

use Exception;

class InvalidAtsMessageException extends Exception
{
    public function __construct()
    {
        parent::__construct('The provided ATS Message is invalid. Only FPL messages are accepted.');
    }
}
