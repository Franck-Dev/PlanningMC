<?php

namespace App\Controller\Admin;

use App\Entity\NomEquipe;
use App\Entity\TypesEquipe;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NomEquipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NomEquipe::class;
    }
    
    /**
     * getNomManager
     * @deprecated 
     *
     * @return void
     */
    public function getNomManager()
    {
        $ListManager= $this->getDoctrine()
        ->getRepository(User::class);
        return $tab = $ListManager->findBy(['poste'=> 2,'isActive'=>1]);
    }
    
    public function configureFields(string $pageName): iterable
    {
        $Managers = $this->getNomManager();
        return [
            TextField::new('Nom'),
            TextEditorField::new('Description'),
            AssociationField::new('OrgaW'),
            AssociationField::new('Manager','Team Leader')->setQueryBuilder(function($queryBuilder) {
                $queryBuilder
                  ->Where('entity.poste = :val')
                  ->andWhere('entity.isActive = :valeur')
                  ->setParameter('val', 2)
                  ->setParameter('valeur', 1)
                ;
            }),
        ];
    }    
}
