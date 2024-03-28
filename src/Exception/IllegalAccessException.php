<?php

namespace App\Exception;

class IllegalAccessException extends AbstractCustomException
{
    public function __construct(
        string $message = 'Permission denied',
        int $code = 403,
        ?\Throwable $previous = null,
        string $level = parent::WARNING
    ) {
        parent::__construct($message, $code, $previous, $level);
    }
}
