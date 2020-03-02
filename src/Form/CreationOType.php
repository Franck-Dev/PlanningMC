<?php

namespace App\Form;

use App\Entity\Outillages;
use App\Entity\ProgMoyens;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CreationOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Ref', TextType::class, array(
                'attr' => array('maxlength' => 8, 'placeholder' => 'OTXXXXX'),
                'help' => 'Mettre le numéro d\'OT de SAP',
                'required' => true,
                'empty_data' => null
            ))
            ->add('Designation', TextType::class, array(
                'attr' => array('maxlength' => 30, 'placeholder' => 'Renseigner la désignation de la pièce et la version de l\'OT. Ex: OT Moulage Pan 213 Z02'),
                'help' => 'Mettre le numéro d\'OT de SAP',
                'required' => true,
                'empty_data' => null
            ))
            ->add('NbEmpreinte', NumberType::class, array(
                'attr' => array('maxlength' => 2, 'placeholder' => 'Renseigner le nombre de pièces(OF) réalisés avec cet outillage'),
                'required' => true,
                'empty_data' => null
            ))
            ->add('NbThermocouples', NumberType::class, array(
                'attr' => array('maxlength' => 2, 'placeholder' => 'Renseigner le nombre de TC branché sur cet outillage'),
                'required' => true,
                'empty_data' => null
            ))
            ->add('Hauteur', NumberType::class, array(
                'scale' => 2,
                'attr' => array('maxlength' => 3, 'placeholder' => 'Renseigner la hauteur de l\'outillage'),
                'help' => 'Renseigner la valeur en ml',
                'required' => true,
                'empty_data' => null
            ))
            ->add('Longueur', NumberType::class, array(
                'attr' => array('maxlength' => 4, 'placeholder' => 'Renseigner la longueur de l\'outillage'),
                'help' => 'Renseigner la valeur en ml',
                'required' => true,
                'empty_data' => null
            ))
            ->add('Largeur', NumberType::class, array(
                'attr' => array('maxlength' => 3, 'placeholder' => 'Renseigner la largeur de l\'outillage'),
                'help' => 'Renseigner la valeur en ml',
                'required' => true,
                'empty_data' => null
            ))
            ->add('Volume', NumberType::class, array(
                'attr' => array('maxlength' => 5, 'placeholder' => 'Renseigner le volume de l\'outillage'),
                'help' => 'Renseigner la valeur en m3',
                'required' => true,
                'empty_data' => null
            ))
            ->add('CoefAero')
            ->add('Dispo', ChoiceType::class, array(
                'choices' => array('OK' => '1', 'HS'  => '2', 'MAINTENANCE' => '3', 'TRAITEMENT' => '4'),
                'empty_data' => '1',
                'preferred_choices' => 'OK',
                'required' => true,
                'expanded' => true,
                'multiple' => false
            ))
            ->add('Programme', EntityType::class, array(
                'class' => ProgMoyens::class,
                'choice_label' => 'Nom',
                'multiple' => true,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outillages::class,
        ]);
    }
}
