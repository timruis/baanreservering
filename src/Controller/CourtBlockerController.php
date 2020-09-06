<?php

namespace App\Controller;

use App\Entity\CourtReservation;
use App\Form\CourtBlockerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CourtBlockerController extends AbstractController
{
    /**
     * @Route("/admin/banenBlocker/CourtReservation-new", name="CourtReservation-Registry")
     */
    public function RegisteredCourtReservation(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(CourtBlockerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $startingDate = new \DateTime(date("Y/m/d H:i:s",  $data->getStartTime()->getTimestamp()));
            $endingDate = new \DateTime(date("Y/m/d H:i:s",  $form->get('EndDate')->getData()->getTimestamp()));
           while ($startingDate->getTimestamp() <= $endingDate->getTimestamp()) {

                $startingtime = $form->get('startingTime')->getData()->getTimestamp() + 3600;
                $endingtime = $form->get('endingTime')->getData()->getTimestamp() + 3600;
                $date = new \DateTime(date('m/d/Y H:i:s', $startingDate->getTimestamp() + $startingtime));
                $infuture = new \DateTime(date('m/d/Y H:i:s', $startingDate->getTimestamp() + $endingtime));
                while ($date < $infuture) {

                        $courts=$form->get('ChooseCourt')->getData();
                        foreach ($courts as $court) {
                            $InfoCourtReservation = new CourtReservation();
                            $InfoCourtReservation->setStartTime($date);
                            $InfoCourtReservation->setCourt($court);
                            $InfoCourtReservation->setPlayers(2);
                            $InfoCourtReservation->setPlayer($this->getUser());
                            $InfoCourtReservation->setMemoText($data->getMemoText());
                            $InfoCourtReservation->setReservationType($data->getReservationType());

                            $em->persist($InfoCourtReservation);
                            $em->flush();
                        }
                        $date = new \DateTime(date("Y/m/d H:i:s", strtotime("+30 minutes", $date->getTimestamp())));
                }
                if ($form->get('Steps')->getData() === 1) {
                    $startingDate = new \DateTime(date("Y/m/d H:i:s", strtotime("+1 day", $startingDate->getTimestamp())));
                } elseif ($form->get('Steps')->getData() === 2) {
                    $startingDate = new \DateTime(date("Y/m/d H:i:s", strtotime("+1 week", $startingDate->getTimestamp())));
                } elseif ($form->get('Steps')->getData() === 3) {
                    $startingDate = new \DateTime(date("Y/m/d H:i:s", strtotime("+1 month", $startingDate->getTimestamp())));
                }else{
                    $startingDate = new \DateTime(date("Y/m/d H:i:s", strtotime("+1 day", $startingDate->getTimestamp())));
                }

            }
            return $this->redirectToRoute('CourtReservation-Registry');
        }

        return $this->render('Court_blocker/AdminCourtReservationRegistry.html.twig', [
            'CourtReservation' => $form->createView(),
            'Title'=> "Register new CourtReservation"
        ]);
    }
}
