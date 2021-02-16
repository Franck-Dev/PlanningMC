<?php

namespace App\Repository;

use App\Entity\Chargement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Chargement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chargement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chargement[]    findAll()
 * @method Chargement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chargement::class);
    }
   
    /**
     * myFindChargtMois est une fonction me permettant de récupérer tous les chargements validés sur 1 mois
     *
     * @param  datetime $dateD
     * @param  date $dateF
     * @return array
     */
    public function myFindChargtMois ( $dateD,$dateF ) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT p.NomChargement, p.Remplissage, p.DatePlannif, p.Programme, p.id
                FROM App\Entity\Chargement p 
                WHERE p.DatePlannif > :dateD AND  p.DatePlannif < :dateF'
        );
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
            // returns an array of Product objects
        return $query -> execute ();
    }

    public function myFindByDispo($dateAval,$dateAmont,$CTO) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT p.id, p.IdPlanning
                FROM App\Entity\Chargement p 
                WHERE p.DatePlannif > :dateD AND  p.DatePlannif = :dateF
                AND p.NomChargement = :nom'
        );
        $query-> setParameter ('dateD' , $dateAval );
        $query-> setParameter ('dateF' , $dateAmont);
        $query-> setParameter ('nom' , $CTO);
            // returns an array of Product objects
        return $query -> execute ();
    }

//    /**
//     * @return Chargement[] Returns an array of Chargement objects
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
    public function findOneBySomeField($value): ?Chargement
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
