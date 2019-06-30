<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Entity\ConfSmenu;
use App\Entity\Moyens;

class SecurityController extends AbstractController
{
    /**
    * @Route("/inscription", name="security_registration")
    */
    Public function registration(request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        $user= new User();

        $form=$this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $user->setDateCreation(new \datetime());
            $manager->persist($user);
            $manager->flush($user);
            dump($Titres);
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig',[
            'form' => $form->createView(),
            'Titres' => $Titres
        ]);
    }

    /**
     * @Route("/connexion",name="security_login")
    */
    Public function login(){
//        $repo=$this->getDoctrine()->getRepository(ConfSmenu::class);
//
//        $Titres=$repo -> findAll();
            $Titres =[];
        return $this->render('security/login.html.twig',[
            'Titres' => $Titres
        ]);
    }

    /**
     * @Route("/deconnexion",name="security_logout")
    */
    Public function logout(){

        $Titres =[];
        return $this->render('security/logout.html.twig',[
            'Titres' => $Titres
        ]);
    }

}
