<?php
// src/Controller/InvoiceController.php
namespace App\Controller;

use App\Entity\Projet;
use App\Form\InvoiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projet/{id}/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Projet $projet, EntityManagerInterface $em): Response
    {
        $invoice = ['reference' => '', 'amount' => 0, 'status' => 'pending'];
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoices = $projet->getInvoices();
            $invoices[] = $form->getData();
            $projet->setInvoices($invoices);
            
            $em->flush();
            
            return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
        }

        return $this->render('invoice/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }
}