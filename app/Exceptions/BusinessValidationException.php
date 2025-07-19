<?php

namespace App\Exceptions;

use Exception;

class BusinessValidationException extends Exception
{
    public function __construct($message = "Business validation failed", $code = 422, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 