<?php
// src/Repository/AuditLogRepository.php
namespace App\Repository;

use App\Entity\AuditLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuditLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditLog::class);
    }

    public function findFilteredLogs(
        ?string $action = null,
        ?int $userId = null,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        int $limit = 100
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.timestamp', 'DESC')
            ->setMaxResults($limit);

        if ($action) {
            $qb->andWhere('a.action = :action')
               ->setParameter('action', $action);
        }

        if ($userId) {
            $qb->andWhere('a.user = :userId')
               ->setParameter('userId', $userId);
        }

        if ($startDate) {
            $qb->andWhere('a.timestamp >= :startDate')
               ->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $qb->andWhere('a.timestamp <= :endDate')
               ->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getResult();
    }

    public function getDistinctActions(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.action')
            ->orderBy('a.action', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}