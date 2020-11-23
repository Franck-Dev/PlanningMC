<?php

namespace App\Form;

use App\Entity\Moyens;
use App\Entity\ChargFige;
use App\Entity\Outillages;
use App\Entity\ProgMoyens;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargFigeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Code')
            ->add('Statut')
            ->add('Pourc')
            ->add('OT', EntityType::class, array(
                'class' => Outillages::class,
                'choice_label' => 'Ref',
                'multiple' => true,
            ))
            ->add('Moyen', EntityType::class, array(
                'class' => Moyens::class,
                'choice_label' => 'Libelle',
                'multiple' => false,
            ))
            ->add('Programme', EntityType::class, array(
                'class' => ProgMoyens::class,
                'choice_label' => 'Nom',
                'multiple' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChargFige::class,
        ]);
    }
}
