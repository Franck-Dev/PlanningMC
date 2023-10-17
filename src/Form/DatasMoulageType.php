<?php

namespace App\Form;

use App\Entity\DatasMoulage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatasMoulageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('artGamm')
            ->add('DescriptifPhase')
            ->add('cycleMoulage')
            ->add('active')
            ->add('debVal')
            ->add('zoneTrav')
            ->add('eqOut')
            ->add('progLaser')
            ->add('progPolym')
            ->add('programme')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DatasMoulage::class,
        ]);
    }
}
