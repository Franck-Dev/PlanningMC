<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\ConfSsmenu;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/METHODES/PE")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route("/Consultation", name="articles_index", methods={"GET"})
     */
    public function index(ArticlesRepository $articlesRepository, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PE']);

        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/Creation", name="articles_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PE']);

        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $manaReg->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles_index');
        }

        return $this->render('articles/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}", name="articles_show", methods={"GET"})
     */
    public function show(Articles $article, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PE']);

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="articles_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Articles $article, ManagerRegistry $manaReg): Response
    {
        $repo=$manaReg->getRepository(ConfSsmenu::class);
        $Titres=$repo -> findBy(['Description' => 'PE']);

        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manaReg->getManager()->flush();

            return $this->redirectToRoute('articles_index');
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'Titres' => $Titres,
        ]);
    }

    /**
     * @Route("/{id}", name="articles_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Articles $article, ManagerRegistry $manaReg): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $manaReg->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('articles_index');
    }
}
