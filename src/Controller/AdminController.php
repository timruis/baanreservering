<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\PageVisit;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\BackgroundImagesType;
use App\Form\MessageType;
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
        $Messages = $this->getDoctrine()->getRepository(Message::class)->findAll();
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
            $InfoAccount->setPassword($this->encoder->encodePassword($InfoAccount,$data->getPassword()));
            $em->persist($InfoAccount);
            $em->flush();
            return $this->redirectToRoute('admin-Tools');
        }
        return $this->render('admin/adminTools.html.twig', [
            'PageVisits'=>$PageVisits,
            'NewAccount' => $form->createView(),
            'Accounts' => $Accounts,
            'messages'=>$Messages,
            'Title'=> "Admin Tools",
            'TotalUsers'=>$Accounts
        ]);
    }
    /**
     * @Route("/admin/admin/tracker", name="adminUserTracker")
     */
    public function adminUserTracker(EntityManagerInterface $em, Request $request)
    {

        return $this->render('admin/adminUserTracker.html.twig', [

            'Title'=> "Admin Tools"
        ]);
    }
    /**
     * @Route("/priviliged/usertracker/list", name="usertracker_List_data")
     */
    public function PageVisitListData()
    {

        $PageVisits = $this->getDoctrine()->getRepository(PageVisit::class)->findAllVisitsOnDate();

        $allVisits = array();
        $i=1;
        foreach ($PageVisits as $PageVisit) {
            array_push($allVisits, array(
                    '<img alt="Image" src="uploads/Images/profile/' . $PageVisit->getUser()->getProfileImage() . '" class="avatar avatar-lg mt-1">',
                    $PageVisit->getUser()->getFirstname() . " " . $PageVisit->getUser()->getLastname(),
                    $PageVisit->getCurrentUrl(),
                    $PageVisit->getTime()->format('u'),

                )
            );
        }


        $GoodArray = array('data' => $allVisits);
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($GoodArray)
        );
    }



    /**
     * @Route("/admin/messages", name="see-admin-Messages")
     */
    public function adminMessage(EntityManagerInterface $em, Request $request)
    {
        $Messages = $this->getDoctrine()->getRepository(Message::class)->findAll();


        return $this->render('admin/dashboardMessage.html.twig', [
            'messages'=>$Messages,
            'Title'=> "Admin Tools",
        ]);
    }

    /**
     * @Route("/admin/admin-Message", name="admin-Message")
     */
    public function Message(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data=$form->getData();
            $InfoMessage = new Message();
            $InfoMessage->setColor($data->getColor());
            $InfoMessage->setMessage($data->getMessage());
            $InfoMessage->setEndingDate($data->getEndingDate());
            $InfoMessage->setStartingDate($data->getStartingDate());
            $em->persist($InfoMessage);
            $em->flush();
            return $this->redirectToRoute('see-admin-Messages');
        }

        return $this->render('admin/adminMessage.html.twig', [
            'Message' => $form->createView(),
            'Title'=> "Admin Message"
        ]);
    }
    /**
     * @Route("/priviliged/Registry/Change-Message-Info/{MessageId}", name="Message-Change")
     */
    public function ChangeMessage($MessageId, EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Message = $em->getRepository('App\Entity\Message')->find($MessageId);

        $form = $this->createForm(MessageType::class, $Message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('see-admin-Messages');
        }


        return $this->render('admin/adminMessage.html.twig', [
            'Message' => $form->createView(),
            'Title' => "Change Existing Message"
        ]);
    }

    /**
     * @Route("/priviliged/Registry/Delete-Message-Info/{MessageId}", name="Message-Delete")
     */
    public function DeleteMessage($MessageId, EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Message = $em->getRepository('App\Entity\Message')->find($MessageId);


        $em = $this->getDoctrine()->getManager();
        $em->remove($Message);
        $em->flush();
        return $this->redirectToRoute('see-admin-Messages');
    }

}
