<?php

namespace App\Controller\Admin;

use App\Entity\Demandes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DemandesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Demandes::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
