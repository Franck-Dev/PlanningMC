<?php

namespace App\Repository;

use App\Entity\PolymReal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PolymReal|null find($id, $lockMode = null, $lockVersion = null)
 * @method PolymReal|null findOneBy(array $criteria, array $orderBy = null)
 * @method PolymReal[]    findAll()
 * @method PolymReal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PolymRealRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PolymReal::class);
    }

//    /**
//     * @return PolymReal[] Returns an array of PolymReal objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PolymReal
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
