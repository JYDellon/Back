<?php

namespace App\Repository;

use App\Entity\LineProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineProduct>
 *
 * @method LineProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineProduct[]    findAll()
 * @method LineProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineProduct::class);
    }

//    /**
//     * @return LineProduct[] Returns an array of LineProduct objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LineProduct
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
