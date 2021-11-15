<?php

namespace App\Repository;

use App\Entity\NomEquipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NomEquipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method NomEquipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method NomEquipe[]    findAll()
 * @method NomEquipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NomEquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NomEquipe::class);
    }

    // /**
    //  * @return NomEquipe[] Returns an array of NomEquipe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NomEquipe
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
