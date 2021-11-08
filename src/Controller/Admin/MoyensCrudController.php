<?php

namespace App\Controller\Admin;

use App\Entity\Moyens;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MoyensCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Moyens::class;
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
