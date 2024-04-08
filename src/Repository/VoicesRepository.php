<?php

namespace App\Repository;

use App\Entity\Voices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VoicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voices::class);
    }

    public function save(Voices $listVoice): void
    {
        $this->getEntityManager()->persist($listVoice);
        $this->getEntityManager()->flush();
    }

    public function delete(Voices $listVoice): void
    {
        $this->getEntityManager()->remove($listVoice);
        $this->getEntityManager()->flush();
    }

    public function edit(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findVoice(string $name): ?Voices
    {
       return $this->findOneBy(['voice' => $name]) ?? null;
    }

    public function allVoices(): array
    {
        return $this->findAll();
    }
}