<?php

namespace App\Repository;

use App\Entity\ImportStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImportStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportStat[]    findAll()
 * @method ImportStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportStat::class);
    }
}
