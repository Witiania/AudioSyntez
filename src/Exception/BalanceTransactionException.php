<?php

namespace App\Exception;

class BalanceTransactionException extends \Exception
{
    public function __construct(string $message = 'Insufficient funds', int $code = 402, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
