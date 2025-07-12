<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        SluggerInterface $slugger,
        ValidatorInterface $validator
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

        // Validation manuelle des données
        $errors = [];
        if ($form->isSubmitted()) {
            $errors = $validator->validate($user);
        }

        if ($form->isSubmitted() && $form->isValid() && count($errors) === 0) {
            // Gestion de l'upload de la photo de profil
            $profileImage = $form->get('profileImage')->getData();
            if ($profileImage) {
                $originalFilename = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profileImage->guessExtension();

                try {
                    $profileImage->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                    $user->setProfileImage($newFilename);
                    $this->addFlash('success', 'Photo de profil mise à jour');
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de la photo: '.$e->getMessage());
                }
            }

            // Vérification si l'email a changé
            $newEmail = $user->getEmail();
            if ($currentEmail !== $newEmail) {
                // Vérifier si le nouvel email est déjà utilisé
                $existingUser = $userRepository->findOneBy(['email' => $newEmail]);
                
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cette adresse email est déjà utilisée.');
                    return $this->render('profile/index.html.twig', [
                        'profileForm' => $form->createView(),
                        'errors' => $errors
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

            // Si le username a changé, déconnecter l'utilisateur
            if ($currentUsername !== $user->getUsername()) {
                return $this->redirectToRoute('app_logout');
            }

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'errors' => $errors
        ]);
    }
}