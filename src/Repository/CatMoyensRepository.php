<?php

namespace App\Repository;

use App\Entity\CatMoyens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CatMoyens|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatMoyens|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatMoyens[]    findAll()
 * @method CatMoyens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatMoyensRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CatMoyens::class);
    }

//    /**
//     * @return CatMoyens[] Returns an array of CatMoyens objects
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
    public function findOneBySomeField($value): ?CatMoyens
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
