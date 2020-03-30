<?php

namespace App\Repository;

use App\Entity\Availability;
use App\Entity\MeetingDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Availability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Availability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Availability[]    findAll()
 * @method Availability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availability::class);
    }

    public function findIfGuestAlreadyChoice ($id_guest, $id_meeting_date)
    {
        try {
            return $this->createQueryBuilder('q')
                ->andWhere('q.meeting_guest = :id_guest')
                ->andWhere('q.meeting_date = :id_meeting_date')
                ->setParameter('id_guest', $id_guest)
                ->setParameter('id_meeting_date', $id_meeting_date)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // error
        }
        return null;
    }

    public function findAllAvailabilityInMeeting ($id_guest, $id_meeting)
    {
        return $this->createQueryBuilder('q')
            ->innerJoin(MeetingDate::class, 'p', Join::WITH, 'q.meeting_date = p.id')
            ->andWhere('p.meeting = :id_meeting')
            ->andWhere('q.meeting_guest = :id_guest')
            ->setParameter('id_meeting', $id_meeting)
            ->setParameter('id_guest', $id_guest)
            ->getQuery()
            ->getResult();
    }

    public function findAllAvailabilityForOneGuest($id_guest)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.meeting_guest = :id_guest')
            ->setParameter('id_guest', $id_guest)
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return Availability[] Returns an array of Availability objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Availability
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
