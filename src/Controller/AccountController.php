<?php

namespace App\Controller;

use App\Entity\PageVisit;
use App\Entity\User;
use App\Form\BackgroundImagesType;
use App\Form\AccountType;
use App\Form\PasswordChangeType;
use App\Form\ProfileImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AccountController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/Courter", name="Courter")
     * @Method("POST")
     */
    public function UserCourter(EntityManagerInterface $em, Request $request)
    {
        $Link = $request->get('Link');

        $pageVisit = new PageVisit();
        $pageVisit->setCurrentUrl($Link);
        $pageVisit->setTime(new \DateTime());

        $em->persist($pageVisit);
        $em->flush();
        return new Response(
            json_encode("Uploaded")
        );
    }

    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('timsplinter@live.nl')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return $this->redirectToRoute('app_login');

    }
}
