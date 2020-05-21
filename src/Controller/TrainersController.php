<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TrainersController extends AbstractController
{
    /**
     * @Route("/trainers", name="trainers")
     */
    public function index()
    {
        return $this->render('trainers/index.html.twig', [
            'controller_name' => 'TrainersController',
        ]);
    }
}
