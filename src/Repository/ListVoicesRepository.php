<?php

namespace App\Repository;

use App\Entity\ListVoices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ListVoicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListVoices::class);
    }

    public function save(ListVoices $listVoice): void
    {
        $this->getEntityManager()->persist($listVoice);
        $this->getEntityManager()->flush();
    }

    public function delete(ListVoices $listVoice): void
    {
        $this->getEntityManager()->remove($listVoice);
        $this->getEntityManager()->flush();
    }

    public function edit(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findVoice(string $name): ListVoices
    {
       return $this->findOneBy(['voice' => $name]);
    }

    public function allVoices(): array
    {
        return $this->findAll();
    }
}