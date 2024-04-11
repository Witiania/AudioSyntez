<?php

namespace App\Controller\Api;

use App\Exception\UserNotFoundException;
use App\Service\TransactionService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/transaction', name: 'api_transaction')]
#[OA\Tag(name: 'Transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionService $transactionService
    )
    {}

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws UserNotFoundException
     * @throws TransactionRequiredException
     */
    #[Route('/', name: 'create_transaction', methods: ['POST'])]
    public function create(Request $request): void
    {
        $postData = json_decode($request->getContent(), true);

        $this->transactionService->createTransaction($postData['specification']);
    }
}