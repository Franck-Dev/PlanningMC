<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\OrgaEq;
use App\Entity\NomEquipe;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;
use App\Repository\AgendaRepository;
use App\Repository\NomEquipeRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrgaEqCrudController extends AbstractCrudController
{
    private $agendaRepository;
    private $userRepository;
    private $NomEquipeRepository;

    public function __construct(AgendaRepository $agendaRepository, UserRepository $userRepository,
    NomEquipeRepository $NomEquipeRepository)
    {
        $this->agendaRepository=$agendaRepository;
        $this->userRepository=$userRepository;
        $this->NomEquipeRepository=$NomEquipeRepository;
    }
    
    public static function getEntityFqcn(): string
    {
        return OrgaEq::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->overrideTemplate('crud/index','admin/custom/my_index.html.twig')
        ->setPageTitle('index','Planification')
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if ($this->getUser()->getRoles()[0] != 'ROLE_SUPER_ADMIN') {
            //Récupération de la liste des équipes par manager
            $user = $this->userRepository->findOneBy(['username' => $this->getUser()->getUsername()]);
            $listEq=$this->NomEquipeRepository->findBy(['Manager' => $user->getId()]);

            $response->andWhere('entity.NomEquipe IN (:eq)')->setParameter('eq', $listEq);
        } else {
            //$response->where("entity.Roles LIKE '%%ROLE_USER%%'");
        }

        return $response;
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->userRepository->findOneBy(['username'=> $this->getUser()->getUsername()]);
        return [
            AssociationField::new('NomEquipe','Team Name')->setQueryBuilder(function($queryBuilder) use ($user) {
                $queryBuilder
                  ->Where('entity.Manager = :val')
                  ->setParameter('val',  $user->getId())
                ;
            }),
            TextField::new('TypeW'),
            DateTimeField::new('DateDebut'),
            DateTimeField::new('DateFin'),
        ];
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            // adds the CSS and JS assets associated to the given Webpack Encore entry
            // it's equivalent to adding these inside the <head> element:
            // {{ encore_entry_link_tags('...') }} and {{ encore_entry_script_tags('...') }}
            //->addWebpackEncoreEntry('admin-app')

            // it's equivalent to adding this inside the <head> element:
            // <link rel="stylesheet" href="{{ asset('...') }}">
            ->addCssFile('build/vis.css')
            //->addCssFile('https://example.org/css/admin2.css')

            // it's equivalent to adding this inside the <head> element:
            // <script src="{{ asset('...'') }}"></script>
            //->addJsFile('https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.js')
            //->addJsFile('build/jquery.min.js')
            //->addJsFile('build/admin.js')

            // use these generic methods to add any code before </head> or </body>
            // the contents are included "as is" in the rendered page (without escaping them)
            ->addHtmlContentToHead('<link rel="stylesheet" type="text/css" href="https://www.bootstrap-year-calendar.com/css/bootstrap.min.css" />')
            ->addHtmlContentToHead('<link rel="stylesheet" type="text/css" href="https://www.bootstrap-year-calendar.com/css/bootstrap-datepicker.min.css" />')
            ->addHtmlContentToHead('<link rel="stylesheet" type="text/css" href="https://www.bootstrap-year-calendar.com/css/bootstrap-theme.min.css" />')
            ->addHtmlContentToHead('<link rel="stylesheet" type="text/css" href="https://www.bootstrap-year-calendar.com/css/bootstrap-year-calendar.min.css" />')
            ->addHtmlContentToHead('<link rel="stylesheet" type="text/css" href="https://www.bootstrap-year-calendar.com/css/font-awesome.min.css" />')
            //->addHtmlContentToHead('<link rel="stylesheet" href="{{ asset(\'build/vis.css\') }}">')
            //->addHtmlContentToBody('<div class="agendaCalendar" data=\'{{taches}}\'></div>')
            //->addHtmlContentToBody('<div id="agenda" data-provide="calendar"></div>')
            //->addHtmlContentToBody('<script>$("#agenda").data("calendar").setDataSource(data);</script>')
            //->addHtmlContentToBody('<script>console.log(document.querySelector(\'.agendaCalendar\'));</script>')
            //->addHtmlContentToBody('<script src="{{ asset(\'build/vis.js\') }}"></script>')
        ;
    }

    /* public function configureActions(Actions $actions): Actions
    {
        $viewInvoice = Action::new('view planning')
            ->setHtmlAttributes(['Equipe' => 'bar', 'target' => '_blank'])
            ->linkToCrudAction('visuAgenda');
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, $viewInvoice);
    } */

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            $i=0;
            $j=0;
            $Equipe=[];
            $data=[];
            $datas=[];
            foreach ($responseParameters->get('entities')as $entity) {
                $Equipe[$j]=['id' => $entity->getInstance()->getId(), 'content' => $entity->getInstance()->getNomEquipe()->getNom()];
                $j++;
                foreach($entity->getInstance()->getAgendas() as $Eq){
                    if ($Eq->getDateDeb()==$Eq->getDateFin()) {
                        $newDateFin=date_modify(($Eq->getDateFin()),'+'.$entity->getInstance()->getNomEquipe()->getOrgaW()->getQteHeureW().'hours');
                    }else{
                        $newDateFin=$Eq->getDateFin();
                    }
                    $data[$i] = ['id'=> $i,'name'=> $Eq->getNomOrgaW()->getNomEquipe()->getOrgaW()->getNom(),'start'=> ($Eq->getDateDeb())->format('c'),'end'=> $newDateFin->format('c'),'group'=> $entity->getInstance()->getId(),'style'=> 'background-color: '.$Eq->getNomOrgaW()->getNomEquipe()->getOrgaW()->getBackgroundColor()];
                    $newDateFin=date_modify(($Eq->getDateFin()),'+6hours');
                    $datas[$i]=['id'=> $i,'title'=> $Eq->getNomOrgaW()->getNomEquipe()->getOrgaW()->getNom(),'startDate'=> ($Eq->getDateDeb())->format('c'),'endDate'=> $newDateFin->format('c'),'location'=>  $Eq->getNomOrgaW()->getNomEquipe()->getNom(),'color'=> $Eq->getNomOrgaW()->getNomEquipe()->getOrgaW()->getBackgroundColor()];
                    $i = $i + 1;
                }
            }
            $Equipes= new JsonResponse($Equipe);
            $taches= new JsonResponse($data);
            $rdvs=json_encode($datas);

            $dateDeb=new \DateTime('first day of this year');
            $dateFin=new \DateTime('last day of next year');
            $responseParameters->set('Taches', $taches->getcontent());
            $responseParameters->set('Equipes', $Equipes->getcontent());
            $responseParameters->set('datedeb', $dateDeb);
            $responseParameters->set('datefin', $dateFin);
            $responseParameters->set('NomEquipe', $this->getUser()->getService());
            $responseParameters->set('Rdvs', $rdvs);
        }
        return $responseParameters;
    }
}
