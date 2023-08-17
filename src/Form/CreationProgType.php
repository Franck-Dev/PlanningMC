<?php

namespace App\Form;

use App\Entity\Moyens;
use App\Entity\ProgAvions;
use App\Entity\ProgMoyens;
use App\Entity\CategoryMoyens;
use App\Repository\MoyensRepository;
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
            ->add('Duree', TimeType::class,[
                'widget' => 'single_text',
            ])
            ->add('Tps_Chargement', TimeType::class,[
                'widget' => 'single_text',
            ])
            ->add('Tps_Dechargement', TimeType::class,[
                'widget' => 'single_text',
            ])
            ->add('Thermocouples')
            ->add('SPC')
            ->add('couleur', colortype::class)
            ->add('avion', EntityType::class, array(
                'class' =>ProgAvions::class,
                'mapped' => true,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Programmes Avions'
                ))
            ->add('moyenVal', EntityType::class, array(
                'class' =>Moyens::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (MoyensRepository $er)  use ($options){
                    return $er->createQueryBuilder('u')
                      ->where('u.Id_Service = :id')
                      ->andWhere('u.Activitees = :act')
                      ->setParameter('id', $options['idService'])
                      ->setParameter('act', $options['activitees'])
                      ->orderBy('u.Libelle', 'ASC');
                    },
                'label' => 'Moyens validés'
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProgMoyens::class,
            'idService' => "",
            'activitees' => "",
        ]);
    }
}
