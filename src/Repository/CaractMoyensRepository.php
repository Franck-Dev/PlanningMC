<?php

namespace App\Repository;

use App\Entity\CaractMoyens;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method CaractMoyens|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaractMoyens|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaractMoyens[]    findAll()
 * @method CaractMoyens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaractMoyensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaractMoyens::class);
    }

//    /**
//     * @return CaractMoyens[] Returns an array of CaractMoyens objects
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
    public function findOneBySomeField($value): ?CaractMoyens
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
