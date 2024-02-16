<?php

namespace App\Exceptions;

class EmailException extends \Exception
{
    public function __construct(string $message = 'Failed to send email ', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
