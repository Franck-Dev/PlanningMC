<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Confapply;

class ConfapplyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Titre=array('DECOUPE','MOULAGE','MOYENS CHAUD','QUALITE','METHODES','LOGISTIQUE');
        $Services=array('PE','PROGRAMMATION','OUTILLAGES','DATA TOOLS');

        foreach ($Titre as $valeur){
            $Confapply= new Confapply();
            $Confapply  ->setTitreMenu($valeur)
                        ->setTitreSmenu("Ameliorations");
            $manager->persist($Confapply);

//            $Confapply= new Confapply();
//            $Confapply  ->setTitreMenu($valeur)
//                        ->setTitreSmenu("Utilisateurs");
//            $manager->persist($Confapply);
//
//            if($valeur=="DECOUPE" OR $valeur=="MOULAGE" OR $valeur=="MOYENS CHAUD"){
//                $Confapply= new Confapply();
//                $Confapply  ->setTitreMenu($valeur)
//                            ->setTitreSmenu("Planning");
//                $manager->persist($Confapply);
//                }
//            switch($valeur){
//                case "MOULAGE":
//                $Confapply= new Confapply();
//                $Confapply  ->setTitreMenu($valeur)
//                            ->setTitreSmenu("Planning");
//                $manager->persist($Confapply);
//                break;
//                case "METHODES":
//                for($i=0;$i<count($Services);$i++){
//                    $Confapply= new Confapply();
//                    $Confapply  ->setTitreMenu($valeur)
//                                ->setTitreSmenu($Services[$i]);
//                    $manager->persist($Confapply);
//                }
//                break;
//            }
        }

        $manager->flush();
    }
}
