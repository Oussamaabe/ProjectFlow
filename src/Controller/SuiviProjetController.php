<?php
// src/Controller/SuiviProjetController.php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Enum\RessourceType as RessourceTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\Security;
#[Route('/suivi-projet')]
#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TECH_AGENT')")]// Un seul IsGranted suffit (ROLE_ADMIN hérite généralement de tous les droits)
class SuiviProjetController extends AbstractController
{
    #[Route('/{id}/ressources', name: 'app_suivi_ressources', methods: ['GET', 'POST'])]
    public function ressources(Projet $projet, Request $request, EntityManagerInterface $em): Response
    {
        $ressource = new Ressource();
        $ressource->setProjet($projet);

        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ressource);
            $em->flush();
            $this->addFlash('success', 'Ressource ajoutée');
            return $this->redirectToRoute('app_suivi_ressources', ['id' => $projet->getId()]);
        }

        return $this->render('suivi_projet/ressources.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
            'ressources' => $projet->getRessources(),
            'total' => $this->calculateTotal($projet),
            'types' => [
                'humain' => RessourceTypeEnum::HUMAIN->value,
                'materiel' => RessourceTypeEnum::MATERIEL->value,
                'matiere' => RessourceTypeEnum::MATIERE->value,
            ],
        ]);
    }

    private function calculateTotal(Projet $projet): float
    {
        $total = 0;
        foreach ($projet->getRessources() as $ressource) {
            $total += $ressource->getCoutTotal();
        }
        return $total;
    }
    #[Route('/ressource/{id}/edit', name: 'app_suivi_ressource_edit')]
public function editRessource(Ressource $ressource, Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(RessourceType::class, $ressource);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Ressource modifiée.');
        return $this->redirectToRoute('app_suivi_ressources', ['id' => $ressource->getProjet()->getId()]);
    }

    return $this->render('suivi_projet/edit.html.twig', [
        'form' => $form->createView(),
        'ressource' => $ressource,
    ]);
}

#[Route('/ressource/{id}', name: 'app_suivi_ressource_delete', methods: ['POST'])]
public function deleteRessource(Request $request, Ressource $ressource, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete' . $ressource->getId(), $request->request->get('_token'))) {
        $em->remove($ressource);
        $em->flush();
        $this->addFlash('success', 'Ressource supprimée.');
    }

    return $this->redirectToRoute('app_suivi_ressources', ['id' => $ressource->getProjet()->getId()]);
}

}
