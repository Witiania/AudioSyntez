<?php

namespace App\Exception;

class EmailTransactionException extends \Exception
{
    public function __construct(string $message = 'Failed to send email', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
