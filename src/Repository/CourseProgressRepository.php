<?php

namespace App\Repository;

use App\Entity\CourseProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseProgress>
 *
 * @method CourseProgress|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseProgress|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseProgress[]    findAll()
 * @method CourseProgress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseProgress::class);
    }

//    /**
//     * @return CourseProgress[] Returns an array of CourseProgress objects
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

//    public function findOneBySomeField($value): ?CourseProgress
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
