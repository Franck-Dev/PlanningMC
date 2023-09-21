<?php

namespace App\Form;

use App\Entity\Moyens;
use App\Entity\Services;
use App\Entity\CategoryMoyens;
use App\Repository\ServicesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CreationMoyensType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        dump($options['listValService']);
        $builder
            ->add('Libelle')
            ->add('Id_Service', ChoiceType::class, [
                'choices' => $options['listValService']])
            ->add('Description')
            ->add('Id_Types_Caracteristiques', ChoiceType::class, [
                'choices'  => [
                'Unique' => 1,
                'Multiple' => 2]])
            -> add('Date_Maintenance', DateTimeType::class,[
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker',
                    'min' => date('Y-m-d H:i',strtotime(date('Y-m-d').'+ 1 days'))
                ]
            ])
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
            'listValService' => [9],
        ]);
    }
}
