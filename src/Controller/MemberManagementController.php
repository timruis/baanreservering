<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;
use App\Form\MemberType;
use App\Form\UserChangeType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response;

class MemberManagementController extends AbstractController
{

    /**
     * @Route("/admin/Change-Member-Info/{MemberId}", name="Member-Change")
     */
    public function ChangeMember($MemberId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Member = $em->getRepository('App\Entity\User')->find($MemberId);

        $form = $this->createForm(UserChangeType::class, $Member);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            if ($form->get('OpenList')->isClicked()) {
                return $this->redirectToRoute('Listed-Member');
            }elseif ($form->get('StayOn')->isClicked()){
                return $this->redirectToRoute('Member-Change', array('MemberId' => $MemberId));
            } else {
                return $this->redirectToRoute('Member-Registry');
            }
        }


        return $this->render('Member/MemberRegistry.html.twig', [
            'Member' => $form->createView(),
            'Title'=> "Change Existing Member"
        ]);
    }
    /**
     * @Route("/admin/Delete-Member-Info/{MemberId}", name="Member-Delete")
     */
    public function DeleteMember($MemberId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Member = $em->getRepository('App\Entity\User')->find($MemberId);


        $em= $this->getDoctrine()->getManager();
        $em->remove($Member);
        $em->flush();
        return $this->redirectToRoute('Listed-Member');
    }

    /**
     * @Route("/admin/Members", name="Members")
     */
    public function ListedMember()
    {
        $Member = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('Member/ShowAllMember.html.twig', [
            'Members' => $Member,
            'Title'=> "Members"
        ]);


    }

    /**
     * @Route("/admin/check/payment", name="Payment")
     */
    public function changepayed(EntityManagerInterface $em,Request $request)
    {

        $id = $request->get('id');
        $User = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($User->getPayed()){
            $User->setPayed(false);
        }else {
            $User->setPayed(true);
        }
        $em->persist($User);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($User->getPayed())
        );
    }
    /**
     * @Route("/admin/check/active", name="Activate")
     */
    public function changeActive(EntityManagerInterface $em,Request $request)
    {

        $id = $request->get('id');
        $User = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($User->getActivateUser()){
            $User->setActivateUser(false);
        }else {
            $User->setActivateUser(true);
        }
        $em->persist($User);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($User->getActivateUser())
        );
    }

}
