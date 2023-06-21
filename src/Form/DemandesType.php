<?php

namespace App\Form;

use App\Entity\Charge;
use App\Entity\Demandes;
use App\Entity\Planning;
use App\Entity\ChargFige;
use App\Entity\ProgMoyens;
use App\Services\ComService;
use App\Services\FunctChargPlan;
use App\Repository\ChargeRepository;
use Symfony\Component\Form\FormEvent;
use App\Repository\ArticlesRepository;
use Symfony\Component\Form\FormEvents;
use App\Repository\ChargFigeRepository;
use App\Repository\ChargementRepository;
use App\Repository\OutillagesRepository;
use App\Repository\ProgMoyensRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\SecurityBundle\Security;

class DemandesType extends AbstractType
{
    private $chargeRepository;
    private $chargFigeRepository;
    private $com;
    private $security;
    private $repo;
    private $Out;
    private $Art;
    private $chargeFige;

    public function __construct(chargeRepository $chargeRepository, ComService $com, Security $security, ChargFigeRepository $chargFigeRepository,
    ChargeRepository $repo, OutillagesRepository $Out, ArticlesRepository $Art, FunctChargPlan $chargeFige)
    {
        $this->chargeRepository = $chargeRepository;
        $this->com = $com;
        $this->security = $security;
        $this->chargFigeRepository = $chargFigeRepository;
        $this->repo = $repo;
        $this->Out = $Out;
        $this->Art = $Art;
        $this->chargeFige = $chargeFige;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Récupération du user pour filtrer sur les programmes avions de ce dernier
        $user = $this->security->getUser();
        //Liste des avions en charge du user
        $progAvions=$user->getProgrammeAvion();
        //Création du tableau de recherche
        if ($progAvions) {
            foreach ($progAvions as $key=>$avion) {
                $listAvions[$key]=$avion['designation'];
            }
        } else {
            $listAvions=[];
        }
        
        if (!$listAvions) {
            $builder -> add('cycle', EntityType::class, [
                'class' => ProgMoyens::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner votre cycle'
            ]);
        } else {
            $builder -> add('cycle', EntityType::class, [
                'class' => ProgMoyens::class,
                'query_builder' => function (ProgMoyensRepository $er)  use ($listAvions){
                  return $er->createQueryBuilder('u')
                    ->leftjoin("u.avion", "t")
                    ->where('t.libelle IN (:avions)')
                    ->setParameter('avions', $listAvions)
                    ->orderBy('u.Nom', 'DESC');
                },
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner votre cycle'
            ]);
        }

        $formModifier = function (FormInterface $form, ProgMoyens $cycle = null, $chargeRepository, $chargFigeRepository, $options) {
            $listOFCycle =  null === $cycle ? $chargeRepository->findBy(['Statut' => 'OUV'],['DateDeb' => 'ASC']) : $chargeRepository->findBy(['NumProg' => $cycle->getNom(),'Statut' => 'OUV'],
            ['DateDeb' => 'ASC']);

            dump($cycle);
            $listCTOCycle =  null === $cycle ? $chargFigeRepository->findBy(['Statut' => '2']) : $chargFigeRepository->findBy(['Programme' => $cycle->getId(),'Statut' => '2']);

            $listCTOCycle = null === $cycle ?  $listCTOCycle : $this->checkRemplissage($listCTOCycle, $options['data']->getDatePropose());
            dump($listCTOCycle);

            $form->add('ListOF', EntityType::class, [
                'class' => Charge::class,
                'placeholder' => 'Sélectionner les OF',
                'choices' => $listOFCycle,
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
                'label' => 'Liste des OF encours: ('. count($listOFCycle) .' OF)'
            ]);

            $form->add('ListCTO', EntityType::class, [
                'class' => ChargFige::class,
                'placeholder' => 'Sélectionner un chargement type',
                'choices' => $listCTOCycle,
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                'by_reference' => false,
                'required' => false,
                'label' => 'Liste des CTO :'
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier, $options) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getCycle(), $this->chargeRepository, $this->chargFigeRepository, $options);
            }
        );
        $builder->get('cycle')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier, $options) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $cycle = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback function!
                $formModifier($event->getForm()->getParent(), $cycle, $this->chargeRepository, $this->chargFigeRepository, $options);
            }
        );
        $builder -> add('date_propose', DateType::class,[
            'widget' => 'single_text',
            'attr' => [
                'class' => 'js-datepicker',
                'min' => date('Y-m-d',strtotime(date('Y-m-d').'+ 2 days'))
            ]
        ]);
        $builder -> add('heure_propose', TimeType::class,[
            'widget' => 'single_text',
        ]);
        $builder -> add('outillages',TextareaType::class, [
            'row_attr' => ['class' => 'text-editor', 'id' => 'Outillage'],
            'required' => false]);
        $builder -> add('commentaires', TextareaType::class, [
            'row_attr' => ['class' => 'text-editor', 'id' => 'Commentaire'],
            'required' => false]);
        $builder -> add('Reccurance',ChoiceType::class, [
            'label'    => 'Récurrance demande',
            'choices'  => [
                'NON' => false,
                'OUI' => true]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demandes::class,
        ]);
    }
    
    /**
     * checkRemplissage Fonction pour remonter les remplissages des CTO par Of de charge
     *
     * @param  array $listCTO
     * @return array
     */
    private function checkRemplissage($listCTO, $dateJour)
    {
        $creno['Jour'] = $dateJour;
        $creno['NbrPcs']=1;
        foreach ($listCTO as $key => $CTO) {
            $creno['Cycles'] = $CTO->getProgramme()->getNom();
            $listOneCTO[0]=$CTO;

            $tabTemp = $this->chargeFige->checkOTCTO($listOneCTO, $this->repo, $this->Out, $this->Art, $creno);

            $listCTO[$key] = $CTO->setRemplissage($tabTemp[0]['Remplissage'][0]);
        }
        return $listCTO;
    }
}
