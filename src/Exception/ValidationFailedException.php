<?php

namespace App\Exception;

class ValidationFailedException extends AbstractCustomException
{
    public function __construct(
        string $level = parent::INFO,
        string $message = 'Validation failed',
        int $code = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
