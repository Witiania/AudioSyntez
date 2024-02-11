<?php

namespace App\Exceptions;

class DuplicatedException extends \Exception
{
    public function __construct(string $message = 'User already exists', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
