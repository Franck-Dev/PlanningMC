<?php

namespace App\Repository;

use App\Entity\Demandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Demandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demandes[]    findAll()
 * @method Demandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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

    /*
    public function findOneBySomeField($value): ?Demandes
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
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
}
