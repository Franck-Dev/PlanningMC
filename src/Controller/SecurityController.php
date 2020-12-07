<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ModifMdPType;
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
                    }elseif($user->getPoste()->getLibelle() == "Responsable"){
                        $user->setRoles(['ROLE_RESP_MOULAGE']);
                    }  
                    else{
                        $user->setRoles(['ROLE_USER']);
                    }
                    break;
                case "EXTER":
                    $user->setRoles(['ROLE_USER']);
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
            //dump($error);
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

    /**
     * * @Route("/inscription/Modification", name="Modif_Inscription")
    */
    Public function ModifMdP(request $request, ObjectManager $manager, user $user=null, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator){

        $Titres=[];
        //Si le formulaire est rempli
        if($request->get('modif_md_p')){
            //On va chercher dans la base l'utilisateur en question
            $user= new User();
            $repo=$this->getDoctrine()->getRepository(User::class);
            $items=$repo -> findBy(['email' => $request->get('modif_md_p')['email']]);
            $user=$items[0];
            //On l'empile dans le formulaire
            $form=$this->createForm(ModifMdPType::class, $user);
            //On force la validation (pas terrible, /!\ a changer)
            $form->submit($form->getName());
            $errors = $validator -> validate ($form);
        }
        //Sinon on génère le formulaire vide
        else{
            $form=$this->createForm(ModifMdPType::class, $user);
        }
        //dump($request);
        //dump($request->get('modif_md_p')['password']);
        if($form->isSubmitted()){
            $hash=$encoder->encodePassword($user, $request->get('modif_md_p')['password']);
            //dump($hash);
            $user->setIsActive('0');
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush($user);
            
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/ModificationMdP.html.twig',[
            'form' => $form->createView(),
            'Titres' => $Titres
        ]);
    }
}
