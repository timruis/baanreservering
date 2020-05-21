<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CheckCourtsController extends AbstractController
{
    /**
     * @Route("/check/Courts", name="check_Courts")
     */
    public function index()
    {
        return $this->render('check_Courts/index.html.twig', [
            'controller_name' => 'CheckCourtsController',
        ]);
    }
}
