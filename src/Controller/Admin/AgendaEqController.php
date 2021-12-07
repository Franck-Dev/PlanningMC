<?php

namespace App\Controller\Admin;

use App\Repository\AgendaRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AgendaEqController extends AbstractController
{
    private $agendaRepository;
    
    public function __construct(AgendaRepository $agendaRepository)
    {
        $this->agendaRepository=$agendaRepository;
    }

    /**
     * @Route("/admin/agenda", name="admin_agenda")
     */
    public function index()
    {
        $data=$this->agendaRepository->findBy(['NomWOrga.NomEquipe.Manager'=>$this->getUser()]);
        return $this->render('admin/pages/my_index.html.twig', [
            'data' => $data,
        ]);
    }
}
