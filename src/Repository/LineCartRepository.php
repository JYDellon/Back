<?php

namespace App\Repository;

use App\Entity\LineCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineCart>
 *
 * @method LineCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineCart[]    findAll()
 * @method LineCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineCartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineCart::class);
    }

//    /**
//     * @return LineCart[] Returns an array of LineCart objects
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

//    public function findOneBySomeField($value): ?LineCart
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
