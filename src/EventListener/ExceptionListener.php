<?php

namespace App\EventListener;

use App\Exception\DuplicateException;
use App\Exception\ValidationFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = sprintf(
            'Error: %s',
            $exception->getMessage(),
        );

        $response = new JsonResponse();
        $response->setContent($message);

        switch (true) {
            case $exception instanceof ValidationFailedException:
                $response->setStatusCode(400);
                $this->logger->info($message, ['exception' => $exception]);
                break;
            case $exception instanceof DuplicateException:
                $response->setContent($exception->getMessage());
                $response->setStatusCode(409);
                $this->logger->warning($message, ['exception' => $exception]);
                break;
            default:
                $response->setContent('Internal server error');
                $response->setStatusCode(500);
                $this->logger->error($message, ['exception' => $exception]);
                break;
        }
        $event->setResponse($response);
    }
}
