<?php

namespace App\Repository;

use App\Entity\CourtReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method CourtReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourtReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourtReservation[]    findAll()
 * @method CourtReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourtReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourtReservation::class);
    }

    // /**
    //  * @return CourtReservation[] Returns an array of CourtReservation objects
    //  */

    public function findToday($date)
    {
        $from = new \DateTime($date." 00:00:00");
        $to   = new \DateTime($date." 23:59:59");

        return $this->createQueryBuilder('c')
            ->andWhere('c.StartTime BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->orderBy('c.StartTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }



    public function findReservation($date, $court)
    {
        $date = new \DateTime(date('m/d/Y H:i:s', $date));


        return $this->createQueryBuilder('c')
            ->andWhere('c.StartTime = :stamp')
            ->andWhere('c.Court = :court')
            ->setParameter('stamp', $date)
            ->setParameter('court', $court)
            ->orderBy('c.StartTime', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findReservations($date, $court)
    {
        $date = new \DateTime(date('m/d/Y H:i:s', $date));


        return $this->createQueryBuilder('c')
            ->andWhere('c.StartTime = :stamp')
            ->andWhere('c.Court = :court')
            ->setParameter('stamp', $date)
            ->setParameter('court', $court)
            ->orderBy('c.StartTime', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?CourtReservation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
