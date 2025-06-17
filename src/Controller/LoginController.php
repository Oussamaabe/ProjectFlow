<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Si déjà connecté, rediriger vers l'appairage 2FA
        if ($this->getUser()) {
            // Reset de la vérification 2FA à chaque nouvelle connexion
            $request->getSession()->remove('2fa_verified');
            return $this->redirectToRoute('app_authenticator_pair');
        }

        // Gestion des erreurs d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}