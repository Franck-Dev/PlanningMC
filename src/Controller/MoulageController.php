<?php

namespace App\Controller;

use App\Entity\Moyens;
use App\Entity\Planning;
use App\Entity\Services;
use App\Entity\ConfSmenu;
use App\Entity\PolymReal;
use App\Entity\DatasMoulage;
use App\Services\FunctPlanning;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MoulageController extends AbstractController
{
    #[Route('/Moulage', name: 'Moulage')]
    public function index(FunctPlanning $plan, ManagerRegistry $manaReg): Response
    {
        //Création du menu pour gérer le controller avec données dans un tableau
        $tbMenu[0]=['Nom'=> 'DONNEES_MOULAGE', 'Description' => '', 'IdSmenu1' => 'Consultation_Donnees', 'IdSmenu2' => 'Creation_Donnees'];
        $tbMenu[1]=['Nom'=> 'CONTRAINTES', 'Description' => '', 'IdSmenu1' => 'Creation_Contraintes', 'IdSmenu2' => 'Consultation_Contraintes'];
        $tbMenu[2]=['Nom'=> 'OUTILLAGES', 'Description' => '', 'IdSmenu1' => 'Suivi', 'IdSmenu2' => 'Planification'];
        foreach ($tbMenu as $id => $varMenu) {
            $menu = new ConfSmenu();
            foreach ($varMenu as $key => $var) {
                $methodSet = 'set'.ucfirst($key);
                $menu->$methodSet($var);
            }
            $Titres[$id]=$menu;
        }
        //Récupération du service concerné par le planning
        $service = $_REQUEST['service'];
        //Recherche des moyens à afficher sur planning suivant service
        $repi=$manaReg->getRepository(Services::class);
        $teams=$repi->findOneBy(['Nom' => $service]);
        $repos=$manaReg->getRepository(Moyens::class);
        $moyens=$plan->moyens($repos,$teams->getId());
        $Ssmoyen= new JsonResponse($moyens[0]);
        $moyen= new JsonResponse($moyens[1]);
        $item= $moyens[2];

    //Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$manaReg->getRepository(Planning::class);
        $repi=$manaReg->getRepository(PolymReal::class);
        $task=$plan->planning($repo, $repos, $repi);
        $taches = new JsonResponse($task[0]);


        return $this->render('moulage/index.html.twig', [
            'service' => $service,
            'Titres' => $Titres,
            'Taches' => $taches->getcontent(),
            'Moyens' => $moyen->getcontent(),
            'Ssmoyen' => $Ssmoyen->getcontent(),
            'Items' => $item,
            'service' => $service
        ]);
    }

    #[Route('/moulage/Creation', name: 'Creation')]
    public function Creation(): Response
    {
        $Titres=[];
        return $this->render('moulage/Moulage.html.twig', [
            'Titres' => $Titres
        ]);
    }

    #[Route('/moulage/Consultation', name: 'Consultation')]
    public function Consultation(Request $Requet,EntityManagerInterface $manager,DatasMoulage $Moul=null): Response
    {
        $Titres=[];
        return $this->render('moulage/Moulage.html.twig', [
            'Titres' => $Titres
        ]);
    }

    #[Route('/moulage/Suivi/Outillage', name: 'Suivi')]
    public function Suivi(): Response
    {
        $Titres=[];
        return $this->render('moulage/suiviOut.html.twig', [
            'Titres' => $Titres
        ]);
    }

    #[Route('/moulage/Planification/TraitementOut', name: 'Planification')]
    public function Planification(): Response
    {
        $Titres=[];
        return $this->render('moulage/PlanifTraitOut.html.twig', [
            'Titres' => $Titres
        ]);
    }
}
