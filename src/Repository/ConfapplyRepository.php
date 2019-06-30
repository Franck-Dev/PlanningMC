<?php

namespace App\Repository;

use App\Entity\Confapply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Confapply|null find($id, $lockMode = null, $lockVersion = null)
 * @method Confapply|null findOneBy(array $criteria, array $orderBy = null)
 * @method Confapply[]    findAll()
 * @method Confapply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfapplyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Confapply::class);
    }

    public function getThreads() {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT m.thread')
            ->where('m.thread IS NOT NULL')
            ->orderBy('m.thread', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        }
    public function findAllTitre($Titres): array
        {
            $entityManager = $this->getEntityManager();

            $query = $entityManager->createQuery(
                'SELECT DISTINCT p
                FROM App\Entity\Confapply p
                WHERE p.titre_menu');

            // returns an array of Product objects
            return $query->execute();
        }

//    /**
//     * @return Confapply[] Returns an array of Confapply objects
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
    public function findOneBySomeField($value): ?Confapply
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
