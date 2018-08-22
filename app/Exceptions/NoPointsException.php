<?php

namespace App\Exceptions;

use Exception;

class NoPointsException extends Exception
{
    public function __construct()
    {
        $this->message = "No existe ningún punto en la base de datos";
    }
}
