<?php

namespace App\Exception;

class BalanceTransactionException extends AbstractCustomException
{
    public function __construct(
        string $level = parent::INFO,
        string $message = 'Insufficient funds',
        int $code = 402,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
