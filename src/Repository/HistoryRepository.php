<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, History::class);
    }

     /**
      * @return History[] Returns an array of History objects
      */
    public function findByCurrency($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.currency = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

}
