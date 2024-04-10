<?php

namespace App\Repository;

use App\Entity\Voices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * Repository for the Voices entity.
 *
 * @method Voices|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voices|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voices[]    findAll()
 * @method Voices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class VoicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voices::class);
    }

    /**
     * Saves a Voices entity.
     */
    public function save(Voices $voice, bool $flush = true): void
    {
        $this->getEntityManager()->persist($voice);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Deletes a Voices entity.
     */
    public function delete(Voices $voice, bool $flush = true): void
    {
        $this->getEntityManager()->remove($voice);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}