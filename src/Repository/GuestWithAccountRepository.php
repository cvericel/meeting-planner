<?php

namespace App\Repository;

use App\Entity\GuestWithAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GuestWithAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuestWithAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuestWithAccount[]    findAll()
 * @method GuestWithAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestWithAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuestWithAccount::class);
    }

    // /**
    //  * @return GuestWithAccount[] Returns an array of GuestWithAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GuestWithAccount
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
