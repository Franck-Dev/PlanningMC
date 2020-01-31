<?php

namespace App\Repository;

use App\Entity\Moyens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Moyens|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moyens|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moyens[]    findAll()
 * @method Moyens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoyensRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Moyens::class);
    }

//    /**
//     * @return Moyens[] Returns an array of Moyens objects
//     */
    
    public function findById($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Moyens
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllMoyensSvtService ( $IdService ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT DISTINCT p.Libelle as Moyen, p.id, count(p.Activitees) as SousTitres
        FROM App\Entity\Moyens p
        WHERE p.Id_Service = :service
        GROUP BY Moyen'
    ) -> setParameter ( 'service' , $IdService );
    // returns an array of Product objects
    return $query -> execute ();
}
//Trouver les moyens correspondant au critères
public function findMoyens ( $IdService,$IdCate ) : array
{
    $entityManager = $this -> getEntityManager ();

    $query = $entityManager -> createQuery (
        'SELECT DISTINCT p.Libelle as Moyen, p.id, g.Libelle as Categorie
        FROM App\Entity\Moyens p LEFT OUTER JOIN  App\Entity\CategoryMoyens g WITH g.id = p.categoryMoyens
        WHERE p.Id_Service = :serv AND p.categoryMoyens < :Cate
        GROUP BY Moyen');            
    $query-> setParameter ( 'serv' , $IdService );
    $query-> setParameter ('Cate' , $IdCate);
    // returns an array of Product objects
    return $query -> execute ();
}
}
