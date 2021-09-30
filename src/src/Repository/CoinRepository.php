<?php

namespace App\Repository;

use App\Entity\Coin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * Get all the coins sorted by value (DESC) and less than one
     *
     * @return array|null
     */
    public function findCoinsSortedByValue(): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('c.value, c.amount')
            ->where('c.value < 1')
            ->orderBy('c.value', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
