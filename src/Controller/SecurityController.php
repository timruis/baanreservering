<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\ForgotPasswordType;
use App\Form\RegisterType;
use App\Form\UserChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\Translator;

class SecurityController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'imgnumber' => rand(1, 3)
        ]);
    }
    /**
     * @Route("/admin/email/{id}", name="email")
     */
    public function email(EntityManagerInterface $em, Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $Account = $em->getRepository('App\Entity\User')->find($id);

        return $this->render('Emails/ForgetPassword.html.twig', [
            'expiration_date' => new \DateTime('+1 days'),
            'Account' => $Account,
            'imgnumber' => rand(1, 3)
        ]);
    }
    /**
     * @Route("/login/Forgot-Password", name="forgotpassword")
     */
    public function ForgetPassword(MailerInterface $mailer,EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $data=$form->getData();
            $em = $this->getDoctrine()->getManager();
            $Account = $em->getRepository('App\Entity\User')->findOneBy(['email'=>$data->getEmail()]);
            if(isset($Account)&& !empty($Account)){
                $email = (new TemplatedEmail())
                    ->from('NO-REPLY@baanreserverenzonenwind.nl')
                    ->to($Account->getEmail())
                    ->subject('Zon en Wind Wachtwoord Reset')
                    ->htmlTemplate('Emails/ForgetPassword.html.twig')
                    ->context([
                        'expiration_date' => new \DateTime('+1 days'),
                        'Account' => $Account,
                        'imgnumber' => rand(1, 3)
                    ]);

                $mailer->send($email);
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/forgotpassword.html.twig', [
            'passforgot'=>$form->createView(),
            'imgnumber' => rand(1, 3)
        ]);
    }
    /**
     * @Route("/Register", name="app_Register")
     */
    public function Register(EntityManagerInterface $em, Request $request)
    {

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $User = new User();
            $User->setUsername($data->getUsername());
            $User->setEmail($data->getEmail());
            $checkPass = $this->encoder->encodePassword($User, $data->getPassword());
            $User->setPassword($checkPass);
            $User->setFirstname($data->getFirstname());
            $User->setLastname($data->getLastname());
            $User->setActivateUser(false);
            $User->setPayed(false);
            $User->setSummerMember(false);
            $User->setWinterMember(false);
            $em->persist($User);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'controller_name' => 'CourtReservationController',
            'UserReg'=>$form->createView(),
            'imgnumber' => rand(1, 3)
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
