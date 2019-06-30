<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Postes;

class PostesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Role=array('Préparateur','Maitrise','Controleur','Responsable','Opérateur');
        $Description=array('Service Méthode','Chef d\'équipe','Qualité','Chef de Service','Mouleurs Ajusteurs Regleurs');

        for($i=0;$i<count($Role);$i++){
            $Postes= new Postes();
            $Postes     ->setLibelle($Role[$i])
                        ->setDescription($Description[$i]);
            $manager->persist($Postes);

        }
        $manager->flush();;
    }
}
