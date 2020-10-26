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
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
