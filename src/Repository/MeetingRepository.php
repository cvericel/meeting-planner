<?php

namespace App\Repository;

use App\Entity\Meeting;
use App\Entity\MeetingDate;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Meeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meeting[]    findAll()
 * @method Meeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meeting::class);
    }

    /**
     * @param $id_user
     * @return Meeting[]
     */
    public function findAllById($id_user) : array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :id_user')
            ->setParameter('id_user', $id_user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Meeting[]
     * @throws Exception
     */
    public function findAllMeetingWhoHasFinalDate(): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin(MeetingDate::class, 'd', Join::WITH, 'm.chosen_date = d.id')
            ->andWhere('m.chosen_date IS NOT NULL')
            ->andWhere('d.day > :date')
            ->setParameter('date', new DateTime())
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Meeting[] Returns an array of Meeting objects
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
    public function findOneBySomeField($value): ?Meeting
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
