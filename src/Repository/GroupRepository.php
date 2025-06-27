<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * Récupère tous les groupes avec leurs permissions pré-chargées
     * @return Group[]
     */
    public function findAllWithPermissions(): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.permissions', 'p')
            ->addSelect('p') // Charge les permissions en une requête
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un groupe spécifique avec ses permissions
     */
    public function findWithPermissions(int $id): ?Group
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.permissions', 'p')
            ->addSelect('p')
            ->andWhere('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte le nombre de groupes ayant une permission spécifique
     */
    public function countGroupsWithPermission(string $permissionCode): int
    {
        return $this->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->join('g.permissions', 'p')
            ->andWhere('p.code = :code')
            ->setParameter('code', $permissionCode)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * QueryBuilder de base pour les filtres
     */
    public function createFilteredQueryBuilder(?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.permissions', 'p')
            ->addSelect('p');

        if ($search) {
            $qb->andWhere('g.name LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        return $qb;
    }
}