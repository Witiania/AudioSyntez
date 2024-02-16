<?php

namespace App\Controller;

use App\DTO\BalanceRequestDTO;
use App\Exceptions\BalanceErrorException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UserNotFoundException;
use App\Services\BalanceService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BalanceController extends AbstractController
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/balance', name: 'balance_replenish', methods: 'PUT')]
    public function balanceReplenish(BalanceRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->balanceService->replenish($requestDTO->getAmount(), $requestDTO->getId());
        } catch (BalanceErrorException|ForbiddenException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(), 403);
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse('Success');
    }

    #[Route('/balance', name: 'balance_view', methods: 'GET')]
    public function balanceView(): JsonResponse
    {
        try {
            return new JsonResponse($this->balanceService->view());
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }
}
