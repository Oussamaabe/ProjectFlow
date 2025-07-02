<?php
// src/Service/AuditLogger.php
namespace App\Service;

use App\Entity\AuditLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class AuditLogger
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function log(string $action, ?string $details = null, ?User $user = null): void
    {
        $log = new AuditLog();
        
        // Utilisateur actuel ou système
        $log->setUser($user ?? $this->security->getUser());
        
        // Adresse IP
        $request = $this->requestStack->getCurrentRequest();
        $log->setIpAddress($request?->getClientIp());
        
        $log->setAction($action);
        $log->setDetails($details);

        $this->em->persist($log);
        $this->em->flush();
    }

    // Nouvelle méthode pour les actions CRUD avec détails des changements
    public function logCrud(
        string $entityType,
        string $operation,
        ?int $entityId = null,
        ?string $entityName = null,
        ?string $changes = null
    ): void {
        $details = sprintf(
            '%s: %s%s%s',
            ucfirst($entityType),
            $entityName ? '"' . $entityName . '" ' : '',
            $entityId ? '(ID: ' . $entityId . ') ' : '',
            $changes ? ' | Changements: ' . $changes : ''
        );

        $this->log('CRUD_' . strtoupper($operation), $details);
    }
}