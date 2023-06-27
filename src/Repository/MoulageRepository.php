<?php

namespace App\Repository;

use App\Entity\Moulage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Moulage>
 *
 * @method Moulage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moulage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moulage[]    findAll()
 * @method Moulage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoulageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moulage::class);
    }

    public function add(Moulage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Moulage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * Fonction permettant de récupérer la liste des moulages lancées dans un tableau de moyen et svt un statut
     *
     * @param  mixed $datedeb
     * @param  mixed $datefin
     * @param  array $moyen Tableau liste de moyen à chercher
     * @param  string $statut Statut à rechercher
     * @return array Retour de la liste des moulages
     */
    public function findChargeStatut(\DateTime $datedeb, \DateTime $datefin, $moyen, $statut): array
    {        
        $listMoul= $this->createQueryBuilder('p')
            ->leftjoin("p.Moyen", "t")
            ->andWhere('t.Libelle IN (:moyens)')
            ->andWhere('p.DebMoul >= :dateD')
            ->andWhere('p.FinMoul >= :dateF')
            ->andWhere('p.Statut = :statut')
            -> setParameter ( 'dateD' , $datedeb )
            -> setParameter ('dateF' , $datefin)
            -> setParameter ('moyens' , $moyen)
            -> setParameter ('statut' , $statut)
            ->orderBy('p.DebMoul', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
        return $listMoul;
    }

//    /**
//     * @return Moulage[] Returns an array of Moulage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Moulage
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
