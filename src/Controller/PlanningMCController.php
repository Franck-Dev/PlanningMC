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
//use Symfony\Component\Serializer\Serializer;

class PlanningMCController extends Controller
{
    /**
     * @Route("/PlanningMC", name="Planning")
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
        $moyens=$repos -> findBy(['Id_Service' => '8']);
        $item=$moyens;   
        $data = [];
        $i = 0;
        foreach($moyens as $moyen){
            $data[$i] = ['id'=> $moyen->getId(),  'content'=> $moyen->getLibelle()];
            $i = $i + 1;
			//On affecte un élément $item à $data
        }
        //dump($data);
        $moyen= new JsonResponse($data);

//Chargement d'une variable pour les tâches déjà plannifiées
        $repo=$this->getDoctrine()->getRepository(Planning::class);
        $Taches=$repo -> findAll();
        $data = [];
        $i = 0;
        foreach($Taches as $tache){
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]);
            $data[$i] = ['id'=> $tache->getId(),'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $tache->getNumDemande()->getCommentaires()];
            $i = $i + 1;
            //dump($MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]));
            //dump($MoyUtil[0]->getId());
            //dump($Taches);
            //dump($tache->getNumDemande()->getCycle()->getCouleur());
        }
        //Implémentation dans la vairaible des polym créées
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAll();
        //dump($data);
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $polym->getId(),'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
            $i = $i + 1;
        }
        $taches= new JsonResponse($data);
        //dump($taches);

        return $this->render('planning_mc/index.html.twig', [
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
            'Taches' => $taches->getcontent(),
            'Moyens' => $moyen->getcontent(),
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
        $DateEncours = date("l", strtotime('first day of January '.date('Y') )); 
        
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAllPcsByDate($DateEncours);
        dump($Polyms);
        $data = [];
        $i = 0;
//        { x: new Date("1 Jan 2015"), y: 868800 },
//        { x: new Date("1 Feb 2015"), y: 1071550 },
        foreach($Polyms as $polym){ 
            $data[$i] = ['y'=> $polym[2],'name'=> $polym['Nom'],'indexLabel'=> $polym['Nom'],'legendText'=> $polym['Nom']];
            $i = $i + 1;
        }
        dump($data);
        $taches= new JsonResponse($data);
        dump($taches->getContent());
        return $this->render('planning_mc/home.html.twig',[
            'controller_name' => 'PlanningMCController',
            'Titres' => $Titres,
            'Taches' => $taches->getContent()
        ]);
        
    }

	/**
     * @Route("/Demandes/Creation", name="Crea_Demandes")
     * @Route("/Demandes/Modification/{id}", name="Modif_Demandes")
     */
    public function DemandesCrea( Request $requette,RequestStack $requestStack,ObjectManager $manager,Demandes $demande=null,$datejour=null, user $user=null)
    {
//Si la demande n'est pas déjà faite(modification), on l'a crée
    
dump(new \Datetime($datejour)); 

        if(!$demande){
            $demande = new Demandes();
            $demande->setDatepropose(new \Datetime($datejour));
            $newdemande=true; 
        }
        else{
            $newdemande=false;
        }
        //dump($app);
            $form = $this -> createFormBuilder($demande)
                      -> add('cycle', EntityType::class, [
                          'class' => ProgMoyens::class,
                          'choice_label' => 'nom',
                      ])
                      -> add('date_propose', DateType::class)
                      -> add('heure_propose', TimeType::class)
                      -> add('outillages')
                      -> add('commentaires')
                      -> add('plannifie',ChoiceType::class, [
                            'choices'  => [
                            'NON' => false]])
                      -> add('Reccurance',ChoiceType::class, [
                        'label'    => 'si besoin de figer cette polymérisation suivant une réccurance',
                        'choices'  => [
                            'NON' => false,
                            'OUI' => true]])
                      ->getForm();
//Si la requette existe c'est une création, sinon une modification
        if($newdemande==false){
            dump($requette);
        }
        else{
//C'est le résultat de la requette du template Demandes pour la création
            $requette=$requestStack->getParentRequest();
            dump($requette);
        }
        $form->handleRequest($requette);

        if ($requette->isMethod('POST')) {
        //$form->submit($requette->request->get($form->getName()));
//On vérifie la validité des données avant de persister en base
            if($form->isSubmitted() && $form->isValid()){
                if(!$demande->getId()){
                    $demande->setDateCreation(new \datetime());
                    dump($user);
                    $demande->setUserCrea($user->getUsername());
                    $mode=true;
                }
                else{
                    $demande->setDateModif(new \datetime());
                    dump($user);
                    $demande->setUserModif($user->getUsername());
                    $mode=false;
                }
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($demande);
                $manager->flush();
                
                $requette->getSession()->getFlashbag()->add('success', 'Votre demande a été enregistré.');

                if($mode==false){
                    dump($demande);
                    
                    return $this->redirectToRoute('home');
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
     */
    public function Demandes(Request $requette, ObjectManager $manager)
    {
//Redirection après remplissage du formulaire 
        if (!$requette){
            dump($requette);
        }
        else{
            dump($requette);
        }
//Recherche du début et fin de la semaine n+1  pour effectuer les demandes de créneaux         
        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('Monday next week');
        $currentMonthDateTime = new \DateTime();
        $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
        $lastDateTime = $lastDateTime->modify('23 hours');
//Visualisation des demandes en cours par semaine
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
        $moyens=$repos -> findBy(['Id_Service' => '8']);
        $item=array();
        $data = [];
        $i = 0;
        foreach($moyens as $moyen){
            $data[$i] = ['id'=> $moyen->getId(),  'content'=> $moyen->getLibelle()];
            $i = $i + 1;
        }
        //dump($data);
        $moyen= new JsonResponse($data);
        //dump($moyen);
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
            $MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]);
            $data[$i] = ['id'=> $tache->getId(),'programmes'=> $tache->getAction(),'statut'=>$tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $tache->getNumDemande()->getCommentaires()];
            $i = $i + 1;
        }
    $repo=$this->getDoctrine()->getRepository(PolymReal::class);
    $Polyms=$repo -> findAll();
    //dump($data);
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $polym->getId(),'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
            $i = $i + 1;
        }
        //dump($data);
        $taches= new JsonResponse($data);
        //dump($taches);
        //dump($lastDateTime);
//Transfert des variables à la vue
        return $this->render('planning_mc/Demandes.html.twig',[
           'Titres' => $Titres,
           'datedeb' => $firstDateTime,
           'datefin' => $lastDateTime,
           'Cycles'=>$cycles,
           'moyens' => $moyen->getContent(),
           'taches' => $taches->getContent(),
           'reqet' => $requette
        ]);   
    }

    /**
     * @Route("/Demandes/Plannification/{id}", name="Planif_Demandes")
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
            dump($moyen);
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
        dump($errors);      
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
            dump($demande);
            if(!$demande->getPlanning()){
                $manager->persist($action);
                $manager->flush();
                dump($action);
                $manager->persist($demande);
                $manager->flush();
            }
            else{
                dump($action);
                $manager->remove($demande->getPlanning());
                $manager->flush();
            }
                dump($demande);
            
            $requette->getSession()->getFlashbag()->add('success', 'Votre polym a été enregistré.');
                
            if($mode==false){
                dump($demande);
                    
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
     */
    public function Planification(Demandes $demande=null)
    {
//Chargement d'une variable pour toutes les demandes créées
        $test = $this->getDoctrine()
            ->getRepository(demandes::class)
            ->findAll();
//Recherche du début de semaine et fin de semaine
        $currentMonthDateTime = new \DateTime();
        $firstDateTime = $currentMonthDateTime->modify('Monday next week');
        $currentMonthDateTime = new \DateTime();
        $lastDateTime = $currentMonthDateTime->modify('Sunday next week');
        $lastDateTime = $lastDateTime->modify('23 hours');
        //dump($firstDateTime);
        //dump($lastDateTime);
//Recherche des moyens à afficher sur planning
        $repos=$this->getDoctrine()->getRepository(Moyens::class);
        $moyens=$repos -> findBy(['Id_Service' => '8']);
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
            $data[$i] = ['id'=> $tache->getId(),'programmes'=> $tache->getAction(),'statut'=> $tache->getStatut(),'start'=> ($tache->getDebutDate())->format('c'),'end'=> ($tache->getFinDate())->format('c'),'group'=> $MoyUtil[0]->getId(),'style'=> 'background-color: '.$tache->getNumDemande()->getCycle()->getCouleur(),'title'=> $tache->getNumDemande()->getCommentaires()];
            $i = $i + 1;
            //dump($MoyUtil=$repos -> findBy(['Libelle' => $tache->getIdentification()]));
            //dump($MoyUtil[0]->getId());
        }
        $repo=$this->getDoctrine()->getRepository(PolymReal::class);
        $Polyms=$repo -> findAll();
        //dump($data);
        foreach($Polyms as $polym){ 
            $data[$i] = ['id'=> $polym->getId(),'programmes'=> $polym->getProgrammes()->getNom(),'statut'=>$polym->getStatut(),'start'=> ($polym->getDebPolym())->format('c'),'end'=> ($polym->getFinPolym())->format('c'),'group'=> $polym->getMoyens()->getid(),'style'=> 'background-color: '.$polym->getProgrammes()->getCouleur(),'title'=> $polym->getNomPolym()];
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
     * @Route("/Creation", name="Creation")
     * @Route("/Modification/{id}", name="Modification")
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
        dump($Titres);
        return $this->render('planning_mc/Creation.html.twig',[
            'Titres' => $Titres,
            'formProg' => $form->createView(),
        ]);

    }

    /**
     * @Route("/Consultation", name="Consultation")
     * @Route("/Consultation/{id}", name="Consul_ProgMoy")
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
    
}
