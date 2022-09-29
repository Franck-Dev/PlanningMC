<?php

namespace App\Repository;

use App\Entity\Charge;
use App\Services\CallApiService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Charge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Charge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Charge[]    findAll()
 * @method Charge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargeRepository extends ServiceEntityRepository
{
    private $api;
    
    public function __construct(ManagerRegistry $registry, CallApiService $api)
    {
        parent::__construct($registry, Charge::class);
        $this->api = $api;
    }

    // /**
    //  * @return Charge[] Returns an array of Charge objects
    //  */
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
    // Calcul de la répartition de la charge polym
    public function findReparChargeCyc ( $dateD,$dateF,$cycle ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT p.DateDeb as Jour, count(p.ReferencePcs) as NbrPcs, p.NumProg as Cycles
        FROM App\Entity\Charge p 
        WHERE p.DateDeb > :dateD AND  p.DateDeb < :dateF AND  p.NumProg = :cyc
        GROUP BY Cycles'
    );
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    $query-> setParameter ('cyc' , $cycle);
    // returns an array of Product objects
    return $query -> execute ();
}
    // Calcul de la charge polym par semaine
    public function findChargeSem ( $dateD,$dateF ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT DATE_FORMAT (p.DateDeb,\'%v\') as Semaine,  YEAR(p.DateDeb) as Annee, count(p.ReferencePcs) as NbrRef, p.NumProg as Cycles
        FROM App\Entity\Charge p 
        WHERE p.DateDeb > :dateD AND  p.DateDeb < :dateF
        GROUP BY Annee,Semaine,Cycles'
    );
    $query-> setParameter ( 'dateD' , $dateD );
    $query-> setParameter ('dateF' , $dateF);
    // returns an array of Product objects
    return $query -> execute ();
}

    // Calcul de la charge polym par mois
    public function findChargeMois ( $dateD,$dateF ) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT DATE_FORMAT (p.DateDeb,\'%v\') as Semaine,MONTH(p.DateDeb) as Mois,  YEAR(p.DateDeb) as Annee, count(p.ReferencePcs) as NbrRef,p.NumProg as Cycles
                FROM App\Entity\Charge p 
                WHERE p.DateDeb > :dateD AND  p.DateDeb < :dateF
                GROUP BY Annee,Mois,Cycles'
        );
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
            // returns an array of Product objects
        return $query -> execute ();
    }

    // Calcul de la charge polym par semaine
    public function findReparChargeW ( $dateD,$dateF ) : array
    {
        $entityManager = $this -> getEntityManager ();
    
        $query = $entityManager -> createQuery (
            'SELECT p.DateDeb as Jour, count(p.ReferencePcs) as NbrPcs, p.NumProg as Cycles
            FROM App\Entity\Charge p 
            WHERE p.DateDeb > :dateD AND  p.DateDeb < :dateF AND p.Statut <> :Stat
            GROUP BY Jour,Cycles'
        );
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
        $query-> setParameter ('Stat' , 'CLOT');
        // returns an array of Product objects
        return $query -> execute ();
    }
        
    /**
     * findReparChargeWCycle
     *
     * @param  mixed $dateD
     * @param  mixed $dateF
     * @param  string $cycle
     * @return array
     */
    public function findReparChargeWCycle ( $dateD,$dateF,$cycle ) : array
    {
        $entityManager = $this -> getEntityManager ();
    
        $query = $entityManager -> createQuery (
            'SELECT p.DateDeb as Jour, count(p.ReferencePcs) as NbrPcs
            FROM App\Entity\Charge p 
            WHERE p.DateDeb > :dateD AND  p.DateDeb <= :dateF AND p.NumProg = :cycle 
            GROUP BY Jour'
        );
        $query-> setParameter ( 'dateD' , $dateD);
        $query-> setParameter ('dateF' , $dateF);
        $query-> setParameter ('cycle' , $cycle);
        //$query-> setParameter ('stat' , '');//Mettre le statut CHARGE pour différencier les OF libres ou planifiés
        // returns an array of Product objects
        return $query -> execute ();
    }

    // Calcul de la charge en pcs par mois
    public function myFindPcsTotMois ( $dateD,$dateF ) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT count(p.ReferencePcs) as NbrRef
                FROM App\Entity\Charge p 
                WHERE p.DateDeb > :dateD AND  p.DateDeb < :dateF'
        );
        $query-> setParameter ( 'dateD' , $dateD );
        $query-> setParameter ('dateF' , $dateF);
            // returns an array of Product objects
        return $query -> execute ();
    }

    // Vérifier si OF dans le passé pour un cycle
    public function myFindOFToPast ($art, $dateD,$cycle ) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT p.OrdreFab as OF, p.DateDeb, p.DesignationPcs as Designation
                FROM App\Entity\Charge p 
                WHERE p.DateDeb < :dateD 
                AND p.ReferencePcs = :refArt 
                AND  p.NumProg = :cycle 
                AND p.Statut = :Stat
                ORDER BY p.DateDeb ASC' 
        );
        $query-> setParameter ( 'refArt' , $art);
        $query-> setParameter ( 'dateD' , $dateD);
        $query-> setParameter ('cycle' , $cycle);
        $query-> setParameter ('Stat' , 'OUV');
            // returns an array of Product objects
        return $query -> execute ();
    }

    // Récupération des OF d'un chargement
    public function myFindOFChargmnt($idChargmnt) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT p
                FROM App\Entity\Charge p 
                WHERE p.chargement = :chargmnt'
        );
        $query-> setParameter ('chargmnt' , $idChargmnt);
            // returns an array of Product objects
        return $query -> execute ();
    }

    // Récupération des OF suivant un statut
    public function myFindOFStatut($Statut) : array
    {
        $entityManager = $this -> getEntityManager ();
        
        $query = $entityManager -> createQuery (
            'SELECT p
                FROM App\Entity\Charge p 
                WHERE p.Statut = :stat'
        );
        $query-> setParameter ('stat' , $Statut);
            // returns an array of Product objects
        return $query -> execute ();
    }
    
    /**
     * findByCyc Récupération du vol de charge par cycle avec péremption
     *
     * @param  mixed $cyc
     * @param  mixed $dateF
     * @return Charge
     */
    public function findByCyc($cyc, $dateF, $statut)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.OrdreFab')
            ->addSelect('c.DesignationPcs')
            ->addSelect('c.DateDeb')
            ->addSelect('c.outillage')
            ->from($this->_entityName, 'c')
            ->andWhere('c.NumProg = :val')
            ->andWhere('c.Statut = :stat')
            ->andWhere('c.DateDeb < :dateF')
            ->setParameter('val', $cyc)
            ->setParameter('stat', $statut)
            ->setParameter('dateF', $dateF)
            ->orderBy('c.DateDeb', 'ASC')
        ;
        
        $listOFCharge = $qb->getQuery()->getResult();
        //Récupération des OF encours qui ont une péremption
        $tabPerempOF = $this->api->getDatasAPI('/api/datas_kits?status=false','tracakit',[] ,'GET');
        // Rajout de la péremption de chaque OF si découpe déjà réalisée
        
        $i=0;
        foreach ($listOFCharge as $OF) {
            $tabPerempOF = $this->search($tabPerempOF, 'idMM', '53894219-1');
            // $tabPerempOF = $this->api->getDatasAPI('/api/datas_kits?idMM=53894219-1','tracakit',[] ,'GET');
            $datePeremp = $tabPerempOF[0]['PeremptionMoins18'];
            if ($datePeremp > $tabPerempOF[0]['ADrapAv']) {
                $datePeremp = $tabPerempOF[0]['ADrapAv'];
            } elseif ($datePeremp > $tabPerempOF[0]['ACuirAv']) {
                $datePeremp = $tabPerempOF[0]['ACuirAv'];
            }
            $OF['Peremption']=$datePeremp;
            $i++;
            $listOFPeremp[$i]=$OF;
        }
            //dd($listOFPeremp);
        return $listOFPeremp;
    }
    
    /**
     * search Recherche de valeur dans un tableau
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    private function search($array, $key, $value) {
        $results = array();
          
        // if it is array
        if (is_array($array)) {
              
            // if array has required key and value
            // matched store result 
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }
              
            // Iterate for each element in array
            foreach ($array as $subarray) {
                  
                // recur through each element and append result 
                $results = array_merge($results, 
                        $this->search($subarray, $key, $value));
            }
        }
      
        return $results;
    }

}
