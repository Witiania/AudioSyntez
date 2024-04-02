<?php

namespace App\Exception;

class EmailTransactionException extends AbstractCustomException
{
    public function __construct(
        string $message = 'Failed to send email',
        int $code = 500,
        ?\Throwable $previous = null,
        string $level = parent::ERROR
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
