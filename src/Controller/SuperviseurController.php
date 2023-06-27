<?php

namespace App\Controller;

use DateTime;
use App\Entity\Moyens;
use App\Entity\Moulage;
use App\Entity\Planning;
use App\Entity\Services;
use App\Entity\PolymReal;
use App\Services\FunctPlanning;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperviseurController extends AbstractController
{
     /**
     * @Route("/{service}/superviseur", name="superviseur")
     */
    public function superviseur($service, FunctPlanning $plan, ManagerRegistry $manaReg)
    {
        //Affichage de la charge plannifiée par étiquette kanban suivant catégories de W
        //Gestion des dates du jour concernée
        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('-1 day');
        $currentMonthDateTime = new DateTime();
        $lastDateTime = $currentMonthDateTime->modify('+1 day');
    //Recherche si demande d'annulation de polym
        $repo=$manaReg->getRepository(Planning::class);

    //Recherche des moyens à afficher sur échéancier suivant service
        $repi=$manaReg->getRepository(Services::class);
        $teams=$repi->findOneBy(['Nom' => $service]);
        $repos=$manaReg->getRepository(Moyens::class);
        $moyens=$repos->findBy(['Id_Service' => $teams->getId(), 'Activitees' => 'Plannifie']);
        foreach ($moyens as $key => $value) {
            $tbMoyens[$key]=$value->getLibelle();
        }
        //Choix de la base des encours suivant le service
        if ($service==='MOULAGE') {
            $repi=$manaReg->getRepository(Moulage::class);
        } elseif($service==='MOYENS CHAUD') {
            $repi=$manaReg->getRepository(PolymReal::class);
        } else{

        }

        //Chargement d'une variable par tâche plannifiée, encours, annulée et terminée
        $taskPla=$repo->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'PLANNIFIE');
        $taskEC=$repi->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'LANCER');
        $taskANul=$repo->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'ANNULE');
        $taskTER=$repi->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'TERMINE');

        //dump($taskEC);

        return $this->render('superviseur/ChargeJour.html.twig',[
            'service' => $service,
            'tachesPLA'=> $taskPla,
            'tachesEC'=> $taskEC,
            'tachesANUL'=> $taskANul,
            'tachesTER'=> $taskTER,
            'moyens'=> $moyens
        ]);
        //return new JsonResponse(['Taches'=> '$task[0]', 'moyen'=> '$moyens[1]', 'Ssmoyen'=> '$moyens[0]'],200);
    }

}
