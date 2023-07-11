<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Outillages;
use App\Entity\ProgMoyens;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Reference', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan([
                        'value' => 7000000,
                    ]),
                    new LessThan([
                        'value' => 7999999,
                    ])
                ],
            ])
            ->add('Designation')
            ->add('OutMoulage', EntityType::class, array(
                'class' => Outillages::class,
                'choice_label' => 'Ref',
                'multiple' => true,
                'label' => 'Veuillez sélectionner le ou les outillage(s) lié(s) à cet article'
            ))
            ->add('Serie')
            ->add('ProgPolym',EntityType::class,[
                'class' => ProgMoyens::class,
                'choice_label' => 'Nom',
                'multiple' => true,
                'label' => 'Veuillez sélectionner le ou les programme(s) lié(s) à cet article'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
