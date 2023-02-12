<?php

namespace App\Controller\Admin;

use App\Entity\Moyens;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MoyensCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Moyens::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('libelle'),
            TextEditorField::new('description'),
            IntegerField::new('Id_Service'),
            AssociationField::new('categoryMoyens'),
            BooleanField::new('operationnel')
        ];
    }
}
