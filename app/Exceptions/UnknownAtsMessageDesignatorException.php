<?php

namespace App\Exceptions;

use Exception;

class UnknownAtsMessageDesignatorException extends Exception
{
    public function __construct(string $designator)
    {
        parent::__construct("Unknown ATS Message designator [$designator].");
    }
}
