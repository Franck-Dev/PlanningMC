<?php

namespace App\Controller;

use DateTime;
use App\Entity\Moyens;
use App\Entity\Moulage;
use App\Entity\Demandes;
use App\Entity\Planning;
use App\Entity\Services;
use App\Entity\ConfSmenu;
use App\Entity\PolymReal;
use App\Entity\Outillages;
use App\Entity\IndicHeader;
use App\Services\FunctPlanning;
use App\Services\CallApiService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperviseurController extends AbstractController
{
     /**
     * @Route("/{service}/Agenda", name="Agenda")
     */
    public function agenda($service, ManagerRegistry $manaReg)
    {
        //Vérification si on rend la page entière ou pas
        if (strpos($_SERVER['REDIRECT_URL'],'Agenda') === false) {
            $Titres=[];
        } else {
            //Titres pour le menu
            $repo=$manaReg->getRepository(ConfSmenu::class);
            $Titres=$repo->findAll();
        }

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
        $taskPla=$repo->findChargeStatut(new \DateTime(), $lastDateTime, $tbMoyens, 'PLANNIFIE');
        $taskEC=$repi->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'LANCER');
        $taskANul=$repo->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'ANNULE');
        $taskTER=$repi->findChargeStatut($firstDateTime, $lastDateTime, $tbMoyens, 'TERMINE');

        dump($taskPla);
        //Traitement du cas des retard qui sont en statut PLANNIFIE
        $taskRetard=$repo->findChargeStatut($firstDateTime, new \DateTime(), $tbMoyens, 'PLANNIFIE');
        //Modification du statut juste pour échéancier
        foreach ($taskRetard as $key => $item) {
            $item->getStatut('RETARD');
        }
        //Fusion des tableaux Annulé et Retard pour n'avoir qu'une seule variable
        $taskAnuRet=array_merge($taskRetard,$taskANul);

        //Calcul du nombre de pièces pour indicateurHeader
        $nbPoly['NbrRef']=count($taskRetard)+count($taskPla)+count($taskEC)+count($taskTER);
        $indic2['Nom']='test';
        $indic2['Valeur']=70;

        //Concatenation de tous les tableaux en un seul par type de statut
        $taskTotal['RETARD/ANNULE']=$taskAnuRet;
        $taskTotal['A FAIRE']=$taskPla;
        $taskTotal['EN COURS']=$taskEC;
        $taskTotal['TERMINE']=$taskTER;
        //dump($taskTotal);

        return $this->render('superviseur/ChargeJour.html.twig',[
            'service' => $service,
            'taches' => $taskTotal,
            'Titres' => $Titres,
            'ChargeMois' => $nbPoly,
            'indic2' => $indic2,
            'moyens'=> $moyens
        ]);
    }

     /**
     * @Route("/{service}/Superviseur", name="Superviseur")
     */
    public function Superviseur($service, CallApiService $api, UserInterface $user=null,
    PaginatorInterface $paginator, Request $request, ManagerRegistry $manaReg)
    {
        if ($user) {
            //Vérification si on rend la page entière ou pas
            if (strpos($_SERVER['REDIRECT_URL'],'Superviseur') === false) {
                $Titres=[];
            } else {
                //Titres pour le menu
                $repo=$manaReg->getRepository(ConfSmenu::class);
                $Titres=$repo->findAll();
            }
            
            //Récupération des kits tissus découpés liés au programme avion de l'utilisateur
            $Kits=$api->getDatasAPI('/api/datas_kits?status=false','tracakit',[],'GET',);
            $listKits=$paginator->paginate($Kits, $request->query->getInt('page',1),10);

            //Récupération des outillages liés au programme avion de l'utilisateur
            $repo=$manaReg->getRepository(Outillages::class);
            $OTs=$repo->findAll();
            $listOTs=$paginator->paginate($OTs, $request->query->getInt('page',1),20);

            //Créer une liste filtrer par nbPolym
            //Création du tableau de recherche
            if ($user->getProgrammeAvion()) {
                dump($user->getProgrammeAvion());
                foreach ($user->getProgrammeAvion() as $key=>$avion) {
                    $listAvions[$key]=$avion;
                }
            } else {
                $listAvions=[];
            }
            $OTparNbPolym=$repo->myFindByAvion($listAvions);

            //Récupération des moyens du service
            $repi=$manaReg->getRepository(Services::class);
            $teams=$repi->findOneBy(['Nom' => $service]);
            $repo=$manaReg->getRepository(Moyens::class);
            $moyens=$repo->findBy(['Id_Service' => $teams->getId()]);
            $listMoyens=$paginator->paginate($moyens, $request->query->getInt('page',1),20);
        }else{
            //Initialisation des variables pour user non connecté
            $Titres=[];
            $listOTs=$paginator->paginate([], $request->query->getInt('page',1),10);
            $OTparNbPolym=[];
            $listMoyens=$paginator->paginate([], $request->query->getInt('page',1),10);
            $listKits=$paginator->paginate([], $request->query->getInt('page',1),10);
        }
            return $this->render('superviseur/TableauBordActivites.html.twig',[
                'service' => $service,
                'Titres' => $Titres,
                'listOTs' => $listOTs,
                'suiviOT' => $OTparNbPolym,
                'moyens' => $listMoyens,
                'ChargeMois' => ['NbrRef' => 0],
                'listKits' => $listKits
            ]);
    }
}
