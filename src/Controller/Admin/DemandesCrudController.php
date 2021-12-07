<?php

namespace App\Controller\Admin;

use App\Entity\Demandes;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NullFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\NullFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DemandesCrudController extends AbstractCrudController
{
    public function __construct(EntityManagerInterface $entityManager) 
{
    $this->entityManager = $entityManager;
}
    
    public static function getEntityFqcn(): string
    {
        return Demandes::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add(EntityFilter::new('Cycle'))
            // most of the times there is no need to define the
            // filter type because EasyAdmin can guess it automatically
            ->add(NullFilter::new('MoyenUtilise')->setChoiceLabels('Null','NotNull'))
            ->add(BooleanFilter::new('Plannifie'))
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('Cycle'),
            DateField::new('DatePropose'),
            DateTimeField::new('HeurePropose'),
            TextField::new('Outillages'),
            TextField::new('MoyenUtilise'),
            BooleanField::new('Plannifie'),
            TextField::new('UserCrea'),
        ];
    }    
}
