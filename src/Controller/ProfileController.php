<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

/**
 * @method User|null getUser()
 */
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Sauvegarde des valeurs actuelles pour comparaison
        $currentEmail = $user->getEmail();
        $currentUsername = $user->getUsername();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newEmail = $user->getEmail();
            
            // Vérification si l'email a changé
            if ($currentEmail !== $newEmail) {
                // Vérifier si le nouvel email est déjà utilisé
                $existingUser = $userRepository->findOneBy(['email' => $newEmail]);
                
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cette adresse email est déjà utilisée.');
                    return $this->render('profile/index.html.twig', [
                        'profileForm' => $form->createView(),
                    ]);
                }
                
                $this->addFlash('info', 'Email mis à jour avec succès.');
            }

            // Vérification si le username a changé
            if ($currentUsername !== $user->getUsername()) {
                $this->addFlash('warning', 'Votre nom d\'utilisateur a été modifié. Veuillez vous reconnecter.');
            }

            // Gestion du mot de passe
            if ($form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $this->addFlash('info', 'Votre mot de passe a été mis à jour');
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
}