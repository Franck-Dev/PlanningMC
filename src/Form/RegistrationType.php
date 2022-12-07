<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Postes;
use App\Entity\Services;
use App\Services\CallApiService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    private $api;

    public function __construct(CallApiService $api)
    {
        $this->api = $api;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Recupération de la liste des programmes avions
        $progAvions=$this->api->getDatasAPI('/api/programme_avions/','Usine',[],'GET');
        //Création du tableau de recherche
        foreach ($progAvions as $avion) {
            $listAvions[$avion['designation']]=$avion['id'].'-'.$avion['designation'];
        }
        //Recupération de la liste des sites
        $usines=$this->api->getDatasAPI('/api/usines/','Usine',[],'GET');
        //Création du tableau de recherche
        foreach ($usines as $key => $usine) {
            $listUsines[$usine['nom']]=$usine['id'].'-'.$usine['nom'];
        }
        //Recupération de la liste des unités
        $divisions=$this->api->getDatasAPI('/api/divisions/','Usine',[],'GET');
        //Création du tableau de recherche
        foreach ($divisions as $key => $division) {
            $listDivisions[$division['nom']]=$division['id'].'-'.$division['nom'];
        }
        //Recupération de la liste des services
        $services=$this->api->getDatasAPI('/api/services/','Usine',[],'GET');
        //Création du tableau de recherche
        foreach ($services as $key => $service) {
            $listServices[$service['nom']]=$service['id'].'-'.$service['nom'];
        }
        //Recupération de la liste des postes
        $postes=$this->api->getDatasAPI('/api/postes/','Usine',[],'GET');
        //Création du tableau de recherche
        foreach ($postes as $key => $poste) {
            $listPostes[$poste['libelle']]=$poste['id'].'-'.$poste['libelle'];
        }

        $builder
            ->add('nom', TextType::class, ['mapped' => false, 'required' => true])
            ->add('prenom', TextType::class, ['mapped' => false, 'required' => true])
            ->add('matricule', TextType::class, ['mapped' => false, 'required' => true])
            ->add('unite', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'choices'  => $listDivisions,
                'choice_label' => function($listDivisions, $key, $division){
                    return $key;
                }
                ])
            ->add('site', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'choices'  => $listUsines,
                'choice_label' => function($listUsines, $key, $usine){
                    return $key;
                }
                ])
            ->add('mail')
            //->add('username', HiddenType::class)
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('service', ChoiceType::class, [
                'choices'  => $listServices,
                'choice_label' => function($listServices, $key, $service){
                    return $key;
                }
                ])
            ->add('poste', ChoiceType::class, [
                'choices'  => $listPostes,
                'choice_label' => function($listPostes, $key, $poste){
                    return $key;
                }
                ])
            /*->add('service', EntityType::class, array(
                'class' =>Services::class,
                'choice_label' => 'nom',))
            ->add('poste', EntityType::class, array(
                'class' =>Postes::class,
                'choice_label' => 'libelle',))*/
            -> add('programmeAvion',ChoiceType::class, [
                'label'    => 'Choisir les programmes qui vous sont attribués',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'choices'  => $listAvions,
                'choice_label' => function($listAvions, $key, $avion){
                    return $key;
                }
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
