<?php

namespace App\Controller;

use App\Entity\CourtReservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CheckCourtsController extends AbstractController
{
    /**
     * @Route("/Players/check/Court/{time}/{Court}", name="check_Court")
     */
    public function index(EntityManagerInterface $em, Request $request,$time,$Court)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findReservations($time, $Court);
           if(count($CourtReservations)>1) {

                   return $this->render('check_Court/CheckDoubleReservation.html.twig', [
                       'Title' => 'CheckCourtsController',
                       'CourtReservations'=>$CourtReservations,
                   ]);
           }else{
               $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->findReservation($time, $Court);
               return $this->render('check_Court/index.html.twig', [
                   'Title' => 'CheckCourtsController',
                   'CourtReservation'=>$CourtReservation,
               ]);
           }

    }

    /**
     * @Route("/admin/players/CourtReservation/{date}", name="CourtPlayersReservationAdmin")
     */
    public function courtReservation($date)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findToday($date);
        $takenSpots=[];
        foreach ($CourtReservations as $CourtReservation){
            array_push($takenSpots ,$CourtReservation->getStartTime()->format('U').$CourtReservation->getCourt()."b".$CourtReservation->getReservationType());
            array_push($takenSpots ,$CourtReservation->getStartTime()->format('U').$CourtReservation->getCourt());
        }
        $timeArray = [];
        if(date('N', strtotime($date)) >= 6) {

            for ($x = 8; $x <= 17; $x++) {
                if ($x ===8){
                    array_push($timeArray, $x . ":30");
                }else {
                    array_push($timeArray, $x . ":00", $x . ":30");
                }
            }
        }else {
            for ($x = 8; $x <= 22; $x++) {
                if ($x ===8){
                    array_push($timeArray, $x . ":30");
                }else {
                    array_push($timeArray, $x . ":00", $x . ":30");
                }
            }
        }

        $sunsetTimestamp =strtotime ($date);
        $sundown=date_sunset($sunsetTimestamp, SUNFUNCS_RET_STRING, 52.29583, 5.1625, 89, 1);


        return $this->render('check_Court/seePlayersPerCourt.html.twig', [
            'controller_name' => 'CourtReservationController',
            'allReservations' => $takenSpots,
            'times'=>$timeArray,
            'ChosenDate'=>$date,
            'sundown'=>$sundown
        ]);
    }

    /**
     * @Route("/Players/check/Court", name="checkOwnReservations")
     */
    public function checkOwnReservations(EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservations = $this->getUser()->getCourtReservations();
        $CourtReservationsTeam = $this->getUser()->getCourtReservationsTeam();
        return $this->render('check_Court/CheckReservation.html.twig', [
            'Title' => 'CheckCourtsController',
            'CourtReservations'=>$CourtReservations,
            'TeamCourtReservations'=>$CourtReservationsTeam,
        ]);
    }

    /**
     * @Route("/DeleteCourtReservation/{time}/{Court}", name="PlayerDeleteReservation")
     */
    public function DeleteRegister(EntityManagerInterface $em, Request $request,$time,$Court)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findReservations($time,$Court);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        foreach ($CourtReservations as $CourtReservation){
            if($this->getUser() === $CourtReservation->getPlayer() || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER-USER') ) {
                $em->remove($CourtReservation);
                $em->flush();
            }
        }
        return $this->redirectToRoute('checkOwnReservations');

    }

    /**
     * @Route("/ChangeCourtReservation/{time}/{Court}", name="PlayerChangeReservation")
     */
    public function ChangeRegister(EntityManagerInterface $em, Request $request,$time,$Court)
    {
        $em = $this->getDoctrine()->getManager();

        $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->findReservation($time,$Court);
        $form = $this->createForm(ReservationType::class, $CourtReservation , ['ChosenDate'=>$time] );
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $arrayMakeNewTeam=[];
            $data=$form->getData();
            foreach ($data->getOtherPlayers() as $Player) {
                array_push($arrayMakeNewTeam , $Player);
            }
            foreach ($CourtReservation->getOtherPlayers() as $Player) {
                $Player->removeCourtReservationsTeam($CourtReservation);
                $em->persist($Player);
                $em->flush();
            }
            $em = $this->getDoctrine()->getManager();
            $FormCourtReservation = $em->getRepository('App\Entity\CourtReservation')->findReservation($time,$Court);

            $FormCourtReservation->setReservationType(9);
            $FormCourtReservation->setPlayers(count($arrayMakeNewTeam) + 1);
            foreach ($arrayMakeNewTeam as $Player) {
                $FormCourtReservation->addOtherPlayer($Player);
            }
            $FormCourtReservation->setPlayer($this->getUser());
            $em->persist($FormCourtReservation);
            $em->flush();

            return $this->redirectToRoute('checkOwnReservations');


            }else{
                $form->get('OtherPlayers0')->setData($CourtReservation->getOtherPlayers()[0]);
                $form->get('OtherPlayers1')->setData($CourtReservation->getOtherPlayers()[1]);
                $form->get('OtherPlayers2')->setData($CourtReservation->getOtherPlayers()[2]);
            }

            return $this->render('Court_reservation/Reservation.html.twig', [
                'controller_name' => 'CourtReservationController',
                'reservation'=>$form->createView(),
            ]);

    }
    /**
     * @Route("/DeleteCourtDublicate/{id}", name="DeleteDublicate")
     */
    public function DeleteDublicate(EntityManagerInterface $em, Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->find($id);
        } catch (FileException $e) {

        }
        $time=$CourtReservation->getStartTime()->format('U');
        $court=$CourtReservation->getCourt();
        if($this->getUser() === $CourtReservation->getPlayer() || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER-USER') ) {
            $em->remove($CourtReservation);
            $em->flush();
        }
        return $this->redirectToRoute('check_Court',array('time' => $time,'Court' => $court));

    }

}
