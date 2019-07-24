<?php

namespace App\Form;

use App\Entity\Moyens;
use App\Entity\Services;
use App\Entity\CategoryMoyens;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CreationMoyensType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Libelle')
            ->add('Id_Service')
            ->add('Description')
            ->add('Id_Types_Caracteristiques')
            ->add('Date_Maintenance', DateTimeType::class)
            ->add('Operationnel')
            ->add('Activitees',ChoiceType::class, [
                'choices'  => [
                'Plannification' => 'Plannifie',
                'Realisation' => 'Realisee']])
            ->add('categoryMoyens', EntityType::class, array(
                'class' =>CategoryMoyens::class,
                'choice_label' => 'libelle',))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Moyens::class,
        ]);
    }
}
