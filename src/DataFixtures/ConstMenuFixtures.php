<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ConstMenu;

class ConstMenuFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Menu=array('Ameliorations','Utilisateurs','Demandes','Planning','Planification','Tracabilite','Services');
        $Description=array('Faire une demande d\'amelioration sur le produit ou process',
        'Permet aux utilisateurs d\'avoir une page perso','Faire une demande de creneaux de polymerisation',
        'Affichage du planning','Permet de faire le planning MC','Permet de realiser la tracabilite � ce service','Sous services');

        for($i=0;$i<count($Menu);$i++){
            $ConstMenu= new ConstMenu();
            $ConstMenu  ->setNom($Menu[$i])
                        ->setDescription($Description[$i]);
            $manager->persist($ConstMenu);

        }

        $manager->flush();
    }
}
