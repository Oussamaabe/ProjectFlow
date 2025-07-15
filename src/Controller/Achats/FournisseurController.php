<?php

namespace App\Controller\Achats;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achats/fournisseurs')]
class FournisseurController extends AbstractController
{
    #[Route('/', name: 'app_achats_fournisseur_index', methods: ['GET'])]
    public function index(FournisseurRepository $fournisseurRepository): Response
    {
        return $this->render('achats/fournisseur/index.html.twig', [
            'fournisseurs' => $fournisseurRepository->findAllOrderedByRaisonSociale(),
        ]);
    }

    #[Route('/new', name: 'app_achats_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fournisseur);
            $entityManager->flush();

            $this->addFlash('success', 'Fournisseur créé avec succès');
            return $this->redirectToRoute('app_achats_fournisseur_index');
        }

        return $this->render('achats/fournisseur/new.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_achats_fournisseur_show', methods: ['GET'])]
    public function show(Fournisseur $fournisseur): Response
    {
        return $this->render('achats/fournisseur/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_achats_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Fournisseur mis à jour avec succès');
            return $this->redirectToRoute('app_achats_fournisseur_index');
        }

        return $this->render('achats/fournisseur/edit.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_achats_fournisseur_delete', methods: ['POST'])]
    public function delete(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($fournisseur);
            $entityManager->flush();
            $this->addFlash('success', 'Fournisseur supprimé avec succès');
        }

        return $this->redirectToRoute('app_achats_fournisseur_index');
    }
}