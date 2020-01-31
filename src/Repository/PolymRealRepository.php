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
    public function findAllPcsByDates ( $date ) : array
{
    $entityManager = $this -> getEntityManager ();
    $query = $entityManager -> createQuery (
        'SELECT g.Nom , count(p.Programmes), sum(p.NbrPcs)
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = p.Programmes  
        WHERE p.DebPolym > :dateDeb AND p.DebPolym < :dateFin
        GROUP BY p.Programmes'
    );
    $query -> setParameter ( 'dateFin' , $date );
    // returns an array of Product objects
    return $query -> execute ();
}
// Requette pour répartition des polymérisation sur la journée n-1
    public function findRepartPcssvtProg ( $dateF,$dateD ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT g.Nom , count(p.Programmes), sum(p.NbrPcs),DAY(p.DebPolym) as jour,MONTH(p.DebPolym) as Mois, YEAR(p.DebPolym) as annee
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = p.Programmes  
        WHERE p.DebPolym > :dateD AND p.DebPolym < :dateF
        GROUP BY p.Programmes'
    );
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de nombre de pièce par mois
    public function findAllPcsByDate ( $date ) : array
{
    $entityManager = $this -> getEntityManager ();
    $query = $entityManager -> createQuery (
        'SELECT MONTH(p.DebPolym) as Mois, sum(p.NbrPcs), p.Articles as Dossier
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = p.Programmes 
        WHERE p.DebPolym > :date 
        GROUP BY Mois'
    ) -> setParameter ( 'date' , $date );
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de nombre total de pcs sur 13 mois
public function findAllPcs ( $date ) : array
{
    $entityManager = $this -> getEntityManager ();
    $query = $entityManager -> createQuery (
        'SELECT sum(p.NbrPcs)
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = p.Programmes 
        WHERE p.DebPolym > :date'
    ) -> setParameter ( 'date' , $date );
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul du TRS machine
    public function findTRSMachine ( $dateF,$dateD ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureePolym , g.Nom , count(p.Programmes), sum(p.NbrPcs)
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\ProgMoyens g WITH g.id = p.Programmes  
        WHERE p.DebPolym > :dateD AND p.DebPolym < :dateF
        GROUP BY p.Programmes'
    );
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul du TRS Global
public function findTRS ( $dateF,$dateD ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureePolym, avg(p.PourcVolCharge) as PourVol
        FROM App\Entity\PolymReal p   
        WHERE p.DebPolym > :dateD AND p.DebPolym < :dateF'
    );
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de la charge réalisée par machine
public function findCharSem ( $dateF,$dateD  ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureTotPolyms, g.Libelle as moyen, sum(p.NbrPcs) as NbrPcs, DAY(p.DebPolym) as jour
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\Moyens g WITH g.id = p.Moyens
        WHERE p.DebPolym > :dateD AND p.FinPolym < :dateF
        GROUP BY p.Moyens');            
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de la charge réalisée pour toutes les machine sur la semaine
public function findCharJourSem ( $dateD  ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureTotPolyms, count(p.Programmes) as NbrProg,DATE_FORMAT (p.DebPolym,\'%v\') as semaine, DAY(p.DebPolym) as jour,MONTH(p.DebPolym) as mois, YEAR(p.DebPolym) as annee
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\Moyens g WITH g.id = p.Moyens
        WHERE p.DebPolym > :dateD
        GROUP BY semaine');            
    $query-> setParameter ( 'dateD' , $dateD );
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de la charge réalisée par machine sur la semaine
public function findCharMachSem ( $dateF,$dateD,$Moy  ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureTotPolyms, g.Libelle as moyen, sum(p.NbrPcs) as NbrPcs, DAY(p.DebPolym) as jour,MONTH(p.DebPolym) as mois, YEAR(p.DebPolym) as annee
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\Moyens g WITH g.id = p.Moyens
        WHERE p.DebPolym > :dateD AND p.FinPolym < :dateF AND g.Libelle = :Mach
        GROUP BY  jour,p.Moyens');            
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    $query-> setParameter ('Mach' , $Moy);
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de la charge en heures et en nbrs de pièces réalisées par semaine
public function findRapportPcsH ( $date  ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureTotPolyms, sum(p.NbrPcs) as NbrPcs, DATE_FORMAT (p.DebPolym,\'%v\') as semaine
        FROM App\Entity\PolymReal p 
        WHERE p.DebPolym > :dateD
        GROUP BY  semaine');            
    $query-> setParameter ( 'dateD' , $date );
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul du TRS par jour
public function findTRSJour ( $dateF,$dateD ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureePolym, avg(p.PourcVolCharge) as PourVol, count(p.Programmes) as NbrProg, DAY(p.DebPolym) as jour,MONTH(p.DebPolym) as mois, YEAR(p.DebPolym) as annee, DATE_FORMAT (p.DebPolym,\'%j\') as journee
        FROM App\Entity\PolymReal p   
        WHERE p.DebPolym > :dateD AND p.DebPolym < :dateF
        GROUP BY journee');
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    // returns an array of Product objects
    return $query -> execute ();
}
// Calcul de la charge réalisée par machine en heure
public function findCharMach ( $dateF,$dateD,$moyen  ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT SUM(TIMETOSEC(TIMEDIFF(p.FinPolym, p.DebPolym))) as DureTotPolyms, g.Libelle as moyen, DAY(p.DebPolym) as jour, MONTH(p.DebPolym) as mois, YEAR(p.DebPolym) as annee, DATE_FORMAT (p.DebPolym,\'%j\') as journee
        FROM App\Entity\PolymReal p LEFT OUTER JOIN  App\Entity\Moyens g WITH g.id = p.Moyens
        WHERE p.DebPolym > :dateD AND p.FinPolym < :dateF AND g.Libelle = :Nmoyen
        GROUP BY jour');            
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    $query-> setParameter ('Nmoyen' , $moyen);
    // returns an array of Product objects
    return $query -> execute ();
}
}
