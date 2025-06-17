<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthenticatorService;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User|null getUser()
 */
class AuthenticatorController extends AbstractController
{
    #[Route('/authenticator/pair', name: 'app_authenticator_pair')]
    public function pair(AuthenticatorService $authenticatorService, Request $request): Response
    {
        // Redirection si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Traitement du formulaire d'appairage
        if ($request->isMethod(Request::METHOD_POST)) {
            $secret = $request->request->get('secret');
            $authenticatorService->validatePairing($this->getUser(), $secret);
            
            // Réinitialisation du statut de vérification
            $request->getSession()->remove('2fa_verified');
            
            // Redirection vers la vérification OTP
            return $this->redirectToRoute('app_authenticator_verify');
        }

        // Génération du QR Code pour nouvel appairage
        [$qrCodeUri, $secret] = $authenticatorService->getQrCodeUri($this->getUser());
        
        return $this->render('authenticator/pair.html.twig', [
            'qrCodeUri' => $qrCodeUri,
            'secret' => $secret,
        ]);
    }

    #[Route('/authenticator/verify', name: 'app_authenticator_verify')]
    public function verify(Request $request): Response
    {
        // Redirection si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Redirection vers l'appairage si 2FA non configuré
        if (!$this->getUser()->getSecret()) {
            return $this->redirectToRoute('app_authenticator_pair');
        }

        // Vérification de l'OTP
        if ($request->isMethod(Request::METHOD_POST)) {
            $totp = TOTP::createFromSecret($this->getUser()->getSecret());
            if ($totp->verify($request->request->get('otp'))) {
                $request->getSession()->set('2fa_verified', true);
                return $this->redirectToRoute('app_dashboard');
            }
            $this->addFlash('error', 'Code OTP invalide');
        }

        return $this->render('authenticator/verify.html.twig');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(Request $request): Response
    {
        // Redirection si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérification du 2FA si configuré
        if ($this->getUser()->getSecret() && !$request->getSession()->get('2fa_verified')) {
            return $this->redirectToRoute('app_authenticator_verify');
        }

        return $this->render('dashboard/index.html.twig');
    }
}