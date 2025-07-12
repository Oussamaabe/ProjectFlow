<?php
// src/Repository/ProjetRepository.php
namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 *
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    public function save(Projet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Projet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createQueryBuilderForList(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.affaire', 'a')
            ->addSelect('a')
            ->orderBy('p.dateDebut', 'DESC');
    }

    public function findProjectsInProgress(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.dateFinPrevue IS NULL OR p.dateFinPrevue > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findProjectsByAffaireType(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.affaire', 'a')
            ->andWhere('a.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    public function findProjectsEndingSoon(int $days = 30): array
    {
        $dateLimit = (new \DateTime())->modify("+$days days");

        return $this->createQueryBuilder('p')
            ->where('p.dateFinPrevue BETWEEN :now AND :limit')
            ->setParameter('now', new \DateTime())
            ->setParameter('limit', $dateLimit)
            ->getQuery()
            ->getResult();
    }
    // src/Repository/ProjetRepository.php


    public function getStatusDistribution(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                CASE 
                    WHEN p.is_completed = 1 THEN 'Terminé' 
                    ELSE 'En cours' 
                END AS status,
                COUNT(p.id) AS count
            FROM projet p
            GROUP BY p.is_completed
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getMonthlyProjectEvolution(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                DATE_FORMAT(p.date_debut, '%Y-%m') AS month, 
                COUNT(p.id) AS count
            FROM projet p
            GROUP BY month
            ORDER BY month ASC
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getAverageCompletionTime(): ?string
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                AVG(DATEDIFF(p.date_fin_prevue, p.date_debut)) AS avg_days
            FROM projet p
            WHERE p.is_completed = 1
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $data = $result->fetchAssociative();

        return $data['avg_days'] ? round($data['avg_days']) . ' jours' : null;
    }

    public function findTopBudgetProjects(int $limit = 5): array
{
    $conn = $this->getEntityManager()->getConnection();
    
    $sql = "
        SELECT 
            p.id,
            p.nom,
            (SELECT SUM(JSON_EXTRACT(pl.value, '$.quantity') * JSON_EXTRACT(pl.value, '$.unitPrice'))
             FROM JSON_TABLE(
                 p.price_list,
                 '$[*]' COLUMNS(
                     value JSON PATH '$'
                 )
             ) AS pl) AS total_budget
        FROM projet p
        ORDER BY total_budget DESC
        LIMIT :limit
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
    $result = $stmt->executeQuery();
    
    return $result->fetchAllAssociative();
}
}
