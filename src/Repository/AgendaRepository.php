<?php

namespace App\Repository;

use App\Entity\Agenda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agenda|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agenda|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agenda[]    findAll()
 * @method Agenda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agenda::class);
    }

    // /**
    //  * @return Agenda[] Returns an array of Agenda objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Agenda
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // Calcul du temps de travaillés dans un intervalle
    public function findTpsW($dateF,$dateD,$type)
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT SUM(p.TpsAlloue),  DAY(p.DateDeb) as jour,MONTH(p.DateDeb) as Mois, YEAR(p.DateDeb) as Annees, DATE_FORMAT (p.DateDeb,\'%j\') as Journees, DATE_FORMAT (p.DateDeb,\'%v\') as Semaines
            FROM App\Entity\Agenda p   
            WHERE p.DateDeb >= :dateD AND p.DateDeb <= :dateF  
            GROUP BY '.$type);
            $query-> setParameter ( 'dateD' , $dateD );
            $query-> setParameter ('dateF' , $dateF);
            // returns an array of Product objects
        return $query -> execute ();
    }
}
