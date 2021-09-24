<?php

namespace App\Repository;

use App\Entity\Coin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coin[]    findAll()
 * @method Coin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coin::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByItemType(string $drinkType): ?Coin
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.type', 'drink_type')
            ->andWhere('drink_type.name = :name')
            ->setParameter('name', $drinkType)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
