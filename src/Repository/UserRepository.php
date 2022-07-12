<?php

namespace App\Repository;

use App\Entity\User;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry,PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator =$paginator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function countuser()
    {   $role = "ROLE_ADMIN";
       return $this->createQueryBuilder('u')
           ->select('COUNT(u)')
           ->Where('u.exist = 1')
           ->andWhere('u.roles NOT LIKE :role')
           ->setParameter('role', '%' .$role. '%')
           ->getQuery()
           ->getSingleScalarResult();

       
    }
    public function countuserBlocekd()
    {   
       return $this->createQueryBuilder('u')
           ->select('COUNT(u)')
           ->Where('u.exist = 1')
           ->andWhere('u.blocked = true')
           ->getQuery()
           ->getSingleScalarResult();

       
    }
    public function findAdmin()
    {$role = "ROLE_ADMIN";
       
        return $this->createQueryBuilder('u')
        ->select('u')
        ->andWhere('u.roles LIKE :role')
        ->setParameter('role', '%' .$role. '%')
        ->getQuery()
        ->getResult();
        
       

    }
    function countNumberUsersPerMonth($m) {

        $date=  date("Y")."-".$m."-%% ";
        $role = "ROLE_ADMIN";
       $query = $this->createQueryBuilder('p')
       ->select('COUNT(p)')
       ->andWhere('p.createdAt LIKE :date')
       ->setParameter('date', '%' .$date. '%')
       ->andWhere('p.roles NOT LIKE :role')
       ->setParameter('role', '%' .$role. '%')
          
           ->getQuery();
   
       return $query->getOneOrNullResult();
   }
   
   
}
