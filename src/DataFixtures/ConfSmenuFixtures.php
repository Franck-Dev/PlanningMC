<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ConfSmenu;

class ConfSmenuFixtures extends Fixture
{
// Fonction a am�liorer par la suite pour �tre plus dynamique sur les noms de Smenu et de Titre
    public function load(ObjectManager $manager)
    {
        $Titre=array('DECOUPE','MOULAGE','MOYENS CHAUD','QUALITE','METHODES','LOGISTIQUE');
        $Description=array('Service decoupe tissu','Drappage laser et manuel','Polymerisation',
        'Suivi des pi�ces','Industrialisation des pieces','Gestion du planning de livraison');

        $i=0;
        foreach ($Titre as $valeur){

            switch($valeur){

                case "DECOUPE":
                    $ConfSmenu= new ConfSmenu();
                    $ConfSmenu  ->setNom($valeur)
                                ->setDescription($Description[$i])
                                ->setIdSmenu1("Ameliorations")
                                ->setIdSmenu2("Utilisateurs")
                                ->setIdSmenu3("Planning")
                                ->setIdSmenu4("Tracabilite")
                                ->setIdSmenu5("")
                                ->setIdSmenu6("")
                                ->setIdSmenu7("");
                    $manager->persist($ConfSmenu);
                    break;
                case "MOULAGE":
                    $ConfSmenu= new ConfSmenu();
                    $ConfSmenu  ->setNom($valeur)
                                ->setDescription($Description[$i])
                                ->setIdSmenu1("Ameliorations")
                                ->setIdSmenu2("Utilisateurs")
                                ->setIdSmenu3("Planning")
                                ->setIdSmenu4("Tracabilite")
                                ->setIdSmenu5("Demandes")
                                ->setIdSmenu6("")
                                ->setIdSmenu7("");
                    $manager->persist($ConfSmenu);
                break;
                case "MOYENS CHAUD":
                    $ConfSmenu= new ConfSmenu();
                    $ConfSmenu  ->setNom($valeur)
                                ->setDescription($Description[$i])
                                ->setIdSmenu1("Ameliorations")
                                ->setIdSmenu2("Utilisateurs")
                                ->setIdSmenu3("Planning")
                                ->setIdSmenu4("Tracabilite")
                                ->setIdSmenu5("Planification")
                                ->setIdSmenu6("")
                                ->setIdSmenu7("");
                    $manager->persist($ConfSmenu);
                break;
                case "QUALITE":
                    $ConfSmenu= new ConfSmenu();
                    $ConfSmenu  ->setNom($valeur)
                                ->setDescription($Description[$i])
                                ->setIdSmenu1("Ameliorations")
                                ->setIdSmenu2("Utilisateurs")
                                ->setIdSmenu3("Tracabilite")
                                ->setIdSmenu4("")
                                ->setIdSmenu5("")
                                ->setIdSmenu6("")
                                ->setIdSmenu7("");
                    $manager->persist($ConfSmenu);
                break;
                case "METHODES":
                    $ConfSmenu= new ConfSmenu();
                    $ConfSmenu  ->setNom($valeur)
                                ->setDescription($Description[$i])
                                ->setIdSmenu1("Ameliorations")
                                ->setIdSmenu2("Utilisateurs")
                                ->setIdSmenu3("PE")
                                ->setIdSmenu4("PROGRAMMATION")
                                ->setIdSmenu5("OUTILLAGES")
                                ->setIdSmenu6("DATA_TOOLS")
                                ->setIdSmenu7("");
                    $manager->persist($ConfSmenu);
                break;
                case "LOGISTIQUE":
                    $ConfSmenu= new ConfSmenu();
                    $ConfSmenu  ->setNom($valeur)
                                ->setDescription($Description[$i])
                                ->setIdSmenu1("Ameliorations")
                                ->setIdSmenu2("Utilisateurs")
                                ->setIdSmenu3("Urgences")
                                ->setIdSmenu4("")
                                ->setIdSmenu5("")
                                ->setIdSmenu6("")
                                ->setIdSmenu7("");
                    $manager->persist($ConfSmenu);
                break;
            }
        $i++;
        }
        $manager->flush();
    }
}
