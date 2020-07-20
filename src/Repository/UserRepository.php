<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
    public function findUserRoles($value)
    {
        return $this->createQueryBuilder('user')
            ->addSelect('user.roles')
            ->andWhere('user.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->execute();
    }
    public function countUser()
    {
        return $this->createQueryBuilder('user')
            ->addSelect('COUNT(user.*)')
            ->getQuery()
            ->execute();
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */

    public function findByTest()
    {
        return$this->createQueryBuilder('u')
            ->join('u.CourtReservations', 'cres')
            ->andWhere('u.Payed = true')
            ->andWhere('u.ActivateUser = true')
            ->andWhere('cres.StartTime != :date2')
            ->setParameter('date2', new \DateInterval("PT2H"))
            ->orderBy('u.Firstname, u.Lastname', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

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
