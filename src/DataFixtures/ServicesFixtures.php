<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Services;

class ServicesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Serv=array('METHODES PE','QUALITE','MOYEN CHAUD','MOULAGE','DECOUPE');
        $Description=array('Pr�paration','','Moyen de polymerisation',
        'Drapage des PE Composites','Service de d�coupe Tissu');

        for($i=0;$i<count($Serv);$i++){
            $Services= new Services();
            $Services   ->setNom($Serv[$i])
                        ->setDescription($Description[$i])
                        ->setIdChef("1");
            $manager->persist($Services);

        }
        $manager->flush();
    }
}
