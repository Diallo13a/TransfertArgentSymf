<?php

namespace App\Repository;

use App\Entity\TRansaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TRansaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method TRansaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method TRansaction[]    findAll()
 * @method TRansaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TRansactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TRansaction::class);
    }

    // /**
    //  * @return TRansaction[] Returns an array of TRansaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findTransactionByCode($value): ?TRansaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.codeTransaction = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    

    
}
