<?php
// src/Controller/RecoveryController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RecoveryController extends AbstractController
{
    private $logger;
    private $params;

    public function __construct(
        LoggerInterface $logger,
        ParameterBagInterface $params
    ) {
        $this->logger = $logger;
        $this->params = $params;
    }

    #[Route('/forgot-password', name: 'app_forgot_password_request')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = Uuid::v4()->toRfc4122(); // Format compatible avec la base de données
                $user->setResetToken($token);
                $user->setTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
                $entityManager->flush();

                $resetUrl = $this->generateUrl(
                    'app_reset_password', 
                    ['token' => $token], 
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                try {
                    // Récupération de l'email de l'expéditeur depuis les paramètres
                    $fromEmail = $this->params->get('app.mailer_from');
                    
                    $emailMessage = (new Email())
                        ->from($fromEmail)
                        ->to($user->getEmail()) // Envoi à l'email de l'utilisateur
                        ->subject('Réinitialisation de votre mot de passe')
                        ->html($this->renderView('emails/reset_password.html.twig', [
                            'resetUrl' => $resetUrl,
                            'expiration_date' => new \DateTimeImmutable('+1 hour'),
                        ]));

                    $mailer->send($emailMessage);
                    
                    $this->logger->info('Email sent to: ' . $user->getEmail());
                } catch (\Exception $e) {
                    $this->logger->error('Email sending failed: ' . $e->getMessage());
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
                }
            } else {
                // Log pour les emails non trouvés
                $this->logger->info('Password reset requested for non-existing email: ' . $email);
            }

            $this->addFlash(
                'success', 
                'Si votre adresse email est valide, vous recevrez un email de réinitialisation.'
            );
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password_request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        // Vérification du token et de son expiration
        if (!$user || $user->getTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Effacer le token
            $user->setResetToken(null);
            $user->setTokenExpiresAt(null);

            // Encoder le nouveau mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->flush();

            $this->addFlash(
                'success', 
                'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'
            );
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $token,
        ]);
    }
}