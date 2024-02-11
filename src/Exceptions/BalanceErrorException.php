<?php

namespace App\Exceptions;

class BalanceErrorException extends \Exception
{
    public function __construct(string $message = 'Balance error', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
