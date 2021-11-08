<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Moyens;
use App\Entity\Demandes;
use App\Entity\Services;
use App\Entity\CategoryMoyens;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PlanningMC');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Site configuration');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-industry', Services::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-puzzle-piece', CategoryMoyens::class);
        yield MenuItem::linkToCrud('Machines', 'fas fa-cog', Moyens::class)->setPermission('ROLE_RESP_POLYM');
        yield MenuItem::section('Administration Polym');
        yield MenuItem::linkToCrud('Demands', 'fas fa-list', Demandes::class);
        yield MenuItem::linkToCrud('Teams', 'fas fa-calendar', User::class);
    }
}
