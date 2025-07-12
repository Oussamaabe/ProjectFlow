<?php
// src/Controller/PriceListItemController.php
namespace App\Controller;

use App\Entity\Projet;
use App\Form\PriceListItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projet/{id}/price-list-item')]
class PriceListItemController extends AbstractController
{
    #[Route('/new', name: 'app_price_list_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Projet $projet, EntityManagerInterface $em): Response
    {
        $item = ['description' => '', 'quantity' => 1, 'unit' => 'unit', 'unitPrice' => 0];
        $form = $this->createForm(PriceListItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['total'] = $data['quantity'] * $data['unitPrice'];
            
            $priceList = $projet->getPriceList();
            $priceList[] = $data;
            $projet->setPriceList($priceList);
            
            $em->flush();
            
            return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
        }

        return $this->render('price_list_item/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }
}