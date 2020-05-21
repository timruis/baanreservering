<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\PageVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageVisit[]    findAll()
 * @method PageVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageVisit::class);
    }
    public function findAllVisitsOnDate()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.Time', 'DESC')
            ->getQuery()
            ->execute();
    }
    // /**
    //  * @return PageVisit[] Returns an array of PageVisit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PageVisit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
