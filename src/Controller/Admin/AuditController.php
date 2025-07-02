<?php
// src/Controller/Admin/AuditController.php
namespace App\Controller\Admin;

use App\Repository\AuditLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/audit', name: 'admin_audit_')]
class AuditController extends AbstractController
{
    #[Route('/logs', name: 'logs')]
    public function logs(
        Request $request,
        AuditLogRepository $repository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Filtrage par date
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        
        $logs = $repository->findFilteredLogs(
            action: $request->query->get('action'),
            userId: $request->query->get('user') ? (int)$request->query->get('user') : null,
            startDate: $startDate ? new \DateTime($startDate) : null,
            endDate: $endDate ? new \DateTime($endDate) : null,
            limit: 100
        );

        return $this->render('admin/audit/logs.html.twig', [
            'logs' => $logs,
            'actions' => $repository->getDistinctActions(),
        ]);
    }
}