<?php

namespace App\Controller;

use App\Entity\CourtReservation;
use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class IntroduceController extends AbstractController
{
    /**
     * @Route("/all/Introduces", name="Introduces")
     */
    public function dashboard()
    {
        $em = $this->getDoctrine();
        $CourtReservations = $em->getRepository(CourtReservation::class)->findAll();
        $IntroduceDate = $em->getRepository('App\Entity\Settings')->findOneBy(['Name'=> 'IntroduceDate'])->getValue();
        $IntroduceDate= new \DateTime($IntroduceDate);
        $Players= [];
        foreach($CourtReservations as $courtReservation) {
            if ($courtReservation->getStartTime()->format('U') > $IntroduceDate->format('U')){
                if ($courtReservation->getReservationType() == 6) {
                    if (!isset($Players[ucwords($courtReservation->getMemoText())]) && empty($Players[ucwords($courtReservation->getMemoText())])) {
                        $Players[ucwords($courtReservation->getMemoText())]["amount"] = 0;
                        $Players[ucwords($courtReservation->getMemoText())]["Name"] = ucwords($courtReservation->getMemoText());
                    }
                    $Players[ucwords($courtReservation->getMemoText())]["amount"]++;
                    if (!empty($courtReservation->getPlayer() && empty($Players[ucwords($courtReservation->getMemoText())]["Introducers"]))) {
                        $Players[ucwords($courtReservation->getMemoText())]["Introducers"] = [[$courtReservation->getPlayer()->getId() => $courtReservation->getPlayer()->getFirstname() . " " . $courtReservation->getPlayer()->getLastname() . " baan " . $courtReservation->getCourt() . " datum " . $courtReservation->getStartTime()->format('d/m/Y')]];
                    } elseif (!empty($courtReservation->getPlayer())) {
                        array_push(
                            $Players[ucwords($courtReservation->getMemoText())]["Introducers"], [$courtReservation->getPlayer()->getId() => $courtReservation->getPlayer()->getFirstname() . " " . $courtReservation->getPlayer()->getLastname() . " baan " . $courtReservation->getCourt() . " datum " . $courtReservation->getStartTime()->format('d/m/Y')]);
                    }
                    foreach ($courtReservation->getOtherPlayers() as $OtherPlayers) {
                        if (!empty($OtherPlayers) && empty($Players[ucwords($courtReservation->getMemoText())]["Introducers"])) {
                            $Players[ucwords($courtReservation->getMemoText())]["Introducers"] = [[$OtherPlayers->getId() => $OtherPlayers->getFirstname() . " " . $OtherPlayers->getLastname() . " baan " . $courtReservation->getCourt() . " datum " . $courtReservation->getStartTime()->format('d/m/Y')]];
                        } elseif (!empty($OtherPlayers)) {
                            array_push($Players[ucwords($courtReservation->getMemoText())]["Introducers"], [$OtherPlayers->getId() => $OtherPlayers->getFirstname() . " " . $OtherPlayers->getLastname() . " baan " . $courtReservation->getCourt() . " datum " . $courtReservation->getStartTime()->format('d/m/Y')]);
                        }
                    }
                }
            }
        }
        return $this->render('Introduce/index.html.twig', [
            'Title'=> "Introduces",
            'Introduces'=>$Players,
        ]);
    }


}
