<?php

namespace App\Repository;

use App\Entity\GuestWithoutAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Collection;

/**
 * @method GuestWithoutAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuestWithoutAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuestWithoutAccount[]    findAll()
 * @method GuestWithoutAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestWithoutAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuestWithoutAccount::class);
    }

    // /**
    //  * @return GuestWithoutAccount[] Returns an array of GuestWithoutAccount objects
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

    public function findAllByEmail($email)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult()
        ;
    }
    /*
    public function findOneBySomeField($value): ?GuestWithoutAccount
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
