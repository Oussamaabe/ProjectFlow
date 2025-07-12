<?php
// src/Controller/DocumentController.php
namespace App\Controller;

use App\Entity\Projet;
use App\Form\DocumentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projet/{id}/document')]
class DocumentController extends AbstractController
{
    #[Route('/new', name: 'app_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Projet $projet, EntityManagerInterface $em): Response
    {
        $document = ['name' => '', 'type' => 'other', 'file' => null];
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $file = $form->get('file')->getData();

            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('documents_directory'), $fileName);
                $data['filePath'] = $fileName;
            }

            $documents = $projet->getDocuments();
            $documents[] = $data;
            $projet->setDocuments($documents);

            $em->flush();

            return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
        }

        return $this->render('document/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/download/{index}', name: 'app_document_download', methods: ['GET'])]
    public function download(Projet $projet, int $index): Response
    {
        $documents = $projet->getDocuments();
        if (!isset($documents[$index])) {
            throw $this->createNotFoundException('Document non trouvé');
        }

        $document = $documents[$index];
        $filePath = $this->getParameter('documents_directory') . '/' . $document['filePath'];

        return $this->file($filePath);
    }
}
