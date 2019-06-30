<?php

namespace App\Repository;

use App\Entity\ConfSmenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConfSmenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfSmenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfSmenu[]    findAll()
 * @method ConfSmenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfSmenuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConfSmenu::class);
    }

//    /**
//     * @return ConfSmenu[] Returns an array of ConfSmenu objects
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
    public function findOneBySomeField($value): ?ConfSmenu
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
