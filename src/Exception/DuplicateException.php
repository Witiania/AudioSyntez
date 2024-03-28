<?php

namespace App\Exception;

class DuplicateException extends AbstractCustomException
{
    public function __construct(
        string $message = 'User already exists',
        int $code = 409,
        ?\Throwable $previous = null,
        string $level = parent::INFO
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
