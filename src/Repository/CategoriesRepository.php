<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categories>
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categories::class);
    }

    public function findTendencyWithAnnouncements(): array
    {
        $firstDayOfMonth = new \DateTimeImmutable('first day of this month 00:00:00');
        $lastDayOfMonth = new \DateTimeImmutable('last day of this month 23:59:59');

        return $this->createQueryBuilder('c')
            ->leftJoin('c.announcements', 'a')
            ->select('c.id, c.name, c.color_hex, c.icon, COUNT(DISTINCT a.id) as total_announcements')
            ->where('a.created_at BETWEEN :start AND :end')
            ->andWhere('a.is_active = :active')
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->setParameter('active', true)
            ->groupBy('c.id, c.name, c.color_hex, c.icon')
            ->orderBy('total_announcements', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();
    }

    //    /**
    //     * @return Categories[] Returns an array of Categories objects
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

    //    public function findOneBySomeField($value): ?Categories
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
