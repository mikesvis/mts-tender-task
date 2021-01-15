<?php

namespace App\Repository;

use App\Entity\Remains;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Remains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Remains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Remains[]    findAll()
 * @method Remains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemainsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Remains::class);
    }

    /**
     * @param DateTime $importDateTime
     * @param int $step
     * @return int
     */
    public function deleteOld(DateTime $importDateTime, int $step): mixed
    {
        $query = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->delete($this->getEntityName(), 'r')
            ->where('r.date_update < :importDateTime')
            ->setParameter('importDateTime', $importDateTime)
            ->setMaxResults($step)
            ->getQuery();
        
        return $query->execute();
    }
}
