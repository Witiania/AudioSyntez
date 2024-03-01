<?php

namespace App\Controller\Api;

use App\DTO\RegistrationRequestDTO;
use App\DTO\ResetRequestDTO;
use App\DTO\SendResetRequestDTO;
use App\DTO\VerifyRequestDTO;
use App\Exception\EmailTransactionException;
use App\Exception\UserNotFoundException;
use App\Service\AuthService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
#[OA\Tag(name: 'Authentication')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/register', name: 'register', methods: 'POST')]
    #[OA\Post(
        path: '/api/register',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: RegistrationRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Register success'
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    #[OA\Response(
        response: 409,
        description: 'User already exists'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function register(RegistrationRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->register(
                $requestDTO->getEmail(),
                $requestDTO->getPhone(),
                $requestDTO->getName(),
                $requestDTO->getPassword()
            );
        } catch (EmailTransactionException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);

            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Register success']);
    }

    #[Route('/send_for_reset', name: 'send_for_reset', methods: 'POST')]
    #[OA\Post(
        path: '/api/send_for_reset',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: SendResetRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'The key has been sent by email'
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function sendResetCode(SendResetRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->sendResetCode($requestDTO->getEmail());
        } catch (EmailTransactionException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);

            return new JsonResponse(['message' => $e->getMessage()], 500);
        } catch (UserNotFoundException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);

            return new JsonResponse(['message' => $e->getMessage()], 404);
        }

        return new JsonResponse(['message' => 'The key has been sent by email']);
    }

    #[Route('/reset', name: 'reset', methods: 'POST')]
    #[OA\Post(
        path: '/api/reset',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: ResetRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'New password added'
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function reset(ResetRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->resetPassword(
                $requestDTO->getEmail(),
                $requestDTO->getToken(),
                $requestDTO->getPassword()
            );
        } catch (UserNotFoundException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);

            return new JsonResponse(['message' => $e->getMessage()], 404);
        }

        return new JsonResponse(['message' => 'New password added']);
    }

    #[Route('/verify', name: 'verify', methods: 'POST')]
    #[OA\Post(
        path: '/api/verify',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: VerifyRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Success'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function verify(VerifyRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->verify($requestDTO->getEmail(), $requestDTO->getToken());
        } catch (UserNotFoundException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);

            return new JsonResponse(['message' => $e->getMessage()], 404);
        }

        return new JsonResponse(['message' => 'Success']);
    }
}
