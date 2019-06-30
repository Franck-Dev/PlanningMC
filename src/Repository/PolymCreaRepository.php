<?php

namespace App\Repository;

use App\Entity\PolymCrea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PolymCrea|null find($id, $lockMode = null, $lockVersion = null)
 * @method PolymCrea|null findOneBy(array $criteria, array $orderBy = null)
 * @method PolymCrea[]    findAll()
 * @method PolymCrea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PolymCreaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PolymCrea::class);
    }

//    /**
//     * @return PolymCrea[] Returns an array of PolymCrea objects
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
    public function findOneBySomeField($value): ?PolymCrea
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
