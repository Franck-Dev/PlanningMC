<?php

namespace App\Form;

use App\Entity\Charge;
use App\Entity\Demandes;
use App\Entity\Planning;
use App\Entity\ProgMoyens;
use App\Services\ComService;
use App\Repository\ChargeRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Repository\ProgMoyensRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DemandesType extends AbstractType
{
    private $chargeRepository;
    private $com;

    public function __construct(chargeRepository $chargeRepository, ComService $com)
    {
        $this->chargeRepository = $chargeRepository;
        $this->com = $com;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder -> add('cycle', EntityType::class, [
            'class' => ProgMoyens::class,
            'query_builder' => function (ProgMoyensRepository $er) {
              return $er->createQueryBuilder('u')
                  ->orderBy('u.Nom', 'DESC');
            },
            'choice_label' => 'nom',
            'placeholder' => 'Sélectionner votre cycle'
        ]);

        $formModifier = function (FormInterface $form, ProgMoyens $cycle = null, $chargeRepository) {
            $listOFCycle =  null === $cycle ? $chargeRepository->findBy(['Statut' => 'OUV'],['DateDeb' => 'ASC']) : $chargeRepository->findBy(['NumProg' => $cycle->getNom(),'Statut' => 'OUV'],
            ['DateDeb' => 'ASC']);
            //dump($chargeRepository->findByCyc('FP A34 STC', '2022-10-10', 'OUV'));
            //dd($listOFCycle);
            $form->add('ListOF', EntityType::class, [
                'class' => Charge::class,
                'placeholder' => 'Sélectionner les OF',
                'choices' => $listOFCycle,
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'label' => 'Liste des OF encours:'
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getCycle(), $this->chargeRepository);
            }
        );
        $builder->get('cycle')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $cycle = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback function!
                //dump($event->getForm()->getParent());
                $formModifier($event->getForm()->getParent(), $cycle, $this->chargeRepository);
            }
        );
        $builder -> add('date_propose', DateType::class,[
            'widget' => 'single_text',
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
}
