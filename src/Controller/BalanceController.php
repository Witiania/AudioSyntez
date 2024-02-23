<?php

namespace App\Controller;

use App\DTO\BalanceRequestDTO;
use App\Exception\BalanceTransactionException;
use App\Exception\IllegalAccessException;
use App\Exception\UserNotFoundException;
use App\Service\BalanceService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/balance', name: 'balance')]
#[OA\Tag(name: 'Balance')]
class BalanceController extends AbstractController
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(name: 'balance_replenish', methods: 'PUT')]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: BalanceRequestDTO::class
                )
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
        response: 403,
        description: 'Insufficient funds/Permission denied'
    )]
    public function balanceReplenish(BalanceRequestDTO $requestDTO): JsonResponse
    {
        try {
            $this->balanceService->replenish($requestDTO->getAmount(), $requestDTO->getId());
        } catch (BalanceTransactionException|IllegalAccessException $e) {
            $this->logger->error($e);

            return new JsonResponse($e->getMessage(), 403);
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse('Success');
    }

    #[Route(name: 'balance_view', methods: 'GET')]
    #[OA\Response(response: 200, description: "Returns user's balance. Type INT")]
    #[OA\Response(response: 404, description: 'User not found')]
    public function balanceView(): JsonResponse
    {
        try {
            return new JsonResponse($this->balanceService->view());
        } catch (UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }
}
