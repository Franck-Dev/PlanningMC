<?php

namespace App\Repository;

use App\Entity\TypesEquipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypesEquipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypesEquipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypesEquipe[]    findAll()
 * @method TypesEquipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypesEquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypesEquipe::class);
    }

    // /**
    //  * @return TypesEquipe[] Returns an array of TypesEquipe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypesEquipe
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
