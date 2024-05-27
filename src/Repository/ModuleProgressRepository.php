<?php

namespace App\Repository;

use App\Entity\ModuleProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleProgress>
 *
 * @method ModuleProgress|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleProgress|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleProgress[]    findAll()
 * @method ModuleProgress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleProgress::class);
    }



    public function findModuleProgressesByCourseProgress(int $courseProgressId): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.moduleProgresses', 'mp')
            ->where('mp.courseProgress = :courseProgressId')
            ->setParameter('courseProgressId', $courseProgressId)
            ->getQuery()
            ->getResult();
    }

}
