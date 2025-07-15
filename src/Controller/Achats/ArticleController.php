<?php

namespace App\Controller\Achats;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achats/articles')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_achats_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('achats/article/index.html.twig', [
            'articles' => $articleRepository->findAllOrderedByDesignation(),
        ]);
    }

    #[Route('/new', name: 'app_achats_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('app_achats_article_index');
        }

        return $this->render('achats/article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_achats_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('achats/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_achats_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Article mis à jour avec succès');
            return $this->redirectToRoute('app_achats_article_index');
        }

        return $this->render('achats/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_achats_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('success', 'Article supprimé avec succès');
        }

        return $this->redirectToRoute('app_achats_article_index');
    }
}