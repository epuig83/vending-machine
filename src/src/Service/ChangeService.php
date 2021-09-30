<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\Item;
use App\Exception\CoinException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ChangeService
{
    /**
     * @Var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var array
     */
    private array $coinStatus;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->coinStatus = [];
    }

    /**
     * @param float $change
     * @return array
     * @throws CoinException
     */
    private function calculateChange(float $change): array
    {
        $result = [];

        foreach ($this->coinStatus as &$coin) {
            if ($change == 0) {
                break;
            }
            while ($coin['amount'] > 0 && bccomp($change, $coin['value'], 2) >= 0) {
                array_push($result, $coin['value']);
                $change = number_format($change - $coin['value'], 2);
                --$coin['amount'];
            }
        }

        if ($change > 0) {
            throw CoinException::notEnoughCoinsMessage();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCoinStatus(): array
    {
        return $this->coinStatus;
    }

    /**
     * @param Item $item
     * @param float $insertedMoney
     * @return array
     * @throws CoinException
     */
    public function getChange(Item $item, float $insertedMoney): array
    {
        $repository = $this->em->getRepository(Coin::class);
        $this->coinStatus = $repository->findCoinsSortedByValue();
        $change = number_format($insertedMoney - $item->getPrice(), 2);

        return $this->calculateChange($change);
    }
}