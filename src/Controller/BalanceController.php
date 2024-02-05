<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BalanceController extends AbstractController
{
    private EntityRepository $userRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security               $security
    )
    {
        $this->userRepository = $entityManager->getRepository(Users::class);
    }

    #[Route('/balance', name: 'balance_replenish', methods: 'put')]
    public function replenish(Request $request): JsonResponse
    {
        try {
            $postData = json_decode($request->getContent(), true);
            $amount = $postData['amount'];
            $user = $this->userRepository->find($postData['id']);

            if (!$this->security->isGranted(Users::ROLE_ADMIN) && $amount >= 0
                || (!$this->security->isGranted(Users::ROLE_ADMIN) && $amount < 0 && $this->security->getUser()->getUserIdentifier() !== $user->getId())) {
                return new JsonResponse('Access denied', 401);
            }

            $wallet = $user->getWallet();

            if ($wallet->getBalance() < abs($amount) && $amount < 0) {
                return new JsonResponse('On your wallet insufficient funds', 403);
            }

            $wallet->setBalance($wallet->getBalance() + $amount);

            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
        } catch (\Exception) {
            return new JsonResponse('Internal server error', 500);
        }

        return new JsonResponse("Success");
    }

    #[Route('/balance', name: 'balance', methods: 'get')]
    public function view(): JsonResponse
    {
        $user = $this->userRepository->find($this->security->getUser()->getUserIdentifier());

        return new JsonResponse($user->getWallet()->getBalance());
    }
}
