<?php

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
}