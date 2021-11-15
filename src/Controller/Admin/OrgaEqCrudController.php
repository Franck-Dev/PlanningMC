<?php

namespace App\Controller\Admin;

use App\Entity\OrgaEq;
use App\Entity\NomEquipe;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrgaEqCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrgaEq::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('NomEquipe'),
            TextField::new('TypeW'),
            DateTimeField::new('DateDebut'),
            DateTimeField::new('DateFin'),
        ];
    }
    
}
