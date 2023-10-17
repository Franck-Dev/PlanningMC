<?php

namespace App\Controller;

use App\Entity\DatasMoulage;
use App\Form\DatasMoulageType;
use App\Repository\DatasMoulageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/moulage')]
class MoulageController extends AbstractController
{
    #[Route('/', name: 'app_moulage_index', methods: ['GET'])]
    public function index(DatasMoulageRepository $datasMoulageRepository): Response
    {
        return $this->render('moulage/index.html.twig', [
            'datas_moulages' => $datasMoulageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_moulage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DatasMoulageRepository $datasMoulageRepository): Response
    {
        $datasMoulage = new DatasMoulage();
        $form = $this->createForm(DatasMoulageType::class, $datasMoulage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datasMoulageRepository->add($datasMoulage, true);

            return $this->redirectToRoute('app_moulage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('moulage/new.html.twig', [
            'datas_moulage' => $datasMoulage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moulage_show', methods: ['GET'])]
    public function show(DatasMoulage $datasMoulage): Response
    {
        return $this->render('moulage/show.html.twig', [
            'datas_moulage' => $datasMoulage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_moulage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DatasMoulage $datasMoulage, DatasMoulageRepository $datasMoulageRepository): Response
    {
        $form = $this->createForm(DatasMoulageType::class, $datasMoulage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datasMoulageRepository->add($datasMoulage, true);

            return $this->redirectToRoute('app_moulage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('moulage/edit.html.twig', [
            'datas_moulage' => $datasMoulage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moulage_delete', methods: ['POST'])]
    public function delete(Request $request, DatasMoulage $datasMoulage, DatasMoulageRepository $datasMoulageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$datasMoulage->getId(), $request->request->get('_token'))) {
            $datasMoulageRepository->remove($datasMoulage, true);
        }

        return $this->redirectToRoute('app_moulage_index', [], Response::HTTP_SEE_OTHER);
    }
}
