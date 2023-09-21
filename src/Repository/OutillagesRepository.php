<?php

namespace App\Repository;

use App\Entity\Outillages;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Outillages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outillages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outillages[]    findAll()
 * @method Outillages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutillagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outillages::class);
    }

    // /**
    //  * @return Outillages[] Returns an array of Outillages objects
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
    public function findOneBySomeField($value): ?Outillages
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
        
    public function myFindByCharFiG($ChargeFiG){
        $qb = $this->createQueryBuilder('outillages')
           ->innerJoin ('outillages.chargFiges','t')
           ->where('t.Code = :code')
           ->setParameter('code', $ChargeFiG);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    public function myFindByPcs($RefPC){
        $qb = $this->createQueryBuilder('outillages')
           ->leftJoin ('outillages.articles','t')
           ->where('t.Reference = :ref')
           ->setParameter('ref', $RefPC);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    public function myFindByCyc($dispo, $cycleId){
        $qb = $this->createQueryBuilder('outillages')
           ->leftJoin ('outillages.Programme','t')
           ->andwhere('t.id = :cycle')
           ->andWhere('outillages.Dispo = :dispo')
           ->setParameter('cycle', $cycleId)
           ->setParameter('dispo', $dispo);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
    
    /**
     * myFindByChargement Recherche les outillages liés à un chargement
     *
     * @param  mixed $idCharge
     * @return void
     */
    public function myFindByChargement($idCharge){
        $qb = $this->createQueryBuilder('outillages')
           ->innerJoin ('outillages.chargements','t')
           ->where('t.id = :id')
           ->setParameter('id', $idCharge);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
    
    /**
     * myFindByAvion Permet de récupérer tous les outillages d'une liste de programme 
     *
     * @param  array $listAvions
     * @param  string $tri (ASC ou DESC)
     * @return void
     */
    public function myFindByAvion($listAvions){
        $qb=$this->createQueryBuilder('u')
            ->leftjoin("u.Projet", "t")
            ->where('t.libelle IN (:avions)')
            ->setParameter('avions', $listAvions)
            ->orderBy('u.nbPolymssTrait', 'DESC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
}