<?php

namespace App\Controller;

use App\Exceptions\DuplicatedException;
use App\Exceptions\EmailException;
use App\Exceptions\UserNotFoundException;
use App\Services\AuthService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['name']) || empty($data['phone']) || empty($data['password'])) {
            return new JsonResponse('Bad request', 400);
        }

        try {
            $this->authService->register($data);
        } catch (EmailException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(). 500);
        } catch (DuplicatedException $e) {
            return new JsonResponse($e->getMessage(), 404);
        } catch (\Throwable $e) {
            $this->logger->critical($e);

            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse('Register success');
    }

    #[Route('/send_for_reset', name: 'send_for_reset', methods: 'POST')]
    public function sendResetCode(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email'])) {
            return new JsonResponse('Bad request', 400);
        }

        try {
            $this->authService->sendResetCode($data['email']);
        } catch (EmailException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(). 500);
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        } catch (\Throwable $e) {
            $this->logger->critical($e);

            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse('The key has been sent by email');
    }

    #[Route('/reset', name: 'reset', methods: 'POST')]
    public function reset(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['token']) || empty($data['newPassword'])) {
            return new JsonResponse('Bad request', 400);
        }

        try {
            $this->authService->resetPassword($data['email'], $data['token'], $data['newPassword']);
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        } catch (\Throwable) {
            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse('New password added');
    }

    #[Route('/verify', name: 'verify', methods: 'POST')]
    public function verify(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['token'])) {
            return new JsonResponse('Bad request', 400);
        }

        try {
            $this->authService->verify($data['email'], $data['token']);
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse('Success');
    }
}
