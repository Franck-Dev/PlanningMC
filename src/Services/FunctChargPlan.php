<?php

namespace App\Services;

use App\Repository\ChargeRepository;
use App\Repository\ArticlesRepository;
use App\Repository\ChargFigeRepository;
use App\Repository\OutillagesRepository;
use App\Repository\ProgMoyensRepository;

class FunctChargPlan
{
    // private $foo;
    // private $bar;
    // private $baz;
    // private $other;
    
    public function __construct(
        // Foo $foo,
        // Bar $bar,
        // Baz $baz,
        // Other $other
    )
    {
        // $this->foo = $foo;
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
    public function checkCTO(ChargFigeRepository $cata, ProgMoyensRepository $STD, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art, $Creno, $i, $TbPcSsOT, $m) {
        $Message=[];
        $datasPlanning=[];
        $listCTO=[];
        //On récupère l'ID du cycle en cours
        $IdProg=$STD->findOneBy(['Nom' => $Creno['Cycles']]);
        //On récupère les chargements figés du cycle en cours
        $ChargementsFiG=$cata->findBy(['Programme' => $IdProg, 'Statut' => 1]);
        $f=0;
        //dump($ChargementsFiG);
        $k=0;
        if ($ChargementsFiG){
            $listOTJour=[];
            //On va trier les CTO suivant les pièces présentent dans le CTJ
            $chargePcsJour=$repo->findBy(['NumProg' => $Creno['Cycles'], 'DateDeb'=>$Creno['Jour']]);
            //dump($chargePcsJour);
            $l=0;
            foreach ($chargePcsJour as $PcsJour) {
                //dump($PcsJour->getReferencePcs());
                $OT=$Out->myFindByPcs($PcsJour->getReferencePcs());
                if ($OT) {
                    $listOTJour[$l]=$OT;
                    $l++;
                } else {
                    if (in_array($PcsJour->getReferencePcs(),$TbPcSsOT)) {
                        
                    } else {
                        $TbPcSsOT[$m]=$PcsJour->getReferencePcs(); 
                        $m++;  
                    } 
                }
            }
            //On a une liste d'OT pour vérifier les CTO possibles
            if ($l>0) {
                //dump($listOTJour);
            } else {
                $Message='Les outillages ne sont pas définis pour ces pièces';
            }
            // On va sélectionner les CTO possible (Qui contiennent les pièces du jour)
            $ChargTecOptJour=$this->checkCTOOF($ChargementsFiG, $listOTJour, $Out);
            if ($ChargTecOptJour) {
                $listCTO=$this->checkOTCTO($ChargTecOptJour, $repo, $Out, $Art, $Creno, $i, $f);
            } else {

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
    private function checkOTCTO($ChargementsFiG, $repo, $Out, $Art, $Creno, $i, $f) {
        
        //dump($ChargementsFiG);
        //Pour chaque cycle, on vient rechercher les CTO possible
        $TbCTJ[$i]=$repo -> findBy(['DateDeb' => $Creno['Jour'],'NumProg' => $Creno['Cycles']]);
        //dump($TbCTJ);  
        $q=0;         
        //On  sélectionne les chargements figés en fonction du nombre de pièces
        foreach($ChargementsFiG as $ChargeFiG){
            //Pour chaque chargement figé on récupère sa composition en outillages
            $listeOT = $Out->myFindByCharFiG($ChargeFiG->getCode());
            if ($listeOT) {
                //Doit remonter la liste de pcs(OF) contenu dans le chargement
                $test=$this->checkOTOF($listeOT, $TbCTJ, $repo, $Out, $Art, $Creno, $i, $f);
            }
            //On va tester le remplissage
            $Remp=(sizeof($test)/sizeof($listeOT))*100;
            $TbDatasCTO[$q]=['Nom'=>$ChargeFiG->getCode(), 'Contenu'=>$test, 'Remplissage'=>$Remp];
            $q++;
        }
        //dump($TbDatasCTO);
        return $TbDatasCTO;
    }
    
    /**
     * checkOTOF On chercher dans la liste des OT du CTO les OF correpondants dans la charge CTJ
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
    private function checkOTOF($listeOT, $TableCTJ, $repo, $Out, $Art, $Creno, $i, $f) {

    $Horizon=10;
    //On va chercher les OT dans la CTJ pour chacun des CTO
        //On va commencer par regarder pour chaque OT de mon CTO si un OF existe dans mon CTJ
        $p=0;
        foreach ($listeOT as $OTCTO) {
            $ArtOFCTJ=$Art->myFindByOT($OTCTO->getRef());
            // Cas des OT multi empreintes
            //dump($ArtOFCTJ);
            if (sizeof($ArtOFCTJ)>1) {
                //dump('Attention multi empreintes à traiter');
            } elseif (sizeof($ArtOFCTJ)==1) {
                $TbDatasArt[$p]=$this->checkOFCharge($ArtOFCTJ, $Creno, $Horizon, $repo);
                //dump($TbDatasArt);
            }
            $p++;
            // if ($this->inArrayObject($ArtOFCTJ,$TableCTJ[$i]) == true) {
            //     //Si un OF existe on l'integre en charge dans le CTO
            //     dump('MArche???');
            // } else {
            //     //Sinon on regarde sur l'horizon s'il existe un OF pour cet OT

            // }
        }
        //On récupère le nombre d'outillages
        $nbOT=sizeof($listeOT);
        //On cherche les OT correspondants aux articles du $creno(TableCTJ[$i])
        $r=0;
        $TabOTArtOFOP=[];
        foreach($TableCTJ[$i] as $OFJ){
            //Pour chaque OF on va récupérer l'OT correspondant à l'article de l'OF
            //dump($OFJ->getReferencePcs());
            $Outill= $Out->myFindByPcs($OFJ->getReferencePcs()); 
            //dump($Outill);
            if(sizeof($Outill)>1){
                $ArtMultiIndus=True;
            }
            elseif(sizeof($Outill)==0){
                //Si pas d'outillage correspondant à l'article, on sort de la boucle
                //dump('Aucun outillage trouvé');
                break;
            }
            //Suivant le nb d'empreinte on recherche les autres pièces si besoin
            if($Outill[0]->getNbEmpreinte()===1){
            //L'OT n'a qu'une seule empreinte, on peut créé le couple OT/Article/OF/OP des articles de CTJ
                $TabOTArtOFOP[$f][$r][0]=$Outill[0]->getRef();
                $TabOTArtOFOP[$f][$r][1]=$OFJ->getReferencePcs();
                $TabOTArtOFOP[$f][$r][2]=$OFJ->getOrdreFab();
                $TabOTArtOFOP[$f][$r][3]=$OFJ->getPosteW();
                $r=$r+1;
            }
            //Sinon on va regarder si les ref des autres empreintes sont dans CTJ
            else{
                $NbE=$Outill->getNbEmpreinte();
                $TabOTMultiEmp=[$NbE];  //Tableau regroupant tous les articles de l'outillage
                //dump($TabOTMultiEmp);
                foreach($TableCTJ[$i] as $OFJOT){
                    if($OFJ->getReferencePcs()==$OFJOT->getReferencePcs()){
                        //C'est le même article, donc on ne le prend pas sauf si plusieurs indus
                        if($Outill->getNbIndus()>1){
                            //Article avec plusieurs indus, donc possibilité d'avoir le même article dans le chargement
                            //On vérifie que celà ne soit pas le même OFOP
                            if($OFJ->getOF()==$OFJOT->getOF()){
                                //Si même OF on ne prend pas l'article
                            }
                            else{
                                //Si OF différent on prend l'article sur la deuxième indus

                            }
                        }
                        else{
                            //Une seule indus donc on prend pas l'article
                        }
                    }
                    else{
                        //C'est un autre article on regarde si l'OT correspond au premier
                        
                    }
                }
                //dump($TabOTArtOFOP);   
                
            }
        }
        //dump($TabOTArtOFOP);
        //On vérifie si les chargements figés sont adéquate pour ce $creno(charge en nb de pièce de la journée)
        foreach($TabOTArtOFOP as $ChargPolym){
            //dump($ChargPolym);
            if(sizeof($ChargPolym)===$Creno['NbrPcs']){
                Dump('Chargement figé OK');

            }
            //Si manque de pièce soit on passe et on va voir plus loin si plus de pièce, soit on vérifie si le chargement >%obj de remplissage
            else{
                //Il faut trouver les OT manquants dans le chargement figé
                
                //En premier on vérifie le % de remplissage par le nombre d'OT si < on passe à l'option ajout de la charge des jours suivants
                $Remp=(sizeof($ChargPolym)/$nbOT)*100;
                if($Remp>75){
                    //On vérifie si un chargement amont n'a pas été validé à <50%
                    Dump('Chargement figé NOK car remplissage à '.$Remp.'%');

                }
                else{
                    //On va essayer en tirant la charge des jours suivants(7 jours)
                    //Dump($Creno);
                    $NJourD=clone $Creno['Jour'];
                    $jourcyle=$NJourD->modify('+1 day');
                    $NJourF= clone $Creno['Jour'];
                    $jour3cycle=$NJourF->modify('+7 day');
                    $Cyc=$Creno['Cycles'];
                    //Dump($jourcyle);
                    //Dump($jour3cycle);
                    //Récupération de tous les OF suivant nouvelles dates et cycle en cours
                    $ChargePart=$repo -> findReparChargeWCycle($jourcyle,$jour3cycle,$Cyc);
                    //dump($ChargePart);
                    $j=0;
                    //On va chercher les articles manquants au chargement figé pour complétude
                    foreach($ChargePart as $CPart){
                        //Pour chaque OF en avance de charge
                        $TabCharPart[$j]=$repo -> findBy(['DateDeb' => $CPart['Jour'],'NumProg' => $Creno['Cycles']]);
                        //dump($TabCharPart);
                        //On va récupérer le n° OT de chaque article
                        foreach($TabCharPart[$j] as $OFBis ){
                            //dump($OFBis->getReferencePcs());
                            $OutBis= $Out->myFindByPcs($OFBis->getReferencePcs());
                            //dump($OutBis);
                            //On comparer pour trouver les outillages manquants du chargement figé
                            
                        }
                        $j=$j+1;
                        
                    }
                }
            }
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

        for ($ix=0; $ix < $Horizon; $ix++) { 
            $dateInit=clone($Creno['Jour']);
            $DureCycH="+".$ix."days";
            $newDateFin=date_modify($dateInit, $DureCycH);
            //dump($newDateFin);
            // $chargeTotJour=$repo -> findReparChargeWCycle($newDateDeb,$newDateFin,$Creno['Cycles']);
            $chargeTotJour=$repo ->findBy(['DateDeb'=>$newDateFin, 'NumProg'=>$Creno['Cycles']]);
            //dump($chargeTotJour);
            $datasOF=[];
            foreach ($chargeTotJour as $ArtCTJ) {
                if ($ArtOT[0]->getReference() == $ArtCTJ->getReferencePcs()) {
                    $datasOF=["OF" => $ArtCTJ->getOrdreFab(), "Designation" => $ArtCTJ->getDesignationPCS(), "Horizon"=> $ix];
                break 2;
                }
            }
        }           
        return $datasOF;
    }
    
    /**
     * checkCTOOF Vérifie si au moins OT de la Charge du jour est dans le CTO
     *
     * @param  mixed $ChargementsFiG
     * @param  mixed $listOTJour
     * @param  mixed $Out
     * @return mixed
     */
    private function checkCTOOF($ChargementsFiG, $listOTJour, $Out) {
        $o=0;
        $listCTOVal=[];
        foreach($ChargementsFiG as $CTOOT) {
            $listeOT = $Out->myFindByCharFiG($CTOOT->getCode());
            $n=0;
            $TbListeOT=[];
            foreach($listeOT as $OT) {
                $TbListeOT[$n]=$OT->getRef();
                $n++;
            }
            foreach($listOTJour as $OTJour) {
                if (in_array($OTJour[0]->getRef(), $TbListeOT)) {
                    $listCTOVal[$o]=$CTOOT;
                break;
                }
            }
            $o++;
        }
        return $listCTOVal;
    }
}