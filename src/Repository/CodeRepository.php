<?php

namespace App\Repository;

use App\Entity\Code;
use App\Entity\Festival;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Code>
 */
class CodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Code::class);
    }

    public function findOneByNameAndFestival(string $name, Festival $festival): ?Code
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.festivals', 'f')
            ->andWhere('c.name = :name')
            ->andWhere('f.id = :festivalId')
            ->setParameter('name', $name)
            ->setParameter('festivalId', $festival->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Code[] Returns an array of Code objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Code
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
