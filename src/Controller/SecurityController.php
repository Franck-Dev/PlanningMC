<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Entity\ConfSmenu;
use App\Entity\Moyens;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
        //dump($form);
        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user, $user->getPassword());
            //Attribution des rôles suivant les postes (a revoir pour faire une page admin)
            switch ($user->getService()->getNom()) {
                case "METHODES PE":
                    if($user->getPoste()->getLibelle() == "Programmeur"){
                        $user->setRoles(['ROLE_METHODES','ROLE_PROGRAMMEUR']);
                    } elseif ($user->getPoste()->getLibelle() == "Support") {
                        //dump($user->getPoste()->getLibelle());
                        $user->setRoles(['ROLE_METHODES','ROLE_DATATOOLS']);
                    }else{
                        $user->setRoles(['ROLE_METHODES']);
                    }
                    break;
                case "MOYEN CHAUD":
                    if($user->getPoste()->getLibelle() == "Maitrise"){
                        $user->setRoles(['ROLE_CE_POLYM']);
                    }else{
                        $user->setRoles(['ROLE_REGLEUR']);
                    }
                    break;
                case "MOULAGE":
                    if($user->getPoste()->getLibelle() == "Maitrise"){
                        $user->setRoles(['ROLE_CE_MOULAGE']);
                    }else{
                        $user->setRoles(['ROLE_USER']);
                    }
                    break;
                break;
            }
            $user->setIsActive('0');
            $user->setPassword($hash);
            $user->setDateCreation(new \datetime());
            // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
            $manager->persist($user);
            $manager->flush($user);
            
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
    Public function login(AuthenticationUtils $authenticationUtils) : Response
    {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            $Titres =[];
            dump($error);
        return $this->render('security/login.html.twig',[
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_user' => $authenticationUtils->getLastUsername(),
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
