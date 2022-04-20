<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\OffreEmploi;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method OffreEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreEmploi[]    findAll()
 * @method OffreEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    // /**
    //  * @return OffreEmploi[] Returns an array of OffreEmploi objects
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
    public function findOneBySomeField($value): ?OffreEmploi
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findActualites()
    {  
            $limit=5;
            return $this
            ->createQueryBuilder('e')
            ->addOrderBy('e.dateOffre', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute()
        ;
    
       
     }
     public function findSearch(SearchData $search):array
     {
         $query=$this
         
         ->createQueryBuilder('o')
         ->select('t','o')
         ->join('o.type','t');

        if(!empty($search->q)){
            $query = $query
            ->andWhere('o.titre LIKE :q')
            ->setParameter('q',"%{$search->q}%");
        }
        if(!empty($search->min)){
            $query = $query
            ->andWhere('o.salaire >= :min')
            ->setParameter('min', $search->min);
        }
        if(!empty($search->max)){
            $query = $query
            ->andWhere('o.salaire <= :max')
            ->setParameter('max', $search->max);
        }
        if(!empty($search->type)){
            $query = $query
            ->andWhere('t.id IN (:type)')
            ->setParameter('type', $search->type);
        }
        return $query->getQuery()->getResult();

     }
    
}