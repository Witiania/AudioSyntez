<?php

namespace App\Service;

use App\Entity\Transaction;

use App\Entity\Users;
use App\Exception\UserNotFoundException;
use App\Repository\VoicesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Bundle\SecurityBundle\Security;

class TransactionService
{
    public function __construct(
        private readonly Security $security,
//        private readonly EntityManager $entityManager,
        private readonly VoicesRepository $voicesRepository
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws UserNotFoundException
     * @throws TransactionRequiredException
     */
    public function createTransaction(array $specification, string $type = "oggopus"): Transaction
    {
        $user = $this->findUser();
        if (null === $user) {
            throw new UserNotFoundException();
        }
        $transaction = (new Transaction())
            ->setUser($user)
            ->setType($type)
            ->setSpecification($specification);

        $this->setFullPrice($transaction);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    /**
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    public function findUser(): Users|null
    {
        $id = $this->security->getUser()->getUserIdentifier();
        return $this->entityManager->find(Users::class, $id);
    }

    public function setFullPrice(Transaction $transaction):void
    {
        $voice = $this->voicesRepository->find($transaction->getVoiceId());
        $fullPrice = mb_strlen($transaction->getText()) * $voice->getPrice();
        $transaction->setFullPrice($fullPrice);
    }
}