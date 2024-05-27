<?php

namespace App\Repository;

use App\Entity\RequestAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestAccount>
 *
 * @method RequestAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestAccount[]    findAll()
 * @method RequestAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestAccount::class);
    }

//    /**
//     * @return RequestAccount[] Returns an array of RequestAccount objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RequestAccount
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
