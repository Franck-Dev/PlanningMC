<?php

namespace App\Entity;

use Doctrine\Persistence\ManagerRegistry;


class IndicHeader
{
    private $nom;

    
    private $datas=[];

    
    private $icone;

    private $manager;

    public function __construct()
    {
        $this->manager = new ManagerRegistry();
    }

    public function getNom(string $nom): ?string
    {
        return $this->nom;
    }
    
    /**
     * getDatas Renvoie les données suivant le tableau de recherche $check
     *
     * @param  string $className
     * @param  array $check
     * @param  array $options
     * @return array
     */
    public function getDatas(string $className, array $check, array $options): ?array
    {
        $repo=$this->manager->getRepository($className);
        $this->datas=$repo->findBy($check, $options);
        return $this->datas;
    }

    public function getIcone(string $icone): ?string
    {
        $this->icone='fa fa-'.$icone;
        return $this->icone;
    }

}
