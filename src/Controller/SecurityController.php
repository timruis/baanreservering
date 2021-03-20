<?php

namespace App\Controller;

use App\Entity\ForgetPasswordCode;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\ForgotPasswordType;
use App\Form\PasswordChangeType;
use App\Form\RegisterType;
use App\Form\UserChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
        // Get the image and convert into string
        $img = file_get_contents($this->getParameter('public_directory') .'\\images\\'.rand(1, 3).'.jpg');
        $BackgroundImage = base64_encode($img);
        $img = file_get_contents($this->getParameter('public_directory') .'\\images\\logo.jpg');
        $iconImage = base64_encode($img);
        return $this->render('Emails/ForgetPassword.html.twig', [
            'expiration_date' => new \DateTime('+1 days'),
            'Account' => $Account,
            'code' => "een code",
            'iconImage' =>$iconImage,
            'BackgroundImage' =>$BackgroundImage,
        ]);
    }

    /**
     * @Route("/login/Forgot-Password", name="forgotpassword")
     */
    public function ForgetPassword(MailerInterface $mailer,EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        $code=$request->get('code');
        $email=$request->get('_email');
        if (isset($code) && !empty($code)) {
            $Account = $em->getRepository('App\Entity\User')->findOneBy(['email' => $email]);
            $RequestedCodes = $em->getRepository('App\Entity\ForgetPasswordCode')->findOneByDateAndCode($code,$Account);
          if(isset($RequestedCodes) && !empty($RequestedCodes)) {
              $validateCode = $RequestedCodes->getValidateKey();
              if (isset($validateCode) && $validateCode > 100000 && $validateCode < 999999) {
                  return $this->redirectToRoute('NewPasswordChanger', array('validcode' => $validateCode));
              } else {
                  return $this->render('security/forgotpassword.html.twig', [
                      'passforgot' => $form->createView(),
                      'imgnumber' => rand(1, 3),
                      'email' => $email,
                      'data' => "codeCheck"
                  ]);
              }
          }else{
              return $this->render('security/forgotpassword.html.twig', [
                  'passforgot' => $form->createView(),
                  'imgnumber' => rand(1, 3),
                  'email' => $email,
                  'data' => "codeIncorrectCheck"
              ]);
          }
        }

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $Account = $em->getRepository('App\Entity\User')->findOneBy(['email' => $data->getEmail()]);

            foreach ($Account->getForgetPasswordCodes() as $ForgetCode){
                $from = new \DateTime();
                $to   = new \DateTime('+24 hours');
                $date= $ForgetCode->getValidUntil()->format('U');
                if($date > $from->format('U') && $date < $to->format('U')){
                    return $this->render('security/forgotpassword.html.twig', [
                        'passforgot' => $form->createView(),
                        'imgnumber' => rand(1, 3),
                        'email' => $data->getEmail(),
                        'data' => "codeAlreadyExists"
                    ]);
                }
            }
                if (isset($Account) && !empty($Account) ) {

                    $numberCode = mt_rand(100000, 999999);
                    $em = $this->getDoctrine()->getManager();
                    $codeAlreadyExi = $em->getRepository('App\Entity\ForgetPasswordCode')->findOneBy(['ValidateKey' => $numberCode]);
                    while ($codeAlreadyExi == $numberCode) {
                        $numberCode = mt_rand(100000, 999999);
                    }

                    $email = (new TemplatedEmail())
                        ->from('NO-REPLY@baanreserverenzonenwind.nl')
                        ->to($Account->getEmail())
                        ->subject('Zon en Wind Wachtwoord Reset')
                        ->htmlTemplate('Emails/ForgetPassword.html.twig')
                        ->context([
                            'expiration_date' => new \DateTime('+1 days'),
                            'Account' => $Account,
                            'imgnumber' => rand(1, 3),
                            'code' => $numberCode,
                        ]);

                    $data = $form->getData();
                    $ChangePasswordCode = new ForgetPasswordCode();
                    $ChangePasswordCode->setValidateKey($numberCode);
                    $ChangePasswordCode->setValidUntil(new \DateTime('+1 days'));
                    $ChangePasswordCode->setUser($Account);
                    $em->persist($ChangePasswordCode);
                    $em->flush();
                    $mailer->send($email);

                    return $this->render('security/forgotpassword.html.twig', [
                        'passforgot' => $form->createView(),
                        'imgnumber' => rand(1, 3),
                        'email' => $data->getEmail(),
                        'data' => "codeCheck"
                    ]);
                }else {
                    $form->addError(new FormError('Niet toegestaan gebruiker bestaat niet.'));
                    return $this->render('security/forgotpassword.html.twig', [
                        'passforgot' => $form->createView(),
                        'imgnumber' => rand(1, 3),
                        'email' => $data->getEmail(),
                        'data' => $data
                    ]);
            }
        }

        return $this->render('security/forgotpassword.html.twig', [
            'passforgot'=>$form->createView(),
            'imgnumber' => rand(1, 3),
            'email' => "unknown",
            'data' => "NoAccount"
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
     * @Route("/login/PasswordChanger/{validcode}", name="NewPasswordChanger")
     */
    public function PasswordChanger($validcode, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $ValidKey = $em->getRepository('App\Entity\ForgetPasswordCode')->findOneBy(['ValidateKey' => $validcode]);
        $user =  $ValidKey->getUser();
        $form = $this->createForm(PasswordChangeType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $password2=$request->get('password_change')['password']['second'];
            $password=$request->get('password_change')['password']['first'];
            if ($password === $password2 ) {
                $data = $form->getData();
                $checkPass = $passwordEncoder->encodePassword($user, $data->getPassword());
                $user->setPassword($checkPass);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $em = $this->getDoctrine()->getManager();
                $em->remove($ValidKey);
                $em->flush();
                return $this->redirectToRoute('app_login');
            }else{
                $form->addError(new FormError('Wachtwoorden matchen niet. Probeer opnieuw'));
            }

        }

        return $this->render('security/forgotpassword.html.twig', [
            'passforgot' => $form->createView(),
            'imgnumber' => rand(1, 3),
            'email' => $user->getEmail(),
            'data' => "NoAccount"
        ]);
    }
    /**
     * @Route("/admin/Registry/Change-Account-Info/{AccountId}", name="Account-Change")
     */
    public function ChangeAccount($AccountId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Account = $em->getRepository('App\Entity\User')->find($AccountId);
        $formAccount = $this->createForm(AccountType::class, $Account);
        $formAccount->handleRequest($request);

        $form = $this->createForm(UserChangeType::class, $Account);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data=$form->getData();
            $Account->setUsername($data->getUsername());
            $Account->setEmail($data->getEmail());
            $Account->setRoles($data->getRoles());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            if ($form->get('OpenList')->isClicked()) {
                return $this->redirectToRoute('admin-Tools');
            }elseif ($form->get('StayOn')->isClicked()){
                return $this->redirectToRoute('Account-Change', array('AccountId' => $AccountId));
            } else {
                return $this->redirectToRoute('Account-Registry');
            }
        }
        if ($formAccount->isSubmitted()) {
            $data = $formAccount->getData();
            $Account->setEmail($data->getEmail());
            $Account->setFirstname($data->getFirstname());
            $Account->setLastname($data->getLastname());
            $Account->setProfileImage($Account->getProfileImage());
            $Account->setBackgroundImage($Account->getBackgroundImage());
            $Account->setAddress($data->getAddress());
            $Account->setMobile($data->getMobile());
            $Account->setDescription($data->getDescription());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('Account-Change', array('AccountId' => $AccountId));

        }


        return $this->render('Accounts/AccountRegistry.html.twig', [
            'Account' => $form->createView(),
            'Accountprofile' => $formAccount->createView(),
            'Title'=> "Change Existing Account"
        ]);
    }
    /**
     * @Route("/admin/Registry/Delete-Account-Info/{AccountId}", name="Account-Delete")
     */
    public function DeleteAccount($AccountId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Account = $em->getRepository('App\Entity\User')->find($AccountId);


        $em= $this->getDoctrine()->getManager();
        $em->remove($Account);
        $em->flush();
        return $this->redirectToRoute('Listed-Accounts');
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
