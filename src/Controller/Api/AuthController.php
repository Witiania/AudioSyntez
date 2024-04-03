<?php

namespace App\Controller\Api;

use App\DTO\RegistrationRequestDTO;
use App\DTO\ResetPasswordRequestDTO;
use App\DTO\SendResetCodeRequestDTO;
use App\DTO\VerifyEmailRequestDTO;
use App\Exception\DuplicateException;
use App\Exception\EmailTransactionException;
use App\Exception\UserNotFoundException;
use App\Service\AuthService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
#[OA\Tag(name: 'Authentication')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * @throws DuplicateException
     * @throws EmailTransactionException
     */
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
        response: 409,
        description: 'User already exists'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function register(RegistrationRequestDTO $requestDTO): JsonResponse
    {
        $this->authService->register(
            $requestDTO->getEmail(),
            $requestDTO->getPhone(),
            $requestDTO->getName(),
            $requestDTO->getPassword()
        );

        return new JsonResponse('Success');
    }

    /**
     * @throws UserNotFoundException
     * @throws EmailTransactionException
     */
    #[Route('/send_for_reset', name: 'send_for_reset', methods: 'POST')]
    #[OA\Post(
        path: '/api/send_for_reset',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: SendResetCodeRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'The key has been sent by email'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function sendResetCode(SendResetCodeRequestDTO $requestDTO): JsonResponse
    {
        $this->authService->sendResetCode($requestDTO->getEmail());

        return new JsonResponse('Success');
    }

    /**
     * @throws UserNotFoundException
     */
    #[Route('/reset', name: 'reset', methods: 'POST')]
    #[OA\Post(
        path: '/api/reset',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: ResetPasswordRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'New password added'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function reset(ResetPasswordRequestDTO $requestDTO): JsonResponse
    {
        $this->authService->resetPassword(
            $requestDTO->getEmail(),
            $requestDTO->getToken(),
            $requestDTO->getPassword()
        );

        return new JsonResponse('Success');
    }

    /**
     * @throws UserNotFoundException
     */
    #[Route('/verify', name: 'verify', methods: 'POST')]
    #[OA\Post(
        path: '/api/verify',
        security: [],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: VerifyEmailRequestDTO::class)
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
    public function verify(VerifyEmailRequestDTO $requestDTO): JsonResponse
    {
        $this->authService->verify($requestDTO->getEmail(), $requestDTO->getToken());

        return new JsonResponse('Success');
    }
}
