<?php

namespace App\Repository;

use App\Entity\DocumentSociete;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentSociete>
 *
 * @method DocumentSociete|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentSociete|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentSociete[]    findAll()
 * @method DocumentSociete[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentSocieteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentSociete::class);
    }

//    /**
//     * @return DocumentSociete[] Returns an array of DocumentSociete objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentSociete
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
