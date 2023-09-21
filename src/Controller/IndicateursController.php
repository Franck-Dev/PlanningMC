<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Charge;
use App\Entity\Moyens;
use App\Entity\Planning;
use App\Entity\PolymReal;
use App\Services\FunctIndic;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndicateursController extends AbstractController
{
    /**
     * @Route("/indicateurs", name="indicateurs")
     */
    public function index()
    {
        return $this->render('indicateurs/index.html.twig', [
            'controller_name' => 'IndicateursController',
        ]);
    }

    /**
     * @Route("/Indicateur/", name="Maj_Indicateur", condition="request.isXmlHttpRequest()")
     * @IsGranted("ROLE_PLANIF")
     */
    public function MaJIndicateur(Request $request, FunctIndic $indic, ManagerRegistry $manaReg)
    {
     //Création des indicateurs
        //Si modification de la date, on revoie la période des données
        if(!$request->get('DatedebPlan')){
            //Création de la variable charge totale sur la semaine
            //Recherche du début et de la fin de semaine à plannifier
            $currentMonthDateTime = new \DateTime();
            $FinSem = $currentMonthDateTime->modify('sunday next week');
            $currentMonthDateTime = new \DateTime();
            $DebSem = $currentMonthDateTime->modify('monday next week');
        }
        else{
            $currentMonthDateTime = new \DateTime($request->get('DatedebPlan'));
            $FinSem = $currentMonthDateTime->modify('sunday this week');
            $currentMonthDateTime = new \DateTime($request->get('DatedebPlan'));
            $DebSem = $currentMonthDateTime->modify('monday this week');
        }
        $repo=$manaReg->getRepository(Planning::class);
        $CharTot= $indic->chargTot($repo, $FinSem, $DebSem);
        $TpsOuvParMach=intval(24*7);

       //Création de la variable charge de chaque machine sur la semaine encours
       $result=$indic->chargMachsem($repo, $FinSem, $DebSem, $TpsOuvParMach, $CharTot);
           if ($request->get('NomKPI')==="occupation-moyen"){
                $datu = $result[0];   
            }
            else {
                $datu = $result[1];
            }
            
       return new JsonResponse(['TabVal'=>$datu]);
    }

    /**
     * @Route("/indicateur/RepartProg", name="indic_Repart_Prog")
     * 
     */
    public function indicRepartProg($an, $unjouran, FunctIndic $indic, ManagerRegistry $manaReg)
    {
        //Recherche date de la veille
        $DateJour = new \DateTime();
        $hier = $DateJour->modify('yesterday');
        //Recherche date du jour
        $DateJour = new \DateTime();
        $jour=$DateJour->modify('today');

    // Création de la variable pour la répartition des programmes svt format ci-dessous
        //{ x: new Date("1 Jan 2015"), y: 868800 },
        //{ x: new Date("1 Feb 2015"), y: 1071550 },
        $repo=$manaReg->getRepository(PolymReal::class);
        $Polyms=$repo -> findAllPcsByDate($unjouran);
        $data = [];
        $i = 0;
        foreach($Polyms as $polym){   
            $y=intval($polym[1]);
            //dump(strtotime($DateMoisenCours));
            $data[$i] = ['x'=> strtotime($an.'-'.$polym['Mois'].'-01')*1000, 'y'=> $y];
            $i = $i + 1;
            //dump($polym['Dossier']);
            //dump(sizeof(explode(';',$polym['Dossier'],4)));
        }
        $RepartP= new JsonResponse($data);
    //Création de la variable pour récupérer le nombre total de pcs svt durée
        $repo=$manaReg->getRepository(PolymReal::class);
        $totPcs= $indic->totalPcs($repo, $jour, $unjouran);

        return $this->render('indicateurs/Frames/IndicRepartProg.html.twig', [
            'controller_name' => 'IndicateursController',
            'Jour' => $hier,
            'TotPcs' => $totPcs,
            'RepartPcs' => $RepartP->getContent()
        ]);
    }

    /**
     * @Route("/indicateur/RatioChargePcs", name="indic_Ratio_Charge_Pcs")
     * 
     */
    public function indicRatioChargePcs(?DateTime $unjouran, FunctIndic $indic, ManagerRegistry $manaReg)
    {
        // { label: "New Jersey",  y: 19034.5 },
        //{ label: "Texas", y: 20015 },
        //{ label: "Oregon", y: 25342 },
        //{ label: "Montana",  y: 20088 },
        //{ label: "Massachusetts",  y: 28234 }
        $unjouran= new \datetime();
        $unjouran->modify('First day of january this year');
        //Recherche du numéro de semaine en cours
        $NumSem= date("W", strtotime('today '.date('Y') ));
        //Création des variables pour le rapport charge/nbrs de pièces par semaine svt format ci dessous
        $repo=$manaReg->getRepository(PolymReal::class);
        $Polyms=$repo -> findRapportPcsH($unjouran);
        $daty = [];
        $i = 0;
        $y=0;
        foreach($Polyms as $polym){
            $y=intval($polym['DureTotPolyms']/3600);
            $daty[$i] = ['label'=> $polym['semaine'],'y'=> $y];
            $i = $i + 1;
        }
        $daty2 = [];
        $i = 0;
        $HProdSem=$y;
        foreach($Polyms as $polym){
            $y=intval($polym['NbrPcs']);
            $daty2[$i] = ['label'=> $polym['semaine'],'y' => $y];
            $i = $i + 1;
        }
        $PProdSem=$y;

        if($HProdSem) {
            $RapportPH=round($PProdSem/$HProdSem,2);
        } else {
            $RapportPH=0;
        }
        $RapportH= new JsonResponse($daty);
        $RapportPcs= new JsonResponse($daty2);

        return $this->render('indicateurs/Frames/IndicRatioChargePcs.html.twig', [
            'controller_name' => 'IndicateursController',
            'RapportH' => $RapportH->getContent(),
            'RapportPH' => $RapportPH,
            'Semaine' => $NumSem,
            'RapportPcs' => $RapportPcs->getContent()
        ]);
    }

    /**
     * @Route("/indicateur/RepartPcsProg", name="indic_Repart_Pcs_Prog")
     * 
     */
    public function indicRepartPcsProg(Request $request, FunctIndic $indic, ManagerRegistry $manaReg)
    {        
    // Création de la variable pour la répartition des pièces suivant les programmes lancées
        //{ y: 47, color: "#c70000", toolTipContent: "United States: " },
        //{ y: 53, color: "#424242", toolTipContent: null }
        
        //Recherche date de la veille
        $DateJour = new \DateTime();
        $hier = $DateJour->modify('yesterday');
        //Recherche date du jour
        $DateJour = new \DateTime();
        $jour=$DateJour->modify('today');

        $repo=$manaReg->getRepository(PolymReal::class);
        $Polyms=$repo -> findRepartPcssvtProg($jour,$hier);

        $dati = [];
        $i = 0;
        $NbPolymJour=0;
        foreach($Polyms as $polym){
            $y=intval($polym[2]);
            $NbPolymJour=$NbPolymJour+intval($polym[1]);
            //dump($y);
            $dati[$i] = ['y'=> $y,'name'=> $polym['Nom']];
            $i = $i + 1;
        }
        $RepartPcs= new JsonResponse($dati);

        return $this->render('indicateurs/Frames/IndicRepartPcsProg.html.twig', [
            'controller_name' => 'IndicateursController',
            'RepartPolyms' => $RepartPcs->getContent(),
            'Jour' => $jour
        ]);
    }

    /**
     * @Route("/indicateur/TRSVol", name="indic_TRS_Vol")
     * 
     */
    public function indicTRSVol(?DateTime $hier, FunctIndic $indic, ManagerRegistry $manaReg)
    {
    //Récupération de la liste des moyens
        $repos=$manaReg->getRepository(Moyens::class);
        $listMoyensDispo=$repos->findBy(['Id_Service' => '8','Activitees' => 'Realisee', 'Operationnel' => 1]);
        $nbMoyens=count($listMoyensDispo);
        if (!$hier) {
            //Recherche date de la veille
            $DateJour = new \DateTime();
            $hier = $DateJour->modify('yesterday');
        }
        //Recherche date du jour
        $DateJour = new \DateTime();
        $jour=$DateJour->modify('today');
        //dump($jour);
        $repo=$manaReg->getRepository(PolymReal::class);
        $Polyms=$repo -> findTRS($jour,$hier);
        //dump($Polyms);
        //Tps de capacité machine en 3x8 VSD SD
        $repo=$manaReg->getRepository(Agenda::class);
        $tbTpsOuv=$repo->findTpsW($DateJour,$hier,'Journees');
        $dato = [];
        $datix = [];
        $i = 0;
        foreach($Polyms as $polym){
            $y=intval($polym['DureePolym']/3600);
			if (!$tbTpsOuv) {
				$TpsOuv=0;
			}else{
				$TpsOuv=$tbTpsOuv[0][1];
			}
            $x=$nbMoyens*$TpsOuv;
			if ($x==0) {
				$valTRS=0;
			} else {
				$valTRS=$y/$x;
			}
            $z=$polym['PourVol'];
            $dato[$i] = ['y'=> ($valTRS)*100,'color' => "#c70000"];
            $datix[$i]= ['y'=> $z,'color' => "#c70000"];
            $i = $i + 1;
            $dato[$i] = ['y'=> 100-($valTRS)*100,'color' => "#424242"];
            $datix[$i]= ['y'=> 100-$z,'color' => "#424242"];
            $RatioP=round(($valTRS)*100,1);
            $RatioV=round(($polym['PourVol']),1);
            
        }
        $TRS= new JsonResponse($dato);
        $VolCharg= new JsonResponse($datix);

        return $this->render('indicateurs/Frames/IndicTRSVol.html.twig', [
            'controller_name' => 'IndicateursController',
            'TRS' => $TRS->getContent(),
            'VolCharg' => $VolCharg->getContent(),
            'CapaMach' => $x,
            'PercentTRS' => $RatioP,
            'PercentVol' => $RatioV,
            'Jour' => $hier
        ]);
    }

    /**
     * @Route("/indicateur/ChargMach", name="indic_Charge_Machine")
     * 
     */
    public function indicChargMach(?DateTime $FinSem,?DateTime $DebSem, ManagerRegistry $manaReg)
    {
        if (!$FinSem or !$DebSem) {
            //Recherche du début et de la fin de semaine en cours
            $currentMonthDateTime = new \DateTime();
            $FinSem = $currentMonthDateTime->modify('sunday this week');
            $currentMonthDateTime = new \DateTime();
            $DebSem = $currentMonthDateTime->modify('monday this week');
        } 
    //Création de la variable charge de chaque machine sur la semaine encours
        $repo=$manaReg->getRepository(PolymReal::class);
        $Polyms=$repo ->findCharSem($FinSem,$DebSem);
        $Tablo = [];
        $i = 0;
        foreach($Polyms as $polym){
            $currentMonthDateTime = new \DateTime();
            $JourDep = $currentMonthDateTime->modify('monday this week');
            $TboData = [];
            $j = 0;
            $PMoy=$repo ->findCharMachSem($FinSem,$DebSem,$polym['moyen']);
            //dump($PMoy);    //$Annee.'-'.$polym['Mois'].'-01')
            foreach($PMoy as $pmoy){
                $y=intval($pmoy['NbrPcs']);
                $TboData[$j]=['x'=> strtotime($pmoy['annee'].'-'.$pmoy['mois'].'-'.$pmoy['jour'])*1000,'y'=>$y];
                $j = $j + 1;
            };
            $Tablo[$i]=['type'=>"stackedBar",'name'=>$polym['moyen'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            $i = $i + 1;
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }
        $Productivite= new JsonResponse($Tablo);

        return $this->render('indicateurs/Frames/IndicChargMach.html.twig', [
            'controller_name' => 'IndicateursController',
            'FinSem' => $FinSem,
            'DebSem' => $DebSem,
            'Productivite' => $Productivite->getContent()
        ]);
    }
    
    /**
     * @Route("/indicateur/TRSMoyNbPolym", name="indic_TRSMoy_NbPolym")
     * 
     */
    public function indicTRSMoyNbPolym(?DateTime $SemAvant, ManagerRegistry $manaReg)
    {
    //Récupération de la liste des moyens
    $repos=$manaReg->getRepository(Moyens::class);
    $listMoyensDispo=$repos->findBy(['Id_Service' => '8','Activitees' => 'Realisee', 'Operationnel' => 1]);
    $nbMoyens=count($listMoyensDispo);
    //Création de la variable TRS moyen et nbr de polym par semaine,
        if (!$SemAvant) {
            $SemAvant = date("Y-m-d", strtotime('- 10 weeks'.date('Y') ));
            $SemAvant=new \datetime($SemAvant);
        }
        $DateJour = new \DateTime();
        //Tps de capacité machine en 3x8 VSD SD
        $repo=$manaReg->getRepository(Agenda::class);
        $TpsOuv=$repo->findTpsW($DateJour,$SemAvant,'Semaines');
        $repo=$manaReg->getRepository(PolymReal::class);
        //$nbJourW=count($repo->findJourW($DateJour,$SemAvant));
        //$TpsOuv=intval($nbMoyens*24*($nbJourW/7));
        //$TpsOuv=intval(24*7*11);
        $Polyms=$repo ->findCharJourSem($SemAvant);
        $daty = [];
        $daty2 = [];
        $y=0;
        $i = 0;
        
        foreach($Polyms as $polym){
            $y=intval($polym['DureTotPolyms']/3600);
            $x=intval($TpsOuv[$i][1]*$nbMoyens);
            $daty[$i] = ['label'=> $polym['semaine'],'y'=> round(($y/$x)*100,1)];
            $y2=intval($polym['NbrProg']);
            $daty2[$i] = ['label'=> $polym['semaine'],'y' => $y2];
            $i = $i + 1;
        }
        $HProdSem=$y;
        $RapportTRSem= new JsonResponse($daty);
        $RapportProgSem= new JsonResponse($daty2);

        return $this->render('indicateurs/Frames/IndicTRSMoyNbPolym.html.twig', [
            'controller_name' => 'IndicateursController',
            'RapportTRSem' => $RapportTRSem->getcontent(),
            'HProdSem' => $HProdSem,
            'RapportNbrProg' => $RapportProgSem->getcontent()
        ]);
    }

    /**
     * @Route("/indicateur/TRSJourNPolymJour", name="indic_TRSJour_NbPolymJour")
     * 
     */
    public function indicTRSJourNPolymJour(?DateTime $SemDer,?DateTime $DateJour,?string $Interval,?string $Moyens ,Request $request, ManagerRegistry $manaReg )
    {
    //Récupération de la liste des moyens
    $repos=$manaReg->getRepository(Moyens::class);
    $listMoyensDispo=$repos->findBy(['Id_Service' => '8','Activitees' => 'Realisee', 'Operationnel' => 1]);
    //Gestion du type d'intervalle de données qu'on va affiché, donné par liste déroulante
        if (!$request->get('Type')) {
            $Interval = "Jour";
        } else {
            $Interval = $request->get('Type');
        }
    //Gestion des moyens a afficher, renvoyer par liste déroulante à cocher
        if (!$request->get('Moyens')) {
            $Moyens = "All";
            $nbMoyens=count($listMoyensDispo);
        } else {
            $Moyens = $request->get('Moyens');
            $nbMoyens=count($request->get('Moyens'));
        }
        //Récupération des paramètres types suivant choix intervalle de données
        switch ($Interval) {
            case 'Semaine':
                $CycleAnal = "- 10 weeks";
                //Recherche du numéro des semaines  recherchés
                $MoisEC = date("M/Y", strtotime('today '.date('Y') ));
                $MoisDer = date("M", strtotime($CycleAnal.date('Y') ));
                //Création des paramètres types pour graphique
                $Cycle = "mois " . $MoisDer . " à " . $MoisEC;
                $Type = "week";
                $Titre = "Semaines";
                $Format = "D/M";
                //$x=$nbMoyens*24*7;
                break;
            case 'Mois':
                //Recherche du numéro des mois recherchés
                $unjouran= new \datetime();
                $unjouran->modify('First day of january this year');
                $CycleAnal = "- 12 months";
                $MoisEC = date("M/Y", strtotime('today '.date('Y') ));
                $MoisDer = date("M/Y", strtotime($CycleAnal.date('Y') ));
                if ($MoisDer < $unjouran) {
                    $MoisDer = date("M/Y", strtotime('First day of january this year '.date('Y') ));
                    $CycleAnal = date_diff(new \datetime, $unjouran)->format('%R%m months');
                }
                //Création des paramètres types pour graphique
                $Cycle = "mois " . $MoisDer . " à " . $MoisEC;
                $Type = "month";
                $Titre = "Mois";
                $Format = "MM/YY";
                //$x=$nbMoyens*24*20;
                break;
            case 'Annee':
                $CycleAnal = "- 5 years";
                //Recherche du numéro des années recherchées
                $MoisEC = date("M/Y", strtotime('today '.date('Y') ));
                $MoisDer = date("M/Y", strtotime($CycleAnal.date('Y') ));
                //Création des paramètres types pour graphique
                $Cycle = "mois " . $MoisDer . " à " . $MoisEC;
                $Type = "year";
                $Titre = "Annees";
                $Format = "DD/MM/YY";
                //$x=$nbMoyens*24*337;
                break;
            default:
                $CycleAnal = "- 15 days";
                //Recherche du numéro de semaine en cours
                $NumSem= date("W", strtotime('today '.date('Y') ));
                $SemDer=date("W", strtotime('- 1 week '.date('Y') ));
                //Création des paramètres types pour graphique
                $Cycle = "semaine " . $SemDer . "-" . $NumSem;
                $Type = "day";
                $Titre = "Journees";
                $Format = "DDD DD/MM/YY";
                //$x=$nbMoyens*24;
                break;
        }
    //Création de la variable TRS et Nbr Polym par jour
        $JourAvant = date("Y-m-d", strtotime($CycleAnal.date('Y') ));
        $JourAvant=new \datetime($JourAvant);
        $DateJour = new \DateTime();
        $repo=$manaReg->getRepository(PolymReal::class);
        $primePolym=$repo->findOneBy(['id' => 12]);
        //Vérification si le 1er jour est un jour travaillé, sinon on prend le dernier jour travaillé
        if ($JourAvant<$primePolym->getDebPolym()) {
            $JourAvant=$primePolym->getDebPolym();
        }
        
    //Récupération du nombre d'heures par jour travaillé dans l'intervalle ( à modifier avec le planning de prod)
        $repo=$manaReg->getRepository(Agenda::class);
        $qteTpsW=$repo->findTpsW($DateJour,$JourAvant,$Titre);
        $repo=$manaReg->getRepository(PolymReal::class);
        //$nbJoursW=$repo->findJourW($DateJour,$JourAvant);
        //$x=$nbMoyens*24*(count($nbJoursW)/7);
        $Polyms=$repo->findTRSJour($DateJour, $JourAvant, $Titre, $Moyens);
        $datuy = [];
        $datuy1 = [];
        $datuy2 = [];
        $i = 0;
        foreach($Polyms as $polym){
            $x=$nbMoyens*intval($qteTpsW[$i][1]);
            $y=intval($polym['DureePolym']/3600);
            $datuy[$i] = ['x'=> strtotime($polym['Annees'].'-'.$polym['Mois'].'-'.$polym['jour'])*1000,'y'=> round(($y/$x)*100,1)];
            $y1=intval($polym['PourVol']);
            $datuy1[$i] = ['x'=> strtotime($polym['Annees'].'-'.$polym['Mois'].'-'.$polym['jour'])*1000,'y' => $y1];
            $y2=intval($polym['NbrProg']);
            $datuy2[$i] = ['x'=> strtotime($polym['Annees'].'-'.$polym['Mois'].'-'.$polym['jour'])*1000,'y' => $y2];
            $i = $i + 1;
        }
        $TRSem= new JsonResponse($datuy); 
        $TxSem= new JsonResponse($datuy1);
        $ProgSem= new JsonResponse($datuy2);

        if ($request->get('Type')) {
            return new JsonResponse(['Titre'=> $Titre, 'Type'=> $Type, 'Format'=> $Format, 'Cycle'=> $Cycle,
             'ProgSem'=> $datuy2, 'TxSem'=> $datuy1, 'TRSem'=> $datuy]);
        } else {
            return $this->render('indicateurs/Frames/IndicTRSJourNPolymJour.html.twig', [
                'controller_name' => 'IndicateursController',
                'TRSem' => $TRSem->getcontent(),
                'TxSem' => $TxSem->getcontent(),
                'ProgSem' => $ProgSem->getcontent(),
                'Cycle' => $Cycle,
                'ListMoyens' => $listMoyensDispo,
                'Type' => $Type,
                'Titre' => $Titre,
                'Format' => $Format
            ]);
        }
    }

    /**
     * @Route("/indicateur/", name="indic_ChargeTot_Semaine")
     * 
     */
    public function indicChargTotSem(Request $request, FunctIndic $indic, ManagerRegistry $manaReg)
    {
    //Création de la variable charge totale sur la semaine
        $repo=$manaReg->getRepository(Planning::class);
        $Polyms=$repo -> findCharge($FinSem,$DebSem);
        foreach($Polyms as $polym){
            $CharTot=intval($polym['DureTheoPolym']/10000);
        }
        
    //Création de la variable charge de chaque machine sur la semaine encours
    //{ y: 2,  indexLabel: "2%",  label: "Etuve2" },
    //{ y: 4,  indexLabel: "4%",  label: "Etuve3" },        
        $Polyms=$repo -> findChargeMach($FinSem,$DebSem);
        $datu = [];
        $i = 0;
        foreach($Polyms as $polym){
            $y=intval($polym['DureTheoPolym']/10000);
            $RatioC=round(($y/$CharTot)*100,1);
            $datu[$i] = ['y'=> $y,'indexLabel'=> $RatioC.'%',  'label' => $polym['Moyen']];
            $i = $i + 1;
        }
        $ChargeMoy= new JsonResponse($datu);
    }

     /**
     * @Route("/indicateur/OTDMoulage", name="indic_OTD_Moulage")
     * 
     */
    public function indicOTDMoulage(Request $request, FunctIndic $indic)
    {
    //Création de la variable de suivi de l'OTD entre le moulage et la polym
        // $repo=$manaReg->getRepository(PolymReal::class);
        // $Polyms=$repo -> findCharge($FinSem,$DebSem);
        // foreach($Polyms as $polym){
        //     $CharTot=intval($polym['DureTheoPolym']/10000);
        // }
        $datu = [0=>['x'=> 01-01-2020, 'y'=>17376],
        1=>['x'=> 01-02-2020, 'y'=>17376],
        2=>['x'=> 01-03-2020, 'y'=>21431],
        3=>['x'=> 01-04-2020, 'y'=>25724],
        4=>['x'=> 01-05-2020, 'y'=>22138],
        5=>['x'=> 01-06-2020, 'y'=>25429],
        6=>['x'=> 01-07-2020, 'y'=>29160]];

        $OTD= new JsonResponse($datu);

        return $this->render('indicateurs/Frames/IndicOTDMoulage.html.twig', [
            'controller_name' => 'IndicateursController',
            'OTD' => $OTD->getcontent()
        ]);
    }

     /**
     * @Route("/indicateur/ChargeSAPUnMois", name="indic_ChargeSAP_UnMois")
     * 
     */
    public function indicChargeSAPUnMois(Request $request, FunctIndic $indic, ManagerRegistry $manaReg)
    {
        // Création de la table de répartition des programmes suivant OF SAP lancés sur 1 mois
        $repo=$manaReg->getRepository(Charge::class);
        // Date à aujourd'hui
        $jour= new \datetime;

        $SemUn=$jour->format("W");
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 30 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        $DerSem=$jourVisu->format("W");
        $result=$indic->ordoUnMois($repo, $jourVisu, $jour);

        $RepartP= new JsonResponse($result);

        return $this->render('indicateurs/Frames/IndicChargeSAPUnMois.html.twig', [
            'controller_name' => 'IndicateursController',
            'RepartPcs' => $RepartP->getContent(),
            'DerSem' => $DerSem,
            'SemUn' => $SemUn,
            'datedeb' => $jour,
            'datefin' => $jourVisu,
        ]);
    } 
    
     /**
     * @Route("/indicateur/ChargeRetardUnMois", name="indic_ChargeRetard_UnMois")
     * 
     */
    public function indicChargeRetardUnMois(Request $request, FunctIndic $indic, ManagerRegistry $manaReg)
    {
        // Création de la table de répartition des programmes en retard suivant OF SAP lancés 
        $repo=$manaReg->getRepository(Charge::class);
        // Date à hier
        $jourFinRetard = date("Y-m-d", strtotime('- 1 days'.date('Y') ));
        $date=new \datetime($jourFinRetard);
        // Date en retard d'1 mois
        $jourVisu = date("Y-m-d", strtotime('- 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        // Date à aujourd'hui
        $jour= new \datetime;
        $result=$indic->ordoRetardUnMois($repo, $jourVisu, $date);
        $RepartRetard= new JsonResponse($result);

        return $this->render('indicateurs/Frames/IndicChargeRetardUnMois.html.twig', [
            'controller_name' => 'PlanningOrdo',
            'RepartRetard'=> $RepartRetard->getContent(),
            'datedeb' => $jour,
            'datefin' => $jourVisu,
        ]);
    }

     /**
     * @Route("/indicateur/ChargeSAPRetard", name="indic_ChargeSAP")
     * 
     */
    public function indicChargeSAPRetard(Request $request, FunctIndic $indic, ManagerRegistry $manaReg)
    {
        // Création de la table de répartition des programmes oubliés
        $repo=$manaReg->getRepository(Charge::class);
        $ChargTot=$repo -> findAll();
        // Date à plus d'un mois
        $jourFinRetard = date("Y-m-d", strtotime('+ 365 days'.date('Y') ));
        $date=new \datetime($jourFinRetard);
        $SemUn=$date->format("W");
        // Date en retard d'1 an
        $jourVisu = date("Y-m-d", strtotime('- 730days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        $DerSem=$jourVisu->format("W");
        $result=$indic->ordoUnMois($repo, $jourFinRetard, $jourVisu);

        $RepartT= new JsonResponse($result);

        return $this->render('indicateurs/Frames/IndicChargeRetard.html.twig', [
            'controller_name' => 'IndicateursController',
            'RepartT' => $RepartT->getContent(),
            'ChargeTot' => $ChargTot,
            'DerSem' => $DerSem,
            'SemUn' => $SemUn,
            'datedeb' => $jourVisu,
            'datefin' => $jourFinRetard,
        ]);
    }

     /**
     * @Route("{service}/indicateur/kpiHeader", name="indic_Header")
     * 
     */
    public function indic_Header(Request $request,
    FunctIndic $indic, 
    ManagerRegistry $manaReg, 
    $service)
    {
        // Date à aujourd'hui
        $jour= new \datetime;
        
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);

        $repo=$manaReg->getRepository(Charge::class);
        $ChargeMois=$repo->myFindPcsTotMois($jour,$jourVisu);

        //Création variables des indicateurs Headers
        $listIndicHeader=[];

        return $this->render('indicateurs/Frames/IndicHeader.html.twig', [
            'controller_name' => 'IndicateursController',
            'datedeb' => $jourVisu,
            'ChargeMois' => $ChargeMois[0],
            'listIndicHeader' => $listIndicHeader,
            'service' => $service
        ]);
    }
}