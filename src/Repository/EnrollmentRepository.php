<?php

// src/Repository/EnrollmentRepository.php

namespace App\Repository;

use App\Entity\Enrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends ServiceEntityRepository<Enrollment>
 *
 * @method Enrollment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enrollment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enrollment[]    findAll()
 * @method Enrollment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    /**
     * Find courses enrolled by a student
     *
     * @param int $studentId
     * @return Enrollment[]
     */
    public function findCoursesByStudent(string $studentEmail): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.student', 's')
            ->innerJoin('e.course', 'c')
            ->where('s.email = :studentEmail')
            ->setParameter('studentEmail', $studentEmail)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find students enrolled in a course
     *
     * @param int $courseId
     * @return Enrollment[]
     */
    public function findStudentsByCourse(int $courseId): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.course', 'c')
            ->where('c.id = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count the number of students enrolled in a course
     *
     * @param int $courseId
     * @return int
     */
    public function countStudentsByCourse(int $courseId): int
    {
        return (int)$this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->innerJoin('e.course', 'c')
            ->where('c.id = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
