<?php

namespace App\Repository;

use App\Entity\Affaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Affaire>
 *
 * @method Affaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Affaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Affaire[]    findAll()
 * @method Affaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affaire::class);
    }

    public function save(Affaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function remove(Affaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createQueryBuilderForList(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.projet', 'p')
            ->addSelect('p')
            ->orderBy('a.dateDebut', 'DESC');
    }

    public function findAffairesWithProjects(): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.projet', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();
    }

    public function findAdjudicatairesWithoutProject(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->andWhere('a.projet IS NULL')
            ->setParameter('status', 'adjudicataire')
            ->getQuery()
            ->getResult();
    }

    public function countByStatus(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.status, COUNT(a.id) as count')
            ->groupBy('a.status')
            ->getQuery()
            ->getResult();
    }

    public function search(string $term): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.titre LIKE :term')
            ->orWhere('a.client LIKE :term')
            ->orWhere('a.description LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->getResult();
    }
 
}