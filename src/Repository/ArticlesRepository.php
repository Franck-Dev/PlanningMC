<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    // /**
    //  * @return Articles[] Returns an array of Articles objects
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
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function myFindByArticle($RefPC){
        $qb = $this->createQueryBuilder('articles')
           ->leftJoin ('articles.OutMoulage','t')
           ->where('t.articles = :ref')
           ->setParameter('ref', $RefPC);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    public function myFindByOT($OT){
        $qb = $this->createQueryBuilder('articles')
           ->leftJoin ('articles.OutMoulage','t')
           ->where('t.Ref = :refOT')
           ->setParameter('refOT', $OT);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    public function myFindByArtProg($RefPC, $cycle){
        $qb = $this->createQueryBuilder('articles')
           ->leftJoin ('articles.ProgPolym','t')
           ->where('articles.Reference = :ref')
           ->andWhere('t.Nom = :cyc')
           ->setParameter('ref', $RefPC)
           ->setParameter('cyc', $cycle);
         
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
}
