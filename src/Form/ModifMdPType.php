<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ModifMdPType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* ->add('mail', EntityType::class, [
                'class' =>User::class,
                'choice_label' => 'mail',
                'choice_value'=> 'mail']) */
            ->add('type',ChoiceType::class, [
                'label' => 'Identifiant type',
                'choices'  => [
                    'Email' => 'email',
                    'Username' => 'username',
                    'Matricule' => 'matricule'
                ],
                'expanded' => true,
                'mapped' => false
            ])
            ->add('mail', TextType::class, [
                'label' => 'User',
                'required'   => true,
                'help' => 'adresse mail ou matricule'])
            /* ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class); */
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}