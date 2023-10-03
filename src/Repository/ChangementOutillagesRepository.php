<?php

namespace App\Repository;

use App\Entity\ChangementOutillages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChangementOutillages>
 *
 * @method ChangementOutillages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChangementOutillages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChangementOutillages[]    findAll()
 * @method ChangementOutillages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChangementOutillagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChangementOutillages::class);
    }

    public function add(ChangementOutillages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChangementOutillages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ChangementOutillages[] Returns an array of ChangementOutillages objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChangementOutillages
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
