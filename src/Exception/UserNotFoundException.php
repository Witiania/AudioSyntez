<?php

namespace App\Exception;

class UserNotFoundException extends AbstractCustomException
{
    public function __construct(
        string $level = parent::INFO,
        string $message = 'User not found',
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
