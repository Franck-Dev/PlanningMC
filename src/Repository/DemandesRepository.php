<?php

namespace App\Repository;

use App\Entity\Demandes;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Demandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demandes[]    findAll()
 * @method Demandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demandes::class);
    }

//    /**
//     * @return Demandes[] Returns an array of Demandes objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    //Récupération des demandes suivant date
    public function myFindByDays(\DateTime $firstDateTime): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.DatePropose >= :firstDate')
            ->setParameter('firstDate', $firstDateTime)
            ->getQuery()
            ->getResult()
        ;
    }

    // Calcul de la Charge Totale
    public function findDemRecur( $dateF,$dateD ) : array
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT d
            FROM App\Entity\Demandes d LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = d.Cycle   
            WHERE d.DatePropose > :dateD AND d.DatePropose < :dateF  AND d.RecurValide = :Reccurance');            
        $query-> setParameter ( 'dateD' , $dateD);
        $query-> setParameter ('dateF' , $dateF);
        $query-> setParameter ('Reccurance' , '1');
        // returns an array of Product objects
        return $query -> execute ();
    }

    public function findDemSem( $firstDateTime,$lastDateTime) : array 
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT d
            FROM App\Entity\Demandes d
            WHERE d.DatePropose >= :dateD AND d.DatePropose <= :dateF');            
        $query-> setParameter ( 'dateD' , $firstDateTime);
        $query-> setParameter ('dateF' , $lastDateTime);
        // returns an array of Product objects
        return $query -> execute ();
    }
}
