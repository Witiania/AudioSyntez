<?php

namespace App\Exception;

class UserNotFoundException extends AbstractCustomException
{
    public function __construct(
        string $message = 'User not found',
        int $code = 404,
        ?\Throwable $previous = null,
        string $level = parent::INFO
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
