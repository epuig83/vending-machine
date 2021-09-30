<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\Item;
use Symfony\Component\HttpFoundation\RequestStack;

class PocketService
{
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $pocket
     */
    private function store(array $pocket): void
    {
        $session = $this->requestStack->getSession();
        $session->set('pocket', $pocket);
    }

    /**
     *
     */
    public function empty(): void
    {
        $session = $this->requestStack->getSession();
        $session->set('pocket', []);
    }

    /**
     * @return array
     */
    public function getCoins(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('pocket', []);
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        $total = 0;
        $coins = $this->getCoins();
        foreach ($coins as $coin) {
            $total += $coin;
        }

        return $total;
    }

    /**
     * @param Coin $coin
     */
    public function insertCoin(Coin $coin): void
    {
        $pocket = $this->getCoins();
        array_push($pocket, $coin->getValue());
        $this->store($pocket);
    }

    /**
     * @return array
     */
    public function returnCoins(): array
    {
        $pocket = $this->getCoins();
        $this->empty();
        return $pocket;
    }

    /**
     * @return array
     */
    public function status(): array
    {
        return [
            'money' => $this->getTotalAmount(),
            'coins' => $this->getCoins()
        ];
    }
}