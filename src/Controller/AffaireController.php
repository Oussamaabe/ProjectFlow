<?php

namespace App\Controller;

use App\Entity\Affaire;
use App\Entity\Projet;
use App\Enum\AffaireStatus;
use App\Form\AffaireType;
use App\Repository\AffaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\Security;

#[Route('/affaires')]
class AffaireController extends AbstractController
{
    #[Route('/', name: 'app_affaire_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')")]
    public function index(AffaireRepository $repository): Response
    {
        return $this->render('affaire/index.html.twig', [
            'affaires' => $repository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/new', name: 'app_affaire_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')")]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $affaire = new Affaire();
        $form = $this->createForm(AffaireType::class, $affaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($affaire);
            $em->flush();

            $this->addFlash('success', 'Affaire créée avec succès');
            return $this->redirectToRoute('app_affaire_index');
        }

        return $this->render('affaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_affaire_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')")]
    public function show(Affaire $affaire): Response
    {
        return $this->render('affaire/show.html.twig', [
            'affaire' => $affaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_affaire_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')")]
    public function edit(Request $request, Affaire $affaire, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AffaireType::class, $affaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Créer automatiquement un projet si le statut passe à "adjudicataire"
            if ($affaire->getStatus() === AffaireStatus::ADJUDICATAIRE->value && !$affaire->getProjet()) {
                $projet = new Projet();
                $projet->setNom($affaire->getTitre());
                $projet->setDateDebut(new \DateTime());
                $projet->setDescription("Projet créé automatiquement pour l'affaire adjudicataire");
                $affaire->setProjet($projet);

                $em->persist($projet);
                $this->addFlash('success', 'Projet créé automatiquement pour cette affaire adjudicataire');
            }

            $em->flush();
            $this->addFlash('success', 'Affaire mise à jour');
            return $this->redirectToRoute('app_affaire_show', ['id' => $affaire->getId()]);
        }

        return $this->render('affaire/edit.html.twig', [
            'affaire' => $affaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/create-project', name: 'app_affaire_create_project', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')")]
    public function createProject(Affaire $affaire, EntityManagerInterface $em): Response
    {
        if ($affaire->getStatus() !== AffaireStatus::ADJUDICATAIRE->value) {
            $this->addFlash('error', 'Seules les affaires adjudicataires peuvent avoir un projet');
            return $this->redirectToRoute('app_affaire_show', ['id' => $affaire->getId()]);
        }

        if ($affaire->getProjet()) {
            $this->addFlash('warning', 'Un projet existe déjà pour cette affaire');
            return $this->redirectToRoute('app_affaire_show', ['id' => $affaire->getId()]);
        }

        $projet = new Projet();
        $projet->setNom($affaire->getTitre());
        $projet->setDateDebut(new \DateTime());
        $projet->setDescription("Projet créé manuellement pour l'affaire adjudicataire");
        $affaire->setProjet($projet);

        $em->persist($projet);
        $em->flush();

        $this->addFlash('success', 'Projet créé avec succès');
        return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
    }

    #[Route('/{id}/delete', name: 'app_affaire_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Affaire $affaire, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$affaire->getId(), $request->request->get('_token'))) {
            $em->remove($affaire);
            $em->flush();
            $this->addFlash('success', 'Affaire supprimée avec succès');
        }

        return $this->redirectToRoute('app_affaire_index');
    }
}
