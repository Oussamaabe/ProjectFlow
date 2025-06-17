<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Route pour la page de bienvenue.
     */
    #[Route('/welcome', name: 'app_welcome')]
    public function welcome(): Response
    {
        // Render la page de bienvenue
        return $this->render('welcome.html.twig');
    }

    /**
     * Route pour la page d'accueil principale.
     * Redirige les utilisateurs connectés vers la page d'accueil
     * et les utilisateurs non connectés vers la page de bienvenue.
     */
    #[Route('/', name: 'app_home_index')]
    public function index(): Response
    {
        // Vérifie si l'utilisateur est connecté
        if ($this->getUser()) {
            // Si connecté, afficher la page d'accueil principale
            return $this->render('home/index.html.twig');
        } else {
            // Si non connecté, rediriger vers la page de bienvenue
            return $this->redirectToRoute('app_welcome');
        }
    }
}
