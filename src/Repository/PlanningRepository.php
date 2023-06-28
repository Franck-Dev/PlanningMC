<?php

namespace App\Repository;

use App\Entity\Planning;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Planning|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planning|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planning[]    findAll()
 * @method Planning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

//    /**
//     * @return Planning[] Returns an array of Planning objects
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

    
    public function findOneBySomeField(\DateTime $datedeb, \DateTime $datefin, $moyen): ?Planning
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.DebutDate = :datedeb AND c.FinDate = :datefin AND c.Identification = :moyen ')
            ->setParameter('datedeb', $datedeb)
            ->setParameter('datefin', $datefin)
            ->setParameter('moyen', $moyen)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    //Récupération des polym suivant date
    public function myFindByDays(\DateTime $firstDateTime, \DateTime $lastDateTime): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.DebutDate >= :firstDate')
            ->andwhere('c.FinDate <= :lastDate')
            ->setParameter('firstDate', $firstDateTime)
            ->setParameter('lastDate', $lastDateTime)
            ->getQuery()
            ->getResult()
        ;
    }
    // Calcul de la charge par machine
    public function findChargeMach ( $dateF,$dateD  ) : array
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT SUM(TIMEDIFF(p.FinDate, p.DebutDate)) as DureTheoPolym, p.Identification as Moyen
            FROM App\Entity\Planning p 
            WHERE p.DebutDate > :dateD AND p.FinDate < :dateF
            GROUP BY p.Identification');            
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
        // returns an array of Product objects
        return $query -> execute ();
    }
    // Calcul de la Charge Totale
    public function findCharge( $dateF,$dateD  ) : array
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT SUM(TIMEDIFF(p.FinDate, p.DebutDate)) as DureTheoPolym
            FROM App\Entity\Planning p 
            WHERE p.DebutDate > :dateD AND p.FinDate < :dateF');            
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
        // returns an array of Product objects
        return $query -> execute ();
    }
        
    /**
     * Fonction permettant de récupérer la liste des polyms plannifiées dans un tableau de moyen et svt un statut
     *
     * @param  mixed $datedeb
     * @param  mixed $datefin
     * @param  array $moyen Tableau liste de moyen à chercher
     * @param  string $statut Statut à rechercher
     * @return array Retour de la liste des polyms
     */
    public function findChargeStatut(\DateTime $datedeb, \DateTime $datefin, $moyen, $statut): array
    {
        $entityManager = $this -> getEntityManager ();
        $query = $entityManager -> createQuery (
            'SELECT p
            FROM App\Entity\Planning p LEFT OUTER JOIN  App\Entity\Demandes g WITH g.id = p.NumDemande
            WHERE p.DebutDate > :dateD AND p.FinDate < :dateF  AND p.Identification IN (:moyens) AND p.Statut = :statut
            ORDER BY p.DebutDate ASC');
        $query-> setParameter ( 'dateD' , $datedeb );
        $query-> setParameter ('dateF' , $datefin);
        $query-> setParameter ('moyens' , $moyen);
        $query-> setParameter ('statut' , $statut);

        // returns an array of Product objects
        return $query -> execute ();
    }    
}   
