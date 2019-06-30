<?php

namespace App\Repository;

use App\Entity\ProgMoyens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProgMoyens|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgMoyens|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgMoyens[]    findAll()
 * @method ProgMoyens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgMoyensRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProgMoyens::class);
    }

//    /**
//     * @return ProgMoyens[] Returns an array of ProgMoyens objects
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

    
    public function findOneBySomeField($ProgId): ?ProgMoyens
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.CateMoyen = :id')
            ->setParameter('id', $ProgId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
