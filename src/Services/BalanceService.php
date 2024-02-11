<?php

namespace App\Services;

use App\Entity\Users;
use App\Exceptions\BalanceErrorException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;

class BalanceService
{
    private readonly EntityRepository $userRepository;

    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->userRepository = $this->entityManager->getRepository(Users::class);
    }

    /**
     * @throws UserNotFoundException
     * @throws ForbiddenException
     * @throws BalanceErrorException
     */
    public function replenish(int $amount, string $id): void
    {
        /** @var Users|null $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $currentUser = $this->security->getUser();

        if ((null === $currentUser) || (!$this->security->isGranted(Users::ROLE_ADMIN) && $amount >= 0)
            || (!$this->security->isGranted(Users::ROLE_ADMIN) && $amount < 0
                && $currentUser->getUserIdentifier() !== $user->getId())
        ) {
            throw new ForbiddenException();
        }

        $wallet = $user->getWallet();

        if ($wallet->getBalance() < abs($amount) && $amount < 0) {
            throw new BalanceErrorException();
        }

        $wallet->setBalance($wallet->getBalance() + $amount);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();
    }

    /**
     * @throws UserNotFoundException
     */
    public function view(): int
    {
        $currentUser = $this->security->getUser();
        if (null === $currentUser) {
            throw new UserNotFoundException();
        }

        /** @var Users|null $user */
        $user = $this->userRepository->find($currentUser->getUserIdentifier());
        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user->getWallet()->getBalance();
    }
}
