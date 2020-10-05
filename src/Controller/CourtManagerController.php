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

class CourtManagerController extends AbstractController
{

    /**
     * @Route("/admin/CourtReservation/{date}", name="CourtReservationAdmin")
     */
    public function courtPerPlayerReservation($date)
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
        return $this->render('Court_manager/admin.html.twig', [
            'controller_name' => 'CourtReservationController',
            'allReservations' => $takenSpots,
            'times'=>$timeArray,
            'ChosenDate'=>$date,
            'sundown'=>$sundown
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
            if ($data->getReservationType() === false and $form->get('introduce')->getData() === false) {
                $date = new \DateTime(date('m/d/Y H:i:s', $time));
                $TwoHoursinfuture = $date->add(new \DateInterval("PT3H"));
                $date = new \DateTime(date('m/d/Y H:i:s', $time));
                $TwoHoursInPast = $date->sub(new \DateInterval("PT2H"));

                $cannotMakeReservation = false;

                foreach ($data->getOtherPlayers() as $Player) {
                    foreach ($Player->getCourtReservations() as $courtReservation) {
                        if (($courtReservation->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservation->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation === true) {
                            $cannotMakeReservation = true;
                        } else {
                            $cannotMakeReservation = false;
                        }
                    }
                    foreach ($Player->getCourtReservationsTeam() as $courtReservationTeam) {
                        if (($courtReservationTeam->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservationTeam->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation === true) {
                            $cannotMakeReservation = true;
                        } else {
                            $cannotMakeReservation = false;
                        }
                    }
                }
                foreach ($this->getUser()->getCourtReservations() as $courtReservation) {
                    if (($courtReservation->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservation->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation) {
                        $cannotMakeReservation = true;
                    } else {
                        $cannotMakeReservation = false;
                    }
                }
                foreach ($this->getUser()->getCourtReservationsTeam() as $courtReservationTeam) {
                    if (($courtReservationTeam->getStartTime()->format('U') >= $TwoHoursInPast->format('U') && $courtReservationTeam->getStartTime()->format('U') <= $TwoHoursinfuture->format('U')) || $cannotMakeReservation) {
                        $cannotMakeReservation = true;
                    } else {
                        $cannotMakeReservation = false;
                    }
                }
            }else{
                $cannotMakeReservation = false;
            }
            $date = new \DateTime(date('m/d/Y H:i:s', $time));
            $em = $this->getDoctrine()->getManager();
            $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findReservation($date->format("U"),$Court);
            if ($CourtReservations){
                $cannotMakeReservation = true;
            }
            if($cannotMakeReservation) {
                $form->addError(new FormError('Niet toegestaan dubbele reservering.'));
            }else{
                $InfoCourtReservation = new CourtReservation();
                $InfoCourtReservation->setStartTime($date);
                $InfoCourtReservation->setCourt($Court);

                if ($data->getReservationType()){
                    $InfoCourtReservation->setPlayers($data->getPlayers());
                    $InfoCourtReservation->setMemoText($data->getMemoText());
                    $InfoCourtReservation->setReservationType(5);
                }elseif ($form->get('introduce')->getData()){

                    $InfoCourtReservation->setPlayers($form->get('PlayersIntroduce')->getData());
                    $InfoCourtReservation->setMemoText($form->get('MemoTextIntroduce')->getData());
                    $InfoCourtReservation->setReservationType(6);
                }
                else{
                    $InfoCourtReservation->setReservationType(9);
                    foreach ($data->getOtherPlayers() as $Player) {
                        $InfoCourtReservation->addOtherPlayer($Player);
                    }
                    $InfoCourtReservation->setPlayer($data->getPlayer());
                    $InfoCourtReservation->setPlayers($data->getOtherPlayers()->count() + 1);
                }

                $em->persist($InfoCourtReservation);
                $em->flush();
                return $this->redirectToRoute('admin-dashboard');
            }
        }
        return $this->render('Court_manager/AdminReservation.html.twig', [
            'controller_name' => 'CourtReservationController',
            'reservation'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/admin/Change-CourtReservation/{CourtReservationId}", name="CourtReservation-Change")
     */
    public function ChangeCourtReservation($CourtReservationId,EntityManagerInterface $em, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->find($CourtReservationId);

        $form = $this->createForm(CourtBlockerType::class, $CourtReservation);
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
        return $this->render('Court_manager/CourtReservationRegistry.html.twig', [
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
     * @Route("/admin/MonthCourtReservations", name="Listed-CourtReservations")
     */
    public function ListedCourtReservations()
    {
        $user= $this->getUser();
        $CourtReservations = $this->getDoctrine()->getRepository(CourtReservation::class)->findAll();
        return $this->render('Court_manager/ShowAllCourtReservations.html.twig', [
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

        return $this->render('Court_manager/ShowAllCourtReservations.html.twig', [
            'CourtReservations' => $CourtReservations,
            'Title'=> "CourtReservations"
        ]);
    }
    /**
     * @Route("/admin/DeleteCourtReservation/{time}/{Court}", name="DeleteRegisterGame")
     */
    public function AdminDeleteRegister(EntityManagerInterface $em, Request $request,$time,$Court)
    {
        $em = $this->getDoctrine()->getManager();
        $CourtReservations = $em->getRepository('App\Entity\CourtReservation')->findReservations($time,$Court);
        foreach ($CourtReservations as $CourtReservation) {
            $em->remove($CourtReservation);
            $em->flush();
        }
        $date = new \DateTime(date('m/d/Y', $time));
        return $this->redirectToRoute('CourtPlayersReservationAdmin', array('date' => $date->format('Y-m-d')));

    }

}
