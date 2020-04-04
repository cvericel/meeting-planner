<?php

namespace App\Repository;

use App\Entity\MeetingDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeetingDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingDate[]    findAll()
 * @method MeetingDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingDate::class);
    }

    // /**
    //  * @return MeetingDate[] Returns an array of MeetingDate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeetingDate
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllById(?int $meetingId)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.meeting = :id')
            ->setParameter('id', $meetingId)
            ->orderBy('m.day', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
