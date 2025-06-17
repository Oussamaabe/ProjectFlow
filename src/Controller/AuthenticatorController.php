<?php
//  AuthenticatorController.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AuthenticatorService;
use OTPHP\TOTP;

/**
 * @method User getUser()
 */
class AuthenticatorController extends AbstractController
{

    #[Route('/authenticator/pair', name: 'app_authenticator_pair')]
    public function pair(AuthenticatorService $authenticatorService, Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $secret = $request->request->get('secret');

            $authenticatorService->validatePairing($this->getUser(), $secret);

            return $this->redirectToRoute('app_home_index',);
        }
        [$qrCodeUri, $secret] = $authenticatorService->getQrCodeUri($this->getUser());
        return $this->render('authenticator/pair.html.twig', [
            'qrCodeUri' => $qrCodeUri,
            'secret' => $secret
        ]);
    }

    #[Route('/authenticator/verify', name: 'app_authenticator_verify')]
    public function verify(Request $request): Response
    {
        if(null=== $this->getUser()->getSecret()) {
            return $this->redirectToRoute('app_authenticator_pair'); 
        }
        if($request->isMethod(Request::METHOD_POST)) {
           $totp=TOTP::createFromSecret($this->getUser()->getSecret());
           $result=$totp->verify($request->request->getString('otp'));
        }
        return $this->render('authenticator/verify.html.twig',[
            'result'=> $result ?? null
        ]);
    }
}
