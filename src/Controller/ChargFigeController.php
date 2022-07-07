<?php

namespace App\Controller;

use App\Entity\ChargFige;
use App\Entity\ConfSsmenu;
use App\Form\ChargFigeType;
use App\Repository\ChargFigeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/METHODES/PROGRAMMATION/Creation_ChargF")
 */
class ChargFigeController extends AbstractController
{
    /**
     * @Route("/", name="charg_fige_index", methods={"GET"})
     */
    public function index(ChargFigeRepository $chargFigeRepository, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        

        return $this->render('charg_fige/index.html.twig', [
            'charg_figes' => $chargFigeRepository->findAll(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/new", name="charg_fige_new", methods={"GET","POST"})
     */
    public function new(Request $request, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        
        $chargFige = new ChargFige();
        $form = $this->createForm(ChargFigeType::class, $chargFige);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $manaReg->getManager();
            $entityManager->persist($chargFige);
            $entityManager->flush();

            return $this->redirectToRoute('charg_fige_index');
        }

        return $this->render('charg_fige/new.html.twig', [
            'charg_fige' => $chargFige,
            'form' => $form->createView(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}", name="charg_fige_show", methods={"GET"})
     */
    public function show(ChargFige $chargFige, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        
        return $this->render('charg_fige/show.html.twig', [
            'charg_fige' => $chargFige,
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="charg_fige_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ChargFige $chargFige, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        dump($chargFige);
        $form = $this->createForm(ChargFigeType::class, $chargFige);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manaReg->getManager()->flush();

            return $this->redirectToRoute('charg_fige_index');
        }

        return $this->render('charg_fige/edit.html.twig', [
            'charg_fige' => $chargFige,
            'form' => $form->createView(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}", name="charg_fige_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ChargFige $chargFige, ManagerRegistry $manaReg): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$chargFige->getId(), $request->request->get('_token'))) {
            $entityManager = $manaReg->getManager();
            $entityManager->remove($chargFige);
            $entityManager->flush();
        }

        return $this->redirectToRoute('charg_fige_index');
    }
}
