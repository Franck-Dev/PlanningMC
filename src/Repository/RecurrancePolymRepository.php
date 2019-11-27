<?php

namespace App\Repository;

use App\Entity\RecurrancePolym;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RecurrancePolym|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecurrancePolym|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecurrancePolym[]    findAll()
 * @method RecurrancePolym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecurrancePolymRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecurrancePolym::class);
    }

    // /**
    //  * @return RecurrancePolym[] Returns an array of RecurrancePolym objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecurrancePolym
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
