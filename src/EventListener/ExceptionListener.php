<?php

namespace App\EventListener;

use App\Exception\AbstractCustomException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

#[AsEventListener]
class ExceptionListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse(['message' => $exception->getMessage()]);

        switch ($exception::class) {
            case $exception instanceof AbstractCustomException:
                $exception->log($this->logger);
                $response->setStatusCode($exception->getCode());
                break;
            case MethodNotAllowedHttpException::class:
            case NotFoundHttpException::class:
                $this->logger->warning($exception);
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setContent('Page not found');
                break;
            case NotNormalizableValueException::class:
            case BadRequestHttpException::class:
                $this->logger->info($exception);
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                break;
            case \TypeError::class:
                $this->logger->info($exception);
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent('Validation error');
                break;
            default:
                $this->logger->error($exception);
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                $response->setContent('Internal server error');
                break;
        }

        $event->setResponse($response);
    }
}
