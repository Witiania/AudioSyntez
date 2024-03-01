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
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = sprintf('%s', $exception->getMessage(),
        );
        $response = new JsonResponse();

        switch (true) {
            case $exception instanceof ValidationFailedException:
                $response->setData(['message' => $message]);
                $response->setStatusCode(400);
                $this->logger->info($message, ['exception' => $exception]);
                break;
            case $exception instanceof DuplicateException:
                $response->setData(['message' => $message]);
                $response->setStatusCode(409);
                $this->logger->warning($message, ['exception' => $exception]);
                break;
            default:
                $response->setData(['message' => 'Internal server error']);
                $response->setStatusCode(500);
                $this->logger->error($message, ['exception' => $exception]);
                break;
        }
        $event->setResponse($response);
    }
}
