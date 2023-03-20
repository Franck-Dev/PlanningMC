<?php

namespace App\Form;

use App\Entity\ProgAvions;
use App\Entity\ProgMoyens;
use App\Entity\CategoryMoyens;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class CreationProgType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('CateMoyen', EntityType::class, array(
                'class' =>CategoryMoyens::class,
                'choice_label' => 'libelle',))
            ->add('Nom')
            ->add('Description')
            ->add('Tps_Theo')
            ->add('Duree', TimeType::class)
            ->add('Tps_Chargement', TimeType::class)
            ->add('Tps_Dechargement', TimeType::class)
            ->add('Thermocouples')
            ->add('SPC')
            ->add('couleur', colortype::class)
            ->add('avion', EntityType::class, array(
                'class' =>ProgAvions::class,
                'mapped' => true,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProgMoyens::class,
        ]);
    }
}
