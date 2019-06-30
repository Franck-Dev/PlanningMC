<?php

namespace App\Repository;

use App\Entity\ConfSsmenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConfSsmenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfSsmenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfSsmenu[]    findAll()
 * @method ConfSsmenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfSsmenuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConfSsmenu::class);
    }

//    /**
//     * @return ConfSsmenu[] Returns an array of ConfSsmenu objects
//     */
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
    public function findOneBySomeField($value): ?ConfSsmenu
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
