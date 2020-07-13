<?php

namespace App\Controller;

use App\Entity\CourtReservation;
use App\Form\CourtReservationType;
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
            array_push($takenSpots ,$CourtReservation->getStartTime()->format('U').$CourtReservation->getCourt().$CourtReservation->getPlayers());
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
     * @Route("/admin/CourtReservation/{date}", name="CourtReservationAdmin")
     */
    public function courtPerPlayerReservation($date)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findToday($date);
        $takenSpots=[];
        foreach ($CourtReservations as $CourtReservation){
            array_push($takenSpots ,$CourtReservation->getStartTime()->format('U').$CourtReservation->getCourt().$CourtReservation->getPlayers());
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
        return $this->render('Court_reservation/admin.html.twig', [
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

    /**
     * @Route("/admin/CourtReservation/{time}/{Court}", name="adminRegisterGame")
     */
    public function AdminRegister(EntityManagerInterface $em, Request $request,$time,$Court)
    {
        $form = $this->createForm(ReservationAdminType::class, null );
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
                $InfoCourtReservation->setPlayer($data->getPlayer());
                $em->persist($InfoCourtReservation);
                $em->flush();
                return $this->redirectToRoute('admin-dashboard');

            }
        }

        return $this->render('Court_reservation/AdminReservation.html.twig', [
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
            $startingtime=$form->get('startingTime')->getData()->getTimestamp()+3600;
            $endingtime=$form->get('endingTime')->getData()->getTimestamp()+3600;
            $date = new \DateTime(date('m/d/Y H:i:s', $data->getStartTime()->getTimestamp()+$startingtime));
            $infuture= new \DateTime(date('m/d/Y H:i:s', $data->getStartTime()->getTimestamp()+$endingtime));
            while ($date <= $infuture) {
                $InfoCourtReservation = new CourtReservation();
                $InfoCourtReservation->setStartTime($date);
                $InfoCourtReservation->setCourt($data->getCourt());
                $InfoCourtReservation->setPlayers(2);
                $InfoCourtReservation->setPlayer($this->getUser());
                $em->persist($InfoCourtReservation);
                $em->flush();
                $date =new \DateTime(date("Y/m/d H:i:s", strtotime("+30 minutes", $date->getTimestamp())));
            }
            return $this->redirectToRoute('CourtReservation-Registry');
        }

        return $this->render('Court_reservation/AdminCourtReservationRegistry.html.twig', [
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
