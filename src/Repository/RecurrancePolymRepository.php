<?php

namespace App\Repository;

use App\Entity\RecurrancePolym;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method RecurrancePolym|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecurrancePolym|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecurrancePolym[]    findAll()
 * @method RecurrancePolym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecurrancePolymRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
    // Récupération de la liste des 
    public function findRecur( $dateF,$dateD ) : array
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT d
            FROM App\Entity\RecurrancePolym d LEFT OUTER JOIN  App\Entity\Planning g WITH g.id = d.NumPlanning  
            WHERE d.DateFinrecurrance > :dateD AND d.DateFinrecurrance < :dateF  AND g.Statut = :Statut');            
        $query-> setParameter ( 'dateD' , $dateD);
        $query-> setParameter ('dateF' , $dateF);
        $query-> setParameter ('Statut' , 'PLANNIFIE');
        // returns an array of Product objects
        return $query -> execute ();
    }
}
