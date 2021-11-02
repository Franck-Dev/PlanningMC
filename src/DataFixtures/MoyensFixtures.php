<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Moyens;
use App\Entity\CategoryMoyens;

class MoyensFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Moyen=array('Autoclave1','Autoclave2','Autoclave3','Autoclave4','Autoclave5','Autoclave6','Etuve1','Etuve2','Etuve3','Dolouet','Satim','CMS1','CMS2','Afma','MecaNum','Lectra1','Lectra2');
        $Service=array('8','8','8','8','8','8','8','8','8','8','8','11','11','11','11','10','10');
        $Caract=array('1','1','1','1','2','1','0','2','1','1','1','1','1','1','1','1','1');
        $Etat=array(True,True,True,True,False,True,True,False,True,True,True,True,True,True,True,True,True);
        $CatMoyen=array('Autoclaves','Etuves','Presses','CNUsinage','CNDecoupe');
        $CateM=array(6,3,2,4,2);
        $Description=array('Moyen de polymerisation sous pression',
        'Moyen de polymerisation sans pression','Moyen de polymerisation sous presse',
        'Moyen usinage Panneau Polym�ris�','Moyen de d�coupe Tissu');
        $h=0;
        $CategoryMoyens= new CategoryMoyens();
        for($i=0;$i<count($Moyen)+4;$i++){
            if($CategoryMoyens->getId($i-1)===$CateM[$h]){

            }
            else{
                $CategoryMoyens= new CategoryMoyens();
                $CategoryMoyens ->setLibelle($CatMoyen[$h])
                                    ->setDescription($Description[$h]);
                $manager->persist($CategoryMoyens);
                $h=$h+1;
            }

            for($j=0;$j<$CateM[$h-1];$j++){
                $Moyens= new Moyens();
                $Moyens  ->setLibelle($Moyen[$i+$j-$h+1])
                        ->setIdService($Service[$i+$j-$h+1])
                        ->setIdTypesCaracteristiques($Caract[$i+$j-$h+1])
                        ->setOperationnel($Etat[$i+$j-$h+1])
                        ->setCategoryMoyens($CategoryMoyens);

                $manager->persist($Moyens);
            }
            $i=$i+$j;            
        }
        $manager->flush();
    }
}
