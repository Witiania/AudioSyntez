<?php

namespace App\Exception;

class DuplicateException extends \Exception
{
    public function __construct(string $message = 'User already exists', int $code = 409, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
