<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\AuditLog; // Nouvelle entité
use App\Form\GroupType;
use App\Form\GroupPermissionsType;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use App\Repository\AuditLogRepository; // Nouveau repository
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\AuditLogger; // Nouveau service

#[Route('/group')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        GroupRepository $groupRepository,
        AuditLogger $auditLogger
    ): Response {
        $auditLogger->log(
            'AFFICHAGE_GROUPES',
            'Consultation de la liste des groupes'
        );

        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAllWithPermissions(),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PermissionRepository $permissionRepository,
        AuditLogger $auditLogger
    ): Response {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($group);
            $entityManager->flush();

            $auditLogger->log(
                'CREATION_GROUPE',
                sprintf('Création du groupe "%s" (ID: %d)', $group->getName(), $group->getId())
            );

            $this->addFlash('success', 'Groupe créé avec succès');
            return $this->redirectToRoute('app_group_index');
        }

        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
            'available_permissions' => $permissionRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(
        Group $group,
        AuditLogger $auditLogger
    ): Response {
        $auditLogger->log(
            'CONSULTATION_GROUPE',
            sprintf('Consultation du groupe "%s" (ID: %d)', $group->getName(), $group->getId())
        );

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'users' => $group->getUsers(),
            'permissions' => $group->getPermissions()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager,
        AuditLogger $auditLogger
    ): Response {
        $originalName = $group->getName();
        
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $details = sprintf(
                'Modification du groupe "%s" (ID: %d). Nouveau nom: "%s"',
                $originalName,
                $group->getId(),
                $group->getName()
            );
            
            $auditLogger->log('MODIFICATION_GROUPE', $details);

            $this->addFlash('success', 'Groupe mis à jour');
            return $this->redirectToRoute('app_group_index');
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/permissions', name: 'app_group_permissions', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function managePermissions(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager,
        PermissionRepository $permissionRepository,
        AuditLogger $auditLogger
    ): Response {
        // Sauvegarde des permissions actuelles pour comparaison
        $originalPermissions = [];
        foreach ($group->getPermissions() as $permission) {
            $originalPermissions[] = $permission->getId();
        }
        sort($originalPermissions);

        $form = $this->createForm(GroupPermissionsType::class, $group, [
            'permissions' => $permissionRepository->findAll()
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            // Journalisation des changements de permissions
            $newPermissions = [];
            foreach ($group->getPermissions() as $permission) {
                $newPermissions[] = $permission->getId();
            }
            sort($newPermissions);
            
            $added = array_diff($newPermissions, $originalPermissions);
            $removed = array_diff($originalPermissions, $newPermissions);
            
            $changes = [];
            if (!empty($added)) {
                $changes[] = 'Ajout: ' . implode(', ', array_map(
                    fn($id) => $permissionRepository->find($id)->getName(), 
                    $added
                ));
            }
            if (!empty($removed)) {
                $changes[] = 'Suppression: ' . implode(', ', array_map(
                    fn($id) => $permissionRepository->find($id)->getName(), 
                    $removed
                ));
            }
            
            $auditLogger->log(
                'MODIFICATION_PERMISSIONS',
                sprintf(
                    'Modification des permissions pour le groupe "%s" (ID: %d). %s',
                    $group->getName(),
                    $group->getId(),
                    $changes ? implode(' | ', $changes) : 'Aucun changement'
                )
            );

            $this->addFlash('success', 'Permissions mises à jour');
            return $this->redirectToRoute('app_group_show', ['id' => $group->getId()]);
        }

        return $this->render('group/permissions.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager,
        AuditLogger $auditLogger
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $auditLogger->log(
                'SUPPRESSION_GROUPE',
                sprintf('Suppression du groupe "%s" (ID: %d)', $group->getName(), $group->getId())
            );
            
            $entityManager->remove($group);
            $entityManager->flush();
            $this->addFlash('success', 'Groupe supprimé');
        }

        return $this->redirectToRoute('app_group_index');
    }
}