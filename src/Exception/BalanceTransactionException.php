<?php

namespace App\Exception;

class BalanceTransactionException extends AbstractCustomException
{
    public function __construct(
        string $message = 'Insufficient funds',
        int $code = 402,
        ?\Throwable $previous = null,
        string $level = parent::INFO
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
