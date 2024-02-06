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
        private readonly Security $security
    ) {
        $this->userRepository = $entityManager->getRepository(Users::class);
    }

    #[Route('/balance', name: 'balance_replenish', methods: 'put')]
    public function replenish(Request $request): JsonResponse
    {
        try {
            $postData = json_decode($request->getContent(), true);
            if (!isset($postData['id']) || !isset($postData['amount']) || !is_int($postData['amount'])) {
                return new JsonResponse('Amount and user is required', 400);
            }

            $amount = $postData['amount'];

            /** @var Users|null $user */
            $user = $this->userRepository->find($postData['id']);
            if (null === $user) {
                return new JsonResponse('User not found', 404);
            }

            $currentUser = $this->security->getUser();

            if ((null === $currentUser) || (!$this->security->isGranted(Users::ROLE_ADMIN) && $amount >= 0)
                || (!$this->security->isGranted(Users::ROLE_ADMIN) && $amount < 0
                    && $currentUser->getUserIdentifier() !== $user->getId())
            ) {
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

        return new JsonResponse('Success');
    }

    #[Route('/balance', name: 'balance', methods: 'get')]
    public function view(): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (null === $currentUser) {
            return new JsonResponse('Access denied', 401);
        }

        /** @var Users|null $user */
        $user = $this->userRepository->find($currentUser->getUserIdentifier());
        if (null === $user) {
            return new JsonResponse('User not found', 404);
        }

        return new JsonResponse($user->getWallet()->getBalance());
    }
}
