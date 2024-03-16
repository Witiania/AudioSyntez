<?php

namespace App\Exception;

class IllegalAccessException extends AbstractCustomException
{
    public function __construct(
        string $level = parent::WARNING,
        string $message = 'Permission denied',
        int $code = 403,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
