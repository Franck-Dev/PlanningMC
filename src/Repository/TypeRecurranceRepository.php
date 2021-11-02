<?php

namespace App\Repository;

use App\Entity\TypeRecurrance;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method TypeRecurrance|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRecurrance|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRecurrance[]    findAll()
 * @method TypeRecurrance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRecurranceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRecurrance::class);
    }

//    /**
//     * @return TypeRecurrance[] Returns an array of TypeRecurrance objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeRecurrance
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
