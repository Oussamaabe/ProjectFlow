<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Entity\DocumentSociete;
use App\Form\SocieteType;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/societe')]
class SocieteController extends AbstractController
{
    #[Route('/', name: 'app_societe_index', methods: ['GET'])]
    public function index(SocieteRepository $societeRepository): Response
    {
        return $this->render('societe/index.html.twig', [
            'societes' => $societeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_societe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $societe = new Societe();
        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($form->get('documents') as $documentForm) {
                $uploadedFile = $documentForm->get('fichier')->getData();
                if ($uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                    try {
                        $uploadedFile->move(
                            $this->getParameter('documents_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        throw new \Exception('Erreur lors du téléversement du fichier');
                    }

                    /** @var DocumentSociete $document */
                    $document = $documentForm->getData();
                    $document->setFichier($newFilename);
                    $document->setSociete($societe);
                }
            }

            $em->persist($societe);
            $em->flush();

            return $this->redirectToRoute('app_societe_index');
        }

        return $this->render('societe/new.html.twig', [
            'societe' => $societe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_societe_show', methods: ['GET'])]
    public function show(Societe $societe): Response
    {
        return $this->render('societe/show.html.twig', [
            'societe' => $societe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_societe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Societe $societe, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($form->get('documents') as $documentForm) {
                $uploadedFile = $documentForm->get('fichier')->getData();
                if ($uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                    try {
                        $uploadedFile->move(
                            $this->getParameter('documents_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        throw new \Exception('Erreur lors du téléversement du fichier');
                    }

                    /** @var DocumentSociete $document */
                    $document = $documentForm->getData();
                    $document->setFichier($newFilename);
                    $document->setSociete($societe);
                }
            }

            $em->flush();

            return $this->redirectToRoute('app_societe_index');
        }

        return $this->render('societe/edit.html.twig', [
            'societe' => $societe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_societe_delete', methods: ['POST'])]
    public function delete(Request $request, Societe $societe, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $societe->getId(), $request->request->get('_token'))) {
            $em->remove($societe);
            $em->flush();
        }

        return $this->redirectToRoute('app_societe_index');
    }
}
