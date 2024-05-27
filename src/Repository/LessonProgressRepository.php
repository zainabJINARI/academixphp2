<?php

namespace App\Repository;

use App\Entity\LessonProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Lesson;


/**
 * @extends ServiceEntityRepository<LessonProgress>
 *
 * @method LessonProgress|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonProgress|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonProgress[]    findAll()
 * @method LessonProgress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonProgress::class);
    }

//    /**
//     * @return LessonProgress[] Returns an array of LessonProgress objects
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

//    public function findOneBySomeField($value): ?LessonProgress
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function isLessonCompleted($studentEnrollment, Lesson $lesson): bool
    {
        $courseProgress = $studentEnrollment->getCourseProgress();

        // Fetch the LessonProgress entity for the given lesson and course progress
        $lessonProgress = $this->findOneBy([
            'lesson' => $lesson,
            'moduleProgress' => $courseProgress->getModuleProgress() // Assuming LessonProgress is related to ModuleProgress
        ]);

        // Check if the lesson has been completed
        return $lessonProgress && $lessonProgress->isCompleted();
    }
}
