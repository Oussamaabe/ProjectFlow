<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\CommandeAchat;
use App\Form\CommandeAchatType;
use App\Repository\CommandeAchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande/achat')]
class CommandeAchatController extends AbstractController
{
    #[Route('/', name: 'app_commande_achat_index', methods: ['GET'])]
    public function index(CommandeAchatRepository $commandeAchatRepository): Response
    {
        return $this->render('commande_achat/index.html.twig', [
            'commande_achats' => $commandeAchatRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_achat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeAchat = new CommandeAchat();
        $form = $this->createForm(CommandeAchatType::class, $commandeAchat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calcul du total et liaison des lignes à la commande
            $total = 0;
            foreach ($commandeAchat->getLignes() as $ligne) {
                $ligneTotal = $ligne->getPrixUnitaire() * $ligne->getQuantite();
                $ligne->setTotal($ligneTotal);
                $ligne->setCommande($commandeAchat);
                $total += $ligneTotal;
            }
            $commandeAchat->setTotal($total);

            $entityManager->persist($commandeAchat);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_achat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_achat/new.html.twig', [
            'commande_achat' => $commandeAchat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_achat_show', methods: ['GET'])]
    public function show(CommandeAchat $commandeAchat): Response
    {
        return $this->render('commande_achat/show.html.twig', [
            'commande_achat' => $commandeAchat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_achat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommandeAchat $commandeAchat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeAchatType::class, $commandeAchat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Recalcul du total et liaison des lignes à la commande
            $total = 0;
            foreach ($commandeAchat->getLignes() as $ligne) {
                $ligneTotal = $ligne->getPrixUnitaire() * $ligne->getQuantite();
                $ligne->setTotal($ligneTotal);
                $ligne->setCommande($commandeAchat);
                $total += $ligneTotal;
            }
            $commandeAchat->setTotal($total);

            $entityManager->flush();

            return $this->redirectToRoute('app_commande_achat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_achat/edit.html.twig', [
            'commande_achat' => $commandeAchat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_achat_delete', methods: ['POST'])]
public function delete(Request $request, CommandeAchat $commandeAchat, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$commandeAchat->getId(), $request->request->get('_token'))) {
        $em->remove($commandeAchat);
        $em->flush();
    }

    return $this->redirectToRoute('app_commande_achat_index');
}

    #[Route('/{id}/receptionner', name: 'commande_achat_receptionner', methods: ['GET'])]
public function receptionner(CommandeAchat $commandeAchat, EntityManagerInterface $em): Response
{
    // Vérifier si déjà réceptionnée
    if ($commandeAchat->getStatut() === 'réceptionnée') {
        $this->addFlash('warning', 'Cette commande est déjà réceptionnée.');
        return $this->redirectToRoute('app_commande_achat_index');
    }

    foreach ($commandeAchat->getLignes() as $ligne) {
        $article = $ligne->getArticle();
        $quantite = $ligne->getQuantite();

        // Vérifie si le stock existe
        $stock = $article->getStock();

        if (!$stock) {
            $stock = new Stock();
            $stock->setArticle($article);
            $stock->setQuantite(0);

            $article->setStock($stock); // lie le stock à l’article
            $em->persist($stock);
            $em->persist($article);
        }

        // Ajoute la quantité reçue
        $stock->setQuantite($stock->getQuantite() + $quantite);
    }

    // Met à jour le statut de la commande
    $commandeAchat->setStatut('réceptionnée');
    $em->persist($commandeAchat);
    $em->flush();

    $this->addFlash('success', 'Commande réceptionnée et stock mis à jour avec succès.');
    return $this->redirectToRoute('app_commande_achat_index');
}

}
