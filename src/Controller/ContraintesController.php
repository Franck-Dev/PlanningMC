<?php

namespace App\Controller;

use App\Entity\Contraintes;
use App\Form\ContraintesType;
use App\Repository\ContraintesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contraintes')]
class ContraintesController extends AbstractController
{
    #[Route('/', name: 'Consultation_Contraintes', methods: ['GET'])]
    public function index(ContraintesRepository $contraintesRepository): Response
    {
        return $this->render('contraintes/index.html.twig', [
            'contraintes' => $contraintesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'Creation_Contraintes', methods: ['GET', 'POST'])]
    public function new(Request $request, ContraintesRepository $contraintesRepository): Response
    {
        $contrainte = new Contraintes();
        $form = $this->createForm(ContraintesType::class, $contrainte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contraintesRepository->add($contrainte, true);

            return $this->redirectToRoute('app_contraintes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contraintes/new.html.twig', [
            'contrainte' => $contrainte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contraintes_show', methods: ['GET'])]
    public function show(Contraintes $contrainte): Response
    {
        return $this->render('contraintes/show.html.twig', [
            'contrainte' => $contrainte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contraintes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contraintes $contrainte, ContraintesRepository $contraintesRepository): Response
    {
        $form = $this->createForm(ContraintesType::class, $contrainte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contraintesRepository->add($contrainte, true);

            return $this->redirectToRoute('app_contraintes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contraintes/edit.html.twig', [
            'contrainte' => $contrainte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contraintes_delete', methods: ['POST'])]
    public function delete(Request $request, Contraintes $contrainte, ContraintesRepository $contraintesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contrainte->getId(), $request->request->get('_token'))) {
            $contraintesRepository->remove($contrainte, true);
        }

        return $this->redirectToRoute('app_contraintes_index', [], Response::HTTP_SEE_OTHER);
    }
}
