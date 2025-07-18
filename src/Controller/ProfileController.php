<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
        UserRepository $userRepository,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Sauvegarde des valeurs actuelles pour comparaison
        $currentEmail = $user->getEmail();
        $currentUsername = $user->getUsername();
        $currentProfileImage = $user->getProfileImage();

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
                        'user' => $user,
                    ]);
                }
                
                $this->addFlash('info', 'Email mis à jour avec succès.');
            }

            // Vérification si le username a changé
            if ($currentUsername !== $user->getUsername()) {
                $this->addFlash('warning', 'Votre nom d\'utilisateur a été modifié. Veuillez vous reconnecter.');
            }

            // Gestion du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );
                $this->addFlash('success', 'Votre mot de passe a été mis à jour');
            }

            // Gestion de la photo de profil
            $profileImageFile = $form->get('profileImage')->getData();
            if ($profileImageFile) {
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profileImageFile->guessExtension();

                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                }

                $user->setProfileImage($newFilename);
                $this->addFlash('success', 'Photo de profil mise à jour');
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/change-password', name: 'app_profile_change_password')]
    public function changePassword(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Vérifier si l'ancien mot de passe est correct
            if (!$passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect');
                return $this->render('profile/change_password.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            
            // Mettre à jour le mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['newPassword']
                )
            );
            
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe mis à jour avec succès');
            
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}