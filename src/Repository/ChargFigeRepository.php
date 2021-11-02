<?php

namespace App\Repository;

use App\Entity\ChargFige;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method ChargFige|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChargFige|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChargFige[]    findAll()
 * @method ChargFige[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargFigeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChargFige::class);
    }

    // /**
    //  * @return ChargFige[] Returns an array of ChargFige objects
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
    public function findOneBySomeField($value): ?ChargFige
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