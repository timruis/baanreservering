<?php

namespace App\Controller;

use App\Entity\CourtReservation;
use App\Form\CourtBlockerType;
use App\Form\ReservationAdminType;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
        $sundown=date_sunset(time(), SUNFUNCS_RET_STRING, 52.29583, 5.1625, 92, 1);
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
            $date = new \DateTime(date('m/d/Y H:i:s', $time));
            $TwoHoursinfuture = $date->add(new \DateInterval("PT3H"));
            $date = new \DateTime(date('m/d/Y H:i:s', $time));
            $TwoHoursInPast = $date->sub(new \DateInterval("PT2H"));

            $cannotMakeReservation = false;

            foreach ($data->getOtherPlayers() as $Player) {
                foreach ($Player->getCourtReservations() as $courtReservation){
                    if(($courtReservation->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservation->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation === true){
                        $cannotMakeReservation = true;
                    }else{
                        $cannotMakeReservation = false;
                    }
                }
                foreach ($Player->getCourtReservationsTeam() as $courtReservationTeam){
                    if(($courtReservationTeam->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservationTeam->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation === true){
                       $cannotMakeReservation = true;
                    }else{
                        $cannotMakeReservation = false;
                    }
                }
            }
            foreach ($this->getUser()->getCourtReservations() as $courtReservation){
                    if(($courtReservation->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservation->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation){
                        $cannotMakeReservation = true;
                    }else{
                        $cannotMakeReservation = false;
                    }
                }
                foreach ($this->getUser()->getCourtReservationsTeam() as $courtReservationTeam){
                    if(($courtReservationTeam->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservationTeam->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation){
                        $cannotMakeReservation = true;
                    }else{
                        $cannotMakeReservation = false;
                    }
                }
            if($cannotMakeReservation) {
                $form->addError(new FormError('Niet toegestaan dubbele reservering.'));
            }else{
                $InfoCourtReservation = new CourtReservation();
                $date = new \DateTime(date('m/d/Y H:i:s', $time));
                $InfoCourtReservation->setStartTime($date);
                $InfoCourtReservation->setCourt($Court);
                $InfoCourtReservation->setPlayers($data->getOtherPlayers()->count() + 1);
                foreach ($data->getOtherPlayers() as $Player) {
                    $InfoCourtReservation->addOtherPlayer($Player);
                }
                $InfoCourtReservation->setPlayer($this->getUser());
                $em->persist($InfoCourtReservation);
                $em->flush();
                return $this->redirectToRoute('dashboard');

               }
        }

        return $this->render('Court_reservation/Reservation.html.twig', [
            'controller_name' => 'CourtReservationController',
            'reservation'=>$form->createView(),
        ]);
    }

}
