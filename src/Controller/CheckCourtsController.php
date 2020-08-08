<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $CourtReservation = $em->getRepository('App\Entity\CourtReservation')->findReservation($time, $Court);
        return $this->render('check_Court/index.html.twig', [
            'Title' => 'CheckCourtsController',
            'CourtReservation'=>$CourtReservation,
        ]);
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
        return $this->render('check_Court/seePlayersPerCourt.html.twig', [
            'controller_name' => 'CourtReservationController',
            'allReservations' => $takenSpots,
            'times'=>$timeArray,
            'ChosenDate'=>$date,
            'sundown'=>$sundown
        ]);
    }
}
