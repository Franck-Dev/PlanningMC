<?php

namespace App\Controller\Admin;

use App\Entity\TypesEquipe;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class TypesEquipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypesEquipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('Nom'),
            TextEditorField::new('Description'),
            CollectionField::new('JourW'),
            IntegerField::new('QteHeureW','Nb Heure W/Jour'),
            ColorField::new('BackgroundColor'),
            ColorField::new('TextColor'),
        ];
    }
    
}
