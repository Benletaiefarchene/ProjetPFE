<?php

namespace App\Repository;

use App\Entity\Competance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Competance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competance[]    findAll()
 * @method Competance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competance::class);
    }

    // /**
    //  * @return Competance[] Returns an array of Competance objects
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

    /*
    public function findOneBySomeField($value): ?Competance
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
