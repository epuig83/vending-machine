<?php

namespace App\Service;

use App\Entity\Coin;
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

    public function findCoinByValue(float $value): ?Coin
    {
        $repository = $this->manager->getRepository(Coin::class);
        return $repository->findOneBy(['value' => $value]);
    }
}