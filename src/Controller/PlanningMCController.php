<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ConfSmenu;
use App\Entity\ConfSsmenu;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\ProgMoyens;
use App\Form\CreationProgType;
use App\Form\CreationMoyensType;
use App\Entity\CategoryMoyens;
use App\Entity\Planning;
use App\Form\PolymFormType;
use App\Form\PlanifDemandeType;
use App\Entity\Demandes;
use App\Entity\Moyens;
use App\Entity\User;
use App\Entity\PolymReal;
use App\Repository\DefaultRepositoryFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Component\HttpFoundation\Session\Session ;
//use Symfony\Component\Serializer\Serializer;

class PlanningMCController extends Controller
{
    /**
     * @Route("/PlanningMC", name="Planning")
     * @Security("has_role('ROLE_REGLEUR')")
     * @Security("has_role('ROLE_CE_POLYM')")
     */
    public function index()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=[];

        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('first day of this week');
        $currentMonthDateTime = new \DateTime();
        $lastDateTime = $currentMonthDateTime->modify('last day of this week');

//Recherche des moyens à afficher sur planning
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
        $moyens=$repos -> findAllMoyensSvtService ( intval('8') );
        $item=$moyens;   
        $data = [];
        $TbEtat=[];
        $i = 0;
        $a=0;
        foreach($moyens as $moyen){
            if ($moyen['SousTitres']==2){
                $Etats=$repos -> findBy(['Libelle' => $moyen['Moyen']]);
                // On rajoute les notions d'activitees au moyen pour créer 2 lignes sur planning
                foreach($Etats as $etat){
                    if ($moyen['id']!=$etat->getId()){
                        $TbEtat[$a]=['id'=>$etat->getId(), 'content'=>$etat->getActivitees()];
                        $a=$a+1;
                    }
                }
                //dump($TbEtat[$a-1]['id']);
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen'], 'nestedGroups' => [$TbEtat[$a-1]['id']]];
            }
            else{
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen']];
            }
            $i = $i + 1;
			//On affecte un élément $item à $data
        }
        $Ssmoyen= new JsonResponse($TbEtat);
        $moyen= new JsonResponse($data);
        //dump($Ssmoyen);
        //dump($moyen);
//Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Taches=$repo -> findAll();
        $data = [];
        $i = 0;
        foreach($Taches as $tache){
            //On cherche le moyen attribué à la polym suivant la demande et l'activité Plannification
            $commentaires=$tache->getNumDemande()->getCommentaires()."/".$tache->getNumDemande()->getOutillages();
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification(),'Activitees'=> 'Plannifie']);
            $data[$i] = ['id'=> $i,'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires];
            $i = $i + 1;
            //dump($MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]));
            //dump($MoyUtil[0]->getId());
            //dump($Taches);
            //dump($tache->getNumDemande()->getCycle()->getCouleur());
        }
        //Implémentation dans la variable des polyms créées
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAll();
        //dump($data);
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $i,'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
            $i = $i + 1;
        }
        $taches= new JsonResponse($data);
        

        return $this->render('planning_mc/index.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
            'Taches' => $taches->getcontent(),
            'Moyens' => $moyen->getcontent(),
            'Ssmoyen' => $Ssmoyen->getcontent(),
            'Items' => $item
        ]);
    }

    /**
     * @Route("//PlanningMC", name="Polym_Edit", condition="request.isXmlHttpRequest()")
     */
    public function editpolym(Request $request)
    {
        $Planning = new Planning();
        
        $form = $this->createForm(PolymFormType::class, $Planning, array(
            'action' => $this->generateUrl($request->get('_route'))
        ))
        ->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($Planning);
            $this->getDoctrine()->getManager()->flush();
            return new Response('success');
        }
        
        return $this->render('planning_mc/editpolym.html.twig', [
            'form' => $form->createView(),
        ]);
    }


	/**
     * @Route("/", name="home")
     */
    public function home()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();
        
        //Recherche du début de l'année avec n+1 mois pour effectuer les calcul des indicateurs         
        $DateAnCours = date("l", strtotime('first day of January '.date('Y') )); 
        //Recherche de la date du début de semaine dernière
        $currentMonthDateTime = new \DateTime();
        $DateSem = $currentMonthDateTime->modify('first day of this week');
        //Recherche date de la veille
        $DateJour = new \DateTime();
        $hier = $DateJour->modify('yesterday');
        //$hier = date("l", strtotime('yesterday '.date('Y-m-d H:i') ));
        //Recherche date du jour
        $DateJour = new \DateTime();
        $jour=$DateJour->modify('today');
        //Recherche date en cours sur les 13 mois pour comparaison du mois en cours
        $DateEncours = date("Y m d", strtotime('- 13 months'.date('Y') ));
        //Recherche du début et de la fin de semaine en cours
        $currentMonthDateTime = new \DateTime();
        $FinSem = $currentMonthDateTime->modify('sunday this week');
        $currentMonthDateTime = new \DateTime();
        $DebSem = $currentMonthDateTime->modify('monday this week');
        //Recherche de l'année en cours
        $Annee = date("Y", strtotime('today '.date('Y') ));
        //Recherche du numéro de semaine en cours
        $NumSem= date("W", strtotime('today '.date('Y') ));

// Création de la variable pour la répartition des programmes svt format ci-dessous
        //{ x: new Date("1 Jan 2015"), y: 868800 },
        //{ x: new Date("1 Feb 2015"), y: 1071550 }, 
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAllPcsByDate($DateEncours);
        //dump($Polyms);
        $data = [];
        $i = 0;
        foreach($Polyms as $polym){ 

            //$DateMoisenCours=new \datetime( $Annee.'-'.$polym['Mois'].'-01');
            //$NewFormatDMC=date("Y-m-d\TH:i.v\Z",strtotime($Annee.'-'.$polym['Mois'].'-01'));
            //$DateMoisenCours='01 '.$NewFormatDMC.' 2019';  
            $y=intval($polym[1]);
            //dump(strtotime($DateMoisenCours));
            $data[$i] = ['x'=> strtotime($Annee.'-'.$polym['Mois'].'-01')*1000, 'y'=> $y];
            $i = $i + 1;
            dump($polym['Dossier']);
            dump(sizeof(explode(';',$polym['Dossier'],4)));
        }
        $RepartP= new JsonResponse($data);

//Création des variables pour le rapport charge/nbrs de pièces par semaine svt format ci dessous
// { label: "New Jersey",  y: 19034.5 },
//{ label: "Texas", y: 20015 },
//{ label: "Oregon", y: 25342 },
//{ label: "Montana",  y: 20088 },
//{ label: "Massachusetts",  y: 28234 }
    $Polyms=$repo -> findRapportPcsH($DateEncours);
    $daty = [];
    $i = 0;
    foreach($Polyms as $polym){
        $y=intval($polym['DureTotPolyms']/10000);
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
    $RapportPH=round($PProdSem/$HProdSem,2);
$RapportH= new JsonResponse($daty);
$RapportPcs= new JsonResponse($daty2);
        
// Création de la variable pour la répartition des pièces suivant les programmes lancées
        //{ y: 47, color: "#c70000", toolTipContent: "United States: " },
        //{ y: 53, color: "#424242", toolTipContent: null }
        //dump($jour);
        $Polyms=$repo -> findRepartPcssvtProg($jour,$hier);
        //dump($Polyms);
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
        //dump($RepartP->getContent());

// Création de la variable pour le nombre total de pcs sur 13 mois
        $Polyms=$repo -> findAllPcs ($DateEncours);
        foreach($Polyms as $polym){
            $TotPcs=intval($polym[1]);
            //dump($TotPcs);
        }

// Création de la variable pour le TRS par moyen svt format 
        //{ y: 47, color: "#c70000", toolTipContent: "United States: " },
        //{ y: 53, color: "#424242", toolTipContent: null }
        //dump($jour);
        $Polyms=$repo -> findTRSMachine($jour,$hier);
        //dump($Polyms);
        $dato = [];
        $i = 0;
        foreach($Polyms as $polym){
            $y=intval($polym[2]);
            //dump($y);
            $dato[$i] = ['y'=> $y,'name'=> $polym['Nom']];
            $i = $i + 1;
        }
        $TRS= new JsonResponse($dato);
        //dump($RepartP->getContent());

// Création de la variable pour le TRS global svt format 
        //{ y: 47, color: "#c70000", toolTipContent: "United States: " },
        //{ y: 53, color: "#424242", toolTipContent: null }
        //dump($jour);
        $Polyms=$repo -> findTRS($jour,$hier);
        //dump($Polyms);
        $dato = [];
        $i = 0;
        foreach($Polyms as $polym){
            $y=intval($polym['DureePolym']/10000);
            $x=11*24;
            $dato[$i] = ['y'=> ($y/$x)*100,'color' => "#c70000"];
            $i = $i + 1;
            $dato[$i] = ['y'=> 100-($y/$x)*100,'color' => "#424242"];
            $Ratio=round(($y/$x)*100,1);
        }
        $TRS= new JsonResponse($dato);

//Création de la variable charge de chaque machine sur la semaine encours
        //{   type: "stackedBar",
        //    name: "Dolouet",
        //    showInLegend: "true",
        //    xValueFormatString: "DD, MMM",
        //    yValueFormatString: "###",
        //     dataPoints: [
        //            { x: new Date(2017, 0, 30), y: 56 },
        //            { x: new Date(2017, 0, 31), y: 45 },
        //            { x: new Date(2017, 1, 1), y: 71 },
        //            { x: new Date(2017, 1, 2), y: 41 },
        //            { x: new Date(2017, 1, 3), y: 60 },
        //            { x: new Date(2017, 1, 4), y: 75 },
        //            { x: new Date(2017, 1, 5), y: 98 }
        //    ]},
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
            //dump($TboData);
            $Tablo[$i]=['type'=>"stackedBar",'name'=>$polym['moyen'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            $i = $i + 1;
            //$CharTot=intval($polym['DureTheoPolym']/10000);
            //dump($Tablo);
        }
        $Productivite= new JsonResponse($Tablo);
 
//Création de la variable charge totale sur la semaine
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Polyms=$repo -> findCharge($FinSem,$DebSem);
        foreach($Polyms as $polym){
            $CharTot=intval($polym['DureTheoPolym']/10000);
        }
        $TpsOuvParMach=intval(24*7);
//Création de la variable charge de chaque machine sur la semaine encours
//{ y: 2,  indexLabel: "2%",  label: "Etuve2" },
//{ y: 4,  indexLabel: "4%",  label: "Etuve3" },        
        $Polyms=$repo -> findChargeMach($FinSem,$DebSem);
        $datu = [];
        $i = 0;
        foreach($Polyms as $polym){
            $y=intval($polym['DureTheoPolym']/10000);
            $RatioC=round(($y/$TpsOuvParMach)*100,1);
            $datu[$i] = ['y'=> $y,'indexLabel'=> $RatioC.'%',  'label' => $polym['Moyen']];
            $i = $i + 1;
        }
        $ChargeMoy= new JsonResponse($datu);

        return $this->render('planning_mc/home.html.twig',[
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
            'RapportPcs' => $RapportPcs->getContent(),
            'Jour' => $hier,
            'RapportH' => $RapportH->getContent(),
            'Semaine' => $NumSem,
            'RepartPcs' => $RepartP->getContent(),
            'TotPcs' => $TotPcs,
            'TRS' => $TRS->getcontent(),
            'PercentTRS' => $Ratio,
            'Productivite' => $Productivite->getContent(),
            'CapaMach' => $x,
            'ChargeMoy' =>$ChargeMoy->getContent(),
            'CharTot' => $CharTot,
            'RepartPolyms' => $RepartPcs->getContent(),
            'PProdSem' => $PProdSem,
            'HProdSem' => $HProdSem,
            'RapportPH' => $RapportPH,
            'NbPolymJ' => $NbPolymJour,
            'FinSem' => $FinSem
        ]);
        
    }

	/**
     * @Route("/Demandes/Creation", name="Crea_Demandes")
     * @Route("/Demandes/Modification/{id}", name="Modif_Demandes")
     * @Security("has_role('ROLE_CE_MOULAGE')")
     */
    public function DemandesCrea( Request $requette,RequestStack $requestStack,ObjectManager $manager,Demandes $demande=null,$datejour=null, userInterface $user=null)
    {
//Si la demande n'est pas déjà faite(modification), on l'a crée
        //dump($demande);    
        if(!$demande){
            //dump($requette);
            if($requette->get('Demandes')){
                //Récupération des données de la polym plannifié
                $repo=$this->getDoctrine()->getRepository(Planning::class);
                $Plan=$repo -> findBy(['id'=> $requette->get('IdPla')]);
                //récupération des données de la demande réccurante
                $repo=$this->getDoctrine()->getRepository(Demandes::class);
                $Dem=$repo -> findBy(['id'=> $requette->get('Demandes')]);
                //On marque cette récurrence comme validé
                //dump($Dem[0]);
                $demande=$Dem[0];
                $demande->SetRecurValide(1);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($demande);
                $manager->flush();
                //On créé la nouvelle demande avec les données de la récurrante
                $demande = new Demandes();
                $date=$requette->get('DatePla');
                $DateSemAv = date("Y-m-d",strtotime(date("Y-m-d", strtotime($date)) . " +7 day"));
                $HeurPolym=$requette->get('HeurePla');
                $demande->setDatepropose(new \Datetime($DateSemAv));
                $demande->setHeurePropose(new \Datetime($HeurPolym));
                $demande->setCycle($Dem[0]->getCycle());
                $demande->setOutillages($Dem[0]->getOutillages());
                $demande->setMoyenUtilise($Dem[0]->getMoyenUtilise());
                $demande->setCommentaires($Dem[0]->getCommentaires());
                $newdemande=false;
            }
            else{
                $demande = new Demandes();
                $demande->setDatepropose(new \Datetime($datejour));
                $newdemande=true; 
                //dump($demande);
            }
        }
        else{
            $newdemande=false;
            //dump($user);
        }
            //Dans la construction du form, on vérifie si la demande est déjà validée lors d'une modif
            if($demande->getPlannifie()==true){
                $form = $this -> createFormBuilder($demande)
                      -> add('cycle', EntityType::class, [
                          'class' => ProgMoyens::class,
                          'choice_label' => 'nom',
                          'disabled' => 'true'
                      ])
                      -> add('date_propose', DateType::class, ['disabled' => 'true'])
                      -> add('heure_propose', TimeType::class, ['disabled' => 'true'])
                      -> add('outillages')
                      -> add('commentaires')
                      -> add('Reccurance',ChoiceType::class, [
                        'label'    => 'si besoin de figer cette polymérisation suivant une réccurance',
                        'choices'  => [
                            'NON' => false,
                            'OUI' => true]])
                      ->getForm();
                }
                else{
                    $form = $this -> createFormBuilder($demande)
                      -> add('cycle', EntityType::class, [
                          'class' => ProgMoyens::class,
                          'choice_label' => 'nom'
                      ])
                      -> add('date_propose', DateType::class)
                      -> add('heure_propose', TimeType::class)
                      -> add('outillages')
                      -> add('commentaires')
                      -> add('Reccurance',ChoiceType::class, [
                        'label'    => 'si besoin de figer cette polymérisation suivant une réccurance',
                        'choices'  => [
                            'NON' => false,
                            'OUI' => true]])
                      ->getForm();
                }
//Si la demande existe c'est une modification , sinon une création
        if($newdemande==false){
            //dump($requette);
        }
        else{
//C'est le résultat de la requette du template Demandes pour la création
            if($requestStack->getParentRequest()){
                $requette=$requestStack->getParentRequest();
                $mode=true;
            }
            else{
                $mode=false;
            }
        }
        $form->handleRequest($requette);
        if ($requette->isMethod('POST')) {
        //$form->submit($requette->request->get($form->getName()));
//On vérifie la validité des données avant de persister en base
            if($form->isSubmitted() && $form->isValid()){
                if(!$demande->getId()){
                    $demande->setDateCreation(new \datetime());
                    $demande->setPlannifie(0);
                    $demande->setRecurValide(0);
                    $demande->setUserCrea($user->getUsername());
                    
                }
                else{
                    $demande->setDateModif(new \datetime());
                    //dump($user);
                    $demande->setUserModif($user->getUsername());
                    $mode=false;
                }
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($demande);
                $manager->flush();
                
                $requette->getSession()->getFlashbag()->add('success', 'Votre demande a été enregistré.');

                if($mode==false){
                    //dump($demande);
                    
                    return $this->redirectToRoute('Demandes');
                }
                else{
                    //return $this->redirectToRoute('Demandes');
                }
            }
        }
//Pas de menu pour la fenêtre modale, juste le retour à l'accueil
        $Titres=[];

        return $this->render('planning_mc/CreationDemandes.html.twig',[
           'Titres' => $Titres,
           'formDemande' => $form->createView()
        ]);
        
    }

    /**
     * @Route("/Demandes", name="Demandes")
     * @Security("has_role('ROLE_CE_MOULAGE')")
     */
    public function Demandes(Request $requette, ObjectManager $manager,userInterface $user=null)
    {
        
//Redirection après remplissage du formulaire 
        if (!$requette->get('DatedebPlan')){
            //Recherche du début et fin de la semaine n+1  pour effectuer les demandes de créneaux         
            $currentMonthDateTime = new \DateTime();
            $firstDateTime = $currentMonthDateTime->modify('Monday next week');
            $currentMonthDateTime = new \DateTime();
            $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
            $lastDateTime = $lastDateTime->modify('23 hours');
        }
        else{
            //$currentMonthDateTime = new \DateTime(strtotime($requette->get('DatedebPlan')));
            //$sem = date("w", strtotime($requette->get('DatedebPlan').date('Y-m-d') ));
            //$firstDateTime = $currentMonthDateTime->modify('Monday next week');
            $firstDateTime=date("Y-m-d",strtotime($requette->get('DatedebPlan')));
            $currentMonthDateTime = new \DateTime($firstDateTime);
            $lastDateTime = $currentMonthDateTime->modify('Sunday this week');
            $lastDateTime = $lastDateTime->modify('23 hours');
        }

//Recherche des dates de la semaine encours pour les demandes avec récurrences

//Visualisation des demandes en cours pour la semaine n+1
    $demande=new Demandes();
           
            if(!$demande){
                $cycles = $this->getDoctrine()
                ->getRepository(Demandes::class)
                ->findAll();
            }
            else{
                $cycles = $this->getDoctrine()
                ->getRepository(Demandes::class)
                ->findAll();
            }
            //dump($demande); 
//Chargement de la variable qui récupère tous les moyens suivant un service
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
        $moyens=$repos -> findAllMoyensSvtService ( intval('8') );
        $item=$moyens;   
        $data = [];
        $TbEtat=[];
        $i = 0;
        $a=0;
        foreach($moyens as $moyen){
            if ($moyen['SousTitres']==2){
                $Etats=$repos -> findBy(['Libelle' => $moyen['Moyen']]);
                // On rajoute les notions d'activitees au moyen pour créer 2 lignes sur planning
                foreach($Etats as $etat){
                    if ($moyen['id']!=$etat->getId()){
                        $TbEtat[$a]=['id'=>$etat->getId(), 'content'=>$etat->getActivitees()];
                        $a=$a+1;
                    }
                }
                //dump($TbEtat[$a-1]['id']);
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen'], 'nestedGroups' => [$TbEtat[$a-1]['id']]];
            }
            else{
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen']];
            }
            $i = $i + 1;
            //On affecte un élément $item à $data
        }
        $Ssmoyen= new JsonResponse($TbEtat);
        $moyen= new JsonResponse($data);
//Chargement d'une variable pour la réalisation de la nav-bar du menu et des sous-titres
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();
//Chargement d'une variable pour les tâches déjà plannifiées
    $repo=$this->getDoctrine()->getRepository(Planning::class);
    $Taches=$repo -> findAll();
    $item=array();
        $data = [];
        $i = 0;
        foreach($Taches as $tache){ 
            $commentaires=$tache->getNumDemande()->getCommentaires()."/".$tache->getNumDemande()->getOutillages();
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification(),'Activitees'=> 'Plannifie']);           
            $data[$i] = ['id'=> $i,'programmes'=> $tache->getAction(),'statut'=>$tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires];
            $i = $i + 1;
        }
    $repo=$this->getDoctrine()->getRepository(PolymReal::class);
    $Polyms=$repo -> findAll();
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $i,'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
            $i = $i + 1;
        }
        $taches= new JsonResponse($data);
//récupération des demandes récurrantes de la semaine dernière

    $repo=$this->getDoctrine()->getRepository(Demandes::class);
    if(!$requette->get('UtilisateursCE')){
        $DemRec=$repo -> findBy(['Reccurance'=>'1','UserCrea'=>$user->getUsername(),'Plannifie'=>'1','RecurValide'=>'0']);
    }
    else{
        $DemRec=$repo -> findBy(['Reccurance'=>'1','UserCrea'=>$requette->get('UtilisateursCE'),'Plannifie'=>'1','RecurValide'=>'0']);
    }
    //$DemRec=$repo ->findDemRecur($lastDateTime,$firstDateTime,$user->getUsername());
//Récupération des CE du moulage pour listing
    $repo=$this->getDoctrine()->getRepository(User::class);    
    $utilisateurs=$repo -> findByRole('ROLE_CE_MOULAGE');
//Transfert des variables à la vue
        return $this->render('planning_mc/Demandes.html.twig',[
           'Titres' => $Titres,
           'datedeb' => $firstDateTime,
           'datefin' => $lastDateTime,
           'Cycles'=>$cycles,
           'Moyens' => $moyen->getContent(),
           'Ssmoyen' => $Ssmoyen->getContent(),
           'taches' => $taches->getContent(),
           'DemRec' => $DemRec,
           'utilisateurs' => $utilisateurs,
           'reqet' => $requette
        ]);   
    }

    /**
     * @Route("/Demandes/Plannification/{id}", name="Planif_Demandes")
     * @Security("has_role('ROLE_CE_POLYM')")
     */
    public function DemandesPlanif( Request $requette,RequestStack $requestStack,ObjectManager $manager,Demandes $demande=null,$datejour=null, ValidatorInterface $validator )
    {
        $action= new Planning();
        $cycles= new ProgMoyens();
        $moyen= new Moyens();
        //dump($demande->getId('id'));
//Récupération de la demande suivant ID
        $cycles = $this->getDoctrine()
                ->getRepository(ProgMoyens::class)
                ->findBy(['id' => $demande->getCycle('id')]);
        //dump($cycles[0]->getNom($demande->getCycle('id')));
//Récupération de l'ID moyen suivant nom
        $moyen = $this->getDoctrine()
            ->getRepository(Moyens::class)
            ->findBy(['Libelle' => $requette->get('Moyen'.$demande->getId('id'))]);
            //dump($moyen);
//Récupération du numéro d'action (içi le cycle)
        $action->setAction($cycles[0]->getNom($demande->getCycle('id')));
        $action->setNumDemande($demande);
//Récupération du moyen utilisé
        $action->setIdentification($requette->get('Moyen'.$demande->getId('id')));

        if (!$moyen){
            $demande->setMoyenUtilise(NULL);
        }
        else{
            $demande->setMoyenUtilise($moyen[0]);
        }       
//Récupération de l'heure de début de l'action
        $action->setDebutDate(new \Datetime($requette->get('Hdeb'.$demande->getId('id'))));
        $demande->setDatePropose(new \Datetime($requette->get('Hdeb'.$demande->getId('id'))));
//Récupération de l'heure de fin de l'action
        $action->setFinDate(new \Datetime($requette->get('Hfin'.$demande->getId('id'))));
        $demande->setDateHeureFin(new \Datetime($requette->get('Hfin'.$demande->getId('id'))));
//Récupération du statut
        $demande->setPlannifie($requette->get('Statut'.$demande->getId('id')));
        $action->setStatut('PLANNIFIE');
        $form= $this->createForm(PolymFormType::class, $action);
        $content = $requette->attributes->get('demande');
        //$form->handleRequest($requette);
        $form->submit($form->getName());
        $errors = $validator -> validate ($form); 
        //dump($errors);      
//On vérifie la validité des données avant de persister en base
        if ( $form -> isSubmitted () ) {
            //dump($demande[500]);
            if(!$demande->getId()){
                $demande->setDateCreation(new \datetime());
                 $mode=true;
            }
            else{
                $demande->setDateModif(new \datetime());
                $mode=false;
            }
            $manager = $this->getDoctrine()->getManager();
            //dump($demande);
            if(!$demande->getPlanning()){
                $manager->persist($action);
                $manager->flush();
                //dump($action);
                $manager->persist($demande);
                $manager->flush();
            }
            else{
                //dump($action);
                $manager->remove($demande->getPlanning());
                $manager->flush();
            }
                //dump($demande);
            
            $requette->getSession()->getFlashbag()->add('success', 'Votre polym a été enregistré.');
                
            if($mode==false){
                //dump($demande);
                    
                return $this->redirectToRoute('Planification');
            }
            else{
                //return $this->redirectToRoute('Demandes');
            }
        }
        $Titres=[];

        return $this->render('planning_mc/CreationPolyms.html.twig',[
           'Titres' => $Titres
        ]);
    }

	/**
     * @Route("/Planification", name="Planification")
     * @Security("has_role('ROLE_CE_POLYM')")
     */
    public function Planification(request $requette,Demandes $demande=null)
    {
//Chargement d'une variable pour toutes les demandes créées
        $test = $this->getDoctrine()
            ->getRepository(demandes::class)
            ->findAll();
//Recherche du début de semaine et fin de semaine
    if(!$requette->get('DatedebPlan')){
        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('Monday next week');
        $currentMonthDateTime = new \DateTime();
        $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
        $lastDateTime = $lastDateTime->modify('23 hours');
    }
    else{
        //$currentMonthDateTime = new \DateTime(strtotime($requette->get('DatedebPlan')));
        //$sem = date("w", strtotime($requette->get('DatedebPlan').date('Y-m-d') ));
        //$firstDateTime = $currentMonthDateTime->modify('Monday next week');
        $firstDateTime=date("Y-m-d",strtotime($requette->get('DatedebPlan')));
        $currentMonthDateTime = new \DateTime($firstDateTime);
        $lastDateTime = $currentMonthDateTime->modify('Sunday this week');
        $lastDateTime = $lastDateTime->modify('23 hours');
    }
        //dump($firstDateTime);
        //dump($lastDateTime);
//Recherche des moyens à afficher sur planning
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
        $moyens=$repos -> findBy(['Id_Service' => '8','Activitees' => 'Plannifie']);
        $item=$moyens;
        //$serializer = new Serializer();
        //$jsonContent = $serializer->serialize($moyen, 'json');
        //dump($moyens);
        
        $data = [];
        $i = 0;
        foreach($moyens as $moyen){
            $data[$i] = ['id'=> $moyen->getId(),  'content'=> $moyen->getLibelle()];
            $i = $i + 1;
			//On affecte un élément $item à $data
        }
        //dump($data);
        $moyen= new JsonResponse($data);
        //dump($moyen);
//Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Taches=$repo -> findAll();
        //$item=array();
        //dump($Taches);
        $data = [];
        $i = 0;
        foreach($Taches as $tache){
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]);
            $data[$i] = ['id'=> $i,'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $tache->getNumDemande()->getCommentaires()];
            $i = $i + 1;
            //dump($MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]));
            //dump($MoyUtil[0]->getId());
        }
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAll();
        //dump($data);
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $i,'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
            $i = $i + 1;
        }
        $taches= new JsonResponse($data);
        //dump($taches);
//Chargement des éléments du nav-bar menu
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();
        //$form = $this->createForm(PlanifDemandeType::class, $demande);
//Envoie au template Plannification
        return $this->render('planning_mc/Planification.html.twig',[
            'Titres' => $Titres,
            'tests' => $test,
            'datedeb' => $firstDateTime,
            'datefin' => $lastDateTime,
            'taches' => $taches->getContent(),
            'moyens' => $moyen->getContent(),
            'items' => $item
        ]);
    }

     /**
     * @Route("/Plannification/Modification", name="Modif_Polym_Pla")
     * @Security("has_role('ROLE_REGLEUR')")
     */
    public function ModifPolymPla(Request $request)
    {
        //Si c'est le retour de la requette AJAX, on récupère les données
        if($request->isXmlHttpRequest()) {

            $idPolymPla = $request->request->get('id');
            //$action->setFinDate(new \Datetime($requette->get('Hfin')));
            //dump($idPolymPla);//die();
            if($idPolymPla) {
                $mr = $this->getDoctrine()->getRepository(Planning::class);
                $maga = $mr->find($idPolymPla);
                //dump($maga);//die();
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($demande);
                $manager->flush();

            return new JsonResponse("Requete enregistrée");
            }
            return new Response("Pas d'id");
        }
        return new Response("Ce n'est pas une requête Ajax");
    }

	/**
     * @Route("/Utilisateurs", name="Utilisateurs")
     */
    public function Utilisateurs()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Utilisateurs.html.twig',[
            'Titres' => $Titres,
        ]);
    }
	/**
     * @Route("/Ameliorations", name="Ameliorations")
     */
    public function Ameliorations()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Ameliorations.html.twig',[
            'Titres' => $Titres,
        ]);
    }
	/**
     * @Route("/Tracabilite", name="Tracabilite")
     */
    public function Tracabilite()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Tracabilite.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/PE", name="PE")
     */
    public function PE()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'PE']);

        return $this->render('planning_mc/PE.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/PROGRAMMATION", name="PROGRAMMATION")
     * @Security("has_role('ROLE_PROGRAMMEUR')")
     */
    public function PROGRAMMATION()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        dump($Titres);
        return $this->render('planning_mc/PROGRAMMATION.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/OUTILLAGES", name="OUTILLAGES")
     */
    public function OUTILLAGE()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'OUTILLAGE']);

        return $this->render('planning_mc/OUTILLAGE.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/DATA_TOOLS", name="DATA_TOOLS")
     */
    public function DATATOOLS()
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'DATA_TOOLS']);

        return $this->render('planning_mc/DATA_TOOLS.html.twig',[
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Urgences", name="Urgences")
     */
    public function Urgences()
        {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Urgences.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/METHODES/PE/Creation", name="Creation")
     * @Route("/METHODES/PE//Modification/{id}", name="Modification")
     */
    public function Creation(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
    {
//Si pas de programmes connus, c'est une création sinon une modif
        if(!$Prog){
            $Prog=new ProgMoyens();
        }
        $form = $this->createForm(CreationProgType::class, $Prog);
        
        $form->handleRequest($Requet);
        //dump($form);
        if($form->isSubmitted() && $form->isValid()){
            if(!$Prog->getId()){
                $Prog->setDateCreation(new \datetime());
                
            }
            else{
                $Prog->setDateModif(new \datetime());
            
            }

            $manager->persist($Prog);
            $manager->flush();
            

            return $this->redirectToRoute('Consultation');
        }

        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findAll();
        //dump($Titres);
        return $this->render('planning_mc/Creation.html.twig',[
            'Titres' => $Titres,
            'formProg' => $form->createView(),
        ]);

    }

    /**
     * @Route("/METHODES/Consultation", name="Consultation")
     * @Route("METHODES/Consultation/{id}", name="Consul_ProgMoy")
     */
    public function Consultation(CategoryMoyens $moyen=null)
    {
//Si pas de moyen affecté, c'est une consulation générale des programmes        
        if(!$moyen){
            $cycles = $this->getDoctrine()
            ->getRepository(ProgMoyens::class)
            ->findAll();
        }
//Sinon par type de moyen du programme consulté
        else{$cycles = $this->getDoctrine()
            ->getRepository(ProgMoyens::class)
            ->findOneBySomeField($moyen->getId());

            dump($moyen->getId());
        }

        //$category = $cycles->getCateMoyen();}
        //dump($category);
        $moyen=new CategoryMoyens();
        $repo=$this->getDoctrine()->getRepository(CategoryMoyens::class);
        $moyen=$repo -> findall();
        dump($moyen);

        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findall();
        dump($Titres);
        dump($cycles);
        
        return $this->render('planning_mc/Consultation.html.twig',[
            'Titres' => $Titres,
            'Cycles' => $cycles,
            'Moyens' => $moyen,
        ]);
    }

    
     /**
     * @Route("/OUTILLAGE/Article/Creation", name="CreationO")
     * @Route("/OUTILLAGE/Article/Modification/{id}", name="ModificationO")
     */
    public function CreationO(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
        {$repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
            $Titres=$repo -> findall();
            dump($Titres);
        $form = $this->createForm(CreationProgType::class, $Prog);
        
        $form->handleRequest($Requet);
        
        return $this->render('planning_mc/CreationOutillages.html.twig',[
            'Titres' => $Titres,
            'formProg' => $form->createView(),
        ]);
    }

    /**
     * @Route("/OUTILLAGE/Article/Consultation", name="ConsultationO")
     * @Route("/OUTILLAGE/Article/Consultation/{id}", name="ConsulO")
     */
    public function ConsultationO(CategoryMoyens $moyen=null)
    {

    }

    /**
     * @Route("/OUTILLAGE/Article/Demandes", name="DemandesO")
     */
    public function DemandesO(Request $requette, ObjectManager $manager)
    {

    }

    /**
     * @Route("/ADMIN/Moyen/Creation", name="CreationMoyen")
     * @Route("/ADMIN/Moyen/Modification/{id}", name="ModificationMoyen")
     */
    public function CreationMoyen(Request $Requet,ObjectManager $manager,Moyens $Moyen=null)
        {
            if(!$Moyen){
                $Moyen=new Moyens();
            }
            $form = $this->createForm(CreationMoyensType::class, $Moyen);
            
            $form->handleRequest($Requet);
            //dump($form);
            if($form->isSubmitted() && $form->isValid()){
                if(!$Moyen->getId()){
                    //$Moyen->setDateCreation(new \datetime());
                    
                }
                else{
                    //$Moyen->setDateModif(new \datetime());
                
                }
    
                $manager->persist($Moyen);
                $manager->flush();
                
    
                return $this->redirectToRoute('Utilisateurs');
            }
            $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
            $Titres=$repo -> findall();
            //dump($Titres);
        $form = $this->createForm(CreationMoyensType::class, $Moyen);
        
        $form->handleRequest($Requet);
        
        return $this->render('planning_mc/CreationMoyen.html.twig',[
            'Titres' => $Titres,
            'formMoy' => $form->createView(),
        ]);
    }
    
}
