<?php

namespace App\Exception;

use Psr\Log\LoggerInterface;

class AbstractCustomException extends \Exception
{
    private string $level;

    protected const WARNING = 'WARNING';
    protected const INFO = 'INFO';
    protected const ERROR = 'ERROR';

    public function __construct(string $message, int $code, ?\Throwable $previous, string $level)
    {
        parent::__construct($message, $code, $previous);

        $this->level = $level;
    }

    public function log(LoggerInterface $logger): void
    {
        match ($this->level) {
            self::INFO => $logger->info($this),
            self::WARNING => $logger->warning($this),
            self::ERROR => $logger->error($this),
            default => null
        };
    }
}
