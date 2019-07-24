<?php

namespace App\Repository;

use App\Entity\Outillages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Outillages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outillages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outillages[]    findAll()
 * @method Outillages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutillagesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Outillages::class);
    }

    // /**
    //  * @return Outillages[] Returns an array of Outillages objects
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
    public function findOneBySomeField($value): ?Outillages
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
