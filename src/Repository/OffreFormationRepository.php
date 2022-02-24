<?php

namespace App\Repository;

use App\Entity\OffreFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OffreFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreFormation[]    findAll()
 * @method OffreFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreFormation::class);
    }

    // /**
    //  * @return OffreFormation[] Returns an array of OffreFormation objects
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
    public function findOneBySomeField($value): ?OffreFormation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
