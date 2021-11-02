<?php

namespace App\Repository;

use App\Entity\CategoryMoyens;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method CategoryMoyens|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryMoyens|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryMoyens[]    findAll()
 * @method CategoryMoyens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryMoyensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryMoyens::class);
    }

//    /**
//     * @return CategoryMoyens[] Returns an array of CategoryMoyens objects
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
    public function findOneBySomeField($value): ?CategoryMoyens
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
