<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use App\Entity\User;
use App\Services\CallApiService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;


class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private $CallApi;
    private $router;
    private $manaReg;
    private $encoder;

    public function __construct(CallApiService $CallApi, UrlGeneratorInterface $router,
    ManagerRegistry $manaReg, UserPasswordHasherInterface $encoder)
    {
        $this->CallApi = $CallApi;
        $this->router = $router;
        $this->manaReg = $manaReg;
        $this->encoder = $encoder;
    }
    
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        //return $request->headers->has('X-AUTH-TOKEN');
        if ($request->get('_username') && $request->getPathInfo() == '/connexion') {
            return true;
        }
            return false;
        //return !$request->get('_username');
    }

    public function authenticate(Request $request): Passport
    {
        //$apiToken = $request->headers->get('X-AUTH-TOKEN');
        $_username=$request->get('_username');
            //dump($request->get('modif_md_p'));
        // Si le username est un nom, on récupère le matricule
        if (strstr($_username, '.', true)!==false && strstr($_username, '@', true)!==false) {
            $username = strstr($_username, '@', true);
            $userCheck=$this->CallApi->getDatasAPI('/api/users?pagination=false&username='.$username,'Usine',null,'GET',null);
            $matricule=strval($userCheck[0]['matricule']);
        } elseif(strstr($_username, '.', true)!==false) {
            $username = $_username;
            $userCheck=$this->CallApi->getDatasAPI('/api/users?pagination=false&username='.$username,'Usine',null,'GET',null);
            $matricule=strval($userCheck[0]['matricule']);
        } else {
            $matricule=$_username;
        }
        dump($matricule);
        $body=['matricule'=>$matricule, 'password'=>$request->get('_password')];
        $apiToken=$this->CallApi->getDatasAPI('/api/login','Usine',$body,'POST',null);
        //Test login direct
        /* dump($body);
        return new Passport(
            new UserBadge($_username,function($matricule){
                $usiter=$this->utilisateur->loadUserByIdentifier($matricule);}),
                new PasswordCredentials($request->get('_password'))
            ); */
        dump($apiToken);
        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(new UserBadge($apiToken['apiToken'],function($apiToken) use ($request){
            $userToken=$this->CallApi->getUserApiToken($apiToken);
            //Gestion des erreurs d'authentification
            if ($userToken instanceof JsonResponse) {
                switch ($userToken->getStatusCode()) {
                    case 423:
                        throw new LockedException();
                        break;
                    case 404:
                        throw new UserNotFoundException();
                        break;
                    default:
                        # code...
                        break;
                }
            }
            $user=new User;
            //Hydratation du User
            $user->hydrate($userToken);
            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $token->setAttributes($token->getUser()->getProgrammeAvion());
        return new RedirectResponse($this->router->generate('home'),301);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
