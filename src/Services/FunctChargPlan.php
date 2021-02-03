<?php

namespace App\Services;

use App\Repository\ChargeRepository;
use App\Repository\ArticlesRepository;
use App\Repository\ChargementRepository;
use App\Repository\ChargFigeRepository;
use App\Repository\OutillagesRepository;
use App\Repository\ProgMoyensRepository;
use phpDocumentor\Reflection\Types\Boolean;

class FunctChargPlan
{
    public $nbMessErr;
    // private $bar;
    // private $baz;
    // private $other;
    
    public function __construct(
        //nbMessErr $nbMessErr,
        // Bar $bar,
        // Baz $baz,
        // Other $other
    )
    {
        //$this->nbMessErr = $nbMessErr;
        // $this->bar = $bar;
        // $this->baz = $baz;
        // $this->other = $other;
    }
    
    /**
     * checkCTO va charger une liste de CTO suivant le cycle de la charge en cours
     *
     * @param  mixed $cata
     * @param  mixed $STD
     * @param  mixed $repo
     * @param  mixed $Out
     * @param  mixed $Creno
     * @param  int $i
     * @return void
     * Retourne un array des CTO chargés des OF
     */
    public function checkCTO(ChargementRepository $chargmnt, ChargFigeRepository $cata, ProgMoyensRepository $STD, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art, $Creno, $TbPcSsOT, $m) {
        $Message=[];
        $datasPlanning=[];
        $listCTO=[];
        //On récupère l'ID du cycle en cours
        $IdProg=$STD->findOneBy(['Nom' => $Creno['Cycles']]);
        //On récupère les chargements figés du cycle en cours, si le PRG<>""
        if ($IdProg) {
            $ChargementsFiG=$cata->findBy(['Programme' => $IdProg->getid(), 'Statut' => 1]);
        } else {
            $datasPlanning=['PlanningSAP'=> $Creno,'CTO'=>"" ,'PcSsOT' => "", 'Messages'=>"Les OF n\'ont pas de programme"];
            return $datasPlanning;
        }
        if ($ChargementsFiG){
            dump($ChargementsFiG);
            $listOTJour=[];
            //On va trier les CTO suivant les pièces présentent dans le CTJ
            $chargePcsJour=$repo->findBy(['NumProg' => $Creno['Cycles'], 'DateDeb'=>$Creno['Jour']]);
            //dump($chargePcsJour);
            $l=0;
            foreach ($chargePcsJour as $PcsJour) {
                //On cherche l'OT correspondant à la pièce
                $OT=$Out->myFindByPcs($PcsJour->getReferencePcs());
                //Si on trouve un OT on le sauvegarde dans la liste
                if ($OT) {
                    $listOTJour[$l]=$OT;
                    $l++;
                } else {
                    //Sinon on l'enregistre dans une liste d'erreur de pièces sans outillages
                    if (in_array($PcsJour->getReferencePcs(),$TbPcSsOT)) {
                        
                    } else {
                        $TbPcSsOT[$m]=$PcsJour->getReferencePcs(); 
                        $m++;  
                    } 
                }
            }
            //On a une liste d'OT pour vérifier les CTO possibles
            if ($l==0) {
                $Message='Les outillages ne sont pas définis pour ces pièces';
                //return $datasPlanning=['PlanningSAP'=> '','CTO'=> '' ,'PcSsOT' => $TbPcSsOT, 'Messages'=>$Message];
            } else {
                // On va sélectionner les CTO possible (Qui contiennent les pièces du jour)
                dump('liste des OT de la Charge du Jour');
                dump($listOTJour);
                $ChargTecOptJour=$this->checkCTOOF($ChargementsFiG, $listOTJour, $Out, $chargmnt, $Creno['Jour']);
            //Si il y a au moins 1 CTO OK, je vais chercher les OT
                if ($ChargTecOptJour) {
                    dump('liste des CTO valides');
                    dump($ChargTecOptJour);
                    $listCTO=$this->checkOTCTO($ChargTecOptJour, $repo, $Out, $Art, $Creno);
                    dump('Liste des CTO chargés');
                    dump($listCTO);
                } else {
                    $Message='Aucunes des pièces du jour est dans le Chargement Technique.';
                } 
            } 
        } else {
            $Message='Pas de chargement figé pour le programme : '.$Creno['Cycles'];
        }
        $datasPlanning=['PlanningSAP'=> $Creno,'CTO'=>$listCTO ,'PcSsOT' => $TbPcSsOT, 'Messages'=>$Message];
        //dump($datasPlanning);
        return $datasPlanning;
    }
    
    /**
     * checkOTCTO va chercher les OT qui composent le Chargement Technique Optimisé
     *
     * @param  mixed $ChargementsFiG
     * @param  mixed $repo
     * @param  mixed $Out
     * @param  mixed $Creno
     * @param  int $i
     * @return void
     * Retourne un array comportant la liste des OTs du CTO
     */
    private function checkOTCTO($ChargementsFiG, $repo, $Out, $Art, $Creno) {
         
        $q=0;         
        //On  va récupérer les OT composants chaque CTO
        foreach($ChargementsFiG as $ChargeFiG){
            //Pour chaque chargement figé on récupère sa composition en outillages
            $listeOT = $Out->myFindByCharFiG($ChargeFiG->getCode());
            if ($listeOT) {
                dump('Liste des OT du CTO');
                dump($listeOT);
                //Doit remonter la liste de pcs(OF) contenu dans le chargement
                $test=$this->checkOTOF($listeOT, $repo, $Art, $Creno);
                dump('Liste des OF du CTO');
                dump($test);
            } else {
                dump('Pas de liste OT correspondant au CTO');
            }
            //On va tester le remplissage (A REVOIR CALCUL EN 2 INDICATEURS)
            $Remp[0]=(round((sizeof($test)/sizeof($listeOT))*100,0));
            $Remp[1]=$ChargeFiG->getPourc();
            $TbDatasCTO[$q]=['Nom'=>$ChargeFiG->getCode(), 'Contenu'=>$test, 'Remplissage'=>$Remp, 'Plannif'=>False];
            $q++;
        }
        //dump($TbDatasCTO);
        return $TbDatasCTO;
    }
    
    /**
     * checkOTOF On va chercher dans la liste des OT du CTO les OF correpondants dans la charge CTJ
     *
     * @param  mixed $listeOT
     * @param  mixed $TableCTJ
     * @param  mixed $repo
     * @param  mixed $Out
     * @param  mixed $Art
     * @param  mixed $Creno
     * @param  int $i
     * @param  int $f
     * @return void
     */
    private function checkOTOF($listeOT, $repo, $Art, $Creno) {

    $Horizon=10;
    //On va chercher les OT dans la CTJ pour chacun des CTO
        //On va commencer par regarder pour chaque OT de mon CTO si un OF existe dans mon CTJ
        $p=0;
        $TbDatasArt=[];
        foreach ($listeOT as $OTCTO) {
            //On cherche les articles liés à chaque OT
            $ArtOFCTJ=$Art->myFindByOT($OTCTO->getRef());
            // Cas des OT multi empreintes
            dump('Liste des articles composant l\'OT'.$OTCTO->getRef());
            dump($ArtOFCTJ);
            if (sizeof($ArtOFCTJ)>1) {
                dump('Attention multi empreintes à traiter');
                $retourDatas=$this->checkOFChargMultiEmp($ArtOFCTJ, $Creno, $Horizon, $repo, $Art);
            } elseif (sizeof($ArtOFCTJ)==1) {
                //Il y a une seule pièce sur l'outillage
                $retourDatas[0]=$this->checkOFCharge($ArtOFCTJ[0], $Creno, $Horizon, $repo);
                //dump($TbDatasArt);
            }
            if (empty($retourDatas)==false) {
                $TbDatasArt[$OTCTO->getRef()]=$retourDatas;
                $p++;
            }
            $retourDatas=[];
            dump('Données à retourner correspondant à la charge et à l\'OT');
            dump($TbDatasArt);
        }
        return $TbDatasArt;
    }
    
    /**
     * inArrayObject est une fonction qui me permet de savoir si une propriété d'un object est
     * dans un tableau d'object
     *
     * @param  mixed $Obj
     * @param  array $TbObj
     * @param  string $Prop
     * @return boolean
     */
    public function inArrayObject($Obj, $Prop, $TbObj) {
        foreach ($TbObj as $tbo) {
            $attrr="get".$Prop."()";
            if ($Obj == $tbo->$attrr) {
                return true;
            } else {
              $Trouv=False;
            }
        }
        if ($Trouv == false) {
            return False;
        }
    }
    
    /**
     * checkOFCharge On cherche le(s) OF correspondant(s) à l'outillage du CTO
     *
     * @param  mixed $ArtOT
     * @param  mixed $Creno
     * @param  mixed $Horizon
     * @param  mixed $repo
     * @return void
     */
    private function checkOFCharge($ArtOT, $Creno, $Horizon, $repo) {

        $i=0;
        //Vérification si présence d'un OF dans le passé
        $OFPast=$repo->myFindOFToPast($ArtOT->getReference(), clone($Creno['Jour']), $Creno['Cycles']);
        dump($OFPast);
        if ($OFPast) {
            //Récupération de l'article trouvé
            $deltaJours=date_diff(clone($Creno['Jour']), $OFPast[0]['DateDeb']);
                $datasOF=["OF" => $OFPast[0]['OF'], "Designation" => $OFPast[0]['Designation'], "Horizon"=> $deltaJours->format('%R%a')];
                $refOK=true;
            } else {
            //On va chercher dans l'horizon si on trouve des OF correspondant au cycle en cours
            for ($ix=0; $ix < $Horizon+1; $ix++) {
                $refOK=False; 
                $dateInit=clone($Creno['Jour']);
                $DureCycH="+".$ix."days";
                $newDateFin=date_modify($dateInit, $DureCycH);
                $chargeTotJour=$repo ->findBy(['DateDeb'=>$newDateFin, 'NumProg'=>$Creno['Cycles'], 'Statut'=> 'OUV']);
                $datasOF=[];
                //Dans les OF trouvés, vérification si un OF de la ref existe
                foreach ($chargeTotJour as $ArtCTJ) {
                    if ($ArtOT->getReference() == $ArtCTJ->getReferencePcs()) {
                        $datasOF=["OF" => $ArtCTJ->getOrdreFab(), "Designation" => $ArtCTJ->getDesignationPCS(), "Horizon"=> $ix];
                        $refOK=true;
                        dump('On a trouvé la référence');
                        dump($datasOF);
                        break 2;
                    }
                }
            }
        }

        //Si on a pas trouvé, on cherche dans la totalité de la charge pour compléter le chargement 
        if ($refOK==false) {
            $OFArtManq=$repo->findOneBy(['ReferencePcs'=>$ArtOT->getReference(), 'NumProg'=>$Creno['Cycles'], 'Statut'=> 'OUV']);
            dump($OFArtManq);
            if ($OFArtManq) {
                $deltaJours=date_diff($dateInit, $OFArtManq->getDateDeb());
                $datasOF=["OF" => $OFArtManq->getOrdreFab(), "Designation" => $OFArtManq->getDesignationPCS(), "Horizon"=> $deltaJours->format('%R%a')];
            } else {
                $Message[$i]='Manque la référence '.$ArtOT->getReference(). ', '.$ArtOT->getDesignation();
                dump($Message);
                $i++;
            }
        }          
        return $datasOF;
    }
    
    /**
     * checkOFChargMultiEmp Permet de traiter le cas des multi empreintes en passant chacunes des ref liées
     *
     * @param  mixed $ArtOT
     * @param  mixed $Creno
     * @param  mixed $Horizon
     * @param  mixed $repo
     * @return void
     */
    private function checkOFChargMultiEmp($ArtOT, $Creno, $Horizon, $repo, $Art) {
        
        $r=0;
        $retourDatas=[];
        foreach ($ArtOT as $RefOT){
            // Il faut vérifier si l'article correspond à la polym
            $datasArtOT=$Art->findBy(['Reference'=>$RefOT->getReference()]);
            // Si ok, recherche d'un OF dans la charge
            $ChargEmp=$this->checkOFCharge($RefOT, $Creno, $Horizon, $repo);
            if (empty($ChargEmp)===false) {
                $retourDatas[$r]=$ChargEmp;
                $r++;
            }
        }
        return $retourDatas;
    }
    
    /**
     * checkCTOOF Vérifie si au moins un OT de la Charge du jour est dans le CTO
     *
     * @param  mixed $ChargementsFiG
     * @param  mixed $listOTJour
     * @param  mixed $Out
     * @return mixed
     */
    private function checkCTOOF($ChargementsFiG, $listOTJour, $Out, $chargmnt, $dateCharge) {
        $o=0;
        $listCTOVal=[];
        foreach($ChargementsFiG as $CTOOT) {
            //Avant tout, je vérifie si la chargement n'a pas déjà été créé en aval sur x jours($cycle a déterminer)
            $cycle=4;
            $dateNew=clone($dateCharge);
            $CTODispo=$this->checkCTODispo($CTOOT, $chargmnt, $dateNew, $cycle);
            if (!$CTODispo) {
                //Si Ok, je récupère les OT de ce CTO
                $listeOT = $Out->myFindByCharFiG($CTOOT->getCode());
                //dump($listeOT);
                $TbListeOT=[];
                $TbListeOT=$this->tboDatasCTO($listeOT);
                //dump($TbListeOT);
                //dump($listOTJour);
                foreach($listOTJour as $OTJour) {
                    if (in_array($OTJour[0]->getRef(), $TbListeOT)) {
                        $listCTOVal[$o]=$CTOOT;
                    break;
                    }
                }
                $o++;
            } else {
                
            }
            
        }
        return $listCTOVal;
    }
    
    /**
     * tboDatasCTO Permet de constituer la liste des OT d'un CTO
     *
     * @param  array $listeOT
     * @return array
     */
    public function tboDatasCTO($listeOT) {

        $n=0;
            $TbListeOT=[];
            foreach($listeOT as $OT) {
                $TbListeOT[$n]=$OT->getRef();
                $n++;
            }
        return $TbListeOT;
    }

    private function checkCTODispo($CTO, $chargmnt, $dateCharger, $cycle) {
        //On récupère les dates à vérifier
        $dateAmont=clone($dateCharger);
        $interv= "-".$cycle."days";
        $dateAval=date_modify($dateAmont, $interv);
        // //Dans ce créneau de date, a-t-il déjà été validé
        $dispo=$chargmnt->myFindByDispo($dateAval,$dateCharger,$CTO->getCode());
        if ($dispo) {
            return true;
        } else {
            return false;
        }
    }
}