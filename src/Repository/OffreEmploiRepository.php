<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\OffreEmploi;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method OffreEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreEmploi[]    findAll()
 * @method OffreEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, OffreEmploi::class);
        $this->paginator =$paginator;
    }
    public function add(OffreEmploi $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
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
    public function findOffre()
    { 

    
    return $this  
    ->createQueryBuilder('o')
    ->select('o')
    ->orderBy('o.dateOffre','DESC')
    ->getQuery()
    ->getResult();
    }
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
     /**
      * Undocumented function
      *
      * 
      */
     public function findSearch(SearchData $search):PaginationInterface
     {
         $query=$this
         
         ->createQueryBuilder('o')
         ->select('t','o')
         ->join('o.type','t')
         ->andWhere('o.blocked = false')
         ->andWhere('o.accepted = 1')
         ->orderBy('o.dateOffre','DESC');
        if(!empty($search->q)){
            $query = $query
            ->Where('o.titre LIKE :q')
            ->Where('o.categorie LIKE :q')
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
       
        $query= $query->getQuery();
        //  dd($this->paginator->paginate($query, 1, 15));
        return $this->paginator->paginate(
            $query, /* query NOT result */
            $search->page, /*page number*/
            6 /*limit per page*/
            
        );

     }
       /**
      * Undocumented function
      *
      * 
      */
      public function Search(SearchData $search)
      {
          $query=$this
          
          ->createQueryBuilder('o')
          ->select('t','o')
          ->join('o.type','t')
          ->andWhere('o.blocked = false')
          ->andWhere('o.accepted = 1')
          ->orderBy('o.dateOffre','DESC');
         if(!empty($search->q)){
             $query = $query
             ->Where('o.titre LIKE :q')
             ->Where('o.categorie LIKE :q')
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
        
         $query= $query->getQuery()->getResult();
         //  dd($this->paginator->paginate($query, 1, 15));
         return  $query;
             
      }
    
     public function countoffre()
     {   
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->getQuery()
            ->getSingleScalarResult();

        
     }
     public function countoffreAcc()
     {   
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.accepted = true')
            ->getQuery()
            ->getSingleScalarResult();

        
     }
     function countNumberOffrePerMonth($m) {
         
         $date=date("Y")."-".$m."-%% ";
        $query = $this->createQueryBuilder('p')
        ->select('COUNT(p)')
        ->andWhere('p.dateOffre LIKE :date')
        ->setParameter('date', '%' .$date. '%')
           
            ->getQuery();
    
        return $query->getOneOrNullResult();
    }
     
}