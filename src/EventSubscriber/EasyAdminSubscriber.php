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
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\Validator\Constraints\All;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $em;
    private $repo;
    private $agendaRepository;
    private $session;
    protected $request;

    public function __construct(EntityManagerInterface  $em, OrgaEqRepository $repo, AgendaRepository $agendaRepository, SessionInterface $session)
    {
        $this->em = $em;
        $this->repo= $repo;
        $this->agendaRepository=$agendaRepository;
        $this->session=$session;
        $this->request = Request::createFromGlobals();
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['getJourWAgenda'],
            AfterEntityPersistedEvent::class => ['setJourWAgenda'],
            //BeforeEntityUpdatedEvent::class => ['upJourWAgenda'],
            AfterEntityUpdatedEvent::class => ['upJourWAgenda'],
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

    public function upJourWAgenda(AfterEntityUpdatedEvent $event)
    {
        //Récupérer les anciennes dates
        $entity = $event->getEntityInstance();
        $oldatedeb=$this->request->cookies->get('olDateDeb');
        $oldatefin=$this->request->cookies->get('olDateFin');
        //Faire la différence avec les nouvelles dates
        //Si date pas encore atteinte, et plus tôt on enlève les tâches de l'agenda, sinon on rajoute
        if ($oldatedeb<new datetime && $oldatedeb!=$entity->getDateDebut()) {
            $msgAlert='La date de début de l\'organisation "'. $entity->getNomEquipe() .'" ne peux pas être modifiée car elle est dans le passé';
            $this->session->getFlashBag()->add('warning', $msgAlert);
            $entity->setDateDebut($oldatedeb);
            $entity->setDateFin($oldatefin);
            return;
        } elseif ($oldatedeb<$entity->getDateDebut()) {
            //On enlève des tâches de l'agenda
            $entity->setDateFin(date_modify($entity->getDateDebut(),'- 1 days'));
            $entity->setDateDebut($oldatedeb);
            dd($entity);
            $this->deleteJourWAgenda($entity);
        } elseif ($oldatedeb>$entity->getDateDebut()) {
            //On rajoute des tâches à l'agenda
            $entity->setDateDebut($entity->getDateDebut());
            dump($oldatedeb);
            $entity->setDateFin(date_modify($oldatedeb ,'- 1 days'));
            dd($entity);
            $this->putJourWAgenda($entity);
        } else {
            $msgInfo='Pas de modification de la date de début';
        }
        if ($oldatefin<new datetime) {
            $msgAlert='La date de fin ne peux pas être modifiée car dans le passé';
        } elseif ($oldatefin<$entity->getDateFin()) {
            //On rajoute des tâches à l'agenda
            $entity->setDateDebut(date_modify($oldatefin,'+ 1 days'));
            $entity->setDateFin($entity->getDateFin());
            $this->putJourWAgenda($entity);
        } elseif ($oldatefin>$entity->getDateFin()) {
            //On enlève des tâches à l'agenda
            $this->deleteJourWAgenda($entity);
        } else {
            $msgInfo=$msgInfo. ' '. 'Pas de modification de la date de fin';
            $this->session->getFlashBag()->add('info', $msgInfo);
        }
    }

    public function moveJJourWAgenda(BeforeEntityDeletedEvent $event)
    {
        dd($event);
    }

    public function getJourWAgenda(BeforeEntityPersistedEvent $event)
    {
        dd($event);
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
          $volHW=$entity->getNomEquipe()->getOrgaW()->getQteHeureW();
          //Récupération des dates de début et fin de planif de cette orga
          $dateDeb=$entity->getDateDebut();
          $dateFin=date_modify($entity->getDateFin(),'+'.$volHW.'hours');
          $interval = new DateInterval('P1D');
          $daterange = new DatePeriod($dateDeb, $interval ,$dateFin);
          $french_days = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
          //dd($tbJoursTypeW);
          //Créer une tâche pour chaque jour de l'intervalle de temps seulement sur les jours W
          foreach($daterange as $date){
              if (in_array($french_days[date("w",strtotime($date->format('Y-m-d')))], array_map('strtolower', $tbJoursTypeW))) {
                  $JW= new Agenda;
                  $JW->setNom($entity->getNomEquipe());
                  $JW->setDescription($entity->getTypeW());
                  $JW->setDateDeb($date);
                  $JW->setDateFin($date);
                  $JW->setTpsAlloue($volHW);
                  $JW->setNomOrgaW($entity);
                  $this->em->persist($JW);
              }    
          }
          $this->em->flush();
          $this->session->getFlashBag()->add('success', 'Création des créneaux de l\'equipe "'.$entity->getNomequipe().'" dans l\'agenda, jusqu\'en '.$entity->getDateFin());
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
        dump($tachesOrgaEq);
        $nbtacheSup=1;
        foreach ($tachesOrgaEq as $tache) {
            dump($tache);
            if ($tache->getDateDeb()>=$entity->getDateDebut() && $tache->getDateDeb()<= $entity->getDateFin()){
                dd($tache);
                $this->em->remove($tache);
                $nbtacheSup++;
            }
        }
        $this->session->getFlashBag()->add('success', 'Suppression de '.$nbtacheSup.' créneaux de l\'equipe "'.$entity->getNomequipe().'" dans l\'agenda, de '.$entity->getDateDeb().'jusqu\'en '.$entity->getDateFin());
    }
}