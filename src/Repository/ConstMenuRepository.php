<?php

namespace App\Repository;

use App\Entity\ConstMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConstMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstMenu[]    findAll()
 * @method ConstMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstMenuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConstMenu::class);
    }

//    /**
//     * @return ConstMenu[] Returns an array of ConstMenu objects
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
    public function findOneBySomeField($value): ?ConstMenu
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
