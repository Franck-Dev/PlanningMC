<?php

namespace App\Repository;

use App\Entity\ProgPolym;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method ProgPolym|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgPolym|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgPolym[]    findAll()
 * @method ProgPolym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgPolymRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgPolym::class);
    }

//    /**
//     * @return ProgPolym[] Returns an array of ProgPolym objects
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
    public function findOneBySomeField($value): ?ProgPolym
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
