<?php

namespace App\Controller;

use App\Entity\PageVisit;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\BackgroundImagesType;
use App\Form\PasswordChangeType;
use App\Form\ProfileImageType;
use App\Form\UserChangeType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder= $encoder;
    }
    /**
     * @Route("/admin/admin-tools", name="admin-Tools")
     */
    public function adminTools(EntityManagerInterface $em, Request $request)
    {
        $PageVisits = $this->getDoctrine()->getRepository(PageVisit::class)->findAllVisitsOnDate();
        $Accounts = $this->getDoctrine()->getRepository(User::class)->findAll();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data=$form->getData();
            $InfoAccount = new User();
            $InfoAccount->setUsername($data->getUsername());
            $InfoAccount->setFirstname($data->getFirstname());
            $InfoAccount->setLastname($data->getLastname());
            $InfoAccount->setEmail($data->getEmail());
            $InfoAccount->setRoles($data->getRoles());
            $InfoAccount->setActivateUser(1);
            $InfoAccount->setPayed(1);
            $InfoAccount->setSummerMember(1);
            $InfoAccount->setWinterMember(1);
            $InfoAccount->setPassword($this->encoder->encodePassword($InfoAccount,$data->getPassword()));
            $em->persist($InfoAccount);
            $em->flush();
        }
        return $this->render('admin/adminTools.html.twig', [
            'PageVisits'=>$PageVisits,
            'NewAccount' => $form->createView(),
            'Accounts' => $Accounts,
            'Title'=> "Admin Tools",
            'TotalUsers'=>$Accounts
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
            'userprofile'=>$Account,
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
}
