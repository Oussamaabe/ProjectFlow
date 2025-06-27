<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Permission;
use App\Form\GroupType;
use App\Form\GroupPermissionsType;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/group')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAllWithPermissions(),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PermissionRepository $permissionRepository
    ): Response {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($group);
            $entityManager->flush();

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
    public function show(Group $group): Response
    {
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
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
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
        PermissionRepository $permissionRepository
    ): Response {
        $form = $this->createForm(GroupPermissionsType::class, $group, [
            'permissions' => $permissionRepository->findAll()
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Permissions mises à jour');
            return $this->redirectToRoute('app_group_show', ['id' => $group->getId()]);
        }

        return $this->render('group/permissions.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function delete(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
            $this->addFlash('success', 'Groupe supprimé');
        }

        return $this->redirectToRoute('app_group_index');
    }
}