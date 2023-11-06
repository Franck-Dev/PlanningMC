<?php

namespace App\Form;

use App\Entity\ProgAvions;
use App\Entity\ProgMoyens;
use App\Entity\DatasMoulage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class DatasMoulageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('artGamm')
            ->add('DescriptifPhase')
            ->add('cycleMoulage', DateIntervalType::class, [
                'widget'      => 'integer', // render a text field for each part
                // 'input'    => 'string',  // if you want the field to return a ISO 8601 string back to you
            
                // customize which text boxes are shown
                'with_years'  => false,
                'with_months' => false,
                'with_days'   => false,
                'with_hours'  => true,
            ])
            ->add('active')
            ->add('debVal', DateType::class,[
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ]
            ])
            ->add('zoneTrav',ChoiceType::class, [
                'label'    => 'Zone de travail',
                'choices'  => [
                    'Salle de Moulage 1' => 'SdM1',
                    'Salle de Moulage 2' => 'SdM2',
                    'Salle de Moulage 3' => 'SdM3',
                    'Salle de Collage 1' => 'SdC1']])
            ->add('eqOut')
            ->add('progLaser', EntityType::class, [
                'class' => ProgMoyens::class,
                'choice_label' => 'nom'
            ])
            ->add('progPolym', EntityType::class, [
                'class' => ProgMoyens::class,
                'choice_label' => 'nom'
            ])
            ->add('programme', EntityType::class, [
                'class' => ProgAvions::class,
                'choice_label' => 'libelle'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DatasMoulage::class,
        ]);
    }
}
