<?php

namespace App\Service;

use App\Entity\Coin;
use Symfony\Component\HttpFoundation\RequestStack;

class PocketService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    private function getCoins(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('pocket', []);
    }

    /**
     * @return float
     */
    private function getTotal(): float
    {
        $total = 0;
        $coins = $this->getCoins();
        foreach ($coins as $coin) {
            $total += $coin;
        }

        return $total;
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
    private function empty(): void
    {
        $session = $this->requestStack->getSession();
        $session->set('pocket', []);
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
            'money' => $this->getTotal(),
            'coins' => $this->getCoins()
        ];
    }
}