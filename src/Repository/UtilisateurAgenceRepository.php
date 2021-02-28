<?php

namespace App\Repository;

use App\Entity\UtilisateurAgence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UtilisateurAgence|null find($id, $lockMode = null, $lockVersion = null)
 * @method UtilisateurAgence|null findOneBy(array $criteria, array $orderBy = null)
 * @method UtilisateurAgence[]    findAll()
 * @method UtilisateurAgence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurAgenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UtilisateurAgence::class);
    }

    // /**
    //  * @return UtilisateurAgence[] Returns an array of UtilisateurAgence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UtilisateurAgence
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
