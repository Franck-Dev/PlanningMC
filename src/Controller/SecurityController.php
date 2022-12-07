<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Moyens;
use App\Entity\ConfSmenu;
use App\Form\ModifMdPType;
use App\Form\RegistrationType;
use App\Security\ApiKeyAuthenticator;
use App\Services\CallApiService;
use App\Services\ComService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
    * @Route("/inscription", name="security_registration")
    */
    Public function registration(request $request, ManagerRegistry $manaReg, CallApiService $api, UserPasswordHasherInterface $encoder, ComService $com){
        $repo=$manaReg->getRepository(ConfSmenu::class);

        $Titres=$repo -> findAll();

        $user= new User();
        $form=$this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Création liste avions
            if ($form->getData()->getProgrammeAvion()){
                foreach($form->getData()->getProgrammeAvion() as $index => $avion){
                    $listAvions[$index]='/api/programme_avions/'.explode("-", $avion)[0];
                }
            }else{
                $listAvions=[];
            }
            
            // Création du body à envoyer
            foreach($request->request as $key => $data){
                $body['password']=$data['password'];
                $body['matricule']=intval($data['matricule']);
                $body['nom']=$data['nom'];
                $body['prenom']=$data['prenom'];
                $body['poste']='/api/postes/'.explode("-",$data['poste'])[0];
                $body['service']='/api/services/'.explode("-",$data['service'])[0];
                $body['programmeAvion']=$listAvions;
                $body['unite']='/api/divisions/'.explode("-",$data['unite'])[0];
                $body['site']='/api/usines/'.explode("-",$data['site'])[0];
                $body['mail']=$data['mail'];

            }
            $response=$api->getDatasAPI('/api/users','Usine',$body,'POST');

            // Création du $user pour enregistrement en local par retour enregistrement dans api
            $user->hydrate($response);
            // Hachage du mot de passe
            $hash=$encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            // Mise en tableau simple des programmes avions
            //$user->setProgrammeAvion(call_user_func_array('array_merge', $user->getProgrammeAvion()));
            // Enregistrement
            $manager = $manaReg->getManager();
            $manager->persist($user);
            $manager->flush();

            //Notification du user de son inscription
            $com->sendNotif('Bonjour '. $user->getUsername(). ' , merci de vous être inscrit sur l\'APAMC. 
            Votre compte doit être vérifier et valider par votre supérieur hiérarchique si vous n\'êtes pas opérateur.');
            //Notification de l'admin pour vérification et validation du compte
            $com->sendNotif('Un nouvel utilisateur: '. $user->getUsername(). ' est inscrit.',['admin']);
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
    ** @Route("/inscription/Modification_MdP", name="Modif_MdP")
    */
    Public function ModifMdP(request $request, user $user=null, ValidatorInterface $validator, ApiKeyAuthenticator $auth, CallApiService $api){

        $Titres=[];
        $errors=[];
        $form=$this->createForm(ModifMdPType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            //Vérification si le formulaire est bien remplie
            $errors = $validator -> validate ($form);
            //Récupération type d'identification
            $type=$request->get('modif_md_p')['type'];
            $username=$request->get('modif_md_p')['mail'];
            if ($type==='email')
            {
                $username = strstr($request->get('modif_md_p')['mail'], '@', true);
                $request->get('modif_md_p')['mail']=$username;
                $type='username';
            }
            //Vérification si l'utilisateur est connu, pour récup matricule
            $user=$api->getUserExist($username,$type);
            if (!$user)
            {
                $error=new FormError('utilisateur inexistant pour '.$type .' : '.$username);
                $form->addError($error);
            }
        }
        if($form->isSubmitted() && $form->isValid()){
            //On va chercher dans la base l'utilisateur en question
            $userResp=$api->getDatasAPI('/api/users/'.$user[0]['id'],'Usine',['password'=>$request->get('modif_md_p')['password']],'PATCH');
            
            return $this->redirectToRoute('security_login');
        }

        return $this->renderForm('security/ModificationMdP.html.twig',[
            'form' => $form,
            'errors' => $errors,
            'Titres' => $Titres
        ]);
    }

    /**
    ** @Route("/inscription/Modification_User/{id}", name="Modif_User")
    */

    Public function ModifUser(request $request, user $user=null, ValidatorInterface $validator, ApiKeyAuthenticator $auth, CallApiService $api){

        $Titres=[];
        $errors=[];

        dd($user);
        $form=$this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
           dd($form);
        }
        if($form->isSubmitted() && $form->isValid()){
            //On va chercher dans la base l'utilisateur en question
            $userResp=$api->getDatasAPI('/api/users/'.$user[0]['id'],'Usine',['password'=>$request->get('modif_md_p')['password']],'PATCH');
            
            return $this->redirectToRoute('security_login');
        }

        return $this->renderForm('security/ModificationMdP.html.twig',[
            'form' => $form,
            'errors' => $errors,
            'Titres' => $Titres
        ]);
    }
}
