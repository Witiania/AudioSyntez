<?php

namespace App\Controller\Api;

use App\DTO\BalanceRequestDTO;
use App\Exception\BalanceTransactionException;
use App\Exception\IllegalAccessException;
use App\Exception\UserNotFoundException;
use App\Service\BalanceService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/balance', name: 'balance')]
#[OA\Tag(name: 'Balance')]
class BalanceController extends AbstractController
{
    public function __construct(
        private readonly BalanceService $balanceService
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws IllegalAccessException
     * @throws BalanceTransactionException
     */
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
        description: 'Insufficient funds / Permission denied'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed'
    )]
    public function balanceReplenish(BalanceRequestDTO $requestDTO): JsonResponse
    {
        $this->balanceService->replenish($requestDTO->getAmount(), $requestDTO->getId());

        return new JsonResponse('Success');
    }

    /**
     * @throws UserNotFoundException
     */
    #[Route(name: 'balance_view', methods: 'GET')]
    #[OA\Response(
        response: 200,
        description: "User's balance"
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    public function balanceView(): JsonResponse
    {
        return new JsonResponse($this->balanceService->view());
    }
}
