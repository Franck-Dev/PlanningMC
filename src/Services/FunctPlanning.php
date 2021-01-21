<?php

namespace App\Services;

use App\Repository\MoyensRepository;
use App\Repository\PlanningRepository;
use App\Repository\PolymRealRepository;

class FunctPlanning
{
    public function planning(PlanningRepository $repo, MoyensRepository $repos, PolymRealRepository $repi, $params=null) {
        if ($params) {
            $Taches=$repo -> findBy(['Statut'=>$params]);
        } else {
            $Taches=$repo -> findAll();
        }
        $data = [];
        $i = 0;
        //On créé le pourcentage de volumetrie en test, a changer par le réel avec nb outillage
        $Pourc=10;
        $dateInit=new \DateTime;
        $DureCycH="-21days";
        $newDateFin=date_modify($dateInit, $DureCycH);
        
        foreach($Taches as $tache){
            if ($tache->getDebutDate() > $newDateFin) {
                //On construit l'info bulle(tooltip) avec certaines données de la polym plannifiée
                $commentaires=nl2br("Demande n° ". $tache->getNumDemande()->getId()." / ID Plannif : ". $tache->getId()."\n" ."Départ: ". ($tache->getDebutDate())->format('G:i') . "\n" .$tache->getNumDemande()->getCommentaires()."\n".$tache->getNumDemande()->getOutillages() ."\n" . "Fin à : " . ($tache->getFinDate())->format('G:i'));
                //On cherche le moyen attribué à la polym suivant la demande et l'activité Plannification
                $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification(),'Activitees'=> 'Plannifie']);
                if($Pourc<75){
                    $data[$i] = ['id'=> '1'.$tache->getId(),'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires,'visibleFrameTemplate' => '<div class="progress-wrapper"><div class="progress" style="width:'.$Pourc.'%; background:red"></div><label class="progress-label">'.$Pourc.'%<label></div>'];
                }
                else{
                    $data[$i] = ['id'=> '1'.$tache->getId(),'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires, 'visibleFrameTemplate' => '<div class="progress-wrapper"><div class="progress" style="width:'.$Pourc.'%"></div><label class="progress-label">'.$Pourc.'%<label></div>'];
                }
                
                $i = $i + 1;
                //dump($MoyUtil=$repos -> )findBy(['Libelle' => $tache->getIdentification()]));
                //dump($MoyUtil[0]->getId());
                //dump($Taches);
                //dump($tache->getNumDemande()->getCycle()->getCouleur());
            }
        }
        //Implémentation dans la variable des polyms créées
        $Polyms=$repi -> findAll();
        //dump($Polyms);
        foreach($Polyms as $polym){ 
            if ($polym->getDebPolym() > $newDateFin) {
                if(!$polym->getPourcVolCharge()){
                    $Pourc==10;
                }               
                else{
                    $Pourc=$polym->getPourcVolCharge();
                }    
                if($Pourc<75){
                    $data[$i] = ['id'=>  '2'.$polym->getId(),'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym(),'visibleFrameTemplate' => '<div class="progress-wrapper"><div class="progress" style="width:'.$Pourc.'%; background:red"></div><label class="progress-label">'.$Pourc.'%<label></div>'];
                }
                else{
                    $data[$i] = ['id'=>  '2'.$polym->getId(),'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym(),'visibleFrameTemplate' => '<div class="progress-wrapper"><div class="progress" style="width:'.$Pourc.'%"></div><label class="progress-label">'.$Pourc.'%<label></div>'];
                }
                $i = $i + 1;
            }
        }
        //$taches= new JsonResponse($data);
        return $result=[$data];
    }

    public function moyens(MoyensRepository $repos) {
        //Recherche des moyens à afficher sur planning
        $moyens=$repos -> findAllMoyensSvtService ( intval('8'), intval('1') );
        $item=$moyens;   
        $data = [];
        $TbEtat=[];
        $i = 0;
        $a=0;
        foreach($moyens as $moyen){
            // Si le moyen à 2 sous fonctions (ex: "plannifié et réalisé")
            if ($moyen['SousTitres']==2){
                $Etats=$repos -> findBy(['Libelle' => $moyen['Moyen']]);
                // On rajoute les notions d'activitees au moyen pour créer 2 lignes sur planning
                foreach($Etats as $etat){
                    if ($etat->getActivitees() == 'Plannifie') {
                        $MoyPla = $etat->getId();
                    } else {
                        $TbEtat[$a]=['id'=>$etat->getId(), 'content'=>$etat->getActivitees()];
                         $a=$a+1;
                    }
                }
                $data[$i] = ['id'=> $MoyPla, 'style'=>  "color:white;", 'content'=> $moyen['Moyen'], 'className'=> 'gris', 'nestedGroups' => [$TbEtat[$a-1]['id']]];
            }
            else{
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen']];
            }
            $i = $i + 1;
			//On affecte un élément $item à $data
        }
        return $result=[$TbEtat, $data, $item];
        // $Ssmoyen= new JsonResponse($TbEtat);
        // $moyen= new JsonResponse($data);
    }
}