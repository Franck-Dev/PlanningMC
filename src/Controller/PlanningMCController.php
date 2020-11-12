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
use App\Entity\ConfSsmenu;
use App\Entity\Outillages;
use App\Entity\ProgMoyens;
use App\Form\CreationOType;
use App\Form\PolymFormType;
use App\Entity\CategoryMoyens;
use App\Entity\TypeRecurrance;
use App\Form\CreationProgType;
use App\Entity\RecurrancePolym;
use App\Form\PlanifDemandeType;
use App\Form\CreationMoyensType;
use PhpParser\Node\Stmt\Foreach_;
use Doctrine\ORM\EntityRepository;
use App\Repository\DefaultRepositoryFactory;
use App\Services\FunctIndic;
use Symfony\Component\HttpFoundation\Request;
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
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

//use Symfony\Component\HttpFoundation\Session\Session ;
//use Symfony\Component\Serializer\Serializer;

class PlanningMCController extends Controller
{
    /**
     * @Route("/Planning/Edit", name="Planning_Edit")
     * @Security("is_granted('ROLE_REGLEUR')")
     */
    public function planningEdit()
    {
        //Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Taches=$repo -> findAll();
        $data = [];
        $i = 0;
        //On créé le pourcentage de volumetrie en test, a changer par le réel avec nb outillage
        $Pourc=10;
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
        foreach($Taches as $tache){
            //On cherche le moyen attribué à la polym suivant la demande et l'activité Plannification
            $commentaires=$tache->getNumDemande()->getCommentaires()."/".$tache->getNumDemande()->getOutillages();
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
        //Implémentation dans la variable des polyms créées
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAll();
        //dump($Polyms);
        foreach($Polyms as $polym){ 
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
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
        $moyens=$repos -> findAllMoyensSvtService ( intval('8') );  
        $dati = [];
        $TbEtat=[];
        $i = 0;
        $a=0;
        foreach($moyens as $moyen){
            // Si le moyen à 2 sous fonctions (ex: "plannifié et réalisé")
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
                $dati[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen'], 'className'=> 'gris', 'nestedGroups' => [$TbEtat[$a-1]['id']]];
            }
            else{
                $dati[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen']];
            }
            $i = $i + 1;
			//On affecte un élément $item à $data
        }

        return new JsonResponse(['Taches'=> $data, 'moyen'=> $dati, 'Ssmoyen'=> $TbEtat]);
    }    
    /**
     * @Route("/Planning", name="Planning")
     * @Security("is_granted('ROLE_REGLEUR')")
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
            // Si le moyen à 2 sous fonctions (ex: "plannifié et réalisé")
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
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen'], 'className'=> 'gris', 'nestedGroups' => [$TbEtat[$a-1]['id']]];
            }
            else{
                $data[$i] = ['id'=> $moyen['id'],  'content'=> $moyen['Moyen']];
            }
            $i = $i + 1;
			//On affecte un élément $item à $data
        }
        $Ssmoyen= new JsonResponse($TbEtat);
        $moyen= new JsonResponse($data);

//Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Taches=$repo -> findAll();
        $data = [];
        $i = 0;
        //On créé le pourcentage de volumetrie en test, a changer par le réel avec nb outillage
        $Pourc=10;
        foreach($Taches as $tache){
            //On cherche le moyen attribué à la polym suivant la demande et l'activité Plannification
            $commentaires=$tache->getNumDemande()->getCommentaires()."/".$tache->getNumDemande()->getOutillages();
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
        //Implémentation dans la variable des polyms créées
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAll();
        //dump($Polyms);
        foreach($Polyms as $polym){ 
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
        $taches= new JsonResponse($data);
        //dump($taches);

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
    public function CreaPolymRecur()
    {
            
            //Chargement des dates de la semanine -1
            $currentMonthDateTime = new \DateTime();
            $firstDateTime = $currentMonthDateTime->modify('Monday next week');
            $currentMonthDateTime = new \DateTime();
            $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
            $lastDateTime = $lastDateTime->modify('23 hours');

            //Récupération des polyms récurrantes de la semaine -1    
            //$repo=$this->getDoctrine()->getRepository(Demandes::class);
            //$DemRec=$repo ->findDemRecur($lastDateTime,$firstDateTime);
            //$DemRec=$repo -> findBy(['Reccurance'=>'1','UserCrea'=>$user->getUsername(),'Plannifie'=>'1','RecurValide'=>'0']);
            $repo=$this->getDoctrine()->getRepository(RecurrancePolym::class);
            $ListRec=$repo ->findRecur($lastDateTime,$firstDateTime);
            //dump($ListRec);
            //On va créer la demande et la polym à semaine +1 de chaque polym recur de la semaine -1
            foreach($ListRec as $dem){
                //dump($dem->getNumHeritage());
                if(!$dem->getNumHeritage()){
                    //Création de la demande
                    $demande = new Demandes();
                    $demande=clone $dem->getNumPlanning()->getNumDemande();
                    //$NewDate=date_modify($demande->getDatePropose(),'+ 7days');
                    //$demande->setDatePropose($NewDate);
                    $demande->setDatePropose($dem->getDateFinrecurrance());

                    $manager = $this->getDoctrine()->getManager();
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
                        dump($demande);
                        $Planning->setIdentification($demande->getMoyenUtilise()->getLibelle());
                        $Planning->setAction($demande->getCycle()->getNom());
                        $Planning->setNumDemande($demande);
                        $Planning->setStatut("PLANNIFIE");
                        
                        //Intégration en base
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($Planning);
                        $manager->flush();

                        //Création de la donnée récurrance
                        $Recur = new RecurrancePolym();
                        $Recur->setTypeRecurrance($dem->getTypeRecurrance());
                        //Récupération des données du type de récurrance
                        $repo=$this->getDoctrine()->getRepository(TypeRecurrance::class);
                        $Dur=$repo ->findBy(['id' =>$dem->getTypeRecurrance()]);
                        
                        $DurRec='+ '.$Dur[0]->getNbrJourCycle().'Days';
                        $NewDateRecur=date_modify($dem->getDateFinrecurrance(),$DurRec);
                        
                        $Recur->setDateFinrecurrance($NewDateRecur);
                        $Recur->setNumPlanning($Planning);

                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($Recur);
                        $manager->flush();

                        //Archivage de la récurrance valider par retour du n° de la récurrance enfant
                        $repo=$this->getDoctrine()->getRepository(RecurrancePolym::class);
                        $Recurrepo=$repo ->find($dem->getid());
                        $Recurrepo->setNumHeritage($Recur->getid());
                        $manager = $this->getDoctrine()->getManager();
                        //$manager->persist($ModifDem);
                        $manager->flush();
                    }
                }
            }
            return $this->redirectToRoute('Planning');
    } 
     /**
     * @Route("/PlanningMC/Creation/", name="CreaDemPolymf", condition="request.isXmlHttpRequest()")
     */
    public function CreaDemPolymf(Request $request,RequestStack $requestStack, userInterface $user=null)
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
                    $Cyc = $this->getDoctrine()
                        ->getRepository(ProgMoyens::class)
                        ->findBy(['id' =>$TabDem['cycle']]);
                    $demande->setCycle($Cyc[0]);

                    //Récupération de l'objet moyen
                    $Moy = $this->getDoctrine()
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
                    
                        $manager = $this->getDoctrine()->getManager();
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

                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($Planning);
                        $manager->flush();

                        //return $this->redirectToRoute('Planning');
                        //Si polym avec un recurrance, création de cette dernière
                        //dump($demande->getRecurValide());
                        dump($demande);
                        if($demande->getRecurValide() == "true"){
                            $recur= new RecurrancePolym;
                            
                            //Récupération du type de récurrence(à modifier pour automatisation)
                            $TypRecur = $this->getDoctrine()
                            ->getRepository(TypeRecurrance::class)
                            ->findBy(['Type' => 'HEBDO']);

                            $recur->setTypeRecurrance($TypRecur[0]);
                            $recur->setNumPlanning($Planning);
                            //récupération du nombre de jour et création de la variable
                            $DureRecTyp=$TypRecur[0]->getNbrJourCycle();
                            $varCycle='+ '.$DureRecTyp.'days';
                            $NewDate=date_modify($Planning->getDebutDate(),$varCycle);
                            $recur->setDateFinrecurrance($NewDate);

                            $manager = $this->getDoctrine()->getManager();
                            $manager->persist($recur);
                            $manager->flush();
                        }
                    }
                    $request->getSession()->getFlashbag()->add('success', 'Votre polym a été enregistré.');
                    //return $this->redirectToRoute('demandes');
                }else{
                    //dump(count($TabDem));
                    $planning = new Planning();
                    //$demande->setDatePropose($request->get('DatePropose'));
                    //dump($TabDem);
                    //Récupération de l'objet polym suivant l'id concerné
                    $PolymPla = $this->getDoctrine()
                        ->getRepository(Planning::class)
                        ->findBy(['id' =>$TabDem['id']]);
                    //dump($PolymPla);
                    $planning=$PolymPla[0];
                    //dump($planning);
                    $planning->setStatut($TabDem['statut']);
                    //dump($planning);
                    $manager = $this->getDoctrine()->getManager();
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
    public function creapolym(Request $request,Demandes $demande=null)
    {

    $Planning = new Planning();
    $moyen = new Moyens();

        //On verifie si c'est une création ou une modification
        if($demande->getPlannifie()===false) {
            //C'est une création
            //dump($request);
            //Récupération de l'ID moyen suivant nom
            $moyen = $this->getDoctrine()
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
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($Planning);
                $manager->flush();
                //On verifie si la récurrance existe pour modification si oui
                $RecurD = $this->getDoctrine()
                    ->getRepository(RecurrancePolym::class)
                    ->findBy(['NumPlanning' => $Planning->getid()]);
                if(!$RecurD){
                //Création du numéro de Recurrance dans la table de ce dernier si besoin
                    if($request->get('Reccurance')=="true"){
                        $recurP = new RecurrancePolym();
                        //Récupération du type de récurrence(à modifier pour automatisation)
                        $TypRecur = $this->getDoctrine()
                        ->getRepository(TypeRecurrance::class)
                        ->findBy(['Type' => 'HEBDO']);

                        $recurP->setTypeRecurrance($TypRecur[0]);
                        $recurP->setNumPlanning($Planning);
                        //récupération du nombre de jour et création de la variable
                        $DureRecTyp=$TypRecur[0]->getNbrJourCycle();
                        $varCycle='+ '.$DureRecTyp.'days';
                        $NewDate=date_modify($Planning->getDebutDate(),$varCycle);
                        $recurP->setDateFinrecurrance($NewDate);

                        $manager = $this->getDoctrine()->getManager();
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
                    $manager = $this->getDoctrine()->getManager();
                    $manager->remove($RecurD->getNumPlanning());
                    $manager->flush();

                    $recurP = new RecurrancePolym();
                    $TypRecur = $this->getDoctrine()
                        ->getRepository(TypeRecurrance::class)
                        ->findBy(['Type' => 'HEBDO']);
                    $recurP->setTypeRecurrance($TypRecur[0]);
                    $recurP->setNumPlanning($Planning);
                    $recurP->setDateFinrecurrance(new \Datetime("9999-12-30"));

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($recurP);
                    $manager->flush();
                    $demande->SetRecurValide(1);
                    $StatRecur=true;
                    
                }
                //Création dans les tables appropriés
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($demande);
                $manager->flush();

                //Récupération de l'ID moyen suivant nom
                $Planning = $this->getDoctrine()
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
            $RecurD = $this->getDoctrine()
            ->getRepository(RecurrancePolym::class)
            ->findBy(['NumPlanning' => $demande->getPlanning()->getid()]);
                //Donne s'il y a une recurrance ou pas            
                if($request->get('Reccurance')=="true"){
                    if($RecurD){
                        $RecurDel= new RecurrancePolym($RecurD[0]);
                        $RecurDel=$RecurD[0];
                        
                        $manager = $this->getDoctrine()->getManager();
                        $manager->remove($RecurDel->getNumPlanning());
                        $manager->flush();
                        $StatRecur=true;
                    }
                    else{
                        $StatRecur=false;
                    }
                }
                else{           
                    $manager = $this->getDoctrine()->getManager();
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
     * @Security("has_role('ROLE_PLANIF')")
     */
    public function editpolym(Request $request)
    {
        $demande = new Demandes();
        //Récupération du libelle du moyen

        //Récupération des données de la requette
        $demande->setDatePropose(new \Datetime($request->get('DateDeb')));
        
        $lastDateTime=date("Y-m-d H:m:s",strtotime($request->get('Hdeb')));
        $newfindate = new \DateTime($lastDateTime);

        $demande->setHeurePropose($newfindate);
        //Récupération de l'objet moyen
        $Moy = $this->getDoctrine()
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
     * @Route("/PlanningMC/Modification", name="Polym_Modif", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_REGLEUR')")
     */
    public function polymodif(Request $request)
    {
        //Remonté d'info pour modification du statut de la polymérisation
        $idPolym=substr($request->get('PolymId'),1,strlen($request->get('PolymId'))-1);
        //dump($idPolym);
        $PolymPla = $this->getDoctrine()
            ->getRepository(Planning::class)
            ->findBy(['id' => $idPolym]);
        //$planning->setMoyenUtilise($PolymPla[0]);
        //dump($PolymPla);
        $form = $this -> createFormBuilder($PolymPla[0])
        -> add('id')
        -> add('identification')
        -> add('action')
        -> add('debut_date', DateTimeType::class,['disabled'=>true])
        -> add('fin_date', DateTimeType::class,['disabled'=>true])
        -> add('statut', ChoiceType::class, [
            'choices'  => [
                'PLANNIFIE' => 'PLANNIFIE',
                'ANNULER' => 'ANNULER',
                'REMPLACER' => 'REMPLACER',
            ]])
        ->add('save', SubmitType::class, ['label' => 'Modifier'])
        /* -> add('commentaires') */
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

    /**
     * @Route("/PlanningMC/ModificationPolym", name="CreaDemPolym")
     * @Security("has_role('ROLE_REGLEUR')")
     */
    public function CreaDemPolym(Request $request,RequestStack $requestStack, userInterface $user=null)
    {
        //Modification du statut de la polym
        if($requestStack->getParentRequest()){
            
            $request=$requestStack->getParentRequest();
            if ($request->isMethod('POST') and $request->request->get('form')) {
                dump($request);
                $planning = new Planning();
                //$demande->setDatePropose($request->get('DatePropose'));
                $TabDem=$request->request->get('form');
                //dump($TabDem);
                //Récupération de l'objet cycle
                $PolymPla = $this->getDoctrine()
                    ->getRepository(Planning::class)
                    ->findBy(['id' =>$TabDem['id']]);
                $planning=$PolymPla[0];
                $planning->setStatut($TabDem['statut']);
                $manager = $this->getDoctrine()->getManager();
                    $manager->persist($planning);
                    $manager->flush();
                    $request->getSession()->getFlashbag()->add('success', 'Création de la polym n°' . $planning->getId() . ' réalisée');
            }
        }
        
        return new JsonResponse(['Message'=>"Vous n'avez pas les droits pour créer une polym",'Code'=>404]);
    }
    
     /**
     * @Route("/PlanningMC/Suppression", name="Polym_Del", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_PLANIF')")
     */
    public function deletepolym(Request $request)
    {
        //Suppression de la polym
        dump($request->get('PolymId'));
        if($request->get('PolymId')){
            if ($request->isMethod('POST')) {
                $planning = new Planning();
                $idPolym=substr($request->get('PolymId'),1,strlen($request->get('PolymId'))-1);
                dump($idPolym);
                $PolymPla = $this->getDoctrine()
                    ->getRepository(Planning::class)
                    ->findBy(['id' => $idPolym]);
                $planning=$PolymPla[0];

                //On va rechercher la demande liée pour modifier son statut et la récurrence si besoin
                $Demande = new Demandes();
                $Dem = $this->getDoctrine()
                    ->getRepository(Demandes::class)
                    ->findBy(['id' => $planning->getNumDemande()]);
                $Demande = $Dem[0];

                $manager = $this->getDoctrine()->getManager();
                $manager->remove($planning);
                $manager->flush();

                //Si la demande à une récurrence on la supprime
                $Demande->setRecurValide(0);
                $Demande->setPlannifie(0);
                $Demande->setMoyenUtilise(Null);
                dump($planning);

                $manager = $this->getDoctrine()->getManager();
                    $manager->persist($Demande);
                    $manager->flush();
                    dump($planning);
            }
        }
        return new JsonResponse(['Message'=>"Vous n'avez pas les droits pour créer une polym",'Code'=>404]);
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
     */
    public function DemandesCrea( Request $requette,RequestStack $requestStack,ObjectManager $manager,Demandes $demande=null,$datejour=null, userInterface $user=null)
    {
//Si la demande n'est pas déjà faite(modification), on l'a crée
        //dump($datejour);    
        if(!$demande){
            //Création automatique par CE_POLYM
            if($requette->get('Demandes')){
                //Récupération des données de la polym plannifié
                $repo=$this->getDoctrine()->getRepository(Planning::class);
                $Plan=$repo -> findBy(['id'=> $requette->get('IdPla')]);
                //Récupération des données de la demande réccurante
                $repo=$this->getDoctrine()->getRepository(Demandes::class);
                $Dem=$repo -> findBy(['id'=> $requette->get('Demandes')]);
                //On marque cette récurrance comme validée
                //dump($Dem[0]);
                $demande=$Dem[0];
                $demande->SetRecurValide(1);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($demande);
                $manager->flush();
                dump($demande);
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
                $demande->setDatepropose(new \Datetime($requette->get('datejour')));
                $newdemande=true; 
            }
        }
        else{
            $newdemande=false;
            //dump($user);
        }
            //Dans la construction du form, on vérifie si la demande est déjà validée lors d'une modif et on bloque des éléments
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
                
                $requette->getSession()->getFlashbag()->add('success', 'La demande'. $demande->getId() . 'a bien été enregistré.');

                if($mode==false){
                    //dump($demande);
                    
                    return $this->redirectToRoute('Demandes');
                }
                else{
                    dump($demande);
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
     */
    public function Demandes(Request $requette, ObjectManager $manager,userInterface $user=null)
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
            $firstDateTime=date("Y-m-d",strtotime($requette->get('DatedebPlan')));
            $currentMonthDateTime = new \DateTime($firstDateTime);
            $lastDateTime = $currentMonthDateTime->modify('Sunday this week');
            $lastDateTime = $lastDateTime->modify('23 hours');
        }

//Visualisation des demandes en cours
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
        //dump($cycles); 
    //Recherche des moyens à afficher sur planning
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
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
           //'Ssmoyen' => $Ssmoyen->getContent(),
           'taches' => $taches->getContent(),
           'DemRec' => $DemRec,
           'utilisateurs' => $utilisateurs,
           'reqet' => $requette
        ]);   
    }

    /**
     * @Route("/Demandes/Supression/{id}", name="Sup_Demandes")
     */
    public function demandeSup(ObjectManager $manager,Demandes $demande=null)
    {
        $manager = $this->getDoctrine()->getManager();
            $manager->remove($demande);
            $manager->flush();
        return $this->redirectToRoute('Demandes');
    }

    /**
     * @Route("/Demandes/Deprogrammation/{id}", name="Deprog_Demandes")
     */
    public function demandeDeprog(ObjectManager $manager,Demandes $demande=null)
    {
        // $manager = $this->getDoctrine()->getManager();
        //     $manager->remove($demande);
        //     $manager->flush();
        //Envoyer un mail pour faire la demande de modification
        return $this->redirectToRoute('Demandes');
    }

    /**
     * @Route("/Demandes/Plannification/{id}", name="Planif_Demandes")
     * @Security("has_role('ROLE_PLANIF')")
     */
    public function DemandesPlanif( Request $requette,ObjectManager $manager,Demandes $demande=null, ValidatorInterface $validator )
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
            $manager = $this->getDoctrine()->getManager();
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
            
            $requette->getSession()->getFlashbag()->add('success', 'Votre polym a été enregistré.');
                
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
     * @Route("/Planification", name="Planification")
     * @Security("has_role('ROLE_PLANIF')")
     */
    public function Planification(request $requette,Demandes $demande=null, FunctIndic $indic)
    {
//Création des indicateurs
    //Recherche du début et de la fin de semaine à plannifier
        $currentMonthDateTime = new \DateTime();
        $FinSem = $currentMonthDateTime->modify('sunday next week');
        $currentMonthDateTime = new \DateTime();
        $DebSem = $currentMonthDateTime->modify('monday next week');
    //Création de la variable charge totale sur la semaine
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $CharTot= $indic->chargTot($repo, $FinSem, $DebSem);

    $TpsOuvParMach=intval(24*7);
        
    //Création de la variable charge de chaque machine sur la semaine encours
       $result=$indic->chargMachsem($repo, $FinSem, $DebSem, $TpsOuvParMach, $CharTot);
       $ChargeMoy= new JsonResponse($result[0]);
       $ReparCharg= new JsonResponse($result[1]);
        
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
        $firstDateTime=date("Y-m-d",strtotime($requette->get('DatedebPlan')));
        $currentMonthDateTime = new \DateTime($firstDateTime);
        $lastDateTime = $currentMonthDateTime->modify('Sunday this week');
        $lastDateTime = $lastDateTime->modify('23 hours');
    }

//Recherche des moyens à afficher sur planning
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
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
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Taches=$repo -> findAll();
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
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
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
     * @Security("has_role('ROLE_PLANIF')")
     */
    public function Modif_Polym_Pla(Request $request, Planning $Polyms=null)
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
            $firstDateTime=date("Y-m-d H:m",strtotime($request->request->get('newdatedeb')));
            $newdebdate = new \DateTime($firstDateTime);
            $lastDateTime=date("Y-m-d H:m",strtotime($request->request->get('newdatefin')));
            $newfindate = new \DateTime($lastDateTime);
            //Récupération de la désignation du moyen suivant id
            $basemoy = $this->getDoctrine()->getRepository(Moyens::class);
            $moyen = $basemoy->findBy(['id' => $idmoyen]);
            //dump($idPolymPla);//die();
            if($idPolymPla) {
                dump($newdebdate);
                $mr = $this->getDoctrine()->getRepository(Planning::class);
                //$maga = $mr->findOneBySomeField($olddebdate,$oldfindate,$moyen[0]->getLibelle($idmoyen));
                $maga=$mr->findBy(['id' => $idPolymPla]);
                $old=$request->request->get('olddatedeb');
                //$new=date("Y-m-d H:m",strtotime($request->request->get('newdatedeb')));
                dump($maga);//die();
                $maga[0]->setDebutDate($newdebdate);
                $maga[0]->setFinDate($newfindate);
                dump($maga);//die();
                $manager = $this->getDoctrine()->getManager();
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
     * @Route("/LOGISTIQUE/Ordonnancement", name="Ordo")
     */
    public function Ordo()
        {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();
        // Création de la table de répartition des programmes suivant OF SAP lancés sur 1 mois
        $repo=$this->getDoctrine()->getRepository(Charge::class);
        // Date à aujourd'hui
        $jour= new \datetime;
        $date=$jour;
        $SemUn=$jour->format("W");
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 30 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        $DerSem=$jourVisu->format("W");
        //Récupération de la charge SAP sur 1 mois
        $ChargeTot=$repo -> findChargeSem($date,$jourVisu); 
        $i=0;
        $j=0;
        foreach($ChargeTot as $charge){
            $y=intval($charge['NbrRef']);
            $lundi = new \DateTime();
            $lundi->setISOdate($charge['Annee'], $charge['Semaine']);
            $jour=strtotime($lundi->format("Y-m-d"))*1000;
            //dump($lundi->format("WY"));
            $TboData[$j]=['x'=> $jour,'y'=>$y];
            $Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
                $i = $i + 1;
            //$Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"false",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }
        $RepartP= new JsonResponse($Tablo);
        //dump($RepartP);

        // Création de la table de répartition des programmes en retard suivant OF SAP lancés 
        $repo=$this->getDoctrine()->getRepository(Charge::class);
        // Date à hier
        $jourFinRetard = date("Y-m-d", strtotime('- 1 days'.date('Y') ));
        $date=new \datetime($jourFinRetard);
        // Date en retard d'1 mois
        $jourVisu = date("Y-m-d", strtotime('- 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        $ChargeTard=$repo -> findChargeSem($jourVisu,$date);
        //dump($ChargeTard);
        $i=0;
        $j=0;
        foreach($ChargeTard as $charge){
            $y=intval($charge['NbrRef']);
            $DebSem = new \DateTime();
            $DebSem->setISOdate($charge['Annee'], $charge['Semaine']);
            $JDebSem=strtotime($DebSem->format("Y-m-d"))*1000;
            //dump($lundi->format("WY"));
            $TboDatas[$j]=['x'=> $JDebSem,'y'=>$y];
            $Tablos[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboDatas];
                $i = $i + 1;
            //$Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"false",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }

        $RepartRetard= new JsonResponse($Tablos);
        // Création de la table de répartition des programmes oubliés
        $repo=$this->getDoctrine()->getRepository(Charge::class);
        // Date à plus d'un mois
        $jourFinRetard = date("Y-m-d", strtotime('+ 365 days'.date('Y') ));
        $date=new \datetime($jourFinRetard);
        // Date en retard d'1 an
        $jourVisu = date("Y-m-d", strtotime('- 730days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        $ChargeOubli=$repo -> findChargeMois($jourVisu,$date);
        $i=0;
        $j=0;
        foreach($ChargeOubli as $charge){
            $y=intval($charge['NbrRef']);
            $DebSem = new \DateTime();
            $DebSem->setISOdate($charge['Annee'], $charge['Mois']);
            $JDebSem=strtotime($DebSem->format("Y-m-d"))*1000;
            $TboDati[$j]=['x'=> $JDebSem,'y'=>$y];
            $Tabli[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboDati];
                $i = $i + 1;
            //$Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"false",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }

        $RepartT= new JsonResponse($Tabli);

        // Création de la charge totale SAP
        $repo=$this->getDoctrine()->getRepository(Charge::class);
        $ChargTot=$repo -> findAll();
        
    //Création de la planification à long terme avec les chargements figés
        // Création de la table de répartition des programmes suivant OF SAP lancés sur 1 mois
        $repo=$this->getDoctrine()->getRepository(Charge::class);
        // Date à aujourd'hui
        $jour= new \datetime;
        // Date à 1 mois
        $jourVisu = date("Y-m-d", strtotime('+ 31 days'.date('Y') ));
        $jourVisu=new \datetime($jourVisu);
        //Récupération de la charge SAP sur 1 mois
        $ChargeTot=$repo -> findReparChargeW($jour,$jourVisu);
        $i=0;
        dump($ChargeTot);
        foreach($ChargeTot as $Creno){
            $TableCTJ[$i]=$repo -> findBy(['DateDeb' => $Creno['Jour'],'NumProg' => $Creno['Cycles']]);
            dump($Creno['Cycles']);          
            //On récupère l'ID du cycle en cours
            $STD=$this->getDoctrine()->getRepository(ProgMoyens::class);
            $IdProg=$STD->findOneBy(['Nom' => $Creno['Cycles']]);
            //On récupère les chargements figés du cycle en cours
            $cata=$this->getDoctrine()->getRepository(ChargFige::class);
            $ChargementsFiG=$cata->findBy(['Programme' =>$IdProg]);
            $f=0;
            dump($ChargementsFiG);
            //On  sélectionne les chargements figés en fonction du nombre de pièces
            foreach($ChargementsFiG as $ChargeFiG){
                //Pour chaque chargement figé on récupère sa composition en outillages
                $listeOT = $this->getDoctrine()->getRepository(Outillages::class)->myFindByCharFiG($ChargeFiG->getCode());
                dump($listeOT);
                //On récupère le nombre d'outillages
                $nbOT=sizeof($listeOT);
                //On cherche les OT correspondants aux articles du $creno(TableCTJ[$i])
                $r=0;
                $TabOTArtOFOP=[];
                dump($TableCTJ);
                foreach($TableCTJ[$i] as $OFJ){
                    //Pour chaque OF on va récupérer l'OT correspondant à l'article de l'OF
                    dump($OFJ->getReferencePcs());
                    $Outill= $this->getDoctrine()->getRepository(Outillages::class)->myFindByOutillage($OFJ->getReferencePcs()); 
                    dump($Outill);
                    if(sizeof($Outill)>1){
                        $ArtMultiIndus=True;
                    }
                    elseif(sizeof($Outill)==0){
                        //Si pas d'outillage correspondant à l'article, on sort de la boucle
                        dump('Aucun outillage trouvé');
                        break 2;
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
                        dump($TabOTMultiEmp);
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
                        dump($TabOTArtOFOP);   
                        
                    }
                }
                dump($TabOTArtOFOP);
                //On vérifie si les chargements figés sont adéquate pour ce $creno(charge en nb de pièce de la journée)
                foreach($TabOTArtOFOP as $ChargPolym){
                    dump($ChargPolym);
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
                            Dump($Creno);
                            $NJourD=clone $Creno['Jour'];
                            $jourcyle=$NJourD->modify('+1 day');
                            $NJourF= clone $Creno['Jour'];
                            $jour3cycle=$NJourF->modify('+7 day');
                            $Cyc=$Creno['Cycles'];
                            Dump($jourcyle);
                            Dump($jour3cycle);
                            //Récupération de tous les OF suivant nouvelles dates et cycle en cours
                            $ChargePart=$repo -> findReparChargeWCycle($jourcyle,$jour3cycle,$Cyc);
                            dump($ChargePart);
                            $j=0;
                            //On va chercher les articles manquants au chargement figé pour complétude
                            foreach($ChargePart as $CPart){
                                //Pour chaque OF en avance de charge
                                $TabCharPart[$j]=$repo -> findBy(['DateDeb' => $CPart['Jour'],'NumProg' => $Creno['Cycles']]);
                                dump($TabCharPart);
                                //On va récupérer le n° OT de chaque article
                                foreach($TabCharPart[$j] as $OFBis ){
                                    dump($OFBis->getReferencePcs());
                                    $OutBis= $this->getDoctrine()->getRepository(Outillages::class)->myFindByOutillage($OFBis->getReferencePcs());
                                    dump($OutBis);
                                    //On comparer pour trouver les outillages manquants du chargement figé
                                    
                                }
                                $j=$j+1;
                                
                            }
                        }
                    }
                }
            }
        $i = $i + 1;
        }

        return $this->render('planning_mc/Ordo.html.twig', [
            'controller_name' => 'PlanningOrdo',
            'RepartPcs' => $RepartP->getContent(),
            'RepartRetard'=> $RepartRetard->getContent(),
            'RepartT'=> $RepartT->getContent(),
            'DerSem' => $DerSem,
            'ChargeTot' => $ChargTot,
            'SemUn' => $SemUn,
            'Titres' => $Titres,
            'datedeb' => $jour,
            'datefin' => $jourVisu,
            'tests' => $ChargeTot,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Urgences", name="Urgences")
     */
    public function Urgences(Request $requette, ObjectManager $manager)
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Urgences.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/LOGISTIQUE/Bloquants", name="Bloquants")
     */
    public function Bloquants(Request $requette, ObjectManager $manager)
    {
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
        $Titres=$repo -> findAll();

        return $this->render('planning_mc/Bloquants.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/METHODES/PE/Consultation_PE", name="Consultation PE")
     * @Route("METHODES/PE/Consultation_PE/{id}", name="Consul_PE")
     */
    public function Consultation_PE(CategoryMoyens $moyen=null)
    {

    }

    /**
     * @Route("/METHODES/PE/Demande_SPF", name="Demandes SPF")
     */
    public function Demandes_SPF(CategoryMoyens $moyen=null)
    {

    }

    /**
     * @Route("/METHODES/PE/Creation_PE", name="Creation PE")
     * @Route("/METHODES/PE/Modification_PE/{id}", name="Modification_PE")
     */
    public function Creation_PE(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PE']);
        dump($Titres);
        
        return $this->render('planning_mc/articles/edit.html.twig',[
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/METHODES/PROGRAMMATION/Creation_PRP", name="Creation PRP")
     * @Route("/METHODES/PROGRAMMATION/Modification_PRP/{id}", name="Modification_PRP")
     */
    public function Creation_PRP(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
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
    public function Creation_ChargeF(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        //dump($Titres);
        
        return $this->render('planning_mc/charg_fige/edit.html.twig',[
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/METHODES/PROGRAMMATION/Consultation", name="Consultation")
     * @Route("METHODES/PROGRAMMATION/Consultation/{id}", name="Consul_ProgMoy")
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
    public function CreationO(Request $Requet,ObjectManager $manager,Outillages $OT=null)
        {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
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
            $entityManager = $this->getDoctrine()->getManager();
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
    public function ConsultationO(Outillages $OT=null)
    {
        $OT=new Outillages();
        $repo=$this->getDoctrine()->getRepository(Outillages::class);
        $OTs=$repo -> findall();
        dump($OTs);

        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'OUTILLAGE']);
        dump($Titres);
        dump($OTs);
        
        return $this->render('planning_mc/ConsultationOutil.html.twig',[
            'Titres' => $Titres,
            'Outillages' => $OTs,
        ]);
    }

    /**
     * @Route("/OUTILLAGE/Article/Demandes", name="DemandesO")
     */
    public function DemandesO(Request $requette, ObjectManager $manager)
    {

    }

     /**
     * @Route("/METHODES/Moyens", name="MOYENS_INDUS")
     */
    public function CreationM(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
        {//$repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
            //$Titres=$repo -> findall();

        //Recherche date du jour
        $DateJour = new \DateTime();
        $jour=$DateJour->modify('today');
        $dateDebAn= new \datetime();
        $dateDebAn->modify('First day of january this year');

        //La requête remonte les moyens inférieur à 23 du service 8
        $repo=$this->getDoctrine()->getRepository(Moyens::class);
        $Moyen=$repo ->findMoyens(intval('8'),intval('23'));
        dump($Moyen);
        $Tablo = [];
        $i = 0;
        foreach($Moyen as $moyen){
            $currentMonthDateTime = new \DateTime();
            $JourDep = $currentMonthDateTime->modify('monday this week');
            $TboData = [];
            $j = 0;
            $repo=$this->getDoctrine()->getRepository(PolymReal::class);
            $PMoy=$repo ->findCharMach($jour,$dateDebAn,$moyen['Moyen']);
            dump($PMoy);    //$Annee.'-'.$polym['Mois'].'-01')
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
    public function Creation(Request $Requet,ObjectManager $manager,ProgMoyens $Prog=null)
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
