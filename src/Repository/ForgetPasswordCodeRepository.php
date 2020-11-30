<?php

namespace App\Repository;

use App\Entity\ForgetPasswordCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForgetPasswordCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForgetPasswordCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForgetPasswordCode[]    findAll()
 * @method ForgetPasswordCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForgetPasswordCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForgetPasswordCode::class);
    }

    // /**
    //  * @return ForgetPasswordCode[] Returns an array of ForgetPasswordCode objects
    //  */

    public function findOneByDateAndCode($passwordcode,$account)
    {
        $from = new \DateTime();
        $to   = new \DateTime('+24 hours');

        return $this->createQueryBuilder('f')
            ->andWhere('f.ValidUntil BETWEEN :from AND :to')
            ->andWhere('f.ValidateKey = :ValidateKey')
            ->andWhere('f.User = :user')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->setParameter('ValidateKey', $passwordcode)
            ->setParameter('user', $account)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?ForgetPasswordCode
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
