<?php

namespace App\Exception;

class DuplicateException extends AbstractCustomException
{
    public function __construct(
        string $level = parent::INFO,
        string $message = 'User already exists',
        int $code = 409,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
