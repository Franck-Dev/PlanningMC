<?php

namespace App\Form;

use App\Entity\Demandes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ComOutilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Commentaires', TextType::class, [
                'required'   => false,
                'help' => 'Insérer un commentaire concernant cette polymérisation.'])
            ->add('Outillages', TextType::class, [
                'required'   => false,
                'help' => 'Insérer les outillages à passer dans cette polymérisation.'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demandes::class,
        ]);
    }
}
