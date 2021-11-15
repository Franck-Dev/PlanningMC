<?php

namespace App\Repository;

use App\Entity\OrgaEq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrgaEq|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrgaEq|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrgaEq[]    findAll()
 * @method OrgaEq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrgaEqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrgaEq::class);
    }

    // /**
    //  * @return OrgaEq[] Returns an array of OrgaEq objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrgaEq
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
