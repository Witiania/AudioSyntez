<?php

namespace App\Exception;

class ValidationFailedException extends \Exception
{
    public function __construct(string $message = 'Validation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
