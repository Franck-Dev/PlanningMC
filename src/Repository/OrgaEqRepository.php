<?php

namespace App\Repository;

use App\Entity\OrgaEq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrgaEq|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrgaEq|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrgaEq[]    findAll()
 * @method OrgaEq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrgaEqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrgaEq::class);
    }

    // /**
    //  * @return OrgaEq[] Returns an array of OrgaEq objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrgaEq
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
    * @return OrgaEq[] Returns an array of OrgaEq objects
    */
    
    // Calcul de la capacité réalisée par equipes en heure sur un intervalle de temps donné
    public function findCapaEq ( $dateF,$dateD ) : array
    {
        $entityManager = $this -> getEntityManager ();

        $query = $entityManager -> createQuery (
            'SELECT SUM(TIMETOSEC(p.TypeW)) as DureTotCapa, DAY(p.DebPolym) as jour, MONTH(p.DebPolym) as mois, YEAR(p.DebPolym) as annee, DATE_FORMAT (p.DebPolym,\'%j\') as journee
            FROM App\Entity\OrgaEq p LEFT OUTER JOIN  App\Entity\NomEquipe g WITH g.id = p.NomEquipe
            WHERE p.DebPolym > :dateD AND p.FinPolym < :dateF
            GROUP BY jour');            
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
        // returns an array of Product objects
        return $query -> execute ();
    }
}
