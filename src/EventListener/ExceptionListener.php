<?php

namespace App\EventListener;

use App\Exception\DuplicateException;
use App\Exception\ValidationFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $response->setData(['message' => $message]);

        switch (true) {
            case $exception instanceof ValidationFailedException:
                $response->setStatusCode(400);
                $this->logger->info($message, ['exception' => $exception]);
                break;
            case $exception instanceof DuplicateException:
                $response->setStatusCode(409);
                $this->logger->warning($message, ['exception' => $exception]);
                break;
            case $exception instanceof NotFoundHttpException:
                $response->setStatusCode(404);
                $response->setData(['message' => 'Page not found']);
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
