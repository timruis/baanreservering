<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('dashboard/dashboard.html.twig', [
            'Title'=> "Dashboard"
        ]);
    }
    /**
     * @Route("/admin/reservation", name="admin-dashboard")
     */
    public function home()
    {
        return $this->render('dashboard/admin_dashboard.html.twig', [
            'Title'=> "Dashboard"
        ]);
    }
}
