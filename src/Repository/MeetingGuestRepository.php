<?php

namespace App\Repository;

use App\Entity\GuestWithAccount;
use App\Entity\GuestWithoutAccount;
use App\Entity\MeetingGuest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method MeetingGuest|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingGuest|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingGuest[]    findAll()
 * @method MeetingGuest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingGuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingGuest::class);
    }

    /**
     * @param $id_user
     * @param $id_meeting
     * @return array
     */
    public function findAlreadyInWithAccount ($id_user, $id_meeting) : array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin(GuestWithAccount::class, 'g', Join::WITH, 'q.id = g.meeting_guest')
            ->andWhere('g.user = :id_user')
            ->andWhere('q.meeting = :id_meeting')
            ->setParameter('id_user', $id_user)
            ->setParameter('id_meeting', $id_meeting)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAlreadyInWithoutAccount ($email, $id_meeting) : array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin(GuestWithoutAccount::class, 'g', Join::WITH, 'q.id = g.meeting_guest')
            ->andWhere('g.email = :email')
            ->andWhere('q.meeting = :id_meeting')
            ->setParameter('email', $email)
            ->setParameter('id_meeting', $id_meeting)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Return all meeting guest for one meeting
     * @param $meeting_id
     * @return MeetingGuest[]
     */
    public function findAllInMeeting ($meeting_id) : array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.meeting = :id_meeting')
            ->setParameter('id_meeting', $meeting_id)
            ->getQuery()
            ->getResult();
    }

    public function findMeetingWithUserId ($user_id): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin(GuestWithAccount::class, 'g', Join::WITH, 'q.id = g.meeting_guest')
            ->andWhere('g.user = :id_user')
            ->setParameter('id_user', $user_id)
            ->getQuery()
            ->getResult();
    }

    public function findUserInMeetingGuest ($id_meeting, $id_user)
    {
        try {
            return $this->createQueryBuilder('q')
                ->innerJoin(GuestWithAccount::class, 'g', Join::WITH, 'q.id = g.meeting_guest')
                ->andWhere('g.user = :id_user')
                ->andWhere('q.meeting = :id_meeting')
                ->setParameter('id_meeting', $id_meeting)
                ->setParameter('id_user', $id_user)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findInMeetingGuestWithToken ($id_meeting, $token)
    {
        try {
            return $this->createQueryBuilder('q')
                ->innerJoin(GuestWithoutAccount::class, 'g', Join::WITH, 'q.id = g.meeting_guest')
                ->andWhere('g.token = :token')
                ->andWhere('q.meeting = :id_meeting')
                ->setParameter('id_meeting', $id_meeting)
                ->setParameter('token', $token)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }


    public function findOneGuest($guestId)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id = :guestId')
            ->setParameter('guestId', $guestId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    // /**
    //  * @return MeetingGuest[] Returns an array of MeetingGuest objects
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
    public function findOneBySomeField($value): ?MeetingGuest
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
