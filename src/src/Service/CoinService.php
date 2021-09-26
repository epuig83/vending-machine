<?php

namespace App\Service;

use App\Entity\Coin;
use App\Exception\CoinException;
use Doctrine\Persistence\ManagerRegistry;

class CoinService
{
    /**
     * @Var ManagerRegistry
     */
    protected $manager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->manager = $managerRegistry;
    }

    /**
     * @param float $value
     * @return Coin|null
     * @throws CoinException
     */
    public function findCoinByValue(float $value): ?Coin
    {
        $repository = $this->manager->getRepository(Coin::class);
        $coin = $repository->findOneBy(['value' => $value]);

        if (!$coin) {
            throw CoinException::notFoundMessage();
        }

        return $coin;
    }
}