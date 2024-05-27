<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

//    /**
//     * @return Module[] Returns an array of Module objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Module
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function incrementOrderAfterModule($courseId, $order)
{
    return $this->createQueryBuilder('m')
        ->update(Module::class, 'm')
        ->set('m.order', 'm.order + 1')
        ->andWhere('m.idCourse = :courseId')
        ->andWhere('m.order > :order')
        ->setParameter('courseId', $courseId)
        ->setParameter('order', $order)
        ->getQuery()
        ->execute();
}

public function incrementOrderForCourse($courseId)
{
    return $this->createQueryBuilder('m')
        ->update(Module::class, 'm')
        ->set('m.order', 'm.order + 1')
        ->andWhere('m.idCourse = :courseId')
        ->setParameter('courseId', $courseId)
        ->getQuery()
        ->execute();
}
public function getMaxOrderForCourse($courseId)
{
    return $this->createQueryBuilder('m')
        ->select('MAX(m.order) as max_order')
        ->andWhere('m.idCourse = :courseId')
        ->setParameter('courseId', $courseId)
        ->getQuery()
        ->getSingleScalarResult();
}


public function decrementOrderAfterModule($courseId, $order)
{
    return $this->createQueryBuilder('m')
        ->update(Module::class, 'm')
        ->set('m.order', 'm.order - 1')
        ->andWhere('m.idCourse = :courseId')
        ->andWhere('m.order > :order')
        ->setParameter('courseId', $courseId)
        ->setParameter('order', $order)
        ->getQuery()
        ->execute();
}



     /**
     * Get all modules associated with a course.
     *
     * @param int $courseId The ID of the course
     * @return array The modules associated with the course
     */
    public function getModules(int $courseId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.idCourse = :idCourse')
            ->setParameter('idCourse', $courseId)
            ->getQuery()
            ->getResult();
    }


}
