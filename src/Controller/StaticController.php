<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('static/about.html.twig');
    }

    #[Route('/features', name: 'app_features')]
    public function features(): Response
    {
        return $this->render('static/features.html.twig');
    }

    #[Route('/team', name: 'app_team')]
    public function team(): Response
    {
        return $this->render('static/team.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('static/contact.html.twig');
    }
}