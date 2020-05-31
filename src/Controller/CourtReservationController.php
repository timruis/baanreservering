<?php

namespace App\Controller;

use App\Entity\CourtReservation;
use App\Form\CourtReservationType;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CourtReservationController extends AbstractController
{
    /**
     * @Route("/CourtReservation/{date}", name="CourtReservation")
     */
    public function index($date)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findToday($date);
        $takenSpots=[];
        foreach ($CourtReservations as $CourtReservation){
            array_push($takenSpots ,$CourtReservation->getStartTime()->format('U').$CourtReservation->getCourt().$CourtReservation->getPlayers());
        }
        $timeArray = [];
        if(date('N', strtotime($date)) >= 6) {

            for ($x = 9; $x <= 17; $x++) {
                array_push($timeArray, $x . ":00", $x . ":30");
            }
        }else {
            for ($x = 9; $x <= 22; $x++) {
                array_push($timeArray, $x . ":00", $x . ":30");
            }
        }
        $sundown=date_sunset(time(), SUNFUNCS_RET_STRING, 38.4, -9, 90, 1);
        return $this->render('Court_reservation/index.html.twig', [
            'controller_name' => 'CourtReservationController',
            'allReservations' => $takenSpots,
            'times'=>$timeArray,
            'ChosenDate'=>$date,
            'sundown'=>$sundown
        ]);
    }
    /**
     * @Route("/CourtReservation/{time}/{Court}", name="RegisterGame")
     */
    public function Register(EntityManagerInterface $em, Request $request,$time,$Court)
    {
        $form = $this->createForm(ReservationType::class, null , ['ChosenDate'=>$time]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $InfoCourtReservation = $this->getUser()->getCourtReservations();

            $data=$form->getData();
            $InfoCourtReservation = new CourtReservation();
            $InfoCourtReservation->setCourt($Court);
            $InfoCourtReservation->setPlayers($data->getOtherPlayers()->count()+1);
            foreach ($data->getOtherPlayers() as $Player) {
                $InfoCourtReservation->addOtherPlayer($Player);
            }
            $date = new \DateTime(date('m/d/Y H:i:s', $time));
            $InfoCourtReservation->setStartTime($date);
            $InfoCourtReservation->setPlayer($this->getUser());
            $em->persist($InfoCourtReservation);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('Court_reservation/Reservation.html.twig', [
            'controller_name' => 'CourtReservationController',
            'reservation'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/admin/CourtReservation-new", name="CourtReservation-Registry")
     */
    public function RegisteredCourtReservation(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(CourtReservationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $InfoCourtReservation = new CourtReservation();
            $InfoCourtReservation->setCourtReservationLocation($data->getCourtReservationLocation());
            $InfoCourtReservation->setStory($data->getStory());
            $InfoCourtReservation->setCourtReservationName($data->getCourtReservationName());
            $em->persist($InfoCourtReservation);
            $em->flush();
            if ($form->get('OpenList')->isClicked()) {
                return $this->redirectToRoute('Listed-CourtReservations');
            }elseif ($form->get('StayOn')->isClicked()){
                return $this->redirectToRoute('CourtReservation-Change', array('CourtReservationId' => $InfoCourtReservation->getId()));
            } else {
                return $this->redirectToRoute('CourtReservation-Registry');
            }
        }

        return $this->render('Court_reservation/CourtReservationRegistry.html.twig', [
            'CourtReservation' => $form->createView(),
            'Title'=> "Register new CourtReservation"
        ]);
    }
    /**
     * @Route("/admin/Change-CourtReservation/{CourtReservationId}", name="CourtReservation-Change")
     */
    public function ChangeCourtReservation($CourtReservationId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->find($CourtReservationId);

        $form = $this->createForm(CourtReservationType::class, $CourtReservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            if ($form->get('OpenList')->isClicked()) {
                return $this->redirectToRoute('Listed-CourtReservations');
            }elseif ($form->get('StayOn')->isClicked()){
                return $this->redirectToRoute('CourtReservation-Change', array('CourtReservationId' => $CourtReservationId));
            } else {
                return $this->redirectToRoute('CourtReservation-Registry');
            }
        }


        return $this->render('Court_reservation/CourtReservationRegistry.html.twig', [
            'CourtReservation' => $form->createView(),
            'Title'=> "Change Existing CourtReservation"
        ]);
    }
    /**
     * @Route("/admin/Delete-CourtReservation-Info/{CourtReservationId}", name="CourtReservation-Delete")
     */
    public function DeleteCourtReservation($CourtReservationId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->find($CourtReservationId);


        $em= $this->getDoctrine()->getManager();
        $em->remove($CourtReservation);
        $em->flush();
        return $this->redirectToRoute('Listed-CourtReservations');
    }

    /**
     * @Route("/CourtReservations", name="Listed-CourtReservations")
     */
    public function ListedCourtReservations()
    {
        $user= $this->getUser();
        $CourtReservations = $this->getDoctrine()->getRepository(CourtReservation::class)->findAll();

        return $this->render('Court_reservation/ShowAllCourtReservations.html.twig', [
            'CourtReservations' => $user->getCourtReservations(),
            'Title'=> "CourtReservations"
        ]);


    }
    /**
     * @Route("/admin/CourtReservations", name="admin-CourtReservations")
     */
    public function ListedCourtReservationsToday()
    {

        $CourtReservations = $this->getDoctrine()->getRepository(CourtReservation::class)->findAll();

        return $this->render('Court_reservation/ShowAllCourtReservations.html.twig', [
            'CourtReservations' => $CourtReservations,
            'Title'=> "CourtReservations"
        ]);


    }
}
