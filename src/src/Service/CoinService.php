<?php

namespace App\Service;

use App\Entity\Coin;
use App\Exception\CoinException;
use Doctrine\ORM\EntityManagerInterface;

class CoinService
{
    /**
     * @Var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param float $value
     * @return Coin|null
     * @throws CoinException
     */
    public function findCoinByValue(float $value): ?Coin
    {
        $repository = $this->em->getRepository(Coin::class);
        $coin = $repository->findOneBy(['value' => $value]);

        if (!$coin) {
            throw CoinException::notFoundMessage();
        }

        return $coin;
    }

    /**
     * @param array $vendingCoins
     * @param array $pocketCoins
     */
    public function updateCoinStatus(array $vendingCoins, array $pocketCoins): void
    {
        $repository = $this->em->getRepository(Coin::class);
        $coins = $repository->findBy([], ['value' => 'DESC']);

        foreach ($coins as $coin) {
            $coinValue = $coin->getValue();
            foreach($vendingCoins as $vendingCoin) {
                if (bccomp($vendingCoin['value'], $coinValue, 2) == 0) {
                    $coin->setAmount($vendingCoin['amount']);
                }
            }
            if (in_array($coinValue, $pocketCoins)) {
                $coin->setAmount($coin->getAmount() + 1);
            }
        }

        $this->em->flush();
    }
}