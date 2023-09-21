<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Moyens;
use App\Entity\OrgaEq;
use App\Entity\Demandes;
use App\Entity\Services;
use App\Entity\NomEquipe;
use App\Entity\TypesEquipe;
use App\Entity\CategoryMoyens;
use App\Controller\Admin\UserCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

/**
 * @IsGranted("ROLE_GESTION_EQ")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());
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
        if ($this->isGranted('ROLE_SUPER_ADMIN') && '...') {
            yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
            yield MenuItem::linkToCrud('Services', 'fas fa-industry', Services::class);
            yield MenuItem::linkToCrud('Categories', 'fas fa-puzzle-piece', CategoryMoyens::class);
            yield MenuItem::linkToCrud('Machines', 'fas fa-cogs', Moyens::class)->setPermission('ROLE_RESP_POLYM');
            yield MenuItem::linkToCrud('Teams', 'fas fa-users', NomEquipe::class);
            yield MenuItem::linkToCrud('Pace of work', 'fa fa-clock-o', TypesEquipe::class);
        }
        yield MenuItem::section('Administration Polym');
        if ($this->isGranted('ROLE_CE_POLYM') || $this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Demands', 'fas fa-list', Demandes::class)
            ->setDefaultSort(['Plannifie' => 'DESC']);
            yield MenuItem::linkToCrud('Teams', 'fas fa-users', NomEquipe::class);
            yield MenuItem::linkToCrud('Organization', 'fas fa-calendar', OrgaEq::class);
        }
        yield MenuItem::section('Administration Molding');
        if ($this->isGranted('ROLE_CE_MOULAGE') || $this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Teams', 'fas fa-users', NomEquipe::class);
            yield MenuItem::linkToCrud('Organization', 'fas fa-calendar', OrgaEq::class);
        }
        yield MenuItem::section('Retour Application');
        yield MenuItem::linkToUrl('APAMC', 'fa fa-tachometer','home');
    }
}
