<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder= $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setFirstname('Tim');
        $admin->setLastname('Splinter');
        $admin->setPayed(true);
        $admin->setActivateUser(true);
        $admin->setMobile('0618341213');
        $admin->setEmail('test@trifall.net');
        $admin->setPassword($this->encoder->encodePassword($admin,'0000'));
        $admin->setRoles(array('ROLE_DEVELOPER','ROLE_USER','ROLE_ADMIN','ROLE_REGISTER','ROLE_READ'));
         $manager->persist($admin);

        $manager->flush();

        $admin = new User();
        $admin->setUsername('petermethost');
        $admin->setFirstname('Peter');
        $admin->setLastname('Methorst');
        $admin->setPayed(true);
        $admin->setActivateUser(true);
        $admin->setEmail('peter@pemesport.nl');
        $admin->setPassword($this->encoder->encodePassword($admin,'0000'));
        $admin->setRoles(array('ROLE_USER','ROLE_ADMIN','ROLE_REGISTER','ROLE_READ'));
        $manager->persist($admin);

        $manager->flush();

        $admin = new User();
        $admin->setUsername('arjanbesteman');
        $admin->setFirstname('Arjan ');
        $admin->setLastname('Besteman');
        $admin->setPayed(true);
        $admin->setActivateUser(true);
        $admin->setEmail('Administratie@inzonenwind.nl');
        $admin->setPassword($this->encoder->encodePassword($admin,'0000'));
        $admin->setRoles(array('ROLE_USER','ROLE_ADMIN','ROLE_REGISTER','ROLE_READ'));
        $manager->persist($admin);

        $manager->flush();

        $admin = new User();
        $admin->setUsername('alecfiddelaar');
        $admin->setFirstname('Alec');
        $admin->setLastname('Fiddelaar');
        $admin->setPayed(true);
        $admin->setActivateUser(true);
        $admin->setEmail('alec@inzonenwind.nl');
        $admin->setPassword($this->encoder->encodePassword($admin,'0000'));
        $admin->setRoles(array('ROLE_USER'));
        $manager->persist($admin);

        $manager->flush();
        $admin = new User();
        $admin->setUsername('melissakooij');
        $admin->setFirstname('Melissa');
        $admin->setLastname('Kooij');
        $admin->setPayed(true);
        $admin->setActivateUser(true);
        $admin->setEmail('nelissa@inzonenwind.nl');
        $admin->setPassword($this->encoder->encodePassword($admin,'0000'));
        $admin->setRoles(array('ROLE_USER'));
        $manager->persist($admin);

        $manager->flush();


    }
}
