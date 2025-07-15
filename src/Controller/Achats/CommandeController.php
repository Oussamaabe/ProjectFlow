<?php

namespace App\Controller\Achats;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achats/commandes')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_achats_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('achats/commande/index.html.twig', [
            'commandes' => $commandeRepository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/new', name: 'app_achats_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setNumeroCommande('CMD-'.date('Ymd-His'));

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Commande créée avec succès');
            return $this->redirectToRoute('app_achats_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('achats/commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_achats_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('achats/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_achats_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Commande mise à jour avec succès');
            return $this->redirectToRoute('app_achats_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('achats/commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/valider', name: 'app_achats_commande_valider', methods: ['POST'])]
    public function valider(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('valider'.$commande->getId(), $request->request->get('_token'))) {
            $commande->setStatut('validée');
            $entityManager->flush();
            $this->addFlash('success', 'Commande validée avec succès');
        }

        return $this->redirectToRoute('app_achats_commande_show', ['id' => $commande->getId()]);
    }

    #[Route('/{id}', name: 'app_achats_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
            $this->addFlash('success', 'Commande supprimée avec succès');
        }

        return $this->redirectToRoute('app_achats_commande_index');
    }
}