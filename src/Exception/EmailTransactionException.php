<?php

namespace App\Exception;

class EmailTransactionException extends AbstractCustomException
{
    public function __construct(
        string $level = parent::ERROR,
        string $message = 'Failed to send email',
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
