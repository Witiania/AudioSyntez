<?php

namespace App\Exception;

class IllegalAccessException extends \Exception
{
    public function __construct(string $message = 'Permission denied', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
