<?php

namespace App\Repository;

use App\Entity\FicheFrais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheFrais>
 */
class FicheFraisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheFrais::class);
    }

    public function findTop3ByMonth($mois): array
    {
        return $this->createQueryBuilder('ff')
            ->andWhere('ff.mois = :month')
            ->setParameter('month', $mois, \Doctrine\DBAL\Types\Types::STRING)
            ->orderBy('f.totalAmount', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return FicheFrais[] Returns an array of FicheFrais objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FicheFrais
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
