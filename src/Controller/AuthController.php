<?php

namespace App\Controller;

use App\DTO\RegistrationRequestDTO;
use App\DTO\ResetRequestDTO;
use App\DTO\SendResetRequestDTO;
use App\DTO\VerifyRequestDTO;
use App\Exceptions\DuplicatedException;
use App\Exceptions\EmailException;
use App\Exceptions\UserNotFoundException;
use App\Services\AuthService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/register', name: 'register', methods: 'POST')]
    public function register(RegistrationRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->register(
                $requestDTO->getEmail(),
                $requestDTO->getPhone(),
                $requestDTO->getName(),
                $requestDTO->getPassword(),
            );
        } catch (EmailException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(), 500);
        } catch (DuplicatedException $e) {
            return new JsonResponse($e->getMessage(), 404);
        } catch (\Throwable $e) {
            $this->logger->critical($e);

            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse('Register success');
    }

    #[Route('/send_for_reset', name: 'send_for_reset', methods: 'POST')]
    public function sendResetCode(SendResetRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->sendResetCode($requestDTO->getEmail());
        } catch (EmailException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(), 500);
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        } catch (\Throwable $e) {
            $this->logger->critical($e);

            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse('The key has been sent by email');
    }

    #[Route('/reset', name: 'reset', methods: 'POST')]
    public function reset(ResetRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->resetPassword(
                $requestDTO->getEmail(),
                $requestDTO->getToken(),
                $requestDTO->getNewPassword()
            );
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        } catch (\Throwable) {
            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse('New password added');
    }

    #[Route('/verify', name: 'verify', methods: 'POST')]
    public function verify(VerifyRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->authService->verify($requestDTO->getEmail(), $requestDTO->getToken());
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse('Success');
    }
}
