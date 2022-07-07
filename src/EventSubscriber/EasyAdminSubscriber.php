<?php

namespace App\EventSubscriber;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Entity\Agenda;
use App\Entity\OrgaEq;
use App\Entity\NomEquipe;
use App\Entity\TypesEquipe;
use App\Repository\AgendaRepository;
use App\Repository\OrgaEqRepository;
use App\Repository\NomEquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypesEquipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $em;
    private $repo;
    private $agendaRepository;
    private $requestStack;
    protected $request;
    private $session;

    public function __construct(EntityManagerInterface  $em, OrgaEqRepository $repo, AgendaRepository $agendaRepository, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->repo= $repo;
        $this->agendaRepository=$agendaRepository;
        $this->requestStack=$requestStack;
        $this->request = Request::createFromGlobals();
        $this->session=$requestStack->getSession();
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['getJourWAgenda'],
            AfterEntityPersistedEvent::class => ['setJourWAgenda'],
            BeforeEntityUpdatedEvent::class => ['upJourWAgenda'],
            //AfterEntityUpdatedEvent::class => ['upJourWAgenda'],
            BeforeEntityDeletedEvent::class => ['moveJJourWAgenda'],
            BeforeCrudActionEvent::class => ['checkDatesOld'],
        ];
    }

    public function checkDatesOld(BeforeCrudActionEvent $crud)
    {
        //$crudAction = $crud->getAdminContext()->getCrud();
        $entity= $crud->getAdminContext()->getEntity();
        if (!($entity->getInstance() instanceof OrgaEq)) {
            //dd($crudAction->getCurrentAction());
            return;
        } else {
            $dateDeb = $entity->getInstance()->getDateDebut();
            $dateFin = $entity->getInstance()->getDateFin();
            $this->request->cookies->add(['olDateDeb' => $dateDeb, 'olDateFin' => $dateFin ]);
        }  
    }

    public function setJourWAgenda(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        //dd($entity->getNomEquipe()->getOrgaW()->getJourw());
        if (!($entity instanceof OrgaEq)) {
            return;
        } else {
            $this->putJourWAgenda($entity);
        }
    }

    public function upJourWAgenda(BeforeEntityUpdatedEvent $event)
    {
        //Récupérer les anciennes dates
        $entity = $event->getEntityInstance();
        $oldatedeb=$this->request->cookies->get('olDateDeb');
        $oldatefin=$this->request->cookies->get('olDateFin');
        $newdatedeb=$entity->getDateDebut();
        $newdatefin=$entity->getDateFin();
        //Faire la différence avec les nouvelles dates
        //Si date pas encore atteinte, et plus tôt on enlève les tâches de l'agenda, sinon on rajoute
        if ($oldatedeb<new datetime && $oldatedeb!=$entity->getDateDebut()) {
            $msgAlert='La date de début de l\'organisation "'. $entity->getNomEquipe() .'" ne peux pas être modifiée car elle est dans le passé';
            $this->session->getFlashBag()->add('warning', $msgAlert);
            $entity->setDateDebut($oldatedeb);
            $entity->setDateFin($oldatefin);
            return;
        }
        //Si nouvelle date dans le futur par rapport à l'ancienne
        if ($oldatedeb<$entity->getDateDebut()) {
            //On enlève des tâches de l'agenda
            $entity->setDateFin(date_modify($entity->getDateDebut(),'- 1 days'));
            $entity->setDateDebut($oldatedeb);
            $this->deleteJourWAgenda($entity);
        } elseif ($oldatedeb>$entity->getDateDebut()) {
            //On rajoute des tâches à l'agenda
            $entity->setDateDebut($newdatedeb);
            dump($oldatedeb);
            $entity->setDateFin(date_modify($oldatedeb ,'- 1 days'));
            //dd($entity);
            $this->putJourWAgenda($entity);
        } else {
            $msgInfo='Pas de modification de la date de début';
        }
        
        if ($oldatefin<new datetime) {
            $msgAlert='La date de fin ne peux pas être modifiée car dans le passé';
            $this->session->getFlashBag()->add('warning', $msgAlert);
            $entity->setDateFin($oldatefin);
            return;
        }
        if ($oldatefin<$entity->getDateFin()) {
            //On rajoute des tâches à l'agenda
            $entity->setDateDebut(new datetime(date_modify($oldatefin,'+ 1 days')->format('Y-m-d').' '.$entity->getDateDebut()->format('H:i')));
            $entity->setDateFin($newdatefin);
            $this->putJourWAgenda($entity);
        } elseif ($oldatefin>$newdatefin) {
            //On enlève des tâches à l'agenda
            $entity->setDateDebut(date_modify($newdatefin,'+ 1 days'));
            $entity->setDateFin($oldatefin);
            $this->deleteJourWAgenda($entity);
        } else {
            $msgInfo=$msgInfo. ' '. 'Pas de modification de la date de fin';
            $this->session->getFlashBag()->add('info', $msgInfo);
        }
        $entity->setDateDebut($newdatedeb);
        $entity->setDateFin($newdatefin);
    }

    public function moveJJourWAgenda(BeforeEntityDeletedEvent $event)
    {
        dd($event);
    }

    public function getJourWAgenda(BeforeEntityPersistedEvent $event)
    {
        //dd($event);
    }
    
    /**
     * Fonction permettant la création des tâches dans l'agenda à partir de l'OrgaEquipe
     *
     * @param  mixed $entity
     * @return void
     */
    private function putJourWAgenda($entity)
    {
          //Récupération des jours W de cette orga via typeW
          $tbJoursTypeW=$entity->getNomEquipe()->getOrgaW()->getJourw();
          //Récupération des dates de début et fin de planif de cette orga
          $dateDeb=$entity->getDateDebut();
          $dateFin=$entity->getDateFin();
          $interval = new DateInterval('P1D');
          $daterange = new DatePeriod($dateDeb, $interval ,$dateFin);
          $french_days = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
          //Créer une tâche pour chaque jour de l'intervalle de temps seulement sur les jours W
          foreach($daterange as $date){
              if (in_array($french_days[date("w",strtotime($date->format('Y-m-d')))], array_map('strtolower', $tbJoursTypeW))) {
                  $JW= new Agenda;
                  $JW->setNom($entity->getNomEquipe());
                  $JW->setDescription($entity->getTypeW());
                  $JW->setDateDeb($date);
                  $newdateagenda=clone($date);
                  //Gestion du quota horraire des jours particuliers
                  switch ($french_days[date("w",strtotime($date->format('Y-m-d')))]) {
                      case 'lundi':
                        $volHW=$this->qteHorLundi($entity->getNomEquipe()->getOrgaW()->getQteHeureW(),$date);
                        break;
                      case 'vendredi':
                        $volHW=$this->qteHorVendredi($entity->getNomEquipe()->getOrgaW()->getQteHeureW());
                        break;
                      case 'samedi':
                        $volHW=$this->qteHorSamedi($entity->getNomEquipe()->getOrgaW()->getQteHeureW());
                        break;
                      case 'dimanche':
                        $volHW=$this->qteHorDimanche($entity->getNomEquipe()->getOrgaW()->getQteHeureW());
                        break;
                      default:
                        $volHW=$entity->getNomEquipe()->getOrgaW()->getQteHeureW();
                        break;
                  }
                  $JW->setDateFin(date_modify($newdateagenda,'+'.$volHW.'hours'));
                  $JW->setTpsAlloue($volHW);
                  $JW->setNomOrgaW($entity);
                  $this->em->persist($JW);
              }    
          }
          $this->em->flush();
          $this->session->getFlashBag()->add('success', 'Création des créneaux de l\'equipe "'.$entity->getNomequipe().'" dans l\'agenda, jusqu\'au '.$entity->getDateFin()->format('d/m/Y'));
    }
    
    /**
     * Fonction permettant de supprimer des tâches de l'agenda d'une modification d'OrgaEquipe
     *
     * @param  mixed $entity
     * @return void
     */
    private function deleteJourWAgenda($entity)
    {
        //Récupération par l'ID de l'OrgaEq pour modifier les tâches correspondantes de l'entity
        //dd($entity);
        $tachesOrgaEq=$this->agendaRepository->findBy(['NomOrgaW' => $entity->getId()]);
        //dump($tachesOrgaEq);
        $nbtacheSup=1;
        foreach ($tachesOrgaEq as $tache) {
            //dump($tache);
            if ($tache->getDateDeb()>=$entity->getDateDebut() && $tache->getDateDeb()<= $entity->getDateFin()){
                //dd($tache);
                $this->em->remove($tache);
                $nbtacheSup++;
            }
        }
        $this->session->getFlashBag()->add('success', 'Suppression de '.$nbtacheSup.' créneaux de l\'equipe "'.$entity->getNomequipe().'" dans l\'agenda, de '.$entity->getDateDebut()->format('d/m/Y').'jusqu\'au '.$entity->getDateFin()->format('d/m/Y'));
    }
    
    /**
     * Fonction permettant de moduler le quota horraire du lundi suivant les organisations(3x8 et VSD)
     *
     * @param  int $tpsAlloueOrga
     * @return void
     */
    private function qteHorLundi($tpsAlloueOrga, $date) 
    {
        //Voir si VSD actif pour cette veille de cette date, si oui rajouter des heures au 3x8
        $tacheVeille=$this->agendaRepository->findBy(['DateDeb' => $date]);
        if ($tacheVeille->getNomOrgaW()->getNomEquipe()->getOrgaW()->getNom()=="VSD") {
            $rajoutHVSD=5;
        }else{
            $rajoutHVSD=0;
        }
        switch ($tpsAlloueOrga) {
            case '24':
                $qteHor=19+$rajoutHVSD;
                break;
            default:
                $qteHor=$tpsAlloueOrga;
                break;
        }
        return $qteHor;
    }

    /**
     * Fonction permettant de moduler le quota horraire du vendredi suivant les organisations(3x8 et VSD)
     *
     * @param  int $tpsAlloueOrga
     * @return void
     */
    private function qteHorVendredi($tpsAlloueOrga) 
    {
        switch ($tpsAlloueOrga) {
            case '24':
                $qteHor=21;
                break;
            case '10':
                $qteHor=3;
                break;
            default:
                $qteHor=$tpsAlloueOrga;
                break;
        }
        return $qteHor;
    }

    /**
     * Fonction permettant de moduler le quota horraire du samedi pour le VSD
     *
     * @param  int $tpsAlloueOrga
     * @return void
     */
    private function qteHorSamedi($tpsAlloueOrga) 
    {
        switch ($tpsAlloueOrga) {
            case '10':
                $qteHor=9;
                break;
            default:
                $qteHor=$tpsAlloueOrga;
                break;
        }
        return $qteHor;
    }

    /**
     * Fonction permettant de moduler le quota horraire du dimanche pour le VSD
     *
     * @param  int $tpsAlloueOrga
     * @return void
     */
    private function qteHorDimanche($tpsAlloueOrga) 
    {
        switch ($tpsAlloueOrga) {
            case '10':
                $qteHor=12;
                break;
            default:
                $qteHor=$tpsAlloueOrga;
                break;
        }
        return $qteHor;
    }
}