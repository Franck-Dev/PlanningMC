<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Charge;
use App\Entity\Moyens;
use App\Entity\Articles;
use App\Entity\Demandes;
use App\Entity\Planning;
use App\Entity\ChargFige;
use App\Entity\ConfSmenu;
use App\Entity\PolymReal;
use App\Entity\Chargement;
use App\Entity\ConfSsmenu;
use App\Entity\Outillages;
use App\Entity\ProgMoyens;
use App\Form\ComOutilType;
use App\Form\DatePlanning;
use App\Form\DemandesType;
use App\Form\CreationOType;
use App\Form\PolymFormType;
use App\Services\ComService;
use App\Services\FunctIndic;
use App\Entity\CategoryMoyens;
use App\Entity\TypeRecurrance;
use App\Form\CreationProgType;
use App\Entity\RecurrancePolym;
use App\Form\PlanifDemandeType;
use App\Services\FunctPlanning;
use App\Form\CreationMoyensType;
use App\Services\CallApiService;
use App\Services\FunctChargPlan;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityRepository;
use App\Repository\ChargeRepository;
use Symfony\Component\Form\FormEvent;
use App\Repository\ArticlesRepository;
use function PHPUnit\Framework\isNull;
use Symfony\Component\Form\FormEvents;
use App\Repository\ChargFigeRepository;
use App\Repository\ChargementRepository;
use App\Repository\OutillagesRepository;
use App\Repository\ProgMoyensRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DefaultRepositoryFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Length;
use Doctrine\ORM\Query\AST\Functions\LengthFunction;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//use Symfony\Component\HttpFoundation\Session\Session ;
//use Symfony\Component\Serializer\Serializer;

class PlanningMCController extends AbstractController
{
    /**
     * @Route("/Planning/Edit", name="Planning_Edit")
     * @Security("is_granted('ROLE_REGLEUR')")
     */
    public function planningEdit(Request $request, FunctPlanning $plan, ManagerRegistry $manaReg)
    {
        $repos=$manaReg->getRepository(Moyens::class);
        $moyens=$plan->moyens($repos);
        $statut=$request->request->get('state');
        $repo=$manaReg->getRepository(Planning::class);
        $repi=$manaReg->getRepository(PolymReal::class);
        $task=$plan->planning($repo, $repos, $repi, $statut);

        return new JsonResponse(['Taches'=> $task[0], 'moyen'=> $moyens[1], 'Ssmoyen'=> $moyens[0]]);
    }    
    /**
     * @Route("/Planning", name="Planning")
     */
    public function index(FunctPlanning $plan, ManagerRegistry $manaReg)
    {
    //Gestion menu
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=[];
    //Gestion des dates de la semaine concernée
        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('first day of this week');
        $currentMonthDateTime = new \DateTime();
        $lastDateTime = $currentMonthDateTime->modify('last day of this week');
    //Recherche si demande d'annulation de polym
        $repo=$manaReg->getRepository(Planning::class);
        $polyAnnul=$repo->findBy(['Statut'=>'ANNULATION']);
        foreach ($polyAnnul as $polymA) {
            $this->addFlash('warning', $polymA->getId().'/'.$polymA->getIdentification().'/'.$polymA->getDebutDate()->format('d-m-Y G:i'));
        }
        
    //Recherche des moyens à afficher sur planning
        $repos=$manaReg->getRepository(Moyens::class);
        $moyens=$plan->moyens($repos);
        $Ssmoyen= new JsonResponse($moyens[0]);
        $moyen= new JsonResponse($moyens[1]);
        $item= $moyens[2];

    //Chargement d'une variable pour les tâches déjà plannifiées
        $repi=$manaReg->getRepository(PolymReal::class);
        $task=$plan->planning($repo, $repos, $repi);
        $taches = new JsonResponse($task[0]);

        return $this->render('planning_mc/index.html.twig', [
            'controller_name' => 'Moyen Chaud',
            'Titres' => $Titres,
            'Taches' => $taches->getcontent(),
            'Moyens' => $moyen->getcontent(),
            'Ssmoyen' => $Ssmoyen->getcontent(),
            'Items' => $item
        ]);
    }
     /**
     * @Route("/PlanningMC/Creation_Polym/", name="CreaPolymRecur")
     */
    public function CreaPolymRecur(ManagerRegistry $manaReg)
    {
            
            //Chargement des dates de la semanine -1
            $currentMonthDateTime = new \DateTime();
            $firstDateTime = $currentMonthDateTime->modify('Monday next week');
            $currentMonthDateTime = new \DateTime();
            $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
            $lastDateTime = $lastDateTime->modify('23 hours');

            //Récupération des polyms récurrantes de la semaine -1    
            $repo=$manaReg->getRepository(RecurrancePolym::class);
            $ListRec=$repo ->findRecur($lastDateTime,$firstDateTime);

            //On va créer la demande et la polym à semaine +1 de chaque polym recur de la semaine -1
            foreach($ListRec as $dem){
                if(!$dem->getNumHeritage()){
                    //Création de la demande
                    $demande = new Demandes();
                    $demande=clone $dem->getNumPlanning()->getNumDemande();

                    $demande->setDatePropose($dem->getDateFinrecurrance());
                    $demande->setOutillages('');
                    $demande->setCommentaires('');

                    $manager = $manaReg->getManager();
                    $manager->persist($demande);
                    $manager->flush();
                //Récupération de l'ID de la demande pour plannifier la polym en suivant
                $DemVal = $demande->getId();
                //On créé la polym si une demande a été réalisé
                    if ($DemVal){
                        $Planning=new Planning();

                    //récupération des données de la demande pour les reporter dans la polym
                        //Formatage des dates de début
                        $heure=$demande->getHeurePropose()->format('H');
                        $minute=$demande->getHeurePropose()->format('i');
                        $seconde=$demande->getHeurePropose()->format('s');
                        $DebDate=$demande->getDatePropose()->format('Y-m-d');
                        $date = new \DateTime($DebDate);
                        $date->setTime($heure,$minute,$seconde);
                        $Planning->setDebutDate($date);

                        //Formatage de la date de fin, en récupérant la durée du cycle
                        $dated=clone($date);
                        $DureCycH=$demande->getCycle()->getDuree()->format('H');
                        $DureCycH="+".$DureCycH."Hours";
                        $DateFinH=date_modify($dated,$DureCycH);
                        $DureCycM=$demande->getCycle()->getDuree()->format('i');
                        $DureCycM="+".$DureCycM."Minutes";
                        $DateFin=date_modify($DateFinH,$DureCycM);
                        $Planning->setFinDate($DateFin);

                        $Planning->setIdentification($demande->getMoyenUtilise()->getLibelle());
                        $Planning->setAction($demande->getCycle()->getNom());
                        $Planning->setNumDemande($demande);
                        $Planning->setStatut("PLANNIFIE");
                        
                        //Intégration en base
                        $manager = $manaReg->getManager();
                        $manager->persist($Planning);
                        $manager->flush();

                        //Création de la donnée récurrance
                        $Recur = new RecurrancePolym();
                        $Recur->setTypeRecurrance($dem->getTypeRecurrance());
                        //Récupération des données du type de récurrance
                        $repo=$manaReg->getRepository(TypeRecurrance::class);
                        $Dur=$repo ->findBy(['id' =>$dem->getTypeRecurrance()]);
                        
                        $DurRec='+ '.$Dur[0]->getNbrJourCycle().'Days';
                        $NewDateRecur=date_modify($dem->getDateFinrecurrance(),$DurRec);
                        
                        $Recur->setDateFinrecurrance($NewDateRecur);
                        $Recur->setNumPlanning($Planning);

                        $manager = $manaReg->getManager();
                        $manager->persist($Recur);
                        $manager->flush();

                        //Archivage de la récurrance valider par retour du n° de la récurrance enfant
                        $repo=$manaReg->getRepository(RecurrancePolym::class);
                        $Recurrepo=$repo ->find($dem->getid());
                        $Recurrepo->setNumHeritage($Recur->getid());
                        $manager = $manaReg->getManager();
                        //$manager->persist($ModifDem);
                        $manager->flush();
                    }
                }
            }
            return $this->redirectToRoute('Planning');
    } 
     /**
     * @Route("/PlanningMC/Creation/", name="CreaDemPolymf", condition="request.isXmlHttpRequest()")
     * @IsGranted("ROLE_CE_POLYM")
     */
    public function CreaDemPolymf(Request $request,RequestStack $requestStack, userInterface $user=null, ManagerRegistry $manaReg)
    {
        
        if($requestStack->getParentRequest()){
            $request=$requestStack->getParentRequest();
            if ($request->isMethod('POST')) {
                $demande = new Demandes();
                //$demande->setDatePropose($request->get('DatePropose'));
                $TabDem=$request->request->get('form');
                //On gère si c'est une création ou une modif
                if (count($TabDem)===9){
                    //Récupération de l'objet cycle
                    $Cyc = $manaReg
                        ->getRepository(ProgMoyens::class)
                        ->findBy(['id' =>$TabDem['cycle']]);
                    $demande->setCycle($Cyc[0]);

                    //Récupération de l'objet moyen
                    $Moy = $manaReg
                        ->getRepository(Moyens::class)
                        ->findBy(['id' =>$TabDem['moyen_utilise']]);
                    $demande->setMoyenUtilise($Moy[0]);
                    
                    //Formatage de la date
                    $DatFor=$TabDem['date_propose'];
                    $lastDate=date("Y-m-d H:m:s",strtotime($DatFor['year'].'-'.$DatFor["month"]."-".$DatFor["day"]));
                    $newfindate = new \DateTime($lastDate);
                    $demande->setDatePropose($newfindate);
                    
                    //Formatage de l'heure
                    $HeurFor=$TabDem['heure_propose'];
                    $lastTime=date("H:m:s",strtotime($HeurFor['hour'].':'.$HeurFor["minute"].":00"));
                    $newfinheure = new \DateTime($lastTime);
                    $demande->setHeurePropose($newfinheure);

                    //Récupération des données outillages et commentaires
                    $demande->setOutillages($TabDem["outillages"]);
                    $demande->setCommentaires($TabDem["commentaires"]);

                    $demande->setReccurance($TabDem["Reccurance"]);

                    $demande->setDateCreation(new \datetime());
                    $demande->setPlannifie(1);
                    $demande->setRecurValide($TabDem["Reccurance"]);
                    $demande->setUserCrea($user->getUsername());
                    
                        $manager = $manaReg->getManager();
                        $manager->persist($demande);
                        $manager->flush();

                    //Récupération de l'ID de la demande pour plannifier la polym en suivant
                    $DemVal = $demande->getId();
                    //On créé la polym si une demande a été réalisé
                    if ($DemVal){
                        $Planning=new Planning();
                        //dump($demande);
                    //récupération des données de la demande pour les reporter dans la polym
                        //Formatage des dates de début
                        $heure=$demande->getHeurePropose()->format('H');
                        $minute=$demande->getHeurePropose()->format('i');
                        $seconde=$demande->getHeurePropose()->format('s');
                        $DebDate=$demande->getDatePropose()->format('Y-m-d');
                        $date = new \DateTime($DebDate);
                        $date->setTime($heure,$minute,$seconde);
                        $Planning->setDebutDate($date);
                        //dump($Planning);
                        //Formatage de la date de fin, en récupérant la durée du cycle
                        $dated=clone($date);
                        $DureCycH=$demande->getCycle()->getDuree()->format('H');
                        $DureCycH="+".$DureCycH."Hours";
                        $DateFinH=date_modify($dated,$DureCycH);
                        $DureCycM=$demande->getCycle()->getDuree()->format('i');
                        $DureCycM="+".$DureCycM."Minutes";
                        $DateFin=date_modify($DateFinH,$DureCycM);
                        $Planning->setFinDate($DateFin);
                        //dump($Planning);
                        $Planning->setIdentification($demande->getMoyenUtilise()->getLibelle());
                        $Planning->setAction($demande->getCycle()->getNom());
                        $Planning->setNumDemande($demande);
                        $Planning->setStatut("PLANNIFIE");
                        //dump($Planning);

                        $manager = $manaReg->getManager();
                        $manager->persist($Planning);
                        $manager->flush();

                        //return $this->redirectToRoute('Planning');
                        //Si polym avec un recurrance, création de cette dernière
                        if($demande->getRecurValide() == "true"){
                            $recur= new RecurrancePolym;
                            //Récupération du type de récurrence(à modifier pour automatisation)
                            $TypRecur = $manaReg
                            ->getRepository(TypeRecurrance::class)
                            ->findBy(['Type' => 'HEBDO']);

                            $recur->setTypeRecurrance($TypRecur[0]);
                            $recur->setNumPlanning($Planning);
                            //récupération du nombre de jour et création de la variable
                            $DureRecTyp=$TypRecur[0]->getNbrJourCycle();
                            $varCycle='+ '.$DureRecTyp.'days';
                            $NewDate=date_modify($Planning->getDebutDate(),$varCycle);
                            $recur->setDateFinrecurrance($NewDate);

                            $manager = $manaReg->getManager();
                            $manager->persist($recur);
                            $manager->flush();
                        }
                    }
                    $request->getSession()->getFlashbag()->add('success', 'Votre polym a été enregistré.');
                    //return $this->redirectToRoute('demandes');
                }else{
                    $planning = new Planning();
                    //$demande->setDatePropose($request->get('DatePropose'));
                    //Récupération de l'objet polym suivant l'id concerné
                    $PolymPla = $manaReg
                        ->getRepository(Planning::class)
                        ->findBy(['id' =>$TabDem['id']]);
                    $planning=$PolymPla[0];

                    $demande= $planning->getNumDemande();
                    $demande->setOutillages($TabDem["num_demande"]["Outillages"]);
                    $demande->setCommentaires($TabDem["num_demande"]["Commentaires"]);

                    $planning->setStatut($TabDem['statut']);

                    $manager = $manaReg->getManager();
                    $manager->persist($planning);
                    $manager->flush();
                }            
            }
        }
        return new JsonResponse(['Message'=>"Modification de l'item n° effectuée avec succès",'Code'=>200]);
    }

     /**
     * @Route("/PlanningMC/Creation/{id}", name="Polym_Crea", condition="request.isXmlHttpRequest()")
     */
    public function creapolym(Request $request,Demandes $demande=null, ManagerRegistry $manaReg)
    {

    $Planning = new Planning();
    $moyen = new Moyens();

        //On verifie si c'est une création ou une modification
        if($demande->getPlannifie()===false) {
            //C'est une création
            //dump($request);
            //Récupération de l'ID moyen suivant nom
            $moyen = $manaReg
                ->getRepository(Moyens::class)
                ->findBy(['Libelle' => $request->get('Moyen')]);
            //dump($moyen);
            if (!$moyen){
                $demande->setMoyenUtilise(NULL);
            }
            else{
                $demande->setMoyenUtilise($moyen[0]);
            }       
            //Récupération de l'heure de début de l'action
            //$demande->setDatePropose(new \Datetime($request->get('Hdeb')));
            //Récupération de l'heure de fin de l'action
            //$demande->setDateHeureFin(new \Datetime($request->get('Hfin')));
            //Récupération du statut
            $demande->setPlannifie($request->get('Statut'));
            //Récupération des données de la requette
            $debdate=new \Datetime($request->request->get('Hdeb'));
            $findate=new \Datetime($request->request->get('Hfin'));
            $moyen=$request->request->get('Moyen');
            $cycle=$request->request->get('Cycle');
                //Intégration des données dans l'objet
                $Planning->setDebutDate($debdate);
                $Planning->setFinDate($findate);
                $Planning->setIdentification($moyen);
                $Planning->setAction($cycle);
                $Planning->setNumDemande($demande);
                $Planning->setStatut("PLANNIFIE");
                //Création dans la table appropriée
                $manager = $manaReg->getManager();
                $manager->persist($Planning);
                $manager->flush();
                //On verifie si la récurrance existe pour modification si oui
                $RecurD = $manaReg
                    ->getRepository(RecurrancePolym::class)
                    ->findBy(['NumPlanning' => $Planning->getid()]);
                if(!$RecurD){
                //Création du numéro de Recurrance dans la table de ce dernier si besoin
                    if($request->get('Reccurance')=="true"){
                        $recurP = new RecurrancePolym();
                        //Récupération du type de récurrence(à modifier pour automatisation)
                        $TypRecur = $manaReg
                        ->getRepository(TypeRecurrance::class)
                        ->findBy(['Type' => 'HEBDO']);

                        $recurP->setTypeRecurrance($TypRecur[0]);
                        $recurP->setNumPlanning($Planning);
                        //récupération du nombre de jour et création de la variable
                        $DureRecTyp=$TypRecur[0]->getNbrJourCycle();
                        $varCycle='+ '.$DureRecTyp.'days';
                        $NewDate=date_modify($Planning->getDebutDate(),$varCycle);
                        $recurP->setDateFinrecurrance($NewDate);

                        $manager = $manaReg->getManager();
                        $manager->persist($recurP);
                        $manager->flush();
                        $demande->SetRecurValide(1);
                        $StatRecur=true;
                    }
                    else{
                        $demande->SetRecurValide(0);
                        $StatRecur=false;
                    }
                }
                else{
                    $recurP = new RecurrancePolym($RecurD);
                    $manager = $manaReg->getManager();
                    $manager->remove($RecurD->getNumPlanning());
                    $manager->flush();

                    $recurP = new RecurrancePolym();
                    $TypRecur = $manaReg
                        ->getRepository(TypeRecurrance::class)
                        ->findBy(['Type' => 'HEBDO']);
                    $recurP->setTypeRecurrance($TypRecur[0]);
                    $recurP->setNumPlanning($Planning);
                    $recurP->setDateFinrecurrance(new \Datetime("9999-12-30"));

                    $manager = $manaReg->getManager();
                    $manager->persist($recurP);
                    $manager->flush();
                    $demande->SetRecurValide(1);
                    $StatRecur=true;
                    
                }
                //Création dans les tables appropriés
                $manager = $manaReg->getManager();
                $manager->persist($demande);
                $manager->flush();

                //Récupération de l'ID moyen suivant nom
                $Planning = $manaReg
                ->getRepository(Planning::class)
                ->findBy(['NumDemande' => $request->get('id')]);
                
            return new JsonResponse(['StatRecur'=> $StatRecur, 'Message'=>"Création de l'item n°".$Planning[0]->getId()." effectuée avec succès",'Code'=>200]);
        }
        else{
            //C'est une modification
            $demande->setMoyenUtilise(NULL);
            $demande->setPlannifie($request->get('Statut'));
            //On verifie si la récurrance existée avant la modif
            //dump($demande->getPlanning()->getid());
            $RecurD = $manaReg
            ->getRepository(RecurrancePolym::class)
            ->findBy(['NumPlanning' => $demande->getPlanning()->getid()]);
                //Donne s'il y a une recurrance ou pas            
                if($request->get('Reccurance')=="true"){
                    if($RecurD){
                        $RecurDel= new RecurrancePolym($RecurD[0]);
                        $RecurDel=$RecurD[0];
                        
                        $manager = $manaReg->getManager();
                        $manager->remove($RecurDel->getNumPlanning());
                        $manager->flush();
                        $StatRecur=true;
                    }
                    else{
                        $StatRecur=false;
                    }
                }
                else{           
                    $manager = $manaReg->getManager();
                    $manager->remove($demande->getPlanning());
                    $manager->flush();
                    $StatRecur=false;
                }
            $DemRecur=$demande->getreccurance();
            return new JsonResponse(['StatRecur'=> $StatRecur,'DemRecur'=>$DemRecur,'Message'=>"Modification de l'item n°".$request->get('id')." effectuée avec succès",'Code'=>200]);
        }
        
    }

    /**
     * @Route("/PlanningMC/", name="Polym_Edit", condition="request.isXmlHttpRequest()")
     * @IsGranted("ROLE_PLANIF")
     */
    public function editpolym(Request $request, ManagerRegistry $manaReg)
    {
        $demande = new Demandes();

        //Récupération des données de la requette
        $demande->setDatePropose(new \Datetime($request->get('DateDeb')));
        
        $lastDateTime=date("Y-m-d H:m:s",strtotime($request->get('Hdeb')));
        $newfindate = new \DateTime($lastDateTime);

        $demande->setHeurePropose($newfindate);
        //Récupération de l'objet moyen
        $Moy = $manaReg
        ->getRepository(Moyens::class)
        ->findBy(['id' =>$request->get('Moyen')]);
        $demande->setMoyenUtilise($Moy[0]);

       $form = $this -> createFormBuilder($demande)
                      ->add('moyen_utilise', EntityType::class, array(
                        'class' =>Moyens::class,
                        'query_builder' => function ( EntityRepository $er ) {
                            return $er -> createQueryBuilder ( 'u' )
                            ->Where('u.Id_Service = 8')
                            ->andWhere('u.Activitees = :val')
                            ->setParameter('val', "Plannifie" );
                        },
                        'choice_label' => 'libelle',))
                    -> add('cycle', EntityType::class, [
                          'class' => ProgMoyens::class,
                          'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->orderBy('u.Nom', 'ASC');
                        },
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
                      ->add('save', SubmitType::class, ['label' => 'Valider'])
                      ->getForm();
        $form->handleRequest($request);
        //dump($form);
        //Pas de menu pour la fenêtre modale, juste le retour à l'accueil
        $Titres=[];
        return $this->render('planning_mc/editpolym.html.twig',[
            'controller_name' => 'Moyen Chaud',
           'Titres' => $Titres,
           'form' => $form->createView()
        ]);
    }
    
     /**
     * @Route("/PlanningMC/Modification", name="Polym_Modif")//, condition="request.isXmlHttpRequest()"
     * @IsGranted("ROLE_REGLEUR")
     */
    public function polymodif(Request $request, ManagerRegistry $manaReg)
    {
        if ($request->isXmlHttpRequest() == false) {
            $user = $this->getUser();
            $repo=$manaReg->getRepository(Planning::class);
            $planning=$repo->findOneBy(['id'=>$request->query->get('id')]);
            $planning->setStatut('ANNULER');
            $jour=new \Datetime;
            $jour=$jour->format('d/m/Y G:i');
            $commentaire=$planning->getNumDemande()->getCommentaires().'.Et annulé par : '.$user->getUsername().' le : '. $jour;
            $planning->getNumDemande()->setCommentaires($commentaire);
            $planning->getNumDemande()->setDateModif(new \DateTime());
            $planning->getNumDemande()->setUserModif($user->getUsername());

            $manager = $manaReg->getManager();
            $manager->persist($planning);
            $manager->flush();
            
            return $this->redirectToRoute('Planning');

        } else {
            //Remonté d'info pour modification du statut de la polymérisation
            $idPolym=substr($request->get('PolymId'),1,strlen($request->get('PolymId'))-1);
            //dump($idPolym);
            $PolymPla = $manaReg
                ->getRepository(Planning::class)
                ->findBy(['id' => $idPolym]);
            //$planning->setMoyenUtilise($PolymPla[0]);
            //dump($PolymPla[0]);
            $form = $this -> createFormBuilder($PolymPla[0])
            ->add('id', HiddenType::class, [
                'label' => 'ID',
            ])
            ->add('identification', TextType::class, [
                'label' => 'Moyen',
                'disabled'=>true,
            ])
            ->add('action', TextType::class, [
                'label' => 'Programme',
                'disabled'=>true,
            ])
            -> add('num_demande', ComOutilType::class)
            -> add('debut_date', DateTimeType::class,['disabled'=>true])
            -> add('fin_date', DateTimeType::class,['disabled'=>true])
            -> add('statut', ChoiceType::class, [
                'choices'  => [
                    'PLANNIFIE' => 'PLANNIFIE',
                    'ANNULER' => 'ANNULER',
                    'REMPLACER' => 'REMPLACER',
                ]])
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();
            $form->handleRequest($request);
            //dump($form);
            $Titres=[];
            return $this->render('planning_mc/editpolym.html.twig',[
                'controller_name' => 'Moyen Chaud',
            'Titres' => $Titres,
            'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/PlanningMC/ModificationPolym", name="CreaDemPolym")
     * @IsGranted("ROLE_REGLEUR")
     */
    public function CreaDemPolym(Request $request,RequestStack $requestStack, userInterface $user=null, ManagerRegistry $manaReg)
    {
        //Modification du statut de la polym
        if($requestStack->getParentRequest()){
            
            $request=$requestStack->getParentRequest();
            if ($request->isMethod('POST') and $request->request->get('form')) {
                $demande= new Demandes();
                $planning = new Planning();
                //$demande->setDatePropose($request->get('DatePropose'));
                $TabDem=$request->request->get('form');
                //dump($TabDem);
                //Récupération de l'objet cycle
                $PolymPla = $manaReg
                    ->getRepository(Planning::class)
                    ->findBy(['id' =>$TabDem['id']]);
                $planning=$PolymPla[0];
                $planning->setStatut($TabDem['statut']);

                $demande=$planning->getNumDemande();
                $demande->setOutillages($TabDem["num_demande"]["Outillages"]);
                $demande->setCommentaires($TabDem["num_demande"]["Commentaires"]);

                $manager = $manaReg->getManager();
                $manager->persist($planning);
                $manager->flush();
                    $request->getSession()->getFlashbag()->add('success', 'Création de la polym n°' . $planning->getId() . ' réalisée');
            }
        }
        
        return new JsonResponse(['Message'=>"Vous n'avez pas les droits pour créer une polym",'Code'=>404]);
    }
    
     /**
     * @Route("/PlanningMC/Suppression", name="Polym_Del", condition="request.isXmlHttpRequest()")
     * @IsGranted("ROLE_PLANIF")
     */
    public function deletepolym(Request $request, ManagerRegistry $manaReg)
    {
        //Suppression de la polym
        dump($request->get('PolymId'));
        if($request->get('PolymId')){
            if ($request->isMethod('POST')) {
                $planning = new Planning();
                $idPolym=substr($request->get('PolymId'),1,strlen($request->get('PolymId'))-1);
                dump($idPolym);
                $PolymPla = $manaReg
                    ->getRepository(Planning::class)
                    ->findBy(['id' => $idPolym]);
                $planning=$PolymPla[0];

                //On va rechercher la demande liée pour modifier son statut et la récurrence si besoin
                $Demande = new Demandes();
                $Dem = $manaReg
                    ->getRepository(Demandes::class)
                    ->findBy(['id' => $planning->getNumDemande()]);
                $Demande = $Dem[0];

                $manager = $manaReg->getManager();
                $manager->remove($planning);
                $manager->flush();

                //Si la demande à une récurrence on la supprime
                $Demande->setRecurValide(0);
                $Demande->setPlannifie(0);
                $Demande->setMoyenUtilise(Null);
                dump($planning);

                $manager = $manaReg->getManager();
                    $manager->persist($Demande);
                    $manager->flush();
                    dump($planning);
            }
        }
        return new JsonResponse(['Message'=>"Vous n'avez pas les droits pour créer une polym",'Code'=>404]);
    }

	/**
     * @Route("/home", name="home")
     */
    public function home(CallApiService $callApiService, ManagerRegistry $manaReg)
    {
        //dd($callApiService);
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();
        
        //Recherche du début de l'année avec n+1 mois pour effectuer les calcul des indicateurs         
        $DateAnCours = date("l", strtotime('first day of January '.date('Y') ));
        $dateAn= new \datetime();
        $dateAn->modify('First day of january this year'); 
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
        $SemDer=date("W", strtotime('- 1 week '.date('Y') ));
        //Tps de capacité machine en 3x8 VSD SD
        $TpsOuv=intval(24*7*11);
        //Recherche sur x semaines dans le passé
        $SemAvant = date("Y-m-d", strtotime('- 10 weeks'.date('Y') ));
        $SemAvant=new \datetime($SemAvant);


        return $this->render('planning_mc/home.html.twig',[
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
            'dateAn' => $dateAn,
            'Annee' => $Annee,
            'Jour' => $hier,
            'Semaine' => $NumSem,
            'SemDer' => $SemDer,
            'FinSem' => $FinSem,
            'DebSem' => $DebSem,
            'DateJour' => $DateJour,
            'SemAvant' => $SemAvant
        ]);
        
    }

	/**
     * @Route("/Demandes/Creation", name="Crea_Demandes")
     * @Route("/Demandes/Modification/{id}", name="Modif_Demandes")
     * @IsGranted("ROLE_CE_MOULAGE")
     */
    public function DemandesCrea( Request $requette,
    RequestStack $requestStack,
    EntityManagerInterface $manager,
    ManagerRegistry $manaReg,
    ComService $com,
    Demandes $demande=null,
    $datejour=null,
    userInterface $user=null)
    {
        if (!$demande) {
            $demande= new Demandes();
            $demande->setDatepropose(new \Datetime($requette->get('datejour')));
        } else {
            # code...
        }
        $form = $this->createForm(DemandesType::class, $demande);
        $form->handleRequest($requette);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($demande->getListOF());
            if(!$demande->getId()){
                $demande->setDateCreation(new \datetime());
                $demande->setPlannifie(0);
                $demande->setRecurValide(0);
                $demande->setUserCrea($user->getUsername());
                //$demande->addListOF($demande->getListOF());                   
            }
            else{
                $demande->setDateModif(new \datetime());
                //dump($user);
                $demande->setUserModif($user->getUsername());
            }
            //Enregistrement de la demande
            $manager = $manaReg->getManager();
            $manager->persist($demande);
            $manager->flush();
            
            $com->sendNotif('La demande n° '. $demande->getId() . ' a bien été enregistré.');
            //$requette->getSession()->getFlashbag()->add('success', 'La demande'. $demande->getId() . 'a bien été enregistré.');
                
                return $this->redirectToRoute('Demandes');
        }

        $Titres=[];
            
        if (!$demande->getId()){
            return $this->render('planning_mc/CreationDemandes.html.twig',[
                'Titres' => $Titres,
                'formDemande' => $form->createView()
             ]);
        } else {
            return $this->render('planning_mc/ModificationDemandes.html.twig',[
                'Titres' => $Titres,
                'formDemande' => $form->createView()
             ]);
        } 
    }

    /**
     * @Route("/Demandes", name="Demandes")
     */
    public function Demandes(Request $requette,
    EntityManagerInterface $manager,
    userInterface $user=null,
    ManagerRegistry $manaReg)
    {
        
//Recherche des dates de la semaine encours pour les demandes avec récurrences
        if (!$requette->get('DatedebPlan')){
            //Recherche du début et fin de la semaine n+1  pour effectuer les demandes de créneaux         
            $currentMonthDateTime = new \DateTime();
            $firstDateTime = $currentMonthDateTime->modify('Monday next week');
            $currentMonthDateTime = new \DateTime();
            $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
            $lastDateTime = $lastDateTime->modify('23 hours');
        }
        else{
            //$firstDateTime=date("Y-m-d",strtotime($requette->get('DatedebPlan')));
            $firstDateTime=new \Datetime($requette->get('DatedebPlan'));
            $currentMonthDateTime = clone($firstDateTime);
            $lastDateTime = $currentMonthDateTime->modify('Sunday this week');
            $lastDateTime = $lastDateTime->modify('23 hours');
        }

//Visualisation des demandes en cours
    $demande=new Demandes();  
        if(!$demande){
            $cycles = $manaReg
            ->getRepository(Demandes::class)
            ->findDemSem( $firstDateTime,$lastDateTime);
        }
        else{
            $cycles = $manaReg
            ->getRepository(Demandes::class)
            ->findDemSem( $firstDateTime,$lastDateTime);
        }
    //Recherche des moyens à afficher sur planning
        $repos=$manaReg->getRepository(Moyens::class);
        $moyens=$repos -> findBy(['Id_Service' => '8','Activitees' => 'Plannifie']);
        $item=$moyens;
        //$serializer = new Serializer();
        //$jsonContent = $serializer->serialize($moyen, 'json');        
        $data = [];
        $i = 0;
        foreach($moyens as $moyen){
            $color=$i % 2 ? '#1e90ff':'#DDDDDD';
            $data[$i] = ['id'=> $moyen->getId(),  'content'=> $moyen->getLibelle(), 'style'=> 'background:'.$color];
            $i = $i + 1;
            //On affecte un élément $item à $data
        }
        $moyen= new JsonResponse($data);
//Chargement d'une variable pour la réalisation de la nav-bar du menu et des sous-titres
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();
//Chargement d'une variable pour les tâches déjà plannifiées
    $repo=$manaReg->getRepository(Planning::class);
    $Taches=$repo -> myFindByDays($firstDateTime,$lastDateTime);
    //$item=array();
        $data = [];
        $i = 0;
        foreach($Taches as $tache){ 
            $commentaires=$tache->getNumDemande()->getCommentaires()."/".$tache->getNumDemande()->getOutillages();
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification(),'Activitees'=> 'Plannifie']);           
            $data[$i] = ['id'=> $i,'programmes'=> $tache->getAction(),'statut'=>$tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires];
            $i = $i + 1;
        }
//Chargement des polyms réalisées
    $repo=$manaReg->getRepository(PolymReal::class);
    //$Polyms=$repo -> findAll();
    $Polyms=$repo->myFindByDays($firstDateTime);
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $i,'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
            $i = $i + 1;
        }
        $taches= new JsonResponse($data);
//récupération des demandes récurrantes de la semaine dernière

    $repo=$manaReg->getRepository(Demandes::class);
    if(!$requette->get('UtilisateursCE')){
        $DemRec=$repo -> findBy(['Reccurance'=>'1','UserCrea'=>$user->getUserIdentifier(),'Plannifie'=>'1','RecurValide'=>'0']);
    }
    else{
        $DemRec=$repo -> findBy(['Reccurance'=>'1','UserCrea'=>$requette->get('UtilisateursCE'),'Plannifie'=>'1','RecurValide'=>'0']);
    }
    //$DemRec=$repo ->findDemRecur($lastDateTime,$firstDateTime,$user->getUsername());
//Récupération des CE du moulage pour listing
    $repo=$manaReg->getRepository(User::class);    
    $utilisateurs=$repo -> findByRole('ROLE_CE_MOULAGE');
//Transfert des variables à la vue
        return $this->render('planning_mc/Demandes.html.twig',[
           'Titres' => $Titres,
           'datedeb' => $firstDateTime,
           'datefin' => $lastDateTime,
           'Cycles'=>$cycles,
           'Moyens' => $moyen->getContent(),
           'taches' => $taches->getContent(),
           'DemRec' => $DemRec,
           'utilisateurs' => $utilisateurs,
           'reqet' => $requette
        ]);   
    }

    /**
     * @Route("/Demandes/Supression/{id}", name="Sup_Demandes")
     */
    public function demandeSup(EntityManagerInterface $manager,
    Demandes $demande=null,
    userInterface $user=null,
    ManagerRegistry $manaReg,
    ComService $com)
    {
        $manager = $manaReg->getManager();
            $manager->remove($demande);
            $manager->flush();

            $com->sendNotif('La demande n° '. $demande->getId() . ' a bien été supprimée.');
            $message="<p>Supression de la demande n° ". $demande->getId()." du ".$demande->getDatePropose(). " ". $demande->getHeurePropose()."</p>";
            $com->sendEmail($user->getMail(),'f.dartois@daher.com', 'La demande n° '. $demande->getId() . ' a bien été supprimée.', $message );

        return $this->redirectToRoute('Demandes');
    }

    /**
     * @Route("/Demandes/Deprogrammation/{id}", name="Deprog_Demandes")
     */
    public function demandeDeprog(Demandes $demande=null,
    userInterface $user=null,
    ManagerRegistry $manaReg,
    ComService $com)
    {
        //On récupère le CE polym
        $repo=$manaReg->getRepository(User::class);
        //$CEPolym= new User;
        //$CEPolym=$repo->findBy(['Roles'=> [0=>"ROLE_CE_POLYM"]]);
        
        //Envoyer un mail pour faire la demande de modification
        $datePolym=$demande->getPlanning()->getDebutDate()->format('l Y-m-d H:i:s');
        $message="<p>Deprogrammation de la polymerisation n° ". $demande->getPlanning()->getId()." du ".$datePolym."</p>";
        $subject='Demande de deprogrammation cycle : '.$demande->getCycle()->getNom();
        //TODO : Faire la recherche du mail du chef d'équipe des moyens chauds et responsables
        $CEPolym="f.dartois@daher.com";
        $com->sendEmail($user->getMail(),$CEPolym,$subject,$message);

        //Envoie notification flash
        $com->sendNotif('Votre message concernant l\'annulation de la demande n°'.$demande->getId(). ' a été transmis, nous vous répondrons dans les meilleurs délais.',['browser']);
        //$this->addFlash($canal,$message); // Permet un message flash de renvoi
        
        //Mettre commentaire sur la demande pour tracer la modification
        $comment=$demande->getCommentaires();
        $jour=new \datetime();
        $now=$jour->format('d/m/Y H:i');
        $demande->setCommentaires($comment."Demande d'annulation faite par ".$user->getUsername()." le : ".$now);
        $manager = $manaReg->getManager();
        $manager->persist($demande);
        $manager->flush();

        //Mettre le statut ANNULER sur la polym
        $repoPla=$manaReg->getRepository(Planning::class);
        $polPla=new Planning;
        $polPla=$repoPla->findOneBy(['NumDemande'=>$demande->getId()]);
        $polPla->setStatut('ANNULATION');

        $manager->persist($polPla);
        $manager->flush();

        return $this->redirectToRoute('Demandes');
    }

    /**
     * @Route("/Demandes/Plannification/{id}", name="Planif_Demandes")
     * @IsGranted("ROLE_PLANIF")
     */
    public function DemandesPlanif( Request $requette,
    EntityManagerInterface $manager,
    Demandes $demande=null, 
    ValidatorInterface $validator, 
    ManagerRegistry $manaReg,
    ComService $com)
    {
        $action= new Planning();
        $cycles= new ProgMoyens();
        $moyen= new Moyens();
        //dump($demande->getId('id'));
//Récupération de la demande suivant ID
        $cycles = $manaReg
                ->getRepository(ProgMoyens::class)
                ->findBy(['id' => $demande->getCycle('id')]);
        //dump($cycles[0]->getNom($demande->getCycle('id')));
//Récupération de l'ID moyen suivant nom
        $moyen = $manaReg
            ->getRepository(Moyens::class)
            ->findBy(['Libelle' => $requette->get('Moyen'.$demande->getId('id'))]);
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
        $form->submit($form->getName());
        $errors = $validator -> validate ($form); 
 
//On vérifie la validité des données avant de persister en base
        if ( $form -> isSubmitted () ) {
            if(!$demande->getId()){
                $demande->setDateCreation(new \datetime());
                 $mode=true;
            }
            else{
                $demande->setDateModif(new \datetime());
                $mode=false;
            }
            $manager = $manaReg->getManager();
            if(!$demande->getPlanning()){
                $manager->persist($action);
                $manager->flush();
                $manager->persist($demande);
                $manager->flush();
            }
            else{
                $manager->remove($demande->getPlanning());
                $manager->flush();
            }
            $com->sendNotif('Votre polym a été enregistré.',['sucess']);
            //$requette->getSession()->getFlashbag()->add('success', 'Votre polym a été enregistré.');
                
            if($mode==false){
                    
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
     * @Route("/Demandes/Charge_Fige/{id}/OF", name="list_OT_chargement", methods={"POST"})
     * @IsGranted("ROLE_CE_MOULAGE")
     */
    Public function checkOFOTsvtCTO(Request $request, FunctChargPlan $chargeFige, ChargFigeRepository $cata, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art)
    {
        // On va récupérer le CTO correspondant à l'id
        $CTO=$cata->findOneBy(['id' => $request->attributes->get('id')]);
        //Intégration dans une liste de 1 CTO
        $listCTO[0]=$CTO;
        //Contruction du créno à chercher $Creno['Jour']), $Creno['Cycles']
        $creno['Jour']= new \datetime($request->get('DateJour'));
        $creno['Cycles']=$CTO->getProgramme()->getNom();
        $creno['NbrPcs']=1;
        $listCreno[0]=$creno;
        
        //Récupération de la liste des outillages de ce CTO
        $test=$chargeFige->checkOTCTO($listCTO, $repo, $Out, $Art, $creno);

        return $this->render('planning_mc/form/_form_tbDatasCharg.html.twig', [
            'Datas' => $test[0]['Contenu'],
            'CTO' => $test
        ]);
    }

    /**
     * @Route("/Demandes/Charge_Fige/Modification/OFsOT", name="modif_OFs_OT", methods={"GET"})
     * @IsGranted("ROLE_CE_MOULAGE")
     */
    Public function modifOFsOT(Request $request, FunctChargPlan $chargeFige, ChargFigeRepository $cata, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art)
    {
        dd($request);
    }

    /**
     * @Route("/Demandes/Charge_Fige/Modification/OFOT", name="modif_OF_OT", methods={"GET"})
     * @IsGranted("ROLE_CE_MOULAGE")
     */
    Public function modifOFOT(Request $request, FunctChargPlan $chargeFige, ChargFigeRepository $cata, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art)
    {
        $listOF= $repo->findBy(['ReferencePcs' => $request->get('Ref'), 'Statut' => 'OUV']);

        foreach ($listOF as $key => $OF) {
            $deltaJours=date_diff(clone($OF->getDateDeb()), new \datetime());
            $listOFs[$key] = ['OF' => strval($OF->getOrdreFab())];
            $listHorizon[$key] = [$deltaJours->format('%a')];
        }

        return $this->render('planning_mc/form/_select.html.twig',[
            'listOFs' => $listOFs,
            'art' => $request->get('Ref'),
            'listHorizon' => $listHorizon
        ]);
    }

    /**
     * @Route("/Demandes/Charge_Fige/Supression/OT", name="sup_OT", methods={"GET"})
     * @IsGranted("ROLE_CE_MOULAGE")
     */
    Public function supOT(Request $request, FunctChargPlan $chargeFige, ChargFigeRepository $cata, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art)
    {

    }

    /**
     * @Route("/Demandes/Charge_Fige/Visualisation", name="affichagePlanningMoul", methods={"GET"})
     * @IsGranted("ROLE_CE_MOULAGE")
     */
    Public function affichagePlanningMoul(Request $request, FunctChargPlan $chargeFige, ChargFigeRepository $cata, ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art)
    {

    }

	/**
     * @Route("/Planification", name="Planification")
     * @IsGranted("ROLE_PLANIF")
     */
    public function Planification(request $requette,Demandes $demande=null, FunctIndic $indic, ManagerRegistry $manaReg)
    {
//Création des indicateurs
    //Recherche du début et de la fin de semaine à plannifier
        $currentMonthDateTime = new \DateTime();
        $FinSem = $currentMonthDateTime->modify('sunday next week');
        $currentMonthDateTime = new \DateTime();
        $DebSem = $currentMonthDateTime->modify('monday next week');
    //Création de la variable charge totale sur la semaine
        $repo=$manaReg->getRepository(Planning::class);
        $CharTot= $indic->chargTot($repo, $FinSem, $DebSem);

    $TpsOuvParMach=intval(24*7);
        
    //Création de la variable charge de chaque machine sur la semaine encours
       $result=$indic->chargMachsem($repo, $FinSem, $DebSem, $TpsOuvParMach, $CharTot);
       $ChargeMoy= new JsonResponse($result[0]);
       $ReparCharg= new JsonResponse($result[1]);
        
//Recherche du début de semaine et fin de semaine
    if(!$requette->get('DatedebPlan')){
        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('Monday next week');
        $currentMonthDateTime = new \DateTime();
        $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
        $lastDateTime = $lastDateTime->modify('23 hours');
    }
    else{
        $firstDateTime=new \DateTime(date("Y-m-d",strtotime($requette->get('DatedebPlan'))));
        $currentMonthDateTime =clone($firstDateTime);
        $lastDateTime = $currentMonthDateTime->modify('Sunday this week');
        $lastDateTime = $lastDateTime->modify('23 hours');
    }
//Chargement d'une variable pour toutes les demandes créées
    $test = $manaReg
    ->getRepository(demandes::class)
    ->myFindByDays($firstDateTime);

//Recherche des moyens à afficher sur planning
        $repos=$manaReg->getRepository(Moyens::class);
        $moyens=$repos -> findBy(['Id_Service' => '8','Activitees' => 'Plannifie']);
        $item=$moyens;
        //$serializer = new Serializer();
        //$jsonContent = $serializer->serialize($moyen, 'json');        
        $data = [];
        $i = 0;
        foreach($moyens as $moyen){
            $color=$i % 2 ? '#1e90ff':'#DDDDDD';
            $data[$i] = ['id'=> $moyen->getId(),  'content'=> $moyen->getLibelle(), 'style'=> 'background:'.$color];
            $i = $i + 1;
			//On affecte un élément $item à $data
        }
        $moyen= new JsonResponse($data);
//Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$manaReg->getRepository(Planning::class);
        $Taches=$repo -> findBy([],['DebutDate'=>'DESC'],3000);
        //$item=array();

        $data = [];
        $i = 0;
        $Pourc=10;
        foreach($Taches as $tache){
            $commentaires=$tache->getNumDemande()->getCommentaires()."/".$tache->getNumDemande()->getOutillages();
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification(),'Activitees'=> 'Plannifie']);
            if($Pourc<75){
                $data[$i] = ['id'=> '1'.$tache->getId(),'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires,'visibleFrameTemplate' => '<div class="progress-wrapper"><div class="progress" style="width:'.$Pourc.'%; background:red"></div><label class="progress-label">'.$Pourc.'%<label></div>'];
            }
            else{
                $data[$i] = ['id'=> '1'.$tache->getId(),'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $commentaires, 'visibleFrameTemplate' => '<div class="progress-wrapper"><div class="progress" style="width:'.$Pourc.'%"></div><label class="progress-label">'.$Pourc.'%<label></div>'];
            }
            $i = $i + 1;
        }
        $taches= new JsonResponse($data);
        
//Chargement des éléments du nav-bar menu
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();
//Envoie au template Plannification
        return $this->render('planning_mc/Planification.html.twig',[
            'Titres' => $Titres,
            'tests' => $test,
            'datedeb' => $firstDateTime,
            'datefin' => $lastDateTime,
            'taches' => $taches->getContent(),
            'moyens' => $moyen->getContent(),
            'items' => $item,
            'ChargeMoy' =>$ChargeMoy->getContent(),
            'RepartChargeMoy' =>$ReparCharg->getContent(),
            'CharTot' => $CharTot
        ]);
    }

     /**
     * @Route("/Plannification/Modification", name="Modif_Polym_Pla")
     * @IsGranted("ROLE_PLANIF")
     */
    public function Modif_Polym_Pla(Request $request, Planning $Polyms=null, ManagerRegistry $manaReg)
    {
        //Si c'est le retour de la requette AJAX, on récupère les données
        if($request->isXmlHttpRequest()) {

            //Récupération des données de la requette
            $PolymPla = $request->request->get('id');
            // Récupère l'id de la polym en enlevant le premier digit
            $idPolymPla=substr($PolymPla,1);
            $olddebdate=new \Datetime($request->request->get('olddatedeb'));
            $oldfindate=new \Datetime($request->request->get('olddatefin'));
            $idmoyen=$request->request->get('moyen');
            $firstDateTime=date("Y-m-d H:i",strtotime($request->request->get('newdatedeb')));

            $newdebdate = new \DateTime($firstDateTime);
            $lastDateTime=date("Y-m-d H:i",strtotime($request->request->get('newdatefin')));
            $newfindate = new \DateTime($lastDateTime);
            //Récupération de la désignation du moyen suivant id
            $basemoy = $manaReg->getRepository(Moyens::class);
            $moyen = $basemoy->findBy(['id' => $idmoyen]);
            //dump($idPolymPla);//die();
            if($idPolymPla) {

                $mr = $manaReg->getRepository(Planning::class);
                //$maga = $mr->findOneBySomeField($olddebdate,$oldfindate,$moyen[0]->getLibelle($idmoyen));
                $maga=$mr->findBy(['id' => $idPolymPla]);
                $old=$request->request->get('olddatedeb');
                //$new=date("Y-m-d H:m",strtotime($request->request->get('newdatedeb')));

                $maga[0]->setDebutDate($newdebdate);
                $maga[0]->setFinDate($newfindate);
                $maga[0]->getNumDemande()->setMoyenUtilise($moyen[0]);
                $maga[0]->setIdentification($moyen[0]->getLibelle());

                $manager = $manaReg->getManager();
                $manager->persist($maga[0]);
                $manager->flush();
                
            return new JsonResponse("Modification de l'item n°".$idPolymPla." effectuée avec succès");

        }
            return new Response("Pas d'id");
        }
        return new Response("Ce n'est pas une requête Ajax");
    }

	/**
     * @Route("/Utilisateurs", name="Utilisateurs")
     */
    public function Utilisateurs(ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Utilisateurs.html.twig',[
            'Titres' => $Titres,
        ]);
    }
	/**
     * @Route("/Ameliorations", name="Ameliorations")
     */
    public function Ameliorations(ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Ameliorations.html.twig',[
            'Titres' => $Titres,
        ]);
    }
	/**
     * @Route("/Tracabilite", name="Tracabilite")
     */
    public function Tracabilite(ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Tracabilite.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/PE", name="PE")
     */
    public function PE( ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'PE']);

        return $this->render('planning_mc/PE.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/PROGRAMMATION", name="PROGRAMMATION")
     * @IsGranted("ROLE_PROGRAMMEUR")
     */
    public function PROGRAMMATION( ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);

        return $this->render('planning_mc/PROGRAMMATION.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/OUTILLAGES", name="OUTILLAGES")
     */
    public function OUTILLAGE( ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'OUTILLAGE']);

        return $this->render('planning_mc/OUTILLAGE.html.twig',[
            'Titres' => $Titres,
        ]);
    }
    /**
     * @Route("/METHODES/DATA_TOOLS", name="DATA_TOOLS")
     */
    public function DATATOOLS( ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'DATA_TOOLS']);

        return $this->render('planning_mc/DATA_TOOLS.html.twig',[
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Ordonnancement", name="Ordo")
     * @IsGranted("ROLE_PLANIF")
     */
    public function Ordo(FunctChargPlan $charge, ManagerRegistry $manaReg)
        {   
        //Titres pour le menu
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();

        // Création de la charge totale SAP
        $repo=$manaReg->getRepository(Charge::class);
        $ChargTot=$repo -> findAll();
        
        // Création de la table de répartition des programmes suivant OF SAP lancés sur 1 mois
        // Date à aujourd'hui
        $jour= new \datetime;
        // Date periode
        $dateD=clone($jour);
        $dateF=clone($jour);
        $debPeriode=$dateD->modify('First day of january this year');
        $debPeriode->format('mm YY');
        $finPeriode=$dateF->modify('Last day of december this year');
        $finPeriode->format('mm YY');
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        $ChargeMois=$repo->myFindPcsTotMois($jour,$jourVisu);
       
        return $this->render('planning_mc/Ordo.html.twig', [
            'controller_name' => 'PlanningOrdo',
            'ChargeTot' => $ChargTot,
            'ChargeMois' => $ChargeMois[0],
            'Titres' => $Titres,
            'datedeb' => $jour,
            'datefin' => $jourVisu,
            'debPeriode' => $debPeriode,
            'finPeriode' => $finPeriode,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Plannification", name="PreviPlannif")
     */
    public function PreviPlannif(FunctChargPlan $charge, ManagerRegistry $manaReg)
    {
        //Création de la planification à long terme avec les chargements figés
        $repo=$manaReg->getRepository(Charge::class);
        // Création de la table de répartition des programmes suivant OF SAP lancés sur 1 mois
        // Date à aujourd'hui
        $jour= new \datetime;
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        //Récupération des chargements validés pour ces dates 
        $repoChargmnt=$manaReg->getRepository(Chargement::class);
        $ChargPlaMois=$charge->listCharg($jour, $jourVisu, $repoChargmnt, $repo);
        // $ChargPlaMois=$repoChargmnt->myFindChargtMois($jour,$jourVisu);
        // //On rajoute les OF aux données de chargement
        // $i=0;
        // foreach ($ChargPlaMois as $chargmnt) {
        //     $listOF=[];
        //     $j=0;
        //     $ListOFChargmnt=$repo->myFindOFChargmnt($chargmnt['id']);
        //     foreach ($ListOFChargmnt as $OF) {
        //         $listOF[$j]=$OF['OrdreFab'];
        //         $j++;
        //     }
        //     $ChargPlaMois[$i]['OF']=$listOF;
        //     $i++;
        // }
        //dump($ChargPlaMois);
        //Récupération de la charge SAP sur 1 mois
        $ChargeTot=$repo -> findReparChargeW($jour,$jourVisu);
        $i=0;
        dump($ChargeTot);
        //Attribution des CTO possible pour chacun des créneaux de polymérisation(Creation listCTO)
        $TbPcSsOT=[];
        $TbRepartChargeTot=[];
        $m=0;
        $nbMessErr=0;
        foreach($ChargeTot as $Creno){
            $cata=$manaReg->getRepository(ChargFige::class);
            $STD=$manaReg->getRepository(ProgMoyens::class);
            $Out=$manaReg->getRepository(Outillages::class);
            $Art=$manaReg->getRepository(Articles::class);
            $ListCTO=$charge->checkCTO($repoChargmnt, $cata, $STD, $repo, $Out, $Art, $Creno, $TbPcSsOT, $m);
            dump($ListCTO);
            $TbRepartChargeTot[$i]=$ListCTO;
            if ($ListCTO['Messages']) {
                $nbMessErr++;
            }
        $i = $i + 1;
        }
        dump($TbRepartChargeTot);

        return $this->render('planning_mc/PreviPlannif.html.twig', [
            'controller_name' => 'PlannificationSAP',
            'datedeb' => $jour,
            'datefin' => $jourVisu,
            'tests' => $TbRepartChargeTot,
            'planifie' => $ChargPlaMois,
            'nbMessErr' => $nbMessErr,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Creation/ChargeOF", name="Charge_OF", condition="request.isXmlHttpRequest()")
     */
    public function chargeOF(Request $request, ManagerRegistry $manaReg)
    {
        //Récupération des OF contenus dans la charge du jour
        $repo=$manaReg->getRepository(Charge::class);
        $listOFCharge=$repo->findBy(['DateDeb' => new \datetime($request->request->get('date')), 'NumProg' => $request->request->get('prog')]);
        
        return $this->render('planning_mc/form/_formChargeOF.html.twig', [
            'controller_name' => 'PlannificationSAP',
            'dataChargeOF' => $listOFCharge,
        ]);
    }

     /**
     * @Route("/LOGISTIQUE/Creation/Chargement", name="Chargt_Crea", condition="request.isXmlHttpRequest()")
     */
    public function chargtCrea(Request $request, EntityManagerInterface $manager, ManagerRegistry $manaReg)
    {
        $chargt= new Chargement;
        //On va récupérer le cycle machine par la désignation
        $repo=$manaReg->getRepository(ProgMoyens::class);
        $idProgram=$repo->findOneBy(['Nom'=> $request->get('cycle')]);
        dump($idProgram);
        //Création d'un tableau des OF du chargement
        $repoChar=$manaReg->getRepository(Charge::class);
        $manager = $manaReg->getManager();
        foreach ($request->get('charge') as $OT) {
            foreach ($OT as $OF) {
                $tbOF=$repoChar->FindOneBy(['OrdreFab'=> $OF['OF'],'NumProg'=> $request->get('cycle')]);
                $chargt->addOF($tbOF);
                $OF= new Charge;
                $tbOF->setStatut("PREPLAN");
                $tbOF->setDateDeb(new \datetime($request->get('jour')));
                $manager->persist($tbOF);
                //$manager->flush(); 
            }
        }

        $chargt->setNomChargement($request->get('nom'));
        $chargt->setRemplissage($request->get('remp'));
        $chargt->setProgramme($request->get('cycle'));
        $chargt->setDatePlannif(new \datetime($request->get('jour')));

        $manager = $manaReg->getManager();
        $manager->persist($chargt);
        $manager->flush();

        //Une fois le chargement créé, on valide les OF dans la charge en mettant l'ID
        $repo=$manaReg->getRepository(Charge::class);
        
        $this->addFlash('success', "Enregistrement du chargement n° ".$chargt->getId()." effectué avec succès");

        return $this->redirectToRoute('PreviPlannif');
    }

    /**
     * @Route("/LOGISTIQUE/Export/Chargement", name="Chargt_ExportSAP")
     */
    public function ChargtExport(FunctChargPlan $charge, ManagerRegistry $manaReg)
    {
        //Création de la planification à long terme avec les chargements figés
        $repo=$manaReg->getRepository(Charge::class);
        // Création de la table de répartition des programmes suivant OF SAP lancés sur 1 mois
        // Date à aujourd'hui
        $jour= new \datetime;
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        //Récupération des chargements validés pour ces dates 
        $repoChargmnt=$manaReg->getRepository(Chargement::class);

        $tbChargement=$charge->listCharg($jour, $jourVisu, $repoChargmnt, $repo);
        $myFileOFOP="OF;OP;CONF;DATE;ID;\n";
        
        foreach ($tbChargement as $Chargement) {
            foreach ($Chargement['OF'] as $OFOP) {
                $tbTempDatasOF=[];
                $tbTempDatasOF=explode("/",$OFOP);
                $myFileOFOP.= $tbTempDatasOF[0].";".$tbTempDatasOF[1].";".$tbTempDatasOF[2].";".
                $tbTempDatasOF[3].";".$tbTempDatasOF[4].";\n";
            }
            
        }

        $name="C". $jour->format("dmmY").".csv";
        return new Response(
            $myFileOFOP,
            200,
            [
          //Définit le contenu de la requête en tant que fichier texte
              'Content-Type' => 'text/plain',
          //On indique que le fichier sera en attachment donc ouverture de boite de téléchargement ainsi que le nom du fichier
              "Content-disposition" => "attachment; filename=".$name
           ]
     );
    }
    
     /**
     * @Route("/LOGISTIQUE/Delete/Chargement", name="Chargt_Delete", condition="request.isXmlHttpRequest()")
     */
    public function ChargtDelete(Request $request, EntityManagerInterface $manager, ManagerRegistry $manaReg)
    {
        $chargt= new Chargement;
        //On va récupérer le chargement suivant l'id donné
        $repo=$manaReg->getRepository(Chargement::class);
        $chargt=$repo->findOneBy(['id'=> $request->get('id')]);
        dump($chargt);
        //Suppression des OF du chargement
        $repoChar=$manaReg->getRepository(Charge::class);
        $datasOF=new Charge;
        $OFs=$repoChar->FindBy(['chargement'=>$request->get('id')]);
        foreach($OFs as $OF) {
            $chargt->removeOF($OF);
            $OF->setDateDeb($OF->getDatePilote());
            $OF->setStatut('OUV');
        }

        $manager = $manaReg->getManager();
        $manager->remove($chargt);
        $manager->flush();

        $this->addFlash('success', "Suppression du chargement n° ".$request->get('id')." effectué avec succès");

        return $this->redirectToRoute('PreviPlannif');
    }

     /**
     * @Route("/LOGISTIQUE/Creation/Datas_Chargement", name="tbDatasChargt_Crea", condition="request.isXmlHttpRequest()")
     */
    public function tbDatasChargtCrea(Request $request, ManagerRegistry $manaReg)
    {
        return $this->render('planning_mc/form/_form_tbDatasCharg.html.twig', [
            'Datas' => $request->get('chargement'),
        ]);
    }

     /**
     * @Route("/LOGISTIQUE/Creation/Datas_CTO", name="tbDatasCTO_Crea", condition="request.isXmlHttpRequest()")
     */
    public function tbDatasCTOCrea(Request $request, FunctChargPlan $charge, ManagerRegistry $manaReg)
    {
        $Out=$manaReg->getRepository(Outillages::class);
        $Art=$manaReg->getRepository(Articles::class);
        $listeOT = $Out->myFindByCharFiG($request->get('Code'));
            $TbListeOT=$charge->tboDatasCTO($listeOT);
            foreach ($TbListeOT as $OTCTO) {
                //On cherche les articles liés à chaque OT
                $ArtOFCTJ=$Art->myFindByOT($OTCTO);
                $datasCTO[$OTCTO]=$ArtOFCTJ;
            }
        dump($datasCTO);
        return $this->render('planning_mc/form/_form_tbDatasCTO.html.twig', [
            'Datas' => $datasCTO,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Urgences", name="Urgences")
     */
    public function Urgences(Request $requette, EntityManagerInterface $manager, ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Urgences.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Bloquants", name="Bloquants")
     */
    public function Bloquants(ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Bloquants.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Bloquants/OFErrConf", name="OFErrConf")
     */
    public function OFErrConf(ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(Charge::class);
        $ChargeOFDefConf=$repo -> myFindOFStatut("ERRCONF");

        return $this->render('planning_mc/form/_form_tbOFDefConf.html.twig', [
            'OFERRConf' => $ChargeOFDefConf,
        ]);
    }

    /**
     * @Route("/METHODES/PE/Consultation", name="Consultation")
     * @IsGranted("ROLE_ADMIN")
     */
    public function Consultation(CategoryMoyens $moyen=null)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/METHODES/PE/Demande_SPF", name="Demandes SPF")
     * @IsGranted("ROLE_ADMIN")
     */
    public function Demandes_SPF(CategoryMoyens $moyen=null)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/METHODES/PE/Creation_PE", name="Creation PE")
     * @Route("/METHODES/PE/Modification_PE/{id}", name="Modification_PE")
     */
    public function Creation_PE(Request $Requet,EntityManagerInterface $manager,ProgMoyens $Prog=null, ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PE']);
        
        return $this->render('planning_mc/articles/edit.html.twig',[
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/METHODES/PROGRAMMATION/Creation_PRP", name="Creation PRP")
     * @Route("/METHODES/PROGRAMMATION/Modification_PRP/{id}", name="Modification_PRP")
     */
    public function Creation_PRP(Request $Requet,EntityManagerInterface $manager,ProgMoyens $Prog=null, ManagerRegistry $manaReg)
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

        $repo=$manaReg->getRepository(ConfSsmenu::class);

        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        //dump($Titres);
        return $this->render('planning_mc/Creation.html.twig',[
            'Titres' => $Titres,
            'formProg' => $form->createView(),
        ]);

    }

    /**
     * @Route("/METHODES/PROGRAMMATION/Creation_ChargF", name="Creation Chargement Fige")
     * @Route("/METHODES/PROGRAMMATION/Modification_ChargF/{id}", name="Modification_Charge")
     */
    public function Creation_ChargeF(Request $Requet,EntityManagerInterface $manager,ProgMoyens $Prog=null, ManagerRegistry $manaReg)
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        //dump($Titres);
        
        return $this->render('planning_mc/charg_fige/edit.html.twig',[
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/METHODES/PROGRAMMATION/Consultation", name="Consultation PRP")
     * @Route("METHODES/PROGRAMMATION/Consultation/{id}", name="Consul_ProgMoy")
     */
    public function Consultation_PRP(CategoryMoyens $moyen=null, ManagerRegistry $manaReg)
    {
//Si pas de moyen affecté, c'est une consulation générale des programmes        
        if(!$moyen){
            $cycles = $manaReg
            ->getRepository(ProgMoyens::class)
            ->findAll();
        }
//Sinon par type de moyen du programme consulté
        else{$cycles = $manaReg
            ->getRepository(ProgMoyens::class)
            ->findOneBySomeField($moyen->getId());

            dump($moyen->getId());
        }

        //$category = $cycles->getCateMoyen();}
        //dump($category);
        $moyen=new CategoryMoyens();
        $repo=$manaReg->getRepository(CategoryMoyens::class);
        $moyen=$repo -> findall();
        dump($moyen);

        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
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
    public function CreationO(Request $Requet,EntityManagerInterface $manager,Outillages $OT=null, ManagerRegistry $manaReg)
        {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'OUTILLAGE']);

        if(!$OT){
            $OT=new Outillages();
            $Prog= new ProgMoyens();
            
        }
        $form = $this->createForm(CreationOType::class, $OT);
        
        $form->handleRequest($Requet);
        
        if($form->isSubmitted() && $form->isValid()){
            if(!$OT->getId()){
                $OT->setCycleMoulage(new \Datetime('06:00:00'));
                $OT->setTpsDecharge(new \Datetime('00:05:00'));
                $OT->setTpsCharge(new \Datetime('00:08:00'));
            }
            else{
                //$Prog->setDateModif(new \datetime());
            
            }
            $entityManager = $manaReg->getManager();
            $entityManager->persist($OT);
            $entityManager->flush();
            dump($OT);
                        

            //return $this->redirectToRoute('ConsultationO');
        }

        return $this->render('planning_mc/CreationOutillages.html.twig',[
            'Titres' => $Titres,
            'formProg' => $form->createView(),
        ]);
    }

    /**
     * @Route("/OUTILLAGE/Article/Consultation", name="ConsultationO")
     * @Route("/OUTILLAGE/Article/Consultation/{id}", name="ConsulO")
     */
    public function ConsultationO(Outillages $OT=null, ManagerRegistry $manaReg)
    {
        $OT=new Outillages();
        $repo=$manaReg->getRepository(Outillages::class);
        $OTs=$repo -> findall();

        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'OUTILLAGE']);
        
        return $this->render('planning_mc/ConsultationOutil.html.twig',[
            'Titres' => $Titres,
            'Outillages' => $OTs,
        ]);
    }

    /**
     * @Route("/OUTILLAGE/Article/Demandes", name="DemandesO")
     */
    public function DemandesO(Request $requette, EntityManagerInterface $manager)
    {
        return $this->redirectToRoute('home');
    }

     /**
     * @Route("/METHODES/Moyens", name="MOYENS_INDUS")
     */
    public function CreationM(Request $Requet,EntityManagerInterface $manager,ProgMoyens $Prog=null, ManagerRegistry $manaReg)
        {
        //Recherche date du jour
        $DateJour = new \DateTime();
        $jour=$DateJour->modify('today');
        $dateDebAn= new \datetime();
        $dateDebAn->modify('First day of january this year');

        //La requête remonte les moyens inférieur à 23 du service 8
        $repo=$manaReg->getRepository(Moyens::class);
        $Moyen=$repo ->findMoyens(intval('8'),intval('23'));
        $Tablo = [];
        $i = 0;
        foreach($Moyen as $moyen){
            $currentMonthDateTime = new \DateTime();
            $JourDep = $currentMonthDateTime->modify('monday this week');
            $TboData = [];
            $j = 0;
            $repo=$manaReg->getRepository(PolymReal::class);
            $PMoy=$repo ->findCharMach($jour,$dateDebAn,$moyen['Moyen']);
            foreach($PMoy as $pmoy){
                $y=intval($pmoy['DureTotPolyms'])/3600;
                $w=round($y,1);
                $TboData[$j]=['x'=> strtotime($pmoy['annee'].'-'.$pmoy['mois'].'-'.$pmoy['jour'])*1000,'y'=>$y,'indexLabel'=> $w."H"];
                $j = $j + 1;
            };
            $Tablo[$i]=['type'=>"stackedColumn",'name'=>$moyen['Moyen'],'indexLabelWrap'=> 'true' ,'indexLabelFontSize'=> 12,'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            //dump($Tablo);
            $i = $i + 1;
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }
        $Productivite= new JsonResponse($Tablo);

        $Titres=[];

        return $this->render('planning_mc/MoyensIndus.html.twig',[
            'Titres' => $Titres,
            'Productivite' => $Productivite->getContent(),
            //'formProg' => $form->createView(),
        ]);
    }

     /**
     * @Route("/METHODES/PE/Creation", name="Creation")
     * @Route("/METHODES/PE/Modification/{id}", name="Modification")
     */
    public function Creation(Request $Requet,EntityManagerInterface $manager,ProgMoyens $Prog=null)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/ADMIN/Moyen/Creation", name="CreationMoyen")
     * @Route("/ADMIN/Moyen/Modification/{id}", name="ModificationMoyen")
     */
    public function CreationMoyen(Request $Requet,EntityManagerInterface $manager,Moyens $Moyen=null, ManagerRegistry $manaReg)
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
            $repo=$manaReg->getRepository(ConfSsmenu::class);
            $Titres=$repo -> findall();
            //dump($Titres);
        $form = $this->createForm(CreationMoyensType::class, $Moyen);
        
        $form->handleRequest($Requet);
        
        return $this->render('planning_mc/CreationMoyen.html.twig',[
            'Titres' => $Titres,
            'formMoy' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/AccessDenied", name="noAccess")
     */
    public function noAccess()
    {
        return new JsonResponse(['Message'=>"<div class=\"text-warning\">Vous ne pouvez pas accéder à cette transaction.\n Problème de droits</div>"]);
    }
}
