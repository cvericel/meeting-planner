<?php

namespace App\Repository;

use App\Entity\MeetingSearch;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function findOneByEmail (string $email)
    {
        try {
            return $this->createQueryBuilder('p')
                ->andWhere('p.email = :searchEmail')
                ->setParameter('searchEmail', $email)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }

    public function findAllUserQuery (MeetingSearch $meetingSearch)
    {
        $query = $this->createQueryBuilder('p');

        if ($meetingSearch->getEmail()) {
            $query = $query
                ->andWhere('p.email = :searchEmail')
                ->setParameter('searchEmail', $meetingSearch->getEmail());
        }
        return $query
                ->getQuery()
                ->getResult();
    }

    public function findUserQuery ()
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
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
}
