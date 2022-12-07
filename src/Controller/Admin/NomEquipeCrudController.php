<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\NomEquipe;
use App\Entity\TypesEquipe;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NomEquipeCrudController extends AbstractCrudController
{
    private $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository=$userRepository;
    }
    
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
        //$Managers = $this->getNomManager();

        return [
            TextField::new('Nom'),
            TextEditorField::new('Description'),
            AssociationField::new('OrgaW'),
            AssociationField::new('Manager','Team Leader')->setQueryBuilder(function($queryBuilder) {
                $queryBuilder
                  ->Where('entity.poste = :val')
                  ->andWhere('entity.isActive = :valeur')
                  ->setParameter('val', 'Maitrise')
                  ->setParameter('valeur', 1)
                ;
            }),
        ];
    }
    
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if ($this->getUser()->getRoles()[0] != 'ROLE_SUPER_ADMIN') {
            //dd($this->getUser()->getUserIdentifier());
            $user = $this->userRepository->findOneBy(['username'=> $this->getUser()->getUsername()]);
            $response->andWhere('entity.Manager = :user')->setParameter('user', $user->getId());
        } else {
            //$response->where("entity.Roles LIKE '%%ROLE_USER%%'");
        }
        return $response;
    }
}
