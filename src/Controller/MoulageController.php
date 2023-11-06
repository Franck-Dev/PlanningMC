<?php

namespace App\Controller;

use App\Entity\ConfSmenu;
use App\Entity\DatasMoulage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MoulageController extends AbstractController
{
    #[Route('/Moulage', name: 'Moulage')]
    public function index(): Response
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
            dump($Titres);
        }

        return $this->render('moulage/index.html.twig', [
            'controller_name' => 'MoulageController',
            'Titres' => $Titres
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
