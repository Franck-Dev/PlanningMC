<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Postes;

class PostesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Role=array('Pr�parateur','Maitrise','Controleur','Responsable','Op�rateur');
        $Description=array('Service M�thode','Chef d\'�quipe','Qualit�','Chef de Service','Mouleurs Ajusteurs Regleurs');

        for($i=0;$i<count($Role);$i++){
            $Postes= new Postes();
            $Postes     ->setLibelle($Role[$i])
                        ->setDescription($Description[$i]);
            $manager->persist($Postes);

        }
        $manager->flush();;
    }
}
