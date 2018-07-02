<?php

namespace App\Exceptions;

use Exception;

class PointFactoryException extends Exception
{
    public function __construct(string $type)
    {
        $this->message = "El tipo de punto '$type' no existe";
    }
}
