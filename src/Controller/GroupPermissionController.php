<?php
// src/Controller/GroupPermissionController.php
namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/group')]
class GroupPermissionController extends AbstractController
{
    #[Route('/{id}/permissions', name: 'admin_group_permissions', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function managePermissions(
        Request $request,
        Group $group,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Permissions mises à jour');
            return $this->redirectToRoute('admin_group_show', ['id' => $group->getId()]);
        }

        return $this->render('admin/group/permissions.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }
}