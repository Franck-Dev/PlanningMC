<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ConfSsmenu;

class ConfSsmenuFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        function DefTitre($val,$Desc,$T1,$T2,$T3,$T4,$T5,$T6,$T7,$manager){
        $ConfSsmenu= new ConfSsmenu();
        $ConfSsmenu ->setNom($val)
                    ->setDescription($Desc)
                    ->setIdSmenu1("$T1")
                    ->setIdSmenu2("$T2")
                    ->setIdSmenu3("$T3")
                    ->setIdSmenu4("$T4")
                    ->setIdSmenu5("$T5")
                    ->setIdSmenu6("$T6")
                    ->setIdSmenu7("$T7");
        $manager->persist($ConfSsmenu);
    }
        $Titres=array('PE'=>array('ARTICLE','FI','FAC'),'OUTILLAGE'=>array('ARTICLE','MAINTENANCE','REPARATION'),'PROGRAMMATION'=>array('DECOUPE','LASER','AFP','POLYM','CSCAN','USINAGE'),'DATA_TOOLS'=>array('ERP','PDM','MeS','GED'));
        $Description=array('Service moulage serie','Service reparation petit outillages de moulage','Realise les programmes des moyens communs',
        'Services d\'aide au suivi des proc�dures et leurs applications');

        foreach ($Titres as $Titre => $Lignes){
            foreach ($Lignes as $valeur=> $men){
                DefTitre($men,$Titre,"Creation","Consultation","Demandes","","","","",$manager);
            }
//            switch($valeur){
//
//                case "PE":
//                    DefTitre($valeur,$Description[$i],"Creation","Consultation","","","","","",$manager);
//                    break;
//                case "OUTILLAGE":
//                    DefTitre($Menu,$valeur,$Description[$i],"Creation","Consultation","Demandes","","","","",$manager);
//                break;
//                case "PROGRAMMATION":
//                    DefTitre($Menu,$valeur,$Description[$i],"Creation","Consultation","","","","","",$manager);
//                break;
//                case "DATA_TOOLS":
//                    DefTitre($Menu,$valeur,$Description[$i],"Incidents","Ameliorations","Formations","Accompagnements","","","",$manager);
//                break;
//            }
        }

        $manager->flush();
    }
}
