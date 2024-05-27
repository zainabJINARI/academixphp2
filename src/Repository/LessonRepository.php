<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lesson>
 *
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

//    /**
//     * @return Lesson[] Returns an array of Lesson objects
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

//    public function findOneBySomeField($value): ?Lesson
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function incrementOrderAfterLesson($moduleId, $order)
{
    return $this->createQueryBuilder('m')
        ->update(Lesson::class, 'm')
        ->set('m.order', 'm.order + 1')
        ->andWhere('m.idModule = :moduleId')
        ->andWhere('m.order > :order')
        ->setParameter('moduleId', $moduleId)
        ->setParameter('order', $order)
        ->getQuery()
        ->execute();
}

public function incrementOrderForModule($moduleId)
{
    return $this->createQueryBuilder('m')
        ->update(Lesson::class, 'm')
        ->set('m.order', 'm.order + 1')
        ->andWhere('m.idModule = :moduleId')
        ->setParameter('moduleId', $moduleId)
        ->getQuery()
        ->execute();
}
public function getMaxOrderForModule($moduleId)
{
    return $this->createQueryBuilder('m')
        ->select('MAX(m.order) as max_order')
        ->andWhere('m.idModule = :moduleId')
        ->setParameter('moduleId', $moduleId)
        ->getQuery()
        ->getSingleScalarResult();
}

      /**
     * Get all modules associated with a course.
     *
     * @param int $courseId The ID of the course
     * @return array The modules associated with the course
     */
    public function getLessons(int $courseId): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.idModule = :idModule')
            ->setParameter('idModule', $courseId)
            ->getQuery()
            ->getResult();
    }
    public function findLessonsWithOrderGreaterThan($order)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.order > :order')
            ->setParameter('order', $order)
            ->orderBy('l.order', 'ASC')
            ->getQuery()
            ->getResult();
    }
}