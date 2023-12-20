<?php

namespace App\Repository;

use App\Entity\AddressCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddressCategory>
 *
 * @method AddressCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressCategory[]    findAll()
 * @method AddressCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressCategory::class);
    }

//    /**
//     * @return AddressCategory[] Returns an array of AddressCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AddressCategory
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
