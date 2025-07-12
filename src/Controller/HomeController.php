<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index')]
    public function index(): Response
    {
        // Si utilisateur connecté, rediriger vers le dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('app_projet_statistiques');
        }
        
        // Si non connecté, afficher la page welcome
        return $this->render('welcome.html.twig');
    }

    #[Route('/dashboard', name: 'app_projet_statistiques')]
    public function dashboard(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
}