<?php

namespace App\Repository;

use App\Entity\ChargeFige;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method ChargeFige|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChargeFige|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChargeFige[]    findAll()
 * @method ChargeFige[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargeFigeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChargeFige::class);
    }

    // /**
    //  * @return ChargeFige[] Returns an array of ChargeFige objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChargeFige
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
