<?php

namespace App\Controller;

use App\Entity\DatasMoulage;
use App\Form\DatasMoulageType;
use App\Repository\DatasMoulageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/datas/moulage')]
class DatasMoulageController extends AbstractController
{
    #[Route('/', name: 'Consultation_Donnees', methods: ['GET'])]
    public function index(DatasMoulageRepository $datasMoulageRepository): Response
    {
        $Titres=[];
        
        return $this->render('datas_moulage/index.html.twig', [
            'datas_moulages' => $datasMoulageRepository->findAll(),
            'Titres' => $Titres
        ]);
    }

    #[Route('/new', name: 'Creation_Donnees', methods: ['GET', 'POST'])]
    public function new(Request $request, DatasMoulageRepository $datasMoulageRepository): Response
    {
        $datasMoulage = new DatasMoulage();
        //dd($datasMoulage);
        $form = $this->createForm(DatasMoulageType::class, $datasMoulage);
        $form->handleRequest($request);
        $Titres=[];
        if ($form->isSubmitted() && $form->isValid()) {
            $datasMoulageRepository->add($datasMoulage, true);

            return $this->redirectToRoute('Consultation_Donnees', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('datas_moulage/new.html.twig', [
            'datas_moulage' => $datasMoulage,
            'form' => $form,
            'Titres' => $Titres
        ]);
    }

    #[Route('/{id}', name: 'app_datas_moulage_show', methods: ['GET'])]
    public function show(DatasMoulage $datasMoulage): Response
    {
        return $this->render('datas_moulage/show.html.twig', [
            'datas_moulage' => $datasMoulage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_datas_moulage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DatasMoulage $datasMoulage, DatasMoulageRepository $datasMoulageRepository): Response
    {
        $form = $this->createForm(DatasMoulageType::class, $datasMoulage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datasMoulageRepository->add($datasMoulage, true);

            return $this->redirectToRoute('app_datas_moulage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('datas_moulage/edit.html.twig', [
            'datas_moulage' => $datasMoulage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_datas_moulage_delete', methods: ['POST'])]
    public function delete(Request $request, DatasMoulage $datasMoulage, DatasMoulageRepository $datasMoulageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$datasMoulage->getId(), $request->request->get('_token'))) {
            $datasMoulageRepository->remove($datasMoulage, true);
        }

        return $this->redirectToRoute('app_datas_moulage_index', [], Response::HTTP_SEE_OTHER);
    }
}
