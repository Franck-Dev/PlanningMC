<?php

namespace App\Controller;

use App\Entity\ChargFige;
use App\Entity\ConfSsmenu;
use App\Form\ChargFigeType;
use App\Repository\ChargFigeRepository;
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
    public function index(ChargFigeRepository $chargFigeRepository): Response
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        

        return $this->render('charg_fige/index.html.twig', [
            'charg_figes' => $chargFigeRepository->findAll(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/new", name="charg_fige_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        
        $chargFige = new ChargFige();
        $form = $this->createForm(ChargFigeType::class, $chargFige);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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
    public function show(ChargFige $chargFige): Response
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        
        return $this->render('charg_fige/show.html.twig', [
            'charg_fige' => $chargFige,
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="charg_fige_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ChargFige $chargFige): Response
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        
        $form = $this->createForm(ChargFigeType::class, $chargFige);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
    public function delete(Request $request, ChargFige $chargFige): Response
    {
        $repo=$this->getDoctrine()->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PROGRAMMATION']);
        
        if ($this->isCsrfTokenValid('delete'.$chargFige->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chargFige);
            $entityManager->flush();
        }

        return $this->redirectToRoute('charg_fige_index');
    }
}
