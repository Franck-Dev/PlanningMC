<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TypeRecurrance;

class TypeRecurFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $Serv=array('Hebdo','JourSem','JourWE',
        'BiHebdo','Mensuel');
        $Description=array('1 jour par semaine','Tous les jours de la semaine ouvre','Tous les jours du WE',
        '2 fois par semaine ouvre','1 fois par mois');
        $NbrJour=array('7','1','1','2','30');

        for($i=0;$i<count($Serv);$i++){
            $Typerecur= new TypeRecurrance();
            $Typerecur   ->setType($Serv[$i])
                        ->setNbrJourCycle($NbrJour[$i]);
            $manager->persist($Typerecur);
        }
        $manager->flush();
    }
}
