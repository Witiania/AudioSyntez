<?php

namespace App\Exception;

class ValidationFailedException extends AbstractCustomException
{
    public function __construct(
        string $message = 'Validation failed',
        int $code = 400,
        ?\Throwable $previous = null,
        string $level = parent::INFO
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
