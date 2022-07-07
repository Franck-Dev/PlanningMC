<?php

namespace App\Services;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * CallApiService accèdeaux données des API
 */
class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * getUserApiToken :  Donne le user connecté ou appelé
     *
     * @param  string $apiToken
     * @return mixed
     */
    public function getUserApiToken($apiToken=null)
    {
        //dd($this->client);
        $response = $this->client->request(
            'GET',
            'http://localhost:84/api/users?page=1&itemsPerPage=1&pagination=true&apiToken='.$apiToken
        );
        $user=$response->toArray();
        if ($user){
            if ($user[0]['isActive'] == true)
            {
                return $user[0];
            } else {
                $data=['message' => 'Utilisateur désactivé'];
                
                return new JsonResponse($data, Response::HTTP_LOCKED);
            }
        } else {
            $data=['message' => 'Utilisateur pas trouvé'];
                
            return new JsonResponse($data, Response::HTTP_NOT_FOUND);
        }
        return $response->toArray();
    }
  
    /**
     * Fonction permettant de remonter les données des API exter suivant une url
     *
     * @param  string $url
     * @param  string $api
     * @param  array $body
     * @param  string $method
     * @param  string $apiToken
     * @return array
     */
    public function getDatasAPI($url=null, string $api, array $tabBody=null, string $method, string $apiToken=null): array
    {
        //Gestion de la construction de la requete API svt envirronements et APIs
        switch ($api) {
            case 'tracapolym':
                $port_api=$_ENV['APP_PORT_APIMC_API'];
                break;
            case 'tracakit':
                $port_api=$_ENV['APP_PORT_TRACAKIT_API'];
                break;
            case 'outillage':
                $port_api=$_ENV['APP_PORT_OUTILLAGE_API'];
                break;
            default:
                $port_api=$_ENV['APP_PORT_USINE_API'];
                break;
        }
        $path=$_ENV['APP_SERVER'].$port_api;
        //Requette
        dump($path.$url);
        $response = $this->client->request(
            $method,
            $path.$url,
            [
                'headers' => ['content-type'=>'application/json',
                                'X-AUTH-TOKEN'=>$apiToken
            ],
                'json' => $tabBody
            ]
        );
        return $response->toArray();
    }
    
    /**
     * getUserExist Fonction permettant de récupérer l'utilisateur suivant son matricule,email ou son username
     * @param  string $matricule
     * @param  mixed $type
     * @return array
     */
    public function getUserExist(string $matricule, string $type): array
    {
        //Choix de la variable de recherche
        switch ($type) {
            case 'email':
                $url='http://localhost:84/api/users?page=1&itemsPerPage=1&pagination=true&mail=';
                break;
            case 'username':
                $url='http://localhost:84/api/users?page=1&itemsPerPage=1&pagination=true&username=';
                break;    
            default:
                $url='http://localhost:84/api/users?page=1&itemsPerPage=1&pagination=true&matricule=';
                break;
        }
        $response = $this->client->request(
            'GET',
            $url.$matricule
        );
        return $response->toArray();
    }
}
