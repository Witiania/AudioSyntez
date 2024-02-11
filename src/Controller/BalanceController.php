<?php

namespace App\Controller;

use App\Exceptions\BalanceErrorException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UserNotFoundException;
use App\Services\BalanceService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BalanceController extends AbstractController
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/balance', name: 'balance_replenish', methods: 'PUT')]
    public function balanceReplenish(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['id']) || !isset($data['amount']) || !is_int($data['amount'])) {
            return new JsonResponse('Amount and user is required', 400);
        }

        try {
            $this->balanceService->replenish($data['amount'], $data['id']);
        } catch (BalanceErrorException|ForbiddenException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(), 500);
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
