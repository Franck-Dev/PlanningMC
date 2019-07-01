<?php

namespace App\Repository;

use App\Entity\PolymReal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PolymReal|null find($id, $lockMode = null, $lockVersion = null)
 * @method PolymReal|null findOneBy(array $criteria, array $orderBy = null)
 * @method PolymReal[]    findAll()
 * @method PolymReal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PolymRealRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PolymReal::class);
    }

//    /**
//     * @return PolymReal[] Returns an array of PolymReal objects
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
    public function findOneBySomeField($value): ?PolymReal
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllPcsByDate ( $date ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT g.Nom , count(p.Programmes), sum(p.NbrPcs)
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = p.Programmes  
        WHERE p.DebPolym > :date
        GROUP BY p.Programmes'
    ) -> setParameter ( 'date' , $date );
    // returns an array of Product objects
    return $query -> execute ();
}
}
