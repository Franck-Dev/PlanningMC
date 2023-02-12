<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
    $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    if ($this->getUser()->getRoles()[0] == 'ROLE_SUPER_ADMIN') {
        //dd($this->getUser()->getNomEquipes());
        $response->select();
    } elseif ($this->getUser()->getRoles()[0] == 'ROLE_GESTION_EQ') {
        $equipeMe=$this->getUser()->getNomEquipes();
        //dd($equipeMe);
        if (!$equipeMe){
            $response->where("entity.nomEquipe IN(:serve)")->setParameter('serve', $equipeMe);
        }else {
            //$response->where("entity.Roles LIKE '%%ROLE_USER%%'");
            $response->andWhere('entity.mail = :mail')->setParameter('mail', $this->getUser()->getMail());
        }
    } else {
        dd($this->getUser()->getMail());
        $response->andWhere('entity.mail = :mail')->setParameter('mail', $this->getUser()->getMail());
    }
      return $response;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('username'),
            TextField::new('mail'),
            TextField::new('service'),
            TextField::new('poste'),
            BooleanField::new('isactive'),
            ArrayField::new('roles'),
            //ArrayField::new('programmeavion')
        ];
    }

}
